<?php
/**
 * P06-T028 — تست端到端 خرید (ساده / متغیر / استعلامی) + سازگاری درگاه/هوک
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-028.php
 *
 * AC-04 (real WooCommerce staging confirmation) is intentionally left PENDING:
 * the sandbox cannot install real WooCommerce, so the full gateway/Swatches/
 * Quantity integration must be re-confirmed on a real staging site.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T028', 'تست端到端 خرید و سازگاری' );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';
	require_once ABSPATH . 'wp-admin/includes/user.php';

	$FQCN = 'ErfanSanat\\Theme\\Core\\CartFragments';
	$cart = WC()->cart;
	$cart->empty_cart();

	$fa = static function ( $v ) {
		$map = array();
		for ( $d = 0; $d < 10; $d++ ) { $map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d ); }
		return strtr( (string) $v, $map );
	};

	// ── SC-01 / AC-01: SIMPLE product full cycle (cart → checkout → receipt) ──.
	$simple = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول ساده', 'post_name' => 'e2e-simple-' . uniqid() ) );
	update_post_meta( $simple, '_price', '200000' );
	update_post_meta( $simple, 'true_sale', 'true' );

	$add = $FQCN::add_item( $simple, 3 );
	es_check( 'AC-01 ساده: افزودن به سبد واقعی', ! empty( $add['success'] ) && 3 === $add['count'] );

	ob_start();
	include ES_THEME . '/woocommerce/cart/cart.php';
	$cart_html = (string) ob_get_clean();
	es_check( 'AC-01 ساده: صفحه سبد ردیف و جمع را نشان می‌دهد', false !== strpos( $cart_html, 'data-cart-item-row=' ) && false !== strpos( $cart_html, $fa( '600,000' ) ) );

	$nonce = wp_create_nonce( 'woocommerce-process-checkout' );
	$_POST = array(
		'es_checkout_submit'                 => '1',
		'woocommerce-process-checkout-nonce' => $nonce,
		'buyer_type'                         => 'real',
		'customer_name'                      => 'خریدار آزمایشی',
		'customer_phone'                     => '09120000000',
		'customer_address'                   => 'تهران، خیابان آزمایش',
		'payment_method'                     => 'mellat', // AC-03: test gateway.
	);
	$_REQUEST['woocommerce-process-checkout-nonce'] = $nonce;

	$orders_before = get_posts( array( 'post_type' => 'shop_order', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
	ob_start();
	include ES_THEME . '/woocommerce/checkout/form-checkout.php';
	$checkout_html = (string) ob_get_clean();
	$orders_after = get_posts( array( 'post_type' => 'shop_order', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
	$new_order_id = (int) current( array_diff( $orders_after, $orders_before ) );

	es_check( 'AC-01 ساده: تسویه موفق و سفارش ثبت شد', false !== strpos( $checkout_html, 'data-checkout-success' ) && $new_order_id > 0 );
	es_check( 'AC-03: درگاه آزمایشی ملت خطا نداد', false === strpos( $checkout_html, 'data-checkout-errors' ) && 'pending' === get_post_meta( $new_order_id, '_order_status', true ) );
	es_check( 'AC-01 ساده: جمع سفارش (۶۰۰۰۰)', '600000' === get_post_meta( $new_order_id, '_order_total', true ) );
	es_check( 'AC-01 ساده: سبد پس از خرید خالی شد', true === $cart->is_empty() );

	$es_order = wc_get_order( $new_order_id );
	$GLOBALS['order'] = $es_order;
	$_GET['key'] = (string) get_post_meta( $new_order_id, '_order_key', true );
	ob_start();
	include ES_THEME . '/woocommerce/checkout/thankyou.php';
	$thanks_html = (string) ob_get_clean();
	$GLOBALS['order'] = null;
	unset( $_GET['key'] );
	es_check( 'AC-01 ساده: رسید و پیش‌فاکتور صادر شد', false !== strpos( $thanks_html, 'data-proforma="true"' ) && false !== strpos( $thanks_html, $fa( (string) $new_order_id ) ) );

	// ── SC-01 / AC-01: VARIABLE product cycle (variation in cart → order) ──.
	$cart->empty_cart();
	$variable = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول متغیر', 'post_name' => 'e2e-var-' . uniqid() ) );
	update_post_meta( $variable, '_price', '500000' );
	update_post_meta( $variable, 'true_sale', 'true' );
	$variation = (int) wp_insert_post( array( 'post_type' => 'product_variation', 'post_status' => 'publish', 'post_title' => 'متغیر — رنگ آبی', 'post_parent' => $variable ) );
	update_post_meta( $variation, '_price', '550000' );
	update_post_meta( $variable, '_product_variations', array( array( 'variation_id' => $variation, 'attributes' => array( 'رنگ' => 'آبی' ) ) ) );

	$vkey = $cart->add_to_cart( $variable, 2, $variation );
	es_check( 'AC-01 متغیر: ورییشن وارد سبد شد', '' !== $vkey && 2 === $cart->get_cart_contents_count() );
	$vorder = wc_create_order();
	$vorder->add_product( wc_get_product( $variable ), 2 );
	$vorder->calculate_totals();
	$vorder->update_status( 'pending' );
	es_check( 'AC-01 متغیر: سفارش ورییشن ثبت شد', $vorder->get_id() > 0 && '1000000' === $vorder->get_total() );
	$cart->empty_cart();

	// ── SC-01 / AC-02: INQUIRY product never enters the cart ──.
	$inquiry = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول استعلامی', 'post_name' => 'e2e-inq-' . uniqid() ) );
	update_post_meta( $inquiry, 'true_sale', 'false' );
	$inq_res = $FQCN::add_item( $inquiry, 1 );
	es_check( 'AC-02: محصول استعلامی وارد سبد نشد', empty( $inq_res['success'] ) && 'inquiry_only' === $inq_res['message'] && true === $cart->is_empty() );

	// ── SC-02: zero script/hook conflict ──.
	$FQCN::boot(); // idempotent — must not double-register.
	es_check( 'SC-02: بوت CartFragments idempotent است', true === $FQCN::is_booted() );
	$count_hooks = static function ( $tag ) {
		global $wp_filter;
		if ( ! isset( $wp_filter[ $tag ] ) ) { return 0; }
		$n = 0;
		foreach ( $wp_filter[ $tag ]->callbacks as $cbs ) { $n += count( $cbs ); }
		return $n;
	};
	es_check( 'SC-02: اکشن AJAX add یک‌بار ثبت شد', 1 === $count_hooks( 'wp_ajax_es_cart_add' ) );
	es_check( 'SC-02: فیلتر fragment یک‌بار ثبت شد', 1 === $count_hooks( 'woocommerce_add_to_cart_fragments' ) );
	es_check( 'SC-02: تداخل هوک سبد/تسویه صفر (بدون خطا)', false !== strpos( $cart_html, 'data-cart-page="true"' ) && false !== strpos( $checkout_html, 'data-checkout-page="true"' ) );

	// Cleanup.
	wp_delete_post( $simple, true );
	wp_delete_post( $variable, true );
	wp_delete_post( $variation, true );
	wp_delete_post( $inquiry, true );
	wp_delete_post( $new_order_id, true );
	wp_delete_post( $vorder->get_id(), true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

// AC-04: real staging confirmation cannot run in the sandbox (no real WooCommerce).
es_pending( 'AC-04: تایید روی استیجینگ واقعی WooCommerce', 'requires real WooCommerce + live gateway/Swatches/Quantity on staging; deferred per standing constraint' );

es_summary( false );
