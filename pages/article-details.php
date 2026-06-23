<?php
require_once __DIR__ . '/../includes/config.php';
$pdo = getDB();

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    header('Location: ../index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM `articles` WHERE `slug` = :slug AND `status` = 'published' LIMIT 1");
$stmt->execute([':slug' => $slug]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    include '../includes/header.php';
    echo '<div style="max-width: 800px; margin: 100px auto; text-align: center; font-family: sans-serif;">
            <h1 style="color: #1b5182; font-size: 3rem; margin-bottom: 20px;">404 Page Not Found</h1>
            <p style="color: #666; font-size: 1.1rem; margin-bottom: 30px;">The article you are trying to view does not exist or has been archived.</p>
            <a href="../index.php" style="background: #13a34a; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">Go to Home</a>
          </div>';
    include '../includes/footer.php';
    exit;
}

// Fetch related articles
$relatedStmt = $pdo->prepare("SELECT * FROM `articles` WHERE `type` = :type AND `status` = 'published' AND `id` != :id ORDER BY `created_at` DESC LIMIT 5");
$relatedStmt->execute([':type' => $article['type'], ':id' => $article['id']]);
$relatedArticles = $relatedStmt->fetchAll();

// Determine parent page title for breadcrumb
$parentTitle = ($article['type'] === 'blog') ? 'Blogs' : 'News & Stories';
$parentUrl = ($article['type'] === 'blog') ? 'blogs.php' : 'news-stories.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($article['meta_title'] ?: $article['title'] . ' | The Global Rise Foundation') ?></title>
  
  <?php if (!empty($article['meta_description'])): ?>
  <meta name="description" content="<?= htmlspecialchars($article['meta_description']) ?>">
  <?php else: ?>
  <meta name="description" content="<?= htmlspecialchars(strip_tags(substr($article['description'], 0, 160))) ?>">
  <?php endif; ?>
  
  <?php if (!empty($article['meta_keywords'])): ?>
  <meta name="keywords" content="<?= htmlspecialchars($article['meta_keywords']) ?>">
  <?php endif; ?>

  <!-- FontAwesome 6 CDN for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- CSS Stylesheets -->
  <link rel="stylesheet" href="../assets/css/header.css">
  <link rel="stylesheet" href="../assets/css/page-template.css">
  <link rel="stylesheet" href="../assets/css/footer.css">
  
  <style>
    body {
      background-color: #ffffff;
    }
    
    .article-meta-info {
      font-size: 13px;
      color: #718096;
      margin-bottom: 20px;
      display: flex;
      gap: 20px;
      border-bottom: 1px solid #edf2f7;
      padding-bottom: 15px;
    }
    
    .article-meta-info span {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    
    .article-meta-info i {
      color: #1b5182;
    }
    
    .article-rich-content {
      font-size: 15px;
      color: #2d3748;
      line-height: 1.75;
      text-align: justify;
    }
    
    .article-rich-content p {
      margin-bottom: 20px;
    }
    
    .article-rich-content h2, .article-rich-content h3 {
      color: #1b5182;
      font-weight: 700;
      margin-top: 30px;
      margin-bottom: 15px;
    }
    
    .article-rich-content ul, .article-rich-content ol {
      margin-bottom: 20px;
      padding-left: 20px;
    }
    
    .article-rich-content li {
      margin-bottom: 8px;
    }
    
    .badge-type {
      background-color: #ebf3fc;
      color: #1b5182;
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 50px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .badge-type.blog {
      background-color: #e6f6ec;
      color: #13a34a;
    }
  </style>

  <!-- JSON-LD Schema Injection -->
  <?php if (!empty($article['schema_json'])): ?>
  <script type="application/ld+json">
  <?= $article['schema_json'] ?>
  </script>
  <?php endif; ?>
</head>
<body class="page-body">

  <!-- Include Header Component -->
  <?php include '../includes/header.php'; ?>

  <!-- Page Hero Section -->
  <section class="initiative-hero" aria-label="Article Details Banner">
    <div class="header-container">
      <nav class="initiative-breadcrumbs" aria-label="Breadcrumb">
        <a href="../index.php">Home</a>
        <span class="separator">></span>
        <a href="<?= $parentUrl ?>"><?= $parentTitle ?></a>
        <span class="separator">></span>
        <span style="color: #ffffff;"><?= htmlspecialchars(substr($article['title'], 0, 30)) ?><?= strlen($article['title']) > 30 ? '...' : '' ?></span>
      </nav>
      <h1 class="initiative-title" style="font-size: 1.85rem; line-height: 1.3; text-transform: none; letter-spacing: 0;"><?= htmlspecialchars($article['title']) ?></h1>
    </div>
  </section>

  <!-- Main Content Layout Grid -->
  <main class="initiative-layout-grid">
    
    <!-- Left Column: Core Content -->
    <article class="initiative-main-content">
      
      <!-- Meta Information -->
      <div class="article-meta-info">
        <span><i class="fa-regular fa-calendar"></i> <?= date('d F, Y', strtotime($article['created_at'])) ?></span>
        <span><i class="fa-solid fa-folder-open"></i> <span class="badge-type <?= $article['type'] === 'blog' ? 'blog' : '' ?>"><?= $article['type'] === 'blog' ? 'Blog' : 'News & Stories' ?></span></span>
        <span><i class="fa-regular fa-user"></i> The Global Rise Foundation</span>
      </div>

      <!-- Header Image -->
      <?php if (!empty($article['image'])): ?>
      <div class="initiative-banner-wrapper">
        <img src="../<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="initiative-main-img" loading="lazy">
      </div>
      <?php endif; ?>

      <!-- Article Rich Description -->
      <div class="article-rich-content">
        <?= $article['description'] ?>
      </div>

    </article>

    <!-- Right Column: Sidebar Widgets -->
    <aside class="initiative-sidebar">
      
      <!-- Donation Appeal Card -->
      <section class="sidebar-cta-card">
        <h3>Support Our Cause</h3>
        <p>Your contribution directly funds the operational field initiatives described in our news updates and blogs.</p>
        <a href="../donate.php" class="sidebar-cta-button">Donate Now</a>
      </section>

      <!-- Sidebar Related Articles Links -->
      <?php if (!empty($relatedArticles)): ?>
      <nav class="sidebar-links-card" aria-label="Related Articles Menu">
        <h3>Related <?= $article['type'] === 'blog' ? 'Posts' : 'Stories' ?></h3>
        <ul class="sidebar-links-list">
          <?php foreach ($relatedArticles as $rel): ?>
          <li>
            <a href="article-details.php?slug=<?= $rel['slug'] ?>" title="<?= htmlspecialchars($rel['title']) ?>">
              <i class="fa-solid fa-angle-right"></i> 
              <span style="display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px;"><?= htmlspecialchars($rel['title']) ?></span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </nav>
      <?php endif; ?>

    </aside>

  </main>

  <!-- Include Footer Component -->
  <?php include '../includes/footer.php'; ?>

</body>
</html>
