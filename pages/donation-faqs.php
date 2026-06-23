<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Donation FAQs | The Global Rise Foundation</title>
  <meta name="description" content="Find answers to common questions about contributing, online security, tax exemptions, and donation options at The Global Rise Foundation.">
  
  <!-- FontAwesome 6 CDN for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- CSS Stylesheets -->
  <link rel="stylesheet" href="../assets/css/header.css">
  <link rel="stylesheet" href="../assets/css/page-template.css">
  <link rel="stylesheet" href="../assets/css/footer.css">
  
  <style>
    body { background-color: #ffffff; }
    .faq-item { margin-bottom: 25px; }
    .faq-question { font-weight: 700; color: #2c3e50; font-size: 1.1rem; margin-bottom: 8px; }
    .faq-answer { color: #4a5568; line-height: 1.65; }
  </style>
</head>
<body class="page-body">

  <!-- Include Header Component -->
  <?php include '../includes/header.php'; ?>

  <!-- Page Hero Section -->
  <section class="initiative-hero" aria-label="FAQs Banner">
    <div class="header-container">
      <nav class="initiative-breadcrumbs" aria-label="Breadcrumb">
        <a href="../index.php">Home</a>
        <span class="separator">></span>
        <span>Donate</span>
        <span class="separator">></span>
        <span style="color: #ffffff;">Donation FAQs</span>
      </nav>
      <h1 class="initiative-title">Donation FAQs</h1>
    </div>
  </section>

  <!-- Main Content Layout Grid -->
  <main class="initiative-layout-grid">
    
    <!-- Left Column: Core Content -->
    <article class="initiative-main-content">

      <section class="initiative-content-block">
        <h2>Frequently Asked Questions</h2>
        <p>If you have any questions regarding contributions, transaction security, or tax exemptions, please review our comprehensive guide below.</p>
        
        <div style="margin-top: 30px;">
          
          <div class="faq-item">
            <div class="faq-question">1. How can I contribute online?</div>
            <div class="faq-answer">
              You can make secure online contributions directly through our donation form on <a href="../donate.php">Online Donations Page</a>. We use Razorpay to accept credit/debit cards, net banking, UPI, and popular digital wallets.
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">2. Is my online transaction secure?</div>
            <div class="faq-answer">
              Yes, absolutely. All payment operations are processed over a secure SSL-encrypted channel. Razorpay is fully PCI-DSS compliant, ensuring your credit card, bank, and transaction details are handled under international security standards. We do not store financial credentials on our servers.
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">3. Am I eligible for tax exemptions under 80G?</div>
            <div class="faq-answer">
              Yes. All contributions made by Indian taxpayers qualify for a 50% tax deduction under Section 80G of the Income Tax Act. To request a certificate, please check the 80G checkbox on the donation form and provide a valid Indian PAN card number and billing address.
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">4. Can I donate offline?</div>
            <div class="faq-answer">
              Yes, we accept offline contributions via Cash, Cheques, or direct Bank Wire Transfers. Please write to us at <a href="mailto:info@globalrisefoundation.org">info@globalrisefoundation.org</a> or call us to obtain bank details and coordinate cash collection.
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">5. What is the minimum contribution amount?</div>
            <div class="faq-answer">
              There is no barrier to kindness. The minimum online transaction limit is ₹1. Every single rupee helps us feed children and empower families.
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">6. How is my donation utilized?</div>
            <div class="faq-answer">
              The Global Rise Foundation operates on-ground directly. 92% of your contribution is directly spent on project delivery (purchasing grain, preparing mid-day meals, securing tailors and workshops). The remaining 8% supports necessary administrative audits, database management, and field coordinate overheads.
            </div>
          </div>

        </div>
      </section>

    </article>

    <!-- Right Column: Sidebar Widgets -->
    <aside class="initiative-sidebar">
      
      <!-- Donation Appeal Card -->
      <section class="sidebar-cta-card">
        <h3>Have More Questions?</h3>
        <p>If you need further details or want to set up corporate sponsorship, please contact our support desk.</p>
        <a href="mailto:info@globalrisefoundation.org" class="sidebar-cta-button" style="text-align: center;"><i class="fas fa-question-circle mr-1"></i> Ask a Question</a>
      </section>

      <!-- Sidebar Navigation Menu -->
      <nav class="sidebar-links-card" aria-label="Causes Navigation">
        <h3>Who We Are</h3>
        <ul class="sidebar-links-list">
          <li><a href="about-us.php"><i class="fa-solid fa-angle-right"></i> About Us</a></li>
          <li><a href="vision-mission.php"><i class="fa-solid fa-angle-right"></i> Vision & Mission</a></li>
          <li><a href="inspiration.php"><i class="fa-solid fa-angle-right"></i> Story of Hope</a></li>
          <li><a href="board-trustees.php"><i class="fa-solid fa-angle-right"></i> Board of Trustees</a></li>
          <li><a href="tax-exemption.php"><i class="fa-solid fa-angle-right"></i> Tax Exemption Info</a></li>
          <li><a href="donation-faqs.php" style="color: #1b5182;"><i class="fa-solid fa-angle-right"></i> Donation FAQs</a></li>
        </ul>
      </nav>

    </aside>

  </main>

  <!-- Include Footer Component -->
  <?php include '../includes/footer.php'; ?>

</body>
</html>
