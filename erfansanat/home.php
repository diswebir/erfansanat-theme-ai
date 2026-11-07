<?php
/**
 * Main blog posts page (P07-T009). Reference: static-mirror/blog.html.
 * The immutable main.js filters and paginates the complete published collection
 * in the browser, so this query must not silently truncate it to one page.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_blog_args = apply_filters(
	'es_blog_archive_query_args',
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => -1,
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'no_found_rows'       => true,
	)
);
$es_blog_query = new WP_Query( $es_blog_args );

get_template_part( 'template-parts/archive/blog-shell', null, array( 'query' => $es_blog_query ) );

wp_reset_postdata();
get_footer();
