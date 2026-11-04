<?php
/**
 * WooCommerce loop card adapter.
 *
 * Used by product archives and WooCommerce related-product loops; keeps the
 * shared, reference-matched product card as the single presentation source.
 *
 * @package ErfanSanat\Theme
 */
defined( 'ABSPATH' ) || exit;

$product = isset( $product ) && is_object( $product ) ? $product : null;
if ( ! $product || ! method_exists( $product, 'get_id' ) ) { return; }
if ( method_exists( $product, 'is_visible' ) && ! $product->is_visible() ) { return; }

$product_id = absint( $product->get_id() );
if ( $product_id < 1 ) { return; }
$terms = function_exists( 'get_the_terms' ) ? get_the_terms( $product_id, 'product_cat' ) : array();
$term = is_array( $terms ) && isset( $terms[0] ) && is_object( $terms[0] ) ? $terms[0] : null;
$price = method_exists( $product, 'get_price' ) ? $product->get_price() : '';
$stock = method_exists( $product, 'get_stock_status' ) ? $product->get_stock_status() : '';
$sku = method_exists( $product, 'get_sku' ) ? $product->get_sku() : '';
$title = method_exists( $product, 'get_name' ) ? $product->get_name() : ( function_exists( 'get_the_title' ) ? get_the_title( $product_id ) : '' );
$badge = method_exists( $product, 'is_on_sale' ) && $product->is_on_sale()
	? ( function_exists( '__' ) ? __( 'Sale', 'woocommerce' ) : 'Sale' )
	: ( is_scalar( $sku ) ? (string) $sku : '' );
$args = array(
	'post_id'          => $product_id,
	'title'            => $title,
	'url'              => function_exists( 'get_permalink' ) ? get_permalink( $product_id ) : '',
	'image'            => function_exists( 'get_the_post_thumbnail_url' ) ? get_the_post_thumbnail_url( $product_id, 'large' ) : '',
	'excerpt'          => function_exists( 'get_the_excerpt' ) ? get_the_excerpt( $product_id ) : '',
	'category'         => $term && isset( $term->name ) ? $term->name : '',
	'product_category' => $term && isset( $term->slug ) ? $term->slug : '',
	'price'            => is_scalar( $price ) ? (string) $price : '',
	'stock_status'     => is_scalar( $stock ) ? (string) $stock : '',
	'badge'            => $badge,
);
$woo_hooks = array(
	'woocommerce_before_shop_loop_item'       => array( 'woocommerce_template_loop_product_link_open' ),
	'woocommerce_before_shop_loop_item_title'  => array( 'woocommerce_show_product_loop_sale_flash', 'woocommerce_template_loop_product_thumbnail' ),
	'woocommerce_shop_loop_item_title'         => array( 'woocommerce_template_loop_product_title' ),
	'woocommerce_after_shop_loop_item_title'   => array( 'woocommerce_template_loop_rating', 'woocommerce_template_loop_price' ),
	'woocommerce_after_shop_loop_item'         => array( 'woocommerce_template_loop_product_link_close', 'woocommerce_template_loop_add_to_cart' ),
);
$removed_callbacks = array();
if ( function_exists( 'has_action' ) && function_exists( 'remove_action' ) && function_exists( 'add_action' ) ) {
	foreach ( $woo_hooks as $hook => $callbacks ) {
		foreach ( $callbacks as $callback ) {
			$priority = has_action( $hook, $callback );
			if ( false !== $priority && remove_action( $hook, $callback, $priority ) ) {
				$removed_callbacks[] = array( $hook, $callback, $priority );
			}
		}
	}
}
?>
<li <?php if ( function_exists( 'wc_product_class' ) ) { wc_product_class( '', $product ); } else { echo 'class="product"'; } ?>>
	<?php
	try {
		if ( function_exists( 'do_action' ) ) {
			foreach ( array_keys( $woo_hooks ) as $hook ) { do_action( $hook ); }
			do_action( 'es_before_shared_product_card', $product, $args );
		}
		get_template_part( 'template-parts/cards/card-product', null, $args );
		if ( function_exists( 'do_action' ) ) {
			do_action( 'es_after_shared_product_card', $product, $args );
		}
	} finally {
		foreach ( $removed_callbacks as $removed ) {
			add_action( $removed[0], $removed[1], $removed[2] );
		}
	}
	?>
</li>
