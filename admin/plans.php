<?php
/**
 * admin/plans.php
 * ============================================================
 * Support Plans Management Dashboard
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/includes/auth-check.php'; // Starts session, checks auth, gets DB

$success_msg = '';
$error_msg = '';

$pdo = getDB();

// Handle deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $del = $pdo->prepare("DELETE FROM `volunteer_plans` WHERE `id` = :id");
            $del->execute([':id' => $id]);
            $success_msg = 'Support plan deleted successfully.';
        } catch (PDOException $e) {
            error_log('[Plan Delete Error] ' . $e->getMessage());
            $error_msg = 'Unable to delete plan. This plan might be linked to active volunteer records.';
        }
    }
}

// Handle status toggling
if (isset($_GET['action']) && $_GET['action'] === 'toggle_status') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT `status` FROM `volunteer_plans` WHERE `id` = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $current_status = $stmt->fetchColumn();
            
            $new_status = ($current_status === 'active') ? 'inactive' : 'active';
            
            $update = $pdo->prepare("UPDATE `volunteer_plans` SET `status` = :status WHERE `id` = :id");
            $update->execute([':status' => $new_status, ':id' => $id]);
            $success_msg = 'Plan status toggled successfully.';
        } catch (PDOException $e) {
            error_log('[Plan Toggle Status Error] ' . $e->getMessage());
            $error_msg = 'Database error: Unable to update status.';
        }
    }
}

if (isset($_GET['added']) && $_GET['added'] == '1') {
    $success_msg = 'New support plan created successfully.';
}
if (isset($_GET['updated']) && $_GET['updated'] == '1') {
    $success_msg = 'Support plan updated successfully.';
}

// Fetch all support plans
$plans = $pdo->query("SELECT * FROM `volunteer_plans` ORDER BY `price` ASC")->fetchAll();

include './header.php';
?>

<div class="row mb-3">
    <div class="col-12 text-right">
        <a href="plan-add.php" class="btn btn-success" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important;"><i class="fas fa-plus mr-1"></i> Add New Support Plan</a>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title font-weight-bold" style="color: var(--sidebar-color);"><i class="fas fa-gift mr-1"></i> Support Plans</h3>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="bg-light">
                        <th style="width: 80px;">ID</th>
                        <th>Plan Title</th>
                        <th>Price (₹)</th>
                        <th>Duration</th>
                        <th>Description</th>
                        <th style="width: 120px;">Status</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($plans)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No support plans configured. Click "Add New Support Plan" above to create one.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($plans as $p): 
                            $status_btn = ($p['status'] === 'active') ? 'success' : 'secondary';
                            $status_icon = ($p['status'] === 'active') ? 'fa-toggle-on' : 'fa-toggle-off';
                            
                            $duration_lbl = $p['duration_value'] . ' ' . ucfirst($p['duration_unit']) . ($p['duration_value'] > 1 ? 's' : '');
                            if ($p['duration_unit'] === 'lifetime') $duration_lbl = 'Lifetime';
                            if ($p['duration_unit'] === 'onetime')  $duration_lbl = 'One-Time';
                        ?>
                            <tr>
                                <td>#PLN-<?= $p['id'] ?></td>
                                <td class="font-weight-600"><?= htmlspecialchars($p['title']) ?></td>
                                <td class="text-primary font-weight-bold">₹<?= number_format($p['price'], 2) ?></td>
                                <td><?= $duration_lbl ?></td>
                                <td><span class="small text-muted"><?= htmlspecialchars($p['description']) ?: '—' ?></span></td>
                                <td>
                                    <a href="plans.php?action=toggle_status&id=<?= $p['id'] ?>" class="btn btn-sm btn-<?= $status_btn ?> btn-block">
                                        <i class="fas <?= $status_icon ?> mr-1"></i> <?= ucfirst($p['status']) ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="plan-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-info" title="Edit plan details">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $p['id'] ?>)" title="Delete support plan">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Delete Support Plan?',
        text: "This action cannot be undone! Make sure no active volunteers are currently linked to this plan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'plans.php?action=delete&id=' + id;
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
