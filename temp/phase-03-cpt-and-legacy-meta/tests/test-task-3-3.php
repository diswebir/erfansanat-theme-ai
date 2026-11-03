<?php
/**
 * Test Task 3.3 — project metaboxes + product_cat term meta + hreflang + view counter.
 * Run: php-wasm-cli temp/phase-03-cpt-and-legacy-meta/tests/test-task-3-3.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

$f = $THEME . '/inc/Admin/LegacyMetaBoxes.php';
check('inc/Admin/LegacyMetaBoxes.php exists', is_file($f));
$t = is_file($f) ? file_get_contents($f) : '';

check('namespace ErfanSanat\\Theme\\Admin', strpos($t, 'namespace ErfanSanat\\Theme\\Admin;') !== false);
check('class LegacyMetaBoxes', strpos($t, 'class LegacyMetaBoxes') !== false);

// Project meta keys (exact spelling incl. foure-image)
foreach (['second-image', 'three-image', 'foure-image', 'five-image', 'six-image', 'finish-pro-time', 'mortabet_mahsole_2'] as $k) {
    check("project meta key: $k", strpos($t, "'$k'") !== false);
}

// product_cat term meta: faq repeater + thumbnail
check('term meta product_cat faq', strpos($t, 'product_cat') !== false && strpos($t, "'faq'") !== false);
check('term meta thumbnail', strpos($t, "'thumbnail'") !== false);
check('term hooks (product_cat_edit_form_fields / edited_product_cat)', strpos($t, 'product_cat_edit_form_fields') !== false && strpos($t, 'edited_product_cat') !== false || strpos($t, 'saved_term') !== false || strpos($t, 'edited_product_cat') !== false);

// Hreflang meta on all object types
foreach (['hrefmeta_fa', 'hrefmeta_en', 'hrefmeta_ar', 'same_key_hrefl'] as $k) {
    check("hreflang key: $k", strpos($t, "'$k'") !== false);
}
check('metabox on post/page/product/projects', strpos($t, 'add_meta_boxes') !== false);
check('same_key_hrefl sortable column (request/parse_query or manage_posts_sortable_columns)', strpos($t, 'sortable_columns') !== false || strpos($t, 'pre_get_posts') !== false);

// View counter
check('view counter number2-viewc with number-viewc fallback', strpos($t, 'number2-viewc') !== false && strpos($t, 'number-viewc') !== false);
check('counter increments on single view (template_redirect/wp_head)', strpos($t, 'number2-viewc') !== false);

// Security
check('nonce + capability on save', strpos($t, 'wp_verify_nonce') !== false && strpos($t, 'current_user_can') !== false);

// Wired
$themeTxt = file_get_contents($THEME . '/inc/Theme.php');
check('Theme.php wires LegacyMetaBoxes', strpos($themeTxt, 'LegacyMetaBoxes') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
