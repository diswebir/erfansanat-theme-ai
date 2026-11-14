<?php
/**
 * P08-T007 — price range + purchase mode: numeric validation, real _price,
 * strict true/false sale mode; correct range/inquiry results via endpoint.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Ajax;
use ErfanSanat\Theme\Core\SmartFilters;

ob_start();

es_task( 'P08-T007', 'فیلتر قیمت و حالت خرید' );

es_check( 'target: inc/Core/SmartFilters.php', es_file( 'inc/Core/SmartFilters.php' ) );
es_check( 'وابستگی: JS کنترل‌های مرجع قیمت/حالت را می‌خواند', es_contains( 'assets/js/product-filters.js', 'data-product-price-range' ) && es_contains( 'assets/js/product-filters.js', 'data-product-purchase-mode' ) );

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

	// AC-01: numeric validation — non-numeric/negative rejected (null).
	es_check( 'AC-01: قیمت غیرعددی رد می‌شود', null === SmartFilters::sanitize_price( 'abc' ) && null === SmartFilters::sanitize_price( array( 'x' ) ) );
	es_check( 'AC-01: قیمت منفی رد می‌شود', null === SmartFilters::sanitize_price( '-500' ) );
	es_check( 'AC-01: رشته عددی معتبر پذیرفته می‌شود', 150000.0 === SmartFilters::sanitize_price( '150000' ) && 0.0 === SmartFilters::sanitize_price( '0' ) );
	$args = SmartFilters::build_product_query( array( 'price_min' => 'garbage', 'price_max' => 'NaN; DROP' ) );
	es_check( 'AC-01: ورودی مخرب وارد meta_query نمی‌شود', ! isset( $args['meta_query'] ) );

	// AC-03: strict sale mode.
	es_check( 'AC-03: حالت خرید فقط all/online/inquiry', 'online' === SmartFilters::sanitize_sale_mode( 'online' ) && 'inquiry' === SmartFilters::sanitize_sale_mode( 'inquiry' ) && 'all' === SmartFilters::sanitize_sale_mode( 'evil' ) && 'all' === SmartFilters::sanitize_sale_mode( 'TRUE' ) );

	// Fixtures: priced online/inquiry + unpriced.
	$online  = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'آنلاین گران T007' ) );
	$cheap   = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'آنلاین ارزان T007' ) );
	$inquiry = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'استعلامی T007' ) );
	$noprice = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'بدون قیمت T007' ) );
	update_post_meta( $online, '_price', '800000' );  update_post_meta( $online, 'true_sale', 'true' );
	update_post_meta( $cheap, '_price', '120000' );   update_post_meta( $cheap, 'true_sale', 'true' );
	update_post_meta( $inquiry, '_price', '750000' ); update_post_meta( $inquiry, 'true_sale', 'false' );
	update_post_meta( $noprice, 'true_sale', 'true' );

	// SC-01: price range through the real endpoint (real _price meta).
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'price_min' => '500000', 'price_max' => '900000', 'per_page' => '20' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$html = (string) ( $res['json']['data']['html'] ?? '' );
	es_check( 'SC-01: بازه قیمت فقط محصولات همان بازه را می‌دهد', 200 === $res['status'] && false !== strpos( $html, 'آنلاین گران T007' ) && false !== strpos( $html, 'استعلامی T007' ) && false === strpos( $html, 'آنلاین ارزان T007' ) );
	es_check( 'AC-02: محصول بدون _price از بازه حذف می‌شود', false === strpos( $html, 'بدون قیمت T007' ) );

	// SC-02: inquiry mode returns only true_sale=false products.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'sale_mode' => 'inquiry', 'per_page' => '20' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$html = (string) ( $res['json']['data']['html'] ?? '' );
	es_check( 'SC-02: حالت استعلامی فقط استعلامی‌ها را می‌دهد', false !== strpos( $html, 'استعلامی T007' ) && false === strpos( $html, 'آنلاین گران T007' ) && false === strpos( $html, 'بدون قیمت T007' ) );

	// Combined: price + online.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'sale_mode' => 'online', 'price_min' => '500000', 'price_max' => '900000', 'per_page' => '20' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$html = (string) ( $res['json']['data']['html'] ?? '' );
	es_check( 'ترکیب قیمت+آنلاین فقط هدف را می‌دهد', false !== strpos( $html, 'آنلاین گران T007' ) && false === strpos( $html, 'استعلامی T007' ) && false === strpos( $html, 'آنلاین ارزان T007' ) );

	// Invalid values through the endpoint behave as "no filter", never fatal.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'price_min' => '<script>x</script>', 'sale_mode' => 'evil', 'per_page' => '20' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$data = $res['json']['data'] ?? array();
	es_check( 'AC-01/AC-03: ورودی نامعتبر = بدون فیلتر (بدون fatal)', 200 === $res['status'] && (int) ( $data['total'] ?? 0 ) >= 4 );

	foreach ( array( $online, $cheap, $inquiry, $noprice ) as $pid ) { wp_delete_post( $pid, true ); }
	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
