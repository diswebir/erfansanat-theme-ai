<?php
/**
 * Real WordPress blog fixture for P07-T009/T011 JSDOM tests.
 * Usage: php build-blog-fixture.php [post-count=7] [per-page=4]
 * All fixture posts/categories are deleted after buffering the rendered shell.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';
if ( ! es_load_wp() ) { exit( 1 ); }
require_once ES_THEME . '/functions.php';

$count = max( 0, min( 30, (int) ( $argv[1] ?? 7 ) ) );
$per_page = max( 1, min( 12, (int) ( $argv[2] ?? 4 ) ) );
$cat = es_insert_term( 'مجله علمی', 'category', array( 'slug' => 'magazine-dom-' . uniqid() ) )['term_id'];
$news = es_insert_term( 'اخبار و رویداد ها', 'category', array( 'slug' => 'news-dom-' . uniqid() ) )['term_id'];
$ids = array();
for ( $i = 1; $i <= $count; $i++ ) {
	$id = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله آزمایشی ' . $i, 'post_excerpt' => 'راهنمای نورپردازی شهری شماره ' . $i, 'post_name' => 'blog-dom-' . $i . '-' . uniqid(), 'post_date' => '2025-08-' . str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) . ' 10:00:00' ) );
	wp_set_object_terms( $id, array( 1 === $i ? (int) $news : (int) $cat ), 'category' );
	$ids[] = $id;
}
$query = new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'post__in' => $ids ?: array( 0 ), 'orderby' => 'post__in', 'posts_per_page' => -1, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
ob_start();
get_template_part( 'template-parts/archive/blog-shell', null, array( 'query' => $query, 'per_page' => $per_page ) );
$html = ob_get_clean();
foreach ( $ids as $id ) { wp_delete_post( $id, true ); }
wp_delete_term( $cat, 'category' );
wp_delete_term( $news, 'category' );
echo $html;
