<?php
/** Task 2.2: OptionsStore + Defaults */
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
echo "=== [TEST] Task 2.2: Options Store & Defaults ===\n";
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
foreach(['inc/Admin/OptionsStore.php','inc/Admin/Defaults.php'] as $rel){ $assert(is_file("$themeRoot/$rel"),"exists $rel"); $o=[];$c=0; exec('php -l '.escapeshellarg("$themeRoot/$rel").' 2>&1',$o,$c); $assert($c===0,"php -l $rel"); }
require_once $wpRoot.'/wp-load.php';
use ErfanSanat\Theme\Admin\{Defaults,OptionsStore};
$assert(OptionsStore::OPTION_NAME==='erfansanat_theme_options','Single option name erfansanat_theme_options');
$d=Defaults::all();
foreach(['site_logo','brand_name','header_phone','footer_about','contact_phone','contact_address','social_instagram','brand_primary_color','custom_head_code','custom_footer_code','enabled_filters'] as $k) $assert(array_key_exists($k,$d),"Default key exists: $k");
$assert(is_array($d['enabled_filters']) && count($d['enabled_filters'])>=23,'Defaults include >=23 filter toggles (core + 18 pa_*)');
delete_option(OptionsStore::OPTION_NAME); OptionsStore::flushCache();
$assert(OptionsStore::get('brand_name')===$d['brand_name'],'Fallback to default when option missing');
OptionsStore::set('brand_name',' تست مقدار ');
$assert(get_option(OptionsStore::OPTION_NAME)['brand_name']===' تست مقدار ','Value persisted in single option (raw)');
$assert(OptionsStore::get('unknown_key_xyz','FB')==='FB','Unknown key returns caller default');
OptionsStore::update(['header_phone'=>'031-000','bogus_key'=>'x']);
$all=OptionsStore::all(); $assert(($all['header_phone']??'')==='031-000' && !array_key_exists('bogus_key',$all),'update() keeps known keys only');
delete_option(OptionsStore::OPTION_NAME); OptionsStore::flushCache();
if($failures){fwrite(STDERR,"[FAILED] Task 2.2\n");exit(1);} echo "\n[PASSED] Task 2.2\n";
