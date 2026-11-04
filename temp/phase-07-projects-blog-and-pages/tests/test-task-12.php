<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.12 about page sections ===\n";
$pageId = wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'درباره ما QA']);
$types = ['about-hero', 'about-story', 'about-iso', 'about-values', 'about-contact'];
$sections = [];
foreach ($types as $index => $type) $sections[] = ['id' => $type, 'type' => $type, 'enabled' => true, 'order' => $index, 'fields' => ['title' => 'عنوان ' . $type . ' QA', 'body' => 'محتوای ' . $type . ' QA']];
update_post_meta($pageId, '_es_sections', $sections);
$GLOBALS['post'] = get_post($pageId); setup_postdata($GLOBALS['post']);
ob_start(); include $themeRoot . '/page-templates/template-about.php'; $html = (string) ob_get_clean();
foreach ($types as $type) { $assert(str_contains($html, 'data-about-section="' . $type . '"'), 'section rendered ' . $type); $assert(str_contains($html, 'عنوان ' . $type . ' QA') && str_contains($html, 'محتوای ' . $type . ' QA'), 'section fields rendered ' . $type); }
$assert(is_file($themeRoot . '/page-templates/template-about.php'), 'about page template exists');
delete_post_meta($pageId, '_es_sections'); ob_start(); include $themeRoot . '/page-templates/template-about.php'; $fallback = (string) ob_get_clean();
$assert(substr_count($fallback, 'data-about-section=') === 5, 'five meaningful default sections render when SectionManager data is empty');
wp_delete_post($pageId, true); wp_reset_postdata(); $finishTest();
