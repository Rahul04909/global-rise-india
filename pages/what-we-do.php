<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>What We Do | The Global Rise Foundation</title>
  <meta name="description" content="Discover the core social development focus areas of The Global Rise Foundation. Learn how we address education, health, animal welfare, and women empowerment.">
  
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
    
    .initiatives-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 25px;
      margin-top: 25px;
    }
    
    .initiative-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.02);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
    }
    
    .initiative-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
      border-color: #cbd5e0;
    }
    
    .initiative-card-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      transition: transform 0.4s ease;
    }
    
    .initiative-card:hover .initiative-card-img {
      transform: scale(1.03);
    }
    
    .initiative-card-img-wrapper {
      width: 100%;
      height: 180px;
      overflow: hidden;
    }
    
    .initiative-card-content {
      padding: 20px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }
    
    .initiative-card-content h3 {
      font-size: 1.15rem;
      font-weight: 700;
      color: #1b5182;
      margin-bottom: 10px;
      margin-top: 0;
    }
    
    .initiative-card-content p {
      font-size: 13.5px;
      color: #4a5568;
      margin-bottom: 15px;
      line-height: 1.5;
      flex-grow: 1;
      text-align: left;
    }
    
    .initiative-card-link {
      font-size: 13px;
      font-weight: 700;
      color: #13a34a;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: color 0.2s, padding-left 0.2s;
      align-self: flex-start;
    }
    
    .initiative-card-link:hover {
      color: #0f853b;
      padding-left: 3px;
    }
    
    @media (max-width: 768px) {
      .initiatives-grid {
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
  <section class="initiative-hero" aria-label="What We Do Banner">
    <div class="header-container">
      <nav class="initiative-breadcrumbs" aria-label="Breadcrumb">
        <a href="../index.php">Home</a>
        <span class="separator">></span>
        <span style="color: #ffffff;">What We Do</span>
      </nav>
      <h1 class="initiative-title">What We Do</h1>
    </div>
  </section>

  <!-- Main Content Layout Grid -->
  <main class="initiative-layout-grid">
    
    <!-- Left Column: Core Content -->
    <article class="initiative-main-content">
      
      <section class="initiative-content-block">
        <h2>Our Core Areas of Intervention</h2>
        <p>At The Global Rise Foundation, we are dedicated to resolving classroom hunger, ensuring access to quality education, and raising standards of living across underrepresented communities in India. We operate directly on the ground through a multi-sectoral development approach, recognizing that nutrition, education, health, and clean environments are deeply interconnected.</p>
        <p>Explore our active campaigns and social initiatives below. Each initiative represents a major vertical where we create direct, verifiable impact using structured, transparent processes.</p>
      </section>

      <!-- Initiatives Grid -->
      <div class="initiatives-grid">
        
        <!-- 1. Educating Slum Children -->
        <div class="initiative-card">
          <div class="initiative-card-img-wrapper">
            <img src="../assets/images/slum_education.png" alt="Educating Slum Children" class="initiative-card-img" loading="lazy">
          </div>
          <div class="initiative-card-content">
            <h3>Educating Slum Children</h3>
            <p>Bridging the learning gap for children living in urban slums. We provide digital learning aids, elementary classes, and evening tuition centers.</p>
            <a href="educating-slum-children.php" class="initiative-card-link">Learn More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- 2. Rural Children Education -->
        <div class="initiative-card">
          <div class="initiative-card-img-wrapper">
            <img src="../assets/images/rural_education.png" alt="Rural Children Education" class="initiative-card-img" loading="lazy">
          </div>
          <div class="initiative-card-content">
            <h3>Rural Children Education</h3>
            <p>Empowering children in rural communities through high-quality study resources, teacher trainings, and modern classroom infrastructures.</p>
            <a href="rural-children-education.php" class="initiative-card-link">Learn More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- 3. Health Projects -->
        <div class="initiative-card">
          <div class="initiative-card-img-wrapper">
            <img src="../assets/images/health_projects.png" alt="Health Projects" class="initiative-card-img" loading="lazy">
          </div>
          <div class="initiative-card-content">
            <h3>Health Projects</h3>
            <p>Conducting health screening camps, providing nutritional supplements, and coordinating vital medical aid for underprivileged families.</p>
            <a href="health-projects.php" class="initiative-card-link">Learn More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- 4. Women Empowerment -->
        <div class="initiative-card">
          <div class="initiative-card-img-wrapper">
            <img src="../assets/images/women_empowerment.png" alt="Women Empowerment" class="initiative-card-img" loading="lazy">
          </div>
          <div class="initiative-card-content">
            <h3>Women Empowerment</h3>
            <p>Enabling financial self-reliance through vocational training (such as tailoring, design), micro-grants, and entrepreneurship mentoring.</p>
            <a href="women-empowerment.php" class="initiative-card-link">Learn More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- 5. Persons with Disabilities -->
        <div class="initiative-card">
          <div class="initiative-card-img-wrapper">
            <img src="../assets/images/disabled_persons.png" alt="Persons with Disabilities" class="initiative-card-img" loading="lazy">
          </div>
          <div class="initiative-card-content">
            <h3>Persons with Disabilities</h3>
            <p>Providing physical rehabilitation kits, custom mobility aids, and career development support to foster independent lifestyles.</p>
            <a href="persons-with-disabilities.php" class="initiative-card-link">Learn More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- 6. Senior Citizen Care -->
        <div class="initiative-card">
          <div class="initiative-card-img-wrapper">
            <img src="../assets/images/senior_care.png" alt="Senior Citizen Care" class="initiative-card-img" loading="lazy">
          </div>
          <div class="initiative-card-content">
            <h3>Senior Citizen Care</h3>
            <p>Distributing essential food kits, offering primary geriatric checkups, and establishing safe shelter support programs for elders.</p>
            <a href="senior-citizen-care.php" class="initiative-card-link">Learn More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- 7. Swachh Bharat Mission -->
        <div class="initiative-card">
          <div class="initiative-card-img-wrapper">
            <img src="../assets/images/swachh_bharat.png" alt="Swachh Bharat Mission" class="initiative-card-img" loading="lazy">
          </div>
          <div class="initiative-card-content">
            <h3>Swachh Bharat Mission</h3>
            <p>Driving clean-up drives, solid waste management systems, and setting up clean hygiene infrastructure in schools and communities.</p>
            <a href="swacch-bharat-mission.php" class="initiative-card-link">Learn More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- 8. Animal Welfare -->
        <div class="initiative-card">
          <div class="initiative-card-img-wrapper">
            <img src="../assets/images/animal_welfare.png" alt="Animal Welfare" class="initiative-card-img" loading="lazy">
          </div>
          <div class="initiative-card-content">
            <h3>Animal Welfare</h3>
            <p>Supporting emergency veterinary rescues, stray vaccination programs, and building community stray animal feeding networks.</p>
            <a href="animal-welfare.php" class="initiative-card-link">Learn More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>

        <!-- 9. Disaster Management -->
        <div class="initiative-card">
          <div class="initiative-card-img-wrapper">
            <img src="../assets/images/disaster_relief.png" alt="Disaster Management" class="initiative-card-img" loading="lazy">
          </div>
          <div class="initiative-card-content">
            <h3>Disaster Management</h3>
            <p>Delivering on-ground emergency relief, dry ration packages, hygiene kits, and medical care to populations struck by natural disasters.</p>
            <a href="disaster-management.php" class="initiative-card-link">Learn More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>

      </div>

    </article>

    <!-- Right Column: Sidebar Widgets -->
    <aside class="initiative-sidebar">
      
      <!-- Donation Appeal Card -->
      <section class="sidebar-cta-card">
        <h3>Become a Cause Sponsor</h3>
        <p>Support our developmental programs. Your contribution helps sustain the field projects and directly impacts communities in need.</p>
        <a href="../donate.php" class="sidebar-cta-button">Donate Now</a>
      </section>

      <!-- Sidebar Navigation Menu -->
      <nav class="sidebar-links-card" aria-label="Our Initiatives Menu">
        <h3>Our Programs</h3>
        <ul class="sidebar-links-list">
          <li><a href="educating-slum-children.php"><i class="fa-solid fa-angle-right"></i> Educating Slum Children</a></li>
          <li><a href="rural-children-education.php"><i class="fa-solid fa-angle-right"></i> Rural Children Education</a></li>
          <li><a href="health-projects.php"><i class="fa-solid fa-angle-right"></i> Health Projects</a></li>
          <li><a href="women-empowerment.php"><i class="fa-solid fa-angle-right"></i> Women Empowerment</a></li>
          <li><a href="persons-with-disabilities.php"><i class="fa-solid fa-angle-right"></i> Persons with Disabilities</a></li>
          <li><a href="senior-citizen-care.php"><i class="fa-solid fa-angle-right"></i> Senior Citizen Care</a></li>
          <li><a href="swacch-bharat-mission.php"><i class="fa-solid fa-angle-right"></i> Swacch Bharat Mission</a></li>
          <li><a href="animal-welfare.php"><i class="fa-solid fa-angle-right"></i> Animal Welfare</a></li>
          <li><a href="disaster-management.php"><i class="fa-solid fa-angle-right"></i> Disaster Management</a></li>
        </ul>
      </nav>

    </aside>

  </main>

  <!-- Include Footer Component -->
  <?php include '../includes/footer.php'; ?>

</body>
</html>
