<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
$mkprod=static function(): int { $id=wp_insert_post(['post_type'=>'post','post_title'=>'QA Product','post_status'=>'publish']); return (int)$id; };
echo "=== [TEST] Task 3.8 ProductMetabox shell ===\n";
use ErfanSanat\Theme\Admin\Metaboxes\ProductMetabox;
$assert(has_action('add_meta_boxes_product')!==false,'metabox hook registered'); $assert(has_action('save_post_product')!==false,'save hook registered');
$assert(count(ProductMetabox::groups())===4,'four field groups registered');
$pid=$mkprod(); $post=get_post($pid); ob_start(); (new ProductMetabox())->render($post); $h=(string)ob_get_clean();
$assert(str_contains($h,'data-es-product-metabox')&&str_contains($h,'es_product_nonce'),'shell + nonce rendered');
ProductMetabox::saveGroups($pid,['gs1_id'=>'123']); $assert(get_post_meta($pid,'gs1_id',true)==='123','saveGroups delegates to groups');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
