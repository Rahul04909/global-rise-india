<?php
/**
 * admin/includes/auth-check.php
 * ============================================================
 * Admin Authentication Middleware
 * The Global Rise Foundation
 *
 * Starts/verifies session, redirects to login if unauthorized,
 * and fetches the current admin's DB record into `$current_admin`.
 * ============================================================
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session cookie parameters if HTTPS is active
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    session_start([
        'cookie_lifetime' => 86400, // 24 hours
        'cookie_secure'   => $secure,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Redirect to login if admin_id is not set in the session
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin-login.php');
    exit;
}

// Load root-level application config
require_once __DIR__ . '/../../includes/config.php';

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, name, email, username, mobile, profile_image FROM admins WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $_SESSION['admin_id']]);
    $current_admin = $stmt->fetch();

    // If admin record is not found in database (e.g. deleted/seeder run again), logout immediately
    if (!$current_admin) {
        session_unset();
        session_destroy();
        header('Location: ../admin-login.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('[Admin Auth Middleware] Database Exception: ' . $e->getMessage());
    if (APP_DEBUG) {
        die('Database connection error in authentication check: ' . $e->getMessage());
    } else {
        die('A server error occurred. Please refresh the page or try again later.');
    }
}
