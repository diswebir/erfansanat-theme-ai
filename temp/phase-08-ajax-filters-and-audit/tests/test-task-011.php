<?php
/**
 * P08-T011 — perf/cache: versioned result cache invalidated on content
 * changes, no N+1 card rendering, 100/100-item p95 benchmark under 200ms.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Ajax;
use ErfanSanat\Theme\Core\AjaxSearch;
use ErfanSanat\Theme\Core\SmartFilters;

ob_start();

es_task( 'P08-T011', 'کارایی/کش فیلتر و جستجو' );

es_check( 'target: inc/Core/SmartFilters.php', es_file( 'inc/Core/SmartFilters.php' ) );
es_check( 'target: inc/Core/AjaxSearch.php', es_file( 'inc/Core/AjaxSearch.php' ) );
es_check( 'AC-02: invalidation روی انتشار/ویرایش', es_contains( 'inc/Core/SmartFilters.php', 'save_post' ) && es_contains( 'inc/Core/SmartFilters.php', 'deleted_post' ) );
es_check( 'AC-03: prime کش پست‌ها قبل از رندر کارت‌ها', es_contains( 'inc/Core/SmartFilters.php', '_prime_post_caches' ) );

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
	\ErfanSanat\Theme\Core\AttributeMap::register_missing();
	if ( ! defined( 'DOING_AJAX' ) ) { define( 'DOING_AJAX', true ); }
	wp_set_current_user( 0 );

	// Query counter to observe cache hits vs misses.
	$GLOBALS['es_t011_queries'] = 0;
	add_action( 'pre_get_posts', function () { $GLOBALS['es_t011_queries']++; } );

	$cat = wp_insert_term( 'دسته کش T011', 'product_cat' );
	$cat = (int) $cat['term_id'];
	$ids = array();
	for ( $i = 1; $i <= 10; $i++ ) {
		$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول کش ' . $i . ' T011' ) );
		wp_set_object_terms( $pid, array( $cat ), 'product_cat' );
		update_post_meta( $pid, '_price', (string) ( $i * 10000 ) );
		$ids[] = $pid;
	}

	// Warm the cache, then re-run: no new WP_Query may execute (cache hit).
	$state = array( 'tax' => array( 'product_cat' => array( $cat ) ), 'per_page' => 9 );
	SmartFilters::query_products( $state );
	$GLOBALS['es_t011_queries'] = 0;
	$first  = SmartFilters::query_products( $state );
	$cold_n = $GLOBALS['es_t011_queries'];
	$second = SmartFilters::query_products( $state );
	$warm_n = $GLOBALS['es_t011_queries'];
	es_check( 'AC-02: کوئری دوم از کش می‌آید (بدون WP_Query)', 0 === $cold_n && 0 === $warm_n && $first['total'] === $second['total'] && $first['ids'] === $second['ids'] );

	// SC-02: publishing a new matching product invalidates and the result grows.
	$new = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول کش تازه T011' ) );
	wp_set_object_terms( $new, array( $cat ), 'product_cat' );
	update_post_meta( $new, '_price', '999000' );
	$after = SmartFilters::query_products( $state );
	es_check( 'SC-02: انتشار پست کش را باطل و نتیجه را به‌روز می‌کند', 11 === (int) $after['total'] && in_array( $new, $after['ids'], true ) );
	// Edit path too.
	wp_update_post( array( 'ID' => $new, 'post_status' => 'draft' ) );
	$after2 = SmartFilters::query_products( $state );
	es_check( 'SC-02: ویرایش/پیش‌نویس هم کش را باطل می‌کند', 10 === (int) $after2['total'] );
	wp_delete_post( $new, true );

	// AC-03: card rendering primes posts — bounded query count, no N+1.
	global $wpdb;
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'cats' => (string) $cat, 'per_page' => '9' );
	$wpdb->num_queries = 0;
	$res = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$cards = (int) preg_match_all( '/data-product-item="true"/', (string) ( $res['json']['data']['html'] ?? '' ) );
	$q_first = $wpdb->num_queries;
	$wpdb->num_queries = 0;
	$res = es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
	$q_warm = $wpdb->num_queries;
	es_check( 'AC-03: ۹ کارت با کوئری محدود رندر می‌شود (بدون N+1)', 9 === $cards && $q_first < 40, "queries=$q_first cards=$cards" );
	es_check( 'AC-02/AC-03: پاسخ گرم کوئری ناچیزی دارد', $q_warm < $q_first, "warm=$q_warm cold=$q_first" );

	// SC-01: 100/100 benchmark — p95 of 10 runs per endpoint under 200ms.
	$bench_ids = array();
	for ( $i = 1; $i <= 100; $i++ ) {
		$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'بنچمارک محصول ' . $i . ' T011' ) );
		update_post_meta( $pid, '_price', (string) ( $i * 1000 ) );
		$bench_ids[] = $pid;
	}
	$bench_pids = array();
	for ( $i = 1; $i <= 100; $i++ ) {
		$bench_pids[] = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'بنچمارک پروژه ' . $i . ' T011' ) );
	}
	$times = array();
	for ( $run = 0; $run < 10; $run++ ) {
		$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_filters' ), 'per_page' => '12', 'page' => (string) ( $run + 1 ) );
		$t0 = microtime( true );
		es_capture_ajax( static function () { Ajax::dispatch( 'product_filters' ); } );
		$times[] = ( microtime( true ) - $t0 ) * 1000;
	}
	sort( $times );
	$p95_products = $times[8];
	$times = array();
	for ( $run = 0; $run < 10; $run++ ) {
		$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_project_filters' ), 'per_page' => '12', 'page' => (string) ( $run + 1 ) );
		$t0 = microtime( true );
		es_capture_ajax( static function () { Ajax::dispatch( 'project_filters' ); } );
		$times[] = ( microtime( true ) - $t0 ) * 1000;
	}
	sort( $times );
	$p95_projects = $times[8];
	$times = array();
	for ( $run = 0; $run < 10; $run++ ) {
		$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_search' ), 'term' => 'بنچمارک ' . ( $run + 1 ), 'limit' => '12' );
		$t0 = microtime( true );
		es_capture_ajax( static function () { Ajax::dispatch( 'product_search' ); } );
		$times[] = ( microtime( true ) - $t0 ) * 1000;
	}
	sort( $times );
	$p95_search = $times[8];
	es_check( 'AC-01: p95 فیلتر محصول ۱۰۰ آیتمی زیر 200ms', $p95_products < 200, sprintf( 'p95=%.1fms', $p95_products ) );
	es_check( 'AC-01: p95 فیلتر پروژه ۱۰۰ آیتمی زیر 200ms', $p95_projects < 200, sprintf( 'p95=%.1fms', $p95_projects ) );
	es_check( 'AC-01: p95 جستجوی محصول ۱۰۰ آیتمی زیر 200ms', $p95_search < 200, sprintf( 'p95=%.1fms', $p95_search ) );

	foreach ( $bench_ids as $pid ) { wp_delete_post( $pid, true ); }
	foreach ( $bench_pids as $pid ) { wp_delete_post( $pid, true ); }
	foreach ( $ids as $pid ) { wp_delete_post( $pid, true ); }
	wp_delete_term( $cat, 'product_cat' );
	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
