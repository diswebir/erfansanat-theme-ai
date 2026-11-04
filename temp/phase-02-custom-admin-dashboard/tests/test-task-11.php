<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
echo "=== [TEST] Task 2.11 AppearanceTab ===\n";
require_once $wpRoot.'/wp-load.php';
use ErfanSanat\Theme\Admin\Tabs\AppearanceTab;
$keys=array_column(AppearanceTab::fields(),'key');
foreach(['brand_primary_color','custom_head_code','custom_footer_code'] as $k) $assert(in_array($k,$keys,true),"Field defined: $k");
ob_start(); (new AppearanceTab())->render(); $html=(string)ob_get_clean();
$assert(str_contains($html,'data-es-options-form'),'Tab renders options form');
$assert(str_contains($html,'data-es-save-options'),'Tab renders save button');

if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED] Task 2.11\n";
