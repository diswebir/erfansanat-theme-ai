<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 2.14 SectionMetabox UI ===\n";
use ErfanSanat\Theme\Admin\SectionManager\{SectionMetabox,SectionStorage};
$assert(is_file("$themeRoot/assets/admin/js/section-manager.js"),'section-manager.js exists');
$js=(string)file_get_contents("$themeRoot/assets/admin/js/section-manager.js"); $assert(str_contains($js,'sortable'),'drag & drop sortable initialized');
$pid=wp_insert_post(['post_type'=>'page','post_title'=>'QA Metabox','post_status'=>'publish']);
SectionStorage::save($pid,[['id'=>'hero','type'=>'hero','enabled'=>true,'order'=>0,'fields'=>['title'=>'سلام']]]);
$post=get_post($pid); $m=new SectionMetabox(); ob_start(); $m->render($post); $h=(string)ob_get_clean();
$assert(str_contains($h,'data-es-sections-list'),'sections list rendered'); $assert(str_contains($h,'data-es-section="hero"'),'section item rendered'); $assert(str_contains($h,'es_sections[0][fields][title]'),'field input rendered'); $assert(str_contains($h,'checked'),'enabled toggle checked');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED] Task 2.14\n";
