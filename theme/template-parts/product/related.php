<?php
/** Related products from shared categories with the current item excluded. */

defined( 'ABSPATH' ) || exit;
$product = \ErfanSanat\Theme\Woo\ProductTemplate::product( $args['product'] ?? null );
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $product );
if ( ! $product || ! $product_id ) { return; }
$ids = array();
if ( function_exists( 'wc_get_related_products' ) ) {
	$ids = wc_get_related_products( $product_id, 16, array( $product_id ), array( 'product_cat', 'product_tag' ) );
}
if ( ! is_array( $ids ) || ! $ids ) {
	$terms = function_exists( 'wp_get_post_terms' ) ? wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) ) : array();
	if ( is_array( $terms ) && $terms && ! is_wp_error( $terms ) && class_exists( 'WP_Query' ) ) {
		$query = new \WP_Query( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 16, 'post__not_in' => array( $product_id ), 'fields' => 'ids', 'no_found_rows' => true, 'tax_query' => array( array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => array_map( 'absint', $terms ) ) ) ) );
		$ids = is_array( $query->posts ?? null ) ? $query->posts : array();
	}
}
$ids = array_values( array_unique( array_filter( array_map( 'absint', $ids ), static function ( $id ) use ( $product_id ) { return $id > 0 && $id !== $product_id; } ) ) );
if ( ! $ids ) { return; }
// Stable per-product order avoids a different row ordering on each request.
usort( $ids, static function ( $left, $right ) use ( $product_id ) { return strcmp( sprintf( '%u', crc32( $product_id . ':' . $left ) ), sprintf( '%u', crc32( $product_id . ':' . $right ) ) ); } );
$ids = array_slice( $ids, 0, 4 );
$title = method_exists( $product, 'get_name' ) ? (string) $product->get_name() : '';
?>
<section class="border-t border-slate-200/80 bg-white py-14" aria-labelledby="es-related-products-title"><div class="mx-auto max-w-6xl px-5 lg:px-8"><div class="mb-8 flex flex-wrap items-end justify-between gap-4"><div><p class="mb-1.5 text-xs font-bold text-emerald-700">سایر تولیدات عرفان صنعت</p><h2 id="es-related-products-title" class="text-2xl font-extrabold text-slate-900">محصولات مشابه و مکمل</h2></div><a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="text-xs font-extrabold text-emerald-700">مشاهده همه محصولات</a></div><div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4"><?php foreach ( $ids as $related_id ) : $related = function_exists( 'wc_get_product' ) ? wc_get_product( $related_id ) : null; if ( ! $related || ! method_exists( $related, 'get_id' ) || $related_id === $product_id ) { continue; } $terms = function_exists( 'get_the_terms' ) ? get_the_terms( $related_id, 'product_cat' ) : array(); $term = is_array( $terms ) && isset( $terms[0] ) ? $terms[0] : null; get_template_part( 'template-parts/cards/card-product', null, array( 'post_id' => $related_id, 'title' => $related->get_name(), 'url' => get_permalink( $related_id ), 'image' => get_the_post_thumbnail_url( $related_id, 'large' ), 'excerpt' => get_the_excerpt( $related_id ), 'category' => $term->name ?? '', 'product_category' => $term->slug ?? '', 'price' => $related->get_price(), 'stock_status' => $related->get_stock_status(), 'badge' => $related->get_sku() ) ); endforeach; ?></div></div></section>
