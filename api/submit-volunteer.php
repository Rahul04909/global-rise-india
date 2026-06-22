<?php
/**
 * api/submit-volunteer.php
 * ============================================================
 * Volunteer Registration API Endpoint
 * The Global Rise Foundation
 *
 * Accepts POST (JSON or form-data), validates input,
 * inserts into `volunteers` table, and sends:
 *   1. Confirmation email to the applicant
 *   2. Admin notification email
 *
 * Always responds with: { "success": bool, "message": string }
 * ============================================================
 */

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// ── Load config (env + constants + getDB) ────────────────────
require_once __DIR__ . '/../includes/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;

// ── Collect & sanitise input ─────────────────────────────────
$raw = [
    'full_name'       => trim($_POST['full_name']        ?? ''),
    'email'           => trim($_POST['email']            ?? ''),
    'phone'           => trim($_POST['phone']            ?? ''),
    'dob'             => trim($_POST['dob']              ?? ''),
    'gender'          => trim($_POST['gender']           ?? ''),
    'city'            => trim($_POST['city']             ?? ''),
    'state'           => trim($_POST['state']            ?? ''),
    'pincode'         => trim($_POST['pincode']          ?? ''),
    'education'       => trim($_POST['education']        ?? ''),
    'occupation'      => trim($_POST['occupation']       ?? ''),
    'area_of_interest'=> trim($_POST['area_of_interest'] ?? ''),
    'availability'    => trim($_POST['availability']     ?? ''),
    'hours_per_week'  => (int)($_POST['hours_per_week']  ?? 0),
    'motivation'      => trim($_POST['motivation']       ?? ''),
    'skills'          => trim($_POST['skills']           ?? ''),
    'prior_experience'=> trim($_POST['prior_experience'] ?? 'no'),
    'emergency_name'  => trim($_POST['emergency_name']   ?? ''),
    'emergency_phone' => trim($_POST['emergency_phone']  ?? ''),
    'agree_terms'     => trim($_POST['agree_terms']      ?? ''),
];

// ── Server-side validation ────────────────────────────────────
$errors = [];

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
    $age = (int)(new DateTime())->diff($dobDate)->y;
    if ($age < 18 || $age > 75) {
        $errors[] = 'Volunteer must be between 18 and 75 years old.';
    }
}
if (empty($raw['gender']))          $errors[] = 'Gender is required.';
if (empty($raw['city']))            $errors[] = 'City is required.';
if (empty($raw['state']))           $errors[] = 'State is required.';
if (empty($raw['area_of_interest'])) $errors[] = 'Area of interest is required.';
if (empty($raw['availability']))    $errors[] = 'Availability is required.';
if ($raw['hours_per_week'] < 1 || $raw['hours_per_week'] > 40) {
    $errors[] = 'Hours per week must be between 1 and 40.';
}
if (empty($raw['motivation']) || strlen($raw['motivation']) < 30) {
    $errors[] = 'Please share at least 30 characters about your motivation.';
}
if ($raw['agree_terms'] !== '1') {
    $errors[] = 'You must agree to the terms and code of conduct.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' | ', $errors)]);
    exit;
}

