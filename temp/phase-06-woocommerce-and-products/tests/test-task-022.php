<?php
/**
 * P06-T022 — صفحه سبد خرید (cart.php)
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-022.php
 * Client-side qty/remove recalc: test-task-022-dom.mjs
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T022', 'صفحه سبد خرید' );

es_check( 'target: cart.php', es_file( 'woocommerce/cart/cart.php', ES_THEME ) );
es_check( 'AC-01: هوک‌های استاندارد سبد', es_contains( 'woocommerce/cart/cart.php', "do_action( 'woocommerce_before_cart' )" ) && es_contains( 'woocommerce/cart/cart.php', "do_action( 'woocommerce_after_cart' )" ) );
es_check( 'AC-02: data-cart-page و ردیف‌ها', es_contains( 'woocommerce/cart/cart.php', 'data-cart-page="true"' ) && es_contains( 'woocommerce/cart/cart.php', 'data-cart-item-row' ) );
es_check( 'AC-03: جمع/مالیات/کوپن از WC()->cart', es_contains( 'woocommerce/cart/cart.php', 'get_cart_contents_total' ) && es_contains( 'woocommerce/cart/cart.php', 'get_cart_contents_taxes' ) && es_contains( 'woocommerce/cart/cart.php', 'apply_coupon' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$fa = static function ( $v ) {
		$map = array();
		for ( $d = 0; $d < 10; $d++ ) {
			$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
		}
		return strtr( (string) $v, $map );
	};
	$money = static function ( $a ) use ( $fa ) { return $fa( number_format( (float) $a, 0, '.', ',' ) ) . ' تومان'; };

	$p1 = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'ریسه بلوطی', 'post_name' => 'cart-p1' ) );
	$p2 = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'پوینت لایت', 'post_name' => 'cart-p2' ) );
	update_post_meta( $p1, '_price', '100000' );
	update_post_meta( $p1, 'true_sale', 'true' );
	update_post_meta( $p1, '_min_qty', '5' );
	update_post_meta( $p1, '_step_qty', '5' );
	update_post_meta( $p2, '_price', '200000' );
	update_post_meta( $p2, 'true_sale', 'true' );

	$cart = WC()->cart;
	$k1   = $cart->add_to_cart( $p1, 5 );
	$k2   = $cart->add_to_cart( $p2, 2 );

	// SC-01: cart math reacts to add / set_quantity / remove.
	es_check( 'SC-01: جمع پس از افزودن (۵×۱۰۰۰۰۰ + ۲×۲۰۰۰۰۰)', 900000.0 === $cart->get_cart_contents_total() );
	$cart->set_quantity( $k1, 10 );
	es_check( 'SC-01: تغییر تعداد جمع را عوض می‌کند (۱۰×۱۰۰۰۰۰ + ۴۰۰۰۰۰)', 1400000.0 === $cart->get_cart_contents_total() );
	$cart->set_quantity( $k1, 5 );
	$cart->remove_cart_item( $k2 );
	es_check( 'SC-01: حذف قلم جمع را کم می‌کند (۵۰۰۰۰)', 500000.0 === $cart->get_cart_contents_total() );
	$cart->add_to_cart( $p2, 2 ); // restore for render.

	// SC-02: invalid coupon via POST (single render).
	$_POST['es_apply_coupon']        = '1';
	$_POST['es_coupon_code']         = 'INVALID99';
	$_REQUEST['es_cart_coupon_nonce'] = wp_create_nonce( 'es_cart_coupon' );
	$_POST['es_cart_coupon_nonce']    = wp_create_nonce( 'es_cart_coupon' );

	ob_start();
	include ES_THEME . '/woocommerce/cart/cart.php';
	$html = (string) ob_get_clean();

	es_check( 'AC-02: data-cart-page رندر شد', false !== strpos( $html, 'data-cart-page="true"' ) );
	es_check( 'AC-02: دو ردیف سبد', 2 === substr_count( $html, 'data-cart-item-row=' ) );
	es_check( 'AC-02: کنترل تعداد/حذف ردیف', false !== strpos( $html, 'data-cart-qty-inc="true"' ) && false !== strpos( $html, 'data-cart-remove="true"' ) );
	es_check( 'AC-03: جمع واقعی (۹۰۰,۰۰۰ تومان)', false !== strpos( $html, 'data-cart-subtotal="true"' ) && false !== strpos( $html, $money( 900000 ) ) );
	es_check( 'AC-03: مالیات ۱۰٪ (۹۰,۰۰۰ تومان)', false !== strpos( $html, 'data-cart-tax="true"' ) && false !== strpos( $html, $money( 90000 ) ) );
	es_check( 'AC-03: جمع کل (۹۹۰,۰۰۰ تومان)', false !== strpos( $html, 'data-cart-grandtotal="true"' ) && false !== strpos( $html, $money( 990000 ) ) );
	es_check( 'AC-03: شمارنده اقلام (۷)', false !== strpos( $html, 'data-cart-count="true"' ) && false !== strpos( $html, $fa( 7 ) ) );
	es_check( 'SC-02: کوپن نامعتبر پیام خطا می‌دهد', false !== strpos( $html, 'data-coupon-error' ) && false !== strpos( $html, 'معتبر نیست' ) );
	es_check( 'AC-02: حالت خالی پنهان (سبد پر)', false !== strpos( $html, 'data-cart-empty="true" class="hidden' ) );
	es_check( 'AC-02: لیست اقلام نمایان', false !== strpos( $html, 'data-cart-items-list="true" class="space-y-4 "' ) || false !== strpos( $html, 'data-cart-items-list="true" class="space-y-4' ) );

	// Valid coupon math (shim mirrors Woo API).
	unset( $_POST['es_apply_coupon'], $_POST['es_coupon_code'] );
	es_check( 'AC-03: کوپن معتبر اعمال می‌شود', true === $cart->apply_coupon( 'WELCOME10' ) );
	es_check( 'AC-03: تخفیف کوپن ۱۰٪ (۹۰۰۰۰)', 90000.0 === $cart->get_discount_total() );
	es_check( 'AC-03: جمع کل با تخفیف (۹۰۰۰۰۰)', 900000.0 === $cart->get_total() );
	es_check( 'AC-03: کوپن نامعتبر رد می‌شود', false === $cart->apply_coupon( 'NOPE' ) );

	// Empty cart renders empty state (shell-level check via fresh cart).
	$cart->empty_cart();
	es_check( 'AC-03: سبد خالی → is_empty', true === $cart->is_empty() );

	wp_delete_post( $p1, true );
	wp_delete_post( $p2, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
