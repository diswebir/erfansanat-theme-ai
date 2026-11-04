<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.9 share, related posts and comments ===\n";
$current = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله فعلی QA', 'post_content' => 'محتوا', 'comment_status' => 'open']);
$related = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله مرتبط QA', 'post_content' => 'محتوا']);
$cat = get_term_by('name', 'دسته ارتباط QA', 'category'); if (!$cat instanceof WP_Term) { $r = wp_insert_term('دسته ارتباط QA', 'category'); if (!is_wp_error($r)) $cat = get_term((int) $r['term_id'], 'category'); }
if ($cat instanceof WP_Term) { wp_set_post_categories($current, [(int) $cat->term_id]); wp_set_post_categories($related, [(int) $cat->term_id]); }
$GLOBALS['post'] = get_post($current); setup_postdata($GLOBALS['post']);
ob_start(); get_template_part('template-parts/post/share-related-comments'); $html = (string) ob_get_clean();
foreach (['data-share-btn="true"', 'data-share-label="true"', 'data-related-posts="true"', 'مقاله مرتبط QA', 'data-card-post=', 'data-post-comments="true"'] as $needle) $assert(str_contains($html, $needle), 'post footer contract ' . $needle);
wp_delete_post($related, true);
$emptyCat = wp_insert_term('دسته بدون مطلب QA', 'category'); if (!is_wp_error($emptyCat)) wp_set_post_categories($current, [(int) $emptyCat['term_id']]);
wp_update_post(['ID' => $current, 'comment_status' => 'closed']);
$GLOBALS['post'] = get_post($current); setup_postdata($GLOBALS['post']); ob_start(); get_template_part('template-parts/post/share-related-comments'); $empty = (string) ob_get_clean();
$assert(!str_contains($empty, 'data-related-posts="true"'), 'related section omitted when no related posts are available');
$assert(!str_contains($empty, 'data-post-comments="true"'), 'comments omitted when closed and none exist');
$assert(is_file($themeRoot . '/template-parts/post/share-related-comments.php'), 'post footer component exists');
wp_delete_post($related, true); wp_delete_post($current, true); wp_reset_postdata(); $finishTest();
