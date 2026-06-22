<?php
/**
 * admin/donation-edit.php
 * ============================================================
 * Donation Editor Dashboard
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/includes/auth-check.php'; // Starts session, checks auth, gets DB

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: donations.php');
    exit;
}

$pdo = getDB();
$error_msg = '';

// Retrieve donation details
$stmt = $pdo->prepare("SELECT * FROM `donations` WHERE `id` = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$d = $stmt->fetch();

if (!$d) {
    header('Location: donations.php');
    exit;
}

// Generate CSRF token if not set
if (empty($_SESSION['don_edit_csrf_token'])) {
    $_SESSION['don_edit_csrf_token'] = bin2hex(random_bytes(32));
}

// Process updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!hash_equals($_SESSION['don_edit_csrf_token'], $csrf_token)) {
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
        $payment_status   = trim($_POST['payment_status'] ?? 'pending');

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
                $update = $pdo->prepare("
                    UPDATE `donations`
                    SET 
                        `donor_title` = :donor_title,
                        `donor_name` = :donor_name,
                        `donor_email` = :donor_email,
                        `donor_mobile` = :donor_mobile,
                        `donor_dob` = :donor_dob,
                        `donor_alt_mobile` = :donor_alt_mobile,
                        `citizenship` = :citizenship,
                        `donation_type` = :donation_type,
                        `selected_cause` = :selected_cause,
                        `amount` = :amount,
                        `request_80g` = :request_80g,
                        `donor_pan` = :donor_pan,
                        `donor_address` = :donor_address,
                        `payment_status` = :payment_status
                    WHERE `id` = :id
                ");

                $update->execute([
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
                    ':payment_status'   => $payment_status,
                    ':id'               => $id
                ]);

                header('Location: donations.php?updated=1');
                exit;

            } catch (PDOException $e) {
                error_log('[Donation Edit Error] ' . $e->getMessage());
                $error_msg = APP_DEBUG ? 'Database Error: ' . $e->getMessage() : 'An error occurred while saving the updates.';
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
        <h3 class="card-title font-weight-bold" style="color: var(--sidebar-color);"><i class="fas fa-edit mr-1"></i> Edit Donation Details: #DON-<?= str_pad($d['id'], 5, '0', STR_PAD_LEFT) ?></h3>
    </div>
    
    <form action="donation-edit.php?id=<?= $d['id'] ?>" method="POST" class="form-horizontal">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['don_edit_csrf_token']) ?>">
        
        <div class="card-body">
            <!-- SECTION 1: Personal Details -->
            <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-address-card mr-1"></i> Personal Details</h5>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Full Name *</label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <select name="donor_title" class="form-control col-3">
                            <option value="Mr." <?= $d['donor_title'] === 'Mr.' ? 'selected' : '' ?>>Mr.</option>
                            <option value="Ms." <?= $d['donor_title'] === 'Ms.' ? 'selected' : '' ?>>Ms.</option>
                            <option value="Mrs." <?= $d['donor_title'] === 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
                            <option value="Dr." <?= $d['donor_title'] === 'Dr.' ? 'selected' : '' ?>>Dr.</option>
                            <option value="Prof." <?= $d['donor_title'] === 'Prof.' ? 'selected' : '' ?>>Prof.</option>
                        </select>
                        <input type="text" class="form-control col-9" name="donor_name" value="<?= htmlspecialchars($d['donor_name']) ?>" required>
                    </div>
                </div>
                <label class="col-sm-2 col-form-label">Email Address *</label>
                <div class="col-sm-4">
                    <input type="email" class="form-control" name="donor_email" value="<?= htmlspecialchars($d['donor_email']) ?>" required>
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Mobile Number *</label>
                <div class="col-sm-4">
                    <input type="tel" class="form-control" name="donor_mobile" value="<?= htmlspecialchars($d['donor_mobile']) ?>" required>
                </div>
                <label class="col-sm-2 col-form-label">Date of Birth *</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" name="donor_dob" value="<?= htmlspecialchars($d['donor_dob']) ?>" required>
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Alternate Mobile</label>
                <div class="col-sm-4">
                    <input type="tel" class="form-control" name="donor_alt_mobile" value="<?= htmlspecialchars($d['donor_alt_mobile'] ?? '') ?>">
                </div>
                <label class="col-sm-2 col-form-label">Citizenship</label>
                <div class="col-sm-4">
                    <select class="form-control" name="citizenship">
                        <option value="indian" <?= $d['citizenship'] === 'indian' ? 'selected' : '' ?>>Indian Citizen</option>
                        <option value="nri" <?= $d['citizenship'] === 'nri' ? 'selected' : '' ?>>NRI (Residing Abroad)</option>
                        <option value="foreign" <?= $d['citizenship'] === 'foreign' ? 'selected' : '' ?>>Foreign National</option>
                    </select>
                </div>
            </div>

            <!-- SECTION 2: Donation Preferences -->
            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-hand-holding-usd mr-1"></i> Contribution Info</h5>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Supported Cause</label>
                <div class="col-sm-4">
                    <select class="form-control" name="selected_cause">
                        <option value="feed" <?= $d['selected_cause'] === 'feed' ? 'selected' : '' ?>>Feed a Child</option>
                        <option value="slum" <?= $d['selected_cause'] === 'slum' ? 'selected' : '' ?>>Educate Slum Children</option>
                        <option value="disaster" <?= $d['selected_cause'] === 'disaster' ? 'selected' : '' ?>>Disaster Relief</option>
                        <option value="women" <?= $d['selected_cause'] === 'women' ? 'selected' : '' ?>>Women Empowerment</option>
                        <option value="disabled" <?= $d['selected_cause'] === 'disabled' ? 'selected' : '' ?>>Support Disabilities</option>
                        <option value="senior" <?= $d['selected_cause'] === 'senior' ? 'selected' : '' ?>>Senior Citizen Care</option>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">Donation Interval</label>
                <div class="col-sm-4">
                    <select class="form-control" name="donation_type">
                        <option value="once" <?= $d['donation_type'] === 'once' ? 'selected' : '' ?>>One-Time (Once)</option>
                        <option value="monthly" <?= $d['donation_type'] === 'monthly' ? 'selected' : '' ?>>Monthly Subscription</option>
                    </select>
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Amount (₹) *</label>
                <div class="col-sm-4">
                    <input type="number" step="0.01" class="form-control font-weight-bold" name="amount" value="<?= (float)$d['amount'] ?>" required>
                </div>
                <label class="col-sm-2 col-form-label">Payment Status</label>
                <div class="col-sm-4">
                    <select class="form-control" name="payment_status">
                        <option value="paid" <?= $d['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid (Completed)</option>
                        <option value="pending" <?= $d['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="failed" <?= $d['payment_status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </div>
            </div>

            <!-- SECTION 3: Tax Exemption checkbox -->
            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-percent mr-1"></i> Tax Exemption 80(G)</h5>
            
            <div class="form-group row">
                <div class="col-sm-10 offset-sm-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="request80gCheckbox" name="request_80g" value="1" <?= $d['request_80g'] == 1 ? 'checked' : '' ?>>
                        <label class="custom-control-label font-weight-bold" for="request80gCheckbox">Issue 80(G) Exemption Certificate (Indian Citizens only)</label>
                    </div>
                </div>
            </div>

            <!-- Sliding accordion for PAN and Billing details -->
            <div id="exemptionAccordion" class="<?= $d['request_80g'] == 1 ? '' : 'd-none' ?>">
                <div class="form-group row mt-3">
                    <label class="col-sm-2 col-form-label">PAN Number</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="donor_pan" id="donorPan" value="<?= htmlspecialchars($d['donor_pan'] ?? '') ?>" placeholder="10-character PAN number" style="text-transform: uppercase;" maxlength="10">
                    </div>
                </div>
                <div class="form-group row mt-3">
                    <label class="col-sm-2 col-form-label">Billing Address</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="donor_address" id="donorAddress" rows="2" placeholder="Full Billing Postal Address"><?= htmlspecialchars($d['donor_address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Razorpay metadata -->
            <div class="form-group row bg-light p-3 rounded mx-1 mt-4">
                <div class="col-md-4">
                    <span class="text-muted d-block small">Razorpay Order ID</span>
                    <span class="font-family-monospace text-dark font-weight-500"><?= htmlspecialchars($d['razorpay_order_id'] ?? '') ?: '—' ?></span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block small">Razorpay Payment ID</span>
                    <span class="font-family-monospace text-dark font-weight-500"><?= htmlspecialchars($d['razorpay_payment_id'] ?? '') ?: '—' ?></span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block small">Razorpay Signature Verified</span>
                    <span class="font-family-monospace text-dark font-weight-500"><?= $d['razorpay_signature'] ? 'Yes <i class="fas fa-check-circle text-success ml-1"></i>' : 'No' ?></span>
                </div>
            </div>

        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success shadow-sm" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important; padding: 8px 30px;"><i class="fas fa-save mr-1"></i> Save Changes</button>
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
