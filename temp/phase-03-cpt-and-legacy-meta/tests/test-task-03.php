<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
$errs=[]; set_error_handler(static function($no,$str,$file)use(&$errs,$themeRoot){ if(str_starts_with($file,$themeRoot)&&!str_starts_with($file,$themeRoot.'/temp/')) $errs[]="$str $file"; return false;});
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.3 product_brand ===\n";
$assert(taxonomy_exists('product_brand'),'product_brand exists'); $o=get_taxonomy('product_brand'); $assert($o->hierarchical,'hierarchical'); $assert(in_array('product',$o->object_type,true),'attached to product');
$assert(empty($errs),'zero theme warnings ('.implode(';',$errs).')');
restore_error_handler();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
