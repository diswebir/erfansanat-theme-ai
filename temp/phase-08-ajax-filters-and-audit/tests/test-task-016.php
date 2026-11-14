<?php
/**
 * P08-T016 — client feedback form: rating range guard, sanitized text,
 * pending/approve moderation status.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Forms\Entries;
use ErfanSanat\Theme\Forms\FeedbackForm;

ob_start();

es_task( 'P08-T016', 'فرم نظرات مشتریان' );

es_check( 'target: template-parts/forms/feedback.php', es_file( 'template-parts/forms/feedback.php' ) );
es_check( 'target: inc/Forms/FeedbackForm.php', es_file( 'inc/Forms/FeedbackForm.php' ) );
es_check( 'وابستگی: boot در functions.php', es_contains( 'functions.php', 'Forms\\FeedbackForm::boot' ) );
es_check( 'AC-03: وضعیت بررسی در handler', es_contains( 'inc/Forms/FeedbackForm.php', '_es_feedback_status' ) );

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
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';
	if ( ! defined( 'DOING_AJAX' ) ) { define( 'DOING_AJAX', true ); }
	wp_set_current_user( 0 );

	es_check( 'endpoint عمومی es_feedback_submit ثبت شده', false !== has_action( 'wp_ajax_nopriv_es_feedback_submit' ) );

	// Part: named fields + rating select + honeypot + nonce.
	ob_start();
	include ES_THEME . '/template-parts/forms/feedback.php';
	$part = ob_get_clean();
	es_check( 'پارت: فیلدهای بازخورد با name', false !== strpos( $part, 'name="es_fb_name"' ) && false !== strpos( $part, 'name="es_fb_rating"' ) && false !== strpos( $part, 'name="es_fb_message"' ) );
	es_check( 'پارت: امتیاز ۱..۵ + honeypot + nonce + خطا', false !== strpos( $part, 'value="5"' ) && false !== strpos( $part, 'name="es_fb_hp"' ) && false !== strpos( $part, 'name="nonce"' ) && false !== strpos( $part, 'data-feedback-error="true"' ) );

	// SC-01: valid feedback stored with pending status (AC-03).
	$ok = FeedbackForm::submit( array(
		'es_fb_name'    => 'مشتری T016',
		'es_fb_rating'  => '۵',
		'es_fb_message' => '<b>کیفیت عالی</b><script>alert(1)</script> نصب تمیز',
	), array( 'page' => '/' ) );
	es_check( 'SC-01: بازخورد معتبر ذخیره می‌شود', is_array( $ok ) && ( $ok['entry'] ?? 0 ) > 0 );
	if ( is_array( $ok ) ) {
		$eid = (int) $ok['entry'];
		$data = get_post_meta( $eid, '_es_entry_data', true );
		es_check( 'AC-01: امتیاز فارسی به عدد نرمال می‌شود', ( $data['rating'] ?? '' ) === '5' );
		es_check( 'AC-02: متن بازخورد sanitize می‌شود', false === strpos( (string) ( $data['message'] ?? 'x' ), '<' ) && false !== strpos( (string) ( $data['message'] ?? '' ), 'نصب تمیز' ) );
		es_check( 'AC-03: وضعیت پیش‌فرض pending است', 'pending' === FeedbackForm::moderation( $eid ) );
		FeedbackForm::approve( $eid );
		es_check( 'AC-03: approve وضعیت را می‌گذارد', 'approve' === FeedbackForm::moderation( $eid ) );
		Entries::delete( $eid );
	}

	// SC-02: out-of-range ratings rejected, nothing stored.
	$before = wp_count_posts( 'es_form_entry' );
	es_check( 'SC-02: امتیاز صفر رد می‌شود', is_wp_error( FeedbackForm::submit( array( 'es_fb_name' => 'x', 'es_fb_rating' => '0', 'es_fb_message' => 'm' ), array() ) ) );
	es_check( 'SC-02: امتیاز شش رد می‌شود', is_wp_error( FeedbackForm::submit( array( 'es_fb_name' => 'x', 'es_fb_rating' => '6', 'es_fb_message' => 'm' ), array() ) ) );
	es_check( 'SC-02: امتیاز غیرعددی رد می‌شود', is_wp_error( FeedbackForm::submit( array( 'es_fb_name' => 'x', 'es_fb_rating' => 'five', 'es_fb_message' => 'm' ), array() ) ) );
	$after = wp_count_posts( 'es_form_entry' );
	es_check( 'SC-02: هیچ ورودی نامعتبر ذخیره نشد', ( $after->publish ?? 0 ) === ( $before->publish ?? 0 ) );

	// Guards.
	es_check( 'SC-02: honeypot پر → رد', is_wp_error( FeedbackForm::submit( array( 'es_fb_name' => 'ربات', 'es_fb_rating' => '5', 'es_fb_message' => 'spam', 'es_fb_hp' => 'x' ), array() ) ) );
	$_REQUEST = array( 'nonce' => 'bad' );
	$res = es_capture_ajax( static function () { ErfanSanat\Theme\Core\Ajax::dispatch( 'feedback_submit' ); } );
	es_check( 'SC-02: nonce نامعتبر → 403', 403 === $res['status'] );
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_feedback_submit' ), 'es_fb_name' => 'endpoint T016', 'es_fb_rating' => '۴', 'es_fb_message' => 'از endpoint' );
	$res = es_capture_ajax( static function () { ErfanSanat\Theme\Core\Ajax::dispatch( 'feedback_submit' ); } );
	es_check( 'SC-01: endpoint معتبر موفق', 200 === $res['status'] && true === ( $res['json']['success'] ?? false ) );
	if ( true === ( $res['json']['success'] ?? false ) ) {
		$eid = (int) ( $res['json']['data']['entry'] ?? 0 );
		es_check( 'SC-01: endpoint ورودی feedback ذخیره کرد', 'feedback' === get_post_meta( $eid, '_es_entry_form', true ) && 'pending' === FeedbackForm::moderation( $eid ) );
		Entries::delete( $eid );
	}
	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
