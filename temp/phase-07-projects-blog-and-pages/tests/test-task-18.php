<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.18 parent services page ===\n";
$parentId = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'خدمات نورپردازی QA']);
$children = [];
foreach (['پل شهری QA','نورپردازی ساختمان QA','المان نوری QA','میدان QA','معبر شهری QA'] as $index => $title) $children[] = wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>$title,'post_excerpt'=>'توضیح خدمت ' . $index,'post_parent'=>$parentId,'menu_order'=>$index]);
$GLOBALS['post'] = get_post($parentId); setup_postdata($GLOBALS['post']); ob_start(); include $themeRoot . '/page-templates/template-services.php'; $html = (string) ob_get_clean();
foreach (['data-services-page="true"', 'data-service-card=', 'پل شهری QA', 'نورپردازی ساختمان QA', 'المان نوری QA', 'میدان QA', 'معبر شهری QA', 'data-services-process="true"', 'data-services-contact="true"'] as $needle) $assert(str_contains($html, $needle), 'services page contract ' . $needle);
$assert(substr_count($html, 'data-service-card=') === 5, 'five child service pages rendered');
$assert(is_file($themeRoot . '/page-templates/template-services.php'), 'services page template exists');
foreach ($children as $id) wp_delete_post($id, true); wp_delete_post($parentId, true); wp_reset_postdata(); $finishTest();
