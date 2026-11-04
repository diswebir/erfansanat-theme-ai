<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
echo "=== [TEST] Task 2.12 FiltersTab ===\n";
require_once $wpRoot.'/wp-load.php';
use ErfanSanat\Theme\Admin\Tabs\FiltersTab;
$keys=array_column(FiltersTab::fields(),'key');
foreach(['shop_per_page','projects_per_page'] as $k) $assert(in_array($k,$keys,true),"Field defined: $k");
ob_start(); (new FiltersTab())->render(); $html=(string)ob_get_clean();
$assert(str_contains($html,'data-es-options-form'),'Tab renders options form');
$assert(str_contains($html,'data-es-save-options'),'Tab renders save button');
$h=(function(){ ob_start(); (new FiltersTab())->render(); return (string)ob_get_clean(); })();
$assert(str_contains($h,'options[enabled_filters][attr_pa_color]'),'pa_color toggle rendered');
$assert(substr_count($h,'options[enabled_filters][')>=25,'All 25 filter toggles rendered (7 core + 18 pa_*)');
$assert(count(\ErfanSanat\Theme\Filters\FilterRegistry::attributes())===18,'Exactly 18 pa_* attributes in registry');

if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED] Task 2.12\n";
