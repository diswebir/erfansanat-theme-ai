<?php
/** Automated QA Test Script — Task 8.9: Project filter endpoint. */
declare(strict_types=1);
$root=dirname(__DIR__,3);$failures=[];
$assert=static function(bool $condition,string $label)use(&$failures):void{echo $condition?"  [PASS] {$label}\n":"  [FAIL] {$label}\n";if(!$condition)$failures[]=$label;};
require_once '/opt/wp-testbed/wp-load.php';$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.9: Project filter endpoint ===\n";
$class='ErfanSanat\\Theme\\Ajax\\ProjectFilterEndpoint';$queryClass='ErfanSanat\\Theme\\Filters\\ProjectFilterQuery';
$assert(is_file($root.'/inc/Ajax/ProjectFilterEndpoint.php')&&is_file($root.'/inc/Filters/ProjectFilterQuery.php'),'project endpoint and query builder files exist');
$assert(class_exists($class)&&class_exists($queryClass),'project endpoint and query builder autoload');
$endpoint=new $class();$routes=apply_filters('erfan_sanat_ajax_routes',[]);
$assert(isset($routes['project_filter'])&&is_callable($routes['project_filter']),'project_filter route is registered');
$script=(string)file_get_contents($root.'/assets/js/main.js');$start=strpos($script,'function initProjectsArchive()');$end=strpos($script,'  /* ───────────────────────────────────────────────',$start+10);$section=$start!==false&&$end!==false?substr($script,$start,$end-$start):'';
$assert(str_contains($section,'project_filter')&&str_contains($section,'ajaxNonce'),'project archive client uses the nonce-protected AJAX endpoint');
$assert(str_contains($section,'data-project-type-filter')&&str_contains($section,'data-project-location-filter'),'client sends project type, city, and search state');
$assert(!str_contains($section,'window.location.search')&&!str_contains($section,'pushState')&&!str_contains($section,'replaceState'),'project filters do not use query strings or mutate browser history');
$typeSlug='phase8-type-'.strtolower(wp_generate_password(5,false,false));$citySlug='phase8-city-'.strtolower(wp_generate_password(5,false,false));
$type=wp_insert_term('Phase 8 Type QA '.$typeSlug,'project_type',['slug'=>$typeSlug]);$city=wp_insert_term('Phase 8 City QA '.$citySlug,'city',['slug'=>$citySlug]);
if(is_wp_error($type)||is_wp_error($city)){fwrite(STDERR,"Could not create taxonomy fixtures.\n");exit(1);}
$ids=[];
foreach([['Bridge Search Endpoint QA',true],['Tunnel Search Endpoint QA',false],['Other Published QA',true]] as [$title,$match]){$id=wp_insert_post(['post_type'=>'projects','post_status'=>'publish','post_title'=>$title]);$ids[]=(int)$id;if($match){wp_set_object_terms($id,[(int)$type['term_id']],'project_type');wp_set_object_terms($id,[(int)$city['term_id']],'city');}}
$args=$queryClass::buildArgs(['project_type'=>$typeSlug,'city'=>$citySlug,'search'=>'Search Endpoint QA']);
$assert(isset($args['tax_query'])&&count($args['tax_query'])===3,'project type and city taxonomies are AND-combined with search');
$result=$endpoint->search(['project_type'=>$typeSlug,'city'=>$citySlug,'search'=>'Search Endpoint QA','sort'=>'newest']);
$assert(($result['count']??0)===1&&($result['data']['items'][0]??0)===$ids[0],'type/city/search returns only the expected published project');
$assert(str_contains($result['html']??'','data-card-project='),'results reuse existing project-card markup');
$empty=$endpoint->search(['project_type'=>'no-such-type-qa']);
$assert(($empty['count']??-1)===0&&str_contains($empty['data']['empty_html']??'','پروژه‌ای با این مشخصات پیدا نشد'),'empty query returns project archive empty state');
foreach($ids as $id)wp_delete_post($id,true);wp_delete_term((int)$type['term_id'],'project_type');wp_delete_term((int)$city['term_id'],'city');
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);}echo "[PASSED] Task 8.9 checks completed successfully.\n";
