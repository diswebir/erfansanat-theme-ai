<?php
/** Automated QA Test Script — Task 8.10: Blog filter endpoint. */
declare(strict_types=1);
$root=dirname(__DIR__,3);$failures=[];
$assert=static function(bool $condition,string $label)use(&$failures):void{echo $condition?"  [PASS] {$label}\n":"  [FAIL] {$label}\n";if(!$condition)$failures[]=$label;};
require_once '/opt/wp-testbed/wp-load.php';$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.10: Blog filter endpoint ===\n";
$class='ErfanSanat\\Theme\\Ajax\\BlogFilterEndpoint';
$assert(is_file($root.'/inc/Ajax/BlogFilterEndpoint.php')&&class_exists($class),'BlogFilterEndpoint exists and autoloads');
$endpoint=new $class();$routes=apply_filters('erfan_sanat_ajax_routes',[]);
$assert(isset($routes['blog_filter'])&&is_callable($routes['blog_filter']),'blog_filter route is registered');
$script=(string)file_get_contents($root.'/assets/js/main.js');$start=strpos($script,'function initBlogArchive()');$end=strpos($script,'  /* ───────────────────────────────────────────────',$start+20);$section=$start!==false&&$end!==false?substr($script,$start,$end-$start):'';
$assert(str_contains($section,'blog_filter')&&str_contains($section,'ajaxNonce'),'blog controls use shared nonce-protected AJAX endpoint');
$assert(!str_contains($section,'pushState')&&!str_contains($section,'replaceState'),'blog filtering does not mutate browser history');
$catSlug='phase8-blog-'.strtolower(wp_generate_password(5,false,false));$category=wp_insert_term('Phase 8 Blog QA '.$catSlug,'category',['slug'=>$catSlug]);
if(is_wp_error($category)){fwrite(STDERR,"Could not create blog category.\n");exit(1);}
$postId=wp_insert_post(['post_type'=>'post','post_status'=>'publish','post_title'=>'Blog Endpoint Search QA']);$draftId=wp_insert_post(['post_type'=>'post','post_status'=>'draft','post_title'=>'Blog Endpoint Search QA Draft']);
wp_set_object_terms($postId,[(int)$category['term_id']],'category');
$result=$endpoint->search(['category'=>$catSlug,'search'=>'Endpoint Search QA']);
$assert(($result['count']??0)===1&&($result['data']['items'][0]??0)===$postId,'category/search returns only the matching published post');
$assert(str_contains($result['html']??'','data-card-post='),'blog results reuse existing card-post markup');
$assert(!in_array($draftId,$result['data']['items']??[],true),'draft articles are excluded');
$empty=$endpoint->search(['category'=>'no-such-blog-category-qa']);
$assert(($empty['count']??-1)===0&&str_contains($empty['data']['empty_html']??'','مقاله‌ای با این مشخصات پیدا نشد'),'empty results return existing blog empty state');
wp_delete_post($postId,true);wp_delete_post($draftId,true);wp_delete_term((int)$category['term_id'],'category');
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);}echo "[PASSED] Task 8.10 checks completed successfully.\n";
