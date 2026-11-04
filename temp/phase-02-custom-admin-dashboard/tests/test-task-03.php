<?php
/** Task 2.3: es_option() helper + cache */
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
echo "=== [TEST] Task 2.3: es_option() Helper & Cache ===\n";
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
$assert(is_file("$themeRoot/inc/Helpers/Options.php"),'exists inc/Helpers/Options.php');
require_once $wpRoot.'/wp-load.php';
use ErfanSanat\Theme\Admin\OptionsStore;
$assert(function_exists('es_option'),'es_option() global function exists');
delete_option(OptionsStore::OPTION_NAME); OptionsStore::flushCache();
$assert(es_option('brand_name')==='عرفان صنعت اصفهان','es_option returns default value');
$assert(es_option('nope','fallback-123')==='fallback-123','es_option caller default for unknown key');
OptionsStore::set('brand_name','برند آزمایشی');
$assert(es_option('brand_name')==='برند آزمایشی','Cache invalidated immediately after set (no stale read)');
// Direct DB change + explicit flush reflects new value
update_option(OptionsStore::OPTION_NAME, array_merge(OptionsStore::all(), ['brand_name'=>'مقدار مستقیم']));
$assert(es_option('brand_name')==='برند آزمایشی','In-memory cache serves value until flush (documented cache behavior)');
OptionsStore::flushCache();
$assert(es_option('brand_name')==='مقدار مستقیم','After flushCache, fresh DB value is read');
delete_option(OptionsStore::OPTION_NAME); OptionsStore::flushCache();
if($failures){fwrite(STDERR,"[FAILED] Task 2.3\n");exit(1);} echo "\n[PASSED] Task 2.3\n";
