<?php
/**
 * Product category archive (P06-T018). Uses the shared shop shell with the
 * queried product_cat term context (AC-01). Subcategories resolve without 404
 * (SC-02) and products include child-category items. The term thumbnail and
 * description feed the hero (AC-02); the category FAQ/description section is
 * rendered below the grid (P06-T019). Persian term slugs resolve via the normal
 * WordPress taxonomy query (AC-03).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_term = get_queried_object();
if ( ! $es_term instanceof WP_Term || 'product_cat' !== $es_term->taxonomy ) {
	// Defensive: never fatal on an unexpected query (AC-03 / SC-02).
	get_template_part( 'template-parts/archive/product', 'archive-shell', array(
		'query'     => new WP_Query( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'post__in' => array( 0 ) ) ),
		'show_hero' => false,
	) );
	get_footer();
	return;
}

$es_thumb_id  = (int) get_term_meta( $es_term->term_id, 'thumbnail', true );
$es_hero_img  = $es_thumb_id ? (string) wp_get_attachment_image_url( $es_thumb_id, 'large' ) : '';
if ( ! $es_hero_img ) {
	$es_hero_img = ES_ASSETS_URI . 'images/light-tree.jpg';
}

$es_cat_query = new WP_Query( array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => true,
	'tax_query'      => array(
		array(
			'taxonomy'         => 'product_cat',
			'field'            => 'term_id',
			'terms'            => $es_term->term_id,
			'include_children' => true,
		),
	),
) );

$es_parent = $es_term->parent ? get_term( $es_term->parent, 'product_cat' ) : null;
$es_badge  = ( $es_parent && ! is_wp_error( $es_parent ) )
	? sprintf( 'زیرمجموعه %s · عرفان صنعت', $es_parent->name )
	: __( 'دسته‌بندی محصولات کارخانه عرفان صنعت', 'erfansanat' );

get_template_part( 'template-parts/archive/product', 'archive-shell', array(
	'query'            => $es_cat_query,
	'heading'          => __( 'قیمت و خرید مستقیم', 'erfansanat' ),
	'heading_gradient' => $es_term->name,
	'breadcrumb_label' => $es_term->name,
	'initial_category' => $es_term->name,
	'toolbar_title'    => $es_term->name,
	'hero_subtitle'    => $es_term->description ? $es_term->description : sprintf( 'محصولات %s تولید کارخانه عرفان صنعت اصفهان با گارانتی معتبر و امکان خرید مستقیم.', $es_term->name ),
	'hero_badge'       => $es_badge,
	'hero_image'       => $es_hero_img,
	'per_page'         => (int) apply_filters( 'es_shop_per_page', 9 ),
	'show_hero'        => true,
) );

wp_reset_postdata();

// Category description + FAQ (P06-T019).
get_template_part( 'template-parts/archive/category', 'faq', array( 'term' => $es_term ) );

get_footer();
