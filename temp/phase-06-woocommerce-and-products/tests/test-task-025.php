<?php
/**
 * P06-T025 — رسید سفارش و پیش‌فاکتور چاپی (thankyou.php + proforma.php)
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-025.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T025', 'رسید سفارش و پیش‌فاکتور چاپی' );

es_check( 'target: thankyou.php', es_file( 'woocommerce/checkout/thankyou.php', ES_THEME ) );
es_check( 'target: proforma.php', es_file( 'template-parts/checkout/proforma.php', ES_THEME ) );
es_check( 'AC-02: نسخه چاپی RTL', es_contains( 'template-parts/checkout/proforma.php', 'dir="rtl"' ) && es_contains( 'template-parts/checkout/proforma.php', '@media print' ) );
es_check( 'AC-03: گارد دسترسی صاحب سفارش/مجاز', es_contains( 'template-parts/checkout/proforma.php', 'get_customer_id' ) && es_contains( 'template-parts/checkout/proforma.php', 'data-proforma-denied' ) );
es_check( 'AC-01: اطلاعات واقعی سفارش', es_contains( 'template-parts/checkout/proforma.php', 'get_order_number' ) && es_contains( 'template-parts/checkout/proforma.php', 'get_items' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$fa = static function ( $v ) {
		$map = array();
		for ( $d = 0; $d < 10; $d++ ) { $map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d ); }
		return strtr( (string) $v, $map );
	};

	$owner = (int) wp_insert_user( array( 'user_login' => 'es_owner_t025', 'user_pass' => wp_generate_password(), 'role' => 'customer' ) );
	$other = (int) wp_insert_user( array( 'user_login' => 'es_other_t025', 'user_pass' => wp_generate_password(), 'role' => 'customer' ) );
	$admin = (int) wp_insert_user( array( 'user_login' => 'es_admin_t025', 'user_pass' => wp_generate_password(), 'role' => 'administrator' ) );

	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'ریسه صنعتی', 'post_name' => 'thankyou-p1' ) );
	update_post_meta( $pid, '_price', '300000' );
	update_post_meta( $pid, 'true_sale', 'true' );

	$order = wc_create_order();
	$order->add_product( wc_get_product( $pid ), 2 );
	$order->set_address( array( 'first_name' => 'سارا محمدی', 'phone' => '09131234567', 'address_1' => 'اصفهان، خیابان چهارباغ', 'city' => 'اصفهان' ) );
	$order->calculate_totals();
	update_post_meta( $order->get_id(), '_customer_user', $owner );
	update_post_meta( $order->get_id(), '_es_payment_method', 'proforma' );
	update_post_meta( $order->get_id(), '_order_key', 'wc_order_KEY123' );

	$render_proforma = static function ( $order ) {
		ob_start();
		get_template_part( 'template-parts/checkout/proforma', null, array( 'order' => $order ) );
		return (string) ob_get_clean();
	};

	// SC-01: owner sees the proforma.
	wp_set_current_user( $owner );
	$html_owner = $render_proforma( $order );
	es_check( 'SC-01: صاحب سفارش پیش‌فاکتور را می‌بیند', false !== strpos( $html_owner, 'data-proforma="true"' ) && false === strpos( $html_owner, 'data-proforma-denied' ) );
	es_check( 'AC-01: شماره سفارش چاپ شد', false !== strpos( $html_owner, $fa( $order->get_order_number() ) ) );
	es_check( 'AC-01: نام خریدار و آیتم چاپ شد', false !== strpos( $html_owner, 'سارا محمدی' ) && false !== strpos( $html_owner, 'ریسه صنعتی' ) );
	es_check( 'AC-01: جمع/مالیات/مبلغ کل', false !== strpos( $html_owner, $fa( '600,000' ) ) && false !== strpos( $html_owner, $fa( '60,000' ) ) && false !== strpos( $html_owner, $fa( '660,000' ) ) );
	es_check( 'AC-02: دکمه چاپ', false !== strpos( $html_owner, 'window.print()' ) );

	// SC-02: another user is denied.
	wp_set_current_user( $other );
	$html_other = $render_proforma( $order );
	es_check( 'SC-02: کاربر دیگر دسترسی ندارد', false !== strpos( $html_other, 'data-proforma-denied="true"' ) && false === strpos( $html_other, 'data-proforma="true"' ) );
	es_check( 'SC-02: اطلاعات سفارش برای غیرمجاز چاپ نشد', false === strpos( $html_other, 'سارا محمدی' ) );

	// AC-03: admin is allowed.
	wp_set_current_user( $admin );
	$html_admin = $render_proforma( $order );
	es_check( 'AC-03: مدیر دسترسی دارد', false !== strpos( $html_admin, 'data-proforma="true"' ) );

	// AC-03: guest with valid order key is allowed.
	wp_set_current_user( 0 );
	$_GET['key'] = 'wc_order_KEY123';
	$html_key = $render_proforma( $order );
	es_check( 'AC-03: کلید معتبر سفارش دسترسی می‌دهد', false !== strpos( $html_key, 'data-proforma="true"' ) );
	$_GET['key'] = 'wrong_key';
	$html_badkey = $render_proforma( $order );
	es_check( 'AC-03: کلید نامعتبر رد می‌شود', false !== strpos( $html_badkey, 'data-proforma-denied' ) );
	unset( $_GET['key'] );

	// thankyou.php renders the order summary + embeds proforma (owner).
	wp_set_current_user( $owner );
	$GLOBALS['order'] = $order;
	ob_start();
	include ES_THEME . '/woocommerce/checkout/thankyou.php';
	$html_thanks = (string) ob_get_clean();
	es_check( 'AC-01: صفحه رسید، خلاصه سفارش را نشان می‌دهد', false !== strpos( $html_thanks, 'data-order-summary="true"' ) && false !== strpos( $html_thanks, 'data-thankyou-page="true"' ) );
	es_check( 'AC-01: پیش‌فاکتور در رسید جاسازی شد', false !== strpos( $html_thanks, 'data-proforma="true"' ) );
	unset( $GLOBALS['order'] );

	wp_set_current_user( 0 );
	wp_delete_post( $pid, true );
	wp_delete_post( $order->get_id(), true );
	wp_delete_user( $owner );
	wp_delete_user( $other );
	wp_delete_user( $admin );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
