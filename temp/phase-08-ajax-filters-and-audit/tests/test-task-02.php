<?php
/** Automated QA Test Script — Task 8.2: Live product search. */
declare(strict_types=1);

$themeRoot = dirname(__DIR__, 3);
$failures = [];
$assert = static function (bool $condition, string $label) use (&$failures): void {
    echo $condition ? "  [PASS] {$label}\n" : "  [FAIL] {$label}\n";
    if (!$condition) $failures[] = $label;
};
require_once '/opt/wp-testbed/wp-load.php';
$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.2: Live product search ===\n";

$class = 'ErfanSanat\\Theme\\Ajax\\LiveSearchProducts';
$assert(is_file($themeRoot . '/inc/Ajax/LiveSearchProducts.php'), 'LiveSearchProducts source exists');
$assert(class_exists($class), 'LiveSearchProducts autoloads');
$service = new $class();
$routes = apply_filters('erfan_sanat_ajax_routes', []);
$assert(isset($routes['product_search']) && is_callable($routes['product_search']), 'product search route is registered with the shared AJAX controller');

$productId = wp_insert_post(['post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'LED Tower Product QA', 'post_content' => 'Search-only description QA']);
if (is_wp_error($productId) || !$productId) {
    fwrite(STDERR, "Could not create test product.\n");
    exit(1);
}
update_post_meta($productId, '_sku', 'AXZSKU-802');
update_post_meta($productId, '_price', '12500');
update_post_meta($productId, '_regular_price', '12500');
update_post_meta($productId, 'true_sale', 'false');

$titleResult = $service->search(['query' => 'Tower Product QA']);
$assert(($titleResult['count'] ?? 0) >= 1 && in_array($productId, $titleResult['data']['items'] ?? [], true), 'published product title is searchable');
$assert(str_contains($titleResult['html'] ?? '', 'LED Tower Product QA'), 'result HTML uses existing product-card markup and escaped title');
$assert(str_contains($titleResult['html'] ?? '', 'استعلام قیمت'), 'out-of-stock/quote-style product presents its inquiry state');

$descriptionResult = $service->search(['query' => 'description QA']);
$assert(!in_array($productId, $descriptionResult['data']['items'] ?? [], true), 'content-only matches are excluded from title/SKU search');

$skuResult = $service->search(['query' => 'AXZSKU-802']);
$assert(in_array($productId, $skuResult['data']['items'] ?? [], true), 'exact WooCommerce _sku metadata is searchable');
$assert(($skuResult['count'] ?? 0) >= 1, 'SKU results report a positive count');

$empty = $service->search(['query' => '']);
$short = $service->search(['query' => 'x']);
$assert(($empty['count'] ?? -1) === 0 && ($empty['html'] ?? 'not-empty') === '', 'empty search returns a safe empty result');
$assert(($short['count'] ?? -1) === 0, 'single-character query is rejected as too short');

wp_delete_post($productId, true);
if ($failures !== []) {
    fwrite(STDERR, "[FAILED] " . implode('; ', $failures) . "\n");
    exit(1);
}
echo "[PASSED] Task 8.2 checks completed successfully.\n";
