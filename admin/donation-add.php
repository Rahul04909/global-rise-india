<?php
/**
 * admin/donation-add.php
 * ============================================================
 * Manual Donation Logging Dashboard
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/includes/auth-check.php'; // Starts session, checks auth, gets DB

$pdo = getDB();
$error_msg = '';

// Generate CSRF token if not set
if (empty($_SESSION['don_add_csrf_token'])) {
    $_SESSION['don_add_csrf_token'] = bin2hex(random_bytes(32));
}

// Fallback values for form fields
$form_data = [
    'donor_title'      => 'Mr.',
    'donor_name'       => '',
    'donor_email'      => '',
    'donor_mobile'     => '',
    'donor_dob'        => '',
    'donor_alt_mobile' => '',
    'citizenship'      => 'indian',
    'donation_type'    => 'once',
    'selected_cause'   => 'feed',
    'amount'           => '1000.00',
    'request_80g'      => 0,
    'donor_pan'        => '',
    'donor_address'    => '',
    'payment_status'   => 'paid'
];

// Process creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Persist input values
    foreach (array_keys($form_data) as $key) {
        if ($key === 'request_80g') {
            $form_data[$key] = isset($_POST[$key]) ? 1 : 0;
        } elseif (isset($_POST[$key])) {
            $form_data[$key] = $_POST[$key];
        }
    }
    
    if (!hash_equals($_SESSION['don_add_csrf_token'], $csrf_token)) {
        $error_msg = 'Security token validation failed (CSRF mismatch).';
    } else {
        // Collect parameters
        $donor_title      = trim($_POST['donor_title'] ?? 'Mr.');
        $donor_name       = trim($_POST['donor_name'] ?? '');
        $donor_email      = trim($_POST['donor_email'] ?? '');
        $donor_mobile     = trim($_POST['donor_mobile'] ?? '');
        $donor_dob        = trim($_POST['donor_dob'] ?? '');
        $donor_alt_mobile = trim($_POST['donor_alt_mobile'] ?? '');
        $citizenship      = trim($_POST['citizenship'] ?? 'indian');
        $donation_type    = trim($_POST['donation_type'] ?? 'once');
        $selected_cause   = trim($_POST['selected_cause'] ?? 'feed');
        $amount           = (float)($_POST['amount'] ?? 0.00);
        $request_80g      = isset($_POST['request_80g']) ? 1 : 0;
        $donor_pan        = trim($_POST['donor_pan'] ?? '');
        $donor_address    = trim($_POST['donor_address'] ?? '');
        $payment_status   = trim($_POST['payment_status'] ?? 'paid');

        if (empty($donor_name) || empty($donor_email) || empty($donor_mobile) || empty($donor_dob)) {
            $error_msg = 'Donor Name, Email address, mobile number, and Date of birth are required fields.';
        } elseif (!filter_var($donor_email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = 'Please enter a valid email address.';
        } elseif ($amount < 1.00) {
            $error_msg = 'Please enter a valid donation amount (minimum ₹1).';
        } elseif ($request_80g === 1 && (empty($donor_pan) || !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i', $donor_pan))) {
            $error_msg = 'A valid 10-character Indian PAN Card is required for 80G tax exemption.';
        } elseif ($request_80g === 1 && (empty($donor_address) || strlen($donor_address) < 8)) {
            $error_msg = 'A valid billing address is required for 80G tax exemption.';
        } else {
            try {
                $insert = $pdo->prepare("
                    INSERT INTO `donations` (
                        `donor_title`, `donor_name`, `donor_email`, `donor_mobile`, `donor_dob`, `donor_alt_mobile`,
                        `citizenship`, `donation_type`, `selected_cause`, `amount`, `request_80g`, `donor_pan`, `donor_address`,
                        `payment_status`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `created_at`
                    ) VALUES (
                        :donor_title, :donor_name, :donor_email, :donor_mobile, :donor_dob, :donor_alt_mobile,
                        :citizenship, :donation_type, :selected_cause, :amount, :request_80g, :donor_pan, :donor_address,
                        :payment_status, NULL, NULL, NULL, NOW()
                    )
                ");

                $insert->execute([
                    ':donor_title'      => $donor_title,
                    ':donor_name'       => $donor_name,
                    ':donor_email'      => $donor_email,
                    ':donor_mobile'     => $donor_mobile,
                    ':donor_dob'        => $donor_dob,
                    ':donor_alt_mobile' => $donor_alt_mobile ?: null,
                    ':citizenship'      => $citizenship,
                    ':donation_type'    => $donation_type,
                    ':selected_cause'   => $selected_cause,
                    ':amount'           => $amount,
                    ':request_80g'      => $request_80g,
                    ':donor_pan'        => $request_80g === 1 ? strtoupper($donor_pan) : null,
                    ':donor_address'    => $request_80g === 1 ? $donor_address : null,
                    ':payment_status'   => $payment_status
                ]);

                header('Location: donations.php?added=1');
                exit;

            } catch (PDOException $e) {
                error_log('[Manual Donation Log Error] ' . $e->getMessage());
                $error_msg = APP_DEBUG ? 'Database Error: ' . $e->getMessage() : 'An error occurred while logging the manual donation.';
            }
        }
    }
}

include './header.php';
?>

<div class="row mb-3">
    <div class="col-12">
        <a href="donations.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Donations List</a>
    </div>
</div>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($error_msg) ?></div>
<?php endif; ?>

<div class="card card-primary card-outline shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold" style="color: var(--sidebar-color);"><i class="fas fa-plus-circle mr-1"></i> Log Offline / Manual Donation</h3>
    </div>
    
    <form action="donation-add.php" method="POST" class="form-horizontal">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['don_add_csrf_token']) ?>">
        
        <div class="card-body">
            <!-- SECTION 1: Personal Details -->
            <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-address-card mr-1"></i> Personal Details</h5>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Full Name *</label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <select name="donor_title" class="form-control col-3">
                            <option value="Mr." <?= $form_data['donor_title'] === 'Mr.' ? 'selected' : '' ?>>Mr.</option>
                            <option value="Ms." <?= $form_data['donor_title'] === 'Ms.' ? 'selected' : '' ?>>Ms.</option>
                            <option value="Mrs." <?= $form_data['donor_title'] === 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
                            <option value="Dr." <?= $form_data['donor_title'] === 'Dr.' ? 'selected' : '' ?>>Dr.</option>
                            <option value="Prof." <?= $form_data['donor_title'] === 'Prof.' ? 'selected' : '' ?>>Prof.</option>
                        </select>
                        <input type="text" class="form-control col-9" name="donor_name" value="<?= htmlspecialchars($form_data['donor_name']) ?>" placeholder="Donor's Full Name" required>
                    </div>
                </div>
                <label class="col-sm-2 col-form-label">Email Address *</label>
                <div class="col-sm-4">
                    <input type="email" class="form-control" name="donor_email" value="<?= htmlspecialchars($form_data['donor_email']) ?>" placeholder="donor@example.com" required>
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Mobile Number *</label>
                <div class="col-sm-4">
                    <input type="tel" class="form-control" name="donor_mobile" value="<?= htmlspecialchars($form_data['donor_mobile']) ?>" placeholder="10-digit number" required>
                </div>
                <label class="col-sm-2 col-form-label">Date of Birth *</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" name="donor_dob" value="<?= htmlspecialchars($form_data['donor_dob']) ?>" required>
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Alternate Mobile</label>
                <div class="col-sm-4">
                    <input type="tel" class="form-control" name="donor_alt_mobile" value="<?= htmlspecialchars($form_data['donor_alt_mobile']) ?>" placeholder="Optional alternate number">
                </div>
                <label class="col-sm-2 col-form-label">Citizenship</label>
                <div class="col-sm-4">
                    <select class="form-control" name="citizenship">
                        <option value="indian" <?= $form_data['citizenship'] === 'indian' ? 'selected' : '' ?>>Indian Citizen</option>
                        <option value="nri" <?= $form_data['citizenship'] === 'nri' ? 'selected' : '' ?>>NRI (Residing Abroad)</option>
                        <option value="foreign" <?= $form_data['citizenship'] === 'foreign' ? 'selected' : '' ?>>Foreign National</option>
                    </select>
                </div>
            </div>

            <!-- SECTION 2: Donation Preferences -->
            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-hand-holding-usd mr-1"></i> Contribution Info</h5>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Supported Cause</label>
                <div class="col-sm-4">
                    <select class="form-control" name="selected_cause">
                        <option value="feed" <?= $form_data['selected_cause'] === 'feed' ? 'selected' : '' ?>>Feed a Child</option>
                        <option value="slum" <?= $form_data['selected_cause'] === 'slum' ? 'selected' : '' ?>>Educate Slum Children</option>
                        <option value="disaster" <?= $form_data['selected_cause'] === 'disaster' ? 'selected' : '' ?>>Disaster Relief</option>
                        <option value="women" <?= $form_data['selected_cause'] === 'women' ? 'selected' : '' ?>>Women Empowerment</option>
                        <option value="disabled" <?= $form_data['selected_cause'] === 'disabled' ? 'selected' : '' ?>>Support Disabilities</option>
                        <option value="senior" <?= $form_data['selected_cause'] === 'senior' ? 'selected' : '' ?>>Senior Citizen Care</option>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">Donation Interval</label>
                <div class="col-sm-4">
                    <select class="form-control" name="donation_type">
                        <option value="once" <?= $form_data['donation_type'] === 'once' ? 'selected' : '' ?>>One-Time (Once)</option>
                        <option value="monthly" <?= $form_data['donation_type'] === 'monthly' ? 'selected' : '' ?>>Monthly Subscription</option>
                    </select>
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Amount (₹) *</label>
                <div class="col-sm-4">
                    <input type="number" step="0.01" class="form-control font-weight-bold" name="amount" value="<?= htmlspecialchars($form_data['amount']) ?>" required>
                </div>
                <label class="col-sm-2 col-form-label">Payment Status</label>
                <div class="col-sm-4">
                    <select class="form-control" name="payment_status">
                        <option value="paid" <?= $form_data['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid (Completed)</option>
                        <option value="pending" <?= $form_data['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="failed" <?= $form_data['payment_status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </div>
            </div>

            <!-- SECTION 3: Tax Exemption checkbox -->
            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-percent mr-1"></i> Tax Exemption 80(G)</h5>
            
            <div class="form-group row">
                <div class="col-sm-10 offset-sm-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="request80gCheckbox" name="request_80g" value="1" <?= $form_data['request_80g'] === 1 ? 'checked' : '' ?>>
                        <label class="custom-control-label font-weight-bold" for="request80gCheckbox">Issue 80(G) Exemption Certificate (Indian Citizens only)</label>
                    </div>
                </div>
            </div>

            <!-- Sliding accordion for PAN and Billing details -->
            <div id="exemptionAccordion" class="<?= $form_data['request_80g'] === 1 ? '' : 'd-none' ?>">
                <div class="form-group row mt-3">
                    <label class="col-sm-2 col-form-label">PAN Number</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="donor_pan" id="donorPan" value="<?= htmlspecialchars($form_data['donor_pan']) ?>" placeholder="10-character PAN number" style="text-transform: uppercase;" maxlength="10">
                    </div>
                </div>
                <div class="form-group row mt-3">
                    <label class="col-sm-2 col-form-label">Billing Address</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="donor_address" id="donorAddress" rows="2" placeholder="Full Billing Postal Address"><?= htmlspecialchars($form_data['donor_address']) ?></textarea>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success shadow-sm" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important; padding: 8px 30px;"><i class="fas fa-plus-circle mr-1"></i> Log Contribution</button>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const r80g = document.getElementById('request80gCheckbox');
    const accordion = document.getElementById('exemptionAccordion');
    const pan = document.getElementById('donorPan');
    const addr = document.getElementById('donorAddress');

    r80g.addEventListener('change', function() {
        if (this.checked) {
            accordion.classList.remove('d-none');
            pan.setAttribute('required', 'true');
            addr.setAttribute('required', 'true');
        } else {
            accordion.classList.add('d-none');
            pan.removeAttribute('required');
            addr.removeAttribute('required');
        }
    });
});
</script>

<?php include './footer.php'; ?>
