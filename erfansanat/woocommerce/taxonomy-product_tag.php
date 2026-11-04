<?php
/**
 * Product tag archive (P06-T020). Reuses the shared shop shell (AC-03: same
 * standard cards) with the queried product_tag term context. The existing tag
 * slug is preserved (AC-01) and the term name/description feed the hero (AC-02).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_term = get_queried_object();
if ( ! $es_term instanceof WP_Term || 'product_tag' !== $es_term->taxonomy ) {
	get_template_part( 'template-parts/archive/product', 'archive-shell', array(
		'query'     => new WP_Query( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'post__in' => array( 0 ) ) ),
		'show_hero' => false,
	) );
	get_footer();
	return;
}

$es_tag_query = new WP_Query( array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => true,
	'tax_query'      => array(
		array(
			'taxonomy' => 'product_tag',
			'field'    => 'term_id',
			'terms'    => $es_term->term_id,
		),
	),
) );

get_template_part( 'template-parts/archive/product', 'archive-shell', array(
	'query'            => $es_tag_query,
	'heading'          => __( 'محصولات برچسب', 'erfansanat' ),
	'heading_gradient' => $es_term->name,
	'breadcrumb_label' => $es_term->name,
	'initial_category' => 'همه',
	'toolbar_title'    => sprintf( 'محصولات با برچسب %s', $es_term->name ),
	'hero_subtitle'    => $es_term->description ? $es_term->description : sprintf( 'همه محصولات دارای برچسب %s در فروشگاه عرفان صنعت.', $es_term->name ),
	'hero_badge'       => __( 'برچسب محصولات کارخانه عرفان صنعت', 'erfansanat' ),
	'per_page'         => (int) apply_filters( 'es_shop_per_page', 9 ),
	'show_hero'        => true,
) );

wp_reset_postdata();

get_footer();
