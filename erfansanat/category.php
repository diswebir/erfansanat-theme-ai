<?php
/**
 * Category archive (P07-T010). Reference: category/magazine.html.
 * Uses the actual queried term, never the mirror's undefined document title.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_term = get_queried_object();
if ( ! $es_term instanceof WP_Term || 'category' !== $es_term->taxonomy ) {
	get_template_part( 'template-parts/archive/blog-shell', null, array(
		'query'   => new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'post__in' => array( 0 ), 'posts_per_page' => -1 ) ),
		'context' => 'category',
		'title'   => __( 'آرشیو دسته‌بندی', 'erfansanat' ),
	) );
	get_footer();
	return;
}

$es_category_args                     = $GLOBALS['wp_query']->query_vars;
$es_category_args['post_type']        = 'post';
$es_category_args['post_status']      = 'publish';
$es_category_args['posts_per_page']   = -1;
$es_category_args['paged']            = 0;
$es_category_args['page']             = 0;
$es_category_args['ignore_sticky_posts'] = true;
$es_category_args['no_found_rows']    = true;
$es_category_query = new WP_Query( apply_filters( 'es_blog_category_query_args', $es_category_args, $es_term ) );

get_template_part( 'template-parts/archive/blog-shell', null, array(
	'query'        => $es_category_query,
	'context'      => 'category',
	'title'        => $es_term->name,
	'description'  => $es_term->description,
	'term'         => $es_term,
	'badge_label'  => sprintf( __( 'آرشیو دسته‌بندی: %s', 'erfansanat' ), $es_term->name ),
	'toolbar_title' => sprintf( __( 'آرشیو مطالب «%s»', 'erfansanat' ), $es_term->name ),
	'count_suffix' => sprintf( __( 'مقاله در دسته «%s»', 'erfansanat' ), $es_term->name ),
) );

wp_reset_postdata();
get_footer();
