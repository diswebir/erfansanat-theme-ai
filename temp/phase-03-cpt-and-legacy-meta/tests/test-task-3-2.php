<?php
/**
 * Test Task 3.2 — ProductMetaboxes with ALL legacy meta keys + repeaters.
 * Run: php-wasm-cli temp/phase-03-cpt-and-legacy-meta/tests/test-task-3-2.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

$f = $THEME . '/inc/Admin/ProductMetaboxes.php';
check('inc/Admin/ProductMetaboxes.php exists', is_file($f));
$t = is_file($f) ? file_get_contents($f) : '';

check('namespace ErfanSanat\\Theme\\Admin', strpos($t, 'namespace ErfanSanat\\Theme\\Admin;') !== false);
check('class ProductMetaboxes', strpos($t, 'class ProductMetaboxes') !== false);
check('metabox only on product (add_meta_boxes + screen check)', strpos($t, 'add_meta_boxes') !== false && strpos($t, "'product'") !== false);
check('save_post with nonce + capability', strpos($t, 'save_post') !== false && strpos($t, 'wp_verify_nonce') !== false && strpos($t, 'current_user_can') !== false);

// ALL legacy meta keys with exact spelling
$keys = ['gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id',
         'zireonvan', 'english_titl', 'before-sale-c', 'true_sale',
         'product-catalog-file', 'galerry_produ', 'project-ids', 'usage_countr',
         'product-feature', 'product-feature2_copy', 'vaule_of_variable',
         'usage_product', 'faq', 'name_question', 'answer_question', 'add-new-feature', 'add-new-usage'];
foreach ($keys as $k) {
    check("legacy key present: $k", strpos($t, "'$k'") !== false);
}

// Read compatibility: serialized arrays AND JSON (JetEngine formats)
check('reads serialized (maybe_unserialize)', strpos($t, 'maybe_unserialize') !== false);
check('reads JSON (json_decode)', strpos($t, 'json_decode') !== false);
check('repeater format item-0 ({"item-0": {...}})', strpos($t, 'item-') !== false);
check('gallery fallbacks: _product_image_gallery + gallery-filed + image-2..6', strpos($t, '_product_image_gallery') !== false && strpos($t, 'gallery-filed') !== false && strpos($t, 'image-6') !== false);

// Wired
$themeTxt = file_get_contents($THEME . '/inc/Theme.php');
check('Theme.php wires ProductMetaboxes', strpos($themeTxt, 'ProductMetaboxes') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
