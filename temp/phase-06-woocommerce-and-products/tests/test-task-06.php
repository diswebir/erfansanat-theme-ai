<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.6 variable product selectors and swatches ===\n";
if (!class_exists('ES_Test_Variable_Product')) { class ES_Test_Variable_Product { public function get_variation_attributes(): array { return ['pa_color' => ['red', 'blue'], 'pa_size' => ['small', 'large']]; } public function get_available_variations(): array { return [['variation_id' => 91, 'attributes' => ['attribute_pa_color' => 'red', 'attribute_pa_size' => 'small']]]; } public function get_id(): int { return 77; } public function get_min_purchase_quantity(): int { return 1; } } }
$id = $makeProduct(); $GLOBALS['product'] = new ES_Test_Variable_Product();
$html = $renderPart('variations');
foreach (['variations_form', 'data-opt-group', 'data-opt-btn', 'data-opt-value=', 'data-opt-selected-label="true"', 'attribute_pa_color', 'attribute_pa_size', 'variation_id', 'single_add_to_cart_button', 'name="quantity"'] as $needle) $assert(str_contains($html, $needle), 'variation contract ' . $needle);
$assert(str_contains($html, 'data-product_variations='), 'variation JSON available to WooCommerce variation script');
$source = (string) file_get_contents($themeRoot . '/template-parts/product/variations.php');
$assert(str_contains($source, 'wc_dropdown_variation_attribute_options'), 'Woo variation-dropdown filter path enables Variation Swatches compatibility');
$assert(str_contains($source, 'woocommerce_quantity_input'), 'Woo quantity input filter path allows WPC Product Quantity rules');
wp_delete_post($id, true); $GLOBALS['product'] = null;
$finishTest();
