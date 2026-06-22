<?php
/**
 * admin/logout.php
 * ============================================================
 * Admin Sign-Out Handler
 * The Global Rise Foundation
 * ============================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables
$_SESSION = [];

// Destroy session cookies if they exist
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect back to login with a logout success parameter
header('Location: ../admin-login.php?logout=1');
exit;
