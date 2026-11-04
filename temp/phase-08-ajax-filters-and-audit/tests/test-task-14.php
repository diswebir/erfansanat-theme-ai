<?php
/** Automated QA Test Script — Task 8.14: Recruitment resume upload. */
declare(strict_types=1);
$root=dirname(__DIR__,3);$failures=[];
$assert=static function(bool $condition,string $label)use(&$failures):void{echo $condition?"  [PASS] {$label}\n":"  [FAIL] {$label}\n";if(!$condition)$failures[]=$label;};
require_once '/opt/wp-testbed/wp-load.php';$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.14: Recruitment resume upload ===\n";
$class='ErfanSanat\\Theme\\Forms\\RecruitmentForm';
$assert(is_file($root.'/inc/Forms/RecruitmentForm.php')&&class_exists($class),'RecruitmentForm exists and autoloads');
$service=new $class();$routes=apply_filters('erfan_sanat_ajax_routes',[]);
$assert(isset($routes['recruitment_submission'])&&is_callable($routes['recruitment_submission']),'recruitment submission route is registered');
$pdf=tempnam(sys_get_temp_dir(),'resume-');file_put_contents($pdf,"%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\n%%EOF");
$valid=['name'=>'Resume QA','phone'=>'09123456789','message'=>'Application','email'=>'qa@example.test','website'=>'','form_nonce'=>wp_create_nonce('erfan_sanat_form')];
$assert($service->validateResume(['name'=>'resume.pdf','tmp_name'=>$pdf,'size'=>filesize($pdf),'error'=>UPLOAD_ERR_OK])===true,'PDF resume MIME, extension and size pass validation');
$badFile=tempnam(sys_get_temp_dir(),'not-pdf-');file_put_contents($badFile,'plain text without PDF magic');
$wrong=$service->validateResume(['name'=>'resume.pdf','tmp_name'=>$badFile,'size'=>filesize($badFile),'error'=>UPLOAD_ERR_OK]);unlink($badFile);
$assert(is_wp_error($wrong),'non-PDF content disguised as PDF is rejected');
$large=$service->validateResume(['name'=>'resume.pdf','tmp_name'=>$pdf,'size'=>6*1024*1024,'error'=>UPLOAD_ERR_OK]);
$assert(is_wp_error($large),'oversized resume is rejected');
$disallowed=$service->validateResume(['name'=>'resume.exe','tmp_name'=>$pdf,'size'=>filesize($pdf),'error'=>UPLOAD_ERR_OK]);
$assert(is_wp_error($disallowed),'unsupported file extensions are rejected');
$template=(string)file_get_contents($root.'/page-templates/template-recruitment.php');
$script=(string)file_get_contents($root.'/assets/js/main.js');
$assert(str_contains($template,'data-recruitment-form="true"')&&str_contains($template,'enctype="multipart/form-data"'),'recruitment template renders the native multipart application form');
$assert(str_contains($template,'name="resume"')&&str_contains($template,'accept=".pdf,.doc,.docx"')&&str_contains($template,'required'),'resume control requires only approved PDF/DOC/DOCX uploads');
$assert(str_contains($script,'function initRecruitmentForm()')&&str_contains($script,'payload.set("route", "recruitment_submission")')&&str_contains($script,'body: payload'),'frontend sends the resume form as multipart to the registered route');
unlink($pdf);
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);}echo "[PASSED] Recruitment upload validation and native form contract tests passed.\n";
