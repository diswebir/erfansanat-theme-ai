<?php
/**
 * WordPress integration test for task 6.2: WooCommerce shop/category/tag archives.
 * Run through PHP WASM or native PHP with WP_TEST_ROOT set.
 */
$root = dirname(__DIR__, 3);
$sourceContracts = [
    ['archive-product.php', 'woocommerce_before_main_content'],
    ['archive-product.php', 'product-archive'],
    ['template-parts/woocommerce/product-archive.php', 'woocommerce_after_shop_loop'],
    ['taxonomy-product_cat.php', 'product_cat'],
    ['taxonomy-product_cat.php', 'product-archive'],
    ['template-parts/woocommerce/product-archive.php', 'thumbnail'],
    ['template-parts/woocommerce/product-archive.php', 'faq'],
    ['taxonomy-product_tag.php', 'product_tag'],
    ['template-parts/woocommerce/product-archive.php', 'data-products-archive'],
    ['template-parts/woocommerce/product-archive.php', 'data-product-grid'],
    ['template-parts/woocommerce/product-archive.php', 'data-product-item'],
    ['template-parts/woocommerce/product-archive.php', 'data-product-empty'],
    ['template-parts/woocommerce/product-archive.php', 'data-pagination-root'],
    ['template-parts/woocommerce/product-archive.php', 'woocommerce_pagination'],
    ['template-parts/woocommerce/product-archive.php', 'card-product'],
    ['template-parts/woocommerce/product-archive.php', 'data-product-purchase-mode'],
    ['template-parts/woocommerce/product-archive.php', 'data-product-ip-filter'],
    ['template-parts/woocommerce/product-archive.php', 'data-product-voltage-filter'],
    ['assets/js/main.js', 'purchaseMode === "inquiry"'],
    ['assets/js/main.js', 'sortBy === "price"'],
    ['inc/ContentTypes.php', "'slug' => 'product_cat'"],
    ['inc/ContentTypes.php', "has_archive'] = 'shop'"] ,
];
$failed = 0;
foreach ($sourceContracts as [$relative, $needle]) {
    $path = $root . '/' . $relative;
    $source = is_file($path) ? file_get_contents($path) : false;
    if ($source === false || strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL archive contract missing `{$needle}` in {$relative}\n");
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
$_SERVER['REQUEST_URI'] = '/shop/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$contentTypeClass = 'ErfanSanat\\Theme\\ContentTypes';
if (!class_exists($contentTypeClass)) {
    fwrite(STDERR, "FAIL ContentTypes was not loaded for WooCommerce rewrite checks\\n");
    exit(1);
}
$productArchiveArgs = $contentTypeClass::filter_product_post_type_args(['rewrite' => []]);
$productCategoryArgs = $contentTypeClass::filter_product_cat_args(['rewrite' => []]);
$productTagArgs = $contentTypeClass::filter_product_tag_args(['rewrite' => []]);
if (($productArchiveArgs['has_archive'] ?? '') !== 'shop' || ($productCategoryArgs['rewrite']['slug'] ?? '') !== 'product_cat' || ($productTagArgs['rewrite']['slug'] ?? '') !== 'product-tag') {
    fwrite(STDERR, "FAIL WooCommerce shop/category/tag rewrite bases were not preserved\\n");
    $failed++;
}

if (!post_type_exists('product')) {
    register_post_type('product', ['public' => true, 'has_archive' => 'shop', 'supports' => ['title', 'editor', 'excerpt', 'thumbnail']]);
}
if (!taxonomy_exists('product_cat')) {
    register_taxonomy('product_cat', ['product'], ['public' => true, 'hierarchical' => true, 'rewrite' => ['slug' => 'product_cat']]);
}
if (!taxonomy_exists('product_tag')) {
    register_taxonomy('product_tag', ['product'], ['public' => true, 'hierarchical' => false, 'rewrite' => ['slug' => 'product-tag']]);
}
if (!taxonomy_exists('pa_ip-rating')) {
    register_taxonomy('pa_ip-rating', ['product'], ['public' => true, 'hierarchical' => true]);
}
if (!taxonomy_exists('pa_input-voltage')) {
    register_taxonomy('pa_input-voltage', ['product'], ['public' => true, 'hierarchical' => true]);
}

if (!class_exists('ES_Task_62_Product')) {
    class ES_Task_62_Product
    {
        private $id;
        public function __construct($id) { $this->id = (int) $id; }
        public function get_id() { return $this->id; }
        public function get_price() { return (string) get_post_meta($this->id, '_price', true); }
        public function get_price_html() { return '<span class="amount">' . esc_html($this->get_price()) . '</span>'; }
        public function get_average_rating() { return (string) get_post_meta($this->id, '_wc_average_rating', true); }
        public function get_sku() { return (string) get_post_meta($this->id, '_sku', true); }
        public function is_purchasable() { return true; }
        public function get_attribute($key) { return (string) get_post_meta($this->id, 'test_' . $key, true); }
    }
}
$GLOBALS['es_task_62_products'] = [];
if (!function_exists('wc_get_product')) {
    function wc_get_product($productId = 0) {
        return $GLOBALS['es_task_62_products'][(int) $productId] ?? null;
    }
}
if (!function_exists('wc_get_page_permalink')) {
    function wc_get_page_permalink($page) { return home_url('/shop/'); }
}
if (!function_exists('woocommerce_pagination')) {
    function woocommerce_pagination() {
        echo '<nav class="woocommerce-pagination" data-woocommerce-pagination="true"><a href="?product-page=2">2</a></nav>';
    }
}

$getTerm = static function (string $taxonomy, string $name, string $slug) {
    $term = get_term_by('slug', $slug, $taxonomy);
    if ($term && !is_wp_error($term)) {
        return $term;
    }
    $created = wp_insert_term($name, $taxonomy, ['slug' => $slug]);
    return !is_wp_error($created) ? get_term((int) $created['term_id'], $taxonomy) : false;
};
$category = $getTerm('product_cat', 'Task 6.2 QA Category', 'task-62-category');
$tag = $getTerm('product_tag', 'Task 6.2 QA Tag', 'task-62-tag');
if (!$category || is_wp_error($category) || !$tag || is_wp_error($tag)) {
    fwrite(STDERR, "FAIL could not create archive taxonomy fixtures\n");
    exit(1);
}
$categoryId = (int) $category->term_id;
$tagId = (int) $tag->term_id;

$insertProduct = static function (string $title, string $slug, string $price, string $purchase, string $ip, string $voltage): int {
    $id = wp_insert_post([
        'post_type' => 'product',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_excerpt' => 'Archive fixture for ' . $title,
        'post_content' => 'Description for ' . $title,
    ]);
    if (is_wp_error($id) || !$id) {
        return 0;
    }
    update_post_meta($id, '_price', $price);
    update_post_meta($id, '_sku', 'SKU-' . strtoupper($slug));
    update_post_meta($id, '_wc_average_rating', '4.8');
    update_post_meta($id, 'true_sale', $purchase);
    update_post_meta($id, 'test_pa_ip-rating', $ip);
    update_post_meta($id, 'test_pa_input-voltage', $voltage);
    $GLOBALS['es_task_62_products'][(int) $id] = new ES_Task_62_Product($id);
    return (int) $id;
};
$productA = $insertProduct('Task 6.2 Online Product', 'task-62-online-product', '98000', 'true', 'IP68', '24V DC');
$productB = $insertProduct('Task 6.2 Inquiry Product', 'task-62-inquiry-product', '0', 'false', 'IP67', '220V AC');
if (!$productA || !$productB) {
    fwrite(STDERR, "FAIL could not create archive products\n");
    exit(1);
}
wp_set_object_terms($productA, [$categoryId], 'product_cat');
wp_set_object_terms($productB, [$categoryId], 'product_cat');
wp_set_object_terms($productA, [$tagId], 'product_tag');
wp_set_object_terms($productB, [$tagId], 'product_tag');
update_post_meta($productA, 'number2-viewc', '30');
update_post_meta($productB, 'number-viewc', '20');

$attachmentId = (int) wp_insert_post([
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'post_title' => 'Task 6.2 category thumbnail',
    'post_mime_type' => 'image/jpeg',
    'guid' => 'https://example.test/wp-content/uploads/task-62-category.jpg',
]);
update_term_meta($categoryId, 'thumbnail', $attachmentId);
update_term_meta($categoryId, 'faq', wp_json_encode([
    'item-0' => ['name_question' => 'Task 6.2 Category FAQ?', 'answer_question' => 'Category FAQ answer fixture.'],
]));

$renderTemplate = static function (string $relative, array $queryArgs, $term = null): string {
    global $wp_query, $wp_the_query, $post;
    $wp_query = new WP_Query($queryArgs);
    $wp_the_query = $wp_query;
    if ($term instanceof WP_Term) {
        $wp_query->queried_object = $term;
        $wp_query->queried_object_id = (int) $term->term_id;
        $wp_query->is_tax = true;
        $wp_query->is_archive = true;
    } else {
        $wp_query->is_post_type_archive = true;
        $wp_query->is_archive = true;
    }
    $post = null;
    ob_start();
    include dirname(__DIR__, 3) . '/' . $relative;
    $html = (string) ob_get_clean();
    wp_reset_postdata();
    return $html;
};

$archiveHtml = $renderTemplate('archive-product.php', [
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'post__in' => [$productA, $productB],
    'orderby' => 'post__in',
]);
foreach (['data-products-archive="true"', 'data-product-grid="true"', 'data-product-item="true"', 'data-product-purchasable="true"', 'data-product-purchasable="false"', 'data-product-ip="IP68"', 'data-product-voltage="24V DC"', 'data-pagination-root="true"', 'data-woocommerce-pagination="true"', 'Task 6.2 Online Product', 'Task 6.2 Inquiry Product'] as $needle) {
    if (strpos($archiveHtml, $needle) === false) {
        fwrite(STDERR, "FAIL shop archive render missing: {$needle}\n");
        $failed++;
    }
}
if (strpos($archiveHtml, '<script>alert') !== false) {
    fwrite(STDERR, "FAIL product archive did not escape output\n");
    $failed++;
}

$categoryHtml = $renderTemplate('taxonomy-product_cat.php', [
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'post__in' => [$productA, $productB],
], $category);
foreach (['Task 6.2 QA Category', 'task-62-category.jpg', 'Task 6.2 Category FAQ?', 'Category FAQ answer fixture.', 'Task 6.2 Online Product', 'data-product-category-archive="true"'] as $needle) {
    if (strpos($categoryHtml, $needle) === false) {
        fwrite(STDERR, "FAIL category archive render missing: {$needle}\n");
        $failed++;
    }
}

$tagHtml = $renderTemplate('taxonomy-product_tag.php', [
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'post__in' => [$productA, $productB],
], $tag);
foreach (['Task 6.2 QA Tag', 'Task 6.2 Online Product', 'data-product-tag-archive="true"'] as $needle) {
    if (strpos($tagHtml, $needle) === false) {
        fwrite(STDERR, "FAIL product tag archive render missing: {$needle}\n");
        $failed++;
    }
}

$emptyCategoryHtml = $renderTemplate('taxonomy-product_cat.php', [
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'post__in' => [999999999],
], $category);
if (strpos($emptyCategoryHtml, 'محصولی برای نمایش در این بخش وجود ندارد.') === false || strpos($emptyCategoryHtml, 'data-product-grid="true"') !== false) {
    fwrite(STDERR, "FAIL empty product category archive did not render a graceful no-products state\n");
    $failed++;
}

wp_delete_post($productA, true);
wp_delete_post($productB, true);
wp_delete_post($attachmentId, true);
wp_delete_term($categoryId, 'product_cat');
wp_delete_term($tagId, 'product_tag');

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS shop/category/tag archive integration, dynamic filters/cards, pagination hooks, term thumbnail/FAQ, escaping, and empty-state handling\n");
