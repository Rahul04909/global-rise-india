<?php
/**
 * admin/articles.php
 * ============================================================
 * Articles (Blogs & News) Management Dashboard
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/includes/auth-check.php';

$success_msg = '';
$error_msg = '';

$pdo = getDB();

// Handle deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            // First fetch the image path to delete the file if it's uploaded under uploads/
            $imgStmt = $pdo->prepare("SELECT `image` FROM `articles` WHERE `id` = :id LIMIT 1");
            $imgStmt->execute([':id' => $id]);
            $image = $imgStmt->fetchColumn();
            
            $del = $pdo->prepare("DELETE FROM `articles` WHERE `id` = :id");
            $del->execute([':id' => $id]);
            
            // Delete the image file if it exists in uploads/ and is not a default asset
            if ($image && strpos($image, 'uploads/') !== false) {
                $filePath = __DIR__ . '/../' . $image;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            
            $success_msg = 'Article deleted successfully.';
        } catch (PDOException $e) {
            error_log('[Article Delete Error] ' . $e->getMessage());
            $error_msg = 'Unable to delete article due to database error.';
        }
    }
}

// Handle status toggling
if (isset($_GET['action']) && $_GET['action'] === 'toggle_status') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT `status` FROM `articles` WHERE `id` = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $current_status = $stmt->fetchColumn();
            
            $new_status = ($current_status === 'published') ? 'draft' : 'published';
            
            $update = $pdo->prepare("UPDATE `articles` SET `status` = :status WHERE `id` = :id");
            $update->execute([':status' => $new_status, ':id' => $id]);
            $success_msg = 'Article status updated successfully.';
        } catch (PDOException $e) {
            error_log('[Article Toggle Status Error] ' . $e->getMessage());
            $error_msg = 'Database error: Unable to update status.';
        }
    }
}

if (isset($_GET['added']) && $_GET['added'] == '1') {
    $success_msg = 'New article created successfully.';
}
if (isset($_GET['updated']) && $_GET['updated'] == '1') {
    $success_msg = 'Article updated successfully.';
}

// Search and filtering inputs
$search = trim($_GET['search'] ?? '');
$type_filter = trim($_GET['type'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

// Build dynamic query
$query = "SELECT * FROM `articles` WHERE 1=1";
$params = [];

if ($search !== '') {
    $query .= " AND (`title` LIKE :search OR `description` LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}
if ($type_filter !== '') {
    $query .= " AND `type` = :type";
    $params[':type'] = $type_filter;
}
if ($status_filter !== '') {
    $query .= " AND `status` = :status";
    $params[':status'] = $status_filter;
}

$query .= " ORDER BY `created_at` DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$articles = $stmt->fetchAll();

include './header.php';
?>

<!-- Filter Options -->
<div class="card card-outline card-secondary mb-3">
    <div class="card-body">
        <form method="GET" action="articles.php" class="row align-items-end">
            <div class="col-md-4 mb-2 mb-md-0">
                <label for="search" class="font-weight-600 small">Search Keywords</label>
                <div class="input-group">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search title, details..." value="<?= htmlspecialchars($search) ?>">
                    <?php if ($search !== ''): ?>
                        <div class="input-group-append">
                            <a href="articles.php?type=<?= urlencode($type_filter) ?>&status=<?= urlencode($status_filter) ?>" class="btn btn-outline-secondary" title="Clear search"><i class="fas fa-times"></i></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="type" class="font-weight-600 small">Article Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="news" <?= $type_filter === 'news' ? 'selected' : '' ?>>News & Stories</option>
                    <option value="blog" <?= $type_filter === 'blog' ? 'selected' : '' ?>>Blog Post</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-2 mb-md-0">
                <label for="status" class="font-weight-600 small">Publish Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="published" <?= $status_filter === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= $status_filter === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12 text-right">
        <a href="article-add.php" class="btn btn-success" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important;"><i class="fas fa-plus mr-1"></i> Add New Article</a>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title font-weight-bold" style="color: var(--sidebar-color);"><i class="fas fa-newspaper mr-1"></i> Articles Management</h3>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="bg-light">
                        <th style="width: 80px;">ID</th>
                        <th style="width: 80px;" class="text-center">Image</th>
                        <th>Title</th>
                        <th style="width: 140px;">Type</th>
                        <th>Slug</th>
                        <th style="width: 120px;" class="text-center">Status</th>
                        <th style="width: 130px;" class="text-center">Created At</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No articles found. Click "Add New Article" above to create one.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($articles as $art): 
                            $status_btn = ($art['status'] === 'published') ? 'success' : 'secondary';
                            $status_icon = ($art['status'] === 'published') ? 'fa-eye' : 'fa-eye-slash';
                            $type_badge = ($art['type'] === 'blog') ? 'success' : 'info';
                            $type_label = ($art['type'] === 'blog') ? 'Blog Post' : 'News & Stories';
                            
                            $image_src = $art['image'];
                            if (empty($image_src)) {
                                $image_src = '../assets/logo.png';
                            } else {
                                $image_src = '../' . $image_src;
                            }
                        ?>
                            <tr>
                                <td>#ART-<?= $art['id'] ?></td>
                                <td class="text-center">
                                    <img src="<?= htmlspecialchars($image_src) ?>" alt="article thumb" style="width: 50px; height: 35px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                </td>
                                <td class="font-weight-600"><?= htmlspecialchars($art['title']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $type_badge ?> py-1 px-2 d-inline-block" style="font-size: 11px;"><?= $type_label ?></span>
                                </td>
                                <td><code class="small text-muted"><?= htmlspecialchars($art['slug']) ?></code></td>
                                <td class="text-center">
                                    <a href="articles.php?action=toggle_status&id=<?= $art['id'] ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type_filter) ?>&status=<?= urlencode($status_filter) ?>" class="btn btn-xs btn-<?= $status_btn ?> btn-block py-1" title="Click to toggle status">
                                        <i class="fas <?= $status_icon ?> mr-1"></i> <?= ucfirst($art['status']) ?>
                                    </a>
                                </td>
                                <td class="small text-muted"><?= date('d-M-Y', strtotime($art['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="../pages/article-details.php?slug=<?= $art['slug'] ?>" target="_blank" class="btn btn-sm btn-light" title="View live article">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a href="article-edit.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-info" title="Edit article details">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $art['id'] ?>)" title="Delete article">
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
        title: 'Delete Article?',
        text: "This action cannot be undone! The article page and uploaded file will be permanently removed.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'articles.php?action=delete&id=' + id;
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
