<?php
/**
 * sql/create-articles-table.php
 * ============================================================
 * Database Migrator / Articles Table Creation & Seeding Script
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/../includes/config.php';

$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Setup - Articles Table</title>
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

    // 1. Create articles table
    $articlesTableSql = "
        CREATE TABLE IF NOT EXISTS `articles` (
            `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `type`             ENUM('news', 'blog') NOT NULL,
            `title`            VARCHAR(255)     NOT NULL,
            `slug`             VARCHAR(255)     NOT NULL UNIQUE,
            `image`            VARCHAR(255)     NULL,
            `description`      LONGTEXT         NOT NULL,
            `meta_title`       VARCHAR(255)     NULL,
            `meta_description` TEXT             NULL,
            `meta_keywords`    VARCHAR(255)     NULL,
            `schema_json`      TEXT             NULL,
            `status`           ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
            `created_at`       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($articlesTableSql);
    $report .= "SUCCESS: 'articles' table is verified/created.\n";

    // 2. Seed default articles if empty
    $countStmt = $pdo->query("SELECT COUNT(*) FROM `articles`");
    $articlesCount = (int)$countStmt->fetchColumn();

    if ($articlesCount === 0) {
        $defaultArticles = [
            // --- News ---
            [
                'type'             => 'news',
                'title'            => 'New Centralized Mid Day Meal Kitchen Launched in Bangalore',
                'slug'             => 'new-centralized-mid-day-meal-kitchen-launched-in-bangalore',
                'image'            => 'assets/images/slide1.png',
                'description'      => '<p>To support more government school children and eradicate classroom hunger, we have inaugurated our new fully automated central kitchen in North Bangalore. Spanning 5,000 sq ft, this kitchen will prepare hot, nutritional meals for over 5,000 students daily under absolute hygiene standards.</p><p>Equipped with modern steam-cooking boilers, heavy vegetable cutters, and insulated delivery vehicles, the facility ensures that food is prepared cleanly and delivered piping hot to local municipal schools within a 15km radius.</p>',
                'meta_title'       => 'New Centralized Mid Day Meal Kitchen Bangalore | The Global Rise Foundation',
                'meta_description' => 'The Global Rise Foundation launches a new centralized kitchen in Bangalore to feed 5,000+ government school children daily under clean hygiene standards.',
                'meta_keywords'    => 'mid day meal, central kitchen bangalore, charity kitchen, child hunger india',
                'schema_json'      => '{"@context":"https://schema.org","@type":"NewsArticle","headline":"New Centralized Mid Day Meal Kitchen Launched in Bangalore","image":["https://globalrisefoundation.org/assets/images/slide1.png"],"datePublished":"2026-06-15T00:00:00Z","author":{"@type":"Organization","name":"The Global Rise Foundation"}}',
                'status'           => 'published'
            ],
            [
                'type'             => 'news',
                'title'            => 'Stitch by Stitch: How Anita Achieved Livelihood Independence',
                'slug'             => 'stitch-by-stitch-how-anita-achieved-livelihood-independence',
                'image'            => 'assets/images/women_empowerment.png',
                'description'      => '<p>Anita, a resident of an urban slum cluster, was struggle-ridden as the sole breadwinner for a family of four. After enrolling in our 6-month Vocational Tailoring program, she received a free sewing machine and entrepreneurial guidance.</p><p>Today, Anita runs a home-based boutique, earning a stable livelihood, designing garments for local customers, and funding her children\'s schooling. Her journey stands as a testament to how skills training combined with startup aid breaks generational poverty cycles.</p>',
                'meta_title'       => 'Success Story: How Anita Achieved Financial Independence | TGRF',
                'meta_description' => 'Read how Anita, a tailoring graduate of The Global Rise Foundation, built her home-based boutique and achieved stable livelihood independence.',
                'meta_keywords'    => 'women empowerment, success story, vocational tailoring, skill training india',
                'schema_json'      => '{"@context":"https://schema.org","@type":"NewsArticle","headline":"Stitch by Stitch: How Anita Achieved Livelihood Independence","image":["https://globalrisefoundation.org/assets/images/women_empowerment.png"],"datePublished":"2026-05-28T00:00:00Z","author":{"@type":"Organization","name":"The Global Rise Foundation"}}',
                'status'           => 'published'
            ],
            [
                'type'             => 'news',
                'title'            => 'Emergency Flood Relief Kits Dispatched to Rural Communities',
                'slug'             => 'emergency-flood-relief-kits-dispatched-to-rural-communities',
                'image'            => 'assets/images/disaster_relief.png',
                'description' => '<p>In response to the flash floods affecting coastal hamlets, our rapid action force has set up temporary medical camps and successfully distributed 1,200 survival kits containing dry rations, sanitizers, chlorine tablets, and warm blankets to the displaced families.</p><p>Our emergency response team remains stationed in the affected areas, working alongside local authorities to deliver fresh drinking water and prevent waterborne disease outbreaks through localized health awareness camps.</p>',
                'meta_title'       => 'Emergency Flood Relief Dispatched to Coastal Districts | TGRF',
                'meta_description' => 'The Global Rise Foundation distributes over 1,200 emergency relief kits and dry rations to flood-affected coastal communities.',
                'meta_keywords'    => 'flood relief, disaster management, emergency aid, NGO rescue work',
                'schema_json'      => '{"@context":"https://schema.org","@type":"NewsArticle","headline":"Emergency Flood Relief Kits Dispatched to Rural Communities","image":["https://globalrisefoundation.org/assets/images/disaster_relief.png"],"datePublished":"2026-05-12T00:00:00Z","author":{"@type":"Organization","name":"The Global Rise Foundation"}}',
                'status'           => 'published'
            ],
            [
                'type'             => 'news',
                'title'            => 'Digital Learning Tools Introduced in Rural Primary Schools',
                'slug'             => 'digital-learning-tools-introduced-in-rural-primary-schools',
                'image'            => 'assets/images/slide2.png',
                'description'      => '<p>To reduce learning loss and improve digital literacy, we have deployed 50 interactive tablet learning setups across three rural primary schools. These kits feature offline curriculum modules, visual sciences, and basic mathematics software, bringing digital pedagogy to remote corners.</p><p>By exposing rural students to modern technology early, we aim to bridge the digital divide and nurture curiosity-driven learning in underprivileged schools.</p>',
                'meta_title'       => 'Digital Classrooms Deployed in Rural Primary Schools | TGRF',
                'meta_description' => 'Learn how The Global Rise Foundation is introducing interactive tablet setups and offline digital learning tools to remote rural primary schools.',
                'meta_keywords'    => 'digital learning, rural education, NGO school programs, computer literacy',
                'schema_json'      => '{"@context":"https://schema.org","@type":"NewsArticle","headline":"Digital Learning Tools Introduced in Rural Primary Schools","image":["https://globalrisefoundation.org/assets/images/slide2.png"],"datePublished":"2026-04-20T00:00:00Z","author":{"@type":"Organization","name":"The Global Rise Foundation"}}',
                'status'           => 'published'
            ],

            // --- Blogs ---
            [
                'type'             => 'blog',
                'title'            => 'Understanding Classroom Hunger & Learning Outcomes',
                'slug'             => 'understanding-classroom-hunger-learning-outcomes',
                'image'            => 'assets/images/slide3.png',
                'description'      => '<p>Why is nutritional security the most critical predictor of academic performance? We dive deep into school lunch impact statistics in underprivileged communities.</p><p>Studies consistently reveal that a hungry child cannot focus, absorb new concepts, or retain information. Regular, wholesome meals not only improve attendance but also reduce school dropouts and support healthier physical and cognitive development in young children.</p>',
                'meta_title'       => 'Understanding Classroom Hunger and Student Success | TGRF Blog',
                'meta_description' => 'Read our analysis of why nutritional security is the primary catalyst for student retention and cognitive learning outcomes in urban and rural slums.',
                'meta_keywords'    => 'classroom hunger, child nutrition, student attendance, school feeding program',
                'schema_json'      => '{"@context":"https://schema.org","@type":"BlogPosting","headline":"Understanding Classroom Hunger & Learning Outcomes","image":"https://globalrisefoundation.org/assets/images/slide3.png","datePublished":"2026-06-02T00:00:00Z","author":{"@type":"Person","name":"Nutrition Lead"}}',
                'status'           => 'published'
            ],
            [
                'type'             => 'blog',
                'title'            => 'Women Micro-Enterprises: The Power of Sewing',
                'slug'             => 'women-micro-enterprises-the-power-of-sewing',
                'image'            => 'assets/images/women_empowerment.png',
                'description'      => '<p>How a single vocational skill combined with asset distribution (sewing machines) creates structural wealth and breaks generational cycles of poverty.</p><p>By training women in pattern design and tailoring, and supporting them with micro-enterprise guidance, we help them transition into active earning members of their families. Financial agency in the hands of women immediately reflects in better nutrition, education, and health standards for their children.</p>',
                'meta_title'       => 'The Impact of Sewing Machines on Women Empowerment | TGRF Blog',
                'meta_description' => 'Discover how vocational tailoring training and asset grants empower women to start home boutiques, building independent family livelihoods.',
                'meta_keywords'    => 'women entrepreneurship, sewing machines, vocational training, livelihood empowerment',
                'schema_json'      => '{"@context":"https://schema.org","@type":"BlogPosting","headline":"Women Micro-Enterprises: The Power of Sewing","image":"https://globalrisefoundation.org/assets/images/women_empowerment.png","datePublished":"2026-05-18T00:00:00Z","author":{"@type":"Person","name":"Skills Director"}}',
                'status'           => 'published'
            ],
            [
                'type'             => 'blog',
                'title'            => 'Beyond Charity: Sustainable Grassroots Ownership',
                'slug'             => 'beyond-charity-sustainable-grassroots-ownership',
                'image'            => 'assets/images/rural_education.png',
                'description'      => '<p>Looking at how decentralized village committees help maintain study centers, ensuring development remains driven by communities rather than external aid.</p><p>True development happens when the beneficiaries become active stakeholders. By forming local school steering groups, we transfer maintenance responsibilities, fostering deep community pride and ensuring long-term institutional survival.</p>',
                'meta_title'       => 'Sustainable Development Models for Communities | TGRF Blog',
                'meta_description' => 'Explore how decentralized local ownership shifts development projects from temporary charity to long-term community-run institutions.',
                'meta_keywords'    => 'sustainable development, NGO models, local governance, community ownership',
                'schema_json'      => '{"@context":"https://schema.org","@type":"BlogPosting","headline":"Beyond Charity: Sustainable Grassroots Ownership","image":"https://globalrisefoundation.org/assets/images/rural_education.png","datePublished":"2026-05-05T00:00:00Z","author":{"@type":"Person","name":"Strategy Head"}}',
                'status'           => 'published'
            ],
            [
                'type'             => 'blog',
                'title'            => 'Street Animal Welfare: Creating Compassionate Cities',
                'slug'             => 'street-animal-welfare-creating-compassionate-cities',
                'image' => 'assets/images/animal_welfare.png',
                'description'      => '<p>Exploring stray dog feeding networks, emergency first aid, and vaccinations as essential strategies for safe and harmonious urban co-existence.</p><p>Stray animals in urban hubs often suffer from road traffic accidents and acute food shortages. Through organized medical camps, anti-rabies vaccinations, and water bowl installations, we build compassionate community structures that foster safety and harmony.</p>',
                'meta_title'       => 'Building Compassionate Cities for Stray Animals | TGRF Blog',
                'meta_description' => 'How localized stray feeding programs, vaccination camps, and basic veterinary aid foster safe, harmonious urban co-existence.',
                'meta_keywords'    => 'stray dog welfare, animal rescue, NGO veterinary support, urban stray care',
                'schema_json'      => '{"@context":"https://schema.org","@type":"BlogPosting","headline":"Street Animal Welfare: Creating Compassionate Cities","image":"https://globalrisefoundation.org/assets/images/animal_welfare.png","datePublished":"2026-04-24T00:00:00Z","author":{"@type":"Person","name":"Animal Welfare Lead"}}',
                'status'           => 'published'
            ]
        ];

        $insertSql = "
            INSERT INTO `articles` (
                `type`, `title`, `slug`, `image`, `description`, 
                `meta_title`, `meta_description`, `meta_keywords`, `schema_json`, `status`
            ) VALUES (
                :type, :title, :slug, :image, :description, 
                :meta_title, :meta_description, :meta_keywords, :schema_json, :status
            )
        ";
        $insertStmt = $pdo->prepare($insertSql);

        foreach ($defaultArticles as $art) {
            $insertStmt->execute($art);
        }
        $report .= "SUCCESS: Seeded 8 default news stories and blog posts successfully.\n";
    } else {
        $report .= "INFO: Articles table already has " . $articlesCount . " records. Seeding skipped.\n";
    }

    if ($is_cli) {
        echo $report;
    } else {
        echo '<div class="alert alert-success"><strong>Success!</strong> Database update ran successfully.</div>';
        echo '<h3>Execution Logs</h3>';
        echo '<pre class="details">' . htmlspecialchars($report) . '</pre>';
    }

} catch (PDOException $e) {
    $err = "ERROR Executing database updates: " . $e->getMessage() . "\n";
    if ($is_cli) {
        fwrite(STDERR, $err);
        exit(1);
    } else {
        echo '<div class="alert alert-error"><strong>Error!</strong> Database update failed.</div>';
        echo '<pre class="details">' . htmlspecialchars($err) . '</pre>';
    }
}

if (!$is_cli) {
    echo '</div></body></html>';
}
