<?php
/**
 * REAL WORDPRESS INTEGRATION TEST — boots actual WordPress 7.2 (cloned from
 * github.com/WordPress/WordPress) with SQLite, installs it, activates the
 * theme, seeds content, and renders templates through real WP.
 *
 * Environment: /home/user/wp-test (wp-core + sqlite dropin + this theme)
 * Run: php-wasm-cli temp/phase-08-ajax-filters-and-audit/tests/test-wp-integration.php
 */

$WP = '/home/user/wp-test';
$fail = 0;
function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

@unlink('/home/user/wp-test/wp-content/database/.ht.sqlite'); // fresh install each run
define('WP_INSTALLING', true);
require $WP . '/wp-load.php';

check('WordPress booted: ' . $GLOBALS['wp_version'], version_compare($GLOBALS['wp_version'], '6.0', '>='));
check('SQLite driver active', stripos(get_class($GLOBALS['wpdb']), 'sqlite') !== false);

// install
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
$result = wp_install('عرفان صنعت اصفهان', 'admin', 'admin@example.com', true, '', 'admin123');
check('WP installed', !is_wp_error($result));

// activate theme
switch_theme('erfansanat-theme-ai');
check('theme activated', wp_get_theme()->get_stylesheet() === 'erfansanat-theme-ai');
$themeErrors = wp_get_theme()->errors();
check('theme errors are empty (codes: ' . implode(',', $themeErrors && is_wp_error($themeErrors) ? $themeErrors->get_error_codes() : []) . ')', !$themeErrors || !is_wp_error($themeErrors) || !$themeErrors->has_errors());
// in CLI the theme's functions.php loads after init already fired -> load it and re-fire init
require_once get_theme_file_path('functions.php');
do_action('init');

// seed
$cat = wp_insert_term('ریسه LED', 'product_cat');
$catId = is_wp_error($cat) ? $cat->get_error_data()['term_exists'] : $cat['term_id'];
wp_insert_term('تونل نوری', 'project_type');
wp_insert_term('اصفهان', 'city');

$prodId = wp_insert_post(['post_type' => 'product', 'post_title' => 'والاشر RGB تست', 'post_status' => 'publish', 'post_content' => 'محصول تست']);
update_post_meta($prodId, 'true_sale', 'true');
update_post_meta($prodId, 'zireonvan', 'زیرعنوان تست');
update_post_meta($prodId, 'faq', ['item-0' => ['name_question' => 'سوال تست؟', 'answer_question' => 'جواب تست']]);
wp_set_object_terms($prodId, [$catId], 'product_cat');

$projId = wp_insert_post(['post_type' => 'projects', 'post_title' => 'تونل نوری اصفهان', 'post_status' => 'publish']);
update_post_meta($projId, 'finish-pro-time', '1404/01/01');
update_post_meta($projId, 'mortabet_mahsole_2', json_encode([$prodId]));
wp_set_object_terms($projId, ['اصفهان'], 'city');
wp_set_object_terms($projId, ['تونل نوری'], 'project_type');

$postId = wp_insert_post(['post_type' => 'post', 'post_title' => 'مقاله تست', 'post_status' => 'publish']);
update_post_meta($postId, 'number2-viewc', 42);

check('CPT projects registered', post_type_exists('projects'));
check('taxonomies registered', taxonomy_exists('project_type') && taxonomy_exists('city'));
check('product meta saved', get_post_meta($prodId, 'true_sale', true) === 'true');
check('project meta saved', get_post_meta($projId, 'finish-pro-time', true) === '1404/01/01');

// render front-page through real WP
$homeId = wp_insert_post(['post_type' => 'page', 'post_title' => 'خانه', 'post_status' => 'publish']);
update_option('page_on_front', $homeId);
update_option('show_on_front', 'page');

ob_start();
query_posts(['page_id' => $homeId]);
include get_theme_file_path('front-page.php');
$out = (string) ob_get_clean();

check('front-page rendered via real WP (' . strlen($out) . ' bytes)', strlen($out) > 10000);
check('front-page has header shell', strpos($out, 'data-navbar-header') !== false);
check('front-page has 15 sections', substr_count($out, '<section') >= 15);
check('front-page has footer', strpos($out, '<footer') !== false);
check('no fatal in output', strpos($out, 'Fatal error') === false);

// render single product
ob_start();
query_posts(['p' => $prodId, 'post_type' => 'product']);
include get_theme_file_path('single-product.php');
$out2 = (string) ob_get_clean();
check('single-product rendered', strpos($out2, 'والاشر RGB تست') !== false);
check('single-product shows zireonvan meta', strpos($out2, 'زیرعنوان تست') !== false);
check('single-product shows faq meta', strpos($out2, 'سوال تست؟') !== false);

// render single project
ob_start();
query_posts(['p' => $projId, 'post_type' => 'projects']);
include get_theme_file_path('single-projects.php');
$out3 = (string) ob_get_clean();
check('single-projects rendered', strpos($out3, 'تونل نوری اصفهان') !== false);
check('single-projects shows finish date', strpos($out3, '1404/01/01') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
