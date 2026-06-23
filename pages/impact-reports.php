<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Impact Reports & Audits | The Global Rise Foundation</title>
  <meta name="description" content="View the audited reports, compliance details, and measurable social impact of The Global Rise Foundation. Learn how we utilize your support for communities.">
  
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
    
    .stats-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-top: 25px;
      margin-bottom: 35px;
    }
    
    .stat-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 25px 20px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.01);
      transition: all 0.3s ease;
    }
    
    .stat-box:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 15px rgba(0,0,0,0.05);
      border-color: #cbd5e0;
    }
    
    .stat-box-icon {
      font-size: 1.8rem;
      color: #13a34a;
      margin-bottom: 12px;
    }
    
    .stat-box-num {
      font-size: 1.85rem;
      font-weight: 800;
      color: #1b5182;
      margin-bottom: 8px;
      line-height: 1;
    }
    
    .stat-box-label {
      font-size: 13px;
      font-weight: 700;
      color: #4a5568;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .compliance-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-top: 20px;
    }
    
    .compliance-card {
      background: #ffffff;
      border: 1px solid #edf2f7;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.01);
      display: flex;
      align-items: flex-start;
      gap: 15px;
    }
    
    .compliance-icon {
      font-size: 1.5rem;
      color: #1b5182;
      margin-top: 3px;
    }
    
    .compliance-info h4 {
      margin: 0 0 5px 0;
      font-size: 14.5px;
      font-weight: 700;
      color: #2d3748;
    }
    
    .compliance-info p {
      margin: 0;
      font-size: 13px;
      color: #718096;
      line-height: 1.4;
    }
    
    .reports-list {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-top: 20px;
    }
    
    .report-download-item {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.2s;
      text-decoration: none;
    }
    
    .report-download-item:hover {
      border-color: #1b5182;
      background-color: #f7fafc;
    }
    
    .report-title-section {
      display: flex;
      align-items: center;
      gap: 12px;
      color: #2d3748;
      font-size: 14px;
      font-weight: 600;
    }
    
    .report-title-section i {
      font-size: 1.25rem;
      color: #e53e3e; /* Red PDF icon colors */
    }
    
    .report-download-btn {
      font-size: 13px;
      color: #1b5182;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .report-download-item:hover .report-download-btn {
      color: #13a34a;
    }
    
    @media (max-width: 768px) {
      .stats-container {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      .compliance-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
    }
  </style>
</head>
<body class="page-body">

  <!-- Include Header Component -->
  <?php include '../includes/header.php'; ?>

  <!-- Page Hero Section -->
  <section class="initiative-hero" aria-label="Impact Reports Banner">
    <div class="header-container">
      <nav class="initiative-breadcrumbs" aria-label="Breadcrumb">
        <a href="../index.php">Home</a>
        <span class="separator">></span>
        <span style="color: #ffffff;">Impact Reports</span>
      </nav>
      <h1 class="initiative-title">Impact Reports & Audits</h1>
    </div>
  </section>

  <!-- Main Content Layout Grid -->
  <main class="initiative-layout-grid">
    
    <!-- Left Column: Core Content -->
    <article class="initiative-main-content">
      
      <section class="initiative-content-block">
        <h2>Absolute Financial and Operational Transparency</h2>
        <p>At The Global Rise Foundation, we hold ourselves accountable to our donors, institutional partners, and the communities we serve. We believe transparency builds trust, and trust enables growth. Every rupee received is traced, cataloged, and allocated with optimal operational efficiency. Below are the key metric outcomes of our projects, along with access to verified audit compliance dossiers.</p>
      </section>

      <!-- Key Impact Numbers Dashboard Grid -->
      <section class="initiative-content-block">
        <h2>Our Impact In Numbers</h2>
        <div class="stats-container">
          
          <!-- Stat 1 -->
          <div class="stat-box">
            <div class="stat-box-icon"><i class="fa-solid fa-bowl-rice"></i></div>
            <div class="stat-box-num">1.2 Million+</div>
            <div class="stat-box-label">Meals Served</div>
          </div>

          <!-- Stat 2 -->
          <div class="stat-box">
            <div class="stat-box-icon"><i class="fa-solid fa-graduation-cap"></i></div>
            <div class="stat-box-num">15,000+</div>
            <div class="stat-box-label">Students Supported</div>
          </div>

          <!-- Stat 3 -->
          <div class="stat-box">
            <div class="stat-box-icon"><i class="fa-solid fa-scissors"></i></div>
            <div class="stat-box-num">1,500+</div>
            <div class="stat-box-label">Women Empowered</div>
          </div>

          <!-- Stat 4 -->
          <div class="stat-box">
            <div class="stat-box-icon"><i class="fa-solid fa-paw"></i></div>
            <div class="stat-box-num">2,000+</div>
            <div class="stat-box-label">Animals Rescued</div>
          </div>

          <!-- Stat 5 -->
          <div class="stat-box">
            <div class="stat-box-icon"><i class="fa-solid fa-house-chimney-medical"></i></div>
            <div class="stat-box-num">5,000+</div>
            <div class="stat-box-label">Relief Families Aided</div>
          </div>

          <!-- Stat 6 -->
          <div class="stat-box">
            <div class="stat-box-icon"><i class="fa-solid fa-users"></i></div>
            <div class="stat-box-num">800+</div>
            <div class="stat-box-label">Active Volunteers</div>
          </div>

        </div>
      </section>

      <!-- Audited Statements & PDF Downloads -->
      <section class="initiative-content-block">
        <h2>Audited Financial Statements</h2>
        <p>Our books and accounting statements are audited annually by certified independent Chartered Accountants. You can download our audited balance sheets and receipts/payments statements below.</p>
        
        <div class="reports-list">
          
          <!-- PDF Report 1 -->
          <a href="#" class="report-download-item">
            <div class="report-title-section">
              <i class="fa-solid fa-file-pdf"></i>
              <span>Annual Audit Statement FY 2024 - 2025</span>
            </div>
            <div class="report-download-btn">
              Download PDF <i class="fa-solid fa-arrow-down-long"></i>
            </div>
          </a>

          <!-- PDF Report 2 -->
          <a href="#" class="report-download-item">
            <div class="report-title-section">
              <i class="fa-solid fa-file-pdf"></i>
              <span>Annual Audit Statement FY 2023 - 2024</span>
            </div>
            <div class="report-download-btn">
              Download PDF <i class="fa-solid fa-arrow-down-long"></i>
            </div>
          </a>

          <!-- PDF Report 3 -->
          <a href="#" class="report-download-item">
            <div class="report-title-section">
              <i class="fa-solid fa-file-pdf"></i>
              <span>Annual Audit Statement FY 2022 - 2023</span>
            </div>
            <div class="report-download-btn">
              Download PDF <i class="fa-solid fa-arrow-down-long"></i>
            </div>
          </a>

        </div>
      </section>

      <!-- Compliance Registrations info -->
      <section class="initiative-content-block">
        <h2>Trust Registrations & Compliance</h2>
        <p>The Global Rise Foundation Trust operates in complete alignment with regulatory guidelines of the Ministry of Corporate Affairs and the Income Tax Department.</p>
        
        <div class="compliance-grid">
          
          <!-- Card 1 -->
          <div class="compliance-card">
            <div class="compliance-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <div class="compliance-info">
              <h4>Section 12A & 80G Registered</h4>
              <p>Registered under section 12A of the Income Tax Act. All contributions qualify for tax exemptions under Section 80G.</p>
            </div>
          </div>

          <!-- Card 2 -->
          <div class="compliance-card">
            <div class="compliance-icon"><i class="fa-solid fa-briefcase"></i></div>
            <div class="compliance-info">
              <h4>CSR-1 Registration</h4>
              <p>Approved by the Ministry of Corporate Affairs for executing Corporate Social Responsibility campaigns. Reg No: CSR00010485.</p>
            </div>
          </div>

          <!-- Card 3 -->
          <div class="compliance-card">
            <div class="compliance-icon"><i class="fa-solid fa-file-invoice"></i></div>
            <div class="compliance-info">
              <h4>NITI Aayog NGO Darpan</h4>
              <p>Listed on the government NGO portal for institutional compliance, verifying our active status. Unique ID: KA/2021/0290145.</p>
            </div>
          </div>

          <!-- Card 4 -->
          <div class="compliance-card">
            <div class="compliance-icon"><i class="fa-solid fa-landmark"></i></div>
            <div class="compliance-info">
              <h4>FCRA Compliant</h4>
              <p>Fully compliant with FCRA guidelines, ensuring smooth management of any international funding collaborations.</p>
            </div>
          </div>

        </div>
      </section>

    </article>

    <!-- Right Column: Sidebar Widgets -->
    <aside class="initiative-sidebar">
      
      <!-- Donation Appeal Card -->
      <section class="sidebar-cta-card">
        <h3>Invest in Transparency</h3>
        <p>Your donation is spent directly on execution models. We maintain regular updates so you can see exactly where your funds are going.</p>
        <a href="../donate.php" class="sidebar-cta-button">Donate Now</a>
      </section>

      <!-- Sidebar Navigation Menu -->
      <nav class="sidebar-links-card" aria-label="Trust Navigation Menu">
        <h3>Who We Are</h3>
        <ul class="sidebar-links-list">
          <li><a href="about-us.php"><i class="fa-solid fa-angle-right"></i> About Us</a></li>
          <li><a href="vision-mission.php"><i class="fa-solid fa-angle-right"></i> Our Vision & Mission</a></li>
          <li><a href="tax-exemption.php"><i class="fa-solid fa-angle-right"></i> Tax Exemption 80G</a></li>
          <li><a href="donation-faqs.php"><i class="fa-solid fa-angle-right"></i> Donation FAQs</a></li>
        </ul>
      </nav>

    </aside>

  </main>

  <!-- Include Footer Component -->
  <?php include '../includes/footer.php'; ?>

</body>
</html>
