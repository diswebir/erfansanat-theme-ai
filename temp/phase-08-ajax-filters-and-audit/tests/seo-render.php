<?php
/** Render one page on real WP for SEO checks. Usage: seo-render.php <home|product> */
$WP = '/home/user/wp-test';
$key = $argv[1] ?? 'home';
@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('عرفان صنعت', 'admin', 'a@b.c', true, '', 'admin123');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('after_setup_theme');
do_action('init');
register_taxonomy('product_cat', 'product');
$home = wp_insert_post(['post_type'=>'page','post_title'=>'خانه','post_status'=>'publish']);
update_option('page_on_front', $home); update_option('show_on_front', 'page');
update_option('blogname', 'عرفان صنعت اصفهان');
$prod = wp_insert_post(['post_type'=>'product','post_title'=>'والاشر RGB','post_status'=>'publish','post_name'=>'p1']);
update_post_meta($prod, 'hrefmeta_en', 'https://erfansanat.com/en/wall-washer-rgb/');
update_post_meta($prod, 'hrefmeta_ar', 'https://erfansanat.com/ar/wall-washer-rgb/');
if ($key === 'product') {
    query_posts(['p'=>$prod,'post_type'=>'product']);
    include get_theme_file_path('single-product.php');
} else {
    query_posts(['page_id'=>$home]);
    include get_theme_file_path('front-page.php');
}
