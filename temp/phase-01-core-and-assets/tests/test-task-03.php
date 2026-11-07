<?php
/**
 * Automated QA Test Script — Task 1.3: Static Assets Migration, AssetManager (filemtime) & Live HTTP 200 Check
 * Phase: phase-01-core-and-assets
 */
declare(strict_types=1);

$themeRoot = dirname(__DIR__, 3);
$wpRoot    = '/opt/wp-testbed';

echo "=== [TEST] Task 1.3: Static Assets Migration & AssetManager ===\n";

$failures = [];
$assert = static function (bool $cond, string $label) use (&$failures): void {
    if ($cond) {
        echo "  [PASS] {$label}\n";
    } else {
        echo "  [FAIL] {$label}\n";
        $failures[] = $label;
    }
};

// 1. Check required PHP & static asset files
$requiredAssets = [
    'inc/Core/AssetManager.php',
    'inc/Helpers/AssetHelper.php',
    'assets/css/styles.css',
    'assets/js/main.js',
    'assets/fonts/vazirmatn-arabic.woff2',
    'assets/fonts/vazirmatn-latin.woff2',
    'assets/fonts/vazirmatn-latin-ext.woff2',
    'assets/images/ese-logo.svg',
    'assets/images/hero.jpg',
    'assets/images/hero-video.mp4',
    'assets/images/factory.jpg',
    'assets/images/factory.webp',
    'assets/images/light-tunnel.jpg',
    'assets/images/light-tunnel.webp',
    'assets/images/hero.webp',
    'assets/images/string-lights.webp',
    'assets/images/light-element.webp',
    'assets/images/light-tree.webp',
    'assets/images/light-tree.jpg',
    'assets/images/light-sphere.jpg',
    'assets/images/light-element.jpg',
    'assets/images/chandelier.jpg',
    'assets/images/string-lights.jpg',
    'assets/images/sun-light.jpg',
];

foreach ($requiredAssets as $rel) {
    $full = $themeRoot . '/' . $rel;
    $assert(is_file($full) && filesize($full) > 0, "Asset/file exists & non-empty: {$rel}");
}

$pexelsFiles = glob($themeRoot . '/assets/images/pexels/*.jpg') ?: [];
$assert(count($pexelsFiles) === 11, 'All 11 Pexels localized images exist in assets/images/pexels/ (' . count($pexelsFiles) . ')');

// 2. Lint new PHP files
foreach (['inc/Core/AssetManager.php', 'inc/Helpers/AssetHelper.php', 'functions.php'] as $phpRel) {
    $full = $themeRoot . '/' . $phpRel;
    if (is_file($full)) {
        $out = [];
        $code = 0;
        exec('php -l ' . escapeshellarg($full) . ' 2>&1', $out, $code);
        $assert($code === 0, "php -l syntax valid: {$phpRel}");
    }
}

// 3. Validate all relative url(...) paths inside assets/css/styles.css
$cssPath = $themeRoot . '/assets/css/styles.css';
if (is_file($cssPath)) {
    $cssContent = (string) file_get_contents($cssPath);
    preg_match_all('/url\(\s*[\'"]?([^\'")]+)[\'"]?\s*\)/i', $cssContent, $matches);
    $urls = array_unique($matches[1] ?? []);
    $relativeUrlsChecked = 0;
    foreach ($urls as $u) {
        $u = trim(rawurldecode($u));
        if ($u === '' || str_starts_with($u, 'data:') || str_starts_with($u, '#') || str_starts_with($u, 'http')) {
            continue;
        }
        $cleanRel = preg_replace('/[?#].*$/', '', $u) ?? $u;
        $resolved = realpath(dirname($cssPath) . '/' . $cleanRel);
        $assert($resolved !== false && is_file($resolved), "CSS relative url('{$u}') resolves to existing file on disk");
        $relativeUrlsChecked++;
    }
    $assert($relativeUrlsChecked === 3, "Verified all 3 relative @font-face url() paths in styles.css ({$relativeUrlsChecked})");
}

// 4. Boot WordPress and verify AssetManager enqueue, filemtime versions, preload, and esThemeData
$capturedErrors = [];
set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline) use (&$capturedErrors, $themeRoot): bool {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    if (str_starts_with($errfile, $themeRoot) && !str_starts_with($errfile, $themeRoot . '/temp/')) {
        $capturedErrors[] = "[{$errno}] {$errstr} in {$errfile}:{$errline}";
    }
    return false;
});

require_once $wpRoot . '/wp-load.php';

$assert(function_exists('es_asset_uri') && function_exists('es_image_uri'), 'Global asset helper functions es_asset_uri() & es_image_uri() available');

$GLOBALS['wp']->main();
ob_start();
wp_head();
$headHtml = (string) ob_get_clean();

