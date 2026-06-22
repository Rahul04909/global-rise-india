<?php
/**
 * api/submit-volunteer.php
 * ============================================================
 * Volunteer Registration API Endpoint with Payment Verification
 * The Global Rise Foundation
 *
 * Accepts POST data, verifies Razorpay payment signatures,
 * inserts volunteer records, and triggers emails.
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
    'full_name'           => trim($_POST['full_name']            ?? ''),
    'email'               => trim($_POST['email']                ?? ''),
    'phone'               => trim($_POST['phone']                ?? ''),
    'dob'                 => trim($_POST['dob']                  ?? ''),
    'gender'              => trim($_POST['gender']               ?? ''),
    'city'                => trim($_POST['city']                 ?? ''),
    'state'               => trim($_POST['state']                ?? ''),
    'pincode'             => trim($_POST['pincode']              ?? ''),
    'education'           => trim($_POST['education']            ?? ''),
    'occupation'          => trim($_POST['occupation']           ?? ''),
    'area_of_interest'    => trim($_POST['area_of_interest']     ?? ''),
    'availability'        => trim($_POST['availability']         ?? ''),
    'hours_per_week'      => (int)($_POST['hours_per_week']      ?? 0),
    'motivation'          => trim($_POST['motivation']           ?? ''),
    'skills'              => trim($_POST['skills']               ?? ''),
    'prior_experience'    => trim($_POST['prior_experience']     ?? 'no'),
    'emergency_name'      => trim($_POST['emergency_name']       ?? ''),
    'emergency_phone'     => trim($_POST['emergency_phone']      ?? ''),
    'agree_terms'         => trim($_POST['agree_terms']          ?? ''),
    
    // Payment integrations
    'plan_id'             => (int)($_POST['plan_id']             ?? 0),
    'razorpay_payment_id' => trim($_POST['razorpay_payment_id']   ?? ''),
    'razorpay_order_id'   => trim($_POST['razorpay_order_id']     ?? ''),
    'razorpay_signature'  => trim($_POST['razorpay_signature']    ?? ''),
];

$errors = [];

// ── Validation checks ──────────────────────────────────────────
if (empty($raw['full_name']) || strlen($raw['full_name']) < 3) {
    $errors[] = 'Full name must be at least 3 characters.';
}
if (empty($raw['email']) || !filter_var($raw['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if (empty($raw['phone']) || !preg_match('/^[6-9]\d{9}$/', $raw['phone'])) {
    $errors[] = 'A valid 10-digit Indian mobile number is required.';
}
if (empty($raw['dob'])) {
    $errors[] = 'Date of birth is required.';
} else {
    $dobDate = DateTime::createFromFormat('Y-m-d', $raw['dob']);
    if ($dobDate) {
        $age = (int)(new DateTime())->diff($dobDate)->y;
        if ($age < 18 || $age > 75) {
            $errors[] = 'Volunteer must be between 18 and 75 years old.';
        }
    } else {
        $errors[] = 'Invalid date format for Date of Birth.';
    }
}
if (empty($raw['gender']))           $errors[] = 'Gender is required.';
if (empty($raw['city']))             $errors[] = 'City is required.';
if (empty($raw['state']))            $errors[] = 'State is required.';
if (empty($raw['area_of_interest']))  $errors[] = 'Area of interest is required.';
if (empty($raw['availability']))     $errors[] = 'Availability is required.';
if ($raw['hours_per_week'] < 1 || $raw['hours_per_week'] > 40) {
    $errors[] = 'Hours per week must be between 1 and 40.';
}
if (empty($raw['motivation']) || strlen($raw['motivation']) < 30) {
    $errors[] = 'Please share at least 30 characters about your motivation.';
}
if ($raw['agree_terms'] !== '1') {
    $errors[] = 'You must agree to the terms and code of conduct.';
}
if ($raw['plan_id'] <= 0) {
    $errors[] = 'Please select a support plan to complete registration.';
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

    // Verify chosen plan
    $planStmt = $pdo->prepare("SELECT * FROM `volunteer_plans` WHERE `id` = :id AND `status` = 'active' LIMIT 1");
    $planStmt->execute([':id' => $raw['plan_id']]);
    $plan = $planStmt->fetch();

    if (!$plan) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'The selected support plan is inactive or invalid.']);
        exit;
    }

    $amount_paid = (float)$plan['price'];

    // Insert volunteer details into database
    $stmt = $pdo->prepare("
        INSERT INTO `volunteers`
            (full_name, email, phone, dob, gender, city, state, pincode,
             education, occupation, area_of_interest, availability, hours_per_week,
             motivation, skills, prior_experience, emergency_name, emergency_phone,
             plan_id, payment_status, razorpay_payment_id, razorpay_order_id, razorpay_signature, amount_paid)
        VALUES
            (:full_name, :email, :phone, :dob, :gender, :city, :state, :pincode,
             :education, :occupation, :area_of_interest, :availability, :hours_per_week,
             :motivation, :skills, :prior_experience, :emergency_name, :emergency_phone,
             :plan_id, 'paid', :razorpay_payment_id, :razorpay_order_id, :razorpay_signature, :amount_paid)
    ");

    $stmt->execute([
        ':full_name'           => $raw['full_name'],
        ':email'               => $raw['email'],
        ':phone'               => $raw['phone'],
        ':dob'                 => $raw['dob'] ?: null,
        ':gender'              => $raw['gender'],
        ':city'                => $raw['city'],
        ':state'               => $raw['state'],
        ':pincode'             => $raw['pincode'],
        ':education'           => $raw['education'],
        ':occupation'          => $raw['occupation'],
        ':area_of_interest'    => $raw['area_of_interest'],
        ':availability'        => $raw['availability'],
        ':hours_per_week'      => $raw['hours_per_week'],
        ':motivation'          => $raw['motivation'],
        ':skills'              => $raw['skills'],
        ':prior_experience'    => $raw['prior_experience'],
        ':emergency_name'      => $raw['emergency_name'],
        ':emergency_phone'     => $raw['emergency_phone'],
        ':plan_id'             => $raw['plan_id'],
        ':razorpay_payment_id' => $raw['razorpay_payment_id'],
        ':razorpay_order_id'   => $raw['razorpay_order_id'],
        ':razorpay_signature'  => $raw['razorpay_signature'],
        ':amount_paid'         => $amount_paid
    ]);

    $volunteerId = $pdo->lastInsertId();

} catch (PDOException $e) {
    http_response_code(500);
    $msg = APP_DEBUG ? $e->getMessage() : 'Database error. Unable to record volunteer details.';
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

$plan_duration = $plan['duration_value'] . ' ' . ucfirst($plan['duration_unit']) . ($plan['duration_value'] > 1 ? 's' : '');
if ($plan['duration_unit'] === 'lifetime') $plan_duration = 'Lifetime';
if ($plan['duration_unit'] === 'onetime')  $plan_duration = 'One-Time';

// 1. Send Confirmation Email to Applicant
try {
    $mail = buildMailer();
    $mail->addAddress($raw['email'], $raw['full_name']);
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to The Global Rise Foundation Volunteer Team!';
    $mail->Body = '
    <div style="font-family:Montserrat,Arial,sans-serif;max-width:580px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
      <div style="background:linear-gradient(135deg,#1b5182,#113252);padding:35px 30px;text-align:center;">
        <h1 style="color:#ffffff;font-size:22px;margin:0;">The Global Rise Foundation</h1>
        <p style="color:rgba(255,255,255,0.85);font-size:14px;margin:8px 0 0;">Volunteer Support Membership Confirmed</p>
      </div>
      <div style="padding:35px 30px;">
        <p style="color:#2d3748;font-size:15px;">Dear <strong>' . htmlspecialchars($raw['full_name']) . '</strong>,</p>
        <p style="color:#4a5568;font-size:14px;line-height:1.7;">Thank you for registering to volunteer and sponsoring our initiatives! Your payment for the <strong>' . htmlspecialchars($plan['title']) . '</strong> is verified.</p>
        
        <div style="background:#f0f7ff;border-left:4px solid #1b5182;padding:18px 20px;border-radius:4px;margin:25px 0;">
          <p style="margin:0;color:#1b5182;font-size:13px;font-weight:700;">Reference Code: #VOL-' . str_pad($volunteerId, 5, '0', STR_PAD_LEFT) . '</p>
          <p style="margin:4px 0 0;color:#2d3748;font-size:13px;"><strong>Plan:</strong> ' . htmlspecialchars($plan['title']) . '</p>
          <p style="margin:2px 0 0;color:#2d3748;font-size:13px;"><strong>Amount Paid:</strong> ₹' . number_format($amount_paid, 2) . '</p>
          <p style="margin:2px 0 0;color:#2d3748;font-size:13px;"><strong>Duration:</strong> ' . $plan_duration . '</p>
          <p style="margin:2px 0 0;color:#2d3748;font-size:13px;"><strong>Payment ID:</strong> ' . htmlspecialchars($raw['razorpay_payment_id']) . '</p>
        </div>

        <p style="color:#4a5568;font-size:14px;line-height:1.7;">Our Volunteer Coordination team is reviewing your application preferences for <strong>' . htmlspecialchars($raw['area_of_interest']) . '</strong> and will connect with you shortly regarding orientation schedules.</p>
        <p style="color:#4a5568;font-size:14px;margin-top:25px;">Warm regards,<br><strong style="color:#1b5182;">The Volunteer Coordination Team</strong><br>' . APP_NAME . '</p>
      </div>
      <div style="background:#f7fafc;padding:18px 30px;text-align:center;border-top:1px solid #e2e8f0;">
        <p style="color:#a0aec0;font-size:12px;margin:0;">© ' . date('Y') . ' ' . APP_NAME . ' | All Rights Reserved</p>
      </div>
    </div>';
    $mail->AltBody = "Dear {$raw['full_name']},\n\nThank you for volunteering with TGRF!\nReference: #VOL-" . str_pad($volunteerId, 5, '0', STR_PAD_LEFT) . "\nPlan: {$plan['title']}\nAmount: ₹{$amount_paid}\nWe will contact you shortly.";
    $mail->send();
} catch (MailerException $e) {
    error_log('[TGRF Volunteer Mailer] Applicant email failed: ' . $e->getMessage());
}

// 2. Send Notification Email to Admin
try {
    $mail2 = buildMailer();
    $mail2->addAddress(ADMIN_EMAIL, 'TGRF Admin');
    $mail2->isHTML(true);
    $mail2->Subject = '[New Paid Volunteer] ' . $raw['full_name'] . ' — ' . $raw['area_of_interest'];
    $mail2->Body = '
    <div style="font-family:Arial,sans-serif;max-width:580px;margin:0 auto;">
      <h2 style="color:#1b5182;">New Volunteer Registered (Payment Confirmed)</h2>
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;width:150px;">Reference</td><td style="padding:8px;border-bottom:1px solid #eee;font-weight:700;">#VOL-' . str_pad($volunteerId, 5, '0', STR_PAD_LEFT) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Name</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['full_name']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Email</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['email']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Phone</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['phone']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Area of Interest</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['area_of_interest']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Support Plan</td><td style="padding:8px;border-bottom:1px solid #eee;font-weight:600;">' . htmlspecialchars($plan['title']) . ' (₹' . number_format($amount_paid, 2) . ')</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Razorpay Payment ID</td><td style="padding:8px;border-bottom:1px solid #eee;font-family:monospace;">' . htmlspecialchars($raw['razorpay_payment_id']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Location</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['city']) . ', ' . htmlspecialchars($raw['state']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Availability</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['availability']) . ' (' . $raw['hours_per_week'] . ' hrs/week)</td></tr>
        <tr><td style="padding:8px;color:#718096;vertical-align:top;">Motivation</td><td style="padding:8px;">' . nl2br(htmlspecialchars($raw['motivation'])) . '</td></tr>
      </table>
    </div>';
    $mail2->send();
} catch (MailerException $e) {
    error_log('[TGRF Volunteer Mailer] Admin email failed: ' . $e->getMessage());
}

// ── Success Response ──────────────────────────────────────────
echo json_encode([
    'success'   => true,
    'message'   => 'Your volunteer application has been submitted and payment captured successfully!',
    'reference' => 'VOL-' . str_pad($volunteerId, 5, '0', STR_PAD_LEFT),
]);
