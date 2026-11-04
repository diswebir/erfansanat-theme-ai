<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.3 project content and relations ===\n";
if (!post_type_exists('product')) register_post_type('product', ['public' => true, 'supports' => ['title', 'editor', 'thumbnail']]);
$id = $makeProject('پروژه مرجع QA', 'شرح فنی کامل پروژه QA');
$productId = wp_insert_post(['post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول استفاده‌شده QA']);
update_post_meta($id, 'mortabet_mahsole_2', [$productId]);
$otherId = $makeProject('پروژه دیگر QA', 'پروژه مشابه');
$GLOBALS['post'] = get_post($id); setup_postdata($GLOBALS['post']); $GLOBALS['es_project_id'] = $id;
$html = $renderPart('template-parts/project/content-related');
foreach (['id="project-details"', 'شرح فنی کامل پروژه QA', 'id="used-products"', 'محصول استفاده‌شده QA', 'data-card-product', 'پروژه دیگر QA', 'data-card-project'] as $needle) $assert(str_contains($html, $needle), 'project relation contract ' . $needle);
update_post_meta($id, 'mortabet_mahsole_2', []); $html2 = $renderPart('template-parts/project/content-related'); $assert(!str_contains($html2, 'id="used-products"'), 'used-products section omitted when relation meta is empty');
wp_delete_post($productId, true); wp_delete_post($otherId, true); wp_delete_post($id, true); wp_reset_postdata(); unset($GLOBALS['es_project_id']); $finishTest();
