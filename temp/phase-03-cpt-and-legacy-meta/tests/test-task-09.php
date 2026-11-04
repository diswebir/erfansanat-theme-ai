<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
$mkprod=static function(): int { $id=wp_insert_post(['post_type'=>'post','post_title'=>'QA Product','post_status'=>'publish']); return (int)$id; };
echo "=== [TEST] Task 3.9 Product identity fields ===\n";
use ErfanSanat\Theme\Admin\Metaboxes\ProductIdentityFields;
$assert(array_keys(ProductIdentityFields::fields())===['gs1_id','national_code','hs_id','isic_id','cpc_id','product_id'],'exact 6 identity keys');
$pid=$mkprod(); ProductIdentityFields::saveValues($pid,['gs1_id'=>' 890 ','product_id'=>'ES-1']); $assert(get_post_meta($pid,'gs1_id',true)==='890','gs1 saved trimmed'); $assert(get_post_meta($pid,'product_id',true)==='ES-1','product_id saved');
ob_start(); ProductIdentityFields::render(get_post($pid)); $h=(string)ob_get_clean(); $assert(str_contains($h,'es_product[national_code]'),'renders all inputs');
$assert(has_action('quick_edit_custom_box')!==false,'quick edit hook registered');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
