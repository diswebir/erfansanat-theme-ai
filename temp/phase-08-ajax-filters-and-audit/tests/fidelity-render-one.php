<?php
/** Render one page on real WP and print the HTML. Usage: fidelity-render-one.php <key> */
$WP = '/home/user/wp-test';
$key = $argv[1] ?? '';
@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('init');
$menuId = wp_insert_term('main', 'nav_menu')->term_id;
foreach (['index'=>'صفحه اصلی','products'=>'محصولات','projects'=>'پروژه‌ها','about'=>'درباره ما','contact'=>'تماس با ما','blog'=>'وبلاگ'] as $slug => $title) {
    wp_update_nav_menu_item($menuId, 0, ['menu-item-title'=>$title,'menu-item-url'=>home_url('/'.$slug.'/'),'menu-item-status'=>'publish','menu-item-classes'=>'menu-item-has-children']);
}
update_option('theme_mods_' . get_stylesheet(), ['nav_menu_locations' => ['main-mega-menu'=>$menuId, 'mobile-menu'=>$menuId, 'tolidat-sherkat'=>$menuId, 'landing'=>$menuId]]);

register_taxonomy('product_cat', 'product');

$home = wp_insert_post(['post_type'=>'page','post_title'=>'خانه','post_status'=>'publish']);
update_option('page_on_front', $home); update_option('show_on_front', 'page');
foreach (['about','contact','faq','services','recruitment','refund_returns','licenses-and-honors','cart','checkout'] as $slug) {
    wp_insert_post(['post_type'=>'page','post_title'=>$slug,'post_status'=>'publish','post_name'=>$slug]);
}
$cats = ['point-light','light-tree','urban-lighting-elements','needle-thread','wall-washer','acorn-thread','hazelnut-thread','control-box','sun-light-elements','strings-and-lighting-decorations','light-chandelier'];
foreach ($cats as $s) { wp_insert_term($s, 'product_cat', ['slug'=>$s]); }
wp_insert_term('light-tunnel', 'project_type', ['slug'=>'light-tunnel']);
wp_insert_term('esfahan', 'city', ['slug'=>'esfahan']);
$prodSlugs = ['point-light-pixel','needle-string-lights','wall-washer','finger-string-lights','fandoghi-string','point-light-pixel-6cm','baloti-string'];
foreach ($prodSlugs as $i => $s) {
    $id = wp_insert_post(['post_type'=>'product','post_title'=>$s,'post_status'=>'publish','post_name'=>$s]);
    wp_set_object_terms($id, [$cats[$i % count($cats)]], 'product_cat');
    update_post_meta($id, 'featured_products', '1');
}
$projSlugs = ['half-arch-tunnel-alborz','esfahan-shohada-square','ahvaz-semi-pyramid-light','artam-tree-midan','tunnel-90-alborz','heart-tunnel-khorsand','tehran-commercial-facade-lighting','kerman-arch-light-tunnel','bridge-fullcolor-qom'];
foreach ($projSlugs as $s) {
    $id = wp_insert_post(['post_type'=>'projects','post_title'=>$s,'post_status'=>'publish','post_name'=>$s]);
    wp_set_object_terms($id, ['light-tunnel'], 'project_type');
    update_post_meta($id, 'latest_projects', '1');
    wp_set_object_terms($id, ['esfahan'], 'city');
}
for ($i = 0; $i < 3; $i++) { wp_insert_post(['post_type'=>'post','post_title'=>'مقاله '.$i,'post_status'=>'publish']); }
add_filter('locale', fn() => 'fa_IR'); $GLOBALS['wp_locale']->text_direction = 'rtl';

$map = [
    'index'    => [['page_id'=>$home], 'front-page.php'],
    'shop'     => [['post_type'=>'product'], 'archive-product.php'],
    'about'    => [['post_type'=>'page','name'=>'about'], 'page-about.php'],
    'contact'  => [['post_type'=>'page','name'=>'contact'], 'page-contact.php'],
    'product'  => [['post_type'=>'product','name'=>'fandoghi-string'], 'single-product.php'],
    'project'  => [['post_type'=>'projects','name'=>'artam-tree-midan'], 'single-projects.php'],
    'faq'      => [['post_type'=>'page','name'=>'faq'], 'page-faq.php'],
    'services' => [['post_type'=>'page','name'=>'services'], 'page-services.php'],
    'recruitment' => [['post_type'=>'page','name'=>'recruitment'], 'page-recruitment.php'],
    'refund'   => [['post_type'=>'page','name'=>'refund_returns'], 'page-refund_returns.php'],
    'licenses' => [['post_type'=>'page','name'=>'licenses-and-honors'], 'page-licenses-and-honors.php'],
    'cart'     => [['post_type'=>'page','name'=>'cart'], 'page-cart.php'],
    'checkout' => [['post_type'=>'page','name'=>'checkout'], 'page-checkout.php'],
    'blog'     => [['post_type'=>'post'], 'home.php'],
    'blogsingle' => [['post_type'=>'post','name'=>'مقاله-0'], 'single.php'],
    'search'   => [['s'=>'مقاله','post_type'=>'post'], 'search.php'],
    'notfound' => [['post_type'=>'post','name'=>'__none__'], '404.php'],
];
if (!isset($map[$key])) { fwrite(STDERR, "unknown key\n"); exit(1); }
[$q, $file] = $map[$key];
query_posts($q);
include get_theme_file_path($file);
