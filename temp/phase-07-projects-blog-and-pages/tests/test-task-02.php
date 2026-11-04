<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.2 project gallery and identity ===\n";
$id = $makeProject(); update_post_meta($id, 'second-image', 201); update_post_meta($id, 'three-image', 202); update_post_meta($id, 'foure-image', 204); update_post_meta($id, 'five-image', 205); update_post_meta($id, 'six-image', 206); update_post_meta($id, 'finish-pro-time', '1404/01/01');
$GLOBALS['post'] = get_post($id); setup_postdata($GLOBALS['post']); $GLOBALS['es_project_id'] = $id;
$html = $renderPart('template-parts/project/gallery-info');
foreach (['data-gallery-root="true"', 'data-gallery-main-img="true"', 'data-gallery-thumb', 'data-gallery-lightbox="true"', 'data-gallery-lightbox-close="true"', 'data-project-info', '1404/01/01', 'تهران QA', 'مشاوره پروژه مشابه'] as $needle) $assert(str_contains($html, $needle), 'project gallery/info ' . $needle);
$assert(str_contains($html, 'data-gallery-attachment="204"'), 'historical typo foure-image is read');
wp_delete_post($id, true); wp_reset_postdata(); unset($GLOBALS['es_project_id']); $finishTest();
