<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.13 Project metabox ===\n";
use ErfanSanat\Theme\Admin\Metaboxes\ProjectMetabox;
$assert(array_keys(ProjectMetabox::galleryKeys())===['second-image','three-image','foure-image','five-image','six-image'],'exact gallery keys incl foure-image typo');
$pid=wp_insert_post(['post_type'=>'projects','post_title'=>'QA','post_status'=>'publish']);
ProjectMetabox::saveValues($pid,['foure-image'=>'42','finish-pro-time'=>'1404/01/01','second-image'=>7]);
$assert((int)get_post_meta($pid,'foure-image',true)===42,'foure-image saved'); $assert(get_post_meta($pid,'finish-pro-time',true)==='1404/01/01','finish-pro-time saved');
$assert(ProjectMetabox::galleryIds($pid)===[7,42],'galleryIds returns stored ids in order');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
