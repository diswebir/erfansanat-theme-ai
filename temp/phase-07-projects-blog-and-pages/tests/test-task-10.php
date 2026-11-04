<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.10 mixed search results ===\n";
if (!post_type_exists('product')) register_post_type('product', ['public' => true, 'supports' => ['title', 'editor', 'thumbnail']]);
$projectId = $makeProject('نور QA پروژه جستجو');
$productId = wp_insert_post(['post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'نور QA محصول جستجو']);
$postId = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'نور QA مقاله جستجو']);
$query = new WP_Query(['post_type' => ['product', 'projects', 'post'], 'post_status' => 'publish', 'posts_per_page' => 20, 's' => 'نور QA']);
$GLOBALS['wp_query'] = $query; $GLOBALS['wp_the_query'] = $query;
ob_start(); include $themeRoot . '/search.php'; $html = (string) ob_get_clean();
foreach (['data-search-results="true"', 'data-search-query="نور QA"', 'data-card-product=', 'نور QA محصول جستجو', 'data-card-project=', 'نور QA پروژه جستجو', 'data-card-post=', 'نور QA مقاله جستجو'] as $needle) $assert(str_contains($html, $needle), 'mixed search contract ' . $needle);
$emptyQuery = new WP_Query(['post_type' => ['product', 'projects', 'post'], 'post_status' => 'publish', 'posts_per_page' => 20, 's' => 'نتیجه‌ناپذیر QA 915']);
$GLOBALS['wp_query'] = $emptyQuery; $GLOBALS['wp_the_query'] = $emptyQuery;
ob_start(); include $themeRoot . '/search.php'; $emptyHtml = (string) ob_get_clean();
$assert(str_contains($emptyHtml, 'data-search-empty="true"'), 'empty search state rendered');
foreach ([$productId, $postId, $projectId] as $id) wp_delete_post($id, true);
wp_reset_postdata(); $finishTest();
