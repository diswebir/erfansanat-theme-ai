<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
$mkprod=static function(): int { $id=wp_insert_post(['post_type'=>'post','post_title'=>'QA Product','post_status'=>'publish']); return (int)$id; };
echo "=== [TEST] Task 3.11 Product relations ===\n";
use ErfanSanat\Theme\Admin\Metaboxes\ProductRelationFields; use ErfanSanat\Theme\Helpers\LegacyMeta;
$pid=$mkprod(); ProductRelationFields::saveValues($pid,['project-ids'=>'10,11','usage_countr'=>[7]]);
$assert(LegacyMeta::getIds($pid,'project-ids')===[10,11],'project-ids saved'); $assert(LegacyMeta::getIds($pid,'usage_countr')===[7],'usage_countr exact key saved');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
