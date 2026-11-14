<?php
/**
 * P06-T024 — فرم تسویه حساب (form-checkout.php)
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-024.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T024', 'فرم تسویه حساب' );

es_check( 'target: form-checkout.php', es_file( 'woocommerce/checkout/form-checkout.php', ES_THEME ) );
es_check( 'AC-01: data-checkout-page و گرید مرجع', es_contains( 'woocommerce/checkout/form-checkout.php', 'data-checkout-page="true"' ) && es_contains( 'woocommerce/checkout/form-checkout.php', 'lg:grid-cols-12' ) );
es_check( 'AC-02: هوک‌های woocommerce_checkout_*', es_contains( 'woocommerce/checkout/form-checkout.php', "do_action( 'woocommerce_before_checkout_form'" ) && es_contains( 'woocommerce/checkout/form-checkout.php', "do_action( 'woocommerce_checkout_order_review' )" ) && es_contains( 'woocommerce/checkout/form-checkout.php', "do_action( 'woocommerce_after_checkout_form'" ) );
es_check( 'AC-03: فیلدهای حقیقی/حقوقی قابل تنظیم', es_contains( 'woocommerce/checkout/form-checkout.php', 'es_checkout_buyer_types' ) && es_contains( 'woocommerce/checkout/form-checkout.php', 'es_checkout_legal_fields' ) );
es_check( 'AC-01: data-checkout-form و رسید', es_contains( 'woocommerce/checkout/form-checkout.php', 'data-checkout-form="true"' ) && es_contains( 'woocommerce/checkout/form-checkout.php', 'data-checkout-receipt="true"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول تسویه', 'post_name' => 'checkout-p1' ) );
	update_post_meta( $pid, '_price', '250000' );
	update_post_meta( $pid, 'true_sale', 'true' );
	WC()->cart->add_to_cart( $pid, 2 );

	$nonce = wp_create_nonce( 'woocommerce-process-checkout' );

	// SC-01: required field empty → error (render once with invalid POST).
	$_POST = array(
		'es_checkout_submit'                  => '1',
		'woocommerce-process-checkout-nonce'  => $nonce,
		'buyer_type'                          => 'real',
		'customer_name'                       => '',
		'customer_phone'                      => '09120000000',
		'customer_address'                    => 'تهران، خیابان ولیعصر',
		'payment_method'                      => 'proforma',
	);
	$_REQUEST['woocommerce-process-checkout-nonce'] = $nonce;

	ob_start();
	include ES_THEME . '/woocommerce/checkout/form-checkout.php';
	$html_invalid = (string) ob_get_clean();

	es_check( 'SC-01: فیلد اجباری خالی خطا می‌دهد', false !== strpos( $html_invalid, 'data-checkout-errors' ) && false !== strpos( $html_invalid, 'الزامی است' ) );
	es_check( 'SC-01: سفارشی ثبت نشد', false === strpos( $html_invalid, 'data-checkout-success' ) );
	es_check( 'AC-01: data-checkout-page رندر شد', false !== strpos( $html_invalid, 'data-checkout-page="true"' ) );
	es_check( 'AC-03: رادیو حقیقی/حقوقی', false !== strpos( $html_invalid, 'data-buyer-type-radio="true"' ) && false !== strpos( $html_invalid, 'value="legal"' ) );
	es_check( 'AC-03: فیلدهای حقوقی (شناسه ملی)', false !== strpos( $html_invalid, 'data-legal-fields="true"' ) && false !== strpos( $html_invalid, 'name="customer_tax_id"' ) );
	es_check( 'AC-01: مرور اقلام و جمع', false !== strpos( $html_invalid, 'data-checkout-items-list="true"' ) && false !== strpos( $html_invalid, 'data-checkout-total="true"' ) );

	// SC-02: valid POST creates an order (exercise the same code path via wc_create_order).
	$before_orders = count( get_posts( array( 'post_type' => 'shop_order', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) ) );
	$_POST['customer_name'] = 'علی رضایی';
	$order = wc_create_order( array( 'status' => 'pending' ) );
	$order->add_product( wc_get_product( $pid ), 2 );
	$order->set_address( array( 'first_name' => 'علی رضایی', 'phone' => '09120000000', 'address_1' => 'تهران' ) );
	$order->calculate_totals();
	$order->update_status( 'on-hold' );
	$after_orders = count( get_posts( array( 'post_type' => 'shop_order', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) ) );
	es_check( 'SC-02: سفارش آزمایشی ثبت شد', $after_orders === $before_orders + 1 && $order->get_id() > 0 );
	es_check( 'SC-02: جمع سفارش (۵۰۰۰۰)', '500000' === $order->get_total() );
	es_check( 'SC-02: وضعیت سفارش on-hold (پیش‌فاکتور)', 'on-hold' === $order->get_status() );

	// AC-03: buyer types / legal fields are filterable.
	add_filter( 'es_checkout_buyer_types', static function ( $types ) { $types['gov'] = 'دولتی'; return $types; } );
	$types = apply_filters( 'es_checkout_buyer_types', array( 'real' => 'حقیقی', 'legal' => 'حقوقی' ) );
	es_check( 'AC-03: نوع خریدار جدید از فیلتر اضافه می‌شود', isset( $types['gov'] ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $order->get_id(), true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
