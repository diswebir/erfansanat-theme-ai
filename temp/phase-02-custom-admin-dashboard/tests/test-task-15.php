<?php
declare(strict_types=1);
$themeRoot=dirname(__DIR__,3); $wpRoot='/opt/wp-testbed';
$failures=[];$assert=static function(bool $c,string $l)use(&$failures):void{echo $c?"  [PASS] $l\n":"  [FAIL] $l\n"; if(!$c)$failures[]=$l;};
require_once $wpRoot.'/wp-load.php';
echo "=== [TEST] Task 2.15 SectionRenderer API ===\n";
use ErfanSanat\Theme\Admin\SectionManager\SectionStorage;
$assert(function_exists('es_get_sections')&&function_exists('es_render_sections'),'global section helpers exist');
$pid=wp_insert_post(['post_type'=>'page','post_title'=>'QA Render','post_status'=>'publish']);
SectionStorage::save($pid,[['id'=>'x','type'=>'alpha','enabled'=>true,'order'=>1,'fields'=>[]],['id'=>'y','type'=>'beta','enabled'=>false,'order'=>0,'fields'=>[]],['id'=>'z','type'=>'gamma','enabled'=>true,'order'=>2,'fields'=>[]]]);
add_filter('erfansanat_render_section_html', static fn($html,$s)=>$html.'['.$s['type'].']',10,2);
$out=es_render_sections($pid);
$assert($out==='[alpha][gamma]',"only enabled sections rendered in order (got $out)");
$assert(count(es_get_sections($pid))===3,'es_get_sections returns all incl disabled');
wp_delete_post($pid,true);
if($failures){fwrite(STDERR,"[FAILED]\n");exit(1);} echo "\n[PASSED] Task 2.15\n";
