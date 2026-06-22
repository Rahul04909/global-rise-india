<?php
/**
 * api/create-donation-order.php
 * ============================================================
 * Razorpay Order Initialization for Online Donations
 * The Global Rise Foundation
 *
 * Accepts amount, selected_cause, and donation_type via POST,
 * and creates a secure payment transaction token via Razorpay SDK.
 *
 * Returns: { "success": bool, "order_id": string, "amount": int, "key_id": string }
 * ============================================================
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Load configurations & dependencies
require_once __DIR__ . '/../includes/config.php';

use Razorpay\Api\Api;

$amount = (float)($_POST['amount'] ?? 0);
$cause = trim($_POST['selected_cause'] ?? 'feed');
$donation_type = trim($_POST['donation_type'] ?? 'once');

if ($amount < 1.00) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid donation amount (minimum ₹1).']);
    exit;
}

try {
    // Verify Razorpay keys are configured
    if (empty(RZP_KEY_ID) || empty(RZP_KEY_SECRET)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Razorpay keys are not configured in the .env file.']);
        exit;
    }

    // Initialize Razorpay API Client
    $api = new Api(RZP_KEY_ID, RZP_KEY_SECRET);

    // Create unique receipt identifier
    $receipt_id = 'don_' . substr($cause, 0, 8) . '_' . time() . '_' . rand(100, 999);

    // Convert price to paise (e.g. ₹500.00 -> 50000 paise)
    $amount_in_paise = (int)round($amount * 100);

    // Create Order with Razorpay
    $order = $api->order->create([
        'receipt'         => $receipt_id,
        'amount'          => $amount_in_paise,
        'currency'        => RZP_CURRENCY,
        'payment_capture' => 1
    ]);

    if (!empty($order->id)) {
        echo json_encode([
            'success'  => true,
            'order_id' => $order->id,
            'amount'   => $amount_in_paise,
            'key_id'   => RZP_KEY_ID
        ]);
    } else {
        http_response_code(520);
        echo json_encode(['success' => false, 'message' => 'Failed to initialize transaction order with payment gateway.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    $msg = APP_DEBUG ? $e->getMessage() : 'Razorpay Connection Error: Unable to initialize donation payment.';
    error_log('[Razorpay Donation Order Error] ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $msg]);
}
