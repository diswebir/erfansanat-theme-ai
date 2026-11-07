<?php
/** Shared product card adapter for WooCommerce loops. */
/** @version 9.4.0 */

defined( 'ABSPATH' ) || exit;

$product = isset( $product ) && is_object( $product ) ? $product : null;
if ( ! $product || ! method_exists( $product, 'get_id' ) ) { return; }
if ( method_exists( $product, 'is_visible' ) && ! $product->is_visible() ) { return; }
$product_id = absint( $product->get_id() );
if ( $product_id < 1 ) { return; }
$terms = function_exists( 'get_the_terms' ) ? get_the_terms( $product_id, 'product_cat' ) : array();
$terms = is_array( $terms ) && ( ! function_exists( 'is_wp_error' ) || ! is_wp_error( $terms ) ) ? $terms : array();
$term = $terms[0] ?? null;
$price = method_exists( $product, 'get_price' ) ? $product->get_price() : '';
$stock = method_exists( $product, 'get_stock_status' ) ? $product->get_stock_status() : '';
$sku = method_exists( $product, 'get_sku' ) ? $product->get_sku() : '';
$title = method_exists( $product, 'get_name' ) ? $product->get_name() : ( function_exists( 'get_the_title' ) ? get_the_title( $product_id ) : '' );
$excerpt = function_exists( 'get_the_excerpt' ) ? get_the_excerpt( $product_id ) : '';
$sale_mode = function_exists( 'get_post_meta' ) ? strtolower( trim( (string) get_post_meta( $product_id, 'true_sale', true ) ) ) : '';
$purchasable = in_array( $sale_mode, array( 'false', '0', 'no' ), true ) ? false : ( method_exists( $product, 'is_purchasable' ) ? (bool) $product->is_purchasable() : true );
$price_value = $purchasable && is_scalar( $price ) ? preg_replace( '/[^0-9.]/', '', (string) $price ) : '0';
$category_names = array();
$category_slugs = array();
foreach ( $terms as $product_term ) {
	if ( isset( $product_term->name ) ) { $category_names[] = (string) $product_term->name; }
	if ( isset( $product_term->slug ) ) { $category_slugs[] = (string) $product_term->slug; }
}
$category = $term && isset( $term->name ) ? (string) $term->name : '';
$category_slug = $term && isset( $term->slug ) ? (string) $term->slug : '';
$ip = method_exists( $product, 'get_attribute' ) ? (string) $product->get_attribute( 'pa_ip-rating' ) : '';
$voltage = method_exists( $product, 'get_attribute' ) ? (string) $product->get_attribute( 'pa_input-voltage' ) : '';
$views = function_exists( 'get_post_meta' ) ? get_post_meta( $product_id, 'product_views', true ) : '';
$rating = method_exists( $product, 'get_average_rating' ) ? (string) $product->get_average_rating() : '';
$search = trim( implode( ' ', array_filter( array( $title, $excerpt, $sku, $category, $ip, $voltage ) ) ) );
$badge = method_exists( $product, 'is_on_sale' ) && $product->is_on_sale() ? ( function_exists( '__' ) ? __( 'Sale', 'woocommerce' ) : 'Sale' ) : ( is_scalar( $sku ) ? (string) $sku : '' );
$args = array(
	'post_id' => $product_id,
	'title' => $title,
	'url' => function_exists( 'get_permalink' ) ? get_permalink( $product_id ) : '',
	'image' => function_exists( 'get_the_post_thumbnail_url' ) ? get_the_post_thumbnail_url( $product_id, 'large' ) : '',
	'excerpt' => $excerpt,
	'category' => $category,
	'product_category' => $category_slug,
	'price' => is_scalar( $price ) ? (string) $price : '',
	'stock_status' => is_scalar( $stock ) ? (string) $stock : '',
	'badge' => $badge,
	'_omit_product_data_attributes' => true,
);
$attributes = array(
	'data-product-item' => 'true',
	'data-product-id' => (string) $product_id,
	'data-product-category' => $category,
	'data-product-categories' => implode( '|', $category_names ),
	'data-product-purchasable' => $purchasable ? 'true' : 'false',
	'data-product-price' => is_scalar( $price_value ) ? (string) $price_value : '0',
	'data-product-stock' => is_scalar( $stock ) ? (string) $stock : '',
	'data-product-ip' => $ip,
	'data-product-voltage' => $voltage,
	'data-product-views' => is_scalar( $views ) ? (string) $views : '0',
	'data-product-rating' => $rating,
	'data-product-search-text' => $search,
);
$woo_hooks = array(
	'woocommerce_before_shop_loop_item' => array( 'woocommerce_template_loop_product_link_open' ),
	'woocommerce_before_shop_loop_item_title' => array( 'woocommerce_show_product_loop_sale_flash', 'woocommerce_template_loop_product_thumbnail' ),
	'woocommerce_shop_loop_item_title' => array( 'woocommerce_template_loop_product_title' ),
	'woocommerce_after_shop_loop_item_title' => array( 'woocommerce_template_loop_rating', 'woocommerce_template_loop_price' ),
	'woocommerce_after_shop_loop_item' => array( 'woocommerce_template_loop_product_link_close', 'woocommerce_template_loop_add_to_cart' ),
);
$removed_callbacks = array();
if ( function_exists( 'has_action' ) && function_exists( 'remove_action' ) && function_exists( 'add_action' ) ) {
	foreach ( $woo_hooks as $hook => $callbacks ) {
		foreach ( $callbacks as $callback ) {
			$priority = has_action( $hook, $callback );
			if ( false !== $priority && remove_action( $hook, $callback, $priority ) ) { $removed_callbacks[] = array( $hook, $callback, $priority ); }
		}
	}
}
?>
<li <?php if ( function_exists( 'wc_product_class' ) ) { wc_product_class( '', $product ); } else { echo 'class="product"'; } ?><?php foreach ( $attributes as $name => $value ) : ?> <?php echo esc_attr( $name ); ?>="<?php echo esc_attr( $value ); ?>"<?php endforeach; ?>>
	<?php
	try {
		if ( function_exists( 'do_action' ) ) { do_action( 'woocommerce_before_shop_loop_item' ); }
		get_template_part( 'template-parts/cards/card-product', null, $args );
		if ( function_exists( 'do_action' ) ) {
			do_action( 'es_after_shared_product_card', $product, $args );
			do_action( 'woocommerce_after_shop_loop_item' );
		}
	} finally {
		foreach ( $removed_callbacks as $removed ) { add_action( $removed[0], $removed[1], $removed[2] ); }
	}
	?>
</li>
