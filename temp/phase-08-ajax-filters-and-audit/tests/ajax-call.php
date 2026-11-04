<?php
/** Boot real WP, seed minimal data, call one AJAX handler, print its JSON.
 *  Usage: ajax-call.php <filters_search|filters_products|filters_projects|form> [json-params] */
$WP = '/home/user/wp-test';
$key = $argv[1] ?? '';
$params = json_decode($argv[2] ?? '[]', true) ?: [];
@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('init');
register_taxonomy('product_cat', 'product');
register_taxonomy('project_type', 'projects');
register_taxonomy('city', 'projects');

$c = wp_insert_term('پوینت لایت', 'product_cat', ['slug'=>'point-light']);
$cId = is_array($c) ? $c['term_id'] : $c->get_error_data()['term_exists'];
wp_insert_term('light-tunnel', 'project_type', ['slug'=>'light-tunnel']);
wp_insert_term('esfahan', 'city', ['slug'=>'esfahan']);
$p1 = wp_insert_post(['post_type'=>'product','post_title'=>'والاشر RGB 20W','post_status'=>'publish','post_name'=>'wall-washer-rgb']);
update_post_meta($p1, 'true_sale', 'true');
wp_set_object_terms($p1, [$cId], 'product_cat');
$p2 = wp_insert_post(['post_type'=>'product','post_title'=>'پوینت لایت پیکسل','post_status'=>'publish','post_name'=>'point-light-pixel']);
wp_set_object_terms($p2, [$cId], 'product_cat');
$pr = wp_insert_post(['post_type'=>'projects','post_title'=>'تونل نوری اصفهان','post_status'=>'publish','post_name'=>'tunnel-esfahan']);
wp_set_object_terms($pr, ['light-tunnel'], 'project_type');
wp_set_object_terms($pr, ['esfahan'], 'city');

$params['nonce'] = $params['nonce'] ?? wp_create_nonce('erfansanat_filters_nonce');
$_REQUEST = $_GET = $_POST = $params;

switch ($key) {
    case 'filters_search':   \ErfanSanat\Theme\Ajax\Filters::search_products(); break;
    case 'filters_products': \ErfanSanat\Theme\Ajax\Filters::filter_products(); break;
    case 'filters_projects': \ErfanSanat\Theme\Ajax\Filters::filter_projects(); break;
    case 'form':
        $_REQUEST['nonce'] = $_POST['nonce'] = wp_create_nonce('erfansanat_form_nonce');
        \ErfanSanat\Theme\Ajax\Forms::handle();
        break;
    default: fwrite(STDERR, "unknown $key\n"); exit(1);
}
