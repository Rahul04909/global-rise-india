<?php
/**
 * sitemap.php
 * ============================================================
 * Dynamic XML Sitemap Generator
 * The Global Rise Foundation
 * ============================================================
 */

require_once __DIR__ . '/includes/config.php';
$pdo = getDB();

// Determine base URL dynamically or from config
$site_url = 'https://globalrisefoundation.com';
if (defined('APP_URL') && !empty(APP_URL)) {
    $site_url = rtrim(APP_URL, '/');
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if (strpos($host, 'localhost') !== false) {
        $site_url = $protocol . $host . "/gloabl-rise";
    } else {
        $site_url = $protocol . $host;
    }
}

// Static pages
$static_pages = [
    '',
    '/index.php',
    '/volunteer.php',
    '/donate.php',
    '/pages/about-us.php',
    '/pages/vision-mission.php',
    '/pages/inspiration.php',
    '/pages/board-trustees.php',
    '/pages/tax-exemption.php',
    '/pages/donation-faqs.php',
    '/pages/terms-conditions.php',
    '/pages/what-we-do.php',
    '/pages/news-stories.php',
    '/pages/how-to-help.php',
    '/pages/impact-reports.php',
    '/pages/blogs.php',
    '/pages/animal-welfare.php',
    '/pages/disaster-management.php',
    '/pages/educating-slum-children.php',
    '/pages/health-projects.php',
    '/pages/persons-with-disabilities.php',
    '/pages/rural-children-education.php',
    '/pages/senior-citizen-care.php',
    '/pages/swacch-bharat-mission.php',
    '/pages/women-empowerment.php'
];

// Fetch published articles
$articles = [];
try {
    $stmt = $pdo->query("SELECT `slug`, `updated_at` FROM `articles` WHERE `status` = 'published' ORDER BY `updated_at` DESC");
    $articles = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("[Sitemap Error] " . $e->getMessage());
}

// Build XML Sitemap
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Add static pages
foreach ($static_pages as $page) {
    $loc = $site_url . $page;
    $xml .= '  <url>' . "\n";
    $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . "\n";
    $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    $xml .= '    <changefreq>' . (empty($page) ? 'daily' : 'weekly') . '</changefreq>' . "\n";
    $xml .= '    <priority>' . (empty($page) ? '1.0' : '0.8') . '</priority>' . "\n";
    $xml .= '  </url>' . "\n";
}

// Add dynamic article pages
foreach ($articles as $art) {
    $loc = $site_url . '/pages/article-details.php?slug=' . $art['slug'];
    $lastmod = date('Y-m-d', strtotime($art['updated_at']));
    $xml .= '  <url>' . "\n";
    $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . "\n";
    $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . "\n";
    $xml .= '    <changefreq>weekly</changefreq>' . "\n";
    $xml .= '    <priority>0.7</priority>' . "\n";
    $xml .= '  </url>' . "\n";
}

$xml .= '</urlset>';

// Save static copy of sitemap.xml in root
$sitemapXmlPath = __DIR__ . '/sitemap.xml';
@file_put_contents($sitemapXmlPath, $xml);

// Output XML headers and content
header('Content-Type: application/xml; charset=utf-8');
echo $xml;
exit;
