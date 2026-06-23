<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News & Stories | The Global Rise Foundation</title>
  <meta name="description" content="Stay updated with the latest news, on-ground stories, and success stories of community transformation from The Global Rise Foundation.">
  
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
    
    .news-grid {
      display: flex;
      flex-direction: column;
      gap: 30px;
      margin-top: 25px;
    }
    
    .news-item {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.02);
      transition: all 0.3s ease;
      display: flex;
    }
    
    .news-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.06);
      border-color: #cbd5e0;
    }
    
    .news-item-img-wrapper {
      width: 250px;
      min-width: 250px;
      overflow: hidden;
      position: relative;
    }
    
    .news-item-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }
    
    .news-item:hover .news-item-img {
      transform: scale(1.04);
    }
    
    .news-item-content {
      padding: 25px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }
    
    .news-badge {
      align-self: flex-start;
      background-color: #ebf3fc;
      color: #1b5182;
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 50px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 12px;
    }
    
    .news-badge.success {
      background-color: #e6f6ec;
      color: #13a34a;
    }
    
    .news-badge.alert {
      background-color: #fff5f5;
      color: #e53e3e;
    }
    
    .news-item-content h3 {
      font-size: 1.3rem;
      font-weight: 700;
      color: #2d3748;
      margin: 0 0 10px 0;
      line-height: 1.3;
    }
    
    .news-meta {
      font-size: 12px;
      color: #a0aec0;
      margin-bottom: 15px;
      display: flex;
      gap: 15px;
    }
    
    .news-meta span {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .news-item-content p {
      font-size: 14px;
      color: #4a5568;
      line-height: 1.6;
      margin-bottom: 15px;
      text-align: left;
    }
    
    .news-read-more {
      font-size: 13px;
      font-weight: 700;
      color: #1b5182;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      margin-top: auto;
      transition: color 0.2s;
    }
    
    .news-read-more:hover {
      color: #13a34a;
    }
    
    @media (max-width: 768px) {
      .news-item {
        flex-direction: column;
      }
      
      .news-item-img-wrapper {
        width: 100%;
        height: 200px;
      }
    }
  </style>
</head>
<body class="page-body">

  <!-- Include Header Component -->
  <?php include '../includes/header.php'; ?>

  <!-- Page Hero Section -->
  <section class="initiative-hero" aria-label="News & Stories Banner">
    <div class="header-container">
      <nav class="initiative-breadcrumbs" aria-label="Breadcrumb">
        <a href="../index.php">Home</a>
        <span class="separator">></span>
        <span style="color: #ffffff;">News & Stories</span>
      </nav>
      <h1 class="initiative-title">News & Stories</h1>
    </div>
  </section>

  <!-- Main Content Layout Grid -->
  <main class="initiative-layout-grid">
    
    <!-- Left Column: Core Content -->
    <article class="initiative-main-content">
      
      <section class="initiative-content-block">
        <h2>Latest Updates & Ground Realities</h2>
        <p>Explore the real stories behind our metrics. At The Global Rise Foundation, every project represents human lives transformed, children nourished, and communities empowered. Read our latest press bulletins, success stories of hope, and emergency campaign logs from our project locations across India.</p>
      </section>

      <!-- News and Stories Grid -->
      <div class="news-grid">
        
        <!-- News Item 1 -->
        <div class="news-item">
          <div class="news-item-img-wrapper">
            <img src="../assets/images/slide1.png" alt="Centralized Kitchen Opening" class="news-item-img" loading="lazy">
          </div>
          <div class="news-item-content">
            <span class="news-badge">Press Release</span>
            <h3>New Centralized Mid Day Meal Kitchen Launched in Bangalore</h3>
            <div class="news-meta">
              <span><i class="fa-regular fa-calendar"></i> June 15, 2026</span>
              <span><i class="fa-regular fa-user"></i> Program Director</span>
            </div>
            <p>To support more government school children and eradicate classroom hunger, we have inaugurated our new fully automated central kitchen in North Bangalore. Spanning 5,000 sq ft, this kitchen will prepare hot, nutritional meals for over 5,000 students daily under absolute hygiene standards.</p>
            <a href="#" class="news-read-more">Read Full Story <i class="fa-solid fa-chevron-right"></i></a>
          </div>
        </div>

        <!-- News Item 2 -->
        <div class="news-item">
          <div class="news-item-img-wrapper">
            <img src="../assets/images/women_empowerment.png" alt="Anita graduation story" class="news-item-img" loading="lazy">
          </div>
          <div class="news-item-content">
            <span class="news-badge success">Success Story</span>
            <h3>Stitch by Stitch: How Anita Achieved Livelihood Independence</h3>
            <div class="news-meta">
              <span><i class="fa-regular fa-calendar"></i> May 28, 2026</span>
              <span><i class="fa-regular fa-user"></i> Impact Team</span>
            </div>
            <p>Anita, a resident of an urban slum cluster, was struggle-ridden as the sole breadwinner for a family of four. After enrolling in our 6-month Vocational Tailoring program, she received a free sewing machine and entrepreneurial guidance. Today, Anita runs a home-based boutique, earning a stable livelihood and funding her children's schooling.</p>
            <a href="#" class="news-read-more">Read Full Story <i class="fa-solid fa-chevron-right"></i></a>
          </div>
        </div>

        <!-- News Item 3 -->
        <div class="news-item">
          <div class="news-item-img-wrapper">
            <img src="../assets/images/disaster_relief.png" alt="Flood disaster relief dispatch" class="news-item-img" loading="lazy">
          </div>
          <div class="news-item-content">
            <span class="news-badge alert">Relief Update</span>
            <h3>Emergency Flood Relief Kits Dispatched to Rural Communities</h3>
            <div class="news-meta">
              <span><i class="fa-regular fa-calendar"></i> May 12, 2026</span>
              <span><i class="fa-regular fa-user"></i> Disaster Response</span>
            </div>
            <p>In response to the flash floods affecting coastal hamlets, our rapid action force has set up temporary medical camps and successfully distributed 1,200 survival kits containing dry rations, sanitizers, chlorine tablets, and warm blankets to the displaced families.</p>
            <a href="#" class="news-read-more">Read Full Story <i class="fa-solid fa-chevron-right"></i></a>
          </div>
        </div>

        <!-- News Item 4 -->
        <div class="news-item">
          <div class="news-item-img-wrapper">
            <img src="../assets/images/slide2.png" alt="Children study session" class="news-item-img" loading="lazy">
          </div>
          <div class="news-item-content">
            <span class="news-badge">Ground Update</span>
            <h3>Digital Learning Tools Introduced in Rural Primary Schools</h3>
            <div class="news-meta">
              <span><i class="fa-regular fa-calendar"></i> April 20, 2026</span>
              <span><i class="fa-regular fa-user"></i> Education Specialist</span>
            </div>
            <p>To reduce learning loss and improve digital literacy, we have deployed 50 interactive tablet learning setups across three rural primary schools. These kits feature offline curriculum modules, visual sciences, and basic mathematics software, bringing digital pedagogy to remote corners.</p>
            <a href="#" class="news-read-more">Read Full Story <i class="fa-solid fa-chevron-right"></i></a>
          </div>
        </div>

      </div>

    </article>

    <!-- Right Column: Sidebar Widgets -->
    <aside class="initiative-sidebar">
      
      <!-- Donation Appeal Card -->
      <section class="sidebar-cta-card">
        <h3>Support On-Ground Action</h3>
        <p>Make a contribution to help us fund emergency relief supplies, classroom resources, and vocational starter setups.</p>
        <a href="../donate.php" class="sidebar-cta-button">Donate Now</a>
      </section>

      <!-- Sidebar Navigation Menu (Categories) -->
      <nav class="sidebar-links-card" aria-label="Stories Category Menu">
        <h3>Filter Stories</h3>
        <ul class="sidebar-links-list">
          <li><a href="#"><i class="fa-solid fa-angle-right"></i> Press Releases</a></li>
          <li><a href="#"><i class="fa-solid fa-angle-right"></i> On-Ground Success Stories</a></li>
          <li><a href="#"><i class="fa-solid fa-angle-right"></i> Disaster Relief Dispatches</a></li>
          <li><a href="#"><i class="fa-solid fa-angle-right"></i> Volunteer Spotlight</a></li>
          <li><a href="#"><i class="fa-solid fa-angle-right"></i> Annual Reports</a></li>
        </ul>
      </nav>

    </aside>

  </main>

  <!-- Include Footer Component -->
  <?php include '../includes/footer.php'; ?>

</body>
</html>
