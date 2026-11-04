<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.15 FAQ page ===\n";
$pageId = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'سوالات متداول QA']);
$sections = [];
foreach ([['faq-products', 'محصولات'], ['faq-projects', 'پروژه‌ها'], ['faq-services', 'خدمات']] as $index => [$type, $title]) {
    $sections[] = ['id'=>$type, 'type'=>$type, 'enabled'=>true, 'order'=>$index, 'fields'=>['title'=>$title . ' QA', 'questions'=>[['question'=>'پرسش ' . $title . ' QA؟','answer'=>'پاسخ ' . $title . ' QA']]]];
}
update_post_meta($pageId, '_es_sections', $sections); $GLOBALS['post'] = get_post($pageId); setup_postdata($GLOBALS['post']);
ob_start(); include $themeRoot . '/page-templates/template-faq.php'; $html = (string) ob_get_clean();
foreach (['data-faq-section="products"', 'data-faq-section="projects"', 'data-faq-section="services"', 'data-faq-list="true"', 'data-faq-toggle="0"', 'data-faq-body="0"', 'پرسش محصولات QA؟', 'پاسخ محصولات QA', 'application/ld+json', '"@type":"FAQPage"'] as $needle) $assert(str_contains($html, $needle), 'FAQ page contract ' . $needle);
$assert(substr_count($html, 'data-faq-list="true"') === 3, 'three editable FAQ category lists render');
$assert(is_file($themeRoot . '/page-templates/template-faq.php'), 'FAQ template exists');
wp_delete_post($pageId, true); $finishTest();
