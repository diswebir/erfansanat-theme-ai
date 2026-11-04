<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 2.4 Admin Assets ===\n";
use ErfanSanat\Theme\Admin\{AdminAssets,ThemeOptions};
$assert(is_file("$themeRoot/assets/admin/css/admin-dashboard.css"),'admin css exists');
$assert(is_file("$themeRoot/assets/admin/js/admin-dashboard.js"),'admin js exists');
$a=new AdminAssets(); $a->enqueue('plugins.php');
$assert(!wp_style_is('erfansanat-admin','enqueued'),'Not enqueued on unrelated admin page');
$a->enqueue('toplevel_page_'.ThemeOptions::PAGE_SLUG);
$assert(wp_style_is('erfansanat-admin','enqueued'),'CSS enqueued on dashboard page');
$assert(wp_script_is('erfansanat-admin','enqueued'),'JS enqueued on dashboard page');
$data=wp_scripts()->get_data('erfansanat-admin','data'); $assert(is_string($data)&&str_contains($data,'esAdminData'),'esAdminData localized with ajax/nonce');
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
