<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.17 ViewCounter ===\n";
use ErfanSanat\Theme\Helpers\ViewCounter;
$pid=wp_insert_post(['post_type'=>'post','post_title'=>'QA','post_status'=>'publish']);
$assert(ViewCounter::get($pid)===0,'zero when empty'); update_post_meta($pid,'number-viewc',41); $assert(ViewCounter::get($pid)===41,'legacy number-viewc fallback read');
$assert(ViewCounter::increment($pid)===42,'increment writes on top of legacy fallback'); $assert((int)get_post_meta($pid,'number2-viewc',true)===42,'stored in number2-viewc');
wp_set_current_user(1); $assert(ViewCounter::maybeCount($pid)===null,'admin view not counted'); wp_set_current_user(0);
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
