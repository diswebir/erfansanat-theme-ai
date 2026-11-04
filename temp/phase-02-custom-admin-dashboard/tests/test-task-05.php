<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 2.5 AJAX Save ===\n";
use ErfanSanat\Theme\Admin\{AjaxSaveOptions,OptionsStore};
$assert(has_action('wp_ajax_erfansanat_save_options')!==false,'wp_ajax_erfansanat_save_options registered');
delete_option(OptionsStore::OPTION_NAME); OptionsStore::flushCache();
$s=new AjaxSaveOptions();
$saved=$s->processSave(['brand_name'=>' <b>برند</b> ','contact_email'=>' bad@','brand_primary_color'=>'#12ab34','shop_per_page'=>'-5','header_show_search'=>'0','unknown'=>'x']);
$assert($saved['brand_name']==='برند','text sanitized');
$assert($saved['brand_primary_color']==='#12ab34','color sanitized');
$assert($saved['shop_per_page']===5,'int absint'); $assert($saved['header_show_search']===false,'bool coerced');
$assert(!array_key_exists('unknown',$saved),'unknown keys rejected');
$assert(es_option('brand_name')==='برند','saved value readable via es_option');
delete_option(OptionsStore::OPTION_NAME); OptionsStore::flushCache();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
