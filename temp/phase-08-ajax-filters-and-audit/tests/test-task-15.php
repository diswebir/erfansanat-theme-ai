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
$template=(string)file_get_contents($root.'/template-parts/forms/client-feedback.php');
$homeClients=(string)file_get_contents($root.'/template-parts/home/section-12-clients.php');
$script=(string)file_get_contents($root.'/assets/js/main.js');
$assert(str_contains($template,'data-client-feedback-form="true"')&&str_contains($homeClients,"get_template_part('template-parts/forms/client-feedback')"),'native feedback form is rendered in the existing clients section');
$assert(str_contains($template,'name="name"')&&str_contains($template,'name="organization"')&&str_contains($template,'name="rating"')&&str_contains($template,'name="message"')&&str_contains($template,'name="website"'),'feedback form supplies all fields required by its backend and honeypot');
$assert(str_contains($script,'function initClientFeedbackForm()')&&str_contains($script,'payload.set("route", "client_feedback")'),'frontend submits the feedback form through its registered AJAX route');
$_SERVER['REMOTE_ADDR']='192.0.2.'.random_int(10,200);$form=new $class();
$result=$form->submit(['form_nonce'=>wp_create_nonce('erfan_sanat_form'),'website'=>'','name'=>'Feedback QA','organization'=>'QA Co','rating'=>'5','message'=>'Excellent work']);
$assert(is_array($result)&&is_int($result['submission_id']??null),'valid rating feedback is stored in the message CPT');
$invalid=$form->submit(['form_nonce'=>wp_create_nonce('erfan_sanat_form'),'website'=>'','name'=>'Feedback QA','rating'=>'9','message'=>'Bad rating']);
$assert(is_wp_error($invalid),'out-of-range ratings are rejected');
if(is_array($result))wp_delete_post($result['submission_id'],true);
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);}echo "[PASSED] Client feedback handler and native UI contract tests passed.\n";
