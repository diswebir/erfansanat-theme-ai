<?php
/**
 * Test Task 6.1 — single-product.php with all legacy meta + true_sale logic.
 * Run: php-wasm-cli temp/phase-06-woocommerce-and-products/tests/test-task-6-1.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

$f = "$THEME/single-product.php";
check('single-product.php exists', is_file($f));
$t = is_file($f) ? file_get_contents($f) : '';

check('uses get_header/get_footer + the_post loop', strpos($t, 'get_header()') !== false && strpos($t, 'the_post()') !== false && strpos($t, 'get_footer()') !== false);

// gallery + identity meta
check('gallery from galerry_produ (+WC fallback)', strpos($t, 'galerry_produ') !== false && strpos($t, '_product_image_gallery') !== false);
check('zireonvan subtitle', strpos($t, 'zireonvan') !== false);
check('english_titl', strpos($t, 'english_titl') !== false);
foreach (['gs1_id','national_code','hs_id','isic_id','cpc_id','product_id'] as $k) {
    check("legal id: $k", strpos($t, "'$k'") !== false);
}
check('before-sale-c notice', strpos($t, 'before-sale-c') !== false);
check('catalog file product-catalog-file', strpos($t, 'product-catalog-file') !== false);

// true_sale logic
check("true_sale branch (buy vs inquiry)", strpos($t, 'true_sale') !== false && strpos($t, "add_to_cart") !== false || strpos($t, 'woocommerce') !== false);

// 18 pa_* attributes table
foreach (['pa_wattage','pa_ip-rating','pa_input-voltage','pa_color','pa_beam-angle','pa_lifespan','pa_warranty','pa_body-material','pa_led-chip-type','pa_bulb-type','pa_cable-material','pa_certifications','pa_diameter','pa_size','pa_intensity','pa_diffuser-color','pa_after-sales-support','pa_brand'] as $pa) {
    check("pa attribute: $pa", strpos($t, $pa) !== false);
}

// repeaters + relations + faq
foreach (['product-feature','product-feature2_copy','vaule_of_variable','usage_product','usage_countr','project-ids','faq'] as $k) {
    check("meta/repeater: $k", strpos($t, "'$k'") !== false);
}

// related products + comments
check('related products query (WP_Query + post__not_in)', strpos($t, 'WP_Query') !== false && strpos($t, 'post__not_in') !== false);
check('comments_template', strpos($t, 'comments_template') !== false);

// empty sections hidden
check('conditional rendering (if meta) for optional blocks', (bool) preg_match('/if\s*\(\s*\$/', $t));

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
