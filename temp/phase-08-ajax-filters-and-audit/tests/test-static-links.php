<?php
/** Verify all legacy static .html links are rewritten to real WP URLs on render. */
$WP = '/home/user/wp-test'; $fail = 0;
function check($l, $c) { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }
@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('init');
$pid = wp_insert_post(['post_type'=>'page','post_title'=>'خانه','post_status'=>'publish']);
update_option('page_on_front', $pid); update_option('show_on_front', 'page');
register_taxonomy('product_cat', 'product');
register_taxonomy('project_type', 'projects');
foreach (['point-light','light-tree','urban-lighting-elements','needle-thread','wall-washer','acorn-thread','hazelnut-thread','control-box','sun-light-elements','strings-and-lighting-decorations','light-chandelier'] as $s) {
    wp_insert_term($s, 'product_cat', ['slug'=>$s]);
}
wp_insert_term('light-tunnel', 'project_type', ['slug'=>'light-tunnel']);
$prod = wp_insert_post(['post_type'=>'product','post_title'=>'پوینت','post_status'=>'publish','post_name'=>'point-light-pixel']);
foreach (['needle-string-lights','wall-washer','finger-string-lights','fandoghi-string','point-light-pixel-6cm','baloti-string'] as $s) {
    wp_insert_post(['post_type'=>'product','post_title'=>$s,'post_status'=>'publish','post_name'=>$s]);
}
foreach (['half-arch-tunnel-alborz','esfahan-shohada-square','ahvaz-semi-pyramid-light','artam-tree-midan','tunnel-90-alborz','heart-tunnel-khorsand','tehran-commercial-facade-lighting','kerman-arch-light-tunnel','bridge-fullcolor-qom'] as $s) {
    wp_insert_post(['post_type'=>'projects','post_title'=>$s,'post_status'=>'publish','post_name'=>$s]);
}
add_filter('locale', fn() => 'fa_IR'); $GLOBALS['wp_locale']->text_direction = 'rtl';
query_posts(['page_id'=>$pid]);
ob_start(); include get_theme_file_path('front-page.php'); $h = (string) ob_get_clean();
preg_match_all('/href="([^"]*)"/', $h, $m);
$bad = array_filter($m[1], fn($u) => preg_match('#\.html$#', $u) && strpos($u, '<?') === false);
check('rendered front-page: ZERO literal .html hrefs (' . count($m[1]) . ' total hrefs)', $bad === []);
foreach (array_slice($bad, 0, 5) as $b) { echo "  bad: $b\n"; }
check('shop.html resolves to product archive', strpos($h, get_post_type_archive_link('product')) !== false);
check('term link resolves (point-light cat)', strpos($h, get_term_link('point-light', 'product_cat')) !== false);
check('product single resolves', strpos($h, get_permalink($prod)) !== false);
check('es_static_url fallback', es_static_url('nonexistent/x.html') === home_url('/nonexistent/x.html'));
echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
