<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 2.6 Reset/Export/Import ===\n";
use ErfanSanat\Theme\Admin\{OptionsStore,SettingsTransfer};
use ErfanSanat\Theme\Admin\Tabs\ToolsTab;
$t=new SettingsTransfer();
$assert(class_exists(ToolsTab::class),'ToolsTab implementation exists');
$assert(has_action('erfansanat_admin_tab_content_tools')!==false,'Tools tab content is registered');
if (class_exists(ToolsTab::class)) {
    ob_start(); (new ToolsTab())->render(); $toolsHtml=(string)ob_get_clean();
    foreach (['data-es-tools','data-es-export-options','data-es-import-options','data-es-reset-options','data-es-import-json','data-es-tools-status'] as $hook) {
        $assert(str_contains($toolsHtml,$hook),"Tools UI includes $hook control");
    }
    $assert(!str_contains($toolsHtml,'data-es-save-options'),'Tools tab does not show an unrelated save-settings action');
}
OptionsStore::set('brand_name','قبل از ریست');
$assert($t->processExport()['brand_name']==='قبل از ریست','export returns current values');
$t->processReset(); $assert(es_option('brand_name')==='عرفان صنعت اصفهان','reset restores defaults');
$assert($t->processImport('{"brand_name":"واردشده","evil":"x"}')===true,'valid JSON import accepted');
$assert(es_option('brand_name')==='واردشده','imported value applied'); $assert(!array_key_exists('evil',OptionsStore::all()),'import rejects unknown keys');
$assert($t->processImport('{invalid')===false,'invalid JSON rejected');
$filterKey=array_key_first(\ErfanSanat\Theme\Filters\FilterRegistry::all());
$filterImport=json_encode(['enabled_filters'=>[$filterKey=>true]],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
$assert(is_string($filterImport)&&$t->processImport($filterImport),'filter-map object imports from a JSON backup');
$assert(!empty(OptionsStore::get('enabled_filters',[])[$filterKey]),'nested filter-map values survive JSON import');
$assert($t->processImport('[]')===false,'JSON arrays rejected; imports must be an object');
$assert($t->processImport(str_repeat('x',SettingsTransfer::MAX_IMPORT_BYTES + 1))===false,'oversized imports rejected server-side');
delete_option(OptionsStore::OPTION_NAME); OptionsStore::flushCache();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
