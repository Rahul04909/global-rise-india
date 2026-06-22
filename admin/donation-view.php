<?php
/**
 * admin/donation-view.php
 * ============================================================
 * Donation Profile Viewer Panel
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

// Fetch donation details
$stmt = $pdo->prepare("SELECT * FROM `donations` WHERE `id` = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$d = $stmt->fetch();

if (!$d) {
    header('Location: donations.php');
    exit;
}

include './header.php';

// Prepare status and payment styling classes
$status_badge = 'secondary';
$status_icon = 'hourglass-half';
if ($d['payment_status'] === 'paid') {
    $status_badge = 'success';
    $status_icon = 'check-circle';
} elseif ($d['payment_status'] === 'failed') {
    $status_badge = 'danger';
    $status_icon = 'times-circle';
}

$type_badge = 'info';
if ($d['donation_type'] === 'monthly') {
    $type_badge = 'warning';
}

// Map causes codes to friendly names
$causeMap = [
    'feed'     => 'Feed a Child (Mid-Day Meals)',
    'slum'     => 'Educate Slum Children',
    'disaster' => 'Disaster Relief & Mitigation',
    'women'    => 'Women Empowerment',
    'disabled' => 'Support Persons with Disabilities',
    'senior'   => 'Senior Citizen Care'
];
?>

<!-- Back & Actions Row -->
<div class="row mb-3">
    <div class="col-sm-6">
        <a href="donations.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Donations</a>
    </div>
    <div class="col-sm-6 text-right">
        <a href="donation-edit.php?id=<?= $d['id'] ?>" class="btn btn-info mr-2">
            <i class="fas fa-edit mr-1"></i> Edit Donation
        </a>
        <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $d['id'] ?>)">
            <i class="fas fa-trash-alt mr-1"></i> Delete Record
        </button>
    </div>
</div>

<div class="row">
    <!-- Left Column: Quick Donor Card -->
    <div class="col-md-4">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-body box-profile text-center py-4">
                <div class="mb-3 text-center">
                    <div class="d-inline-flex justify-content-center align-items-center bg-light rounded-circle shadow-sm" style="width: 110px; height: 110px;">
                        <i class="fas fa-hand-holding-heart text-danger" style="font-size: 3.5rem;"></i>
                    </div>
                </div>

                <h3 class="profile-username font-weight-bold text-dark mt-2 mb-1"><?= htmlspecialchars($d['donor_title']) ?> <?= htmlspecialchars($d['donor_name']) ?></h3>
                <p class="text-muted mb-3"><i class="fas fa-tag mr-1 small"></i> Receipt ID: #DON-<?= str_pad($d['id'], 5, '0', STR_PAD_LEFT) ?></p>

                <!-- Key Badges -->
                <div class="mb-3">
                    <span class="badge badge-<?= $status_badge ?> py-2 px-3 text-capitalize mr-1" style="font-size: 0.85rem;">
                        <i class="fas fa-<?= $status_icon ?> mr-1"></i> <?= htmlspecialchars($d['payment_status']) ?>
                    </span>
                    <span class="badge badge-<?= $type_badge ?> py-2 px-3 text-capitalize" style="font-size: 0.85rem;">
                        <?= htmlspecialchars($d['donation_type']) ?>
                    </span>
                </div>

                <ul class="list-group list-group-unbordered mb-3 mt-4 text-left">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="font-weight-600 text-muted"><i class="fas fa-envelope mr-2"></i>Email</span>
                        <a href="mailto:<?= htmlspecialchars($d['donor_email']) ?>" class="text-dark font-weight-500 text-truncate" style="max-width: 180px;"><?= htmlspecialchars($d['donor_email']) ?></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="font-weight-600 text-muted"><i class="fas fa-phone mr-2"></i>Mobile</span>
                        <a href="tel:<?= htmlspecialchars($d['donor_mobile']) ?>" class="text-dark font-weight-500"><?= htmlspecialchars($d['donor_mobile']) ?></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="font-weight-600 text-muted"><i class="fas fa-globe mr-2"></i>Citizenship</span>
                        <span class="text-dark font-weight-500 text-capitalize"><?= htmlspecialchars($d['citizenship']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="font-weight-600 text-muted"><i class="fas fa-calendar-day mr-2"></i>Date</span>
                        <span class="text-dark font-weight-500"><?= date('d M, Y', strtotime($d['created_at'])) ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Detail Information Cards -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header p-2 bg-light">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active font-weight-bold" href="#donation" data-toggle="tab"><i class="fas fa-hand-holding-usd mr-1"></i> Donation Details</a></li>
                    <li class="nav-item"><a class="nav-link font-weight-bold" href="#tax" data-toggle="tab"><i class="fas fa-percent mr-1"></i> Tax Exemption (80G)</a></li>
                    <li class="nav-item"><a class="nav-link font-weight-bold" href="#payment" data-toggle="tab"><i class="fas fa-credit-card mr-1"></i> Payment Metadata</a></li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- TAB 1: Donation Details -->
                    <div class="active tab-pane" id="donation">
                        <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3"><i class="fas fa-heart text-danger mr-1"></i> Contribution</h5>
                        
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Donated Amount</span>
                                <span class="text-success font-weight-bold" style="font-size: 1.4rem;">₹<?= number_format($d['amount'], 2) ?></span>
                            </div>
                            <div class="col-sm-8">
                                <span class="text-muted d-block small">Supported Project Cause</span>
                                <span class="text-dark font-weight-600" style="font-size: 1.1rem;"><?= htmlspecialchars($causeMap[$d['selected_cause']] ?? $d['selected_cause']) ?></span>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Interval / Type</span>
                                <span class="text-dark font-weight-600 text-capitalize"><?= htmlspecialchars($d['donation_type']) ?> Contribution</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Donor Date of Birth</span>
                                <span class="text-dark font-weight-600"><?= $d['donor_dob'] ? date('d M, Y', strtotime($d['donor_dob'])) : '—' ?></span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Alternate Mobile</span>
                                <span class="text-dark font-weight-600"><?= htmlspecialchars($d['donor_alt_mobile'] ?: '—') ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Tax Exemption Details -->
                    <div class="tab-pane" id="tax">
                        <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3"><i class="fas fa-file-invoice mr-1"></i> 80G Certification Exemption</h5>
                        
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Requested 80G?</span>
                                <?php if ($d['request_80g']): ?>
                                    <span class="badge badge-warning py-2 px-3 font-weight-bold text-dark"><i class="fas fa-percent"></i> Yes, Requested</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary py-2 px-3 font-weight-bold">No Exemption Requested</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-8">
                                <span class="text-muted d-block small">PAN Card Number</span>
                                <span class="text-dark font-weight-bold font-family-monospace" style="font-size: 1.1rem; text-transform: uppercase;"><?= htmlspecialchars($d['donor_pan'] ?: '—') ?></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <span class="text-muted d-block small">Billing Address</span>
                                <div class="bg-light p-3 rounded text-dark font-weight-500 border" style="font-size: 0.95rem; white-space: pre-wrap; background-color: #f8f9fa; border: 1px solid #e2e8f0;"><?= htmlspecialchars($d['donor_address'] ?: 'No address logged.') ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Razorpay Payment Details -->
                    <div class="tab-pane" id="payment">
                        <h5 class="text-primary font-weight-bold border-bottom pb-2 mb-3"><i class="fab fa-cc-amazon-pay mr-1"></i> Razorpay Transaction Details</h5>
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Razorpay Order ID</span>
                                <code class="text-dark font-weight-600" style="font-size: 0.95rem;"><?= htmlspecialchars($d['razorpay_order_id'] ?: '—') ?></code>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Razorpay Payment ID</span>
                                <code class="text-dark font-weight-600" style="font-size: 0.95rem;"><?= htmlspecialchars($d['razorpay_payment_id'] ?: '—') ?></code>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <span class="text-muted d-block small">Razorpay Signature Verification Status</span>
                                <?php if ($d['razorpay_signature']): ?>
                                    <span class="badge badge-success py-2 px-3"><i class="fas fa-check-circle mr-1"></i> Securely Verified Signature</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary py-2 px-3"><i class="fas fa-info-circle mr-1"></i> Manual/Admin Recorded (Offline Payment)</span>
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
        title: 'Delete Donation Record?',
        text: "This action cannot be undone! The contribution details will be removed.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'donations.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include './footer.php'; ?>
