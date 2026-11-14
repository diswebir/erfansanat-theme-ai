<?php
/**
 * P08-T001 — shared AJAX contract: unique actions + nonces, input whitelist,
 * structured JSON errors (403 invalid nonce, XSS/SQLi rejected, JSON schema).
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Ajax;

ob_start();

es_task( 'P08-T001', 'قرارداد و امنیت AJAX' );

es_check( 'target: inc/Core/Ajax.php', es_file( 'inc/Core/Ajax.php' ) );
es_check( 'boot در functions.php', es_contains( 'functions.php', 'Core\Ajax::boot()' ) );

/** Capture an AJAX callback's JSON output + HTTP status without dying. */
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
	if ( ! defined( 'DOING_AJAX' ) ) { define( 'DOING_AJAX', true ); }

	wp_set_current_user( 1 );
	$GLOBALS['es_t001_seen'] = null;
	$GLOBALS['es_t001_calls'] = 0;

	$ok = Ajax::register( 't001demo', function ( array $input ) {
		$GLOBALS['es_t001_seen'] = $input;
		$GLOBALS['es_t001_calls']++;
		return array( 'echo' => $input['term'], 'page' => $input['page'] );
	}, array(
		'public' => true,
		'inputs' => array(
			'term' => array( 'type' => 'text', 'default' => '' ),
			'page' => array( 'type' => 'int', 'default' => 1, 'min' => 1, 'max' => 100 ),
			'kind' => array( 'type' => 'enum', 'default' => 'all', 'choices' => array( 'all', 'product', 'project' ) ),
		),
	) );
	es_check( 'AC-01: ثبت action موفق است', true === $ok );
	es_check( 'AC-01: نام یکتا — ثبت تکراری رد می‌شود', true !== Ajax::register( 't001demo', function () { return array(); } ) );
	es_check( 'AC-01: نام غیرمجاز رد می‌شود', true !== Ajax::register( 'Bad Name!', function () { return array(); } ) );
	es_check( 'AC-01: hookهای یکتای es_ + nopriv برای public', false !== has_action( 'wp_ajax_es_t001demo' ) && false !== has_action( 'wp_ajax_nopriv_es_t001demo' ) );
	es_check( 'AC-01: nonce اختصاصی هر action', 'es_ajax_t001demo' === Ajax::nonce_action( 't001demo' ) && Ajax::nonce_action( 't001demo' ) !== Ajax::nonce_action( 'other' ) );

	$dispatch = static function () { Ajax::dispatch( 't001demo' ); };

	// SC-01: invalid nonce → 403, handler never runs.
	$_REQUEST = array( 'nonce' => 'invalid-nonce', 'term' => 'x' );
	$res = es_capture_ajax( $dispatch );
	es_check( 'SC-01: nonce نامعتبر 403 می‌دهد', 403 === $res['status'] );
	es_check( 'SC-01: هندلر با nonce نامعتبر اجرا نشد', 0 === $GLOBALS['es_t001_calls'] );
	es_check( 'AC-03: خطای nonce ساختاریافته است', is_array( $res['json'] ) && false === $res['json']['success'] && isset( $res['json']['data']['code'], $res['json']['data']['message'] ) && 'invalid_nonce' === $res['json']['data']['code'] );

	// SC-02: malicious inputs are sanitized/whitelisted before the handler.
	$_REQUEST = array(
		'nonce' => wp_create_nonce( 'es_ajax_t001demo' ),
		'term'  => '<script>alert(1)</script>سلام',
		'page'  => '2 OR 1=1; DROP TABLE wp_posts',
		'kind'  => 'evil_kind',
		'inject_extra' => 'should-not-pass',
	);
	$res = es_capture_ajax( $dispatch );
	$seen = $GLOBALS['es_t001_seen'];
	es_check( 'SC-02: تگ XSS از ورودی متنی حذف می‌شود', is_array( $seen ) && false === strpos( $seen['term'], '<script>' ) && false !== strpos( $seen['term'], 'سلام' ) );
	es_check( 'SC-02: ورودی SQLi به عدد امن تبدیل می‌شود', is_array( $seen ) && 2 === $seen['page'] );
	es_check( 'AC-02: enum خارج از whitelist به پیش‌فرض برمی‌گردد', is_array( $seen ) && 'all' === $seen['kind'] );
	es_check( 'AC-02: کلید خارج از whitelist حذف می‌شود', is_array( $seen ) && ! array_key_exists( 'inject_extra', $seen ) && ! array_key_exists( 'nonce', $seen ) );

	// SC-03: valid response follows the JSON schema.
	es_check( 'SC-03: پاسخ موفق schema استاندارد دارد', 200 === $res['status'] && is_array( $res['json'] ) && true === $res['json']['success'] && isset( $res['json']['data']['echo'], $res['json']['data']['page'] ) );

	// Capability policy: private action rejects a subscriber even with a valid nonce.
	Ajax::register( 't001private', function () { return array( 'ok' => true ); }, array( 'capability' => 'manage_options' ) );
	es_check( 'AC-01: action خصوصی nopriv ندارد', false !== has_action( 'wp_ajax_es_t001private' ) && false === has_action( 'wp_ajax_nopriv_es_t001private' ) );
	$sub_id = wp_insert_user( array( 'user_login' => 'es_t001_sub', 'user_pass' => wp_generate_password( 20 ), 'user_email' => 'es_t001_sub@example.com', 'role' => 'subscriber' ) );
	wp_set_current_user( $sub_id );
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_t001private' ) );
	$res = es_capture_ajax( static function () { Ajax::dispatch( 't001private' ); } );
	es_check( 'SC-01: کاربر بدون capability با nonce معتبر 403 می‌گیرد', 403 === $res['status'] && is_array( $res['json'] ) && false === $res['json']['success'] );
	wp_delete_user( $sub_id );
	wp_set_current_user( 1 );

	// AC-03: WP_Error and exceptions become structured errors without traces.
	Ajax::register( 't001error', function () { return new WP_Error( 'bad_input', 'ورودی نامعتبر است', array( 'status' => 422 ) ); }, array( 'public' => true ) );
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_t001error' ) );
	$res = es_capture_ajax( static function () { Ajax::dispatch( 't001error' ); } );
	es_check( 'AC-03: WP_Error → خطای ساختاریافته با status خودش', 422 === $res['status'] && is_array( $res['json'] ) && false === $res['json']['success'] && 'bad_input' === $res['json']['data']['code'] );
	Ajax::register( 't001boom', function () { throw new RuntimeException( 'secret internal detail' ); }, array( 'public' => true ) );
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_t001boom' ) );
	$res = es_capture_ajax( static function () { Ajax::dispatch( 't001boom' ); } );
	es_check( 'AC-03: استثنا → 500 بدون stack trace/جزئیات داخلی', 500 === $res['status'] && is_array( $res['json'] ) && false === $res['json']['success'] && false === strpos( $res['output'], 'secret internal detail' ) && false === strpos( $res['output'], 'Stack trace' ) );

	// Front-end config: url + per-action nonces attached to es-main.
	$config = Ajax::config();
	es_check( 'پیکربندی فرانت: ajaxUrl و nonce هر action', isset( $config['url'], $config['nonces']['t001demo'] ) && false !== strpos( $config['url'], 'admin-ajax.php' ) );

	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
