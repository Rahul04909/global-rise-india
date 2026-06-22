<?php
/**
 * includes/config.php
 * ============================================================
 * Central Bootstrap File — The Global Rise Foundation
 *
 * Loads environment variables from .env via vlucas/phpdotenv,
 * then exposes a set of named constants for:
 *   - Database (DB_*)
 *   - SMTP Mail (SMTP_*)
 *   - Razorpay (RZP_*)
 *   - App-level settings (APP_*)
 *
 * Usage: require_once __DIR__ . '/config.php';
 * ============================================================
 */

// ── Autoload Composer packages ──────────────────────────────
$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    http_response_code(500);
    die('Fatal: Composer autoload not found. Run `composer install` in the project root.');
}
require_once $autoload;

// ── Load .env file ───────────────────────────────────────────
use Dotenv\Dotenv;

$envPath = __DIR__ . '/..';   // Project root (one level above includes/)
if (file_exists($envPath . '/.env')) {
    $dotenv = Dotenv::createImmutable($envPath);
    $dotenv->load();

    // Define required keys — will throw an exception if missing
    $dotenv->required([
        'DB_HOST', 'DB_NAME', 'DB_USER',
        'SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_PASS',
        'RAZORPAY_KEY_ID', 'RAZORPAY_KEY_SECRET',
    ])->notEmpty();
}

// ── Application ──────────────────────────────────────────────
define('APP_NAME',    $_ENV['APP_NAME']  ?? 'The Global Rise Foundation');
define('APP_ENV',     $_ENV['APP_ENV']   ?? 'production');
define('APP_URL',     $_ENV['APP_URL']   ?? '');
define('APP_DEBUG',   filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));

// ── Database ─────────────────────────────────────────────────
define('DB_HOST',     $_ENV['DB_HOST']   ?? '127.0.0.1');
define('DB_PORT',     $_ENV['DB_PORT']   ?? '3306');
define('DB_NAME',     $_ENV['DB_NAME']   ?? '');
define('DB_USER',     $_ENV['DB_USER']   ?? 'root');
define('DB_PASS',     $_ENV['DB_PASS']   ?? '');

// ── SMTP ─────────────────────────────────────────────────────
define('SMTP_HOST',       $_ENV['SMTP_HOST']       ?? 'smtp.gmail.com');
define('SMTP_PORT',       (int)($_ENV['SMTP_PORT'] ?? 587));
define('SMTP_SECURE',     $_ENV['SMTP_SECURE']     ?? 'tls');
define('SMTP_USER',       $_ENV['SMTP_USER']       ?? '');
define('SMTP_PASS',       $_ENV['SMTP_PASS']       ?? '');
define('SMTP_FROM_NAME',  $_ENV['SMTP_FROM_NAME']  ?? APP_NAME);
define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL'] ?? $_ENV['SMTP_USER'] ?? '');

// ── Razorpay ─────────────────────────────────────────────────
define('RZP_KEY_ID',      $_ENV['RAZORPAY_KEY_ID']      ?? '');
define('RZP_KEY_SECRET',  $_ENV['RAZORPAY_KEY_SECRET']  ?? '');
define('RZP_CURRENCY',    $_ENV['RAZORPAY_CURRENCY']    ?? 'INR');
define('RZP_COMPANY',     $_ENV['RAZORPAY_COMPANY_NAME'] ?? APP_NAME);

// ── Admin ────────────────────────────────────────────────────
define('ADMIN_EMAIL',     $_ENV['ADMIN_EMAIL'] ?? SMTP_FROM_EMAIL);

// ── Error Reporting (based on environment) ────────────────────
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ── Database Helper: get a singleton PDO connection ──────────
function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST, DB_PORT, DB_NAME);
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                throw $e;
            }
            http_response_code(500);
            die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
        }
    }
    return $pdo;
}
