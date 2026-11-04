<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.14 licenses and honors gallery ===\n";
$pageId = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'مجوزها QA']);
update_post_meta($pageId, '_es_sections', [
 ['id'=>'licenses-hero','type'=>'licenses-hero','enabled'=>true,'order'=>0,'fields'=>['title'=>'مجوزها و افتخارات QA','body'=>'معرفی مجوزها']],
 ['id'=>'licenses-certificates','type'=>'licenses-certificates','enabled'=>true,'order'=>1,'fields'=>['title'=>'گواهینامه‌های ISO QA','items'=>[['title'=>'گواهی ISO QA','image'=>'https://example.test/iso.jpg','alt'=>'گواهی ISO']]]],
 ['id'=>'licenses-honors','type'=>'licenses-honors','enabled'=>true,'order'=>2,'fields'=>['title'=>'تقدیرنامه‌ها QA','items'=>[['title'=>'تقدیرنامه QA','image'=>'https://example.test/honor.jpg','alt'=>'تقدیرنامه']]]],
]);
$GLOBALS['post'] = get_post($pageId); setup_postdata($GLOBALS['post']); ob_start(); include $themeRoot . '/page-templates/template-licenses.php'; $html = (string) ob_get_clean();
foreach (['data-license-section="hero"', 'مجوزها و افتخارات QA', 'data-license-section="certificates"', 'گواهینامه‌های ISO QA', 'گواهی ISO QA', 'data-license-section="honors"', 'تقدیرنامه‌ها QA', 'تقدیرنامه QA', 'data-gallery-root="true"', 'data-gallery-lightbox="true"'] as $needle) $assert(str_contains($html, $needle), 'licenses page contract ' . $needle);
$assert(substr_count($html, 'data-gallery-root="true"') >= 2, 'certificate and honors galleries are independently usable');
$assert(is_file($themeRoot . '/page-templates/template-licenses.php'), 'licenses page template exists');
wp_delete_post($pageId, true); $finishTest();
