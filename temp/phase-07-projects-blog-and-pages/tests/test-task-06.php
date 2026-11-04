<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.6 blog and category archives ===\n";
$postId = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله آرشیو QA', 'post_excerpt' => 'راهنمای نورپردازی QA']);
$category = get_term_by('name', 'دسته وبلاگ QA', 'category'); if (!$category instanceof WP_Term) { $r = wp_insert_term('دسته وبلاگ QA', 'category'); if (!is_wp_error($r)) $category = get_term((int) $r['term_id'], 'category'); }
if ($category instanceof WP_Term) wp_set_post_categories($postId, [(int) $category->term_id]);
$GLOBALS['es_blog_archive_query'] = new WP_Query(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 20]);
ob_start(); get_template_part('template-parts/post/archive-grid'); $html = (string) ob_get_clean();
foreach (['data-blog-archive="true"', 'data-blog-search="true"', 'data-blog-cat-btn="همه"', 'data-blog-grid="true"', 'data-blog-item="true"', 'data-blog-category="دسته وبلاگ QA"', 'data-blog-search-text=', 'data-blog-count="true"', 'data-blog-empty="true"', 'مقاله آرشیو QA'] as $needle) $assert(str_contains($html, $needle), 'blog archive contract ' . $needle);
foreach (['home.php', 'category.php', 'archive.php'] as $file) $assert(is_file($themeRoot . '/' . $file), 'archive template ' . $file . ' exists');
wp_delete_post($postId, true); wp_reset_postdata(); unset($GLOBALS['es_blog_archive_query']); $finishTest();
