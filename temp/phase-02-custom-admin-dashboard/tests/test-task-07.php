<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
echo "=== [TEST] Task 2.7 FieldRenderer ===\n";
require_once $wpRoot.'/wp-load.php';
use ErfanSanat\Theme\Admin\Fields\FieldRenderer;
$assert(str_contains(FieldRenderer::render(['key'=>'brand_name','label'=>'نام','type'=>'text'],'<x>'),'value="&lt;x&gt;"'),'text field escapes value');
$assert(str_contains(FieldRenderer::render(['key'=>'footer_about','label'=>'درباره','type'=>'textarea'],'متن'),'متن'),'textarea renders');
$h=FieldRenderer::render(['key'=>'announcement_enabled','label'=>'فعال','type'=>'toggle'],true); $assert(str_contains($h,'checked')&&str_contains($h,'type="hidden"'),'toggle renders checked + hidden fallback');
$h=FieldRenderer::render(['key'=>'mode','label'=>'حالت','type'=>'select','options'=>['a'=>'الف','b'=>'ب']],'b'); $assert(str_contains($h,'selected'),'select marks selected option');
$assert(str_contains(FieldRenderer::render(['key'=>'brand_primary_color','label'=>'رنگ','type'=>'color'],'#0e7a5f'),'type="color"'),'color field renders');
$assert(str_contains(FieldRenderer::render(['key'=>'shop_per_page','label'=>'تعداد','type'=>'number'],12),'type="number"'),'number field renders');
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED] Task 2.7\n";
