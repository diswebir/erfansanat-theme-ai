<?php
/**
 * P06-T027 — برگه احراز هویت (page-templates/auth.php)
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-027.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T027', 'برگه احراز هویت' );

es_check( 'target: auth.php', es_file( 'page-templates/auth.php', ES_THEME ) );
es_check( 'AC-01: هوک‌های ورود/ثبت‌نام افزونه', es_contains( 'page-templates/auth.php', "do_action( 'login_form' )" ) && es_contains( 'page-templates/auth.php', "do_action( 'register_form' )" ) && es_contains( 'page-templates/auth.php', 'woocommerce_login_form' ) );
es_check( 'AC-01: هوک‌های voorodak/fl-panel', es_contains( 'page-templates/auth.php', 'fl_panel_login_fields' ) && es_contains( 'page-templates/auth.php', 'voorodak_login_fields' ) );
es_check( 'AC-02: redirect امن', es_contains( 'page-templates/auth.php', 'wp_validate_redirect' ) && es_contains( 'page-templates/auth.php', 'wp_safe_redirect' ) );
es_check( 'AC-03: پیام خطای OTP', es_contains( 'page-templates/auth.php', 'es_otp_invalid' ) && es_contains( 'page-templates/auth.php', 'نادرست است' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';
	require_once ABSPATH . 'wp-admin/includes/user.php';

	// auth.php defines the helpers on include; load it once for the structure,
	// then exercise the helpers directly.
	ob_start();
	include ES_THEME . '/page-templates/auth.php';
	$html = (string) ob_get_clean();

	es_check( 'AC-01: صفحه auth رندر شد', false !== strpos( $html, 'data-auth-page="true"' ) && false !== strpos( $html, 'data-auth-login="true"' ) );
	es_check( 'AC-01: فرم OTP و ثبت‌نام', false !== strpos( $html, 'data-auth-otp="true"' ) && false !== strpos( $html, 'name="es_otp_code"' ) );

	// SC-02 / AC-02: redirect must stay on the site.
	$home = home_url( '/' );
	es_check( 'SC-02: دامنه خارجی به خانه برمی‌گردد', es_auth_safe_redirect( 'https://evil.example.com/phish' ) === $home );
	es_check( 'SC-02: پروتکل جاوااسکریپت رد می‌شود', es_auth_safe_redirect( 'javascript:alert(1)' ) === $home );
	$internal = home_url( '/my-account/' );
	es_check( 'AC-02: آدرس داخلی حفظ می‌شود', es_auth_safe_redirect( $internal ) === $internal );
	es_check( 'AC-02: مسیر نسبی داخلی حفظ می‌شود', es_auth_safe_redirect( '/account/' ) === home_url( '/account/' ) );
	es_check( 'AC-02: خالی → خانه', es_auth_safe_redirect( '' ) === $home );

	// SC-01: OTP shim login succeeds with a deterministic code.
	$phone = '0912' . wp_rand( 1000000, 9999999 );
	add_filter( 'es_auth_otp_code', static function () { return '12345'; } );
	$sent = es_auth_otp_request( $phone );
	es_check( 'SC-01: ارسال کد آزمایشی', '12345' === $sent );
	$uid = es_auth_otp_verify( $phone, '12345' );
	es_check( 'SC-01: ورود پیامکی shim موفق', is_int( $uid ) && $uid > 0 );
	es_check( 'SC-01: کاربر با شماره ساخته شد', get_user_meta( $uid, 'es_phone', true ) === $phone );

	// AC-03: wrong code → correct error.
	es_auth_otp_request( $phone );
	$bad = es_auth_otp_verify( $phone, '99999' );
	es_check( 'AC-03: کد نادرست پیام درست می‌دهد', is_wp_error( $bad ) && 'es_otp_invalid' === $bad->get_error_code() && false !== strpos( $bad->get_error_message(), 'نادرست' ) );

	// AC-03: expired / missing code.
	$expired = es_auth_otp_verify( '09120000000', '12345' );
	es_check( 'AC-03: کد منقضی پیام درست می‌دهد', is_wp_error( $expired ) && 'es_otp_expired' === $expired->get_error_code() );

	// AC-03: invalid phone.
	$bad_phone = es_auth_otp_request( '123' );
	es_check( 'AC-03: شماره نامعتبر رد می‌شود', is_wp_error( $bad_phone ) && 'es_otp_phone' === $bad_phone->get_error_code() );

	// AC-01: plugin hooks actually fire during render.
	$fired = array();
	add_action( 'voorodak_login_fields', static function () use ( &$fired ) { $fired[] = 'voorodak'; } );
	add_action( 'fl_panel_login_fields', static function () use ( &$fired ) { $fired[] = 'flpanel'; } );
	ob_start();
	include ES_THEME . '/page-templates/auth.php';
	ob_get_clean();
	es_check( 'AC-01: هوک‌های افزونه در رندر اجرا شدند', in_array( 'voorodak', $fired, true ) && in_array( 'flpanel', $fired, true ) );

	wp_delete_user( $uid );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
