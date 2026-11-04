<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 2.4 Admin Assets ===\n";
use ErfanSanat\Theme\Admin\{AdminAssets,ThemeOptions};
$adminCssPath = "$themeRoot/assets/admin/css/admin-dashboard.css";
$assert(is_file($adminCssPath),'admin css exists');
$assert(is_file("$themeRoot/assets/admin/js/admin-dashboard.js"),'admin js exists');
$adminCss = is_file($adminCssPath) ? (string) file_get_contents($adminCssPath) : '';
$assert(substr_count($adminCss, '@font-face') === 3, 'Admin CSS declares all three local Vazirmatn subsets');
foreach (['vazirmatn-arabic.woff2', 'vazirmatn-latin.woff2', 'vazirmatn-latin-ext.woff2'] as $fontFile) {
    $assert(str_contains($adminCss, $fontFile), "Admin CSS loads local font subset: $fontFile");
}
$assert(str_contains($adminCss, 'body.toplevel_page_erfansanat-theme') && str_contains($adminCss, "font-family: 'Vazirmatn'"), 'Vazirmatn is applied to the theme dashboard and controls');
$assert(str_contains($adminCss, '.media-modal') && str_contains($adminCss, 'font-family: inherit'), 'Media library dialog inherits Vazirmatn without replacing icon fonts');
$assert(str_contains($adminCss, ".postbox[id^='erfansanat-']"), 'Theme metaboxes inherit the admin font');
$assert(str_contains($adminCss, 'body.taxonomy-product_cat') && str_contains($adminCss, 'body.taxonomy-city'), 'Theme taxonomy screens inherit the admin font');
$assert(str_contains($adminCss, 'body.post-type-product .wp-list-table'), 'Product/project list screens inherit the admin font');
$assert(AdminAssets::shouldEnqueueStyle('post.php', 'product'), 'Font stylesheet is enabled on theme product editor');
$assert(AdminAssets::shouldEnqueueStyle('post-new.php', 'page'), 'Font stylesheet is enabled on page editor and section manager');
$assert(AdminAssets::shouldEnqueueStyle('edit-tags.php', null, 'product_cat'), 'Font stylesheet is enabled on theme taxonomy screens');
$assert(AdminAssets::shouldEnqueueStyle('edit.php', 'projects'), 'Font stylesheet is enabled on project list screen');
$assert(!AdminAssets::shouldEnqueueStyle('post.php', 'unrelated_cpt'), 'Font stylesheet remains scoped away from unrelated post types');
$a=new AdminAssets(); $a->enqueue('plugins.php');
$assert(!wp_style_is('erfansanat-admin','enqueued'),'Not enqueued on unrelated admin page');
if (!class_exists('WP_Screen')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
}
if (!function_exists('get_current_screen')) {
    require_once ABSPATH . 'wp-admin/includes/screen.php';
}
$screenFactory = new ReflectionClass(WP_Screen::class);
$productScreen = $screenFactory->newInstanceWithoutConstructor();
$productScreen->post_type = 'product';
$GLOBALS['current_screen'] = $productScreen;
$a->enqueue('post.php');
$assert(wp_style_is('erfansanat-admin','enqueued'),'Font CSS enqueued for a theme product metabox screen');
wp_dequeue_style('erfansanat-admin');
$unrelatedScreen = $screenFactory->newInstanceWithoutConstructor();
$unrelatedScreen->post_type = 'unrelated_cpt';
$GLOBALS['current_screen'] = $unrelatedScreen;
$a->enqueue('post.php');
$assert(!wp_style_is('erfansanat-admin','enqueued'),'Font CSS stays off unrelated custom post screens');
$GLOBALS['current_screen'] = null;
$a->enqueue('toplevel_page_'.ThemeOptions::PAGE_SLUG);
$assert(wp_style_is('erfansanat-admin','enqueued'),'CSS enqueued on dashboard page');
$assert(wp_script_is('erfansanat-admin','enqueued'),'JS enqueued on dashboard page');
$data=wp_scripts()->get_data('erfansanat-admin','data'); $assert(is_string($data)&&str_contains($data,'esAdminData'),'esAdminData localized with ajax/nonce');
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
