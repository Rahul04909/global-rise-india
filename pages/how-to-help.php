<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>How to Help | The Global Rise Foundation</title>
  <meta name="description" content="Find out how you can support The Global Rise Foundation. Learn about online donations, corporate CSR sponsorships, bank transfers, and volunteering.">
  
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
    
    .help-methods {
      display: flex;
      flex-direction: column;
      gap: 35px;
      margin-top: 20px;
    }
    
    .help-method-card {
      background: #fdfdfd;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 30px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.01);
      position: relative;
    }
    
    .help-method-card h3 {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1b5182;
      margin-top: 0;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .help-method-card h3 i {
      color: #13a34a;
      font-size: 1.4rem;
    }
    
    .help-method-card p {
      font-size: 14px;
      color: #4a5568;
      line-height: 1.6;
      margin-bottom: 20px;
      text-align: left;
    }
    
    .help-action-btn {
      display: inline-block;
      background-color: #1b5182;
      color: #ffffff;
      font-size: 13px;
      font-weight: 700;
      padding: 10px 25px;
      border-radius: 4px;
      text-decoration: none;
      text-transform: uppercase;
      transition: background 0.2s, transform 0.1s;
    }
    
    .help-action-btn:hover {
      background-color: #13a34a;
      transform: translateY(-1px);
    }
    
    .bank-details-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      font-size: 13.5px;
    }
    
    .bank-details-table td {
      padding: 10px 15px;
      border: 1px solid #e2e8f0;
    }
    
    .bank-details-table td.label-td {
      font-weight: 700;
      color: #2d3748;
      background-color: #f7fafc;
      width: 35%;
    }
    
    .bank-details-table td.value-td {
      color: #4a5568;
    }
  </style>
</head>
<body class="page-body">

  <!-- Include Header Component -->
  <?php include '../includes/header.php'; ?>

  <!-- Page Hero Section -->
  <section class="initiative-hero" aria-label="How To Help Banner">
    <div class="header-container">
      <nav class="initiative-breadcrumbs" aria-label="Breadcrumb">
        <a href="../index.php">Home</a>
        <span class="separator">></span>
        <span style="color: #ffffff;">How To Help</span>
      </nav>
      <h1 class="initiative-title">How to Help</h1>
    </div>
  </section>

  <!-- Main Content Layout Grid -->
  <main class="initiative-layout-grid">
    
    <!-- Left Column: Core Content -->
    <article class="initiative-main-content">
      
      <section class="initiative-content-block">
        <h2>Partner with Us to Create Lasting Impact</h2>
        <p>Eradicating hunger, enabling self-reliance, and providing quality education require collective societal commitment. Whether you are an individual wanting to sponsor a child's nutrition, or a large enterprise looking to make systemic changes under a CSR project, your partnership is vital to our mission.</p>
        <p>Below are the multiple channels through which you can offer support. Rest assured, all contributions are tracked, audited, and receipted instantly with maximum transparency.</p>
      </section>

      <!-- Ways to Support Cards List -->
      <div class="help-methods">
        
        <!-- 1. Online Contributions -->
        <div class="help-method-card">
          <h3><i class="fa-solid fa-credit-card"></i> Online Safe Donation</h3>
          <p>The fastest and most direct way to support our on-ground activities is via online payments. We accept all major Credit/Debit Cards, Net Banking, and instant UPI payments. All online contributions receive tax exemption receipts instantly on successful transaction capture.</p>
          <a href="../donate.php" class="help-action-btn">Donate Online</a>
        </div>

        <!-- 2. Direct Bank Transfers -->
        <div class="help-method-card">
          <h3><i class="fa-solid fa-building-columns"></i> Bank & Wire Transfers</h3>
          <p>For large contributions, institutional transfers, or recurring wire deposits, you can directly transfer funds into our verified organizational bank accounts. After performing a transfer, please mail your transaction details and PAN to <strong>finance@globalrisefoundation.org</strong> to claim your tax receipt.</p>
          
          <table class="bank-details-table">
            <tbody>
              <tr>
                <td class="label-td">Account Name</td>
                <td class="value-td">The Global Rise Foundation Trust</td>
              </tr>
              <tr>
                <td class="label-td">Bank Name</td>
                <td class="value-td">Axis Bank Of India</td>
              </tr>
              <tr>
                <td class="label-td">Account Number</td>
                <td class="value-td">926020024782390</td>
              </tr>
              <tr>
                <td class="label-td">IFSC Code</td>
                <td class="value-td">UTIB0000348</td>
              </tr>
              <tr>
                <td class="label-td">Branch Name</td>
                <td class="value-td">NIT Faridabad</td>
              </tr>
              <tr>
                <td class="label-td">Account Type</td>
                <td class="value-td">Current Account</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 3. Corporate CSR Collaborations -->
        <div class="help-method-card">
          <h3><i class="fa-solid fa-handshake"></i> Corporate CSR Projects</h3>
          <p>We work closely with corporate entities to run high-impact development initiatives. Businesses can align with our work by adopting central kitchens, sponsoring digital learning labs in rural districts, or sponsoring women's sewing machines batch-wise. All collaborations are fully compliant under CSR guidelines (Form CSR-1 registered).</p>
          <a href="mailto:csr@globalrisefoundation.org" class="help-action-btn">Contact CSR Team</a>
        </div>

        <!-- 4. Volunteer with Us -->
        <div class="help-method-card">
          <h3><i class="fa-solid fa-users"></i> Ground & Digital Volunteering</h3>
          <p>Do you wish to invest your skills and time instead of capital? Join our growing volunteer force. You can teach subjects online, participate in local distribution drives, write stories for our publications, or help build digital systems. Volunteer certificates are issued on successfully logging baseline hours.</p>
          <a href="../volunteer.php" class="help-action-btn">Register to Volunteer</a>
        </div>

      </div>

    </article>

    <!-- Right Column: Sidebar Widgets -->
    <aside class="initiative-sidebar">
      
      <!-- Donation Appeal Card -->
      <section class="sidebar-cta-card">
        <h3>Need Assistance?</h3>
        <p>If you face any issues while completing a transaction or need customization in your donation setup, contact our support team.</p>
        <a href="tel:+918049607200" class="sidebar-cta-button" style="background-color: #1b5182; box-shadow: 0 4px 8px rgba(27, 81, 130, 0.2);"><i class="fa-solid fa-phone"></i> Call Support</a>
      </section>

      <!-- Sidebar Navigation Menu -->
      <nav class="sidebar-links-card" aria-label="Support Navigation Menu">
        <h3>Support Info</h3>
        <ul class="sidebar-links-list">
          <li><a href="tax-exemption.php"><i class="fa-solid fa-angle-right"></i> Tax Exemption 80G</a></li>
          <li><a href="donation-faqs.php"><i class="fa-solid fa-angle-right"></i> Donation FAQs</a></li>
          <li><a href="terms-conditions.php"><i class="fa-solid fa-angle-right"></i> Refund Policy</a></li>
          <li><a href="about-us.php"><i class="fa-solid fa-angle-right"></i> Who We Are</a></li>
        </ul>
      </nav>

    </aside>

  </main>

  <!-- Include Footer Component -->
  <?php include '../includes/footer.php'; ?>

</body>
</html>
