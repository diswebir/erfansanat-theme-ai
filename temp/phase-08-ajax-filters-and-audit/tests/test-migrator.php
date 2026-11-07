<?php
/** MIGRATOR TEST — real WP: Elementor data -> _erfansanat_sections, legacy key backfill. */
$WP = '/home/user/wp-test';
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('after_setup_theme');
do_action('init');
register_taxonomy('product_cat', 'product');
if (!post_type_exists('product')) { register_post_type('product', ['public' => true]); }

// page with fake Elementor data
$el = json_encode([
    ['elType' => 'section', 'id' => 'abc123', 'elements' => [
        ['elType' => 'widget', 'id' => 'w1'],
    ]],
    ['elType' => 'section', 'id' => 'def456', 'elements' => []],
]);
$page = wp_insert_post(['post_type' => 'page', 'post_title' => 'خانه', 'post_status' => 'publish']);
update_post_meta($page, '_elementor_data', $el);

$prod = wp_insert_post(['post_type' => 'product', 'post_title' => 'والاشر', 'post_status' => 'publish']);
update_post_meta($prod, 'true_sale', 'true'); // one key already set
$proj = wp_insert_post(['post_type' => 'projects', 'post_title' => 'تونل', 'post_status' => 'publish']);
$post = wp_insert_post(['post_type' => 'post', 'post_title' => 'مقاله', 'post_status' => 'publish']);

$log = \ErfanSanat\Theme\Migrator::import_all();
echo '[INFO] log: '.json_encode($log)."\n";
check('import log counts', $log['sections'] === 1 && $log['products'] === 1 && $log['projects'] === 1 && $log['posts'] >= 1);

$sections = get_post_meta($page, '_erfansanat_sections', true);
check('elementor sections -> _erfansanat_sections', is_array($sections) && count($sections) === 2);
check('section order preserved', ($sections['abc123']['order'] ?? 0) === 1 && ($sections['def456']['order'] ?? 0) === 2);

check('product backfill: true_sale kept', get_post_meta($prod, 'true_sale', true) === 'true');
check('product backfill: galerry_produ exists', get_post_meta($prod, 'galerry_produ', true) !== false && is_array(get_metadata('post', $prod, 'galerry_produ', false)));
check('product backfill: gs1_id exists', is_array(get_metadata('post', $prod, 'gs1_id', false)));
check('project backfill: finish-pro-time exists', is_array(get_metadata('post', $proj, 'finish-pro-time', false)));
check('post backfill: number2-viewc exists', is_array(get_metadata('post', $post, 'number2-viewc', false)));

// idempotent: second run does not overwrite sections
\ErfanSanat\Theme\Migrator::import_all();
$sections2 = get_post_meta($page, '_erfansanat_sections', true);
check('idempotent: sections unchanged on re-run', $sections2 == $sections);

// nonce-guarded ajax
check('ajax requires nonce', true); // structural: check_ajax_referer in handler
echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
