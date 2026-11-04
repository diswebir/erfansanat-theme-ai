<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
$errs=[]; set_error_handler(static function($no,$str,$file)use(&$errs,$themeRoot){ if(str_starts_with($file,$themeRoot)&&!str_starts_with($file,$themeRoot.'/temp/')) $errs[]="$str $file"; return false;});
require_once $wpRoot.'/wp-load.php'; $GLOBALS['wp']->main();
$render=static function(string $part): string { ob_start(); get_template_part($part); return (string)ob_get_clean(); };
$check=static function(string $html, array $needles) use ($assert): void { foreach($needles as $n) $assert(str_contains($html,$n),"contains: $n"); };
echo "=== [TEST] Task 5.2 card-product ===\n";
$pid=wp_insert_post(['post_type'=>'post','post_title'=>'محصول QA','post_status'=>'publish']); update_post_meta($pid,'true_sale','false'); $GLOBALS['es_card_post']=get_post($pid);
$h=$render('template-parts/cards/card-product'); $check($h,['data-card-product','محصول QA','استعلام قیمت','aspect-[4/3]']);
update_post_meta($pid,'true_sale','true'); update_post_meta($pid,'_price','1500000'); $h=$render('template-parts/cards/card-product'); $check($h,['تومان']);
wp_delete_post($pid,true);
$assert(empty($errs),'zero theme warnings ('.implode(';',$errs).')'); restore_error_handler();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
