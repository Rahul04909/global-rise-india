<?php
/**
 * admin/volunteer-view.php
 * ============================================================
 * Volunteer Profile Viewer Panel
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

// Fetch volunteer with plan details
$stmt = $pdo->prepare("
    SELECT v.*, p.title AS plan_title, p.price AS plan_price 
    FROM `volunteers` v 
    LEFT JOIN `volunteer_plans` p ON v.plan_id = p.id 
    WHERE v.id = :id 
    LIMIT 1
");
$stmt->execute([':id' => $id]);
$v = $stmt->fetch();

if (!$v) {
    header('Location: volunteers.php');
    exit;
}

include './header.php';

// Prepare status and payment styling classes
$status_badge = 'secondary';
$status_icon = 'clock';
if ($v['status'] === 'approved') {
    $status_badge = 'success';
    $status_icon = 'check-circle';
} elseif ($v['status'] === 'rejected') {
    $status_badge = 'danger';
    $status_icon = 'times-circle';
}

$pay_badge = 'secondary';
$pay_icon = 'hourglass-half';
if ($v['payment_status'] === 'paid') {
    $pay_badge = 'success';
    $pay_icon = 'check';
} elseif ($v['payment_status'] === 'failed') {
    $pay_badge = 'danger';
    $pay_icon = 'exclamation-triangle';
}
?>

<!-- Back & Actions Row -->
<div class="row mb-3">
    <div class="col-sm-6">
        <a href="volunteers.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Volunteers</a>
    </div>
    <div class="col-sm-6 text-right">
        <a href="volunteer-edit.php?id=<?= $v['id'] ?>" class="btn btn-info mr-2">
            <i class="fas fa-user-edit mr-1"></i> Edit Profile
        </a>
        <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $v['id'] ?>)">
            <i class="fas fa-trash-alt mr-1"></i> Delete Volunteer
        </button>
    </div>
</div>

<div class="row">
    <!-- Left Column: Quick Summary Card -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile text-center py-4">
                <div class="mb-3 text-center">
                    <div class="d-inline-flex justify-content-center align-items-center bg-light rounded-circle shadow-sm" style="width: 110px; height: 110px;">
                        <i class="fas fa-user text-secondary" style="font-size: 3.5rem;"></i>
                    </div>
                </div>

                <h3 class="profile-username font-weight-bold text-dark mt-2 mb-1"><?= htmlspecialchars($v['full_name']) ?></h3>
                <p class="text-muted mb-3"><i class="fas fa-tag mr-1 small"></i> Volunteer ID: #VOL-<?= str_pad($v['id'], 5, '0', STR_PAD_LEFT) ?></p>

                <!-- Key Badges -->
                <div class="mb-3">
                    <span class="badge badge-<?= $status_badge ?> py-2 px-3 text-capitalize mr-1" style="font-size: 0.85rem;">
                        <i class="fas fa-<?= $status_icon ?> mr-1"></i> <?= htmlspecialchars($v['status']) ?>
                    </span>
                    <span class="badge badge-<?= $pay_badge ?> py-2 px-3 text-capitalize" style="font-size: 0.85rem;">
                        <i class="fas fa-<?= $pay_icon ?> mr-1"></i> <?= htmlspecialchars($v['payment_status']) ?>
                    </span>
                </div>

                <ul class="list-group list-group-unbordered mb-3 mt-4 text-left">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="font-weight-600 text-muted"><i class="fas fa-envelope mr-2"></i>Email</span>
                        <a href="mailto:<?= htmlspecialchars($v['email']) ?>" class="text-dark font-weight-500 text-truncate" style="max-width: 200px;"><?= htmlspecialchars($v['email']) ?></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="font-weight-600 text-muted"><i class="fas fa-phone mr-2"></i>Mobile</span>
                        <a href="tel:<?= htmlspecialchars($v['phone']) ?>" class="text-dark font-weight-500"><?= htmlspecialchars($v['phone']) ?></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="font-weight-600 text-muted"><i class="fas fa-map-marker-alt mr-2"></i>Location</span>
                        <span class="text-dark font-weight-500"><?= htmlspecialchars($v['city'] ?: '—') ?>, <?= htmlspecialchars($v['state'] ?: '—') ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="font-weight-600 text-muted"><i class="fas fa-calendar-alt mr-2"></i>Join Date</span>
                        <span class="text-dark font-weight-500"><?= date('d M, Y', strtotime($v['created_at'])) ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Full Details Card -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2 bg-light">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active font-weight-bold" href="#personal" data-toggle="tab"><i class="fas fa-user-circle mr-1"></i> Personal & Preferences</a></li>
                    <li class="nav-item"><a class="nav-link font-weight-bold" href="#financial" data-toggle="tab"><i class="fas fa-wallet mr-1"></i> Support Plan Details</a></li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- TAB 1: Personal & Preferences -->
                    <div class="active tab-pane" id="personal">
                        <!-- Personal Metadata -->
                        <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3"><i class="fas fa-address-book mr-1"></i> Profile & Background</h5>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Gender</span>
                                <span class="text-dark font-weight-600 text-capitalize"><?= htmlspecialchars($v['gender'] ?: '—') ?></span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Date of Birth</span>
                                <span class="text-dark font-weight-600"><?= $v['dob'] ? date('d M, Y', strtotime($v['dob'])) : '—' ?></span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">PIN Code</span>
                                <span class="text-dark font-weight-600"><?= htmlspecialchars($v['pincode'] ?: '—') ?></span>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Highest Education</span>
                                <span class="text-dark font-weight-600"><?= htmlspecialchars($v['education'] ?: '—') ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Occupation</span>
                                <span class="text-dark font-weight-600"><?= htmlspecialchars($v['occupation'] ?: '—') ?></span>
                            </div>
                        </div>

                        <!-- Volunteer Preferences -->
                        <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3 mt-4"><i class="fas fa-hand-holding-heart mr-1"></i> Volunteering Settings</h5>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Area of Interest</span>
                                <span class="badge badge-info py-2 px-3 text-white font-weight-600" style="font-size: 0.85rem;"><?= htmlspecialchars($v['area_of_interest']) ?></span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Availability</span>
                                <span class="text-dark font-weight-600"><?= htmlspecialchars($v['availability'] ?: '—') ?></span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Commitment Time</span>
                                <span class="text-dark font-weight-600"><?= (int)$v['hours_per_week'] ?> hours / week</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Prior Experience</span>
                                <span class="text-dark font-weight-600 text-capitalize"><?= htmlspecialchars($v['prior_experience'] ?: 'no') ?></span>
                            </div>
                            <div class="col-sm-8">
                                <span class="text-muted d-block small">Skills & Talents</span>
                                <span class="text-dark font-weight-600"><?= htmlspecialchars($v['skills'] ?: '—') ?></span>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <span class="text-muted d-block small">Motivation Statement</span>
                                <div class="bg-light p-3 rounded text-dark font-italic border-left border-primary" style="font-size: 0.95rem; white-space: pre-wrap;"><?= htmlspecialchars($v['motivation'] ?: 'No statement provided.') ?></div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3 mt-4"><i class="fas fa-phone-alt mr-1"></i> Emergency Contact</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Contact Person Name</span>
                                <span class="text-dark font-weight-600"><?= htmlspecialchars($v['emergency_name'] ?: '—') ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Contact Number</span>
                                <?php if ($v['emergency_phone']): ?>
                                    <span class="text-dark font-weight-600"><a href="tel:<?= htmlspecialchars($v['emergency_phone']) ?>" class="text-dark"><?= htmlspecialchars($v['emergency_phone']) ?></a></span>
                                <?php else: ?>
                                    <span class="text-dark font-weight-600">—</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Financial Details -->
                    <div class="tab-pane" id="financial">
                        <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3"><i class="fas fa-money-check-alt mr-1"></i> Plan Subscription</h5>
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Assigned Support Plan</span>
                                <?php if ($v['plan_id']): ?>
                                    <span class="text-dark font-weight-bold" style="font-size: 1.1rem; color: var(--sidebar-color);"><?= htmlspecialchars($v['plan_title']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted font-italic">No Plan Selected (Free Registration)</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Amount Subscribed / Paid</span>
                                <span class="text-success font-weight-bold" style="font-size: 1.1rem;">₹<?= number_format($v['amount_paid'], 2) ?></span>
                            </div>
                        </div>

                        <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3 mt-4"><i class="fab fa-cc-amazon-pay mr-1"></i> Razorpay Transaction Metadata</h5>
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Order ID</span>
                                <code class="text-dark font-weight-600"><?= htmlspecialchars($v['razorpay_order_id'] ?: '—') ?></code>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Payment ID</span>
                                <code class="text-dark font-weight-600"><?= htmlspecialchars($v['razorpay_payment_id'] ?: '—') ?></code>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <span class="text-muted d-block small">Razorpay Signature Verification Status</span>
                                <?php if ($v['razorpay_signature']): ?>
                                    <span class="badge badge-success py-2 px-3"><i class="fas fa-check-circle mr-1"></i> Securely Verified Signature</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary py-2 px-3"><i class="fas fa-info-circle mr-1"></i> Manual/Admin Enrolled (No Signature)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Delete Volunteer Record?',
        text: "This action cannot be undone! The registration history will be removed.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'volunteers.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include './footer.php'; ?>
