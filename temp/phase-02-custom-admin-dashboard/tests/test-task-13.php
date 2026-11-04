<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 2.13 SectionStorage ===\n";
use ErfanSanat\Theme\Admin\SectionManager\SectionStorage;
$assert(SectionStorage::META_KEY==='_es_sections','Meta key _es_sections');
$pid=wp_insert_post(['post_type'=>'page','post_title'=>'QA Sections','post_status'=>'publish']);
$assert($pid>0,'test page created');
SectionStorage::save($pid,[['id'=>'B','type'=>'Hero Block!','enabled'=>'0','order'=>5,'fields'=>['title'=>'<b>عنوان</b>']],['id'=>'a','type'=>'cta','enabled'=>'1','order'=>1,'fields'=>[]]]);
$s=SectionStorage::get($pid);
$assert(count($s)===2,'two sections stored'); $assert($s[0]['type']==='cta','sections sorted by order'); $assert($s[1]['enabled']===false,'enabled coerced to bool'); $assert($s[1]['fields']['title']==='عنوان','field values sanitized');
$assert(get_post_meta($pid,'_es_sections',true)===$s,'stored in _es_sections postmeta');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED] Task 2.13\n";
