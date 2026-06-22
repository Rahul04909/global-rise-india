<?php
/**
 * sql/insert-admin.php
 * ============================================================
 * Database Migrator / Admin Seeding Script
 * The Global Rise Foundation
 *
 * Usage: Run via CLI (php sql/insert-admin.php) or access via browser.
 * ============================================================
 */

// ── Load config (includes autoloader, environment loader, and getDB) ──
$configPath = __DIR__ . '/../includes/config.php';
if (!file_exists($configPath)) {
    die("Error: config.php not found at: " . realpath($configPath) . "\n");
}
require_once $configPath;

$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Seeder - The Global Rise Foundation</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: "Montserrat", sans-serif; background: #f4f6f9; color: #333; padding: 40px 20px; margin: 0; }
            .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
            h2 { color: #1b5182; border-bottom: 2px solid #eef2f6; padding-bottom: 10px; margin-top: 0; }
            .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; line-height: 1.5; }
            .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
            .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
            .details { background: #f8f9fa; border: 1px solid #e9ecef; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 13px; line-height: 1.6; white-space: pre-wrap; }
        </style>
    </head>
    <body>
    <div class="container">';
}

try {
    // 1. Connect to MySQL server first without selecting DB to create it if it doesn't exist
    $dsn_no_db = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT);
    $pdo_init = new PDO($dsn_no_db, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Create the database if it doesn't exist
    $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $db_status = "SUCCESS: Database '" . DB_NAME . "' is verified/created.\n";

    // 2. Now use regular getDB() to connect to the database
    $pdo = getDB();

    // 3. Create Admins Table
    $createTableSql = "
        CREATE TABLE IF NOT EXISTS `admins` (
            `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name`          VARCHAR(120)     NOT NULL,
            `email`         VARCHAR(180)     NOT NULL UNIQUE,
            `username`      VARCHAR(60)      NOT NULL UNIQUE,
            `mobile`        VARCHAR(15)      NOT NULL DEFAULT '',
            `profile_image` VARCHAR(255)     NOT NULL DEFAULT '',
            `password`      VARCHAR(255)     NOT NULL,
            `last_login`    TIMESTAMP        NULL,
            `created_at`    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_username` (`username`),
            INDEX `idx_email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($createTableSql);
    $table_status = "SUCCESS: 'admins' table is ready in '" . DB_NAME . "'.\n";

    // 4. Check if admin already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `admins` WHERE `username` = :username OR `email` = :email");
    $stmt->execute([
        ':username' => 'tgrf_admin',
        ':email'    => 'admin@globalrisefoundation.org'
    ]);
    $exists = (int)$stmt->fetchColumn() > 0;

    $seed_status = "";
    if (!$exists) {
        // Insert default admin
        $defaultPassword = 'Admin@TGRF2026!';
        $hashedPassword  = password_hash($defaultPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $insertStmt = $pdo->prepare("
            INSERT INTO `admins` (name, email, username, mobile, profile_image, password)
            VALUES (:name, :email, :username, :mobile, :profile_image, :password)
        ");

        $insertStmt->execute([
            ':name'          => 'The Global Rise Foundation',
            ':email'         => 'admin@globalrisefoundation.org',
            ':username'      => 'tgrf_admin',
            ':mobile'        => '9876543210',
            ':profile_image' => '', // Empty means using standard avatar fallback
            ':password'      => $hashedPassword
        ]);

        $seed_status = "SUCCESS: Seeded default admin user:\n"
                     . "  - Name: The Global Rise Foundation\n"
                     . "  - Email: admin@globalrisefoundation.org\n"
                     . "  - Username: tgrf_admin\n"
                     . "  - Password: Admin@TGRF2026!\n"
                     . "(Remember to update credentials immediately in the admin profile settings page.)\n";
    } else {
        $seed_status = "NOTICE: Default admin already exists. Seeding skipped to prevent overwriting existing admin accounts.\n";
    }

    // Success response styling
    if ($is_cli) {
        echo "=== TGRF Admin Seeder ===\n";
        echo $db_status;
        echo $table_status;
        echo $seed_status;
        echo "=========================\n";
    } else {
        echo '<h2>Migration Status</h2>';
        echo '<div class="alert alert-success">Database migrated and seeded successfully!</div>';
        echo '<div class="details">' . htmlspecialchars($db_status . $table_status . $seed_status) . '</div>';
    }

} catch (PDOException $e) {
    $err_msg = "FAILURE: Could not complete migration/seeding.\nReason: " . $e->getMessage() . "\n";
    if ($is_cli) {
        echo $err_msg;
    } else {
        echo '<h2>Migration Status</h2>';
        echo '<div class="alert alert-error">Database migration failed!</div>';
        echo '<div class="details">' . htmlspecialchars($err_msg) . '</div>';
    }
}

if (!$is_cli) {
    echo '</div></body></html>';
}
