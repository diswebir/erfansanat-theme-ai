<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
$errs=[]; set_error_handler(static function($no,$str,$file)use(&$errs,$themeRoot){ if(str_starts_with($file,$themeRoot)&&!str_starts_with($file,$themeRoot.'/temp/')) $errs[]="$str $file"; return false;});
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.2 project taxonomies ===\n";
foreach(['project_type','city'] as $tax){ $assert(taxonomy_exists($tax),"$tax exists"); $o=get_taxonomy($tax); $assert($o->hierarchical,"$tax hierarchical"); $assert($o->rewrite['slug']===$tax&&$o->rewrite['with_front']===false,"$tax rewrite"); $assert(in_array('projects',$o->object_type,true),"$tax attached to projects"); }
$assert(empty($errs),'zero theme warnings ('.implode(';',$errs).')');
restore_error_handler();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
