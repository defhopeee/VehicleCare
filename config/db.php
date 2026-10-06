<?php
date_default_timezone_set('Africa/Nairobi');

$host = 'localhost';
$db   = 'vehicle_care';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('DB error: ' . $e->getMessage());
}

define('SITE_NAME', 'VehicleCare');

// Auto-detects the site's own URL from where this file actually sits on
// disk relative to the web server's document root, so the project works
// unmodified no matter what folder it's installed into or what domain it's
// served from, no hardcoded path to edit by hand.
$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$projectRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
$relativePath = ($docRoot && strpos($projectRoot, $docRoot) === 0)
    ? substr($projectRoot, strlen($docRoot))
    : '';
$encodedPath = implode('/', array_map('rawurlencode', explode('/', $relativePath)));
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('SITE_URL', $scheme . '://' . $host . $encodedPath);
define('UPLOAD_DIR', dirname(__DIR__) . '/assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');

// A part with no uploaded photo still gets a real illustration matching its
// category, instead of a blank grey box.
function part_thumb($categoryId, $imagePath) {
    if ($imagePath) return UPLOAD_URL . htmlspecialchars($imagePath);
    $slugs = [
        1 => 'tyres', 2 => 'oils', 3 => 'filters', 4 => 'brakes',
        5 => 'batteries', 6 => 'suspension', 7 => 'engine',
        8 => 'bodypaint', 9 => 'accessories',
    ];
    $slug = $slugs[$categoryId] ?? 'accessories';
    return SITE_URL . "/assets/img/parts/$slug.svg";
}

// A brand with no uploaded logo still gets a distinct colored monogram
// instead of the same generic car icon repeated on every card.
function brand_badge_color($name) {
    $colors = ['#1f2937', '#f59e0b', '#0369a1', '#15803d', '#b91c1c', '#6d28d9', '#0891b2', '#c2410c'];
    return $colors[crc32($name) % count($colors)];
}
?>