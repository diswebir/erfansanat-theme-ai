<?php
/**
 * WordPress integration test for task 1.2: frontend resource cleanup.
 * Run through temp/setup-wordpress-test.sh, or set WP_TEST_ROOT and execute with PHP.
 */
$root = dirname(__DIR__, 3);
$sourceFile = $root . '/inc/DeBloat.php';
$source = is_file($sourceFile) ? file_get_contents($sourceFile) : false;
$staticChecks = [
    'wp_dequeue_style',
    'wp-block-library',
    'global-styles',
    'wp-global-styles-placeholder',
    'wp_enqueue_global_styles',
    'wp_dequeue_script',
    'wp-emoji-release',
    'is_admin',
    'wp_generator',
];
$failed = 0;
foreach ($staticChecks as $needle) {
    if ($source === false || strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL source contract missing: {$needle}\n");
        $failed++;
    } else {
        fwrite(STDOUT, "PASS source contract: {$needle}\n");
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
if (!current_theme_supports('title-tag')) {
    fwrite(STDERR, "FAIL theme did not load in WordPress\n");
    exit(1);
}
if ($isAdminTest) {
    if (!is_admin()) {
        fwrite(STDERR, "FAIL admin test did not enter an admin context\n");
        $failed++;
    }
    if (!has_action('wp_head', 'wp_generator') || !has_action('wp_head', 'print_emoji_detection_script')) {
        fwrite(STDERR, "FAIL admin hooks were modified by frontend cleanup\n");
        $failed++;
    }
    if ($failed !== 0) {
        exit(1);
    }
    fwrite(STDOUT, "PASS WordPress admin remains untouched\n");
    exit(0);
}

add_action('wp_head', static function (): void {
    echo '<meta name="integration-hook-preserved" content="yes">';
}, 3);
$GLOBALS['wp']->main('/');
foreach (['wp-block-library', 'wp-block-library-theme', 'classic-theme-styles'] as $handle) {
    wp_enqueue_style($handle);
}
do_action('wp_enqueue_scripts');
foreach (
    [
        'wp-block-library',
        'wp-block-library-theme',
        'global-styles',
        'wp-global-styles-placeholder',
        'wp-block-styles-placeholder',
        'classic-theme-styles',
        'wp-emoji-styles',
    ] as $handle
) {
    if (wp_style_is($handle, 'enqueued')) {
        fwrite(STDERR, "FAIL core style remains enqueued: {$handle}\n");
        $failed++;
    }
}
ob_start();
require get_theme_file_path('index.php');
$html = ob_get_clean();
foreach ([
    'wp-block-library-css',
    'wp-block-library-theme-css',
    'global-styles-inline-css',
    'wp-global-styles-placeholder-inline-css',
    'wp-block-styles-placeholder-inline-css',
    'classic-theme-styles-inline-css',
    'wp-emoji-release',
    'wp-emoji-styles',
    'window._wpemojiSettings',
    'name="generator"',
    'rsd+xml',
    'wlwmanifest',
] as $forbidden) {
    if (strpos($html, $forbidden) !== false) {
        fwrite(STDERR, "FAIL frontend still contains {$forbidden}\n");
        $failed++;
    }
}
if (strpos($html, 'integration-hook-preserved') === false || strpos($html, '</head>') === false) {
    fwrite(STDERR, "FAIL wp_head or third-party metadata hooks were not preserved\n");
    $failed++;
}
if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS WordPress frontend resource/head integration\n");
