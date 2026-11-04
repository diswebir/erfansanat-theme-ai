<?php
/**
 * Integration contract for task 2.2: global settings and frontend defaults.
 * Run from repository root through PHP WASM or native PHP after test WP installation.
 */
$root = dirname(__DIR__, 3);
$source = $root . '/inc/Admin/ThemeOptions.php';
$checks = [
    [$source, "'header' =>"],
    [$source, 'mega_menu_title'],
    [$source, "'footer' =>"],
    [$source, "'contact' =>"],
    [$source, "'social' =>"],
    [$source, "'appearance' =>"],
    [$source, "'scripts' =>"],
    [$source, "'archives' =>"],
    [$source, 'wp_head'],
    [$source, 'esc_attr'],
    [$source, 'loop_shop_per_page'],
];
$failed = 0;
foreach ($checks as [$path, $needle]) {
    $contents = is_file($path) ? file_get_contents($path) : false;
    if ($contents === false || strpos($contents, $needle) === false) {
        fwrite(STDERR, "FAIL global settings contract missing: {$needle} in {$path}\n");
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

if (!class_exists(\ErfanSanat\Theme\Admin\ThemeOptions::class)) {
    fwrite(STDERR, "FAIL ThemeOptions class was not loaded\n");
    exit(1);
}
$defaults = \ErfanSanat\Theme\Admin\ThemeOptions::defaults();
$expectedDefaults = [
    'branding.site_name' => 'عرفان صنعت اصفهان',
    'contact.phone' => '031-91091011',
    'contact.sales_phone' => '09137976915',
    'contact.email' => 'info@erfansanat.com',
    'social.instagram' => 'https://instagram.com/erfan_sanat_esfahan',
    'social.telegram' => 'https://t.me/erfansanat',
    'social.linkedin' => 'https://linkedin.com/company/erfansanat',
    'archives.products_per_page' => 9,
    'archives.projects_per_page' => 6,
    'archives.posts_per_page' => 4,
];
foreach ($expectedDefaults as $path => $expected) {
    $value = $defaults;
    foreach (explode('.', $path) as $segment) {
        $value = is_array($value) && array_key_exists($segment, $value) ? $value[$segment] : null;
    }
    if ($value !== $expected) {
        fwrite(STDERR, "FAIL mirror default mismatch at {$path}\n");
        $failed++;
    }
}

$previous = get_option('erfansanat_theme_options', null);
update_option(
    'erfansanat_theme_options',
    [
        'contact' => ['email' => 'qa@example.test'],
        'appearance' => ['primary_color' => '#12ab34'],
        'archives' => ['products_per_page' => 80, 'products_orderby' => 'not-an-order'],
        'scripts' => ['head' => 'window.eseTest = true;'],
    ],
    false
);
if (function_exists('es_option') && es_option('contact.email') !== 'qa@example.test') {
    fwrite(STDERR, "FAIL es_option did not expose the saved global setting\n");
    $failed++;
}
if (method_exists(\ErfanSanat\Theme\Admin\ThemeOptions::class, 'render_css_variables')) {
    ob_start();
    \ErfanSanat\Theme\Admin\ThemeOptions::render_css_variables();
    $css = ob_get_clean();
    if (strpos($css, '#12ab34') === false) {
        fwrite(STDERR, "FAIL saved color was not rendered on the frontend\n");
        $failed++;
    }
}
if (\ErfanSanat\Theme\Admin\ThemeOptions::filter_products_per_page(12) !== 48) {
    fwrite(STDERR, "FAIL product archive page-size setting was not bounded/applied\n");
    $failed++;
}
if (\ErfanSanat\Theme\Admin\ThemeOptions::filter_products_orderby('date') !== 'date') {
    fwrite(STDERR, "FAIL invalid archive ordering was not ignored in favor of WooCommerce's safe default\n");
    $failed++;
}
if (current_user_can('unfiltered_html')) {
    ob_start();
    \ErfanSanat\Theme\Admin\ThemeOptions::render_head_script();
    $script = ob_get_clean();
    if (strpos($script, 'window.eseTest = true;') === false) {
        fwrite(STDERR, "FAIL saved administrator script was not emitted at the frontend head hook\n");
        $failed++;
    }
}
if (null === $previous) {
    delete_option('erfansanat_theme_options');
} else {
    update_option('erfansanat_theme_options', $previous, false);
}

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS global settings defaults, option access/cache, and frontend palette integration\n");
