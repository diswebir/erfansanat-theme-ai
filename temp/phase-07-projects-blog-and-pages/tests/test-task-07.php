<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.7 single post header ===\n";
$id = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله تک‌نوشته QA', 'post_content' => 'بدنه مقاله']);
update_post_meta($id, 'number2-viewc', 321);
$GLOBALS['post'] = get_post($id); setup_postdata($GLOBALS['post']);
ob_start(); get_template_part('template-parts/post/header'); $html = (string) ob_get_clean();
foreach (['مقاله تک‌نوشته QA', 'data-post-date=', 'data-post-author=', 'data-post-views="321"', 'aria-label="breadcrumb"'] as $needle) $assert(str_contains($html, $needle), 'single post header ' . $needle);
$assert(is_file($themeRoot . '/single.php'), 'single.php exists');
wp_delete_post($id, true); wp_reset_postdata(); $finishTest();
