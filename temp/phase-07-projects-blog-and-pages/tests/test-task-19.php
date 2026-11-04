<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.19 service detail page ===\n";
if (!post_type_exists('product')) register_post_type('product', ['public'=>true,'supports'=>['title','editor','thumbnail']]);
$parentId = wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'خدمات QA']);
$serviceId = wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'نورپردازی پل QA','post_excerpt'=>'خدمت نورپردازی پل','post_parent'=>$parentId]);
$projectId = $makeProject('پروژه پل مرتبط QA');
$productId = wp_insert_post(['post_type'=>'product','post_status'=>'publish','post_title'=>'محصول نورپردازی مرتبط QA']);
update_post_meta($serviceId, '_es_sections', [
 ['id'=>'service-hero','type'=>'service-hero','enabled'=>true,'order'=>0,'fields'=>['title'=>'خدمت نورپردازی پل QA','body'=>'توضیحات تخصصی خدمت QA']],
 ['id'=>'service-details','type'=>'service-details','enabled'=>true,'order'=>1,'fields'=>['title'=>'دامنه خدمات QA','body'=>'مشاوره، طراحی و اجرا','items'=>['طراحی فنی QA'],'projects'=>[$projectId],'products'=>[$productId]]],
 ['id'=>'service-contact','type'=>'service-contact','enabled'=>true,'order'=>2,'fields'=>['title'=>'درخواست خدمت QA','body'=>'برای بررسی پروژه تماس بگیرید.']],
]);
$GLOBALS['post'] = get_post($serviceId); setup_postdata($GLOBALS['post']); ob_start(); include $themeRoot . '/page-templates/template-service-single.php'; $html = (string) ob_get_clean();
foreach (['data-service-page="true"', 'data-service-hero="true"', 'خدمت نورپردازی پل QA', 'خدمات QA', 'data-service-details="true"', 'طراحی فنی QA', 'data-card-project=', 'پروژه پل مرتبط QA', 'data-card-product=', 'محصول نورپردازی مرتبط QA', 'data-service-contact="true"'] as $needle) $assert(str_contains($html, $needle), 'service detail contract ' . $needle);
$assert(is_file($themeRoot . '/page-templates/template-service-single.php'), 'service detail template exists');
wp_delete_post($productId,true); wp_delete_post($projectId,true); wp_delete_post($serviceId,true); wp_delete_post($parentId,true); wp_reset_postdata(); $finishTest();