ob_start();
wp_footer();
$footerHtml = (string) ob_get_clean();

$fullOutput = $headHtml . "\n" . $footerHtml;

$cssMtime = (string) filemtime($themeRoot . '/assets/css/styles.css');
$jsMtime  = (string) filemtime($themeRoot . '/assets/js/main.js');

$assert(wp_style_is('erfansanat-styles', 'done'), 'Stylesheet handle erfansanat-styles enqueued & printed');
$assert(str_contains($headHtml, 'assets/css/styles.css?ver=' . $cssMtime), "Stylesheet URL includes exact filemtime version (?ver={$cssMtime})");

$assert(wp_script_is('erfansanat-main', 'done'), 'Script handle erfansanat-main enqueued & printed');
$assert(str_contains($fullOutput, 'assets/js/main.js?ver=' . $jsMtime), "Script URL includes exact filemtime version (?ver={$jsMtime})");

$assert(str_contains($headHtml, 'rel="preload"') && str_contains($headHtml, 'vazirmatn-arabic.woff2'), 'Vazirmatn Persian WOFF2 font preloaded in <head>');
$imagePreloads = [];
preg_match_all('/<link[^>]+as="image"[^>]*>/', $headHtml, $imagePreloads);
$assert(count($imagePreloads[0] ?? []) === 1 && str_contains($headHtml, 'light-tunnel.webp') && str_contains($headHtml, 'fetchpriority="high"'), 'Only the visible WebP hero LCP image is preloaded at high priority');
$assert(str_contains($fullOutput, 'window.esThemeData'), 'window.esThemeData inline config injected');
$assert(str_contains($fullOutput, '"filterUrlMutation":false'), 'esThemeData enforces filterUrlMutation: false (SEO rule)');

// 5. Real HTTP 200 verification via PHP built-in server + cURL
$port = random_int(18100, 18900);
$baseUrl = "http://127.0.0.1:{$port}";
update_option('siteurl', $baseUrl);
update_option('home', $baseUrl);

$cmd = sprintf('php -S 127.0.0.1:%d -t %s >/dev/null 2>&1 & echo $!', $port, escapeshellarg($wpRoot));
$pid = trim((string) shell_exec($cmd));

try {
    // Wait briefly for server socket
    for ($i = 0; $i < 20; $i++) {
        $fp = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.1);
        if ($fp !== false) {
            fclose($fp);
            break;
        }
        usleep(100000);
    }

    $endpointsToCheck = [
        '/' => 'Front-end root HTML response',
        '/wp-content/themes/erfansanat-theme-ai/assets/css/styles.css' => 'Static compiled CSS (styles.css)',
        '/wp-content/themes/erfansanat-theme-ai/assets/js/main.js' => 'Static interactive JS (main.js)',
        '/wp-content/themes/erfansanat-theme-ai/assets/fonts/vazirmatn-arabic.woff2' => 'Persian WOFF2 font (vazirmatn-arabic.woff2)',
        '/wp-content/themes/erfansanat-theme-ai/assets/fonts/vazirmatn-latin.woff2' => 'Latin WOFF2 font (vazirmatn-latin.woff2)',
        '/wp-content/themes/erfansanat-theme-ai/assets/fonts/vazirmatn-latin-ext.woff2' => 'Latin-Ext WOFF2 font (vazirmatn-latin-ext.woff2)',
        '/wp-content/themes/erfansanat-theme-ai/assets/images/ese-logo.svg' => 'Brand SVG Logo (ese-logo.svg)',
    ];

    foreach ($endpointsToCheck as $path => $desc) {
        $curlOut = [];
        $curlCode = 0;
        exec('curl -s -o /dev/null -w "%{http_code}" ' . escapeshellarg($baseUrl . $path), $curlOut, $curlCode);
        $httpStatus = trim($curlOut[0] ?? '');
        $assert($curlCode === 0 && $httpStatus === '200', "HTTP 200 OK via cURL: {$desc} ({$path} -> {$httpStatus})");
    }
} finally {
    if ($pid !== '' && ctype_digit($pid)) {
        exec('kill ' . (int) $pid . ' >/dev/null 2>&1 || true');
    }
    update_option('siteurl', 'http://localhost:8080');
    update_option('home', 'http://localhost:8080');
}

$assert(empty($capturedErrors), 'Zero PHP warnings/notices from theme files (' . implode('; ', $capturedErrors) . ')');

restore_error_handler();

if (!empty($failures)) {
    fwrite(STDERR, "\n[FAILED] Task 1.3 failed " . count($failures) . " check(s).\n");
    exit(1);
}

echo "\n[PASSED] Task 1.3 passed all checks!\n";
exit(0);
