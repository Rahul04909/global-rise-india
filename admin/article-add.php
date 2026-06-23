<?php
/**
 * admin/article-add.php
 * ============================================================
 * Add New Article Form
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/includes/auth-check.php';

$error_msg = '';

// Generate CSRF token if not set
if (empty($_SESSION['article_add_csrf_token'])) {
    $_SESSION['article_add_csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!hash_equals($_SESSION['article_add_csrf_token'], $csrf_token)) {
        $error_msg = 'Security token validation failed (CSRF mismatch).';
    } else {
        $type             = trim($_POST['type'] ?? 'news');
        $title            = trim($_POST['title'] ?? '');
        $slug             = trim($_POST['slug'] ?? '');
        $description      = trim($_POST['description'] ?? '');
        $status           = trim($_POST['status'] ?? 'draft');
        $meta_title       = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $meta_keywords    = trim($_POST['meta_keywords'] ?? '');
        $schema_json      = trim($_POST['schema_json'] ?? '');

        // Generate dynamic slug if empty
        if (empty($slug) && !empty($title)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        }

        if (empty($title) || empty($description)) {
            $error_msg = 'Article title and description content are required.';
        } elseif (!in_array($type, ['news', 'blog'])) {
            $error_msg = 'Invalid article type selected.';
        } elseif (!in_array($status, ['draft', 'published'])) {
            $error_msg = 'Invalid status selected.';
        } else {
            // Check for slug uniqueness
            $pdo = getDB();
            $stmtSlug = $pdo->prepare("SELECT COUNT(*) FROM `articles` WHERE `slug` = :slug");
            $stmtSlug->execute([':slug' => $slug]);
            $slugCount = (int)$stmtSlug->fetchColumn();

            if ($slugCount > 0) {
                // Make slug unique by appending timestamp
                $slug = $slug . '-' . time();
            }

            // Handle Image Upload
            $imagePath = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../assets/uploads/articles/';
                    
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    
                    $dest_path = $uploadFileDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $imagePath = 'assets/uploads/articles/' . $newFileName;
                    } else {
                        $error_msg = 'There was an error moving the uploaded image file.';
                    }
                } else {
                    $error_msg = 'Upload failed. Allowed image formats: JPG, JPEG, PNG, GIF, WEBP.';
                }
            }

            // Save to database if no error
            if (empty($error_msg)) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO `articles` (type, title, slug, image, description, meta_title, meta_description, meta_keywords, schema_json, status)
                        VALUES (:type, :title, :slug, :image, :description, :meta_title, :meta_description, :meta_keywords, :schema_json, :status)
                    ");
                    $stmt->execute([
                        ':type'             => $type,
                        ':title'            => $title,
                        ':slug'             => $slug,
                        ':image'            => $imagePath,
                        ':description'      => $description,
                        ':meta_title'       => $meta_title ?: null,
                        ':meta_description' => $meta_description ?: null,
                        ':meta_keywords'    => $meta_keywords ?: null,
                        ':schema_json'      => $schema_json ?: null,
                        ':status'           => $status
                    ]);

                    header('Location: articles.php?added=1');
                    exit;

                } catch (PDOException $e) {
                    error_log('[Article Add Error] ' . $e->getMessage());
                    $error_msg = APP_DEBUG ? 'Database Error: ' . $e->getMessage() : 'An error occurred while saving the new article.';
                }
            }
        }
    }
}

include './header.php';
?>

<!-- Import Trumbowyg CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css">
<style>
    .trumbowyg-box, .trumbowyg-editor {
        border-color: #ced4da !important;
        background-color: #fff !important;
    }
    .img-preview-container {
        margin-top: 10px;
        display: none;
        border: 1px dashed #ced4da;
        border-radius: 6px;
        padding: 8px;
        max-width: 320px;
        text-align: center;
        background: #fdfdfd;
    }
    .img-preview {
        max-width: 100%;
        max-height: 180px;
        object-fit: contain;
        border-radius: 4px;
    }
    .seo-section {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 20px;
        margin-top: 20px;
    }
</style>

<div class="row mb-3">
    <div class="col-12">
        <a href="articles.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Articles List</a>
    </div>
</div>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($error_msg) ?></div>
<?php endif; ?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title font-weight-bold" style="color: var(--sidebar-color);"><i class="fas fa-plus mr-1"></i> Add New Article</h3>
    </div>
    
    <form action="article-add.php" method="POST" enctype="multipart/form-data" class="form-horizontal">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['article_add_csrf_token']) ?>">
        
        <div class="card-body">
            <!-- 1. Basic Fields -->
            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label font-weight-bold">Article Type *</label>
                <div class="col-sm-4">
                    <select class="form-control" name="type" id="type" onchange="updateSchema()">
                        <option value="news">News & Stories</option>
                        <option value="blog">Blog Post</option>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label font-weight-bold">Status *</label>
                <div class="col-sm-4">
                    <select class="form-control" name="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label font-weight-bold">Article Title *</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="title" id="title" placeholder="e.g. Bangalore Kitchen Launch Event" required>
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label font-weight-bold">Slug URL *</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="slug" id="slug" placeholder="e.g. bangalore-kitchen-launch-event" required>
                    <small class="form-text text-muted">A clean, unique string for the URL. Will be auto-generated from title, but is fully editable.</small>
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label font-weight-bold">Header Image</label>
                <div class="col-sm-10">
                    <input type="file" class="form-control-file" name="image" id="image" accept="image/*" onchange="previewImage(this)">
                    <small class="form-text text-muted">Upload article banner image (Formats: JPEG, PNG, WEBP. Max 2MB).</small>
                    <div class="img-preview-container" id="previewContainer">
                        <img src="" alt="preview" class="img-preview" id="imagePreview">
                        <div class="small text-muted mt-1">Image Preview</div>
                    </div>
                </div>
            </div>

            <!-- 2. Rich Text Editor -->
            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label font-weight-bold">Description *</label>
                <div class="col-sm-10">
                    <textarea name="description" id="description" rows="10" placeholder="Write your article content here..." required></textarea>
                </div>
            </div>

            <!-- 3. SEO Optimization Panel -->
            <div class="seo-section">
                <h4 class="font-weight-bold text-primary mb-3" style="font-size: 1.1rem;"><i class="fas fa-search-plus mr-1"></i> SEO & Meta Optimization</h4>
                
                <div class="form-group row mb-3">
                    <label class="col-sm-2 col-form-label small font-weight-600">Meta Title</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" name="meta_title" placeholder="Search engine title tag. If left blank, will fallback to article title.">
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label class="col-sm-2 col-form-label small font-weight-600">Meta Description</label>
                    <div class="col-sm-10">
                        <textarea class="form-control form-control-sm" name="meta_description" rows="2" placeholder="Brief summary of the article for Google search snippet."></textarea>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label class="col-sm-2 col-form-label small font-weight-600">Meta Keywords</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" name="meta_keywords" placeholder="Comma-separated keywords (e.g. food charity, volunteer stories)">
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <label class="col-sm-2 col-form-label small font-weight-600">JSON-LD Schema</label>
                    <div class="col-sm-10">
                        <textarea class="form-control form-control-sm text-monospace" name="schema_json" id="schema_json" rows="4" style="font-size: 12px;" placeholder="Schema JSON text..."></textarea>
                        <small class="form-text text-muted">Automatic JSON-LD Schema generated dynamically. Feel free to customize/edit it.</small>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important; padding: 8px 30px;"><i class="fas fa-save mr-1"></i> Save & Publish</button>
        </div>
    </form>
</div>

<!-- Import Trumbowyg JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/trumbowyg.min.js"></script>
<script>
var schemaTextareaDirty = false;

$(document).ready(function() {
    // Initialize Trumbowyg Editor
    $('#description').trumbowyg({
        btns: [
            ['viewHTML'],
            ['undo', 'redo'],
            ['formatting'],
            ['strong', 'em', 'del'],
            ['superscript', 'subscript'],
            ['link'],
            ['insertImage'],
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat'],
            ['fullscreen']
        ]
    });

    // Event listeners for automatic slug & schema generation
    document.getElementById('title').addEventListener('input', function() {
        var title = this.value;
        var slug = title.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '') // remove invalid characters
            .replace(/\s+/g, '-')       // collapse whitespace and replace by -
            .replace(/-+/g, '-');        // collapse dashes
        document.getElementById('slug').value = slug;
        updateSchema();
    });

    document.getElementById('schema_json').addEventListener('input', function() {
        schemaTextareaDirty = true;
    });
});

function previewImage(input) {
    var container = document.getElementById('previewContainer');
    var preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
            updateSchema();
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        container.style.display = 'none';
        updateSchema();
    }
}

function updateSchema() {
    var type = document.getElementById('type').value;
    var title = document.getElementById('title').value;
    var slug = document.getElementById('slug').value;
    var imageInput = document.getElementById('image');
    
    var imageURL = 'https://globalrisefoundation.org/assets/logo.png';
    if (imageInput.files && imageInput.files[0]) {
        imageURL = 'https://globalrisefoundation.org/assets/uploads/articles/' + imageInput.files[0].name;
    }
    
    var articleType = (type === 'blog') ? 'BlogPosting' : 'NewsArticle';
    
    var schema = {
        "@context": "https://schema.org",
        "@type": articleType,
        "headline": title || "Article Title",
        "image": [
            imageURL
        ],
        "datePublished": new Date().toISOString(),
        "dateModified": new Date().toISOString(),
        "author": {
            "@type": type === 'blog' ? "Person" : "Organization",
            "name": type === 'blog' ? "TGRF Author" : "The Global Rise Foundation"
        },
        "publisher": {
            "@type": "Organization",
            "name": "The Global Rise Foundation",
            "logo": {
                "@type": "ImageObject",
                "url": "https://globalrisefoundation.org/assets/logo.png"
            }
        },
        "description": "Read this article on The Global Rise Foundation portal."
    };
    
    if (!schemaTextareaDirty) {
        document.getElementById('schema_json').value = JSON.stringify(schema, null, 4);
    }
}
</script>

<?php include './footer.php'; ?>
