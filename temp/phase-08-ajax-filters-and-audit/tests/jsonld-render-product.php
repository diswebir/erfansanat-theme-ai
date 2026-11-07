<?php
/** Render a single product page on real WP (subprocess helper). */
$WP = '/home/user/wp-test';
@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
add_filter('locale', fn() => 'fa_IR');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('after_setup_theme');
do_action('init');
if (!post_type_exists('product')) { register_post_type('product', ['public' => true]); }
$p = wp_insert_post(['post_type' => 'product', 'post_title' => 'والاشر LED', 'post_status' => 'publish', 'post_excerpt' => 'توضیح محصول تست']);
update_post_meta($p, 'true_sale', 'true');
update_post_meta($p, 'pa_brand', 'عرفان صنعت');
query_posts(['p' => $p, 'post_type' => 'product']);
include get_theme_file_path('single-product.php');
