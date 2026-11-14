<?php
/**
 * Product archive / shop (P06-T016). Overrides WooCommerce's archive-product
 * template with the reference shop layout (hero + filter sidebar + toolbar +
 * grid + client-side pagination). The shop page and the product CPT archive
 * share this template (AC-02). Filtering/sorting/pagination are handled by the
 * immutable main.js, so the full matching product set is rendered server-side.
 * An empty archive renders the reference empty state without fatal (AC-03).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Filter the shop archive query args (integrations/tests may scope the set,
 * e.g. post__in). Defaults to the full published product catalogue.
 */
$es_archive_args = apply_filters(
	'es_shop_archive_query_args',
	array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1, // Client-side pagination needs the full set.
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	)
);

$es_archive_query = new WP_Query( $es_archive_args );

get_template_part(
	'template-parts/archive/product',
	'archive-shell',
	array(
		'query'             => $es_archive_query,
		'heading'           => __( 'فروشگاه محصولات', 'erfansanat' ),
		'heading_gradient'  => __( 'نورپردازی شهری و صنعتی', 'erfansanat' ),
		'breadcrumb_label'  => __( 'فروشگاه محصولات', 'erfansanat' ),
		'initial_category'  => 'همه',
		'toolbar_title'     => __( 'همه محصولات تولیدی عرفان صنعت', 'erfansanat' ),
		'per_page'          => (int) apply_filters( 'es_shop_per_page', 9 ),
		'show_hero'         => true,
	)
);

wp_reset_postdata();

get_footer();
