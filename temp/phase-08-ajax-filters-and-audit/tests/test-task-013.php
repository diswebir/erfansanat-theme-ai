<?php
/**
 * P08-T013 — contact form wired to a real handler: reference fields keep their
 * DOM vocabulary with name attributes, honeypot + nonce rejected, messages
 * escaped, valid submits stored as unread entries.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Forms\ContactForm;
use ErfanSanat\Theme\Forms\Entries;

ob_start();

es_task( 'P08-T013', 'فرم تماس با ما' );

es_check( 'target: template-parts/forms/contact.php', es_file( 'template-parts/forms/contact.php' ) );
es_check( 'target: inc/Forms/ContactForm.php', es_file( 'inc/Forms/ContactForm.php' ) );
es_check( 'وابستگی ثبت‌شده: فرم سکشن تماس به پارت ارجاع می‌دهد', es_contains( 'template-parts/sections/contact/section-form.php', "forms/contact" ) );
es_check( 'وابستگی: تست P07-T021 قرارداد را به فایل جدید نشان می‌دهد', es_contains( dirname( __DIR__, 2 ) . '/phase-07-projects-blog-and-pages/tests/test-task-021.php', 'template-parts/forms/contact.php' ) );
es_check( 'AC-02: honeypot در فرم و در handler', es_contains( 'template-parts/forms/contact.php', 'es_cf_hp' ) && es_contains( 'inc/Forms/ContactForm.php', 'es_cf_hp' ) );

/** Capture an AJAX callback's JSON output + HTTP status without dying. */
function es_capture_ajax( callable $fn ): array {
	$captured = array( 'status' => 200, 'json' => null, 'output' => '' );
	$status_filter = function ( $header, $code ) use ( &$captured ) {
		$captured['status'] = (int) $code;
		return $header;
	};
	add_filter( 'status_header', $status_filter, 10, 2 );
	$die_handler = function () {
		return function ( $message, $title, $args ) { throw new RuntimeException( 'wp_die' ); };
	};
	add_filter( 'wp_die_ajax_handler', $die_handler );
	add_filter( 'wp_die_handler', $die_handler );
	ob_start();
	try { $fn(); } catch ( Throwable $e ) { /* expected wp_die */ }
	$output = ob_get_clean();
	remove_filter( 'status_header', $status_filter );
	remove_filter( 'wp_die_ajax_handler', $die_handler );
	remove_filter( 'wp_die_handler', $die_handler );
	$captured['output'] = $output;
	$captured['json']   = json_decode( $output, true );
	return $captured;
}

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	\ErfanSanat\Theme\Forms\SpamGuard::reset_all(); // P08-T018: fresh rate budget (dependency).
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';
	if ( ! defined( 'DOING_AJAX' ) ) { define( 'DOING_AJAX', true ); }
	wp_set_current_user( 0 );

	es_check( 'endpoint عمومی es_contact_submit ثبت شده', false !== has_action( 'wp_ajax_nopriv_es_contact_submit' ) );

	// Part: reference fields with name attrs, honeypot, nonce, error box.
	ob_start();
	include ES_THEME . '/template-parts/forms/contact.php';
	$part = ob_get_clean();
	es_check( 'AC-01: فیلدهای مرجع name دارند', false !== strpos( $part, 'name="es_cf_name"' ) && false !== strpos( $part, 'name="es_cf_phone"' ) && false !== strpos( $part, 'name="es_cf_organization"' ) && false !== strpos( $part, 'name="es_cf_message"' ) );
	es_check( 'AC-01: DOM مرجع حفظ شده (data-contact-form + کلاس‌ها)', false !== strpos( $part, 'data-contact-form="true"' ) && false !== strpos( $part, 'نام شما' ) && false !== strpos( $part, '۰۹۱۲ ...' ) );
	es_check( 'AC-03: nonce + جعبه خطا + ارسال واقعی', false !== strpos( $part, 'name="nonce"' ) && false !== strpos( $part, 'data-contact-error="true"' ) && es_contains( 'template-parts/forms/contact.php', 'es_contact_submit' ) && es_contains( 'template-parts/forms/contact.php', 'wp_add_inline_script' ) );

	// SC-02: honeypot filled → rejected, nothing stored.
	$before = wp_count_posts( 'es_form_entry' );
	$res = ContactForm::submit( array(
		'es_cf_hp'       => 'bot-was-here',
		'es_cf_name'     => 'ربات آزمایشی',
		'es_cf_phone'    => '09131234567',
		'es_cf_message'  => 'اسپم',
	), array( 'page' => '/contact/' ) );
	es_check( 'SC-02: honeypot پر → رد بدون ذخیره', is_wp_error( $res ) );
	$after = wp_count_posts( 'es_form_entry' );
	es_check( 'SC-02: ورودی ربات ذخیره نشده', ( $after->publish ?? 0 ) === ( $before->publish ?? 0 ) );

	// Valid submit → stored as unread contact entry (SC-01).
	$ok = ContactForm::submit( array(
		'es_cf_name'         => '<b>علی</b><script>alert(1)</script>',
		'es_cf_phone'        => '09131234567',
		'es_cf_organization' => 'شهرداری اصفهان',
		'es_cf_message'      => 'درخواست نورپردازی میدان',
	), array( 'page' => '/contact/' ) );
	es_check( 'SC-01: ارسال معتبر ذخیره می‌شود', is_array( $ok ) && isset( $ok['entry'] ) && $ok['entry'] > 0 && isset( $ok['message'] ) );
	if ( is_array( $ok ) ) {
		$data = get_post_meta( (int) $ok['entry'], '_es_entry_data', true );
		es_check( 'AC-03: پیام‌ها sanitize شده‌اند (اسکریپت حذف)', is_array( $data ) && false === strpos( (string) ( $data['name'] ?? 'x' ), 'script' ) && 'درخواست نورپردازی میدان' === ( $data['message'] ?? '' ) );
		es_check( 'SC-01: ورودی contact ناخوانده ثبت شد', 'contact' === get_post_meta( (int) $ok['entry'], '_es_entry_form', true ) && 'unread' === Entries::status( (int) $ok['entry'] ) );
		Entries::delete( (int) $ok['entry'] );
	}

	// Missing required field → structured error, no entry.
	$missing = ContactForm::submit( array( 'es_cf_name' => 'بدون پیام', 'es_cf_phone' => '09131234567' ), array() );
	es_check( 'فیلد الزامی غایب → خطا بدون ذخیره', is_wp_error( $missing ) );

	// Endpoint: bad nonce → 403; good nonce → success.
	$_REQUEST = array( 'nonce' => 'bad' );
	$res = es_capture_ajax( static function () { ErfanSanat\Theme\Core\Ajax::dispatch( 'contact_submit' ); } );
	es_check( 'SC-02: nonce نامعتبر → 403', 403 === $res['status'] );
	$_REQUEST = array(
		'nonce'         => wp_create_nonce( 'es_ajax_contact_submit' ),
		'es_cf_name'    => 'تماس endpoint T013',
		'es_cf_phone'   => '09131112233',
		'es_cf_message' => 'پیام از endpoint',
	);
	$res = es_capture_ajax( static function () { ErfanSanat\Theme\Core\Ajax::dispatch( 'contact_submit' ); } );
	es_check( 'SC-01: endpoint معتبر موفق می‌شود', 200 === $res['status'] && true === ( $res['json']['success'] ?? false ) );
	if ( true === ( $res['json']['success'] ?? false ) ) {
		$eid = (int) ( $res['json']['data']['entry'] ?? 0 );
		es_check( 'SC-01: endpoint ورودی contact ذخیره کرد', 'contact' === get_post_meta( $eid, '_es_entry_form', true ) );
		Entries::delete( $eid );
	}
	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
