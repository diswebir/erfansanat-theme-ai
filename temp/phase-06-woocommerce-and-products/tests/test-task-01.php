<?php
/**
 * WordPress integration test for task 6.1: dynamic WooCommerce single-product page.
 * Run through PHP WASM or native PHP with WP_TEST_ROOT set.
 */
$root = dirname(__DIR__, 3);
$templatePath = $root . '/single-product.php';
$source = is_file($templatePath) ? file_get_contents($templatePath) : false;
$requiredSource = [
    'true_sale',
    'galerry_produ',
    'get_product_gallery_ids',
    'woocommerce_template_single_add_to_cart',
    'comments_template',
    'wc_get_related_products',
    'wc_get_template',
    'project-ids',
    'usage_countr',
    'product-feature',
    'product-feature2_copy',
    'usage_product',
    'data-gallery-root',
    'data-gallery-lightbox',
    'data-prod-tabs-container',
    'data-faq-list',
];
$attributeSlugs = [
    'pa_after-sales-support', 'pa_beam-angle', 'pa_body-material', 'pa_brand',
    'pa_bulb-type', 'pa_cable-material', 'pa_certifications', 'pa_color',
    'pa_diameter', 'pa_diffuser-color', 'pa_input-voltage', 'pa_intensity',
    'pa_ip-rating', 'pa_led-chip-type', 'pa_lifespan', 'pa_size', 'pa_warranty', 'pa_wattage',
];
$failed = 0;
if ($source === false) {
    fwrite(STDERR, "FAIL missing single-product.php\n");
    exit(1);
}
foreach (array_merge($requiredSource, $attributeSlugs) as $needle) {
    if (strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL single-product template contract missing: {$needle}\n");
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
$_SERVER['REQUEST_URI'] = '/product/task-61-product/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$productMetaClass = 'ErfanSanat\\Theme\\Admin\\ProductMetaboxes';
if (!class_exists($productMetaClass)) {
    fwrite(STDERR, "FAIL ProductMetaboxes was not loaded\n");
    exit(1);
}
if (!post_type_exists('product')) {
    register_post_type('product', ['public' => true, 'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'comments']]);
}
if (!post_type_exists('projects')) {
    register_post_type('projects', ['public' => true, 'supports' => ['title']]);
}
if (!taxonomy_exists('product_cat')) {
    register_taxonomy('product_cat', ['product'], ['public' => true, 'hierarchical' => true]);
}
if (!taxonomy_exists('project_type')) {
    register_taxonomy('project_type', ['projects'], ['public' => true, 'hierarchical' => true]);
}
if (!taxonomy_exists('pa_wattage')) {
    register_taxonomy('pa_wattage', ['product'], ['public' => true, 'hierarchical' => true]);
}

if (!class_exists('ES_Task_61_Product')) {
    class ES_Task_61_Product
    {
        public $id;
        public $attributes = [];
        public function __construct($id, array $attributes = []) { $this->id = (int) $id; $this->attributes = $attributes; }
        public function get_id() { return $this->id; }
        public function get_name() { return get_the_title($this->id); }
        public function get_sku() { return (string) get_post_meta($this->id, '_sku', true); }
        public function get_price_html() { return '<span class="amount">۲,۴۰۰,۰۰۰ تومان</span>'; }
        public function get_image_id() { return (int) get_post_thumbnail_id($this->id); }
        public function get_gallery_image_ids() { return []; }
        public function get_attribute($name) { return $this->attributes[$name] ?? ''; }
        public function get_average_rating() { return '4.75'; }
        public function get_review_count() { return 2; }
        public function get_type() { return 'variable'; }
        public function is_type($type) { return $type === 'variable'; }
        public function is_purchasable() { return true; }
        public function is_in_stock() { return true; }
    }
}
$GLOBALS['es_task_61_wc_product'] = null;
$GLOBALS['es_task_61_add_to_cart_calls'] = 0;
$GLOBALS['es_task_61_review_template_calls'] = 0;
$GLOBALS['es_task_61_related_ids'] = [];
if (!function_exists('wc_get_product')) {
    function wc_get_product($productId = 0) {
        $product = $GLOBALS['es_task_61_wc_product'];
        return $product && $product->get_id() === (int) $productId ? $product : null;
    }
}
if (!function_exists('woocommerce_template_single_add_to_cart')) {
    function woocommerce_template_single_add_to_cart() {
        $GLOBALS['es_task_61_add_to_cart_calls']++;
        echo '<form class="variations_form"><button type="submit">WooCommerce variable purchase</button></form>';
    }
}
if (!function_exists('woocommerce_template_single_price')) {
    function woocommerce_template_single_price() {
        $product = $GLOBALS['es_task_61_wc_product'];
        if ($product) {
            echo wp_kses_post($product->get_price_html());
        }
    }
}
if (!function_exists('woocommerce_template_single_rating')) {
    function woocommerce_template_single_rating() { echo '<div class="woocommerce-product-rating">4.75</div>'; }
}
if (!function_exists('wc_get_related_products')) {
    function wc_get_related_products($productId, $limit = 5, $excludedIds = []) {
        return array_slice(array_values(array_diff($GLOBALS['es_task_61_related_ids'], [(int) $productId], $excludedIds)), 0, (int) $limit);
    }
}
if (!function_exists('wc_get_template')) {
    function wc_get_template($templateName, $args = [], $templatePath = '', $defaultPath = '') {
        if ('single-product-reviews.php' === $templateName) {
            $GLOBALS['es_task_61_review_template_calls']++;
            echo '<div class="woocommerce-reviews-test">WooCommerce reviews test</div>';
        }
    }
}

$insertPost = static function (string $type, string $title, string $slug, string $content = ''): int {
    $id = wp_insert_post([
        'post_type' => $type,
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => $content,
        'comment_status' => 'open',
    ]);
    return is_wp_error($id) ? 0 : (int) $id;
};
$productId = $insertPost('product', '<script>alert(1)</script> Product QA', 'task-61-product', '<p>Long description with <strong>safe markup</strong>.</p>');
$relatedProductId = $insertPost('product', 'Related Product QA', 'task-61-related-product');
$projectId = $insertPost('projects', 'Related Lighting Project QA', 'task-61-related-project');
$usageTerm = get_term_by('slug', 'task-61-project-type', 'project_type');
if (!$usageTerm) {
    $usageTermResult = wp_insert_term('Task 6.1 Project Type QA', 'project_type', ['slug' => 'task-61-project-type']);
    $usageTerm = !is_wp_error($usageTermResult) ? get_term((int) $usageTermResult['term_id'], 'project_type') : false;
}
$usageTermId = $usageTerm && !is_wp_error($usageTerm) ? (int) $usageTerm->term_id : 0;
if (!$productId || !$relatedProductId || !$projectId || !$usageTermId) {
    fwrite(STDERR, "FAIL could not create product/project integration fixtures\n");
    exit(1);
}

$attachmentIds = [];
foreach (['task-61-gallery-1.jpg', 'task-61-gallery-2.jpg', 'task-61-featured.jpg'] as $filename) {
    $attachmentIds[] = (int) wp_insert_post([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_title' => $filename,
        'post_mime_type' => 'image/jpeg',
        'guid' => 'https://example.test/wp-content/uploads/' . $filename,
    ]);
}
update_post_meta($productId, '_thumbnail_id', $attachmentIds[2]);
update_post_meta($productId, 'galerry_produ', $attachmentIds[0] . ',' . $attachmentIds[1]);
update_post_meta($productId, 'true_sale', 'true');
update_post_meta($productId, 'zireonvan', '<script>unsafe subtitle</script>Industrial lighting element');
update_post_meta($productId, 'english_titl', 'Industrial Light Element');
update_post_meta($productId, 'before-sale-c', 'Check the required beam angle before ordering.');
update_post_meta($productId, 'gs1_id', '6260491807260');
update_post_meta($productId, 'national_code', '2901140800601');
update_post_meta($productId, 'hs_id', 'HS-TASK-61');
update_post_meta($productId, 'isic_id', 'ISIC-TASK-61');
update_post_meta($productId, 'cpc_id', 'CPC-TASK-61');
update_post_meta($productId, 'product_id', 'ESE-TASK-61');
update_post_meta($productId, 'product-catalog-file', 'https://example.test/catalog.pdf');
update_post_meta($productId, 'project-ids', wp_json_encode([$projectId]));
update_post_meta($productId, 'usage_countr', wp_json_encode([$usageTermId]));
update_post_meta($productId, 'product-feature', wp_json_encode(['item-0' => ['add-new-feature' => 'Test feature'] ]));
update_post_meta($productId, 'product-feature2_copy', wp_json_encode(['item-0' => ['add-new-feature' => 'Warranty', 'vaule_of_variable' => '24 months']]));
update_post_meta($productId, 'usage_product', wp_json_encode(['item-0' => ['add-new-usage' => 'Bridge and facade lighting']]));
update_post_meta($productId, 'faq', wp_json_encode(['item-0' => ['name_question' => 'Is it weather resistant?', 'answer_question' => 'Yes, it is rated for outdoor use.']]));
$GLOBALS['es_task_61_wc_product'] = new ES_Task_61_Product($productId, ['pa_wattage' => '36 W', 'pa_ip-rating' => 'IP68']);
$GLOBALS['es_task_61_related_ids'] = [$relatedProductId];

$renderProduct = static function (int $id): string {
    global $post, $product;
    $post = get_post($id);
    setup_postdata($post);
    $product = $GLOBALS['es_task_61_wc_product'];
    ob_start();
    include dirname(__DIR__, 3) . '/single-product.php';
    $html = (string) ob_get_clean();
    wp_reset_postdata();
    return $html;
};
$html = $renderProduct($productId);
$expectedOutput = [
    'Industrial lighting element',
    'Industrial Light Element',
    'Check the required beam angle before ordering.',
    'ESE-TASK-61',
    '6260491807260',
    '2901140800601',
    'HS-TASK-61',
    'ISIC-TASK-61',
    'CPC-TASK-61',
    'https://example.test/catalog.pdf',
    '36 W',
    'IP68',
    'Task 6.1 Project Type QA',
    'Test feature',
    '24 months',
    'Bridge and facade lighting',
    'Is it weather resistant?',
    'Related Lighting Project QA',
    'Related Product QA',
    'woocommerce-product-rating',
    'variations_form',
    'data-gallery-root="true"',
    'data-gallery-lightbox="true"',
    'data-prod-tab-panel="reviews"',
    'WooCommerce reviews test',
    'data-faq-list="true"',
];
foreach ($expectedOutput as $needle) {
    if (strpos($html, $needle) === false) {
        fwrite(STDERR, "FAIL product page render omitted expected output: {$needle}\n");
        $failed++;
    }
}
if (strpos($html, '<script>alert(1)</script>') !== false || strpos($html, '<script>unsafe subtitle</script>') !== false) {
    fwrite(STDERR, "FAIL untrusted title/subtitle markup was rendered\n");
    $failed++;
}
if (substr_count($html, 'data-gallery-thumb=') < 2 || strpos($html, 'task-61-gallery-1.jpg') === false) {
    fwrite(STDERR, "FAIL legacy product gallery images were not used in gallery markup\n");
    $failed++;
}
if ($GLOBALS['es_task_61_add_to_cart_calls'] !== 1) {
    fwrite(STDERR, "FAIL true_sale did not invoke the standard WooCommerce add-to-cart template once\n");
    $failed++;
}
if ($GLOBALS['es_task_61_review_template_calls'] !== 1) {
    fwrite(STDERR, "FAIL WooCommerce product review template was not invoked\n");
    $failed++;
}

$gallery = $productMetaClass::get_product_gallery_ids($productId);
if ($gallery !== [$attachmentIds[0], $attachmentIds[1]]) {
    fwrite(STDERR, "FAIL exact legacy galerry_produ IDs were not used first\n");
    $failed++;
}
delete_post_meta($productId, 'galerry_produ');
update_post_meta($productId, '_product_image_gallery', (string) $attachmentIds[1]);
if ($productMetaClass::get_product_gallery_ids($productId) !== [$attachmentIds[1]]) {
    fwrite(STDERR, "FAIL standard WooCommerce gallery fallback failed\n");
    $failed++;
}
delete_post_meta($productId, '_product_image_gallery');
update_post_meta($productId, 'gallery-filed', (string) $attachmentIds[0]);
if ($productMetaClass::get_product_gallery_ids($productId) !== [$attachmentIds[0]]) {
    fwrite(STDERR, "FAIL gallery-filed legacy fallback failed\n");
    $failed++;
}
delete_post_meta($productId, 'gallery-filed');
update_post_meta($productId, 'image-2', (string) $attachmentIds[0]);
update_post_meta($productId, 'image-3', (string) $attachmentIds[1]);
if ($productMetaClass::get_product_gallery_ids($productId) !== [$attachmentIds[0], $attachmentIds[1]]) {
    fwrite(STDERR, "FAIL image-2 through image-6 legacy gallery fallback failed\n");
    $failed++;
}

update_post_meta($productId, 'true_sale', 'false');
update_post_meta($productId, 'faq', wp_json_encode([]));
$GLOBALS['es_task_61_wc_product'] = new ES_Task_61_Product($productId);
$quoteHtml = $renderProduct($productId);
if (strpos($quoteHtml, 'tel:09137976915') === false || strpos($quoteHtml, 'variations_form') !== false) {
    fwrite(STDERR, "FAIL false true_sale did not render contact/quote path exclusively\n");
    $failed++;
}
if ($GLOBALS['es_task_61_add_to_cart_calls'] !== 1) {
    fwrite(STDERR, "FAIL false true_sale invoked the WooCommerce purchase template\n");
    $failed++;
}
if (strpos($quoteHtml, 'data-faq-list="true"') !== false) {
    fwrite(STDERR, "FAIL empty product FAQ section was not hidden\n");
    $failed++;
}

wp_delete_post($productId, true);
wp_delete_post($relatedProductId, true);
wp_delete_post($projectId, true);
wp_delete_term($usageTermId, 'project_type');
foreach ($attachmentIds as $attachmentId) {
    wp_delete_post($attachmentId, true);
}

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS single-product render, legacy/Woo gallery fallbacks, attributes, relations, FAQs, reviews, and true_sale purchase/quote branches\n");
