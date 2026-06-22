<?php
/**
 * admin-login.php
 * ============================================================
 * Administrator Authentication Portal
 * The Global Rise Foundation
 *
 * Provides a secure login interface for administrators.
 * Features CSRF protection, secure session creation, and BCrypt validation.
 * ============================================================
 */

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    session_start([
        'cookie_lifetime' => 86400, // 24 hours
        'cookie_secure'   => $secure,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// If already logged in, redirect directly to admin dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/index.php');
    exit;
}

// Load configurations & DB helper
require_once __DIR__ . '/includes/config.php';

// Generate CSRF token if not set
if (empty($_SESSION['login_csrf_token'])) {
    $_SESSION['login_csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';

// Handle logout message from query parameters
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $success = 'You have been successfully logged out.';
}

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password          = $_POST['password']          ?? '';
    $csrf_token        = $_POST['csrf_token']        ?? '';

    // Validate CSRF token
    if (!hash_equals($_SESSION['login_csrf_token'], $csrf_token)) {
        $error = 'Invalid request verification (CSRF mismatch). Please try again.';
    } elseif (empty($username_or_email) || empty($password)) {
        $error = 'Please enter both username/email and password.';
    } else {
        try {
            $pdo = getDB();

            // Find administrator by username or email
            $stmt = $pdo->prepare("SELECT * FROM `admins` WHERE `username` = :username OR `email` = :email LIMIT 1");
            $stmt->execute([
                ':username' => $username_or_email,
                ':email'    => $username_or_email
            ]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Regenerate session id to protect against session fixation
                session_regenerate_id(true);

                // Store credentials in session
                $_SESSION['admin_id']       = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name']     = $admin['name'];

                // Update last login timestamp
                $updateStmt = $pdo->prepare("UPDATE `admins` SET `last_login` = CURRENT_TIMESTAMP WHERE `id` = :id");
                $updateStmt->execute([':id' => $admin['id']]);

                // Clear CSRF token upon successful login
                unset($_SESSION['login_csrf_token']);

                // Redirect to dashboard
                header('Location: admin/index.php');
                exit;
            } else {
                $error = 'Invalid username/email or password.';
            }
        } catch (PDOException $e) {
            error_log('[Admin Login Error] Exception: ' . $e->getMessage());
            $error = APP_DEBUG ? 'Database Error: ' . $e->getMessage() : 'An internal database error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - The Global Rise Foundation</title>
    <link rel="icon" href="favicon.png" type="image/png">
    
    <!-- CSS & Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin-login.css" rel="stylesheet">
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <div class="logo-wrapper">
                <img src="assets/logo.png" alt="TGRF Logo">
            </div>
            <h1>The Global Rise Foundation</h1>
            <p>Admin Portal Login</p>
        </div>

        <div class="login-body">
            <!-- Alert Display -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <form action="admin-login.php" method="POST" autocomplete="off">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['login_csrf_token']) ?>">

                <!-- Username/Email Field -->
                <div class="form-group">
                    <label for="username_or_email" class="form-label">Username or Email</label>
                    <div class="input-wrapper">
                        <input type="text" 
                               name="username_or_email" 
                               id="username_or_email" 
                               class="form-control" 
                               placeholder="Enter your username or email"
                               value="<?= htmlspecialchars($username_or_email ?? '') ?>" 
                               required 
                               autofocus>
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control" 
                               placeholder="Enter your secure password" 
                               required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password placeholders -->
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Remember Me
                    </label>
                    <a href="javascript:void(0);" onclick="alert('Please contact the main server administrator to retrieve or reset your credentials.');" class="forgot-password">Forgot Password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login">
                    <span>Sign In</span>
                    <i class="fas fa-sign-in-alt"></i>
                </button>
            </form>

            <div class="login-footer">
                <span>Go back to </span>
                <a href="index.php" class="back-link">TGRF Main Site</a>
            </div>
        </div>
    </div>

</body>
</html>
