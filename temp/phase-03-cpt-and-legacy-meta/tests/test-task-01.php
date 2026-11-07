<?php
/**
 * Integration contract for task 3.1: native CPTs, taxonomies, and permalink compatibility.
 * Run through PHP WASM or native PHP after installing the disposable WordPress test site.
 */
$root = dirname(__DIR__, 3);
$source = $root . '/inc/ContentTypes.php';
$checks = [
    [$source, 'register_post_type('],
    [$source, "'projects'"],
    [$source, "'services'"],
    [$source, 'register_taxonomy('],
    [$source, "'project_type'"],
    [$source, "'city'"],
    [$source, 'woocommerce_register_post_type_product'],
    [$source, 'restrict_manage_posts'],
    [$source, 'same_key_hrefl'],
    [$source, 'after_switch_theme'],
    [$source, 'services/([^/]+)'],
    [$source, '.html'],
    [$source, 'post_type_archive_link'],
    [$source, 'post_type_link'],
];
$failed = 0;
foreach ($checks as [$path, $needle]) {
    $contents = is_file($path) ? file_get_contents($path) : false;
    if ($contents === false || strpos($contents, $needle) === false) {
        fwrite(STDERR, "FAIL content types contract missing: {$needle} in {$path}\n");
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
$_SERVER['REQUEST_URI'] = '/wp-admin/edit.php?post_type=projects';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/wp-admin/edit.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define('WP_ADMIN', true);
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$class = 'ErfanSanat\Theme\ContentTypes';
if (!class_exists($class)) {
    fwrite(STDERR, "FAIL ContentTypes class was not loaded\n");
    exit(1);
}

foreach (['projects', 'services'] as $postTypeName) {
    $postType = get_post_type_object($postTypeName);
    if (!$postType || empty($postType->show_in_rest) || empty($postType->public)) {
        fwrite(STDERR, "FAIL {$postTypeName} CPT is not registered publicly in REST\n");
        $failed++;
    }
}
$projects = get_post_type_object('projects');
if (
    !$projects
    || empty($projects->has_archive)
    || ($projects->rewrite['slug'] ?? '') !== 'projects'
    || !empty($projects->rewrite['with_front'])
) {
    fwrite(STDERR, "FAIL projects archive/permalink settings do not match the live URL blueprint\n");
    $failed++;
}
if ($projects) {
    foreach (['title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions'] as $support) {
        if (!post_type_supports('projects', $support)) {
            fwrite(STDERR, "FAIL projects CPT is missing {$support} support\n");
            $failed++;
        }
    }
}
$services = get_post_type_object('services');
if (!$services || ($services->rewrite['slug'] ?? '') !== 'services' || ($services->has_archive ?? false) !== 'services') {
    fwrite(STDERR, "FAIL services archive/single routes do not preserve the static mirror paths\n");
    $failed++;
}
foreach (['project_type', 'city'] as $taxonomyName) {
    $taxonomy = get_taxonomy($taxonomyName);
    if (!$taxonomy || empty($taxonomy->hierarchical) || empty($taxonomy->show_in_rest)) {
        fwrite(STDERR, "FAIL {$taxonomyName} taxonomy is not hierarchical and REST-enabled\n");
        $failed++;
    }
    if (!$taxonomy || empty($taxonomy->object_type) || !in_array('projects', $taxonomy->object_type, true)) {
        fwrite(STDERR, "FAIL {$taxonomyName} is not attached to projects\n");
        $failed++;
    }
}

$productArgs = $class::filter_product_post_type_args(['rewrite' => ['slug' => 'product'], 'has_archive' => true]);
if (($productArgs['rewrite']['slug'] ?? '') !== 'product' || !empty($productArgs['rewrite']['with_front']) || ($productArgs['has_archive'] ?? '') !== 'shop') {
    fwrite(STDERR, "FAIL WooCommerce product single/archive rewrite does not match /product/ and /shop/\n");
    $failed++;
}
$productCatArgs = $class::filter_product_cat_args(['rewrite' => []]);
if (($productCatArgs['rewrite']['slug'] ?? '') !== 'product_cat' || empty($productCatArgs['rewrite']['with_front']) || empty($productCatArgs['hierarchical'])) {
    fwrite(STDERR, "FAIL product_cat rewrite does not preserve the live product_cat URLs\n");
    $failed++;
}
$productTagArgs = $class::filter_product_tag_args(['rewrite' => []]);
if (($productTagArgs['rewrite']['slug'] ?? '') !== 'product-tag' || !empty($productTagArgs['rewrite']['with_front'])) {
    fwrite(STDERR, "FAIL product_tag rewrite does not match the live permalink blueprint\n");
    $failed++;
}
$brandArgs = $class::filter_product_brand_args(['hierarchical' => false, 'show_in_rest' => false]);
if (empty($brandArgs['hierarchical']) || empty($brandArgs['show_in_rest'])) {
    fwrite(STDERR, "FAIL product_brand is not hierarchical/REST-enabled\n");
    $failed++;
}
$servicePost = new WP_Post((object) ['ID' => 99, 'post_type' => 'services', 'post_name' => 'bridge-lighting']);
$serviceLink = $class::filter_service_permalink('https://example.test/services/bridge-lighting/', $servicePost);
$serviceArchive = $class::filter_service_archive_link('https://example.test/services/', 'services');
if (strpos($serviceLink, '/services/bridge-lighting.html') === false || strpos($serviceArchive, '/services.html') === false) {
    fwrite(STDERR, "FAIL service permalink helpers did not preserve mirror .html paths\n");
    $failed++;
}
if (!has_action('restrict_manage_posts') || !has_filter('manage_projects_posts_columns')) {
    fwrite(STDERR, "FAIL project admin taxonomy filters or custom columns are not registered\n");
    $failed++;
}
if (!has_action('after_switch_theme', [$class, 'flush_rewrite_rules'])) {
    fwrite(STDERR, "FAIL one-time rewrite flush is not limited to theme activation\n");
    $failed++;
}

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS CPT/taxonomy registration, permalink rules, WooCommerce compatibility filters, admin filters, and activation-only rewrite flush\n");
