<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
$errs=[]; set_error_handler(static function($no,$str,$file)use(&$errs,$themeRoot){ if(str_starts_with($file,$themeRoot)&&!str_starts_with($file,$themeRoot.'/temp/')) $errs[]="$str $file"; return false;});
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.6 project admin columns ===\n";
use ErfanSanat\Theme\Admin\Columns\ProjectColumns;
$c=new ProjectColumns(); $cols=$c->columns(['title'=>'عنوان','date'=>'تاریخ']);
foreach(['es_thumb','city','project_type'] as $k) $assert(isset($cols[$k]),"column $k added");
$pid=wp_insert_post(['post_type'=>'projects','post_title'=>'QA پروژه','post_status'=>'publish']);
wp_set_object_terms($pid,'تهران','city'); ob_start(); $c->renderColumn('city',$pid); $out=(string)ob_get_clean(); $assert(str_contains($out,'تهران'),'city column renders term name');
wp_delete_post($pid,true);
$assert(empty($errs),'zero theme warnings ('.implode(';',$errs).')');
restore_error_handler();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
