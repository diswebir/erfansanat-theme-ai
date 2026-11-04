<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.17 recruitment page ===\n";
$pageId = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'استخدام QA']);
$sections = [['id'=>'recruitment-hero','type'=>'recruitment-hero','enabled'=>true,'order'=>0,'fields'=>['title'=>'همکاری با عرفان صنعت QA','body'=>'فرصت‌های شغلی']]];
foreach ([['rd','تحقیق و توسعه'],['projects','پروژه'],['finance','مالی'],['it','فناوری اطلاعات']] as $i => [$key,$label]) $sections[] = ['id'=>'recruitment-dept-'.$key,'type'=>'recruitment-dept-'.$key,'enabled'=>true,'order'=>$i+1,'fields'=>['title'=>$label.' QA','body'=>'توضیح '.$label.' QA']];
update_post_meta($pageId, '_es_sections', $sections); $GLOBALS['post'] = get_post($pageId); setup_postdata($GLOBALS['post']);
ob_start(); include $themeRoot . '/page-templates/template-recruitment.php'; $html = (string) ob_get_clean();
foreach (['data-recruit-tabs="true"', 'data-recruit-tab-btn="rd"', 'data-recruit-tab-btn="projects"', 'data-recruit-tab-btn="finance"', 'data-recruit-tab-btn="it"', 'data-recruit-panel="rd"', 'data-recruit-panel="projects"', 'data-recruit-panel="finance"', 'data-recruit-panel="it"', 'data-recruit-form-slot="true"', 'همکاری با عرفان صنعت QA', 'توضیح تحقیق و توسعه QA'] as $needle) $assert(str_contains($html, $needle), 'recruitment page contract ' . $needle);
$assert(is_file($themeRoot . '/page-templates/template-recruitment.php'), 'recruitment template exists');
wp_delete_post($pageId, true); $finishTest();
