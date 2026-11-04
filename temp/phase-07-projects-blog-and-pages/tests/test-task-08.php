<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.8 automatic post TOC ===\n";
$content = '<h2>مقدمه فارسی</h2><p>متن</p><h2>مقدمه فارسی</h2><h3 id="custom-anchor">English heading</h3>';
$id = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله فهرست QA', 'post_content' => $content]);
$GLOBALS['post'] = get_post($id); setup_postdata($GLOBALS['post']);
ob_start(); get_template_part('template-parts/post/toc'); $html = (string) ob_get_clean();
foreach (['data-post-toc="true"', 'href="#مقدمه-فارسی"', 'href="#مقدمه-فارسی-2"', 'href="#custom-anchor"', 'مقدمه فارسی', 'English heading'] as $needle) $assert(str_contains($html, $needle), 'TOC heading contract ' . $needle);
$renderedContent = apply_filters('the_content', $content);
$assert(substr_count($renderedContent, 'id="مقدمه-فارسی') >= 2, 'heading IDs applied to article content');
$GLOBALS['post'] = get_post($id); wp_update_post(['ID' => $id, 'post_content' => '<p>No headings</p>']);
ob_start(); get_template_part('template-parts/post/toc'); $empty = (string) ob_get_clean(); $assert(!str_contains($empty, 'data-post-toc="true"'), 'TOC omitted without headings');
wp_delete_post($id, true); wp_reset_postdata(); unset($GLOBALS['es_post_toc_headings'], $GLOBALS['es_post_toc_post_id']); $finishTest();
