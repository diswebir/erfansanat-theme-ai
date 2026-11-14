<?php
/**
 * P06-T026 — حساب کاربری (my-account.php + dashboard.php)
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-026.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T026', 'حساب کاربری' );

es_check( 'target: my-account.php', es_file( 'woocommerce/myaccount/my-account.php', ES_THEME ) );
es_check( 'target: dashboard.php', es_file( 'woocommerce/myaccount/dashboard.php', ES_THEME ) );
es_check( 'AC-01: ناوبری حساب', es_contains( 'woocommerce/myaccount/my-account.php', 'data-myaccount-nav="true"' ) && es_contains( 'woocommerce/myaccount/my-account.php', 'data-account-tab=' ) );
es_check( 'AC-02: هوک‌های حساب Woo', es_contains( 'woocommerce/myaccount/my-account.php', "do_action( 'woocommerce_account_navigation' )" ) && es_contains( 'woocommerce/myaccount/my-account.php', "do_action( 'woocommerce_account_content' )" ) );
es_check( 'AC-02: endpointهای Woo', es_contains( 'woocommerce/myaccount/my-account.php', 'wc_get_account_endpoint_url' ) );
es_check( 'AC-03: بدون تداخل fl-panel', ! es_contains( 'woocommerce/myaccount/my-account.php', 'fl-panel' ) && ! es_contains( 'woocommerce/myaccount/dashboard.php', 'fl-panel' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';
	require_once ABSPATH . 'wp-admin/includes/user.php';
	$es_uniq = uniqid();

	$fa = static function ( $v ) {
		$map = array();
		for ( $d = 0; $d < 10; $d++ ) { $map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d ); }
		return strtr( (string) $v, $map );
	};

	$uid = (int) wp_insert_user( array( 'user_login' => 'es_acct_t026_' . $es_uniq, 'user_pass' => wp_generate_password(), 'role' => 'customer', 'display_name' => 'مریم احمدی' ) );
	$order = wc_create_order();
	update_post_meta( $order->get_id(), '_customer_user', $uid );
	update_post_meta( $order->get_id(), '_order_total', '750000' );
	update_post_meta( $order->get_id(), '_order_status', 'processing' );

	wp_set_current_user( $uid );

	// SC-01: dashboard shows the user's real orders.
	ob_start();
	get_template_part( 'woocommerce/myaccount/dashboard' );
	$dash = (string) ob_get_clean();
	es_check( 'SC-01: پیشخوان خوش‌آمد می‌گوید', false !== strpos( $dash, 'data-myaccount-dashboard="true"' ) && false !== strpos( $dash, 'مریم احمدی' ) );
	es_check( 'SC-01: سفارش کاربر در پیشخوان', false !== strpos( $dash, 'data-myaccount-orders="true"' ) && false !== strpos( $dash, $fa( $order->get_id() ) ) && false !== strpos( $dash, $fa( '750,000' ) ) );

	// SC-02: save default address (single full render, addresses tab).
	$nonce = wp_create_nonce( 'es_save_address' );
	$_GET['tab'] = 'addresses';
	$_POST = array(
		'es_save_address'      => '1',
		'es_address_nonce'     => $nonce,
		'billing_first_name'   => 'مریم',
		'billing_last_name'    => 'احمدی',
		'billing_phone'        => '09121234567',
		'billing_city'         => 'شیراز',
		'billing_address_1'    => 'شیراز، بلوار زند',
		'shipping_address_1'   => 'شیراز، بلوار زند',
	);
	$_REQUEST['es_address_nonce'] = $nonce;

	ob_start();
	include ES_THEME . '/woocommerce/myaccount/my-account.php';
	$html = (string) ob_get_clean();

	es_check( 'AC-01: ناوبری حساب رندر شد', false !== strpos( $html, 'data-myaccount-nav="true"' ) && false !== strpos( $html, 'data-account-tab="orders"' ) && false !== strpos( $html, 'data-account-tab="addresses"' ) );
	es_check( 'AC-01: تب فعال مشخص شد', false !== strpos( $html, 'aria-current="page"' ) );
	es_check( 'SC-02: فرم آدرس نمایش داده شد', false !== strpos( $html, 'data-myaccount-address="true"' ) && false !== strpos( $html, 'name="billing_address_1"' ) );
	es_check( 'SC-02: آدرس پیش‌فرض ذخیره شد', 'شیراز، بلوار زند' === get_user_meta( $uid, 'billing_address_1', true ) );
	es_check( 'SC-02: آدرس ارسال نیز ذخیره شد', 'شیراز، بلوار زند' === get_user_meta( $uid, 'shipping_address_1', true ) );
	es_check( 'SC-02: شهر/تماس ذخیره شد', 'شیراز' === get_user_meta( $uid, 'billing_city', true ) && '09121234567' === get_user_meta( $uid, 'billing_phone', true ) );
	es_check( 'SC-02: پیام ذخیره نمایش داده شد', false !== strpos( $html, 'data-myaccount-notice' ) && false !== strpos( $html, 'ذخیره شد' ) );
	es_check( 'AC-03: خروجی بدون fl-panel', false === strpos( $html, 'fl-panel' ) );

	// Guest sees login prompt (no nav).
	wp_set_current_user( 0 );
	unset( $_POST, $_GET['tab'] );
	ob_start();
	include ES_THEME . '/woocommerce/myaccount/my-account.php';
	$guest_html = (string) ob_get_clean();
	es_check( 'AC-01: مهمان پیام ورود می‌بیند', false !== strpos( $guest_html, 'data-myaccount-guest="true"' ) && false === strpos( $guest_html, 'data-myaccount-nav="true"' ) );

	wp_delete_post( $order->get_id(), true );
	wp_delete_user( $uid );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
