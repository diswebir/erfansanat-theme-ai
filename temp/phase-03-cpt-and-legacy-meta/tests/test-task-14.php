<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.14 Project products ===\n";
use ErfanSanat\Theme\Admin\Metaboxes\ProjectProductsField; use ErfanSanat\Theme\Helpers\LegacyMeta;
$assert(ProjectProductsField::KEY==='mortabet_mahsole_2','exact key mortabet_mahsole_2');
$pid=wp_insert_post(['post_type'=>'projects','post_title'=>'QA','post_status'=>'publish']);
ProjectProductsField::saveValues($pid,['mortabet_mahsole_2'=>'3, 9']); $assert(LegacyMeta::getIds($pid,'mortabet_mahsole_2')===[3,9],'products saved as ids');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
