<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.11 related projects ===\n";
$id = $makeProduct();
$projectId = wp_insert_post(['post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه مرتبط QA']);
update_post_meta($id, 'project-ids', [$projectId]);
$html = $renderPart('related-projects');
$assert(str_contains($html, 'data-related-projects'), 'related projects wrapper rendered');
$assert(str_contains($html, 'پروژه مرتبط QA') && str_contains($html, 'data-card-project'), 'referenced project uses card-project template');
update_post_meta($id, 'project-ids', []); $assert(trim($renderPart('related-projects')) === '', 'related-project section omitted with no relations');
wp_delete_post($projectId, true); wp_delete_post($id, true); $finishTest();
