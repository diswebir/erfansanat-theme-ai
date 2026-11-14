<?php
/**
 * P02-T008 — AJAX Save: امنیت
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ThemeOptions;
use ErfanSanat\Theme\Options\Store;

// Keep output buffered so headers_sent() stays false and status_header() runs.
ob_start();

es_task( 'P02-T008', 'AJAX Save: امنیت' );

es_check( 'target: inc/Admin/ThemeOptions.php', es_file( 'inc/Admin/ThemeOptions.php' ) );

/**
 * Capture an AJAX callback's JSON output + HTTP status without dying.
 *
 * @return array{status:int,json:mixed,output:string}
 */
function es_capture_ajax( callable $fn ): array {
	$captured = array( 'status' => 200, 'json' => null, 'output' => '' );

	$status_filter = function ( $header, $code ) use ( &$captured ) {
		$captured['status'] = (int) $code;
		return $header;
	};
	add_filter( 'status_header', $status_filter, 10, 2 );

	$die_handler = function () {
		return function ( $message, $title, $args ) {
			throw new RuntimeException( 'wp_die' );
		};
	};
	// DOING_AJAX routes wp_die through the ajax handler.
	add_filter( 'wp_die_ajax_handler', $die_handler );
	add_filter( 'wp_die_handler', $die_handler );

	ob_start();
	try {
		$fn();
	} catch ( Throwable $e ) {
		// Expected wp_die.
	}
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
	require_once ABSPATH . 'wp-admin/includes/user.php';

	if ( ! defined( 'DOING_AJAX' ) ) {
		define( 'DOING_AJAX', true );
	}

	es_check( 'AC-01: hook wp_ajax_es_save_options ثبت شده', false !== has_action( 'wp_ajax_es_save_options', 'ErfanSanat\\Theme\\Admin\\ThemeOptions::ajax_save' ) );
	es_check( 'AC-01: سورس از check_ajax_referer استفاده می‌کند', es_contains( 'inc/Admin/ThemeOptions.php', 'check_ajax_referer' ) );
	es_check( 'AC-01: سورس از current_user_can استفاده می‌کند', es_contains( 'inc/Admin/ThemeOptions.php', 'current_user_can' ) );

	Store::reset();
	wp_set_current_user( 1 );
	$valid_nonce = wp_create_nonce( ThemeOptions::NONCE_ACTION );

	// SC-01: invalid nonce → 403.
	$_REQUEST[ ThemeOptions::NONCE_NAME ] = 'invalid-nonce';
	$_POST['values']                      = wp_slash( wp_json_encode( array( 'brand_name' => 'نباید ذخیره شود' ) ) );
	$res = es_capture_ajax( array( ThemeOptions::class, 'ajax_save' ) );
	es_check( 'SC-01: nonce نامعتبر 403 می‌دهد', 403 === $res['status'] );
	es_check( 'AC-02: پاسخ خطا JSON ساختاریافته است', is_array( $res['json'] ) && false === $res['json']['success'] );
	es_check( 'SC-01: با nonce نامعتبر چیزی ذخیره نشد', 'عرفان صنعت اصفهان' === Store::get( 'brand_name' ) );

	// SC-02: unauthorized user (valid nonce, no capability) → 403.
	$sub_id = wp_insert_user(
		array(
			'user_login' => 'es_ajax_subscriber',
			'user_pass'  => wp_generate_password( 20 ),
			'user_email' => 'es_ajax_subscriber@example.com',
			'role'       => 'subscriber',
		)
	);
	wp_set_current_user( $sub_id );
	$_REQUEST[ ThemeOptions::NONCE_NAME ] = wp_create_nonce( ThemeOptions::NONCE_ACTION );
	$_POST['values']                      = wp_slash( wp_json_encode( array( 'brand_name' => 'توسط subscriber' ) ) );
	$res = es_capture_ajax( array( ThemeOptions::class, 'ajax_save' ) );
	es_check( 'SC-02: کاربر غیرمجاز 403 می‌گیرد', 403 === $res['status'] );
	es_check( 'SC-02: کاربر غیرمجاز نتوانست ذخیره کند', 'عرفان صنعت اصفهان' === Store::get( 'brand_name' ) );
	wp_delete_user( $sub_id );

	// SC-03: valid admin save → 200 success.
	wp_set_current_user( 1 );
	$_REQUEST[ ThemeOptions::NONCE_NAME ] = wp_create_nonce( ThemeOptions::NONCE_ACTION );
	$_POST['values']                      = wp_slash( wp_json_encode( array( 'brand_name' => 'ذخیره ایجکس' ) ) );
	$res = es_capture_ajax( array( ThemeOptions::class, 'ajax_save' ) );
	es_check( 'SC-03: ذخیره معتبر 200 می‌دهد', 200 === $res['status'] );
	es_check( 'AC-02: پاسخ موفق JSON ساختاریافته است', is_array( $res['json'] ) && true === $res['json']['success'] && isset( $res['json']['data']['values'] ) );
	es_check( 'SC-03: مقدار واقعاً ذخیره شد', 'ذخیره ایجکس' === Store::get( 'brand_name' ) );

	// AC-03: a repeated identical request does not corrupt data.
	$res2 = es_capture_ajax( array( ThemeOptions::class, 'ajax_save' ) );
	es_check( 'AC-03: درخواست تکراری داده را خراب نمی‌کند', 200 === $res2['status'] && 'ذخیره ایجکس' === Store::get( 'brand_name' ) );

	Store::reset();
	unset( $_REQUEST[ ThemeOptions::NONCE_NAME ], $_POST['values'] );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

ob_end_flush();
es_summary();
