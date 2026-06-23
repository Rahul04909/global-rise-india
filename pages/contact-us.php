<?php
require_once __DIR__ . '/../includes/config.php';
$pdo = getDB();

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_msg = 'Please fill out all required fields (*).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'Please enter a valid email address.';
    } else {
        try {
            // Create contact_inquiries table if not exists
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `contact_inquiries` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(100) NOT NULL,
                    `email` VARCHAR(100) NOT NULL,
                    `phone` VARCHAR(20) NULL,
                    `subject` VARCHAR(150) NOT NULL,
                    `message` TEXT NOT NULL,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");

            // Insert submission
            $stmt = $pdo->prepare("
                INSERT INTO `contact_inquiries` (name, email, phone, subject, message)
                VALUES (:name, :email, :phone, :subject, :message)
            ");
            $stmt->execute([
                ':name'    => $name,
                ':email'   => $email,
                ':phone'   => $phone ?: null,
                ':subject' => $subject,
                ':message' => $message
            ]);

            $success_msg = 'Thank you for reaching out! We have received your inquiry and will contact you shortly.';
            
            // Clean fields after success
            $_POST = [];
        } catch (PDOException $e) {
            error_log('[Contact Form DB Error] ' . $e->getMessage());
            $error_msg = 'Unable to send message due to a database exception.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us | The Global Rise Foundation</title>
  <meta name="description" content="Get in touch with The Global Rise Foundation. Reach us via phone, email, or visit our office in Bangalore. Submit your queries directly using our contact form.">
  
  <!-- FontAwesome 6 CDN for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- CSS Stylesheets -->
  <link rel="stylesheet" href="../assets/css/header.css">
  <link rel="stylesheet" href="../assets/css/page-template.css">
  <link rel="stylesheet" href="../assets/css/footer.css">
  
  <!-- SweetAlert2 CDN for modern notices -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <style>
    body {
      background-color: #ffffff;
    }
    
    .contact-form-container {
      background: #fdfdfd;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 35px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.01);
    }
    
    .contact-form-group {
      margin-bottom: 20px;
    }
    
    .contact-form-group label {
      display: block;
      font-size: 13px;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .contact-form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      font-size: 14px;
      color: #4a5568;
      transition: all 0.2s ease;
      box-sizing: border-box;
    }
    
    .contact-form-control:focus {
      border-color: #1b5182;
      box-shadow: 0 0 0 3px rgba(27, 81, 130, 0.15);
      outline: none;
    }
    
    .contact-submit-btn {
      background-color: #13a34a;
      color: #ffffff;
      font-size: 13.5px;
      font-weight: 700;
      text-transform: uppercase;
      padding: 12px 35px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      letter-spacing: 0.8px;
      transition: background-color 0.2s, transform 0.1s;
      box-shadow: 0 4px 8px rgba(19, 163, 74, 0.2);
    }
    
    .contact-submit-btn:hover {
      background-color: #0f853b;
      box-shadow: 0 6px 12px rgba(19, 163, 74, 0.3);
      transform: translateY(-1px);
    }
    
    .contact-info-widget {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 25px;
      margin-bottom: 25px;
    }
    
    .contact-info-item {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
      align-items: flex-start;
    }
    
    .contact-info-item:last-child {
      margin-bottom: 0;
    }
    
    .contact-info-icon {
      font-size: 1.25rem;
      color: #13a34a;
      margin-top: 3px;
      background-color: rgba(19, 163, 74, 0.08);
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }
    
    .contact-info-text h4 {
      margin: 0 0 5px 0;
      font-size: 13.5px;
      font-weight: 700;
      color: #2d3748;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .contact-info-text p, .contact-info-text a {
      margin: 0;
      font-size: 13.5px;
      color: #4a5568;
      line-height: 1.5;
      text-decoration: none;
    }
    
    .contact-info-text a:hover {
      color: #1b5182;
      text-decoration: underline;
    }
    
    .map-container {
      width: 100%;
      height: 250px;
      border-radius: 8px;
      overflow: hidden;
      border: 1px solid #e2e8f0;
      box-shadow: 0 4px 10px rgba(0,0,0,0.01);
    }
    
    .map-container iframe {
      width: 100%;
      height: 100%;
      border: 0;
    }
  </style>
</head>
<body class="page-body">

  <!-- Include Header Component -->
  <?php include '../includes/header.php'; ?>

  <!-- Page Hero Section -->
  <section class="initiative-hero" aria-label="Contact Us Banner">
    <div class="header-container">
      <nav class="initiative-breadcrumbs" aria-label="Breadcrumb">
        <a href="../index.php">Home</a>
        <span class="separator">></span>
        <span style="color: #ffffff;">Contact Us</span>
      </nav>
      <h1 class="initiative-title">Contact Us</h1>
    </div>
  </section>

  <!-- Main Content Layout Grid -->
  <main class="initiative-layout-grid">
    
    <!-- Left Column: Core Content (Inquiry Form) -->
    <article class="initiative-main-content">
      
      <section class="initiative-content-block">
        <h2>Send Us a Message</h2>
        <p>If you have any questions about our operations, support plans, tax exemptions, or volunteering programs, please write to us. Fill out the secure form below, and our response coordination team will get back to you within 24–48 working hours.</p>
      </section>

      <div class="contact-form-container">
        <form action="contact-us.php" method="POST" id="contactForm">
          
          <div class="contact-form-group">
            <label for="name">Your Name *</label>
            <input type="text" name="name" id="name" class="contact-form-control" placeholder="e.g. Rajesh Kumar" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
          </div>

          <div class="row">
            <div class="col-md-6 contact-form-group">
              <label for="email">Email Address *</label>
              <input type="email" name="email" id="email" class="contact-form-control" placeholder="e.g. rajesh@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 contact-form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" name="phone" id="phone" class="contact-form-control" placeholder="e.g. +91 9845012345" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
          </div>

          <div class="contact-form-group">
            <label for="subject">Subject *</label>
            <input type="text" name="subject" id="subject" class="contact-form-control" placeholder="e.g. CSR Partnership Enquiry" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
          </div>

          <div class="contact-form-group">
            <label for="message">Message *</label>
            <textarea name="message" id="message" rows="6" class="contact-form-control" placeholder="Write your message here..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>

          <div style="margin-top: 25px;">
            <button type="submit" class="contact-submit-btn"><i class="fa-regular fa-paper-plane"></i> Send Message</button>
          </div>

        </form>
      </div>

    </article>

    <!-- Right Column: Sidebar Widgets (Contact Details & Map) -->
    <aside class="initiative-sidebar">
      
      <!-- Contact details card -->
      <section class="contact-info-widget">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: #1b5182; margin-top: 0; margin-bottom: 20px; border-bottom: 1.5px solid #eef2f5; padding-bottom: 8px;">Office Information</h3>
        
        <div class="contact-info-item">
          <div class="contact-info-icon"><i class="fa-solid fa-location-dot"></i></div>
          <div class="contact-info-text">
            <h4>Registered Office</h4>
            <p>The Global Rise Foundation Trust,<br>#14, 10th Cross, Malleshwaram,<br>Bangalore, Karnataka - 560003</p>
          </div>
        </div>

        <div class="contact-info-item">
          <div class="contact-info-icon"><i class="fa-solid fa-phone"></i></div>
          <div class="contact-info-text">
            <h4>Call Us</h4>
            <p><a href="tel:+9172920007777">+91 729200-07777</a></p>
            <p class="small text-muted">Tel: 080-23474351 (BSNL)</p>
          </div>
        </div>

        <div class="contact-info-item">
          <div class="contact-info-icon"><i class="fa-solid fa-envelope"></i></div>
          <div class="contact-info-text">
            <h4>Email Inquiries</h4>
            <p><a href="mailto:info@globalrisefoundation.com">info@globalrisefoundation.com</a></p>
          </div>
        </div>

      </section>

      <!-- Google Map embed widget -->
      <section class="contact-info-widget" style="padding: 10px;">
        <div class="map-container">
          <!-- Google Maps iframe pointing to Malleshwaram Bangalore -->
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m12!1m3!1d3887.6186411545645!2d77.568461!3d12.996162!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae162237887d15%3A0x6b402cf806cf2b63!2sMalleshwaram%2C%20Bengaluru%2C%20Karnataka!5e0!3m2!1sen!2sin!4v1680000000000!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </section>

    </aside>

  </main>

  <!-- Include Footer Component -->
  <?php include '../includes/footer.php'; ?>

  <!-- SweetAlert handlers for notification feedback -->
  <?php if (!empty($success_msg)): ?>
  <script>
      Swal.fire({
          title: 'Message Sent!',
          text: '<?= htmlspecialchars($success_msg) ?>',
          icon: 'success',
          confirmButtonColor: '#13a34a'
      });
  </script>
  <?php endif; ?>

  <?php if (!empty($error_msg)): ?>
  <script>
      Swal.fire({
          title: 'Validation Error',
          text: '<?= htmlspecialchars($error_msg) ?>',
          icon: 'error',
          confirmButtonColor: '#1b5182'
      });
  </script>
  <?php endif; ?>

</body>
</html>
