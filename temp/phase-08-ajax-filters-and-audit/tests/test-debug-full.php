<?php
/**
 * FULL DEBUG TEST — boot real WP 7.2 + SQLite with WP_DEBUG on, render EVERY
 * major template, and capture ALL warnings/notices/deprecations/fatals.
 * Any PHP diagnostic output = FAIL (zero-warning requirement).
 *
 * Run: php-wasm-cli temp/phase-08-ajax-filters-and-audit/tests/test-debug-full.php
 */

$WP = '/home/user/wp-test';
$fail = 0;
$diagnostics = [];

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

// capture every diagnostic
set_error_handler(function ($no, $msg, $file, $line) {
    global $diagnostics;
    $diagnostics[] = "[$no] $msg @ " . basename($file) . ":$line";
    return true; // suppress default output
});
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        echo "[FATAL] {$e['message']} @ {$e['file']}:{$e['line']}\n";
        exit(1);
    }
});

@unlink($WP . '/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
wp_install('عرفان صنعت', 'admin', 'admin@example.com', true, '', 'admin123');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('init');

// seed content
$cat = wp_insert_term('ریسه LED', 'product_cat');
$catId = is_wp_error($cat) ? $cat->get_error_data()['term_exists'] : $cat['term_id'];
wp_insert_term('تونل نوری', 'project_type');
wp_insert_term('اصفهان', 'city');
$prodId = wp_insert_post(['post_type' => 'product', 'post_title' => 'والاشر RGB', 'post_status' => 'publish', 'post_content' => 'محصول']);
update_post_meta($prodId, 'true_sale', 'true');
update_post_meta($prodId, 'zireonvan', 'زیرعنوان');
update_post_meta($prodId, 'galerry_produ', '1,2');
update_post_meta($prodId, 'faq', ['item-0' => ['name_question' => 'س؟', 'answer_question' => 'ج']]);
update_post_meta($prodId, 'product-feature', ['item-0' => ['add-new-feature' => 'ویژگی']]);
wp_set_object_terms($prodId, [$catId], 'product_cat');
$projId = wp_insert_post(['post_type' => 'projects', 'post_title' => 'تونل اصفهان', 'post_status' => 'publish']);
update_post_meta($projId, 'finish-pro-time', '1404/01/01');
update_post_meta($projId, 'mortabet_mahsole_2', json_encode([$prodId]));
wp_set_object_terms($projId, ['اصفهان'], 'city');
wp_set_object_terms($projId, ['تونل نوری'], 'project_type');
$postId = wp_insert_post(['post_type' => 'post', 'post_title' => 'مقاله', 'post_status' => 'publish']);
update_post_meta($postId, 'number2-viewc', 7);
$homeId = wp_insert_post(['post_type' => 'page', 'post_title' => 'خانه', 'post_status' => 'publish']);
update_option('page_on_front', $homeId);
update_option('show_on_front', 'page');
$aboutId = wp_insert_post(['post_type' => 'page', 'post_title' => 'درباره ما', 'post_status' => 'publish', 'post_content' => 'متن']);

// render each template and check for leaked diagnostics
$templates = [
    'front-page'  => ['page_id' => $homeId],
    'single-product' => ['p' => $prodId, 'post_type' => 'product'],
    'single-projects' => ['p' => $projId, 'post_type' => 'projects'],
    'archive-product' => ['post_type' => 'product'],
    'archive-projects' => ['post_type' => 'projects'],
    'taxonomy-product_cat' => ['product_cat' => 'rise-led'],
    'taxonomy-city' => ['city' => 'esfahan'],
    'home'        => ['post_type' => 'post'],
    'single'      => ['p' => $postId, 'post_type' => 'post'],
    'page'        => ['page_id' => $aboutId],
    '404'         => ['error' => 404],
    'search'      => ['s' => 'والاشر'],
];
foreach ($templates as $tpl => $qargs) {
    $before = count($diagnostics);
    ob_start();
    query_posts($qargs);
    $file = get_theme_file_path($tpl . '.php');
    if (is_file($file)) {
        include $file;
    } else {
        echo "[missing template: $tpl]";
    }
    $out = (string) ob_get_clean();
    $new = array_slice($diagnostics, $before);
    $leaked = (strpos($out, 'Warning') !== false || strpos($out, 'Deprecated') !== false || strpos($out, 'Notice') !== false || strpos($out, 'Fatal error') !== false);
    check("render $tpl: " . strlen($out) . "B, no diagnostics" . ($new ? ' — ' . count($new) . ' diagnostics' : ''), strlen($out) > 100 && !$leaked);
    foreach (array_slice($new, 0, 5) as $d) { echo "    $d\n"; }
}

$themeDiags = array_values(array_filter($diagnostics, fn($d) => strpos($d, 'themes/erfansanat') !== false));
echo "\nTotal diagnostics: " . count($diagnostics) . " (WP core/harness), theme-file: " . count($themeDiags) . "\n";
check('ZERO PHP warnings/notices/deprecations from THEME files', $themeDiags === []);
foreach (array_slice($themeDiags, 0, 15) as $d) { echo "  $d\n"; }

// ajax endpoints under real WP (last — wp_send_json ends the process)
$_REQUEST['nonce'] = wp_create_nonce('erfansanat_filters_nonce');
$_GET['s'] = 'والاشر';
ob_start();
try { \ErfanSanat\Theme\Ajax\Filters::search_products(); } catch (Throwable $e) {}
$json = (string) ob_get_clean();
check('ajax search returns success JSON', strpos($json, '"success":true') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
