<?php
/**
 * admin/volunteers.php
 * ============================================================
 * Volunteer Management Panel
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
            $del = $pdo->prepare("DELETE FROM `volunteers` WHERE `id` = :id");
            $del->execute([':id' => $id]);
            $success_msg = 'Volunteer record deleted successfully.';
        } catch (PDOException $e) {
            error_log('[Volunteer Delete Error] ' . $e->getMessage());
            $error_msg = 'Database error: Unable to delete volunteer.';
        }
    }
}

if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $success_msg = 'Volunteer record deleted successfully.';
}
if (isset($_GET['updated']) && $_GET['updated'] == '1') {
    $success_msg = 'Volunteer details updated successfully.';
}
if (isset($_GET['added']) && $_GET['added'] == '1') {
    $success_msg = 'Volunteer registered successfully.';
}

$pdo = getDB();

// ── Search & Filter Logic ────────────────────────────────────
$search = trim($_GET['search'] ?? '');
$filter_area = trim($_GET['area'] ?? '');
$filter_status = trim($_GET['status'] ?? '');
$filter_payment = trim($_GET['payment_status'] ?? '');

$where_clauses = [];
$params = [];

if (!empty($search)) {
    $where_clauses[] = "(v.full_name LIKE :search OR v.email LIKE :search OR v.phone LIKE :search OR v.city LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}
if (!empty($filter_area)) {
    $where_clauses[] = "v.area_of_interest = :area";
    $params[':area'] = $filter_area;
}
if (!empty($filter_status)) {
    $where_clauses[] = "v.status = :status";
    $params[':status'] = $filter_status;
}
if (!empty($filter_payment)) {
    $where_clauses[] = "v.payment_status = :payment_status";
    $params[':payment_status'] = $filter_payment;
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
$count_query = "SELECT COUNT(*) FROM `volunteers` v $where_sql";
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_rows = (int)$count_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Fetch volunteers with plan titles
$query = "
    SELECT v.*, p.title AS plan_title, p.price AS plan_price 
    FROM `volunteers` v 
    LEFT JOIN `volunteer_plans` p ON v.plan_id = p.id
    $where_sql
    ORDER BY v.created_at DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$volunteers = $stmt->fetchAll();

// Unique values for filters
$areas = $pdo->query("SELECT DISTINCT `area_of_interest` FROM `volunteers` ORDER BY `area_of_interest` ASC")->fetchAll(PDO::FETCH_COLUMN);

include './header.php';
?>

<div class="card card-primary card-outline">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold mb-0" style="color: var(--sidebar-color);"><i class="fas fa-users mr-1"></i> Volunteers List</h3>
        <a href="volunteer-add.php" class="btn btn-success btn-sm ml-auto"><i class="fas fa-user-plus mr-1"></i> Add Volunteer</a>
    </div>
    
    <div class="card-body">
        <!-- Search and Filters Form -->
        <form action="volunteers.php" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search name, email, phone..." value="<?= htmlspecialchars($search) ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2 mb-2">
                    <select name="area" class="form-control" onchange="this.form.submit()">
                        <option value="">— Interest Area —</option>
                        <?php foreach ($areas as $area): ?>
                            <option value="<?= htmlspecialchars($area) ?>" <?= $filter_area === $area ? 'selected' : '' ?>><?= htmlspecialchars($area) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 mb-2">
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">— Status —</option>
                        <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $filter_status === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $filter_status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-2">
                    <select name="payment_status" class="form-control" onchange="this.form.submit()">
                        <option value="">— Payment —</option>
                        <option value="pending" <?= $filter_payment === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= $filter_payment === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="failed" <?= $filter_payment === 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-2">
                    <a href="volunteers.php" class="btn btn-secondary btn-block"><i class="fas fa-undo mr-1"></i> Reset Filters</a>
                </div>
            </div>
        </form>

        <!-- Volunteers Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="bg-light">
                        <th>Ref ID</th>
                        <th>Name</th>
                        <th>Contact Details</th>
                        <th>Location</th>
                        <th>Interest Area</th>
                        <th>Support Plan</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($volunteers)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No volunteers found matching the filters.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($volunteers as $v): 
                            $status_badge = 'secondary';
                            if ($v['status'] === 'approved') $status_badge = 'success';
                            if ($v['status'] === 'rejected') $status_badge = 'danger';

                            $pay_badge = 'secondary';
                            if ($v['payment_status'] === 'paid') $pay_badge = 'success';
                            if ($v['payment_status'] === 'failed') $pay_badge = 'danger';
                        ?>
                            <tr>
                                <td class="font-weight-bold">#VOL-<?= str_pad($v['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($v['full_name']) ?></td>
                                <td>
                                    <div class="small"><i class="fas fa-envelope mr-1 text-muted"></i><?= htmlspecialchars($v['email']) ?></div>
                                    <div class="small"><i class="fas fa-phone mr-1 text-muted"></i><?= htmlspecialchars($v['phone']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($v['city']) ?>, <?= htmlspecialchars($v['state']) ?></td>
                                <td>
                                    <span class="badge badge-info"><?= htmlspecialchars($v['area_of_interest']) ?></span>
                                </td>
                                <td>
                                    <?php if ($v['plan_id']): ?>
                                        <div class="font-weight-600 small"><?= htmlspecialchars($v['plan_title']) ?></div>
                                        <div class="text-muted small">₹<?= number_format($v['amount_paid'], 2) ?></div>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $status_badge ?> text-capitalize"><?= $v['status'] ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $pay_badge ?> text-capitalize"><?= $v['payment_status'] ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="volunteer-view.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-primary" title="View volunteer details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="volunteer-edit.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-info" title="Edit volunteer details">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $v['id'] ?>)" title="Delete volunteer record">
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
                        <a class="page-link" href="volunteers.php?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&area=<?= urlencode($filter_area) ?>&status=<?= urlencode($filter_status) ?>&payment_status=<?= urlencode($filter_payment) ?>">«</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="volunteers.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&area=<?= urlencode($filter_area) ?>&status=<?= urlencode($filter_status) ?>&payment_status=<?= urlencode($filter_payment) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="volunteers.php?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&area=<?= urlencode($filter_area) ?>&status=<?= urlencode($filter_status) ?>&payment_status=<?= urlencode($filter_payment) ?>">»</a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>

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

<!-- Dynamic Alert Handlers -->
<?php if (!empty($success_msg)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Success!',
            text: '<?= htmlspecialchars($success_msg) ?>',
            icon: 'success',
            confirmButtonColor: '#13a34a'
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
