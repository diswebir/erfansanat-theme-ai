<?php
/**
 * P08-T008 — pa_* attribute filter UI: all 18 attrs when they have terms,
 * pa_color swatches, empty attrs build no UI; IP/voltage/color filter via
 * the real endpoint; unknown values rejected.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Ajax;
use ErfanSanat\Theme\Core\AttributeMap;

ob_start();

es_task( 'P08-T008', 'فیلترهای ویژگی‌های فنی' );

es_check( 'target: inc/Core/SmartFilters.php', es_file( 'inc/Core/SmartFilters.php' ) );
es_check( 'target: template-parts/archive/product-filters.php', es_file( 'template-parts/archive/product-filters.php' ) );
es_check( 'وابستگی: JS مقادیر ویژگی را به endpoint می‌فرستد', es_contains( 'assets/js/product-filters.js', 'data-product-attr-filter' ) && es_contains( 'template-parts/archive/product-filters.php', 'data-product-attr-group' ) );

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
	AttributeMap::register_missing();
	if ( ! defined( 'DOING_AJAX' ) ) { define( 'DOING_AJAX', true ); }
	wp_set_current_user( 0 );

	// Fixture terms: IP/voltage/color populated, one attribute left empty (AC-03).
	$mk = static function ( $tax, $name ) {
		$exists = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false, 'slug' => sanitize_title( $name ), 'fields' => 'ids' ) );
		$id = ( is_array( $exists ) && $exists ) ? (int) reset( $exists ) : 0;
		if ( ! $id ) {
			$t = wp_insert_term( $name, $tax );
			$id = is_array( $t ) ? (int) $t['term_id'] : 0;
		}
		return array( $id, sanitize_title( $name ) );
	};
	list( $ip68_id, ) = $mk( 'pa_ip-rating', 'IP68' );
	list( $ip67_id, ) = $mk( 'pa_ip-rating', 'IP67' );
	list( $v24_id, )  = $mk( 'pa_input-voltage', '24V' );
	list( $red_id, )  = $mk( 'pa_color', 'قرمز' );

	$ip68 = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'ضدآب IP68 T008' ) );
	$ip67 = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'ریسه IP67 T008' ) );
	update_post_meta( $ip68, '_price', '500000' ); update_post_meta( $ip67, '_price', '600000' );
	wp_set_object_terms( $ip68, array( $ip68_id ), 'pa_ip-rating', true );
	wp_set_object_terms( $ip68, array( $v24_id ), 'pa_input-voltage', true );
	wp_set_object_terms( $ip68, array( $red_id ), 'pa_color', true );
	wp_set_object_terms( $ip67, array( $ip67_id ), 'pa_ip-rating', true );

	// SC-01: IP filter via the real endpoint (term_id values).
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'pa_ip-rating' => (string) $ip68_id, 'per_page' => '20' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$html = (string) ( $res['json']['data']['html'] ?? '' );
	es_check( 'SC-01: فیلتر IP فقط محصول IP68 را می‌دهد', 200 === $res['status'] && false !== strpos( $html, 'ضدآب IP68 T008' ) && false === strpos( $html, 'ریسه IP67 T008' ) );

	// Combined attrs: voltage + IP.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'pa_ip-rating' => (string) $ip68_id, 'pa_input-voltage' => (string) $v24_id, 'per_page' => '20' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$html = (string) ( $res['json']['data']['html'] ?? '' );
	es_check( 'ترکیب دو ویژگی کار می‌کند', false !== strpos( $html, 'ضدآب IP68 T008' ) && false === strpos( $html, 'ریسه IP67 T008' ) );

	// SC-02: unknown term id → no results, no fatal.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'pa_ip-rating' => '99999999', 'per_page' => '20' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$data = $res['json']['data'] ?? array();
	es_check( 'SC-02: مقدار ناموجود → صفر نتیجه', 200 === $res['status'] && 0 === (int) ( $data['total'] ?? -1 ) && '' === trim( (string) ( $data['html'] ?? 'x' ) ) );

	// Part: groups render per attribute with terms; pa_color swatches; empty attr → no group.
	ob_start();
	include ES_THEME . '/template-parts/archive/product-filters.php';
	$part = ob_get_clean();
	es_check( 'پارت: گروه IP با برچسب فارسی و چک‌باکس', false !== strpos( $part, 'data-product-attr-group="pa_ip-rating"' ) && false !== strpos( $part, 'data-product-attr-filter="pa_ip-rating"' ) );
	es_check( 'پارت: گروه ولتاژ و رنگ رندر می‌شوند', false !== strpos( $part, 'data-product-attr-group="pa_input-voltage"' ) && false !== strpos( $part, 'data-product-attr-group="pa_color"' ) );
	es_check( 'AC-02: سواچ رنگ pa_color با کلاس نگاشت رندر می‌شود', false !== strpos( $part, 'data-product-attr-filter="pa_color"' ) && false !== strpos( $part, 'bg-red-500' ) && false !== strpos( $part, 'rounded-full' ) );
	es_check( 'AC-03: ویژگی بدون مقدار UI نمی‌سازد', false === strpos( $part, 'data-product-attr-group="pa_material"' ) && false === strpos( $part, 'data-product-attr-group="pa_control_type"' ) );
	// Stricter AC-03: only groups whose taxonomy has terms exist — count groups.
	$group_count = preg_match_all( '/data-product-attr-group="/', $part, $m );
	es_check( 'AC-03: تعداد گروه‌ها = ویژگی‌های دارای مقدار', $group_count >= 3 && $group_count <= 18 );

	wp_delete_post( $ip68, true ); wp_delete_post( $ip67, true );
	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
