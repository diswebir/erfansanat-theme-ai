<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
$errs=[]; set_error_handler(static function($no,$str,$file)use(&$errs,$themeRoot){ if(str_starts_with($file,$themeRoot)&&!str_starts_with($file,$themeRoot.'/temp/')) $errs[]="$str $file"; return false;});
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.1 CPT projects ===\n";
$assert(post_type_exists('projects'),'post type projects registered');
$o=get_post_type_object('projects'); $assert($o->public&&$o->has_archive,'public + has_archive');
$assert($o->rewrite['slug']==='projects'&&$o->rewrite['with_front']===false,'rewrite projects/with_front=false');
$assert($o->show_in_rest===true,'show_in_rest'); foreach(['title','editor','thumbnail','comments','custom-fields','revisions'] as $s) $assert(post_type_supports('projects',$s),"supports $s");
$assert(empty($errs),'zero theme warnings ('.implode(';',$errs).')');
restore_error_handler();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
