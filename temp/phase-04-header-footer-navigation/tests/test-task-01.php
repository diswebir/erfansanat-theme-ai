<?php
/**
 * WordPress integration contract for task 4.1: theme header, footer, and language alternates.
 * Run after temp/setup-wordpress-test.sh with PHP or PHP WASM.
 */
$root = dirname(__DIR__, 3);
$headerPath = $root . '/header.php';
$footerPath = $root . '/footer.php';
$rendererPath = $root . '/inc/TemplateParts.php';
$mobileFallbackPath = $root . '/template-parts/navigation/mobile-fallback.php';
$mirror = file_get_contents($root . '/static-mirror/index.html');
$failed = 0;

foreach ([$headerPath, $footerPath, $rendererPath, $mobileFallbackPath] as $path) {
    if (!is_file($path)) {
        fwrite(STDERR, 'FAIL missing theme shell file: ' . basename($path) . "\n");
        $failed++;
    }
}
if ($failed) {
    exit(1);
}

$headerSource = file_get_contents($headerPath);
$footerSource = file_get_contents($footerPath);
$rendererSource = file_get_contents($rendererPath);
$mobileFallbackSource = file_get_contents($mobileFallbackPath);
foreach ([
    [$headerSource, 'wp_head()', 'header wp_head hook'],
    [$headerSource, 'body_class(', 'WordPress body classes'],
    [$headerSource, 'wp_body_open()', 'header body-open hook'],
    [$headerSource, 'data-navbar-header="true"', 'desktop header root'],
    [$mobileFallbackSource, 'data-mobile-menu="true"', 'mobile menu fallback panel'],
    [$headerSource, 'data-mobile-toggle="true"', 'mobile menu toggle'],
    [$headerSource, 'data-search-modal="true"', 'search panel'],
    [$headerSource, 'data-cart-modal="true"', 'cart panel'],
    [$footerSource, 'wp_footer()', 'footer wp_footer hook'],
    [$footerSource, 'data-scroll-top="true"', 'footer scroll-top control'],
    [$rendererSource, 'hrefmeta_en', 'English hreflang legacy key'],
    [$rendererSource, 'SEOPRESS_VERSION', 'SEOPress duplicate guard'],
] as [$source, $needle, $description]) {
    if (strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL missing {$description}\n");
        $failed++;
    }
}
foreach (['data-navbar-header="true"', 'data-mobile-menu="true"', 'data-mobile-toggle="true"', 'data-search-modal="true"', 'data-cart-modal="true"', 'data-scroll-top="true"'] as $needle) {
    if (strpos($mirror, $needle) === false) {
        fwrite(STDERR, "FAIL reference mirror is missing required shell selector {$needle}\n");
        $failed++;
    }
}

$wpRoot = getenv('WP_TEST_ROOT');
if (!$wpRoot) {
    $wpRoot = $root . '/node_modules/.cache/ese-wp-test/WordPress-master';
}
if (!is_file($wpRoot . '/wp-load.php')) {
    fwrite(STDERR, "FAIL disposable WordPress installation not found at {$wpRoot}\n");
    exit(1);
}
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['SERVER_NAME'] = 'example.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$class = 'ErfanSanat\\Theme\\TemplateParts';
if (!class_exists($class)) {
    fwrite(STDERR, "FAIL TemplateParts class was not loaded\n");
    exit(1);
}
if ($class::resolve_static_url('products/example-product.html') !== home_url('/product/example-product/')) {
    fwrite(STDERR, "FAIL mirrored product URL did not map to the canonical product route\n");
    $failed++;
}
if ($class::resolve_static_url('images/hero.jpg', true) !== get_theme_file_uri('assets/images/hero.jpg')) {
    fwrite(STDERR, "FAIL mirrored image URL did not map to the theme asset directory\n");
    $failed++;
}

$missingOptions = new stdClass();
$previousOptions = get_option('erfansanat_theme_options', $missingOptions);
$hadPreviousOptions = $previousOptions !== $missingOptions;
$stored = array_replace_recursive(
    \ErfanSanat\Theme\Admin\ThemeOptions::defaults(),
    [
        'branding' => ['site_name' => 'نام پویا', 'site_tagline' => 'شعار پویا', 'logo_url' => 'https://example.test/custom-logo.svg'],
        'header' => [
            'topbar_enabled' => true,
            'topbar_message' => 'متن نوار پویا',
            'topbar_link_label' => 'پیوند آزمایشی',
            'topbar_link_url' => '/test-link/',
            'topbar_links' => "مجوزها و افتخارات | /licenses-and-honors/\nگارانتی | /refund_returns/",
            'announcement_enabled' => true,
            'announcement_text' => 'اطلاعیه پویا',
            'announcement_url' => '/announcement/',
        ],
        'footer' => [
            'company_name' => 'شرکت پویا',
            'description' => 'معرفی پویا',
            'badges' => "نشان یک\nنشان دو",
            'quick_links' => "پیوند پویا | /dynamic-footer/",
            'copyright' => 'حق نشر پویا',
            'show_back_to_top' => true,
        ],
        'contact' => ['phone' => '031-11111111', 'sales_phone' => '09120000000', 'email' => 'test@example.test', 'address' => 'نشانی پویا', 'business_hours' => 'ساعت پویا'],
    ]
);
update_option('erfansanat_theme_options', $stored);
es_clear_theme_option_cache('erfansanat_theme_options');

