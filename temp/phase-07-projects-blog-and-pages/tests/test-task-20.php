<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.20 Arya smart door-opener landing ===\n";
$pageId = wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'درب بازکن هوشمند آریا QA','post_content'=>'توضیحات محصول آریا QA']);
update_post_meta($pageId, '_es_sections', [
 ['id'=>'arya-hero','type'=>'arya-hero','enabled'=>true,'order'=>0,'fields'=>['title'=>'آریا؛ درب بازکن هوشمند QA','body'=>'توضیح قلابی نمی‌سازیم؛ توضیح واقعی از سکشن مدیر می‌آید.','button_text'=>'مشاوره آریا','button_url'=>'/contact/','image'=>'https://example.test/arya.jpg']],
 ['id'=>'arya-features','type'=>'arya-features','enabled'=>true,'order'=>1,'fields'=>['title'=>'ویژگی‌های آریا QA','items'=>['کنترل هوشمند QA','نصب تخصصی QA']]],
 ['id'=>'arya-faq','type'=>'arya-faq','enabled'=>true,'order'=>2,'fields'=>['title'=>'پرسش‌های آریا QA','items'=>['پرسش و پاسخ QA']]],
]);
$GLOBALS['post']=get_post($pageId); setup_postdata($GLOBALS['post']); ob_start(); include $themeRoot . '/page-templates/template-landing-arya.php'; $html=(string)ob_get_clean();
foreach (['data-arya-landing="true"', 'data-landing-menu="landing"', 'data-arya-section="arya-hero"', 'آریا؛ درب بازکن هوشمند QA', 'data-arya-section="arya-features"', 'کنترل هوشمند QA', 'data-arya-section="arya-faq"', 'مشاوره آریا', 'href="/contact/"'] as $needle) $assert(str_contains($html, $needle), 'Arya landing contract ' . $needle);
$assert(is_file($themeRoot . '/page-templates/template-landing-arya.php'), 'Arya landing template exists');
$assert(str_contains($html, 'loading="eager"') && str_contains($html, 'fetchpriority="high"') && str_contains($html, 'decoding="async"'), 'landing hero image is prioritized for LCP');
delete_post_meta($pageId, '_es_sections'); ob_start(); include $themeRoot . '/page-templates/template-landing-arya.php'; $fallback = (string) ob_get_clean();
$assert(str_contains($fallback, 'درب بازکن هوشمند آریا QA') && str_contains($fallback, 'توضیحات محصول آریا QA'), 'landing fallback uses real page title/content when no sections exist');
wp_delete_post($pageId,true); wp_reset_postdata(); $finishTest();
