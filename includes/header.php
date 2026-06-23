<?php
// Calculate relative base url path dynamically to support inclusion in nested directories
$base_url = '';
$request_uri = $_SERVER['REQUEST_URI'];
$project_name = 'gloabl-rise';
$pos = strpos($request_uri, $project_name);
if ($pos !== false) {
    $base_url = substr($request_uri, 0, $pos + strlen($project_name)) . '/';
} else {
    $base_url = '/';
}
?>
<!-- Header Component -->
<header class="site-header">
  <!-- 1. TOP UTILITY BAR (Desktop only, responsive hidden) -->
  <div class="top-bar">
    <div class="header-container">
      <a href="mailto:info@globalrisefoundation.org" class="top-bar-item">
        <i class="fa-solid fa-envelope"></i>
        info@globalrisefoundation.com
      </a>
      <a href="tel:+9172920007777" class="top-bar-item">
        <i class="fa-solid fa-phone"></i>
        +91 729200-07777
      </a>
    </div>
  </div>

  <!-- 2. MAIN NAVIGATION BAR -->
  <div class="main-header">
    <div class="header-container main-nav-bar">
      <!-- Logo Image -->
      <a href="<?php echo $base_url; ?>index.php" class="logo-link" aria-label="The Global Rise Foundation Home">
        <img src="<?php echo $base_url; ?>assets/logo.png" alt="The Global Rise Foundation Logo" class="logo-img">
      </a>

      <!-- Desktop Nav Links -->
      <ul class="nav-menu">
        <li><a href="<?php echo $base_url; ?>pages/about-us.php" class="nav-link">Who We Are</a></li>
        <li><a href="<?php echo $base_url; ?>pages/what-we-do.php" class="nav-link">What We Do</a></li>
        <li><a href="<?php echo $base_url; ?>pages/news-stories.php" class="nav-link">News & Stories</a></li>
        <li><a href="<?php echo $base_url; ?>pages/how-to-help.php" class="nav-link">How To Help</a></li>
        <li><a href="<?php echo $base_url; ?>volunteer.php" class="nav-link">Get Involved</a></li>
        <li><a href="<?php echo $base_url; ?>pages/impact-reports.php" class="nav-link">Impact Reports</a></li>
        <li><a href="<?php echo $base_url; ?>pages/blogs.php" class="nav-link">Blogs</a></li>
      </ul>

      <!-- Desktop Action Buttons -->
      <div class="nav-actions">
        <a href="<?php echo $base_url; ?>donate.php" class="btn-header btn-donate">Donate Now</a>
        <a href="<?php echo $base_url; ?>volunteer.php" class="btn-header btn-login">Volunteer</a>
      </div>

      <!-- Hamburger Menu (Mobile/Tablet Toggle) -->
      <button class="hamburger" id="hamburgerBtn" aria-label="Toggle Navigation Menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>

  <!-- 3. MOBILE NAVIGATION DRAWER -->
  <div class="drawer-overlay" id="drawerOverlay"></div>
  <div class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-header">
      <!-- Mobile Drawer Logo -->
      <a href="<?php echo $base_url; ?>index.php" class="logo-link" aria-label="The Global Rise Foundation Home">
        <img src="<?php echo $base_url; ?>assets/logo.png" alt="The Global Rise Foundation Logo" class="logo-img mobile-logo-img">
      </a>
      <button class="drawer-close-btn" id="closeDrawerBtn" aria-label="Close Menu">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- Mobile Nav Links -->
    <ul class="mobile-nav-links">
      <li><a href="<?php echo $base_url; ?>pages/about-us.php" class="mobile-nav-link">Who We Are</a></li>
      <li><a href="<?php echo $base_url; ?>pages/what-we-do.php" class="mobile-nav-link">What We Do</a></li>
      <li><a href="<?php echo $base_url; ?>pages/news-stories.php" class="mobile-nav-link">News & Stories</a></li>
      <li><a href="<?php echo $base_url; ?>pages/how-to-help.php" class="mobile-nav-link">How To Help</a></li>
      <li><a href="<?php echo $base_url; ?>volunteer.php" class="mobile-nav-link">Get Involved</a></li>
      <li><a href="<?php echo $base_url; ?>pages/impact-reports.php" class="mobile-nav-link">Impact Reports</a></li>
      <li><a href="<?php echo $base_url; ?>pages/blogs.php" class="mobile-nav-link">Blogs</a></li>
    </ul>

    <!-- Mobile Action Buttons -->
    <div class="mobile-nav-actions">
      <a href="<?php echo $base_url; ?>donate.php" class="btn-header btn-donate">Donate Now</a>
      <a href="<?php echo $base_url; ?>volunteer.php" class="btn-header btn-login">Volunteer</a>
    </div>

    <div class="mobile-drawer-divider"></div>

    <!-- Mobile Utilities (Top Bar items merged here) -->
    <div class="mobile-utilities">
      <a href="mailto:info@globalrisefoundation.com" class="mobile-utility-item">
        <i class="fa-solid fa-envelope"></i>
        info@globalrisefoundation.com
      </a>
      <a href="tel:+9172920007777" class="mobile-utility-item">
        <i class="fa-solid fa-phone"></i>
        +91 729200-07777
      </a>
    </div>
  </div>
</header>

<!-- JavaScript for Mobile Responsiveness & Drawer Functionality -->
<script>
  // 1. Dynamic Favicon Injection into <head> (ensures favicon is loaded automatically on any page)
  (function() {
    const faviconHref = "<?php echo $base_url; ?>favicon.png";
    let link = document.querySelector("link[rel~='icon']");
    if (!link) {
      link = document.createElement('link');
      link.rel = 'icon';
      link.type = 'image/png';
      document.head.appendChild(link);
    }
    link.href = faviconHref;
  })();

  document.addEventListener('DOMContentLoaded', function() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const drawerOverlay = document.getElementById('drawerOverlay');

    // Function to open drawer
    function openDrawer() {
      mobileDrawer.classList.add('open');
      drawerOverlay.classList.add('active');
      document.body.classList.add('drawer-open');
    }

    // Function to close drawer
    function closeDrawer() {
      mobileDrawer.classList.remove('open');
      drawerOverlay.classList.remove('active');
      document.body.classList.remove('drawer-open');
    }

    // Event listeners
    if (hamburgerBtn) hamburgerBtn.addEventListener('click', openDrawer);
    if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
    if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);

  });
</script>
