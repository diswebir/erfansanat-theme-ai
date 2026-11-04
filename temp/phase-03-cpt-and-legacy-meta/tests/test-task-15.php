<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.15 product_cat term fields ===\n";
use ErfanSanat\Theme\Admin\Metaboxes\ProductCatTermFields; use ErfanSanat\Theme\Helpers\LegacyMeta;
$tid=(int)wp_insert_term('QA Cat','category')['term_id'];
ProductCatTermFields::saveValues($tid,['thumbnail'=>'15','faq'=>[['name_question'=>'س','answer_question'=>'ج']]]);
$assert((int)get_term_meta($tid,'thumbnail',true)===15,'thumbnail term meta saved'); $rows=LegacyMeta::parseRows(get_term_meta($tid,'faq',true)); $assert($rows[0]['name_question']==='س','faq term rows saved');
wp_delete_term($tid,'category');
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
