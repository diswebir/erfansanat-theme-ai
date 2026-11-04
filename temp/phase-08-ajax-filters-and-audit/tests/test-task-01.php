<?php
/** WordPress integration contract for Task 8.1: public AJAX search and archive filters. */
$root = dirname(__DIR__, 3);
$files = [
    'inc/Ajax/Filters.php' => ['verify_nonce', 'wp_ajax_nopriv_ese_live_search', 'wp_ajax_nopriv_ese_filter_products', 'wp_ajax_nopriv_ese_filter_projects', 'wp_verify_nonce', 'WP_Query'],
    'assets/js/main.js' => ['fetch(', 'ese_live_search', 'ese_filter_products', 'ese_filter_projects', 'data-product-filter-clear', 'data-project-filter-clear'],
    'inc/AssetManager.php' => ['wp_localize_script', 'eseAjax', 'ese_public_filters'],
    'inc/Theme.php' => ['Filters::register'],
    'template-parts/woocommerce/product-archive.php' => ['data-product-category-filter', 'data-product-ajax-status'],
    'template-parts/projects/archive.php' => ['data-project-ajax-pagination', 'data-project-filter-clear'],
];
$failed = 0;
foreach ($files as $relative => $needles) {
    $source = is_file($root . '/' . $relative) ? file_get_contents($root . '/' . $relative) : false;
    if (false === $source) {
        fwrite(STDERR, "FAIL missing Task 8.1 file: {$relative}\n");
        $failed++;
        continue;
    }
    foreach ($needles as $needle) {
        if (false === strpos($source, $needle)) {
            fwrite(STDERR, "FAIL Task 8.1 contract missing `{$needle}` in {$relative}\n");
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
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$class = 'ErfanSanat\\Theme\\Ajax\\Filters';
if (!class_exists($class)) {
    fwrite(STDERR, "FAIL AJAX Filters class did not autoload\n");
    exit(1);
}
$class::register();
foreach (['wp_ajax_nopriv_ese_live_search', 'wp_ajax_ese_live_search', 'wp_ajax_nopriv_ese_filter_products', 'wp_ajax_ese_filter_products', 'wp_ajax_nopriv_ese_filter_projects', 'wp_ajax_ese_filter_projects'] as $hook) {
    if (false === has_action($hook, [$class, 'dispatch'])) {
        fwrite(STDERR, "FAIL public AJAX action is not registered: {$hook}\n");
        $failed++;
    }
}
$validNonce = wp_create_nonce('ese_public_filters');
if (!$class::verify_nonce($validNonce) || $class::verify_nonce('invalid-nonce')) {
    fwrite(STDERR, "FAIL nonce verification accepted an invalid value or rejected a valid one\n");
    $failed++;
}

foreach ([
    'product' => ['public' => true, 'has_archive' => true, 'supports' => ['title', 'editor', 'excerpt', 'thumbnail']],
    'projects' => ['public' => true, 'has_archive' => true, 'supports' => ['title', 'editor', 'excerpt', 'thumbnail']],
] as $postType => $args) {
    if (!post_type_exists($postType)) {
        register_post_type($postType, $args);
    }
}
$taxonomyObjects = [
    'product_cat' => ['product'],
    'product_tag' => ['product'],
    'pa_ip-rating' => ['product'],
    'pa_input-voltage' => ['product'],
    'project_type' => ['projects'],
    'city' => ['projects'],
];
foreach ($taxonomyObjects as $taxonomy => $objectTypes) {
    if (!taxonomy_exists($taxonomy)) {
        register_taxonomy($taxonomy, $objectTypes, ['public' => true, 'hierarchical' => true, 'show_in_rest' => true]);
    }
}

$prefix = 'task-81-' . strtolower(wp_generate_password(7, false, false));
$createdPosts = [];
$createdTerms = [];
$makeTerm = static function (string $taxonomy, string $name) use (&$createdTerms, $prefix) {
    $slug = $prefix . '-' . sanitize_title($name);
    $term = wp_insert_term($name . ' ' . $prefix, $taxonomy, ['slug' => $slug]);
    if (is_wp_error($term)) {
        return false;
    }
    $termId = (int) $term['term_id'];
    $createdTerms[] = [$termId, $taxonomy];
    return get_term($termId, $taxonomy);
};
$makeExactTerm = static function (string $taxonomy, string $name) use (&$createdTerms, $prefix) {
    $term = wp_insert_term($name, $taxonomy, ['slug' => $prefix . '-' . sanitize_title($name)]);
    if (is_wp_error($term)) {
        return false;
    }
    $termId = (int) $term['term_id'];
    $createdTerms[] = [$termId, $taxonomy];
    return get_term($termId, $taxonomy);
};
$category = $makeTerm('product_cat', 'QA Ajax Category');
$otherCategory = $makeTerm('product_cat', 'QA Other Category');
$ip68 = $makeExactTerm('pa_ip-rating', 'IP68');
$ip67 = $makeExactTerm('pa_ip-rating', 'IP67');
$voltage24 = $makeExactTerm('pa_input-voltage', '24V DC');
$voltage220 = $makeExactTerm('pa_input-voltage', '220V AC');
$typeUrban = $makeTerm('project_type', 'QA Urban Lighting');
$typeBridge = $makeTerm('project_type', 'QA Bridge Lighting');
$cityZagreb = $makeTerm('city', 'QA Zagreb');
$citySplit = $makeTerm('city', 'QA Split');
foreach ([$category, $otherCategory, $ip68, $ip67, $voltage24, $voltage220, $typeUrban, $typeBridge, $cityZagreb, $citySplit] as $term) {
    if (!$term instanceof WP_Term) {
        fwrite(STDERR, "FAIL could not create filter taxonomy fixtures\n");
        exit(1);
    }
}

$createProduct = static function (string $title, string $price, string $sale, WP_Term $cat, WP_Term $ip, WP_Term $voltage, string $sku = '') use (&$createdPosts) {
    $id = wp_insert_post([
        'post_type' => 'product', 'post_status' => 'publish', 'post_title' => $title,
        'post_name' => sanitize_title($title), 'post_excerpt' => 'Lighting fixture excerpt.', 'post_content' => 'Fixture description.',
    ]);
    if (is_wp_error($id) || !$id) {
        return 0;
    }
    $id = (int) $id;
    $createdPosts[] = $id;
    update_post_meta($id, '_price', $price);
    update_post_meta($id, '_sku', $sku ?: 'SKU-' . $id);
    update_post_meta($id, 'true_sale', $sale);
    update_post_meta($id, 'number2-viewc', (string) $id);
    update_post_meta($id, '_wc_average_rating', '4.5');
    wp_set_object_terms($id, [$cat->term_id], 'product_cat');
    wp_set_object_terms($id, [$ip->term_id], 'pa_ip-rating');
    wp_set_object_terms($id, [$voltage->term_id], 'pa_input-voltage');
    return $id;
};
$productOnline = $createProduct('QA 8.1 Probe Online Product', '98000', 'true', $category, $ip68, $voltage24, 'SKU-QA-81-ON');
$productInquiry = $createProduct('QA 8.1 Probe Inquiry Product', '0', 'false', $category, $ip67, $voltage220, 'SKU-QA-81-INQ');
$productOther = $createProduct('QA 8.1 Other Category Product', '199000', 'true', $otherCategory, $ip68, $voltage24, 'SKU-QA-81-OTHER');
if (!$productOnline || !$productInquiry || !$productOther) {
    fwrite(STDERR, "FAIL could not create product fixtures\n");
    exit(1);
}
$unsafeTitleId = $createProduct('<script>alert(1)</script> QA 8.1 Escaping Fixture', '125000', 'true', $category, $ip68, $voltage24, 'SKU-QA-81-XSS');

$projectOne = wp_insert_post(['post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'QA 8.1 Urban Project Probe', 'post_excerpt' => 'Zagreb illumination fixture.']);
$projectTwo = wp_insert_post(['post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'QA 8.1 Bridge Project Probe', 'post_excerpt' => 'Bridge lighting fixture.']);
$projectThree = wp_insert_post(['post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'QA 8.1 Other City Project', 'post_excerpt' => 'Another city fixture.']);
foreach ([$projectOne, $projectTwo, $projectThree] as $projectId) {
    if (is_wp_error($projectId) || !$projectId) {
        fwrite(STDERR, "FAIL could not create project fixtures\n");
        exit(1);
    }
    $createdPosts[] = (int) $projectId;
}
wp_set_object_terms((int) $projectOne, [$typeUrban->term_id], 'project_type');
wp_set_object_terms((int) $projectOne, [$cityZagreb->term_id], 'city');
wp_set_object_terms((int) $projectTwo, [$typeBridge->term_id], 'project_type');
wp_set_object_terms((int) $projectTwo, [$cityZagreb->term_id], 'city');
wp_set_object_terms((int) $projectThree, [$typeUrban->term_id], 'project_type');
wp_set_object_terms((int) $projectThree, [$citySplit->term_id], 'city');
update_post_meta((int) $projectOne, 'number2-viewc', '10');
update_post_meta((int) $projectTwo, 'number2-viewc', '30');
update_post_meta((int) $projectThree, 'number2-viewc', '20');
update_post_meta((int) $projectOne, 'project-rating', '4.2');
update_post_meta((int) $projectTwo, 'project-rating', '4.8');
update_post_meta((int) $projectThree, 'project-rating', '4.5');

$baseFilters = [
    'query' => 'Probe',
    'category' => $category->slug,
    'sale' => 'online',
    'min_price' => '50000',
    'max_price' => '150000',
    'ip' => ['IP68'],
    'voltage' => ['24V DC'],
    'orderby' => 'price',
    'page' => 1,
    'per_page' => 9,
];
$productResult = $class::filter_products($baseFilters);
$productArgs = $class::build_product_query_args($baseFilters);
if ((int) $productResult['total'] !== 1 || false === strpos($productResult['html'], 'QA 8.1 Probe Online Product') || false !== strpos($productResult['html'], 'Inquiry Product')) {
    fwrite(STDERR, "FAIL composable product search/category/sale/price/attribute filters returned the wrong results\n");
    $failed++;
}
if (($productArgs['post_type'] ?? '') !== 'product' || ($productArgs['post_status'] ?? '') !== 'publish' || (int) ($productArgs['posts_per_page'] ?? 0) > 24 || !isset($productArgs['tax_query'], $productArgs['meta_query'])) {
    fwrite(STDERR, "FAIL product query did not enforce public bounded whitelisted filters\n");
    $failed++;
}
$inquiryResult = $class::filter_products(['query' => 'Probe', 'category' => $category->slug, 'sale' => 'inquiry']);
if ((int) $inquiryResult['total'] !== 1 || false === strpos($inquiryResult['html'], 'Inquiry Product')) {
    fwrite(STDERR, "FAIL true_sale inquiry filtering returned the wrong products\n");
    $failed++;
}
$sortedPageOne = $class::filter_products(['category' => $category->slug, 'sale' => 'online', 'orderby' => 'price', 'page' => 1, 'per_page' => 1]);
$sortedPageTwo = $class::filter_products(['category' => $category->slug, 'sale' => 'online', 'orderby' => 'price', 'page' => 2, 'per_page' => 1]);
if ((int) $sortedPageOne['total'] !== 2 || (int) $sortedPageOne['pages'] !== 2 || false === strpos($sortedPageOne['html'], 'Online Product') || false === strpos($sortedPageTwo['html'], 'Escaping Fixture')) {
    fwrite(STDERR, "FAIL product price sort or AJAX pagination returned the wrong order/page data\\n");
    $failed++;
}
$skuSearch = $class::filter_products(['query' => 'SKU-QA-81-ON', 'category' => $category->slug, 'sale' => 'online']);
if ((int) $skuSearch['total'] !== 1 || false === strpos($skuSearch['html'], 'Online Product')) {
    fwrite(STDERR, "FAIL product identifier search did not match the SKU\n");
    $failed++;
}
$injectionSearch = $class::filter_products(['query' => "' OR 1=1 --", 'per_page' => 24]);
if ((int) $injectionSearch['total'] > 0) {
    fwrite(STDERR, "FAIL SQL-like input bypassed the product search filter\n");
    $failed++;
}
$unsafeResult = $class::filter_products(['query' => 'Escaping Fixture', 'category' => $category->slug]);
if (false !== strpos($unsafeResult['html'], '<script>')) {
    fwrite(STDERR, "FAIL product result HTML emitted unescaped title markup\n");
    $failed++;
}
$invalidAttribute = $class::filter_products(['ip' => ['Unknown IP value']]);
$invalidSale = $class::filter_products(['sale' => 'unknown-mode']);
if ((int) $invalidAttribute['total'] !== 0 || (int) $invalidSale['total'] !== 0) {
    fwrite(STDERR, "FAIL invalid product attribute or sale filter was ignored instead of returning no results\n");
    $failed++;
}
$invalidSort = $class::build_product_query_args(['orderby' => 'price;DROP TABLE posts']);
if (($invalidSort['orderby'] ?? null) !== ['menu_order' => 'ASC', 'date' => 'DESC']) {
    fwrite(STDERR, "FAIL unsupported product sort was not rejected\n");
    $failed++;
}
$attributeProjectFilters = ['query' => 'Project Probe', 'type' => $typeUrban->name, 'city' => $cityZagreb->slug, 'orderby' => 'popular', 'per_page' => 9];
$projectResult = $class::filter_projects($attributeProjectFilters);
if ((int) $projectResult['total'] !== 1 || false === strpos($projectResult['html'], 'Urban Project Probe') || false !== strpos($projectResult['html'], 'Bridge Project Probe')) {
    fwrite(STDERR, "FAIL composable project type/city/text filters returned the wrong results\n");
    $failed++;
}
$popularProjects = $class::filter_projects(['query' => 'Project Probe', 'orderby' => 'popular', 'per_page' => 9]);
$bridgePosition = strpos($popularProjects['html'], 'Bridge Project Probe');
$urbanPosition = strpos($popularProjects['html'], 'Urban Project Probe');
if ((int) $popularProjects['total'] !== 2 || false === $bridgePosition || false === $urbanPosition || $bridgePosition > $urbanPosition) {
    fwrite(STDERR, "FAIL project popularity sort returned the wrong order\\n");
    $failed++;
}
$projectArgs = $class::build_project_query_args(['type' => $typeUrban->slug, 'city' => $cityZagreb->slug, 'orderby' => 'popular', 'per_page' => 999]);
if (($projectArgs['post_type'] ?? '') !== 'projects' || (int) ($projectArgs['posts_per_page'] ?? 0) !== 24 || ($projectArgs['meta_key'] ?? '') !== 'number2-viewc' || !isset($projectArgs['tax_query'])) {
    fwrite(STDERR, "FAIL project query did not clamp page size or whitelist taxonomy/sort filters\n");
    $failed++;
}
$globalSearch = $class::live_search('Probe');
if ((int) ($globalSearch['product_count'] ?? 0) < 2 || (int) ($globalSearch['project_count'] ?? 0) < 2 || false === strpos($globalSearch['products_html'], 'Online Product') || false === strpos($globalSearch['projects_html'], 'Urban Project Probe')) {
    fwrite(STDERR, "FAIL global live search did not return both product and project results\n");
    $failed++;
}

// Exercise real dispatch callbacks while replacing wp_die with a capturable test handler.
if (!defined('DOING_AJAX')) {
    define('DOING_AJAX', true);
}
if (!function_exists('ese_task81_capture_wp_die')) {
    function ese_task81_capture_wp_die($message = '', $title = '', $args = [])
    {
        throw new RuntimeException('ese-task81-json-captured');
    }
}
add_filter('wp_die_ajax_handler', 'ese_task81_capture_wp_die');
$captureRequest = static function (array $post, string $method = 'POST') use ($class): array {
    $_SERVER['REQUEST_METHOD'] = $method;
    $_POST = $post;
    ob_start();
    try {
        $class::dispatch();
    } catch (RuntimeException $exception) {
        if ('ese-task81-json-captured' !== $exception->getMessage()) {
            throw $exception;
        }
    }
    $output = (string) ob_get_clean();
    $decoded = json_decode($output, true);
    return is_array($decoded) ? $decoded : [];
};
$validDispatch = $captureRequest(['action' => 'ese_filter_products', 'nonce' => $validNonce, 'query' => 'SKU-QA-81-ON', 'category' => $category->slug, 'sale' => 'online']);
if (empty($validDispatch['success']) || (int) ($validDispatch['data']['total'] ?? 0) !== 1) {
    fwrite(STDERR, "FAIL valid nonce public product AJAX dispatch returned an invalid JSON response\n");
    $failed++;
}
$invalidDispatch = $captureRequest(['action' => 'ese_filter_projects', 'nonce' => 'bad', 'query' => 'Probe']);
if (!empty($invalidDispatch['success']) || empty($invalidDispatch['data']['message']) || false === strpos((string) $invalidDispatch['data']['message'], 'نامعتبر')) {
    fwrite(STDERR, "FAIL invalid nonce was not rejected before query execution\n");
    $failed++;
}
$getDispatch = $captureRequest(['action' => 'ese_live_search', 'nonce' => $validNonce, 'query' => 'Probe'], 'GET');
if (!empty($getDispatch['success']) || empty($getDispatch['data']['message']) || false === strpos((string) $getDispatch['data']['message'], 'POST')) {
    fwrite(STDERR, "FAIL non-POST AJAX request was not rejected\n");
    $failed++;
}

$measurements = [];
for ($iteration = 0; $iteration < 5; $iteration++) {
    $timedResult = $class::filter_products(['query' => 'SKU-QA-81-ON', 'category' => $category->slug, 'sale' => 'online']);
    $measurements[] = (float) ($timedResult['duration_ms'] ?? 9999);
}
$maxDuration = max($measurements);
if ($maxDuration >= 200) {
    fwrite(STDERR, "FAIL fixture server-side product filter latency exceeded 200ms: {$maxDuration}ms\n");
    $failed++;
}

foreach (array_reverse($createdPosts) as $postId) {
    wp_delete_post((int) $postId, true);
}
foreach (array_reverse($createdTerms) as [$termId, $taxonomy]) {
    wp_delete_term((int) $termId, $taxonomy);
}
$_POST = [];
if (is_file((getenv('WP_TEST_DIR') ?: $root . '/node_modules/.cache/ese-wp-test') . '/wp-debug.log') && filesize((getenv('WP_TEST_DIR') ?: $root . '/node_modules/.cache/ese-wp-test') . '/wp-debug.log') > 0) {
    fwrite(STDERR, "FAIL WP_DEBUG log recorded an AJAX/filter warning\n");
    $failed++;
}
if ($failed) {
    exit(1);
}
fwrite(STDOUT, sprintf("PASS live search and composable product/project AJAX filters, nonce validation, escaping, safe queries, and fixture latency (max %.2fms; excludes network)\n", $maxDuration));
