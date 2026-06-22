<?php
/**
 * admin/volunteer-add.php
 * ============================================================
 * Volunteer Creation / Enrollment Dashboard
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/includes/auth-check.php'; // Starts session, checks auth, gets DB

$pdo = getDB();
$error_msg = '';

// Fetch all support plans for dropdown
$plans = $pdo->query("SELECT id, title, price FROM `volunteer_plans` ORDER BY price ASC")->fetchAll();

// Generate CSRF token if not set
if (empty($_SESSION['add_csrf_token'])) {
    $_SESSION['add_csrf_token'] = bin2hex(random_bytes(32));
}

// Fallback values for form fields in case of validation error
$form_data = [
    'full_name'        => '',
    'email'            => '',
    'phone'            => '',
    'dob'              => '',
    'gender'           => 'female',
    'city'             => '',
    'state'            => '',
    'pincode'          => '',
    'education'        => '',
    'occupation'       => '',
    'area_of_interest' => 'Animal Welfare',
    'availability'     => '',
    'hours_per_week'   => 4,
    'skills'           => '',
    'motivation'       => '',
    'prior_experience' => 'no',
    'emergency_name'   => '',
    'emergency_phone'  => '',
    'status'           => 'pending',
    'plan_id'          => '',
    'payment_status'   => 'pending',
    'amount_paid'      => '0.00'
];

// Process creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Fill form data with POST to persist inputs
    foreach (array_keys($form_data) as $key) {
        if (isset($_POST[$key])) {
            $form_data[$key] = $_POST[$key];
        }
    }
    
    if (!hash_equals($_SESSION['add_csrf_token'], $csrf_token)) {
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
                // Check if email already exists
                $email_check = $pdo->prepare("SELECT COUNT(*) FROM `volunteers` WHERE `email` = :email");
                $email_check->execute([':email' => $email]);
                if ($email_check->fetchColumn() > 0) {
                    $error_msg = 'The email address is already registered to a volunteer.';
                } else {
                    $insert = $pdo->prepare("
                        INSERT INTO `volunteers` (
                            `full_name`, `email`, `phone`, `dob`, `gender`, `city`, `state`, `pincode`,
                            `education`, `occupation`, `area_of_interest`, `availability`, `hours_per_week`,
                            `skills`, `motivation`, `prior_experience`, `emergency_name`, `emergency_phone`,
                            `status`, `plan_id`, `payment_status`, `amount_paid`, `created_at`
                        ) VALUES (
                            :full_name, :email, :phone, :dob, :gender, :city, :state, :pincode,
                            :education, :occupation, :area_of_interest, :availability, :hours_per_week,
                            :skills, :motivation, :prior_experience, :emergency_name, :emergency_phone,
                            :status, :plan_id, :payment_status, :amount_paid, NOW()
                        )
                    ");

                    $insert->execute([
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
                        ':amount_paid'      => $amount_paid
                    ]);

                    header('Location: volunteers.php?added=1');
                    exit;
                }
            } catch (PDOException $e) {
                error_log('[Volunteer Add Error] ' . $e->getMessage());
                $error_msg = APP_DEBUG ? 'Database Error: ' . $e->getMessage() : 'An error occurred while saving the new volunteer record.';
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
        <h3 class="card-title font-weight-bold" style="color: var(--sidebar-color);"><i class="fas fa-user-plus mr-1"></i> Enroll New Volunteer</h3>
    </div>
    
    <form action="volunteer-add.php" method="POST" class="form-horizontal">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['add_csrf_token']) ?>">
        
        <div class="card-body">
            <!-- SECTION 1: Personal Details -->
            <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fas fa-address-card mr-1"></i> Personal Details</h5>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Full Name *</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($form_data['full_name']) ?>" placeholder="e.g. John Doe" required>
                </div>
                <label class="col-sm-2 col-form-label">Email Address *</label>
                <div class="col-sm-4">
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($form_data['email']) ?>" placeholder="e.g. john@example.com" required>
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Mobile Number *</label>
                <div class="col-sm-4">
                    <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($form_data['phone']) ?>" placeholder="e.g. +91 9876543210" required>
                </div>
                <label class="col-sm-2 col-form-label">Date of Birth</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" name="dob" value="<?= htmlspecialchars($form_data['dob']) ?>">
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Gender</label>
                <div class="col-sm-4">
                    <select class="form-control" name="gender">
                        <option value="female" <?= $form_data['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="male" <?= $form_data['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="non-binary" <?= $form_data['gender'] === 'non-binary' ? 'selected' : '' ?>>Non-Binary</option>
                        <option value="prefer-not-to-say" <?= $form_data['gender'] === 'prefer-not-to-say' ? 'selected' : '' ?>>Prefer not to say</option>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">Highest Education</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="education" value="<?= htmlspecialchars($form_data['education']) ?>" placeholder="e.g. Bachelor of Science">
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">City</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($form_data['city']) ?>" placeholder="e.g. New Delhi">
                </div>
                <label class="col-sm-2 col-form-label">State</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="state" value="<?= htmlspecialchars($form_data['state']) ?>" placeholder="e.g. Delhi">
                </div>
            </div>

            <div class="form-group row mt-3 mb-4">
                <label class="col-sm-2 col-form-label">PIN Code</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="pincode" value="<?= htmlspecialchars($form_data['pincode']) ?>" placeholder="e.g. 110001">
                </div>
                <label class="col-sm-2 col-form-label">Occupation</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="occupation" value="<?= htmlspecialchars($form_data['occupation']) ?>" placeholder="e.g. Software Engineer">
                </div>
            </div>

            <!-- SECTION 2: Volunteer Preferences -->
            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-hand-holding-heart mr-1"></i> Volunteer Preferences</h5>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Interest Area *</label>
                <div class="col-sm-4">
                    <select class="form-control" name="area_of_interest" required>
                        <option <?= $form_data['area_of_interest'] === 'Animal Welfare' ? 'selected' : '' ?>>Animal Welfare</option>
                        <option <?= $form_data['area_of_interest'] === 'Disaster Management' ? 'selected' : '' ?>>Disaster Management</option>
                        <option <?= $form_data['area_of_interest'] === 'Educating Slum Children' ? 'selected' : '' ?>>Educating Slum Children</option>
                        <option <?= $form_data['area_of_interest'] === 'Health Projects' ? 'selected' : '' ?>>Health Projects</option>
                        <option <?= $form_data['area_of_interest'] === 'Persons with Disabilities' ? 'selected' : '' ?>>Persons with Disabilities</option>
                        <option <?= $form_data['area_of_interest'] === 'Rural Children Education' ? 'selected' : '' ?>>Rural Children Education</option>
                        <option <?= $form_data['area_of_interest'] === 'Senior Citizen Care' ? 'selected' : '' ?>>Senior Citizen Care</option>
                        <option <?= $form_data['area_of_interest'] === 'Swachh Bharat Mission' ? 'selected' : '' ?>>Swachh Bharat Mission</option>
                        <option <?= $form_data['area_of_interest'] === 'Women Empowerment' ? 'selected' : '' ?>>Women Empowerment</option>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">Availability</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="availability" value="<?= htmlspecialchars($form_data['availability']) ?>" placeholder="e.g. Weekends, Evenings">
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Hours / Week</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" name="hours_per_week" value="<?= (int)$form_data['hours_per_week'] ?>" min="1" max="40">
                </div>
                <label class="col-sm-2 col-form-label">Prior Experience</label>
                <div class="col-sm-4 d-flex align-items-center">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="expYes" name="prior_experience" class="custom-control-input" value="yes" <?= $form_data['prior_experience'] === 'yes' ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="expYes">Yes</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline ml-3">
                        <input type="radio" id="expNo" name="prior_experience" class="custom-control-input" value="no" <?= $form_data['prior_experience'] === 'no' ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="expNo">No</label>
                    </div>
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Skills</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="skills" value="<?= htmlspecialchars($form_data['skills']) ?>" placeholder="e.g. Teaching, Public Relations, Graphic Design">
                </div>
            </div>

            <div class="form-group row mt-3 mb-4">
                <label class="col-sm-2 col-form-label">Motivation</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="motivation" rows="3" placeholder="Why does the volunteer want to join?"><?= htmlspecialchars($form_data['motivation']) ?></textarea>
                </div>
            </div>

            <!-- SECTION 3: Emergency Contact -->
            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fas fa-phone-alt mr-1"></i> Emergency Contact</h5>
            
            <div class="form-group row mb-4">
                <label class="col-sm-2 col-form-label">Contact Name</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="emergency_name" value="<?= htmlspecialchars($form_data['emergency_name']) ?>" placeholder="Emergency Contact Name">
                </div>
                <label class="col-sm-2 col-form-label">Contact Phone</label>
                <div class="col-sm-4">
                    <input type="tel" class="form-control" name="emergency_phone" value="<?= htmlspecialchars($form_data['emergency_phone']) ?>" placeholder="Emergency Contact Phone">
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
                            <option value="<?= $p['id'] ?>" <?= $form_data['plan_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['title']) ?> (₹<?= number_format($p['price'], 2) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">Amount Paid (₹)</label>
                <div class="col-sm-4">
                    <input type="number" step="0.01" class="form-control" name="amount_paid" value="<?= htmlspecialchars($form_data['amount_paid']) ?>">
                </div>
            </div>

            <div class="form-group row mt-3">
                <label class="col-sm-2 col-form-label">Payment Status</label>
                <div class="col-sm-4">
                    <select class="form-control" name="payment_status">
                        <option value="pending" <?= $form_data['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= $form_data['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="failed" <?= $form_data['payment_status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">Approval Status</label>
                <div class="col-sm-4">
                    <select class="form-control" name="status">
                        <option value="pending" <?= $form_data['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $form_data['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $form_data['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
            </div>

        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important; padding: 8px 30px;"><i class="fas fa-plus-circle mr-1"></i> Enroll Volunteer</button>
        </div>
    </form>
</div>

<?php include './footer.php'; ?>
