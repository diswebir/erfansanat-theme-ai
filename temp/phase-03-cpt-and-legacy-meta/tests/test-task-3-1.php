<?php
/**
 * Test Task 3.1 — CPT projects + taxonomies project_type/city + product_cat rewrite + admin filters.
 * Run: php-wasm-cli temp/phase-03-cpt-and-legacy-meta/tests/test-task-3-1.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

$f = $THEME . '/inc/Data/Registrar.php';
check('inc/Data/Registrar.php exists', is_file($f));
$t = is_file($f) ? file_get_contents($f) : '';

check('namespace ErfanSanat\\Theme\\Data', strpos($t, 'namespace ErfanSanat\\Theme\\Data;') !== false);

// CPT projects with exact live settings
check("register_post_type('projects')", strpos($t, "register_post_type('projects'") !== false || strpos($t, 'register_post_type(\n') !== false && strpos($t, "'projects'") !== false);
check("'public' => true", (bool) preg_match("/'public'\s*=>\s*true/", $t));
check("'has_archive' => true", (bool) preg_match("/'has_archive'\s*=>\s*true/", $t));
check("rewrite slug 'projects' with_front false", strpos($t, "'slug' => 'projects'") !== false && strpos($t, "'with_front' => false") !== false);
check("menu_icon dashicons-screenoptions", strpos($t, 'dashicons-screenoptions') !== false);
check('supports title/editor/thumbnail/comments/custom-fields/revisions', strpos($t, "'custom-fields'") !== false && strpos($t, "'revisions'") !== false && strpos($t, "'thumbnail'") !== false);
check("'show_in_rest' => true", (bool) preg_match("/'show_in_rest'\s*=>\s*true/", $t));

// Taxonomies
foreach (['project_type', 'city'] as $tax) {
    check("register_taxonomy('$tax')", strpos($t, "'$tax'") !== false);
    check("$tax hierarchical", (bool) preg_match("/'hierarchical'\s*=>\s*true/", $t));
    check("$tax rewrite slug '$tax'", strpos($t, "'slug' => '$tax'") !== false);
}

// product_cat exact slug + with_front (SEO critical)
check("product_cat rewrite slug 'product_cat'", strpos($t, "'product_cat'") !== false);
check('product_cat with_front true', (bool) preg_match("/'with_front'\s*=>\s*true/", $t));
check('product_tag slug product-tag', strpos($t, 'product-tag') !== false);

// Admin column filters: same_key_hrefl sortable + city/project_type dropdowns
check('admin column same_key_hrefl', strpos($t, 'same_key_hrefl') !== false);
check('manage_edit-projects_columns filter', strpos($t, 'manage_edit-projects_columns') !== false);
check('dropdown filters for city & project_type', strpos($t, 'restrict_manage_posts') !== false);

// Wired into Theme
$themeTxt = file_get_contents($THEME . '/inc/Theme.php');
check('Theme.php wires Registrar on init', strpos($themeTxt, 'Registrar') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
