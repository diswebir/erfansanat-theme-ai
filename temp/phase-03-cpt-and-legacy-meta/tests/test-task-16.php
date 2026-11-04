<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.16 Hreflang ===\n";
use ErfanSanat\Theme\Admin\Metaboxes\{HreflangMetabox,HreflangTermFields};
$assert(array_keys(HreflangMetabox::fields())===['hrefmeta_fa','hrefmeta_en','hrefmeta_ar','same_key_hrefl'],'exact hreflang keys');
$assert(HreflangTermFields::taxonomies()===['category','product_cat','product_tag','product_brand','project_type','city'],'6 taxonomies covered');
$pid=wp_insert_post(['post_type'=>'post','post_title'=>'QA','post_status'=>'publish']);
HreflangMetabox::saveValues($pid,['hrefmeta_en'=>'https://en.test/x','same_key_hrefl'=>'key-1']); $assert(get_post_meta($pid,'hrefmeta_en',true)==='https://en.test/x','post hreflang saved');
$tid=(int)wp_insert_term('QA T','category')['term_id']; HreflangTermFields::saveValues($tid,['same_key_hrefl'=>'tk']); $assert(get_term_meta($tid,'same_key_hrefl',true)==='tk','term hreflang saved');
wp_delete_term($tid,'category'); wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
