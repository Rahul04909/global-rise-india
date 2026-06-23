<?php
/**
 * admin/index.php
 * ============================================================
 * Administrative Portal Main Dashboard Overview
 * The Global Rise Foundation
 * ============================================================
 */

include './header.php'; // Starts session, checks auth, imports database helper

$pdo = getDB();

// ── 1. Fetch Real-time Summary Statistics ─────────────────────
try {
    // Total Volunteers registered
    $totalVolunteers = (int)$pdo->query("SELECT COUNT(*) FROM `volunteers`")->fetchColumn();

    // Total Donations collected (Paid status)
    $totalDonations = (float)$pdo->query("SELECT SUM(amount) FROM `donations` WHERE payment_status = 'paid'")->fetchColumn();

    // Active Support plans configured
    $activePlansCount = (int)$pdo->query("SELECT COUNT(*) FROM `volunteer_plans` WHERE status = 'active'")->fetchColumn();

    // Tax Exemption (80G) Claims verified
    $taxExemptionClaims = (int)$pdo->query("SELECT COUNT(*) FROM `donations` WHERE request_80g = 1 AND payment_status = 'paid'")->fetchColumn();
} catch (PDOException $e) {
    error_log('[Dashboard Stats Error] ' . $e->getMessage());
    $totalVolunteers = 0;
    $totalDonations = 0.00;
    $activePlansCount = 0;
    $taxExemptionClaims = 0;
}

// ── 2. Fetch Recent Activities ─────────────────────────────────
try {
    // Recent Donors (Latest 5 donations)
    $recentDonations = $pdo->query("
        SELECT * FROM `donations` 
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll();

    // Recent Volunteers (Latest 5 volunteers)
    $recentVolunteers = $pdo->query("
        SELECT v.*, p.title AS plan_title 
        FROM `volunteers` v
        LEFT JOIN `volunteer_plans` p ON v.plan_id = p.id
        ORDER BY v.created_at DESC 
        LIMIT 5
    ")->fetchAll();
} catch (PDOException $e) {
    error_log('[Dashboard Logs Error] ' . $e->getMessage());
    $recentDonations = [];
    $recentVolunteers = [];
}

// Cause code to friendly description mapper
$causeMap = [
    'feed'     => 'Feed a Child',
    'slum'     => 'Educate Slum Children',
    'disaster' => 'Disaster Relief',
    'women'    => 'Women Empowerment',
    'disabled' => 'Support Disabilities',
    'senior'   => 'Senior Citizen Care'
];
?>

<!-- Statistics Blocks Row -->
<div class="row">
    <!-- Card 1: Volunteers -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info shadow-sm">
            <div class="inner">
                <h3><?= number_format($totalVolunteers) ?></h3>
                <p class="font-weight-600">Total Volunteers</p>
            </div>
            <div class="icon">
                <i class="fas fa-hands-helping"></i>
            </div>
            <a href="volunteers.php" class="small-box-footer">Manage Volunteers <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Card 2: Donations -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success shadow-sm">
            <div class="inner">
                <h3>₹<?= number_format($totalDonations, 2) ?></h3>
                <p class="font-weight-600">Total Donations (Paid)</p>
            </div>
            <div class="icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <a href="donations.php" class="small-box-footer">Manage Donations <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Card 3: Active Support Plans -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning shadow-sm text-dark">
            <div class="inner">
                <h3><?= number_format($activePlansCount) ?></h3>
                <p class="font-weight-600">Active Support Plans</p>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <a href="plans.php" class="small-box-footer">Manage Support Plans <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Card 4: 80G Exemption Claims -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger shadow-sm">
            <div class="inner">
                <h3><?= number_format($taxExemptionClaims) ?></h3>
                <p class="font-weight-600">80G Exemption Claims</p>
            </div>
            <div class="icon">
                <i class="fas fa-percent"></i>
            </div>
            <a href="donations.php?citizenship=&cause=&type=&status=paid" class="small-box-footer">View Claims List <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- Recent Submissions Row -->
<div class="row mt-4">
    <!-- Left Column: Recent Donations -->
    <div class="col-md-6">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header border-0 d-flex align-items-center">
                <h3 class="card-title font-weight-bold mb-0" style="color: var(--sidebar-color);"><i class="fas fa-hand-holding-usd mr-1 text-success"></i> Recent Donations</h3>
                <a href="donations.php" class="btn btn-outline-primary btn-xs ml-auto font-weight-bold" style="padding: 2px 8px; font-size: 0.75rem;"><i class="fas fa-list mr-1"></i> View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-valign-middle mb-0">
                        <thead>
                            <tr>
                                <th>Receipt</th>
                                <th>Donor</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentDonations)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No donation records found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentDonations as $don): 
                                    $status_badge = 'secondary';
                                    if ($don['payment_status'] === 'paid') $status_badge = 'success';
                                    if ($don['payment_status'] === 'failed') $status_badge = 'danger';
                                ?>
                                    <tr>
                                        <td>
                                            <a href="donation-view.php?id=<?= $don['id'] ?>" class="font-weight-bold text-primary">
                                                #DON-<?= str_pad($don['id'], 5, '0', STR_PAD_LEFT) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="font-weight-600 text-dark"><?= htmlspecialchars($don['donor_name']) ?></div>
                                            <small class="text-muted text-capitalize"><?= htmlspecialchars($don['citizenship']) ?> &bull; <?= htmlspecialchars($don['donation_type']) ?></small>
                                        </td>
                                        <td class="font-weight-bold text-success">
                                            ₹<?= number_format($don['amount'], 2) ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $status_badge ?> text-capitalize" style="font-size: 0.75rem;"><?= htmlspecialchars($don['payment_status']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Volunteers -->
    <div class="col-md-6">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header border-0 d-flex align-items-center">
                <h3 class="card-title font-weight-bold mb-0" style="color: var(--sidebar-color);"><i class="fas fa-users mr-1 text-primary"></i> Recent Volunteers</h3>
                <a href="volunteers.php" class="btn btn-outline-primary btn-xs ml-auto font-weight-bold" style="padding: 2px 8px; font-size: 0.75rem;"><i class="fas fa-list mr-1"></i> View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-valign-middle mb-0">
                        <thead>
                            <tr>
                                <th>Ref ID</th>
                                <th>Volunteer</th>
                                <th>Interest Area</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentVolunteers)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No volunteer records found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentVolunteers as $vol): 
                                    $status_badge = 'secondary';
                                    if ($vol['status'] === 'approved') $status_badge = 'success';
                                    if ($vol['status'] === 'rejected') $status_badge = 'danger';
                                ?>
                                    <tr>
                                        <td>
                                            <a href="volunteer-view.php?id=<?= $vol['id'] ?>" class="font-weight-bold text-primary">
                                                #VOL-<?= str_pad($vol['id'], 5, '0', STR_PAD_LEFT) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="font-weight-600 text-dark"><?= htmlspecialchars($vol['full_name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($vol['city']) ?>, <?= htmlspecialchars($vol['state']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info text-wrap" style="max-width: 150px; font-size: 0.75rem;"><?= htmlspecialchars($causeMap[$vol['area_of_interest']] ?? $vol['area_of_interest']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $status_badge ?> text-capitalize" style="font-size: 0.75rem;"><?= htmlspecialchars($vol['status']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>