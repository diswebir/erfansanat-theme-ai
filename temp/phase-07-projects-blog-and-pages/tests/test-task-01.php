<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.1 project breadcrumb/header ===\n";
$id = $makeProject(); $GLOBALS['post'] = get_post($id); setup_postdata($GLOBALS['post']); $GLOBALS['es_project_id'] = $id;
$html = $renderPart('template-parts/project/header');
foreach (['پروژه آزمون', 'پل شهری QA', 'تهران QA', 'خلاصه پروژه آزمون', 'aria-label="breadcrumb"'] as $needle) $assert(str_contains($html, $needle), 'project header contract ' . $needle);
$assert(is_file($themeRoot . '/single-projects.php'), 'single project template exists');
wp_delete_post($id, true); wp_reset_postdata(); unset($GLOBALS['es_project_id']); $finishTest();
