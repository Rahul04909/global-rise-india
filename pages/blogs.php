<?php
require_once __DIR__ . '/../includes/config.php';
$pdo = getDB();

$stmt = $pdo->prepare("SELECT * FROM `articles` WHERE `type` = 'blog' AND `status` = 'published' ORDER BY `created_at` DESC");
$stmt->execute();
$blogs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blogs & Articles | The Global Rise Foundation</title>
  <meta name="description" content="Read insightful blogs and articles on child nutrition, quality education, women empowerment, and sustainable community development by The Global Rise Foundation.">
  
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
    
    .blog-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 25px;
      margin-top: 25px;
    }
    
    .blog-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.02);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
    }
    
    .blog-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.07);
      border-color: #cbd5e0;
    }
    
    .blog-card-img-wrapper {
      width: 100%;
      height: 190px;
      overflow: hidden;
    }
    
    .blog-card-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }
    
    .blog-card:hover .blog-card-img {
      transform: scale(1.03);
    }
    
    .blog-card-content {
      padding: 22px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }
    
    .blog-card-meta {
      font-size: 11.5px;
      color: #a0aec0;
      margin-bottom: 10px;
      display: flex;
      gap: 12px;
    }
    
    .blog-card-content h3 {
      font-size: 1.15rem;
      font-weight: 700;
      color: #2d3748;
      margin: 0 0 10px 0;
      line-height: 1.35;
    }
    
    .blog-card-content p {
      font-size: 13.5px;
      color: #4a5568;
      line-height: 1.5;
      margin-bottom: 18px;
      text-align: left;
      flex-grow: 1;
    }
    
    .blog-card-link {
      font-size: 13px;
      font-weight: 700;
      color: #1b5182;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: color 0.2s;
      align-self: flex-start;
      margin-top: auto;
    }
    
    .blog-card-link:hover {
      color: #13a34a;
    }
    
    @media (max-width: 768px) {
      .blog-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }
    }
  </style>
</head>
<body class="page-body">

  <!-- Include Header Component -->
  <?php include '../includes/header.php'; ?>

  <!-- Page Hero Section -->
  <section class="initiative-hero" aria-label="Blogs Banner">
    <div class="header-container">
      <nav class="initiative-breadcrumbs" aria-label="Breadcrumb">
        <a href="../index.php">Home</a>
        <span class="separator">></span>
        <span style="color: #ffffff;">Blogs</span>
      </nav>
      <h1 class="initiative-title">Blogs & Articles</h1>
    </div>
  </section>

  <!-- Main Content Layout Grid -->
  <main class="initiative-layout-grid">
    
    <!-- Left Column: Core Content -->
    <article class="initiative-main-content">
      
      <section class="initiative-content-block">
        <h2>Perspectives on Development & Grassroots Action</h2>
        <p>Explore in-depth articles written by our field experts, social workers, and guest columnists on child development, food logistics, vocational designs, stray welfare protocols, and community empowerment frameworks.</p>
      </section>

      <!-- Blogs Grid -->
      <div class="blog-grid">
        <?php if (empty($blogs)): ?>
          <div style="grid-column: 1 / -1; text-align: center; color: #718096; padding: 40px 0;">
            <p>No blog posts found. Please check back later.</p>
          </div>
        <?php else: ?>
          <?php foreach ($blogs as $blog): 
            $image_src = $blog['image'] ?: 'assets/logo.png';
            $summary = strip_tags($blog['description']);
            if (strlen($summary) > 160) {
                $summary = substr($summary, 0, 157) . '...';
            }
          ?>
            <div class="blog-card">
              <div class="blog-card-img-wrapper">
                <img src="../<?= htmlspecialchars($image_src) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="blog-card-img" loading="lazy">
              </div>
              <div class="blog-card-content">
                <div class="blog-card-meta">
                  <span><i class="fa-regular fa-calendar"></i> <?= date('d F, Y', strtotime($blog['created_at'])) ?></span>
                  <span><i class="fa-regular fa-folder"></i> Advocacy</span>
                </div>
                <h3><?= htmlspecialchars($blog['title']) ?></h3>
                <p><?= htmlspecialchars($summary) ?></p>
                <a href="article-details.php?slug=<?= $blog['slug'] ?>" class="blog-card-link">Read Article <i class="fa-solid fa-arrow-right"></i></a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </article>

    <!-- Right Column: Sidebar Widgets -->
    <aside class="initiative-sidebar">
      
      <!-- Donation Appeal Card -->
      <section class="sidebar-cta-card">
        <h3>Support Our Publications</h3>
        <p>Your contribution keeps our research, articles, and field reporting teams funded. Help us raise awareness about these critical issues.</p>
        <a href="../donate.php" class="sidebar-cta-button">Donate Now</a>
      </section>

      <!-- Sidebar Navigation Menu (Categories) -->
      <nav class="sidebar-links-card" aria-label="Blog Categories Menu">
        <h3>Blog Categories</h3>
        <ul class="sidebar-links-list">
          <li><a href="#"><i class="fa-solid fa-angle-right"></i> Child Nutrition & Food (12)</a></li>
          <li><a href="#"><i class="fa-solid fa-angle-right"></i> Slum & Rural Education (18)</a></li>
          <li><a href="#"><i class="fa-solid fa-angle-right"></i> Livelihoods & Training (8)</a></li>
          <li><a href="#"><i class="fa-solid fa-angle-right"></i> Animal Welfare Advocacy (15)</a></li>
          <li><a href="#"><i class="fa-solid fa-angle-right"></i> Disaster Preparedness (5)</a></li>
        </ul>
      </nav>

    </aside>

  </main>

  <!-- Include Footer Component -->
  <?php include '../includes/footer.php'; ?>

</body>
</html>
