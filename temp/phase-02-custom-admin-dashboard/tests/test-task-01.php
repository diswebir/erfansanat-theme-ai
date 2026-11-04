<?php
/**
 * WordPress integration test for task 2.1: secure custom theme settings.
 * Run through temp/setup-wordpress-test.sh with the test site installed.
 */
$root = dirname(__DIR__, 3);
$source = $root . '/inc/Admin/ThemeOptions.php';
$helperSource = $root . '/inc/Helpers.php';
$checks = [
    [$source, 'current_user_can'],
    [$source, 'check_ajax_referer'],
    [$source, 'erfansanat_theme_options'],
    [$source, 'manage_options'],
    [$source, 'wp_ajax_erfansanat_save_theme_options'],
    [$source, 'sanitize_text_field'],
    [$source, 'wp_send_json'],
    [$helperSource, 'function es_option'],
];
$failed = 0;
foreach ($checks as [$path, $needle]) {
    $contents = is_file($path) ? file_get_contents($path) : false;
    if ($contents === false || strpos($contents, $needle) === false) {
        fwrite(STDERR, "FAIL admin contract missing: {$needle} in {$path}\n");
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
$_SERVER['REQUEST_URI'] = '/wp-admin/admin.php?page=erfansanat-theme-options';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/wp-admin/admin.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define('WP_ADMIN', true);
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

if (!function_exists('es_option') || es_option('branding.site_name', null) !== 'عرفان صنعت اصفهان') {
    fwrite(STDERR, "FAIL es_option did not return the configured schema fallback\n");
    $failed++;
}
$clean = \ErfanSanat\Theme\Admin\ThemeOptions::sanitize_payload(
    [
        'branding.site_name' => '<b>عنوان</b>',
        'contact.email' => 'not-an-email<script>alert(1)</script>',
        'contact.address' => "خیابان <script>alert(1)</script>\nاصفهان",
        'unexpected.admin' => 'should be dropped',
    ]
);
if (($clean['branding']['site_name'] ?? null) !== 'عنوان') {
    fwrite(STDERR, "FAIL text settings were not sanitized\n");
    $failed++;
}
if (($clean['contact']['email'] ?? null) !== '') {
    fwrite(STDERR, "FAIL invalid email was accepted\n");
    $failed++;
}
if (isset($clean['unexpected'])) {
    fwrite(STDERR, "FAIL unknown settings were not discarded\n");
    $failed++;
}
$nonce = wp_create_nonce(\ErfanSanat\Theme\Admin\ThemeOptions::NONCE_ACTION);
if (!\ErfanSanat\Theme\Admin\ThemeOptions::is_authorized_request($nonce)) {
    fwrite(STDERR, "FAIL valid administrator nonce was rejected\n");
    $failed++;
}
wp_set_current_user(0);
if (\ErfanSanat\Theme\Admin\ThemeOptions::is_authorized_request($nonce)) {
    fwrite(STDERR, "FAIL unauthenticated user was authorized\n");
    $failed++;
}
wp_set_current_user(1);
if (!has_action('admin_menu', [\ErfanSanat\Theme\Admin\ThemeOptions::class, 'register_menu'])) {
    fwrite(STDERR, "FAIL custom admin menu was not registered\n");
    $failed++;
}
$originalGet = $_GET;
$_GET['page'] = ['erfansanat-theme-options'];
\ErfanSanat\Theme\Admin\ThemeOptions::enqueue_assets('malformed-settings-hook');
$_GET = $originalGet;
ob_start();
\ErfanSanat\Theme\Admin\ThemeOptions::render_page();
$page = ob_get_clean();
foreach (['dir="rtl"', 'nav-tab', 'es-theme-options', 'data-option-path'] as $needle) {
    if (strpos($page, $needle) === false) {
        fwrite(STDERR, "FAIL admin UI missing {$needle}\n");
        $failed++;
    }
}
if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS ThemeOptions capability/nonce/sanitization/helper/admin UI integration\n");
