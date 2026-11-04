<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
echo "=== [TEST] Task 2.8 Media & Repeater Fields ===\n";
require_once $wpRoot.'/wp-load.php';
use ErfanSanat\Theme\Admin\Fields\{MediaField,RepeaterField};
$h=MediaField::render('favicon_id','فاوآیکون',0); $assert(str_contains($h,'data-es-media-select="favicon_id"'),'media select button'); $assert(str_contains($h,'data-es-media-remove'),'media remove button');
$h=RepeaterField::render('links','لینک‌ها',[['key'=>'text','label'=>'عنوان'],['key'=>'url','label'=>'آدرس']],[['text'=>'الف','url'=>'https://a.test'],['text'=>'ب','url'=>'']]);
$assert(substr_count($h,'data-es-repeater-item')===2,'repeater renders 2 items'); $assert(str_contains($h,'options[links][1][url]'),'repeater indexed input names'); $assert(str_contains($h,'data-es-repeater-add'),'repeater add button');
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED] Task 2.8\n";
