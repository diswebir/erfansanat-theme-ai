<?php
/** Classic WooCommerce checkout form styled with the static site layout. */
/** @version 9.4.0 */

defined( 'ABSPATH' ) || exit;
$checkout = isset( $checkout ) && is_object( $checkout ) ? $checkout : WC()->checkout();
do_action( 'woocommerce_before_checkout_form', $checkout );
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>
<section class="relative overflow-hidden border-b border-emerald-100 bg-slate-50 pb-10 pt-28 lg:pb-12"><div class="mx-auto max-w-[1300px] px-5 lg:px-8"><h1 class="text-3xl font-black text-slate-950 sm:text-4xl">تسویه‌حساب</h1><p class="mt-2 text-sm text-slate-600">اطلاعات سفارش را بررسی و پرداخت خود را تکمیل کنید.</p></div></section>
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">
		<?php if ( $checkout->get_checkout_fields() ) : ?>
			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
			<div class="grid items-start gap-8 lg:grid-cols-3"><div id="customer_details" class="col2-set space-y-6 lg:col-span-7">
				<div class="col-1 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7"><?php do_action( 'woocommerce_checkout_billing' ); ?></div>
				<div class="col-2 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7"><?php do_action( 'woocommerce_checkout_shipping' ); ?></div>
			</div><?php do_action( 'woocommerce_checkout_after_customer_details' ); ?><div class="checkout-order-review lg:col-span-5">
				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?><h2 id="order_review_heading" class="mb-4 text-lg font-extrabold text-slate-900"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h2>
				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?><div id="order_review" class="woocommerce-checkout-review-order rounded-2xl border border-slate-200 bg-white p-5 sm:p-7"><?php do_action( 'woocommerce_checkout_order_review' ); ?></div>
			</div></div>
		<?php else : ?>
			<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?><h2 id="order_review_heading" class="mb-4 text-lg font-extrabold text-slate-900"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h2><?php do_action( 'woocommerce_checkout_before_order_review' ); ?><div id="order_review" class="woocommerce-checkout-review-order rounded-2xl border border-slate-200 bg-white p-5 sm:p-7"><?php do_action( 'woocommerce_checkout_order_review' ); ?></div>
		<?php endif; ?>
		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	</form>
</section>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
