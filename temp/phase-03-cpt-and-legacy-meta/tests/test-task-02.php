<?php
/**
 * Integration contract for task 3.2: legacy-compatible WooCommerce product metaboxes.
 * Run through PHP WASM or native PHP after installing the disposable WordPress test site.
 */
$root = dirname(__DIR__, 3);
$source = $root . '/inc/Admin/ProductMetaboxes.php';
$checks = [
    [$source, 'english_titl'],
    [$source, 'galerry_produ'],
    [$source, 'vaule_of_variable'],
    [$source, 'product-catalog-file'],
    [$source, 'before-sale-c'],
    [$source, 'usage_countr'],
    [$source, 'wp_verify_nonce'],
    [$source, 'current_user_can'],
    [$source, 'register_post_meta'],
];
$failed = 0;
foreach ($checks as [$path, $needle]) {
    $contents = is_file($path) ? file_get_contents($path) : false;
    if ($contents === false || strpos($contents, $needle) === false) {
        fwrite(STDERR, "FAIL product metabox contract missing: {$needle} in {$path}\n");
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
$_SERVER['REQUEST_URI'] = '/wp-admin/post.php?action=edit';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/wp-admin/post.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define('WP_ADMIN', true);
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$class = 'ErfanSanat\Theme\Admin\ProductMetaboxes';
if (!class_exists($class)) {
    fwrite(STDERR, "FAIL ProductMetaboxes class was not loaded\n");
    exit(1);
}
if (!has_action('save_post_product', [$class, 'save_post'])) {
    fwrite(STDERR, "FAIL secure product save handler was not registered\n");
    $failed++;
}
$metaSchema = $class::meta_schema();
$requiredKeys = [
    'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id',
    'zireonvan', 'english_titl', 'before-sale-c', 'true_sale', 'product-catalog-file',
    'galerry_produ', 'project-ids', 'usage_countr', 'product-feature',
    'product-feature2_copy', 'usage_product', 'faq',
];
foreach ($requiredKeys as $key) {
    if (!isset($metaSchema[$key])) {
        fwrite(STDERR, "FAIL product meta schema omitted legacy key {$key}\n");
        $failed++;
    }
}

$arrayInput = ['item-0' => ['add-new-feature' => '<b>گارانتی</b>']];
$jsonInput = json_encode($arrayInput, JSON_UNESCAPED_UNICODE);
$decodedArray = $class::decode_legacy_value($arrayInput);
$decodedJson = $class::decode_legacy_value($jsonInput);
if (($decodedArray['item-0']['add-new-feature'] ?? '') !== '<b>گارانتی</b>' || ($decodedJson['item-0']['add-new-feature'] ?? '') !== '<b>گارانتی</b>') {
    fwrite(STDERR, "FAIL serialized/JSON repeater values were not decoded\n");
    $failed++;
}
$jsonRoundTrip = $class::encode_legacy_value(['item-0' => ['add-new-feature' => 'ویژگی جدید']], $jsonInput);
if (!is_string($jsonRoundTrip) || json_decode($jsonRoundTrip, true)['item-0']['add-new-feature'] !== 'ویژگی جدید') {
    fwrite(STDERR, "FAIL repeater JSON storage shape was not preserved\n");
    $failed++;
}
$serializedInput = serialize($arrayInput);
$serializedRoundTrip = $class::encode_legacy_value(['item-0' => ['add-new-feature' => 'ویژگی تازه']], $serializedInput);
if (!is_string($serializedRoundTrip) || maybe_unserialize($serializedRoundTrip)['item-0']['add-new-feature'] !== 'ویژگی تازه') {
    fwrite(STDERR, "FAIL serialized repeater storage shape was not preserved\n");
    $failed++;
}

$clean = $class::sanitize_product_payload(
    [
        'english_titl' => '<b>LED Light</b>',
        'true_sale' => 'invalid',
        'product-catalog-file' => 'javascript:alert(1)',
        'product-feature' => [
            'item-0' => ['add-new-feature' => '<b>گارانتی ۲۴ ماهه</b>'],
        ],
        'product-feature2_copy' => [
            'item-0' => ['add-new-feature' => 'گارانتی', 'vaule_of_variable' => '<b>۲۴ ماه</b>'],
        ],
        'unknown_key' => 'discard',
    ]
);
if (($clean['english_titl'] ?? '') !== 'LED Light' || ($clean['true_sale'] ?? '') !== 'false') {
    fwrite(STDERR, "FAIL simple product fields were not safely sanitized\n");
    $failed++;
}
if (($clean['product-catalog-file'] ?? null) !== '') {
    fwrite(STDERR, "FAIL unsafe catalog URL was accepted\n");
    $failed++;
}
if (($clean['product-feature']['item-0']['add-new-feature'] ?? $clean['product-feature'][0]['add-new-feature'] ?? '') !== 'گارانتی ۲۴ ماهه') {
    fwrite(STDERR, "FAIL single-field product repeater was not sanitized\n");
    $failed++;
}
if (($clean['product-feature2_copy']['item-0']['vaule_of_variable'] ?? $clean['product-feature2_copy'][0]['vaule_of_variable'] ?? '') !== '۲۴ ماه') {
    fwrite(STDERR, "FAIL two-column feature repeater typo-key value was not preserved\n");
    $failed++;
}
if (isset($clean['unknown_key'])) {
    fwrite(STDERR, "FAIL unknown product meta keys were not discarded\n");
    $failed++;
}

if (!post_type_exists('product')) {
    register_post_type('product', ['public' => true, 'show_ui' => true, 'supports' => ['title']]);
}
$postId = wp_insert_post(['post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'Product Metabox QA']);
if (is_wp_error($postId) || !$postId) {
    fwrite(STDERR, "FAIL could not create product metabox QA post\n");
    exit(1);
}
update_post_meta($postId, 'product-feature', $jsonInput);
update_post_meta($postId, '_product_image_gallery', '17,18');
$storedGallery = $class::get_product_gallery_ids($postId);
if ($storedGallery !== [17, 18]) {
    fwrite(STDERR, "FAIL standard WooCommerce gallery was not used as a legacy fallback\n");
    $failed++;
}
ob_start();
$class::render_meta_box(get_post($postId));
$editor = ob_get_clean();
foreach (['es_product_meta_nonce', 'english_titl', 'galerry_produ', 'vaule_of_variable', 'data-product-repeater-template'] as $needle) {
    if (strpos($editor, $needle) === false) {
        fwrite(STDERR, "FAIL product editor is missing {$needle}\n");
        $failed++;
    }
}

$nonce = wp_create_nonce('es_save_product_meta');
$_POST['es_product_meta_nonce'] = $nonce;
$_POST['es_product_meta'] = [
    'english_titl' => 'New product title',
    'product-feature' => ['0' => ['add-new-feature' => 'متن ذخیره‌شده']],
];
$class::save_post($postId, get_post($postId), true);
$storedJson = get_post_meta($postId, 'product-feature', true);
if (!is_string($storedJson) || ($class::decode_legacy_value($storedJson)['item-0']['add-new-feature'] ?? $class::decode_legacy_value($storedJson)[0]['add-new-feature'] ?? '') !== 'متن ذخیره‌شده') {
    fwrite(STDERR, "FAIL nonce-protected product save did not preserve JSON repeater format\n");
    $failed++;
}
$before = get_post_meta($postId, 'english_titl', true);
$_POST['es_product_meta_nonce'] = 'invalid-nonce';
$_POST['es_product_meta'] = ['english_titl' => 'Forged value'];
$class::save_post($postId, get_post($postId), true);
if (get_post_meta($postId, 'english_titl', true) !== $before) {
    fwrite(STDERR, "FAIL invalid nonce changed protected product metadata\n");
    $failed++;
}
unset($_POST['es_product_meta_nonce'], $_POST['es_product_meta']);
wp_delete_post($postId, true);

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS product meta contract, array/JSON/serialized repeaters, sanitization, fallbacks, and secure saves\n");
