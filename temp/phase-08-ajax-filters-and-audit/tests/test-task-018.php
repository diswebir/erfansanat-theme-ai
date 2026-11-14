<?php
/**
 * P08-T018 — Mailer + SpamGuard: shared honeypot/nonce/rate-limit, configurable
 * wp_mail recipient, entry survives email failure, invalid email rejected.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Forms\ContactForm;
use ErfanSanat\Theme\Forms\Mailer;
use ErfanSanat\Theme\Forms\Schema;
use ErfanSanat\Theme\Forms\SpamGuard;

ob_start();

es_task( 'P08-T018', 'ایمیل، Honeypot و Rate-limit' );

es_check( 'target: inc/Forms/Mailer.php', es_file( 'inc/Forms/Mailer.php' ) );
es_check( 'target: inc/Forms/SpamGuard.php', es_file( 'inc/Forms/SpamGuard.php' ) );
es_check( 'AC-01: Mailer با wp_mail می‌فرستد', es_contains( 'inc/Forms/Mailer.php', 'wp_mail(' ) );
es_check( 'AC-01: گیرنده قابل تنظیم (option+filter)', es_contains( 'inc/Forms/Mailer.php', 'get_option( self::OPTION_KEY' ) && es_contains( 'inc/Forms/Mailer.php', "apply_filters( 'es_form_recipient'" ) );
foreach ( array( 'ContactForm', 'QuoteForm', 'RecruitmentForm', 'FeedbackForm' ) as $es_form ) {
	es_check( "AC-02: honeypot مشترک در $es_form", es_contains( "inc/Forms/$es_form.php", 'SpamGuard::honeypot_ok' ) );
}
es_check( 'AC-02: rate-limit مشترک در هر ۴ فرم', es_contains( 'inc/Forms/ContactForm.php', 'SpamGuard::enforce_rate' ) && es_contains( 'inc/Forms/QuoteForm.php', 'SpamGuard::enforce_rate' ) && es_contains( 'inc/Forms/RecruitmentForm.php', 'SpamGuard::enforce_rate' ) && es_contains( 'inc/Forms/FeedbackForm.php', 'SpamGuard::enforce_rate' ) );
es_check( 'AC-02: nonce مشترک از طریق SpamGuard', es_contains( 'inc/Core/Ajax.php', 'SpamGuard::nonce_ok' ) );
$es_mail_order_ok = true;
foreach ( array( 'ContactForm', 'QuoteForm', 'RecruitmentForm', 'FeedbackForm' ) as $es_form ) {
	$es_src         = file_get_contents( ES_ROOT . '/erfansanat/inc/Forms/' . $es_form . '.php' );
	$es_store_pos   = strpos( (string) $es_src, 'Entries::store' );
	$es_mailer_pos  = strpos( (string) $es_src, 'Mailer::send_entry' );
	if ( false === $es_store_pos || false === $es_mailer_pos || $es_mailer_pos < $es_store_pos ) {
		$es_mail_order_ok = false;
	}
}
es_check( 'AC-03/SC-03: ایمیل بعد از ذخیره ورودی در هر ۴ فرم', $es_mail_order_ok );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$_SERVER['REMOTE_ADDR'] = '203.0.113.10';
	SpamGuard::reset_all();
	$GLOBALS['es_mail_log'] = array();
	$es_mail_capture        = static function ( $es_pre, $es_atts ) {
		$GLOBALS['es_mail_log'][] = $es_atts;
		return false; // Force wp_mail failure (AC-03 path).
	};
	add_filter( 'pre_wp_mail', $es_mail_capture, 10, 2 );

	// Shared guard primitives (AC-02).
	es_check( 'AC-02: honeypot خالی مجاز', SpamGuard::honeypot_ok( '' ) && SpamGuard::honeypot_ok( null ) );
	es_check( 'AC-02: honeypot پرشده رد می‌شود', ! SpamGuard::honeypot_ok( 'bot-footprint' ) );
	es_check( 'AC-02: nonce نامعتبر رد می‌شود', ! SpamGuard::nonce_ok( 'bogus', 'es_ajax_contact_submit' ) && SpamGuard::nonce_ok( wp_create_nonce( 'es_ajax_contact_submit' ), 'es_ajax_contact_submit' ) );

	// SC-01: rate limit per IP+scope.
	es_check( 'SC-01: شروع بدون محدودیت', ! is_wp_error( SpamGuard::enforce_rate( 'contact' ) ) );
	for ( $i = 0; $i < SpamGuard::RATE_MAX; $i++ ) {
		SpamGuard::hit( 'contact' );
	}
	es_check( 'SC-01: ارسال پشت‌سرهم محدود می‌شود', is_wp_error( SpamGuard::enforce_rate( 'contact' ) ) && 'es_rate_limited' === SpamGuard::enforce_rate( 'contact' )->get_error_code() );
	es_check( 'SC-01: scope جدا محدود نمی‌شود', ! is_wp_error( SpamGuard::enforce_rate( 'quote' ) ) );
	SpamGuard::reset_all();
	es_check( 'SC-01: reset_all پنجره را پاک می‌کند', ! is_wp_error( SpamGuard::enforce_rate( 'contact' ) ) );

	// AC-01: configurable recipient.
	es_check( 'AC-01: گیرنده پیش‌فرض admin_email', get_option( 'admin_email' ) === Mailer::recipient() );
	$es_filter_to = 'ops@example.test';
	add_filter( 'es_form_recipient', static function () use ( $es_filter_to ) {
		return $es_filter_to;
	} );
	es_check( 'AC-01: فیلتر گیرنده را عوض می‌کند', 'ops@example.test' === Mailer::recipient() );
	update_option( Mailer::OPTION_KEY, 'option@example.test' );
	es_check( 'AC-01: فیلتر بر option مقدار داده‌شده اولویت', 'ops@example.test' === Mailer::recipient() );
	remove_all_filters( 'es_form_recipient' );
	es_check( 'AC-01: option گیرنده را تنظیم می‌کند', 'option@example.test' === Mailer::recipient() );

	// SC-03 / AC-03: wp_mail fails but the entry survives.
	$es_result = ContactForm::submit( array(
		'es_cf_name'    => 'تست T018',
		'es_cf_phone'   => '09130000000',
		'es_cf_message' => 'پیام تست T018',
		'es_cf_hp'      => '',
	), array( 'page' => 'test' ) );
	es_check( 'SC-03: ثبت ورودی با خطای ایمیل حفظ می‌شود', is_array( $es_result ) && isset( $es_result['entry'] ) && null !== get_post( $es_result['entry'] ) );
	es_check( 'AC-03: Mailer false برمی‌گرداند و استثنا نمی‌دهد', false === Mailer::send_entry( 'contact', (int) $es_result['entry'], array( 'name' => 'تست T018' ) ) );
	$es_log = $GLOBALS['es_mail_log'];
	$es_att = is_array( $es_log ) ? end( $es_log ) : array();
	es_check( 'AC-01: wp_mail به گیرنده تنظیم‌شده می‌رود', is_array( $es_att ) && in_array( 'option@example.test', (array) ( $es_att['to'] ?? array() ), true ) );
	es_check( 'AC-01: موضوع ایمیل شامل برچسب فرم است', is_array( $es_att ) && false !== strpos( (string) ( $es_att['subject'] ?? '' ), Schema::types()['contact'] ) );

	// SC-02: invalid email rejected (shared schema validation).
	$es_bad = Schema::validate( 'recruitment', array( 'name' => 'تست', 'phone' => '09130000000', 'background' => 'سوابق', 'email' => 'not-an-email' ) );
	es_check( 'SC-02: ایمیل نامعتبر در اعتبارسنجی مشترک رد می‌شود', is_wp_error( $es_bad ) );
	// SC-01 at form level: budget exhausted → form refuses.
	SpamGuard::reset_all();
	for ( $i = 0; $i < SpamGuard::RATE_MAX; $i++ ) {
		SpamGuard::hit( 'contact' );
	}
	$es_blocked = ContactForm::submit( array(
		'es_cf_name'    => 'اسپمر',
		'es_cf_phone'   => '09131111111',
		'es_cf_message' => 'اسپم',
		'es_cf_hp'      => '',
	), array() );
	es_check( 'SC-01: فرم پس از سقف، WP_Error es_rate_limited می‌دهد', is_wp_error( $es_blocked ) && 'es_rate_limited' === $es_blocked->get_error_code() );

	// Teardown.
	SpamGuard::reset_all();
	remove_filter( 'pre_wp_mail', $es_mail_capture );
	delete_option( Mailer::OPTION_KEY );
	unset( $_SERVER['REMOTE_ADDR'] );
	if ( is_array( $es_result ) && isset( $es_result['entry'] ) ) {
		wp_delete_post( (int) $es_result['entry'], true );
	}
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
