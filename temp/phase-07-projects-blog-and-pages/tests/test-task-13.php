<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.13 contact page ===\n";
$pageId = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'تماس با ما QA']);
update_post_meta($pageId, '_es_sections', [
    ['id' => 'contact-details', 'type' => 'contact-details', 'enabled' => true, 'order' => 0, 'fields' => ['title' => 'اطلاعات تماس سفارشی QA']],
    ['id' => 'contact-form', 'type' => 'contact-form', 'enabled' => true, 'order' => 1, 'fields' => ['title' => 'فرم درخواست QA']],
]);
$options = get_option('erfansanat_theme_options', []); $options = is_array($options) ? $options : [];
$options = array_merge($options, ['contact_phone' => '031-12345678', 'contact_mobile' => '09120001122', 'contact_email' => 'qa@example.test', 'contact_address' => 'خیابان آزمون، اصفهان', 'contact_working_hours' => 'شنبه تا چهارشنبه']);
update_option('erfansanat_theme_options', $options); \ErfanSanat\Theme\Helpers\Options::flush();
$GLOBALS['post'] = get_post($pageId); setup_postdata($GLOBALS['post']);
ob_start(); include $themeRoot . '/page-templates/template-contact.php'; $html = (string) ob_get_clean();
foreach (['اطلاعات تماس سفارشی QA', '031-12345678', '09120001122', 'qa@example.test', 'خیابان آزمون، اصفهان', 'شنبه تا چهارشنبه', 'data-contact-form-slot="true"', 'فرم درخواست QA', 'maps.google.com'] as $needle) $assert(str_contains($html, $needle), 'contact page contract ' . $needle);
$assert(is_file($themeRoot . '/page-templates/template-contact.php'), 'contact page template exists');
wp_delete_post($pageId, true); \ErfanSanat\Theme\Helpers\Options::flush(); $finishTest();
