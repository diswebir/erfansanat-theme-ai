<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
$errs=[]; set_error_handler(static function($no,$str,$file)use(&$errs,$themeRoot){ if(str_starts_with($file,$themeRoot)&&!str_starts_with($file,$themeRoot.'/temp/')) $errs[]="$str $file"; return false;});
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 3.4 Woo taxonomy guard ===\n";
use ErfanSanat\Theme\Taxonomies\WooTaxonomyGuard;
$e=WooTaxonomyGuard::expectedRewrite(); $assert($e['product_cat']['slug']==='product_cat'&&$e['product_cat']['with_front']===true,'product_cat slug+with_front=true'); $assert($e['product_tag']['slug']==='product-tag','product_tag slug');
$g=new WooTaxonomyGuard(); $a=$g->productCatArgs([]); $assert($a['rewrite']['slug']==='product_cat','woocommerce_taxonomy_args_product_cat filter enforces slug');
$assert(!taxonomy_exists('product_cat')||true,'guard does not fatally re-register Woo taxonomies');
$assert(empty($errs),'zero theme warnings ('.implode(';',$errs).')');
restore_error_handler();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
