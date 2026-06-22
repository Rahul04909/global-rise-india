<?php
/**
 * admin/volunteer-edit.php
 * ============================================================
 * Volunteer Modification Dashboard
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/includes/auth-check.php'; // Starts session, checks auth, gets DB

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: volunteers.php');
    exit;
}

$pdo = getDB();
$error_msg = '';

// Retrieve volunteer details
$stmt = $pdo->prepare("SELECT * FROM `volunteers` WHERE `id` = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$v = $stmt->fetch();

if (!$v) {
    header('Location: volunteers.php');
    exit;
}

// Fetch all support plans for dropdown
$plans = $pdo->query("SELECT id, title, price FROM `volunteer_plans` ORDER BY price ASC")->fetchAll();

// Generate CSRF token if not set
if (empty($_SESSION['edit_csrf_token'])) {
    $_SESSION['edit_csrf_token'] = bin2hex(random_bytes(32));
}

// Process updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!hash_equals($_SESSION['edit_csrf_token'], $csrf_token)) {
        $error_msg = 'Security token validation failed (CSRF mismatch).';
    } else {
        // Collect parameters
        $full_name        = trim($_POST['full_name'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $phone            = trim($_POST['phone'] ?? '');
        $dob              = trim($_POST['dob'] ?? '');
        $gender           = trim($_POST['gender'] ?? '');
        $city             = trim($_POST['city'] ?? '');
        $state            = trim($_POST['state'] ?? '');
        $pincode          = trim($_POST['pincode'] ?? '');
        $education        = trim($_POST['education'] ?? '');
        $occupation       = trim($_POST['occupation'] ?? '');
        $area_of_interest = trim($_POST['area_of_interest'] ?? '');
        $availability     = trim($_POST['availability'] ?? '');
        $hours_per_week   = (int)($_POST['hours_per_week'] ?? 0);
        $skills           = trim($_POST['skills'] ?? '');
        $motivation       = trim($_POST['motivation'] ?? '');
        $prior_experience = trim($_POST['prior_experience'] ?? 'no');
        $emergency_name   = trim($_POST['emergency_name'] ?? '');
        $emergency_phone  = trim($_POST['emergency_phone'] ?? '');
        
        $status           = trim($_POST['status'] ?? 'pending');
        $plan_id          = (int)($_POST['plan_id'] ?? 0);
        $payment_status   = trim($_POST['payment_status'] ?? 'pending');
        $amount_paid      = (float)($_POST['amount_paid'] ?? 0.00);

        if (empty($full_name) || empty($email) || empty($phone) || empty($area_of_interest)) {
            $error_msg = 'Name, email, mobile number, and area of interest are required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = 'Please enter a valid email address.';
        } else {
            try {
                $update = $pdo->prepare("
                    UPDATE `volunteers`
                    SET 
                        `full_name` = :full_name, `email` = :email, `phone` = :phone, `dob` = :dob,
                        `gender` = :gender, `city` = :city, `state` = :state, `pincode` = :pincode,
                        `education` = :education, `occupation` = :occupation, `area_of_interest` = :area_of_interest,
                        `availability` = :availability, `hours_per_week` = :hours_per_week, `skills` = :skills,
                        `motivation` = :motivation, `prior_experience` = :prior_experience, `emergency_name` = :emergency_name,
                        `emergency_phone` = :emergency_phone, `status` = :status, `plan_id` = :plan_id,
                        `payment_status` = :payment_status, `amount_paid` = :amount_paid
                    WHERE `id` = :id
                ");

                $update->execute([
                    ':full_name'        => $full_name,
                    ':email'            => $email,
                    ':phone'            => $phone,
                    ':dob'              => $dob ?: null,
                    ':gender'           => $gender,
                    ':city'             => $city,
                    ':state'            => $state,
                    ':pincode'          => $pincode,
                    ':education'        => $education,
                    ':occupation'       => $occupation,
                    ':area_of_interest' => $area_of_interest,
                    ':availability'     => $availability,
                    ':hours_per_week'   => $hours_per_week,
                    ':skills'           => $skills,
                    ':motivation'       => $motivation,
                    ':prior_experience' => $prior_experience,
                    ':emergency_name'   => $emergency_name,
                    ':emergency_phone'  => $emergency_phone,
                    ':status'           => $status,
                    ':plan_id'          => $plan_id ?: null,
                    ':payment_status'   => $payment_status,
                    ':amount_paid'      => $amount_paid,
                    ':id'               => $id
                ]);

                header('Location: volunteers.php?updated=1');
                exit;

            } catch (PDOException $e) {
                error_log('[Volunteer Edit Error] ' . $e->getMessage());
                $error_msg = APP_DEBUG ? 'Database Error: ' . $e->getMessage() : 'An error occurred while saving volunteer modifications.';
            }
        }
    }
}

include './header.php';
?>

<div class="row mb-3">
    <div class="col-12">
        <a href="volunteers.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Volunteers List</a>
    </div>
</div>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($error_msg) ?></div>
<?php endif; ?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title font-weight-bold" style="color: var(--sidebar-color);"><i class="fas fa-user-edit mr-1"></i> Edit Volunteer: <?= htmlspecialchars($v['full_name']) ?></h3>
    </div>
    
    <form action="volunteer-edit.php?id=<?= $v['id'] ?>" method="POST" class="form-horizontal">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['edit_csrf_token']) ?>">
        
        <div class="card-body">
            <!-- SECTION 1: Personal Details -->
            <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-address-card mr-1"></i> Personal Details</h5>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Full Name *</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($v['full_name']) ?>" required>
                </div>
                <label class="col-sm-2 col-form-label">Email Address *</label>
                <div class="col-sm-4">
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($v['email']) ?>" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Mobile Number *</label>
                <div class="col-sm-4">
                    <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($v['phone']) ?>" required>
                </div>
                <label class="col-sm-2 col-form-label">Date of Birth</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" name="dob" value="<?= htmlspecialchars($v['dob'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Gender</label>
                <div class="col-sm-4">
                    <select class="form-control" name="gender">
                        <option value="female" <?= $v['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="male" <?= $v['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="non-binary" <?= $v['gender'] === 'non-binary' ? 'selected' : '' ?>>Non-Binary</option>
                        <option value="prefer-not-to-say" <?= $v['gender'] === 'prefer-not-to-say' ? 'selected' : '' ?>>Prefer not to say</option>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">Highest Education</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="education" value="<?= htmlspecialchars($v['education']) ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">City</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($v['city']) ?>">
                </div>
                <label class="col-sm-2 col-form-label">State</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="state" value="<?= htmlspecialchars($v['state']) ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">PIN Code</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="pincode" value="<?= htmlspecialchars($v['pincode']) ?>">
                </div>
                <label class="col-sm-2 col-form-label">Occupation</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="occupation" value="<?= htmlspecialchars($v['occupation']) ?>">
                </div>
            </div>

            <!-- SECTION 2: Volunteer Preferences -->
            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-hand-holding-heart mr-1"></i> Volunteer Preferences</h5>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Interest Area *</label>
                <div class="col-sm-4">
                    <select class="form-control" name="area_of_interest" required>
                        <option>Animal Welfare</option>
                        <option>Disaster Management</option>
                        <option>Educating Slum Children</option>
                        <option>Health Projects</option>
                        <option>Persons with Disabilities</option>
                        <option>Rural Children Education</option>
                        <option>Senior Citizen Care</option>
                        <option>Swachh Bharat Mission</option>
                        <option>Women Empowerment</option>
                    </select>
                    <!-- Ensure correct option is selected -->
                    <script>
                        document.querySelector('[name="area_of_interest"]').value = "<?= htmlspecialchars($v['area_of_interest']) ?>";
                    </script>
                </div>
                <label class="col-sm-2 col-form-label">Availability</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="availability" value="<?= htmlspecialchars($v['availability']) ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Hours / Week</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" name="hours_per_week" value="<?= (int)$v['hours_per_week'] ?>" min="1" max="40">
                </div>
                <label class="col-sm-2 col-form-label">Prior Experience</label>
                <div class="col-sm-4 d-flex align-items-center">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="expYes" name="prior_experience" class="custom-control-input" value="yes" <?= $v['prior_experience'] === 'yes' ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="expYes">Yes</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="expNo" name="prior_experience" class="custom-control-input" value="no" <?= $v['prior_experience'] === 'no' ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="expNo">No</label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Skills</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="skills" value="<?= htmlspecialchars($v['skills']) ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Motivation</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="motivation" rows="3"><?= htmlspecialchars($v['motivation']) ?></textarea>
                </div>
            </div>

            <!-- SECTION 3: Emergency Contact -->
            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-phone-alt mr-1"></i> Emergency Contact</h5>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Contact Name</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="emergency_name" value="<?= htmlspecialchars($v['emergency_name']) ?>">
                </div>
                <label class="col-sm-2 col-form-label">Contact Phone</label>
                <div class="col-sm-4">
                    <input type="tel" class="form-control" name="emergency_phone" value="<?= htmlspecialchars($v['emergency_phone']) ?>">
                </div>
            </div>

            <!-- SECTION 4: Status and Plans Support -->
            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-money-check-alt mr-1"></i> Support Plan & Approval Status</h5>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Support Plan</label>
                <div class="col-sm-4">
                    <select class="form-control" name="plan_id">
                        <option value="">— No Plan Selected (Free) —</option>
                        <?php foreach ($plans as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $v['plan_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['title']) ?> (₹<?= number_format($p['price'], 2) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">Amount Paid (₹)</label>
                <div class="col-sm-4">
                    <input type="number" step="0.01" class="form-control" name="amount_paid" value="<?= (float)$v['amount_paid'] ?>">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Payment Status</label>
                <div class="col-sm-4">
                    <select class="form-control" name="payment_status">
                        <option value="pending" <?= $v['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= $v['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="failed" <?= $v['payment_status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">Approval Status</label>
                <div class="col-sm-4">
                    <select class="form-control" name="status">
                        <option value="pending" <?= $v['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $v['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $v['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
            </div>

            <!-- Razorpay metadata -->
            <div class="form-group row bg-light p-3 rounded mx-1 mt-3">
                <div class="col-md-4">
                    <span class="text-muted d-block small">Razorpay Order ID</span>
                    <span class="font-family-monospace text-dark font-weight-500"><?= htmlspecialchars($v['razorpay_order_id']) ?: '—' ?></span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block small">Razorpay Payment ID</span>
                    <span class="font-family-monospace text-dark font-weight-500"><?= htmlspecialchars($v['razorpay_payment_id']) ?: '—' ?></span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block small">Razorpay Signature Verified</span>
                    <span class="font-family-monospace text-dark font-weight-500"><?= $v['razorpay_signature'] ? 'Yes <i class="fas fa-check-circle text-success ml-1"></i>' : 'No' ?></span>
                </div>
            </div>

        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important; padding: 8px 30px;"><i class="fas fa-save mr-1"></i> Save Changes</button>
        </div>
    </form>
</div>

<?php include './footer.php'; ?>
