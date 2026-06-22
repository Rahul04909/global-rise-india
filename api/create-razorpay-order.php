<?php
/**
 * api/create-razorpay-order.php
 * ============================================================
 * Razorpay Order Initialization API Endpoint
 * The Global Rise Foundation
 *
 * Accepts plan_id via POST, validates active plan details,
 * and creates a secure payment transaction token via Razorpay SDK.
 *
 * Returns: { "success": bool, "order_id": string, "amount": int, "key_id": string, "plan_title": string }
 * ============================================================
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Load central configs
require_once __DIR__ . '/../includes/config.php';

use Razorpay\Api\Api;

$plan_id = (int)($_POST['plan_id'] ?? 0);

if ($plan_id <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please select a valid support plan.']);
    exit;
}

try {
    $pdo = getDB();

    // Retrieve active plan details
    $stmt = $pdo->prepare("SELECT * FROM `volunteer_plans` WHERE `id` = :id AND `status` = 'active' LIMIT 1");
    $stmt->execute([':id' => $plan_id]);
    $plan = $stmt->fetch();

    if (!$plan) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Selected support plan is inactive or does not exist.']);
        exit;
    }

    // Verify key constants are configured
    if (empty(RZP_KEY_ID) || empty(RZP_KEY_SECRET)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Razorpay payment gateway keys are not configured in .env file.']);
        exit;
    }

    // Initialize Razorpay API Client
    $api = new Api(RZP_KEY_ID, RZP_KEY_SECRET);

    // Create unique receipt identifier
    $receipt_id = 'vol_' . $plan_id . '_' . time() . '_' . rand(100, 999);

    // Convert price to paise (e.g. ₹500.00 -> 50000 paise)
    $amount_in_paise = (int)round($plan['price'] * 100);

    // Create Order with Razorpay
    $order = $api->order->create([
        'receipt'         => $receipt_id,
        'amount'          => $amount_in_paise,
        'currency'        => RZP_CURRENCY,
        'payment_capture' => 1
    ]);

    if (!empty($order->id)) {
        echo json_encode([
            'success'    => true,
            'order_id'   => $order->id,
            'amount'     => $amount_in_paise,
            'key_id'     => RZP_KEY_ID,
            'plan_title' => $plan['title']
        ]);
    } else {
        http_response_code(520);
        echo json_encode(['success' => false, 'message' => 'Failed to initialize transaction order with payment gateway.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    $msg = APP_DEBUG ? $e->getMessage() : 'Database connection error.';
    echo json_encode(['success' => false, 'message' => $msg]);
} catch (Exception $e) {
    http_response_code(500);
    $msg = APP_DEBUG ? $e->getMessage() : 'Razorpay Connection Error: Unable to initialize payment.';
    error_log('[Razorpay Order Creation Error] ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $msg]);
}
