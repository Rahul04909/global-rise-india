<?php
/**
 * admin/plan-edit.php
 * ============================================================
 * Edit Support Plan Form
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/includes/auth-check.php'; // Starts session, checks auth, gets DB

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: plans.php');
    exit;
}

$pdo = getDB();
$error_msg = '';

// Fetch support plan
$stmt = $pdo->prepare("SELECT * FROM `volunteer_plans` WHERE `id` = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$plan = $stmt->fetch();

if (!$plan) {
    header('Location: plans.php');
    exit;
}

// Generate CSRF token if not set
if (empty($_SESSION['plan_edit_csrf_token'])) {
    $_SESSION['plan_edit_csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!hash_equals($_SESSION['plan_edit_csrf_token'], $csrf_token)) {
        $error_msg = 'Security token validation failed (CSRF mismatch).';
    } else {
        $title          = trim($_POST['title'] ?? '');
        $price          = (float)($_POST['price'] ?? 0.00);
        $duration_value = (int)($_POST['duration_value'] ?? 1);
        $duration_unit  = trim($_POST['duration_unit'] ?? 'month');
        $description    = trim($_POST['description'] ?? '');
        $status         = trim($_POST['status'] ?? 'active');

        if (empty($title) || $price < 0 || $duration_value <= 0) {
            $error_msg = 'Plan title, valid positive price, and valid positive duration are required.';
        } elseif (!in_array($duration_unit, ['month', 'year', 'lifetime', 'onetime'])) {
            $error_msg = 'Invalid duration unit selected.';
        } elseif (!in_array($status, ['active', 'inactive'])) {
            $error_msg = 'Invalid status selected.';
        } else {
            try {
                $update = $pdo->prepare("
                    UPDATE `volunteer_plans`
                    SET 
                        `title` = :title, `price` = :price, `duration_value` = :duration_value, 
                        `duration_unit` = :duration_unit, `description` = :description, `status` = :status
                    WHERE `id` = :id
                ");
                $update->execute([
                    ':title'          => $title,
                    ':price'          => $price,
                    ':duration_value' => $duration_value,
                    ':duration_unit'  => $duration_unit,
                    ':description'    => $description,
                    ':status'         => $status,
                    ':id'             => $id
                ]);

                header('Location: plans.php?updated=1');
                exit;

            } catch (PDOException $e) {
                error_log('[Plan Edit Error] ' . $e->getMessage());
                $error_msg = APP_DEBUG ? 'Database Error: ' . $e->getMessage() : 'An error occurred while saving support plan modifications.';
            }
        }
    }
}

include './header.php';
?>

<div class="row mb-3">
    <div class="col-12">
        <a href="plans.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Plans List</a>
    </div>
</div>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($error_msg) ?></div>
<?php endif; ?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title font-weight-bold" style="color: var(--sidebar-color);"><i class="fas fa-edit mr-1"></i> Edit Support Plan: <?= htmlspecialchars($plan['title']) ?></h3>
    </div>
    
    <form action="plan-edit.php?id=<?= $plan['id'] ?>" method="POST" class="form-horizontal">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['plan_edit_csrf_token']) ?>">
        
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Plan Title *</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($plan['title']) ?>" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Price (₹) *</label>
                <div class="col-sm-4">
                    <input type="number" step="0.01" class="form-control" name="price" value="<?= (float)$plan['price'] ?>" min="0" required>
                </div>
                <label class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-4">
                    <select class="form-control" name="status">
                        <option value="active" <?= $plan['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $plan['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Duration Value *</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" name="duration_value" value="<?= (int)$plan['duration_value'] ?>" min="1" <?= in_array($plan['duration_unit'], ['lifetime', 'onetime']) ? 'disabled' : '' ?> required>
                    <small class="form-text text-muted">Enter numeric duration (e.g. 1 for 1 Month, 3 for 3 Months).</small>
                </div>
                <label class="col-sm-2 col-form-label">Duration Unit *</label>
                <div class="col-sm-4">
                    <select class="form-control" name="duration_unit" id="duration_unit" onchange="toggleDurationDisplay()">
                        <option value="month" <?= $plan['duration_unit'] === 'month' ? 'selected' : '' ?>>Month(s)</option>
                        <option value="year" <?= $plan['duration_unit'] === 'year' ? 'selected' : '' ?>>Year(s)</option>
                        <option value="lifetime" <?= $plan['duration_unit'] === 'lifetime' ? 'selected' : '' ?>>Lifetime</option>
                        <option value="onetime" <?= $plan['duration_unit'] === 'onetime' ? 'selected' : '' ?>>One-Time</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($plan['description']) ?></textarea>
                </div>
            </div>
        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important; padding: 8px 30px;"><i class="fas fa-save mr-1"></i> Save Changes</button>
        </div>
    </form>
</div>

<script>
function toggleDurationDisplay() {
    var unit = document.getElementById('duration_unit').value;
    var valInput = document.querySelector('[name="duration_value"]');
    if (unit === 'lifetime' || unit === 'onetime') {
        valInput.value = 1;
        valInput.disabled = true;
    } else {
        valInput.disabled = false;
    }
}
</script>

<?php include './footer.php'; ?>
