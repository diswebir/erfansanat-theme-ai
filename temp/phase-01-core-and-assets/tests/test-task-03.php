<?php
/**
 * Asset migration and WordPress enqueue integration test for task 1.3.
 * Run through temp/setup-wordpress-test.sh, or set WP_TEST_ROOT and execute with PHP.
 */
$root = dirname(__DIR__, 3);
$failed = 0;
$sourceFile = $root . '/inc/AssetManager.php';
$managerSource = is_file($sourceFile) ? file_get_contents($sourceFile) : false;
foreach (['wp_enqueue_style', 'wp_enqueue_script', 'filemtime', 'is_admin'] as $needle) {
    if ($managerSource === false || strpos($managerSource, $needle) === false) {
        fwrite(STDERR, "FAIL AssetManager contract missing: {$needle}\n");
        $failed++;
    }
}
$themeSource = file_get_contents($root . '/inc/Theme.php');
if ($themeSource === false || strpos($themeSource, 'AssetManager::register') === false) {
    fwrite(STDERR, "FAIL AssetManager is not registered by the theme bootstrap\n");
    $failed++;
}

$assetRoots = [
    [$root . '/static-mirror/assets', $root . '/assets'],
    [$root . '/static-mirror/images', $root . '/assets/images'],
];
$assetCount = 0;
foreach ($assetRoots as [$sourceRoot, $targetRoot]) {
    if (!is_dir($sourceRoot)) {
        fwrite(STDERR, "FAIL source assets are missing: {$sourceRoot}\n");
        $failed++;
        continue;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceRoot, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $sourceFileInfo) {
        if (!$sourceFileInfo->isFile()) {
            continue;
        }
        $relative = substr($sourceFileInfo->getPathname(), strlen($sourceRoot) + 1);
        $target = $targetRoot . DIRECTORY_SEPARATOR . $relative;
        if (!is_file($target)) {
            fwrite(STDERR, "FAIL missing migrated asset: {$target}\n");
            $failed++;
            continue;
        }
        if (hash_file('sha256', $sourceFileInfo->getPathname()) !== hash_file('sha256', $target)) {
            fwrite(STDERR, "FAIL migrated asset differs from mirror: {$relative}\n");
            $failed++;
            continue;
        }
        $assetCount++;
    }
}

$cssPath = $root . '/assets/css/styles.css';
$css = is_file($cssPath) ? file_get_contents($cssPath) : false;
if ($css !== false && preg_match_all("~url\\(\\s*(['\"]?)(.*?)\\1\\s*\\)~i", $css, $matches)) {
    foreach ($matches[2] as $url) {
        $url = trim($url);
        if ($url === '' || str_starts_with($url, 'data:') || str_starts_with($url, '#')) {
            continue;
        }
        if (preg_match('~^(?:https?:)?//~i', $url)) {
            fwrite(STDERR, "FAIL unexpected remote CSS asset: {$url}\n");
            $failed++;
            continue;
        }
        $local = realpath(dirname($cssPath) . DIRECTORY_SEPARATOR . rawurldecode($url));
        $assetBase = realpath($root . '/assets');
        if ($local === false || $assetBase === false || strpos($local, $assetBase . DIRECTORY_SEPARATOR) !== 0) {
            fwrite(STDERR, "FAIL unresolved/unsafe CSS url(): {$url}\n");
            $failed++;
        }
    }
}

$wpRoot = getenv('WP_TEST_ROOT');
if (!$wpRoot) {
    $wpTestDir = getenv('WP_TEST_DIR') ?: $root . '/node_modules/.cache/ese-wp-test';
    $wpRoot = rtrim($wpTestDir, DIRECTORY_SEPARATOR) . '/WordPress-master';
}
if (!is_file($wpRoot . '/wp-load.php')) {
    fwrite(STDERR, "FAIL WordPress test installation not found at {$wpRoot}\n");
    exit(1);
}
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['SERVER_NAME'] = 'example.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$isAdminTest = getenv('WP_TEST_ADMIN') === '1';
if ($isAdminTest && !defined('WP_ADMIN')) {
    define('WP_ADMIN', true);
}
require $wpRoot . '/wp-load.php';
if (wp_get_theme()->get_stylesheet() !== 'erfansanat-theme') {
    fwrite(STDERR, "FAIL the theme is not active in the WordPress test site\n");
    exit(1);
}
if ($isAdminTest) {
    do_action('wp_enqueue_scripts');
    foreach (['erfansanat-theme-styles', 'erfansanat-theme-script'] as $handle) {
        if (wp_style_is($handle, 'enqueued') || wp_script_is($handle, 'enqueued')) {
            fwrite(STDERR, "FAIL frontend asset enqueued in wp-admin: {$handle}\n");
            $failed++;
        }
    }
    if ($failed !== 0) {
        exit(1);
    }
    fwrite(STDOUT, "PASS AssetManager skips admin assets\n");
    exit(0);
}
$GLOBALS['wp']->main('/');
ob_start();
require get_theme_file_path('index.php');
$html = ob_get_clean();
$cssVersion = is_file($cssPath) ? filemtime($cssPath) : false;
$jsPath = $root . '/assets/js/main.js';
$jsVersion = is_file($jsPath) ? filemtime($jsPath) : false;
$cssUrl = false === $cssVersion ? '' : get_theme_file_uri('/assets/css/styles.css') . '?ver=' . $cssVersion;
$jsUrl = false === $jsVersion ? '' : get_theme_file_uri('/assets/js/main.js') . '?ver=' . $jsVersion;
if (strpos($html, $cssUrl) === false) {
    fwrite(STDERR, "FAIL stylesheet was not enqueued with its filemtime URL\n");
    $failed++;
}
if (strpos($html, $jsUrl) === false) {
    fwrite(STDERR, "FAIL main script was not enqueued with its filemtime URL\n");
    $failed++;
}
if ($assetCount < 25) {
    fwrite(STDERR, "FAIL expected local asset inventory was incomplete: {$assetCount}\n");
    $failed++;
}
if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS {$assetCount} migrated assets, CSS-relative URLs and WordPress enqueue versions\n");
