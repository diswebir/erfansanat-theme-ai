<?php
/**
 * P08-T009 — sorting/pagination/reset without refresh: ORDERBY arg contract,
 * filtered page 2 via the real endpoint, price ordering, reset restores totals.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Ajax;
use ErfanSanat\Theme\Core\SmartFilters;

ob_start();

es_task( 'P08-T009', 'مرتب‌سازی/صفحه‌بندی/حذف فیلتر' );

es_check( 'target: assets/js/product-filters.js', es_file( 'assets/js/product-filters.js' ) );
es_check( 'target: inc/Core/SmartFilters.php', es_file( 'inc/Core/SmartFilters.php' ) );
es_check( 'وابستگی: JS سورت و صفحه‌بندی مرجع را می‌خواند', es_contains( 'assets/js/product-filters.js', 'data-product-sort' ) && es_contains( 'assets/js/product-filters.js', 'data-pagination-root' ) && es_contains( 'assets/js/product-filters.js', 'data-page-btn' ) );
es_check( 'AC-03: reset کامل state', es_contains( 'assets/js/product-filters.js', 'data-product-filters-clear' ) );

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

	// AC-01: ORDERBY arg contract per option.
	$a = SmartFilters::build_product_query( array( 'orderby' => 'price_asc' ) );
	es_check( 'AC-01: ارزان‌ترین → meta_value_num صعودی روی _price', '_price' === ( $a['meta_key'] ?? '' ) && 'meta_value_num' === ( $a['orderby'] ?? '' ) && 'ASC' === ( $a['order'] ?? '' ) );
	$a = SmartFilters::build_product_query( array( 'orderby' => 'price_desc' ) );
	es_check( 'AC-01: گران‌ترین → نزولی', 'DESC' === ( $a['order'] ?? '' ) );
	$a = SmartFilters::build_product_query( array( 'orderby' => 'oldest' ) );
	es_check( 'AC-01: قدیمی‌ترین → date ASC', 'date' === ( $a['orderby'] ?? '' ) && 'ASC' === ( $a['order'] ?? '' ) );
	$a = SmartFilters::build_product_query( array( 'orderby' => 'popular' ) );
	es_check( 'AC-01: محبوب‌ترین → comment_count DESC', 'comment_count' === ( $a['orderby'] ?? '' ) && 'DESC' === ( $a['order'] ?? '' ) );
	$a = SmartFilters::build_product_query( array( 'orderby' => 'hacked' ) );
	es_check( 'AC-01: مقدار ناشناس → پیش‌فرض latest', 'date' === ( $a['orderby'] ?? '' ) && 'DESC' === ( $a['order'] ?? '' ) );

	// Fixtures: 12 products in one category.
	$cat = wp_insert_term( 'دسته صفحه‌بندی T009', 'product_cat' );
	$cat = (int) $cat['term_id'];
	$ids = array();
	for ( $i = 1; $i <= 12; $i++ ) {
		// Distinct timestamps: #01 newest (largest ts) so date-DESC paging is deterministic.
		$when = date( 'Y-m-d H:i:s', strtotime( '-1 day' ) + ( 13 - $i ) * 60 );
		$pid  = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => sprintf( 'محصول شماره %02d T009', $i ), 'post_date' => $when, 'post_date_gmt' => gmdate( 'Y-m-d H:i:s', strtotime( '-1 day' ) + ( 13 - $i ) * 60 ) ) );
		wp_set_object_terms( $pid, array( $cat ), 'product_cat' );
		update_post_meta( $pid, '_price', (string) ( 100000 + $i * 10000 ) );
		$ids[] = $pid;
	}

	// SC-01: filtered page 2 (9 per page → 3 items on page 2).
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'cats' => (string) $cat, 'page' => '2', 'per_page' => '9' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$data = $res['json']['data'] ?? array();
	$html = (string) ( $data['html'] ?? '' );
	es_check( 'SC-01: صفحه دوم فیلترشده فقط سه آیتم درست می‌دهد', 200 === $res['status'] && false !== strpos( $html, 'محصول شماره 10 T009' ) && false !== strpos( $html, 'محصول شماره 12 T009' ) && false === strpos( $html, 'محصول شماره 01 T009' ) && 3 === (int) preg_match_all( '/data-product-item="true"/', $html ) );
	es_check( 'AC-02: صفحه‌بندی با فیلتر حفظ می‌شود (total/pages درست)', 12 === (int) ( $data['total'] ?? 0 ) && 2 === (int) ( $data['pages'] ?? 0 ) && 2 === (int) ( $data['page'] ?? 0 ) );

	// Price ordering actually applied (page 1 of price_asc holds the 9 cheapest).
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'cats' => (string) $cat, 'orderby' => 'price_asc', 'per_page' => '9' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$html = (string) ( $res['json']['data']['html'] ?? '' );
	$first = strpos( $html, 'محصول شماره 01 T009' );
	$last  = strpos( $html, 'محصول شماره 02 T009' );
	es_check( 'AC-01: ارزان‌ترین اول رندر می‌شود', false !== $first && false !== $last && $first < $last );

	// SC-02: reset contract — no filters → full set restored.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'per_page' => '9' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$data = $res['json']['data'] ?? array();
	es_check( 'SC-02: بدون فیلتر تعداد کل برمی‌گردد', 12 === (int) ( $data['total'] ?? 0 ) && 2 === (int) ( $data['pages'] ?? 0 ) && 9 === (int) preg_match_all( '/data-product-item="true"/', (string) ( $data['html'] ?? '' ) ) );

	// Page beyond range → clamped to last page, never fatal.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'cats' => (string) $cat, 'page' => '99', 'per_page' => '9' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$data = $res['json']['data'] ?? array();
	es_check( 'صفحه خارج از بازه → آخرین صفحه', 2 === (int) ( $data['page'] ?? 0 ) && 3 === (int) preg_match_all( '/data-product-item="true"/', (string) ( $data['html'] ?? '' ) ) );

	foreach ( $ids as $pid ) { wp_delete_post( $pid, true ); }
	wp_delete_term( $cat, 'product_cat' );
	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
