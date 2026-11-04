<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
$errs=[]; set_error_handler(static function($no,$str,$file)use(&$errs,$themeRoot){ if(str_starts_with($file,$themeRoot)&&!str_starts_with($file,$themeRoot.'/temp/')) $errs[]="$str $file"; return false;});
require_once $wpRoot.'/wp-load.php'; $GLOBALS['wp']->main();
$render=static function(string $part): string { ob_start(); get_template_part($part); return (string)ob_get_clean(); };
$check=static function(string $html, array $needles) use ($assert): void { foreach($needles as $n) $assert(str_contains($html,$n),"contains: $n"); };
echo "=== [TEST] Task 5.20 homepage audit ===\n";
$pid=wp_insert_post(['post_type'=>'page','post_title'=>'خانه','post_status'=>'publish']); update_option('show_on_front','page'); update_option('page_on_front',$pid);
$proj=wp_insert_post(['post_type'=>'projects','post_title'=>'پروژه شاخص QA','post_status'=>'publish']);
$post=wp_insert_post(['post_type'=>'post','post_title'=>'مقاله شاخص QA','post_status'=>'publish']);
ob_start(); get_header(); $h1=(string)ob_get_clean();
$main=''; foreach(es_get_sections($pid) as $s){ if(empty($s['enabled'])) continue; $GLOBALS['es_section']=$s; $main.=$render('template-parts/home/'.$s['type']); } unset($GLOBALS['es_section']);
foreach(['data-home1-hero','id="projects"','id="about"','id="products"','id="services"','id="blog-preview"','id="contact"','پروژه شاخص QA','مقاله شاخص QA','data-counter-target','data-services-section'] as $n2) $assert(str_contains($main,$n2),"front page contains: $n2");
$assert(str_contains($h1,'data-navbar-header'),'header renders before sections');
wp_delete_post($proj,true); wp_delete_post($post,true); wp_delete_post($pid,true); update_option('show_on_front','posts'); delete_option('page_on_front');
$assert(empty($errs),'zero theme warnings ('.implode(';',$errs).')'); restore_error_handler();
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED]\n";
