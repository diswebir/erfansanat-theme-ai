<?php
/** WooCommerce order-received and failed-payment result. */
/** @version 11.2.0 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-order mx-auto max-w-5xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
	<?php if ( $order ) : do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>
		<?php if ( $order->has_status( 'failed' ) ) : $failed_text = apply_filters( 'woocommerce_thankyou_order_failed_text', esc_html__( 'پرداخت سفارش ناموفق بود. می‌توانید دوباره برای پرداخت تلاش کنید.', 'erfansanat-theme' ), $order ); ?>
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed rounded-xl border border-rose-100 bg-rose-50 p-4 text-sm text-rose-700"><?php echo wp_kses_post( $failed_text ); ?></p><p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions mt-4 flex flex-wrap gap-3"><a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a><?php if ( is_user_logged_in() ) : ?><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a><?php endif; ?></p>
		<?php else : ?>
			<?php wc_get_template( 'checkout/order-received.php', array( 'order' => $order ) ); ?>
			<h1 class="mb-5 text-2xl font-black text-slate-900"><?php esc_html_e( 'سفارش شما ثبت شد', 'erfansanat-theme' ); ?></h1>
			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
				<li class="woocommerce-order-overview__order order rounded-xl bg-slate-50 p-4 text-xs text-slate-600"><?php esc_html_e( 'Order number:', 'woocommerce' ); ?><strong class="mt-1 block text-sm font-black text-slate-900"><?php echo esc_html( $order->get_order_number() ); ?></strong></li>
				<li class="woocommerce-order-overview__date date rounded-xl bg-slate-50 p-4 text-xs text-slate-600"><?php esc_html_e( 'Date:', 'woocommerce' ); ?><strong class="mt-1 block text-sm font-black text-slate-900"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong></li>
				<?php if ( $order->get_billing_email() ) : ?><li class="woocommerce-order-overview__email email rounded-xl bg-slate-50 p-4 text-xs text-slate-600"><?php esc_html_e( 'Email:', 'woocommerce' ); ?><strong class="mt-1 block  text-sm font-black text-slate-900"><?php echo esc_html( $order->get_billing_email() ); ?></strong></li><?php endif; ?>
				<li class="woocommerce-order-overview__total total rounded-xl bg-slate-50 p-4 text-xs text-slate-600"><?php esc_html_e( 'Total:', 'woocommerce' ); ?><strong class="mt-1 block text-sm font-black text-emerald-800"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong></li>
				<?php if ( $order->get_payment_method_title() ) : ?><li class="woocommerce-order-overview__payment-method method rounded-xl bg-slate-50 p-4 text-xs text-slate-600"><?php esc_html_e( 'Payment method:', 'woocommerce' ); ?><strong class="mt-1 block text-sm font-black text-slate-900"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong></li><?php endif; ?>
			</ul>
		<?php endif; ?>
		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
	<?php else : ?><?php wc_get_template( 'checkout/order-received.php', array( 'order' => false ) ); ?><?php endif; ?>
</div>
