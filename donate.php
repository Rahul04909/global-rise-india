<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Online Donations - The Global Rise Foundation</title>
  
  <!-- FontAwesome 6 CDN for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Custom Header Stylesheet -->
  <link rel="stylesheet" href="assets/css/header.css">
  
  <!-- Custom Donation Stylesheet -->
  <link rel="stylesheet" href="assets/css/donate.css">
  
  <!-- Custom Footer Stylesheet -->
  <link rel="stylesheet" href="assets/css/footer.css">
  
  <style>
    /* Reset and general page styling */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background-color: #f7fafc;
      color: #2d3748;
      font-family: 'Montserrat', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
  </style>
</head>
<body>

  <!-- Include Header Component -->
  <?php include 'includes/header.php'; ?>

  <!-- Breadcrumb Navigation Banner -->
  <div class="donate-breadcrumb-banner">
    <div class="header-container">
      <nav class="donate-breadcrumb" aria-label="Breadcrumb">
        <a href="index.php">Home</a>
        <span class="donate-breadcrumb-separator">></span>
        <span>Online Donations</span>
      </nav>
      <h1 class="donate-page-title">Online Donations</h1>
    </div>
  </div>

  <!-- Include Custom Online Donations Form Component -->
  <?php include 'components/donate-form.php'; ?>

  <!-- Include Footer Component -->
  <?php include 'includes/footer.php'; ?>

</body>
</html>
