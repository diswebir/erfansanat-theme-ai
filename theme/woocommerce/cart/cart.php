<?php
/** Classic WooCommerce cart table with live cart fragments and core hooks. */
/** @version 11.2.0 */

defined( 'ABSPATH' ) || exit;
do_action( 'woocommerce_before_cart' );
?>
<section class="relative overflow-hidden border-b border-emerald-100 bg-slate-50 pb-10 pt-28 lg:pb-12"><div class="mx-auto max-w-[1300px] px-5 lg:px-8"><h1 class="text-3xl font-black text-slate-950 sm:text-4xl">سبد خرید</h1><p class="mt-2 text-sm text-slate-600">بررسی محصولات و تعداد پیش از ثبت سفارش</p></div></section>
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>
	<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents w-full" cellspacing="0">
		<thead><tr><th scope="col" class="product-remove"><span class="screen-reader-text"><?php esc_html_e( 'Remove item', 'woocommerce' ); ?></span></th><th scope="col" class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e( 'Thumbnail image', 'woocommerce' ); ?></span></th><th scope="col" class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th><th scope="col" class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th><th scope="col" class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th><th scope="col" class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th></tr></thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>
			<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key ); $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key ); $visible = apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ); if ( ! is_object( $_product ) || ( method_exists( $_product, 'exists' ) && ! $_product->exists() ) || empty( $cart_item['quantity'] ) || ! $visible ) { continue; } $cart_item_name = WC()->cart->get_item_product_name( $cart_item, $_product ); $product_name = apply_filters( 'woocommerce_cart_item_name', $cart_item_name, $cart_item, $cart_item_key ); $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', method_exists( $_product, 'is_visible' ) && $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key ); ?>
				<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<td class="product-remove"><?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a role="button" href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>', esc_url( wc_get_cart_remove_url( $cart_item_key ) ), esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ), esc_attr( $product_id ), esc_attr( method_exists( $_product, 'get_sku' ) ? $_product->get_sku() : '' ) ), $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					<td class="product-thumbnail"><?php $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key ); if ( ! $product_permalink ) { echo wp_kses_post( $thumbnail ); } else { printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) ); } ?></td>
					<td role="rowheader" class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>"><?php if ( ! $product_permalink ) { echo wp_kses_post( $product_name . '&nbsp;' ); } else { echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $cart_item_name ) ); } do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key ); echo wc_get_formatted_cart_item_data( $cart_item, false, $cart_item_name ); if ( method_exists( $_product, 'backorders_require_notification' ) && $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) { echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) ); } ?></td>
					<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>"><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>"><?php $min_quantity = method_exists( $_product, 'is_sold_individually' ) && $_product->is_sold_individually() ? 1 : 0; $max_quantity = 1 === $min_quantity ? 1 : $_product->get_max_purchase_quantity(); $product_quantity = woocommerce_quantity_input( array( 'input_name' => "cart[{$cart_item_key}][qty]", 'input_value' => $cart_item['quantity'], 'max_value' => $max_quantity, 'min_value' => $min_quantity, 'product_name' => $product_name ), $_product, false ); echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>
			<?php endforeach; ?>
			<?php do_action( 'woocommerce_cart_contents' ); ?>
			<tr><td colspan="6" class="actions"><div class="flex flex-wrap items-center justify-between gap-3">
				<?php if ( wc_coupons_enabled() ) : ?><div class="coupon flex flex-wrap items-center gap-2"><label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label><input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>"><button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button><?php do_action( 'woocommerce_cart_coupon' ); ?></div><?php endif; ?>
				<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button><?php do_action( 'woocommerce_cart_actions' ); ?>
			</div><?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?></td></tr>
			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	</div>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>
<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
<div class="cart-collaterals mt-8 grid gap-8 lg:grid-cols-3"><?php do_action( 'woocommerce_cart_collaterals' ); ?></div>
<?php do_action( 'woocommerce_after_cart' ); ?>
</section>
