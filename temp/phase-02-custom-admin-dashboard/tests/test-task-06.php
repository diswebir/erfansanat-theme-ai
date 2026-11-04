<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 2.6 Reset/Export/Import ===\n";
use ErfanSanat\Theme\Admin\{OptionsStore,SettingsTransfer};
$t=new SettingsTransfer();
OptionsStore::set('brand_name','قبل از ریست');
$assert($t->processExport()['brand_name']==='قبل از ریست','export returns current values');
$t->processReset(); $assert(es_option('brand_name')==='عرفان صنعت اصفهان','reset restores defaults');
$assert($t->processImport('{"brand_name":"واردشده","evil":"x"}')===true,'valid JSON import accepted');
$assert(es_option('brand_name')==='واردشده','imported value applied'); $assert(!array_key_exists('evil',OptionsStore::all()),'import rejects unknown keys');
$assert($t->processImport('{invalid')===false,'invalid JSON rejected');
delete_option(OptionsStore::OPTION_NAME); OptionsStore::flushCache();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
