<?php
// Calculate relative base url path dynamically to support inclusion in nested directories
$footer_base_url = '';
$request_uri = $_SERVER['REQUEST_URI'];
$project_name = 'gloabl-rise';
$pos = strpos($request_uri, $project_name);
if ($pos !== false) {
    $footer_base_url = substr($request_uri, 0, $pos + strlen($project_name)) . '/';
} else {
    $footer_base_url = '/';
}
?>
<!-- Footer Component -->
<footer class="site-footer">
  <div class="header-container footer-grid">
    
    <!-- Column 1: Branding, Tagline, Socials & QR Code -->
    <div class="footer-brand">
      <img src="<?php echo $footer_base_url; ?>assets/logo.png" alt="The Global Rise Foundation Logo" class="footer-brand-logo">
      <p class="footer-tagline">The Global Rise Foundation is a non profit organisation that strives to eliminate classroom hunger by implementing the Mid Day Meal Programme</p>
      
      <!-- Social Media Icons -->
      <div class="footer-socials">
        <a href="#" class="footer-social-link" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" class="footer-social-link" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
        <a href="#" class="footer-social-link" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#" class="footer-social-link" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
        <a href="#" class="footer-social-link" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
      </div>

      <!-- QR Code Card Box -->
      <div class="footer-qr-card">
        <div class="footer-qr-title">The Global Rise Foundation</div>
        <!-- Generates UPI payload QR code dynamically -->
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&color=000000&data=upi://pay?pa=globalrise@kotak" alt="UPI Donation QR Code" class="footer-qr-image" loading="lazy">
        <div class="footer-qr-caption">UPI ID: <span>globalrise@kotak</span></div>
      </div>
    </div>

    <!-- Column 2: About Us Nav Links -->
    <div class="footer-nav-col">
      <h3>About Us</h3>
      <ul class="footer-links-list">
        <li><a href="#" class="footer-link">About Us</a></li>
        <li><a href="#" class="footer-link">Our Vision and Mission</a></li>
        <li><a href="#" class="footer-link">Inspiration - The story of Hope</a></li>
        <li><a href="#" class="footer-link">Board of Trustees</a></li>
        <li><a href="#" class="footer-link">Tax exemption</a></li>
        <li><a href="#" class="footer-link">donation faqs</a></li>
        <li><a href="#" class="footer-link">Term's and Conditions</a></li>
      </ul>
    </div>

    <!-- Column 3: Our Work Nav Links -->
    <div class="footer-nav-col">
      <h3>Our Work</h3>
      <ul class="footer-links-list">
        <li><a href="#" class="footer-link">Our Work</a></li>
        <li><a href="#" class="footer-link">Feeding For Education</a></li>
        <li><a href="#" class="footer-link">Mid-Day Meal</a></li>
        <li><a href="#" class="footer-link">Relief feeding</a></li>
        <li><a href="#" class="footer-link">Research & Advocacy</a></li>
        <li><a href="#" class="footer-link">Beyond Meals</a></li>
      </ul>
    </div>

    <!-- Column 4: Donate, Get in Touch & Get Involved -->
    <div class="footer-nav-col">
      <h3>Donate</h3>
      <ul class="footer-links-list">
        <li><a href="<?php echo $footer_base_url; ?>donate.php" class="footer-link">Online donations</a></li>
        <li><a href="<?php echo $footer_base_url; ?>donate.php" class="footer-link">Sponsor a School</a></li>
        <li><a href="<?php echo $footer_base_url; ?>donate.php" class="footer-link">Sponsor a kitchen</a></li>
      </ul>

      <h3 class="sub-heading">Get in Touch</h3>
      <ul class="footer-links-list">
        <li><a href="#" class="footer-link">Contact Us</a></li>
      </ul>

      <h3 class="sub-heading">Get Involved</h3>
      <ul class="footer-links-list">
        <li><a href="#" class="footer-link">Future shaper</a></li>
      </ul>
    </div>

    <!-- Column 5: Certifications -->
    <div class="footer-certifications">
      <img src="<?php echo $footer_base_url; ?>assets/GPTW_Logo.png" alt="Great Place to Work Certified" class="gptw-logo-img" loading="lazy">
    </div>

  </div>

  <!-- Copyright and Charity Info Bar -->
  <div class="footer-info-bar">
    <div class="header-container">
      <span>Charity Id : AAATT6468P</span>
      <span>The Global Rise Foundation &copy; 2026</span>
    </div>
  </div>

  <!-- Silhouette Illustration strip (full-width bottom) -->
  <div class="footer-silhouette" style="background-image: url('<?php echo $footer_base_url; ?>assets/footer_bg.png');">
    <!-- Floating WhatsApp Contact Button -->
    <a href="https://wa.me/9172920007777" class="whatsapp-float-btn" target="_blank" rel="noopener noreferrer" aria-label="Chat with The Global Rise Foundation on WhatsApp">
      <i class="fa-brands fa-whatsapp"></i>
    </a>
  </div>
</footer>
