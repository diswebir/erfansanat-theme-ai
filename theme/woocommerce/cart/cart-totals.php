<?php
/** Cart totals with shipping, fees, coupons, taxes, and checkout actions. */
/** @version 2.3.6 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cart_totals lg:col-span-5 <?php echo WC()->customer->has_calculated_shipping() ? 'calculated_shipping' : ''; ?> rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
	<?php do_action( 'woocommerce_before_cart_totals' ); ?>
	<h2 class="mb-5 text-lg font-extrabold text-slate-900"><?php esc_html_e( 'جمع سبد خرید', 'erfansanat-theme' ); ?></h2>
	<table cellspacing="0" class="shop_table shop_table_responsive w-full">
		<tr class="cart-subtotal"><th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th><td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td></tr>
		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?><tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>"><th><?php wc_cart_totals_coupon_label( $coupon ); ?></th><td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td></tr><?php endforeach; ?>
		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?><?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?><?php wc_cart_totals_shipping_html(); ?><?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?><?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?><tr class="shipping"><th><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></th><td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>"><?php woocommerce_shipping_calculator(); ?></td></tr><?php endif; ?>
		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?><tr class="fee"><th><?php echo esc_html( $fee->name ); ?></th><td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td></tr><?php endforeach; ?>
		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?><?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?><?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?><tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>"><th><?php echo esc_html( $tax->label ); ?></th><td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td></tr><?php endforeach; ?><?php else : ?><tr class="tax-total"><th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th><td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td></tr><?php endif; ?><?php endif; ?>
		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>
		<tr class="order-total"><th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th><td data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td></tr>
		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
	</table>
	<div class="wc-proceed-to-checkout mt-5"><?php do_action( 'woocommerce_proceed_to_checkout' ); ?></div>
	<?php do_action( 'woocommerce_after_cart_totals' ); ?>
</div>
