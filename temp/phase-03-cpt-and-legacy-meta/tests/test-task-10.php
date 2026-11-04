<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
$mkprod=static function(): int { $id=wp_insert_post(['post_type'=>'post','post_title'=>'QA Product','post_status'=>'publish']); return (int)$id; };
echo "=== [TEST] Task 3.10 Product sales fields ===\n";
use ErfanSanat\Theme\Admin\Metaboxes\ProductSalesFields;
$pid=$mkprod(); ProductSalesFields::saveValues($pid,['zireonvan'=>'زیر','english_titl'=>'EN Title','before-sale-c'=>'توجه','true_sale'=>'false','product-catalog-file'=>'https://x.test/c.pdf','galerry_produ'=>'5, 6']);
foreach(['zireonvan'=>'زیر','english_titl'=>'EN Title','before-sale-c'=>'توجه','true_sale'=>'false'] as $k=>$v) $assert(get_post_meta($pid,$k,true)===$v,"exact key saved: $k");
$assert(get_post_meta($pid,'galerry_produ',true)==='5,6','galerry_produ normalized comma ids');
ProductSalesFields::saveValues($pid,['true_sale'=>'banana']); $assert(get_post_meta($pid,'true_sale',true)==='true','true_sale coerced to true/false only');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
