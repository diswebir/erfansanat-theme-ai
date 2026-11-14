<?php
/**
 * P08-T010 — project filters: project_type/city + text search with real
 * per-term counters, no-refresh cards via es_project_filters.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Ajax;
use ErfanSanat\Theme\Core\SmartFilters;

ob_start();

es_task( 'P08-T010', 'فیلترهای پروژه‌ها و شمارنده' );

es_check( 'target: inc/Core/SmartFilters.php', es_file( 'inc/Core/SmartFilters.php' ) );
es_check( 'target: assets/js/project-filters.js', es_file( 'assets/js/project-filters.js' ) );
es_check( 'target: template-parts/archive/project-filters.php', es_file( 'template-parts/archive/project-filters.php' ) );
es_check( 'وابستگی: شل پروژه پارت را include می‌کند', es_contains( 'template-parts/archive/project-archive-shell.php', 'archive/project-filters' ) );
es_check( 'AC-03: JS گرید را بدون رفرش عوض می‌کند', es_contains( 'assets/js/project-filters.js', 'data-project-grid' ) && es_contains( 'assets/js/project-filters.js', 'innerHTML' ) );

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

	es_check( 'endpoint عمومی es_project_filters ثبت شده', false !== has_action( 'wp_ajax_nopriv_es_project_filters' ) );

	// Fixtures: 2 types × 3 cities.
	$mk = static function ( $tax, $name ) {
		$found = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false, 'name' => $name, 'fields' => 'ids' ) );
		if ( is_array( $found ) && $found ) { return (int) reset( $found ); }
		$t = wp_insert_term( $name, $tax );
		return is_array( $t ) ? (int) $t['term_id'] : 0;
	};
	$type_a = $mk( 'project_type', 'نورپردازی T010' );
	$type_b = $mk( 'project_type', 'المان شهری T010' );
	$isf = $mk( 'city', 'اصفهان T010' );
	$kh  = $mk( 'city', 'خمینی‌شهر T010' );

	$ids = array();
	$plan = array( // title, type, city
		array( 'میدان آزادی T010', $type_a, $isf ),
		array( 'تونل نوری چهارراه T010', $type_a, $kh ),
		array( 'المان ورودی T010', $type_b, $isf ),
		array( 'نمایش نوری پل T010', $type_b, $kh ),
		array( 'پروژه بدون شهر T010', $type_a, 0 ),
	);
	foreach ( $plan as $row ) {
		$pid = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => $row[0] ) );
		if ( $row[1] ) { wp_set_object_terms( $pid, array( $row[1] ), 'project_type' ); }
		if ( $row[2] ) { wp_set_object_terms( $pid, array( $row[2] ), 'city' ); }
		$ids[] = $pid;
	}

	// SC-01: city filter returns only that city's project cards.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_project_filters' ), 'cities' => (string) $isf, 'per_page' => '10' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'project_filters' ); } );
	$html = (string) ( $res['json']['data']['html'] ?? '' );
	es_check( 'SC-01: فیلتر اصفهان فقط پروژه‌های همان شهر را می‌دهد', 200 === $res['status'] && false !== strpos( $html, 'میدان آزادی T010' ) && false !== strpos( $html, 'المان ورودی T010' ) && false === strpos( $html, 'تونل نوری چهارراه T010' ) && false === strpos( $html, 'پروژه بدون شهر T010' ) );
	es_check( 'AC-03: کارت مرجع data-project در پاسخ هست', false !== strpos( $html, 'data-project-' ) );

	// Combined: type + city.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_project_filters' ), 'types' => (string) $type_a, 'cities' => (string) $isf, 'per_page' => '10' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'project_filters' ); } );
	$html = (string) ( $res['json']['data']['html'] ?? '' );
	es_check( 'ترکیب نوع+شهر فقط هدف را می‌دهد', false !== strpos( $html, 'میدان آزادی T010' ) && false === strpos( $html, 'المان ورودی T010' ) );

	// Text search over project titles (reference «جستجو در پروژه‌ها…»).
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_project_filters' ), 'term' => 'تونل نوری T010', 'per_page' => '10' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'project_filters' ); } );
	$html = (string) ( $res['json']['data']['html'] ?? '' );
	es_check( 'جستجوی متنی عنوان پروژه کار می‌کند', false !== strpos( $html, 'تونل نوری چهارراه T010' ) && false === strpos( $html, 'میدان آزادی T010' ) );
	// Term-name search stays safe: a city name alone matches nothing, never fatal.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_project_filters' ), 'term' => 'خمینی‌شهر', 'per_page' => '10' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'project_filters' ); } );
	$data = $res['json']['data'] ?? array();
	es_check( 'جستجوی نام شهر عنوانی ندارد → خالی اما سالم', 200 === $res['status'] && 0 === (int) ( $data['total'] ?? -1 ) );

	// SC-02: real per-term counters.
	$counts = SmartFilters::project_term_counts( array() );
	es_check( 'SC-02: شمارنده نوع پروژه واقعی است', ( $counts['project_type'][ $type_a ] ?? 0 ) === 3 && ( $counts['project_type'][ $type_b ] ?? 0 ) === 2 );
	es_check('SC-02: شمارنده شهر واقعی است', ( $counts['city'][ $isf ] ?? 0 ) === 2 && ( $counts['city'][ $kh ] ?? 0 ) === 2 );
	// Cross-taxonomy facets: with city=اصفهان active, type counters reflect the subset.
	$counts = SmartFilters::project_term_counts( array( 'tax' => array( 'city' => array( $isf ) ) ) );
	es_check( 'شمارنده Facet با فیلتر دیگر به‌روز می‌شود', ( $counts['project_type'][ $type_a ] ?? 0 ) === 1 && ( $counts['project_type'][ $type_b ] ?? 0 ) === 1 );
	// Counts ride the endpoint response too.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_project_filters' ), 'per_page' => '10' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'project_filters' ); } );
	$data = $res['json']['data'] ?? array();
	es_check( 'شمارنده در پاسخ endpoint هست', isset( $data['counts']['city'][ (string) $isf ] ) || isset( $data['counts']['city'][ $isf ] ) );

	// Unknown value + out-of-range page stay safe.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_project_filters' ), 'cities' => '99999999', 'page' => '99', 'per_page' => '10' );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'project_filters' ); } );
	$data = $res['json']['data'] ?? array();
	es_check( 'مقدار ناموجود → صفر نتیجه بدون fatal', 200 === $res['status'] && 0 === (int) ( $data['total'] ?? -1 ) );

	// Part renders counters + clear + enqueues script.
	ob_start();
	include ES_THEME . '/template-parts/archive/project-filters.php';
	$part = ob_get_clean();
	es_check( 'پارت: چک‌باکس نوع با شمارنده رندر می‌شود', false !== strpos( $part, 'data-project-type-filter="' . $type_a . '"' ) && false !== strpos( $part, 'نورپردازی T010' ) );
	es_check( 'پارت: چک‌باکس شهر + دکمه حذف + enqueue', false !== strpos( $part, 'data-project-city-filter="' . $isf . '"' ) && false !== strpos( $part, 'data-project-filters-clear="true"' ) && wp_script_is( 'es-project-filters', 'enqueued' ) );

	foreach ( $ids as $pid ) { wp_delete_post( $pid, true ); }
	wp_delete_term( $type_a, 'project_type' ); wp_delete_term( $type_b, 'project_type' );
	wp_delete_term( $isf, 'city' ); wp_delete_term( $kh, 'city' );
	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
