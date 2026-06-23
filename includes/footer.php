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

      <!-- Bank Details Card Box -->
      <div class="footer-bank-card">
        <div class="footer-bank-title"><i class="fa-solid fa-building-columns"></i> Bank Account Details</div>
        <div class="footer-bank-details">
          <div><span>A/C Name:</span> The Global Rise Foundation Trust</div>
          <div><span>Bank Name:</span> Axis Bank Of India</div>
          <div><span>A/C No:</span> 926020024782390</div>
          <div><span>IFSC Code:</span> UTIB0000348</div>
          <div><span>Branch:</span> NIT Faridabad</div>
          <div><span>A/C Type:</span> Current Account</div>
        </div>
      </div>
    </div>

    <!-- Column 2: About Us Nav Links -->
    <div class="footer-nav-col">
      <h3>About Us</h3>
      <ul class="footer-links-list">
        <li><a href="<?php echo $footer_base_url; ?>pages/about-us.php" class="footer-link">About Us</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/vision-mission.php" class="footer-link">Our Vision and Mission</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/inspiration.php" class="footer-link">Inspiration - The Story of Hope</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/board-trustees.php" class="footer-link">Board of Trustees</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/tax-exemption.php" class="footer-link">Tax Exemption</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/donation-faqs.php" class="footer-link">Donation FAQs</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/terms-conditions.php" class="footer-link">Terms and Conditions</a></li>
      </ul>
    </div>

    <!-- Column 3: Our Work Nav Links -->
    <div class="footer-nav-col">
      <h3>Our Work</h3>
      <ul class="footer-links-list">
        <li><a href="<?php echo $footer_base_url; ?>pages/animal-welfare.php" class="footer-link">Animal Welfare</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/disaster-management.php" class="footer-link">Disaster Management</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/educating-slum-children.php" class="footer-link">Educating Slum Children</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/health-projects.php" class="footer-link">Health Projects</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/persons-with-disabilities.php" class="footer-link">Persons with Disabilities</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/rural-children-education.php" class="footer-link">Rural Children Education</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/senior-citizen-care.php" class="footer-link">Senior Citizen Care</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/swacch-bharat-mission.php" class="footer-link">Swacch Bharat Mission</a></li>
        <li><a href="<?php echo $footer_base_url; ?>pages/women-empowerment.php" class="footer-link">Women Empowerment</a></li>
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
      <span>The Global Rise Foundation &copy; 2026 | A Website Designed By <a href="https://www.mineib.com" target="_blank" style="color: #666666; text-decoration: none; font-weight: 600;" onmouseover="this.style.color='#1b5182'" onmouseout="this.style.color='#666666'">Mineib Creative Technology</a></span>
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
