<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.16 warranty and returns page ===\n";
$pageId = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'گارانتی QA']);
update_post_meta($pageId, '_es_sections', [
 ['id'=>'warranty-terms','type'=>'warranty-terms','enabled'=>true,'order'=>0,'fields'=>['title'=>'شرایط گارانتی QA','items'=>['گارانتی تعویض ۲۴ ماهه QA','پوشش عیب تولید QA']]],
 ['id'=>'warranty-process','type'=>'warranty-process','enabled'=>true,'order'=>1,'fields'=>['title'=>'فرآیند خدمات QA','items'=>['تماس با پشتیبانی QA','بررسی فنی QA']]],
 ['id'=>'warranty-contact','type'=>'warranty-contact','enabled'=>true,'order'=>2,'fields'=>['title'=>'پشتیبانی گارانتی QA','body'=>'برای پیگیری گارانتی تماس بگیرید.']],
]);
$GLOBALS['post'] = get_post($pageId); setup_postdata($GLOBALS['post']); ob_start(); include $themeRoot . '/page-templates/template-warranty.php'; $html = (string) ob_get_clean();
foreach (['data-warranty-section="terms"', 'شرایط گارانتی QA', 'گارانتی تعویض ۲۴ ماهه QA', 'data-warranty-section="process"', 'فرآیند خدمات QA', 'تماس با پشتیبانی QA', 'data-warranty-section="contact"', 'پشتیبانی گارانتی QA'] as $needle) $assert(str_contains($html, $needle), 'warranty page contract ' . $needle);
$assert(is_file($themeRoot . '/page-templates/template-warranty.php'), 'warranty template exists');
wp_delete_post($pageId, true); $finishTest();
