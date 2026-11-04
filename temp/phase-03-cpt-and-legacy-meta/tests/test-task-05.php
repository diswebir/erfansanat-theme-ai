<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
$errs=[]; set_error_handler(static function($no,$str,$file)use(&$errs,$themeRoot){ if(str_starts_with($file,$themeRoot)&&!str_starts_with($file,$themeRoot.'/temp/')) $errs[]="$str $file"; return false;});
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.5 /project/ redirect ===\n";
use ErfanSanat\Theme\Core\ProjectRedirect;
$assert(ProjectRedirect::shouldRedirect(8366,'anything'),'ID 8366 redirects'); $assert(ProjectRedirect::shouldRedirect(123,'project'),'slug project redirects'); $assert(!ProjectRedirect::shouldRedirect(123,'projects'),'slug projects does NOT redirect'); $assert(!ProjectRedirect::shouldRedirect(123,'about'),'other pages do NOT redirect'); $assert(ProjectRedirect::target()==='/projects/','target /projects/');
$assert(empty($errs),'zero theme warnings ('.implode(';',$errs).')');
restore_error_handler();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
