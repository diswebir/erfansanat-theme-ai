<?php
/**
 * Related / complementary products (P06-T015). Uses WooCommerce's related
 * products (category-based) when available, otherwise an optimized category
 * query. The current product is always excluded (AC-02) and the card count is
 * filterable via `es_related_products_count` (AC-03). Renders the standard
 * product card. No related products → no section.
 *
 * @package ErfanSanat
 * @var WC_Product|ES_Shim_Product $product
 */

defined( 'ABSPATH' ) || exit;

$es_product = isset( $product ) && is_object( $product ) ? $product : null;
if ( ! $es_product && isset( $args['product'] ) && is_object( $args['product'] ) ) {
	$es_product = $args['product'];
}
if ( ! $es_product || ! method_exists( $es_product, 'get_id' ) ) {
	return;
}
$es_id    = (int) $es_product->get_id();
$es_count = (int) apply_filters( 'es_related_products_count', 4, $es_id );
if ( $es_count < 1 ) {
	$es_count = 4;
}

$es_related = array();
if ( defined( 'WC_VERSION' ) && function_exists( 'wc_get_related_products' ) ) {
	// AC-01: WooCommerce's own related-products logic (categories + settings).
	$es_related = array_map( 'intval', (array) wc_get_related_products( $es_id, $es_count ) );
} else {
	// Optimized fallback: same-category products, current excluded, IDs only.
	$es_cat_ids = array();
	$es_terms   = get_the_terms( $es_id, 'product_cat' );
	if ( $es_terms && ! is_wp_error( $es_terms ) ) {
		$es_cat_ids = wp_list_pluck( $es_terms, 'term_id' );
	}

	$es_q_args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $es_count,
		'post__not_in'   => array( $es_id ), // AC-02.
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);
	if ( $es_cat_ids ) {
		$es_q_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $es_cat_ids,
			),
		);
	}
	$es_q       = new WP_Query( $es_q_args );
	$es_related = array_map( 'intval', $es_q->posts );
}

// AC-02: never render the current product; keep only valid published products.
$es_related = array_values( array_filter(
	$es_related,
	static function ( $pid ) use ( $es_id ) {
		return $pid && $pid !== $es_id && 'product' === get_post_type( $pid ) && 'publish' === get_post_status( $pid );
	}
) );

if ( ! $es_related ) {
	return;
}

$es_shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
if ( ! $es_shop ) {
	$es_shop = get_post_type_archive_link( 'product' );
}
if ( ! $es_shop ) {
	$es_shop = home_url( '/shop/' );
}
?>
<section class="border-t border-slate-200/80 bg-white py-14" data-product-related-products>
	<div class="mx-auto max-w-6xl px-5 lg:px-8">
		<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
			<div>
				<div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700">
					<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
					<span><?php esc_html_e( 'سایر تولیدات عرفان صنعت', 'erfansanat' ); ?></span>
				</div>
				<h2 class="text-2xl font-extrabold text-slate-900"><?php esc_html_e( 'محصولات مشابه و مکمل', 'erfansanat' ); ?></h2>
			</div>
			<a href="<?php echo esc_url( $es_shop ); ?>" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900">
				<span><?php esc_html_e( 'مشاهده همه محصولات', 'erfansanat' ); ?></span>
				<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
			</a>
		</div>
		<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
			<?php foreach ( $es_related as $es_related_id ) : ?>
				<?php get_template_part( 'template-parts/cards/card', 'product', array( 'product_id' => $es_related_id ) ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
