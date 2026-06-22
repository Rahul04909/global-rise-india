<?php
/**
 * api/submit-donation.php
 * ============================================================
 * Donation Capture API Endpoint with Payment Verification
 * The Global Rise Foundation
 *
 * Accepts POST data, verifies Razorpay payment signatures,
 * inserts donation records, and triggers emails.
 *
 * Responds: { "success": bool, "message": string, "reference": string }
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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;

// ── Collect and sanitise inputs ──────────────────────────────
$raw = [
    'donor_title'         => trim($_POST['donor_title']          ?? 'Mr.'),
    'donor_name'          => trim($_POST['donor_name']           ?? ''),
    'donor_email'         => trim($_POST['donor_email']          ?? ''),
    'donor_mobile'        => trim($_POST['donor_mobile']         ?? ''),
    'donor_dob'           => trim($_POST['donor_dob']            ?? ''),
    'donor_alt_mobile'    => trim($_POST['donor_alt_mobile']     ?? ''),
    'citizenship'         => trim($_POST['citizenship']          ?? 'indian'),
    'donation_type'       => trim($_POST['donation_type']        ?? 'once'),
    'selected_cause'      => trim($_POST['selected_cause']       ?? 'feed'),
    'amount'              => (float)($_POST['total_donation']    ?? 0),
    'request_80g'         => (isset($_POST['request_80g']) ? 1 : 0),
    'donor_pan'           => trim($_POST['donor_pan']            ?? ''),
    'donor_address'       => trim($_POST['donor_address']        ?? ''),
    
    // Razorpay Details
    'razorpay_payment_id' => trim($_POST['razorpay_payment_id']   ?? ''),
    'razorpay_order_id'   => trim($_POST['razorpay_order_id']     ?? ''),
    'razorpay_signature'  => trim($_POST['razorpay_signature']    ?? ''),
];

$errors = [];

// ── Validation checks ──────────────────────────────────────────
if (empty($raw['donor_name']) || strlen($raw['donor_name']) < 2) {
    $errors[] = 'Full name must be at least 2 characters.';
}
if (empty($raw['donor_email']) || !filter_var($raw['donor_email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if (empty($raw['donor_mobile']) || !preg_match('/^\+?\d{10,14}$/', $raw['donor_mobile'])) {
    $errors[] = 'A valid mobile number is required.';
}
if (empty($raw['donor_dob'])) {
    $errors[] = 'Date of birth is required.';
}
if ($raw['amount'] < 1.00) {
    $errors[] = 'Donation amount must be at least ₹1.';
}

if ($raw['request_80g'] === 1) {
    $panVal = strtoupper($raw['donor_pan']);
    if (empty($panVal) || !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $panVal)) {
        $errors[] = 'A valid 10-character Indian PAN Card number is required for 80G.';
    }
    if (empty($raw['donor_address']) || strlen($raw['donor_address']) < 8) {
        $errors[] = 'A billing address (minimum 8 characters) is required for 80G.';
    }
}

if (empty($raw['razorpay_payment_id']) || empty($raw['razorpay_order_id']) || empty($raw['razorpay_signature'])) {
    $errors[] = 'Payment verification parameters are missing.';
}

// ── Verify Razorpay Signature ──────────────────────────────────
if (empty($errors)) {
    $generated_signature = hash_hmac('sha256', $raw['razorpay_order_id'] . "|" . $raw['razorpay_payment_id'], RZP_KEY_SECRET);
    if (!hash_equals($generated_signature, $raw['razorpay_signature'])) {
        $errors[] = 'Payment signature verification failed. Transaction was not authorized.';
    }
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' | ', $errors)]);
    exit;
}

try {
    $pdo = getDB();

    // Insert donation details into database
    $stmt = $pdo->prepare("
        INSERT INTO `donations` (
            `donor_title`, `donor_name`, `donor_email`, `donor_mobile`, `donor_dob`, `donor_alt_mobile`,
            `citizenship`, `donation_type`, `selected_cause`, `amount`, `request_80g`, `donor_pan`, `donor_address`,
            `payment_status`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `created_at`
        ) VALUES (
            :donor_title, :donor_name, :donor_email, :donor_mobile, :donor_dob, :donor_alt_mobile,
            :citizenship, :donation_type, :selected_cause, :amount, :request_80g, :donor_pan, :donor_address,
            'paid', :razorpay_order_id, :razorpay_payment_id, :razorpay_signature, NOW()
        )
    ");

    $stmt->execute([
        ':donor_title'         => $raw['donor_title'],
        ':donor_name'          => $raw['donor_name'],
        ':donor_email'         => $raw['donor_email'],
        ':donor_mobile'        => $raw['donor_mobile'],
        ':donor_dob'           => $raw['donor_dob'],
        ':donor_alt_mobile'    => $raw['donor_alt_mobile'] ?: null,
        ':citizenship'         => $raw['citizenship'],
        ':donation_type'       => $raw['donation_type'],
        ':selected_cause'      => $raw['selected_cause'],
        ':amount'              => $raw['amount'],
        ':request_80g'         => $raw['request_80g'],
        ':donor_pan'           => $raw['request_80g'] === 1 ? strtoupper($raw['donor_pan']) : null,
        ':donor_address'       => $raw['request_80g'] === 1 ? $raw['donor_address'] : null,
        ':razorpay_order_id'   => $raw['razorpay_order_id'],
        ':razorpay_payment_id' => $raw['razorpay_payment_id'],
        ':razorpay_signature'  => $raw['razorpay_signature'],
    ]);

    $donationId = $pdo->lastInsertId();

} catch (PDOException $e) {
    http_response_code(500);
    $msg = APP_DEBUG ? $e->getMessage() : 'Database error. Unable to record donation details.';
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}

// ── Send Emails via PHPMailer ─────────────────────────────────
function buildMailer(): PHPMailer
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    return $mail;
}

// Translate cause code to text
$causeText = 'Social Welfare Projects';
switch ($raw['selected_cause']) {
    case 'feed':     $causeText = 'Feed a Child (Mid-Day Meals)'; break;
    case 'slum':     $causeText = 'Educate Slum Children'; break;
    case 'disaster': $causeText = 'Disaster Relief & Mitigation'; break;
    case 'women':    $causeText = 'Women Empowerment'; break;
    case 'disabled': $causeText = 'Support Persons with Disabilities'; break;
    case 'senior':   $causeText = 'Senior Citizen Care'; break;
}

// 1. Send Confirmation Email to Donor
try {
    $mail = buildMailer();
    $mail->addAddress($raw['donor_email'], $raw['donor_name']);
    $mail->isHTML(true);
    $mail->Subject = 'Receipt of Your Contribution to The Global Rise Foundation';
    
    $exemptionNote = '';
    if ($raw['request_80g'] === 1) {
        $exemptionNote = '
        <div style="background:#fff9f0;border-left:4px solid #d97706;padding:15px;margin:20px 0;border-radius:4px;">
            <p style="margin:0;color:#b45309;font-size:13px;font-weight:700;"><i class="fas fa-percent"></i> 80(G) Tax Exemption Requested</p>
            <p style="margin:4px 0 0;color:#555;font-size:12px;line-height:1.5;">Your contribution qualifies for tax exemption. The official receipt will be generated under PAN: <strong>' . htmlspecialchars(strtoupper($raw['donor_pan'])) . '</strong> and dispatched to your billing address within 15 working days.</p>
        </div>';
    }

    $mail->Body = '
    <div style="font-family:Montserrat,Arial,sans-serif;max-width:580px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
      <div style="background:linear-gradient(135deg,#1b5182,#113252);padding:35px 30px;text-align:center;">
        <h1 style="color:#ffffff;font-size:22px;margin:0;">Thank You for Your Support!</h1>
        <p style="color:rgba(255,255,255,0.85);font-size:14px;margin:8px 0 0;">The Global Rise Foundation Donation Receipt</p>
      </div>
      <div style="padding:35px 30px;">
        <p style="color:#2d3748;font-size:15px;">Dear <strong>' . htmlspecialchars($raw['donor_title'] . ' ' . $raw['donor_name']) . '</strong>,</p>
        <p style="color:#4a5568;font-size:14px;line-height:1.7;">We have successfully received your contribution of <strong>₹' . number_format($raw['amount'], 2) . '</strong> in support of the project: <strong>' . $causeText . '</strong>. Your contribution plays an essential role in helping us run our on-ground operations and empower lives.</p>
        
        <div style="background:#f0f7ff;border-left:4px solid #1b5182;padding:18px 20px;border-radius:4px;margin:25px 0;">
          <p style="margin:0;color:#1b5182;font-size:13px;font-weight:700;">Receipt Number: #DON-' . str_pad($donationId, 5, '0', STR_PAD_LEFT) . '</p>
          <p style="margin:4px 0 0;color:#2d3748;font-size:13px;"><strong>Cause:</strong> ' . $causeText . '</p>
          <p style="margin:2px 0 0;color:#2d3748;font-size:13px;"><strong>Amount:</strong> ₹' . number_format($raw['amount'], 2) . '</p>
          <p style="margin:2px 0 0;color:#2d3748;font-size:13px;"><strong>Frequency:</strong> ' . ($raw['donation_type'] === 'once' ? 'One-Time Donation' : 'Monthly Subscription') . '</p>
          <p style="margin:2px 0 0;color:#2d3748;font-size:13px;"><strong>Payment Gateway ID:</strong> ' . htmlspecialchars($raw['razorpay_payment_id']) . '</p>
        </div>

        ' . $exemptionNote . '

        <p style="color:#4a5568;font-size:14px;line-height:1.7;">You will receive periodic project updates regarding how your contribution is changing lives on the ground. Together, we are creating a better tomorrow.</p>
        
        <p style="color:#4a5568;font-size:14px;margin-top:25px;">With warm gratitude,<br><strong style="color:#1b5182;">The Global Rise Foundation Team</strong><br>' . APP_NAME . '</p>
      </div>
      <div style="background:#f7fafc;padding:18px 30px;text-align:center;border-top:1px solid #e2e8f0;">
        <p style="color:#a0aec0;font-size:12px;margin:0;">© ' . date('Y') . ' ' . APP_NAME . ' | All Rights Reserved</p>
      </div>
    </div>';
    $mail->AltBody = "Dear {$raw['donor_name']},\n\nThank you for your generous contribution of ₹" . number_format($raw['amount'], 2) . " to The Global Rise Foundation!\nReceipt Code: #DON-" . str_pad($donationId, 5, '0', STR_PAD_LEFT) . "\nCause: {$causeText}\nWe appreciate your support.";
    $mail->send();
} catch (MailerException $e) {
    error_log('[TGRF Donor Mailer] Donor confirmation mail failed: ' . $e->getMessage());
}

// 2. Send Notification Email to Admin
try {
    $mail2 = buildMailer();
    $mail2->addAddress(ADMIN_EMAIL, 'TGRF Admin');
    $mail2->isHTML(true);
    $mail2->Subject = '[New Donation Received] ₹' . number_format($raw['amount']) . ' from ' . $raw['donor_name'];
    $mail2->Body = '
    <div style="font-family:Arial,sans-serif;max-width:580px;margin:0 auto;">
      <h2 style="color:#1b5182;">Online Contribution Verified successfully</h2>
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;width:160px;">Receipt Number</td><td style="padding:8px;border-bottom:1px solid #eee;font-weight:700;">#DON-' . str_pad($donationId, 5, '0', STR_PAD_LEFT) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Donor Name</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['donor_title'] . ' ' . $raw['donor_name']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Email Address</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['donor_email']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Phone</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['donor_mobile']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Citizenship</td><td style="padding:8px;border-bottom:1px solid #eee;text-transform:capitalize;">' . htmlspecialchars($raw['citizenship']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Cause Supported</td><td style="padding:8px;border-bottom:1px solid #eee;font-weight:600;">' . $causeText . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Donation Type</td><td style="padding:8px;border-bottom:1px solid #eee;text-transform:capitalize;">' . htmlspecialchars($raw['donation_type']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Amount Contributed</td><td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;color:#28a745;">₹' . number_format($raw['amount'], 2) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Tax Exemption (80G)</td><td style="padding:8px;border-bottom:1px solid #eee;">' . ($raw['request_80g'] === 1 ? 'Yes' : 'No') . '</td></tr>
        ' . ($raw['request_80g'] === 1 ? '
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">PAN Number</td><td style="padding:8px;border-bottom:1px solid #eee;font-family:monospace;font-weight:600;">' . htmlspecialchars(strtoupper($raw['donor_pan'])) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;vertical-align:top;">Billing Address</td><td style="padding:8px;border-bottom:1px solid #eee;">' . nl2br(htmlspecialchars($raw['donor_address'])) . '</td></tr>
        ' : '') . '
        <tr><td style="padding:8px;color:#718096;">Razorpay Payment ID</td><td style="padding:8px;font-family:monospace;">' . htmlspecialchars($raw['razorpay_payment_id']) . '</td></tr>
      </table>
    </div>';
    $mail2->send();
} catch (MailerException $e) {
    error_log('[TGRF Donor Mailer] Admin alert mail failed: ' . $e->getMessage());
}

// ── Success Response ──────────────────────────────────────────
echo json_encode([
    'success'   => true,
    'message'   => 'Thank you! Your donation was completed and verified successfully.',
    'reference' => 'DON-' . str_pad($donationId, 5, '0', STR_PAD_LEFT),
]);