$postId = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'Hreflang shell QA']);
update_post_meta($postId, 'hrefmeta_en', 'https://example.test/en/sample/');
update_post_meta($postId, 'hrefmeta_ar', 'https://example.test/ar/sample/');
$hreflang = $class::hreflang_markup($postId, 'post');
if (substr_count($hreflang, 'rel="alternate"') !== 3 || strpos($hreflang, 'hreflang="en"') === false || strpos($hreflang, 'hreflang="ar"') === false || strpos($hreflang, 'hreflang="fa"') === false) {
    fwrite(STDERR, "FAIL post hreflang output is incomplete or duplicated\n");
    $failed++;
}
if (!post_type_exists('product')) {
    register_post_type('product', ['public' => true, 'show_ui' => true, 'supports' => ['title']]);
}
if (!taxonomy_exists('product_cat')) {
    register_taxonomy('product_cat', ['product'], ['public' => true, 'hierarchical' => true, 'show_in_rest' => true]);
}
$termId = 0;
$term = term_exists('Hreflang term QA', 'product_cat');
if (!$term) {
    $term = wp_insert_term('Hreflang term QA', 'product_cat');
}
if (!is_wp_error($term) && $term) {
    $termId = is_array($term) ? (int) $term['term_id'] : (int) $term;
    update_term_meta($termId, 'hrefmeta_en', 'https://example.test/en/category/');
    $termMarkup = $class::hreflang_markup($termId, 'product_cat');
    if (strpos($termMarkup, 'hreflang="en"') === false) {
        fwrite(STDERR, "FAIL taxonomy term hreflang was not rendered\n");
        $failed++;
    }
} else {
    fwrite(STDERR, "FAIL could not create taxonomy term hreflang QA record\n");
    $failed++;
}
if (!defined('SEOPRESS_VERSION')) {
    define('SEOPRESS_VERSION', '9.0-test');
}
if ($class::hreflang_markup($postId, 'post') !== '') {
    fwrite(STDERR, "FAIL theme hreflang duplicated output delegated to SEOPress\n");
    $failed++;
}

add_action('wp_head', static function () { echo 'test-wp-head-hook'; }, 999);
add_action('wp_body_open', static function () { echo 'test-wp-body-open-hook'; }, 999);
add_action('wp_footer', static function () { echo 'test-wp-footer-hook'; }, 999);
ob_start();
include $headerPath;
$headerOutput = ob_get_clean();
ob_start();
include $footerPath;
$footerOutput = ob_get_clean();
foreach (['data-navbar-header="true"', 'data-mobile-menu="true"', 'data-mobile-toggle="true"', 'data-search-modal="true"', 'data-cart-modal="true"'] as $needle) {
    if (strpos($headerOutput, $needle) === false) {
        fwrite(STDERR, "FAIL rendered responsive header is missing mirror selector {$needle}\n");
        $failed++;
    }
}
if (strpos($footerOutput, 'data-scroll-top="true"') === false || strpos($footerOutput, '<footer') === false) {
    fwrite(STDERR, "FAIL rendered footer is missing its reference structure\n");
    $failed++;
}
foreach (['نام پویا', 'شعار پویا', 'متن نوار پویا', 'اطلاعیه پویا', 'custom-logo.svg', 'test-link'] as $needle) {
    if (strpos($headerOutput, $needle) === false) {
        fwrite(STDERR, "FAIL rendered header is missing dynamic value {$needle}\n");
        $failed++;
    }
}
foreach (['شرکت پویا', 'معرفی پویا', 'نشان یک', 'پیوند پویا', '031-11111111', 'test@example.test', 'نشانی پویا', 'حق نشر پویا'] as $needle) {
    if (strpos($footerOutput, $needle) === false) {
        fwrite(STDERR, "FAIL rendered footer is missing dynamic value {$needle}\n");
        $failed++;
    }
}
if (strpos($headerOutput, 'href="licenses-and-honors.html"') !== false || strpos($footerOutput, 'href="shop.html"') !== false || strpos($headerOutput, 'src="images/') !== false) {
    fwrite(STDERR, "FAIL static mirror URLs/assets were not converted to WordPress URLs\n");
    $failed++;
}
if (preg_match('/<script\b/i', $headerOutput) && strpos($headerOutput, '<script>alert(') !== false) {
    fwrite(STDERR, "FAIL escaped header settings allowed script markup\n");
    $failed++;
}
if ($class::resolve_static_url('javascript:alert(1)') !== '') {
    fwrite(STDERR, "FAIL unsafe javascript URL was accepted\n");
    $failed++;
}
if (strpos($headerOutput, 'test-wp-head-hook') === false || strpos($headerOutput, 'test-wp-body-open-hook') === false || strpos($footerOutput, 'test-wp-footer-hook') === false) {
    fwrite(STDERR, "FAIL WordPress shell hooks were not rendered\n");
    $failed++;
}

wp_delete_post($postId, true);
if ($termId > 0) {
    wp_delete_term($termId, 'product_cat');
}
if ($hadPreviousOptions) {
    update_option('erfansanat_theme_options', $previousOptions);
} else {
    delete_option('erfansanat_theme_options');
}
es_clear_theme_option_cache('erfansanat_theme_options');

if ($failed) {
    exit(1);
}
fwrite(STDOUT, "PASS responsive header/footer shell, editable settings, WordPress URLs/hooks, and post/term hreflang with SEOPress de-duplication\n");
