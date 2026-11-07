<?php
/** WordPress integration test for local Vazirmatn font integration across admin/editor screens. */
$root = dirname(__DIR__, 3);
$themeSource = $root . '/inc/Theme.php';
$typographySource = $root . '/inc/Admin/Typography.php';
$stylesheet = $root . '/assets/css/admin-vazirmatn.css';
$checks = [
    [$themeSource, 'Admin\\Dashboard::register()'],
    [$themeSource, 'Admin\\Typography::register()'],
    [$themeSource, "add_theme_support('editor-styles')"],
    [$themeSource, "add_editor_style('assets/css/admin-vazirmatn.css')"],
    [$typographySource, "add_action('admin_enqueue_scripts'"],
    [$typographySource, 'wp_enqueue_style('],
    [$typographySource, "hash_file('sha256'"],
    [$stylesheet, '@font-face'],
    [$stylesheet, '#wpadminbar'],
    [$stylesheet, 'body.wp-admin input'],
    [$stylesheet, '[class*="fa-"]'],
    [$root . '/inc/Admin/Dashboard.php', 'register_widget'],
    [$root . '/assets/css/admin-dashboard.css', '.ese-dashboard-action-grid'],

    [$stylesheet, 'vazirmatn-arabic.woff2'],
    [$stylesheet, 'vazirmatn-latin-ext.woff2'],
    [$stylesheet, 'vazirmatn-latin.woff2'],
    [$stylesheet, 'body.wp-admin'],
    [$stylesheet, '.editor-styles-wrapper'],
    [$stylesheet, ':not(.dashicons)'],
    [$stylesheet, 'font-display: swap'],
];
$failed = 0;
foreach ($checks as [$path, $needle]) {
    $contents = is_file($path) ? file_get_contents($path) : false;
    if (false === $contents || false === strpos($contents, $needle)) {
        fwrite(STDERR, "FAIL admin typography contract missing `{$needle}` in {$path}\n");
        $failed++;
    }
}
foreach (['vazirmatn-arabic.woff2', 'vazirmatn-latin-ext.woff2', 'vazirmatn-latin.woff2'] as $font) {
    if (!is_file($root . '/assets/fonts/' . $font)) {
        fwrite(STDERR, "FAIL local Vazirmatn font file missing: {$font}\n");
        $failed++;
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
$_SERVER['REQUEST_URI'] = '/wp-admin/index.php';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/wp-admin/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define('WP_ADMIN', true);
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$class = 'ErfanSanat\\Theme\\Admin\\Typography';
if (!class_exists($class)) {
    fwrite(STDERR, "FAIL Typography class was not loaded\n");
    exit(1);
}
if (!has_action('admin_enqueue_scripts', [$class, 'enqueue_admin_styles'])) {
    fwrite(STDERR, "FAIL admin typography enqueue hook was not registered\n");
    $failed++;
}
$class::enqueue_admin_styles();
if (!wp_style_is('erfansanat-admin-vazirmatn', 'enqueued')) {
    fwrite(STDERR, "FAIL local Vazirmatn admin stylesheet was not enqueued\n");
    $failed++;
}
if (!current_theme_supports('editor-styles')) {
    fwrite(STDERR, "FAIL editor-styles support is not enabled\n");
    $failed++;
}
if ($failed) {
    exit(1);
}
fwrite(STDOUT, "PASS local Vazirmatn loading for admin screens, theme controls, and classic/block editor content\n");
