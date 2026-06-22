<?php
/**
 * admin/donations.php
 * ============================================================
 * Donations Management Panel
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/includes/auth-check.php'; // Starts session, checks auth, gets DB

$success_msg = '';
$error_msg = '';

// Handle deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $pdo = getDB();
            $del = $pdo->prepare("DELETE FROM `donations` WHERE `id` = :id");
            $del->execute([':id' => $id]);
            $success_msg = 'Donation record deleted successfully.';
        } catch (PDOException $e) {
            error_log('[Donation Delete Error] ' . $e->getMessage());
            $error_msg = 'Database error: Unable to delete donation.';
        }
    }
}

if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $success_msg = 'Donation record deleted successfully.';
}
if (isset($_GET['updated']) && $_GET['updated'] == '1') {
    $success_msg = 'Donation details updated successfully.';
}
if (isset($_GET['added']) && $_GET['added'] == '1') {
    $success_msg = 'Donation recorded successfully.';
}

$pdo = getDB();

// ── Search & Filter Logic ────────────────────────────────────
$search = trim($_GET['search'] ?? '');
$filter_cause = trim($_GET['cause'] ?? '');
$filter_status = trim($_GET['status'] ?? '');
$filter_citizenship = trim($_GET['citizenship'] ?? '');
$filter_type = trim($_GET['type'] ?? '');

$where_clauses = [];
$params = [];

if (!empty($search)) {
    $where_clauses[] = "(donor_name LIKE :search OR donor_email LIKE :search OR donor_mobile LIKE :search OR donor_pan LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}
if (!empty($filter_cause)) {
    $where_clauses[] = "selected_cause = :cause";
    $params[':cause'] = $filter_cause;
}
if (!empty($filter_status)) {
    $where_clauses[] = "payment_status = :status";
    $params[':status'] = $filter_status;
}
if (!empty($filter_citizenship)) {
    $where_clauses[] = "citizenship = :citizenship";
    $params[':citizenship'] = $filter_citizenship;
}
if (!empty($filter_type)) {
    $where_clauses[] = "donation_type = :type";
    $params[':type'] = $filter_type;
}

$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// ── Pagination Logic ──────────────────────────────────────────
$limit = 10;
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total
$count_query = "SELECT COUNT(*) FROM `donations` $where_sql";
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_rows = (int)$count_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Fetch donations
$query = "
    SELECT * FROM `donations` 
    $where_sql
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$donations = $stmt->fetchAll();

// ── Calculate Stats for Cards ─────────────────────────────────
try {
    // 1. Total Paid Donations
    $stat_total_paid = (float)$pdo->query("SELECT SUM(amount) FROM `donations` WHERE payment_status = 'paid'")->fetchColumn();
    // 2. Count Active Monthly Subscriptions
    $stat_monthly_count = (int)$pdo->query("SELECT COUNT(*) FROM `donations` WHERE donation_type = 'monthly' AND payment_status = 'paid'")->fetchColumn();
    // 3. Count 80G requested
    $stat_80g_count = (int)$pdo->query("SELECT COUNT(*) FROM `donations` WHERE request_80g = 1 AND payment_status = 'paid'")->fetchColumn();
    // 4. Count Foreign/NRI contributors
    $stat_foreign_count = (int)$pdo->query("SELECT COUNT(*) FROM `donations` WHERE citizenship IN ('nri', 'foreign') AND payment_status = 'paid'")->fetchColumn();
} catch (PDOException $e) {
    $stat_total_paid = 0.00;
    $stat_monthly_count = 0;
    $stat_80g_count = 0;
    $stat_foreign_count = 0;
}

// Map causes codes to friendly names
$causeMap = [
    'feed'     => 'Feed a Child',
    'slum'     => 'Educate Slum Children',
    'disaster' => 'Disaster Relief',
    'women'    => 'Women Empowerment',
    'disabled' => 'Support Disabilities',
    'senior'   => 'Senior Citizen Care'
];

include './header.php';
?>

<!-- Statistics Summary Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success shadow-sm">
            <div class="inner">
                <h3>₹<?= number_format($stat_total_paid, 2) ?></h3>
                <p class="font-weight-bold">Total Donations (Paid)</p>
            </div>
            <div class="icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info shadow-sm">
            <div class="inner">
                <h3><?= $stat_monthly_count ?></h3>
                <p class="font-weight-bold">Monthly Donors</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning shadow-sm text-dark">
            <div class="inner">
                <h3><?= $stat_80g_count ?></h3>
                <p class="font-weight-bold">80G Claims</p>
            </div>
            <div class="icon">
                <i class="fas fa-percent"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger shadow-sm">
            <div class="inner">
                <h3><?= $stat_foreign_count ?></h3>
                <p class="font-weight-bold">Foreign/NRI Donors</p>
            </div>
            <div class="icon">
                <i class="fas fa-globe-americas"></i>
            </div>
        </div>
    </div>
</div>

<div class="card card-primary card-outline shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold mb-0" style="color: var(--sidebar-color);"><i class="fas fa-heart mr-1"></i> Donations Management</h3>
        <a href="donation-add.php" class="btn btn-success btn-sm ml-auto"><i class="fas fa-plus-circle mr-1"></i> Add Donation</a>
    </div>
    
    <div class="card-body">
        <!-- Search and Filters Form -->
        <form action="donations.php" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Name, Email, PAN..." value="<?= htmlspecialchars($search) ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 mb-2">
                    <select name="cause" class="form-control" onchange="this.form.submit()">
                        <option value="">— Choose Cause —</option>
                        <?php foreach ($causeMap as $key => $val): ?>
                            <option value="<?= $key ?>" <?= $filter_cause === $key ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <select name="type" class="form-control" onchange="this.form.submit()">
                        <option value="">— Donation Type —</option>
                        <option value="once" <?= $filter_type === 'once' ? 'selected' : '' ?>>One-Time</option>
                        <option value="monthly" <?= $filter_type === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-2">
                    <select name="citizenship" class="form-control" onchange="this.form.submit()">
                        <option value="">— Citizenship —</option>
                        <option value="indian" <?= $filter_citizenship === 'indian' ? 'selected' : '' ?>>Indian</option>
                        <option value="nri" <?= $filter_citizenship === 'nri' ? 'selected' : '' ?>>NRI</option>
                        <option value="foreign" <?= $filter_citizenship === 'foreign' ? 'selected' : '' ?>>Foreign</option>
                    </select>
                </div>

                <div class="col-md-1 mb-2">
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">— Status —</option>
                        <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= $filter_status === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="failed" <?= $filter_status === 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-2">
                    <a href="donations.php" class="btn btn-secondary btn-block"><i class="fas fa-undo mr-1"></i> Reset Filters</a>
                </div>
            </div>
        </form>

        <!-- Donations Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="bg-light">
                        <th>Receipt ID</th>
                        <th>Donor Details</th>
                        <th>Citizenship / Type</th>
                        <th>Supported Cause</th>
                        <th>Amount</th>
                        <th class="text-center">80G</th>
                        <th class="text-center">Status</th>
                        <th>Date</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($donations)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No donation records found matching the filters.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($donations as $d): 
                            $status_badge = 'secondary';
                            if ($d['payment_status'] === 'paid') $status_badge = 'success';
                            if ($d['payment_status'] === 'failed') $status_badge = 'danger';

                            $type_badge = 'info';
                            if ($d['donation_type'] === 'monthly') $type_badge = 'warning';
                        ?>
                            <tr>
                                <td class="font-weight-bold">#DON-<?= str_pad($d['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td>
                                    <div class="font-weight-bold"><?= htmlspecialchars($d['donor_title']) ?> <?= htmlspecialchars($d['donor_name']) ?></div>
                                    <div class="small text-muted"><i class="fas fa-envelope mr-1"></i><?= htmlspecialchars($d['donor_email']) ?></div>
                                    <div class="small text-muted"><i class="fas fa-phone mr-1"></i><?= htmlspecialchars($d['donor_mobile']) ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-light text-capitalize"><?= htmlspecialchars($d['citizenship']) ?></span>
                                    <span class="badge badge-<?= $type_badge ?> text-capitalize d-block mt-1"><?= htmlspecialchars($d['donation_type']) ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-info text-wrap" style="max-width: 150px;"><?= htmlspecialchars($causeMap[$d['selected_cause']] ?? $d['selected_cause']) ?></span>
                                </td>
                                <td class="font-weight-bold text-success">
                                    ₹<?= number_format($d['amount'], 2) ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($d['request_80g']): ?>
                                        <span class="badge badge-warning" title="PAN: <?= htmlspecialchars($d['donor_pan']) ?>"><i class="fas fa-percent"></i> Yes</span>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $status_badge ?> text-capitalize"><?= htmlspecialchars($d['payment_status']) ?></span>
                                </td>
                                <td class="small">
                                    <?= date('d M, Y', strtotime($d['created_at'])) ?>
                                </td>
                                <td class="text-center">
                                    <a href="donation-view.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-primary" title="View donation details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="donation-edit.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-info" title="Edit donation details">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $d['id'] ?>)" title="Delete donation record">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <?php if ($total_pages > 1): ?>
            <div class="card-footer bg-white clearfix px-0">
                <ul class="pagination pagination-sm m-0 float-right">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="donations.php?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&cause=<?= urlencode($filter_cause) ?>&status=<?= urlencode($filter_status) ?>&citizenship=<?= urlencode($filter_citizenship) ?>&type=<?= urlencode($filter_type) ?>">«</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="donations.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&cause=<?= urlencode($filter_cause) ?>&status=<?= urlencode($filter_status) ?>&citizenship=<?= urlencode($filter_citizenship) ?>&type=<?= urlencode($filter_type) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="donations.php?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&cause=<?= urlencode($filter_cause) ?>&status=<?= urlencode($filter_status) ?>&citizenship=<?= urlencode($filter_citizenship) ?>&type=<?= urlencode($filter_type) ?>">»</a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Delete Donation Record?',
        text: "This action cannot be undone! The donation history will be removed.",
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

<!-- Dynamic Alert Handlers -->
<?php if (!empty($success_msg)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Success!',
            text: '<?= htmlspecialchars($success_msg) ?>',
            icon: 'success',
            confirmButtonColor: '#28a745'
        });
    });
</script>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Error!',
            text: '<?= htmlspecialchars($error_msg) ?>',
            icon: 'error',
            confirmButtonColor: '#1b5182'
        });
    });
</script>
<?php endif; ?>
