<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.4 projects archive ===\n";
$id = $makeProject('پروژه آرشیو QA');
$query = new WP_Query(['post_type' => 'projects', 'post_status' => 'publish', 'posts_per_page' => 20]);
$GLOBALS['wp_query'] = $query; $GLOBALS['es_project_archive_query'] = $query;
$html = ''; ob_start(); get_template_part('template-parts/project/archive-grid'); $html = (string) ob_get_clean();
foreach (['data-projects-archive="true"', 'data-project-search="true"', 'data-project-sort="true"', 'data-project-grid="true"', 'data-project-item="true"', 'data-project-type="پل شهری QA"', 'data-project-location="تهران QA"', 'data-pagination-root="true"', 'data-project-empty="true"', 'پروژه آرشیو QA'] as $needle) $assert(str_contains($html, $needle), 'archive contract ' . $needle);
$assert(is_file($themeRoot . '/archive-projects.php'), 'project archive template exists');
wp_delete_post($id, true); wp_reset_postdata(); unset($GLOBALS['es_project_archive_query']); $finishTest();
