<?php
/** Automated QA Test Script — Task 8.15: Client feedback form. */
declare(strict_types=1);
$root=dirname(__DIR__,3);$failures=[];
$assert=static function(bool $condition,string $label)use(&$failures):void{echo $condition?"  [PASS] {$label}\n":"  [FAIL] {$label}\n";if(!$condition)$failures[]=$label;};
require_once '/opt/wp-testbed/wp-load.php';$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.15: Client feedback form ===\n";
$class='ErfanSanat\\Theme\\Forms\\ClientFeedbackForm';
$assert(is_file($root.'/inc/Forms/ClientFeedbackForm.php')&&class_exists($class),'ClientFeedbackForm exists and autoloads');
$routes=apply_filters('erfan_sanat_ajax_routes',[]);$assert(isset($routes['client_feedback'])&&is_callable($routes['client_feedback']),'client_feedback route is registered');
$css=(string)file_get_contents($root.'/assets/css/styles.css');$templateFiles=array_merge(glob($root.'/page-templates/*.php')?:[],glob($root.'/template-parts/**/*.php')?:[]);
$source=implode("\n",array_map(static fn($f)=>(string)file_get_contents($f),$templateFiles));
$assert(!str_contains($source,'client-feedback')&&!str_contains($css,'client-feedback'),'repository has no static client-feedback form markup or stylesheet selector; do not fabricate UI');
$_SERVER['REMOTE_ADDR']='192.0.2.'.random_int(10,200);$form=new $class();
$result=$form->submit(['form_nonce'=>wp_create_nonce('erfan_sanat_form'),'website'=>'','name'=>'Feedback QA','organization'=>'QA Co','rating'=>'5','message'=>'Excellent work']);
$assert(is_array($result)&&is_int($result['submission_id']??null),'valid rating feedback is stored in the message CPT');
$invalid=$form->submit(['form_nonce'=>wp_create_nonce('erfan_sanat_form'),'website'=>'','name'=>'Feedback QA','rating'=>'9','message'=>'Bad rating']);
$assert(is_wp_error($invalid),'out-of-range ratings are rejected');
if(is_array($result))wp_delete_post($result['submission_id'],true);
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);}echo "[PASSED] Client feedback handler tests passed; UI remains blocked by absent static form source.\n";
