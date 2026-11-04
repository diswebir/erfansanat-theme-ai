<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
$errs=[]; set_error_handler(static function($no,$str,$file)use(&$errs,$themeRoot){ if(str_starts_with($file,$themeRoot)&&!str_starts_with($file,$themeRoot.'/temp/')) $errs[]="$str $file"; return false;});
require_once $wpRoot.'/wp-load.php'; $GLOBALS['wp']->main();
$render=static function(string $part): string { ob_start(); get_template_part($part); return (string)ob_get_clean(); };
$check=static function(string $html, array $needles) use ($assert): void { foreach($needles as $n) $assert(str_contains($html,$n),"contains: $n"); };
echo "=== [TEST] Task 4.6 mobile menu ===\n";
$h=$render('template-parts/global/mobile-menu');
$check($h,['data-mobile-menu','data-mobile-backdrop','data-mobile-sidebar','data-mobile-close','lg:hidden','data-search-modal-open','data-cart-modal-open','data-mobile-acc-btn','data-mobile-prod-tab']);
$assert(empty($errs),'zero theme warnings ('.implode(';',$errs).')'); restore_error_handler();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