// ── Database Insert ───────────────────────────────────────────
try {
    $pdo = getDB();

    // Create table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `volunteers` (
            `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `full_name`        VARCHAR(120)     NOT NULL,
            `email`            VARCHAR(180)     NOT NULL,
            `phone`            VARCHAR(15)      NOT NULL,
            `dob`              DATE             NULL,
            `gender`           VARCHAR(20)      NOT NULL,
            `city`             VARCHAR(80)      NOT NULL,
            `state`            VARCHAR(80)      NOT NULL,
            `pincode`          VARCHAR(10)      NOT NULL DEFAULT '',
            `education`        VARCHAR(100)     NOT NULL DEFAULT '',
            `occupation`       VARCHAR(100)     NOT NULL DEFAULT '',
            `area_of_interest` VARCHAR(120)     NOT NULL,
            `availability`     VARCHAR(50)      NOT NULL,
            `hours_per_week`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `motivation`       TEXT             NOT NULL,
            `skills`           TEXT             NOT NULL DEFAULT '',
            `prior_experience` ENUM('yes','no') NOT NULL DEFAULT 'no',
            `emergency_name`   VARCHAR(120)     NOT NULL DEFAULT '',
            `emergency_phone`  VARCHAR(15)      NOT NULL DEFAULT '',
            `status`           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
            `created_at`       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_email` (`email`),
            INDEX `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $stmt = $pdo->prepare("
        INSERT INTO `volunteers`
            (full_name, email, phone, dob, gender, city, state, pincode,
             education, occupation, area_of_interest, availability, hours_per_week,
             motivation, skills, prior_experience, emergency_name, emergency_phone)
        VALUES
            (:full_name, :email, :phone, :dob, :gender, :city, :state, :pincode,
             :education, :occupation, :area_of_interest, :availability, :hours_per_week,
             :motivation, :skills, :prior_experience, :emergency_name, :emergency_phone)
    ");

    $stmt->execute([
        ':full_name'        => $raw['full_name'],
        ':email'            => $raw['email'],
        ':phone'            => $raw['phone'],
        ':dob'              => $raw['dob'] ?: null,
        ':gender'           => $raw['gender'],
        ':city'             => $raw['city'],
        ':state'            => $raw['state'],
        ':pincode'          => $raw['pincode'],
        ':education'        => $raw['education'],
        ':occupation'       => $raw['occupation'],
        ':area_of_interest' => $raw['area_of_interest'],
        ':availability'     => $raw['availability'],
        ':hours_per_week'   => $raw['hours_per_week'],
        ':motivation'       => $raw['motivation'],
        ':skills'           => $raw['skills'],
        ':prior_experience' => $raw['prior_experience'],
        ':emergency_name'   => $raw['emergency_name'],
        ':emergency_phone'  => $raw['emergency_phone'],
    ]);

    $volunteerId = $pdo->lastInsertId();

} catch (PDOException $e) {
    http_response_code(500);
    $msg = APP_DEBUG ? $e->getMessage() : 'Database error. Please try again.';
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

// 1. Confirmation email to volunteer
try {
    $mail = buildMailer();
    $mail->addAddress($raw['email'], $raw['full_name']);
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to The Global Rise Foundation Volunteer Team!';
    $mail->Body = '
    <div style="font-family:Montserrat,Arial,sans-serif;max-width:580px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
      <div style="background:linear-gradient(135deg,#1b5182,#113252);padding:35px 30px;text-align:center;">
        <h1 style="color:#ffffff;font-size:22px;margin:0;">The Global Rise Foundation</h1>
        <p style="color:rgba(255,255,255,0.85);font-size:14px;margin:8px 0 0;">Volunteer Application Received</p>
      </div>
      <div style="padding:35px 30px;">
        <p style="color:#2d3748;font-size:15px;">Dear <strong>' . htmlspecialchars($raw['full_name']) . '</strong>,</p>
        <p style="color:#4a5568;font-size:14px;line-height:1.7;">Thank you for applying to join our volunteer team! We have received your application for <strong>' . htmlspecialchars($raw['area_of_interest']) . '</strong> and our team will review it shortly.</p>
        <div style="background:#f0f7ff;border-left:4px solid #1b5182;padding:18px 20px;border-radius:4px;margin:25px 0;">
          <p style="margin:0;color:#1b5182;font-size:13px;font-weight:700;">Application Reference: #VOL-' . str_pad($volunteerId, 5, '0', STR_PAD_LEFT) . '</p>
          <p style="margin:6px 0 0;color:#4a5568;font-size:13px;">You will receive a response within 3-5 business days.</p>
        </div>
        <p style="color:#4a5568;font-size:14px;line-height:1.7;">In the meantime, follow us on social media to stay updated on our latest initiatives and field activities.</p>
        <p style="color:#4a5568;font-size:14px;margin-top:25px;">Warm regards,<br><strong style="color:#1b5182;">The Volunteer Coordination Team</strong><br>' . APP_NAME . '</p>
      </div>
      <div style="background:#f7fafc;padding:18px 30px;text-align:center;border-top:1px solid #e2e8f0;">
        <p style="color:#a0aec0;font-size:12px;margin:0;">© ' . date('Y') . ' ' . APP_NAME . ' | All Rights Reserved</p>
      </div>
    </div>';
    $mail->AltBody = "Dear {$raw['full_name']},\n\nThank you for applying to volunteer with " . APP_NAME . ".\nReference: #VOL-" . str_pad($volunteerId, 5, '0', STR_PAD_LEFT) . "\n\nWe will contact you within 3-5 business days.";
    $mail->send();
} catch (MailerException $e) {
    // Log but don't fail the request — DB record is already saved
    error_log('[TGRF Volunteer Mailer] Confirmation email failed: ' . $e->getMessage());
}

// 2. Admin notification
try {
    $mail2 = buildMailer();
    $mail2->addAddress(ADMIN_EMAIL, 'TGRF Admin');
    $mail2->isHTML(true);
    $mail2->Subject = '[New Volunteer] ' . $raw['full_name'] . ' — ' . $raw['area_of_interest'];
    $mail2->Body = '
    <div style="font-family:Arial,sans-serif;max-width:580px;margin:0 auto;">
      <h2 style="color:#1b5182;">New Volunteer Application</h2>
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Reference</td><td style="padding:8px;border-bottom:1px solid #eee;font-weight:700;">#VOL-' . str_pad($volunteerId, 5, '0', STR_PAD_LEFT) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Name</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['full_name']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Email</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['email']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Phone</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['phone']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Area of Interest</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['area_of_interest']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Location</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['city']) . ', ' . htmlspecialchars($raw['state']) . '</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#718096;">Availability</td><td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($raw['availability']) . ' (' . $raw['hours_per_week'] . ' hrs/week)</td></tr>
        <tr><td style="padding:8px;color:#718096;vertical-align:top;">Motivation</td><td style="padding:8px;">' . nl2br(htmlspecialchars($raw['motivation'])) . '</td></tr>
      </table>
    </div>';
    $mail2->send();
} catch (MailerException $e) {
    error_log('[TGRF Volunteer Mailer] Admin notification failed: ' . $e->getMessage());
}

// ── Success Response ──────────────────────────────────────────
echo json_encode([
    'success'   => true,
    'message'   => 'Your volunteer application has been submitted successfully!',
    'reference' => 'VOL-' . str_pad($volunteerId, 5, '0', STR_PAD_LEFT),
]);
