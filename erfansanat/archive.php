<?php
/**
 * General post archive (P07-T010): date, author, tags and safe fallbacks.
 * Dedicated CPT archives keep their own templates; this fallback never labels
 * an archive "undefined" and never exposes non-published content.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_object = get_queried_object();
if ( is_post_type_archive() && $es_object instanceof WP_Post_Type && 'post' !== $es_object->name ) {
	require locate_template( 'index.php' );
	return;
}

get_header();

$es_title       = __( 'آرشیو مقالات', 'erfansanat' );
$es_description = '';
$es_badge       = __( 'آرشیو مطالب عرفان صنعت', 'erfansanat' );
$es_term        = null;
if ( $es_object instanceof WP_Term ) {
	$es_title       = $es_object->name;
	$es_description = $es_object->description;
	$es_badge       = sprintf( __( 'آرشیو %s', 'erfansanat' ), $es_title );
	$es_term        = $es_object;
} elseif ( $es_object instanceof WP_User ) {
	$es_title       = $es_object->display_name;
	$es_description = (string) get_user_meta( $es_object->ID, 'description', true );
	$es_badge       = __( 'نویسنده مقالات عرفان صنعت', 'erfansanat' );
} elseif ( is_date() ) {
	$es_title = __( 'آرشیو زمانی مقالات', 'erfansanat' );
	$es_badge = __( 'مقالات منتشرشده در بازه زمانی انتخاب‌شده', 'erfansanat' );
}

$es_archive_args                     = $GLOBALS['wp_query']->query_vars;
$es_archive_args['post_status']      = 'publish';
$es_archive_args['posts_per_page']   = -1;
$es_archive_args['paged']            = 0;
$es_archive_args['page']             = 0;
$es_archive_args['ignore_sticky_posts'] = true;
$es_archive_args['no_found_rows']    = true;
$es_archive_query = new WP_Query( apply_filters( 'es_blog_archive_query_args', $es_archive_args ) );

get_template_part( 'template-parts/archive/blog-shell', null, array(
	'query'         => $es_archive_query,
	'context'       => 'archive',
	'title'         => $es_title,
	'description'   => $es_description,
	'term'          => $es_term,
	'badge_label'   => $es_badge,
	'toolbar_title' => $es_title,
	'count_suffix'  => __( 'مقاله در این آرشیو', 'erfansanat' ),
) );

wp_reset_postdata();
get_footer();
