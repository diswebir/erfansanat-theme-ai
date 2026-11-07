<?php
/**
 * P08-T006 — product filter UI wired to the AJAX endpoint: reference DOM kept,
 * shareable URL state, cards swap without refresh (behavior in JSDOM suite).
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Ajax;
use ErfanSanat\Theme\Core\AttributeMap;

ob_start();

es_task( 'P08-T006', 'UI فیلترهای آرشیو محصول' );

es_check( 'target: template-parts/archive/product-filters.php', es_file( 'template-parts/archive/product-filters.php' ) );
es_check( 'target: assets/js/product-filters.js', es_file( 'assets/js/product-filters.js' ) );
es_check( 'AC-01: چک‌باکس دسته با data-product-cat-filter', es_contains( 'template-parts/archive/product-filters.php', 'data-product-cat-filter' ) );
es_check( 'SC-02: دکمه حذف فیلترها data-product-filters-clear', es_contains( 'template-parts/archive/product-filters.php', 'data-product-filters-clear="true"' ) );
es_check( 'وابستگی ثبت‌شده: شل آرشیو پارت را شامل می‌شود', es_contains( 'template-parts/archive/product-archive-shell.php', "archive/product-filters" ) );
es_check( 'AC-02: state در URL با replaceState', es_contains( 'assets/js/product-filters.js', 'replaceState' ) && es_contains( 'assets/js/product-filters.js', 'URLSearchParams' ) );
es_check( 'AC-03: تعویض گرید بدون رفرش', es_contains( 'assets/js/product-filters.js', 'data-product-grid' ) && es_contains( 'assets/js/product-filters.js', 'innerHTML' ) );
es_check( 'AC-01: data-attrهای مرجع مصرف می‌شوند', es_contains( 'assets/js/product-filters.js', 'data-product-search' ) && es_contains( 'assets/js/product-filters.js', 'data-product-count' ) && es_contains( 'assets/js/product-filters.js', 'data-product-empty' ) );

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

	es_check( 'endpoint عمومی es_product_filters ثبت شده', false !== has_action( 'wp_ajax_nopriv_es_product_filters' ) );

	$cat_a = wp_insert_term( 'دسته هدف T006', 'product_cat' );
	$cat_b = wp_insert_term( 'دسته دیگر T006', 'product_cat' );
	$cat_a = (int) $cat_a['term_id']; $cat_b = (int) $cat_b['term_id'];
	$p1 = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول هدف T006' ) );
	$p2 = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول دیگر T006' ) );
	wp_set_object_terms( $p1, array( $cat_a ), 'product_cat' );
	wp_set_object_terms( $p2, array( $cat_b ), 'product_cat' );
	update_post_meta( $p1, '_price', '100000' ); update_post_meta( $p1, 'true_sale', 'true' );
	update_post_meta( $p2, '_price', '200000' ); update_post_meta( $p2, 'true_sale', 'true' );

	// SC-01 (server side): category filter returns only that category's cards.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'cats' => (string) $cat_a );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$data = $res['json']['data'] ?? array();
	es_check( 'SC-01: پاسخ کارت HTML فقط دسته انتخابی است', 200 === $res['status'] && false !== strpos( (string) ( $data['html'] ?? '' ), 'محصول هدف T006' ) && false === strpos( (string) ( $data['html'] ?? '' ), 'محصول دیگر T006' ) );
	es_check( 'AC-03: کارت مرجع data-product-item دارد', false !== strpos( (string) ( $data['html'] ?? '' ), 'data-product-item="true"' ) );
	es_check( 'شمارنده/صفحه‌ها در پاسخ هست', isset( $data['total'], $data['pages'], $data['page'] ) );

	// Empty result → empty html + total 0 (JS shows the reference empty state).
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'term' => 'چیزناموجود T006' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$data = $res['json']['data'] ?? array();
	es_check( 'حالت خالی: html خالی و total صفر', '' === trim( (string) ( $data['html'] ?? 'x' ) ) && 0 === (int) ( $data['total'] ?? -1 ) );

	// Shared contract: bad nonce → 403.
	$_REQUEST = array( 'nonce' => 'bad' );
	$res = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	es_check( 'قرارداد: nonce نامعتبر 403', 403 === $res['status'] );

	// Part renders checkboxes + clear button and enqueues the script.
	ob_start();
	include ES_THEME . '/template-parts/archive/product-filters.php';
	$part = ob_get_clean();
	es_check( 'پارت: چک‌باکس دسته‌ها با شمارنده رندر می‌شود', false !== strpos( $part, 'data-product-cat-filter="' . $cat_a . '"' ) && false !== strpos( $part, 'دسته هدف T006' ) );
	es_check( 'پارت: دکمه حذف فیلترها + enqueue اسکریپت', false !== strpos( $part, 'data-product-filters-clear="true"' ) && wp_script_is( 'es-product-filters', 'enqueued' ) );

	wp_delete_post( $p1, true ); wp_delete_post( $p2, true );
	wp_delete_term( $cat_a, 'product_cat' ); wp_delete_term( $cat_b, 'product_cat' );
	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
