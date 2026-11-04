<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
$mkprod=static function(): int { $id=wp_insert_post(['post_type'=>'post','post_title'=>'QA Product','post_status'=>'publish']); return (int)$id; };
echo "=== [TEST] Task 3.12 Product repeaters ===\n";
use ErfanSanat\Theme\Admin\Metaboxes\ProductRepeaters; use ErfanSanat\Theme\Helpers\LegacyMeta;
$d=ProductRepeaters::definitions(); $assert(array_keys($d)===['product-feature','product-feature2_copy','usage_product','faq'],'exact 4 repeater keys'); $assert($d['product-feature2_copy']===['add-new-feature','vaule_of_variable'],'vaule_of_variable typo preserved');
$pid=$mkprod(); ProductRepeaters::saveValues($pid,['faq'=>[['name_question'=>'س','answer_question'=>'ج'],['name_question'=>'','answer_question'=>'']],'product-feature2_copy'=>[['add-new-feature'=>'گارانتی','vaule_of_variable'=>'۲۴ ماه']]]);
$faq=LegacyMeta::getRows($pid,'faq'); $assert(count($faq)===1&&$faq[0]['name_question']==='س','faq saved, empty rows dropped'); $assert(LegacyMeta::getRows($pid,'product-feature2_copy')[0]['vaule_of_variable']==='۲۴ ماه','feature2 value saved');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
