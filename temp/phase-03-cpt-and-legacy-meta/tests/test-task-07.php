<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
$mkprod=static function(): int { $id=wp_insert_post(['post_type'=>'post','post_title'=>'QA Product','post_status'=>'publish']); return (int)$id; };
echo "=== [TEST] Task 3.7 LegacyMeta ===\n";
use ErfanSanat\Theme\Helpers\LegacyMeta;
$r=LegacyMeta::parseRows(['item-1'=>['a'=>'2'],'item-0'=>['a'=>'1']]); $assert($r[0]['a']==='1'&&$r[1]['a']==='2','item-N rows sorted');
$r=LegacyMeta::parseRows('{"item-0":{"x":"y"}}'); $assert($r[0]['x']==='y','JSON rows parsed');
$r=LegacyMeta::parseRows(serialize(['item-0'=>['x'=>'s']])); $assert($r[0]['x']==='s','serialized rows parsed');
$assert(LegacyMeta::parseIds('1, 2,3')===[1,2,3],'comma ids parsed'); $assert(LegacyMeta::parseIds([5,'6'])===[5,6],'array ids parsed');
$pid=$mkprod(); LegacyMeta::saveRows($pid,'faq',[['name_question'=>'س؟','answer_question'=>'ج']]);
$raw=get_post_meta($pid,'faq',true); $assert(isset($raw['item-0']),'saved back in JetEngine item-0 format');
LegacyMeta::saveIds($pid,'project-ids',[3,4]); $assert(LegacyMeta::getIds($pid,'project-ids')===[3,4],'ids round trip');
update_post_meta($pid,'image-3',99); $assert(LegacyMeta::productGalleryIds($pid)===[99],'gallery fallback image-3');
update_post_meta($pid,'galerry_produ','7,8'); $assert(LegacyMeta::productGalleryIds($pid)===[7,8],'galerry_produ has gallery priority');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
