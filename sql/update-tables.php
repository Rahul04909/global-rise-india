<?php
/**
 * sql/update-tables.php
 * ============================================================
 * Database Migrator / Volunteer System Seeding Script
 * The Global Rise Foundation
 *
 * Usage: Run via CLI (php sql/update-tables.php) or access via browser.
 * ============================================================
 */

// ── Load config (includes autoloader, environment loader, and getDB) ──
$configPath = __DIR__ . '/../includes/config.php';
if (!file_exists($configPath)) {
    die("Error: config.php not found. Run from root or update paths.\n");
}
require_once $configPath;

$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Updates - The Global Rise Foundation</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: "Montserrat", sans-serif; background: #f4f6f9; color: #333; padding: 40px 20px; margin: 0; }
            .container { max-width: 700px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
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
    $pdo = getDB();
    $report = "";

    // 1. Create volunteer_plans table
    $plansTableSql = "
        CREATE TABLE IF NOT EXISTS `volunteer_plans` (
            `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `title`         VARCHAR(100)     NOT NULL,
            `price`         DECIMAL(10,2)    NOT NULL,
            `duration_value` INT             NOT NULL,
            `duration_unit` ENUM('month', 'year', 'lifetime', 'onetime') NOT NULL,
            `description`   TEXT             NULL,
            `status`        ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
            `created_at`    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($plansTableSql);
    $report .= "SUCCESS: 'volunteer_plans' table is verified/created.\n";

    // 2. Seed Default Plans if empty
    $countStmt = $pdo->query("SELECT COUNT(*) FROM `volunteer_plans`");
    $plansCount = (int)$countStmt->fetchColumn();

    if ($plansCount === 0) {
        $defaultPlans = [
            [
                'title'          => '1 Month Support Plan',
                'price'          => 500.00,
                'duration_value' => 1,
                'duration_unit'  => 'month',
                'description'    => 'Uplift a child\'s education and basic needs for one month.',
                'status'         => 'active'
            ],
            [
                'title'          => '3 Months Support Plan',
                'price'          => 1200.00,
                'duration_value' => 3,
                'duration_unit'  => 'month',
                'description'    => 'Provide education, food and health resources for three months.',
                'status'         => 'active'
            ],
            [
                'title'          => '1 Year Support Plan',
                'price'          => 4000.00,
                'duration_value' => 1,
                'duration_unit'  => 'year',
                'description'    => 'Sponsor comprehensive development support for an entire year.',
                'status'         => 'active'
            ],
            [
                'title'          => 'Lifetime Support Member',
                'price'          => 15000.00,
                'duration_value' => 1,
                'duration_unit'  => 'lifetime',
                'description'    => 'Become a lifetime support pillar for The Global Rise Foundation.',
                'status'         => 'active'
            ],
            [
                'title'          => 'One-Time Support Plan',
                'price'          => 1000.00,
                'duration_value' => 1,
                'duration_unit'  => 'onetime',
                'description'    => 'Make a single-time impact donation to help support our on-ground efforts.',
                'status'         => 'active'
            ]
        ];

        $insertStmt = $pdo->prepare("
            INSERT INTO `volunteer_plans` (title, price, duration_value, duration_unit, description, status)
            VALUES (:title, :price, :duration_value, :duration_unit, :description, :status)
        ");

        foreach ($defaultPlans as $plan) {
            $insertStmt->execute($plan);
        }
        $report .= "SUCCESS: Seeded 5 default volunteer support plans.\n";
    } else {
        $report .= "NOTICE: Plans already exist in 'volunteer_plans'. Seeding skipped.\n";
    }

    // 3. Update 'volunteers' table columns to support plans & Razorpay payments
    // First check if volunteers table exists. If not, submit-volunteer API will create it, but let's make sure it is created now so we can alter it.
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `volunteers` (
            `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `full_name`        VARCHAR(120)     NOT NULL,
            `email`            VARCHAR(180)     NOT NULL,
            `phone`            VARCHAR(15)      NOT NULL,
            `dob`              DATE             NULL,
            `gender`           VARCHAR(20)      NOT NULL,
            `city`             VARCHAR(80)      NOT NULL,
            `state`            VARCHAR(80)      NOT NULL,
            `pincode`          VARCHAR(10)      NOT NULL DEFAULT '',
            `education`        VARCHAR(100)     NOT NULL DEFAULT '',
            `occupation`       VARCHAR(100)     NOT NULL DEFAULT '',
            `area_of_interest` VARCHAR(120)     NOT NULL,
            `availability`     VARCHAR(50)      NOT NULL,
            `hours_per_week`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `motivation`       TEXT             NOT NULL,
            `skills`           TEXT             NOT NULL DEFAULT '',
            `prior_experience` ENUM('yes','no') NOT NULL DEFAULT 'no',
            `emergency_name`   VARCHAR(120)     NOT NULL DEFAULT '',
            `emergency_phone`  VARCHAR(15)      NOT NULL DEFAULT '',
            `status`           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
            `created_at`       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_email` (`email`),
            INDEX `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Add fields if they do not exist
    $columnsToCheck = [
        'plan_id'             => "ALTER TABLE `volunteers` ADD COLUMN `plan_id` INT UNSIGNED DEFAULT NULL AFTER `emergency_phone`",
        'payment_status'      => "ALTER TABLE `volunteers` ADD COLUMN `payment_status` ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending' AFTER `plan_id`",
        'razorpay_payment_id' => "ALTER TABLE `volunteers` ADD COLUMN `razorpay_payment_id` VARCHAR(100) DEFAULT NULL AFTER `payment_status`",
        'razorpay_order_id'   => "ALTER TABLE `volunteers` ADD COLUMN `razorpay_order_id` VARCHAR(100) DEFAULT NULL AFTER `razorpay_payment_id`",
        'razorpay_signature'  => "ALTER TABLE `volunteers` ADD COLUMN `razorpay_signature` VARCHAR(255) DEFAULT NULL AFTER `razorpay_order_id`",
        'amount_paid'         => "ALTER TABLE `volunteers` ADD COLUMN `amount_paid` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `razorpay_signature`"
    ];

    foreach ($columnsToCheck as $column => $alterSql) {
        $chkStmt = $pdo->query("SHOW COLUMNS FROM `volunteers` LIKE '$column'");
        $colExists = $chkStmt->fetch();
        if (!$colExists) {
            $pdo->exec($alterSql);
            $report .= "SUCCESS: Added column '$column' to 'volunteers' table.\n";
        } else {
            $report .= "NOTICE: Column '$column' already exists in 'volunteers' table.\n";
        }
    }

    // 4. Create donations table
    $donationsTableSql = "
        CREATE TABLE IF NOT EXISTS `donations` (
            `id`                  INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `donor_title`         VARCHAR(10)      NOT NULL DEFAULT 'Mr.',
            `donor_name`          VARCHAR(120)     NOT NULL,
            `donor_email`         VARCHAR(180)     NOT NULL,
            `donor_mobile`        VARCHAR(15)      NOT NULL,
            `donor_dob`           DATE             NOT NULL,
            `donor_alt_mobile`    VARCHAR(15)      NULL,
            `citizenship`         ENUM('indian', 'nri', 'foreign') NOT NULL DEFAULT 'indian',
            `donation_type`       ENUM('once', 'monthly') NOT NULL DEFAULT 'once',
            `selected_cause`      VARCHAR(50)      NOT NULL DEFAULT 'feed',
            `amount`              DECIMAL(10,2)    NOT NULL,
            `request_80g`         TINYINT(1)       NOT NULL DEFAULT 0,
            `donor_pan`           VARCHAR(10)      NULL,
            `donor_address`       TEXT             NULL,
            `payment_status`      ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
            `razorpay_order_id`   VARCHAR(100)     NULL,
            `razorpay_payment_id` VARCHAR(100)     NULL,
            `razorpay_signature`  VARCHAR(255)     NULL,
            `created_at`          TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`          TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_donor_email` (`donor_email`),
            INDEX `idx_payment_status` (`payment_status`),
            INDEX `idx_cause` (`selected_cause`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($donationsTableSql);
    $report .= "SUCCESS: 'donations' table is verified/created.\n";

    if ($is_cli) {
        echo "=== TGRF DB Update Seeder ===\n";
        echo $report;
        echo "=============================\n";
    } else {
        echo '<h2>Migration Status</h2>';
        echo '<div class="alert alert-success">Database structure updated successfully!</div>';
        echo '<div class="details">' . htmlspecialchars($report) . '</div>';
    }

} catch (PDOException $e) {
    $err_msg = "FAILURE: Could not complete DB updates.\nReason: " . $e->getMessage() . "\n";
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
