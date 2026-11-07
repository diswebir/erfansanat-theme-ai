<?php
/**
 * P08-T003 — live projects/posts search: type-flagged rows, real permalinks,
 * safe empty message, Persian terms (incl. city/project_type terms).
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Ajax;
use ErfanSanat\Theme\Core\AjaxSearch;

ob_start();

es_task( 'P08-T003', 'جستجوی زنده پروژه‌ها و مقالات' );

es_check( 'target: inc/Core/AjaxSearch.php', es_file( 'inc/Core/AjaxSearch.php' ) );

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
	if ( ! defined( 'DOING_AJAX' ) ) { define( 'DOING_AJAX', true ); }
	wp_set_current_user( 0 );

	es_check( 'AC-01: endpoint عمومی es_content_search ثبت شده', false !== has_action( 'wp_ajax_nopriv_es_content_search' ) && false !== has_action( 'wp_ajax_es_content_search' ) );

	// Fixtures.
	$city_id = 0; $type_id = 0;
	if ( taxonomy_exists( 'city' ) ) {
		$t = wp_insert_term( 'خرم‌آباد T003', 'city' );
		if ( is_array( $t ) ) { $city_id = (int) $t['term_id']; }
	}
	if ( taxonomy_exists( 'project_type' ) ) {
		$t = wp_insert_term( 'نورپردازی پل T003', 'project_type' );
		if ( is_array( $t ) ) { $type_id = (int) $t['term_id']; }
	}
	$project = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه پل سفید T003' ) );
	if ( $city_id ) { wp_set_object_terms( $project, array( $city_id ), 'city' ); }
	if ( $type_id ) { wp_set_object_terms( $project, array( $type_id ), 'project_type' ); }
	$article = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله راهنمای نورپردازی T003', 'post_content' => 'متن مقاله آزمایشی درباره فوتوالیک T003.' ) );
	$draft_p = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'draft', 'post_title' => 'پروژه پل پیش‌نویس T003' ) );
	$draft_a = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'draft', 'post_title' => 'مقاله پیش‌نویس T003' ) );

	// SC-01: project + article title search through the real endpoint.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_content_search' ), 'term' => 'T003', 'limit' => 10 );
	$res  = es_capture_ajax( static function () { Ajax::dispatch( 'content_search' ); } );
	$rows = $res['json']['data']['results'] ?? array();
	$ids  = array_column( $rows, 'id' );
	es_check( 'SC-01: نام پروژه نتیجه می‌دهد', 200 === $res['status'] && in_array( $project, $ids, true ) );
	es_check( 'SC-01: نام مقاله نتیجه می‌دهد', in_array( $article, $ids, true ) );
	es_check( 'AC-01: نوع هر نتیجه مشخص است (project/post)', in_array( 'project', array_column( $rows, 'type' ), true ) && in_array( 'post', array_column( $rows, 'type' ), true ) );
	es_check( 'AC-02: لینک‌ها permalink واقعی‌اند', in_array( get_permalink( $project ), array_column( $rows, 'url' ), true ) && in_array( get_permalink( $article ), array_column( $rows, 'url' ), true ) );
	es_check( 'فقط منتشرشده‌ها برگردند', ! in_array( $draft_p, $ids, true ) && ! in_array( $draft_a, $ids, true ) );

	// SC-02: Persian term + Arabic ye/kaf normalization + taxonomy terms.
	$fa = AjaxSearch::search_content( 'پل سفید', 6 );
	es_check( 'SC-02: عبارت فارسی نتیجه درست می‌دهد', in_array( $project, array_column( $fa, 'id' ), true ) );
	$arabic = AjaxSearch::search_content( 'خرم‌آباد T003', 6 );
	es_check( 'SC-02: ترم شهر فارسی نتیجه می‌دهد', in_array( $project, array_column( $arabic, 'id' ), true ) );
	$content = AjaxSearch::search_content( 'فوتوالیک', 6 );
	es_check( 'جستجو در متن مقاله هم کار می‌کند', in_array( $article, array_column( $content, 'id' ), true ) );

	// AC-03: empty result → safe message through the endpoint.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_content_search' ), 'term' => 'چیزناموجود T003', 'limit' => 6 );
	$res = es_capture_ajax( static function () { Ajax::dispatch( 'content_search' ); } );
	es_check( 'AC-03: نتیجه خالی پیام امن می‌دهد', 200 === $res['status'] && array() === ( $res['json']['data']['results'] ?? null ) && '' !== ( $res['json']['data']['message'] ?? '' ) );

	// Invalid nonce still 403 on this endpoint (shared contract).
	$_REQUEST = array( 'nonce' => 'bad', 'term' => 'T003' );
	$res = es_capture_ajax( static function () { Ajax::dispatch( 'content_search' ); } );
	es_check( 'قرارداد: nonce نامعتبر 403', 403 === $res['status'] );

	// Cap.
	$capped = AjaxSearch::search_content( 'T003', 50 );
	es_check( 'سقف نتایج رعایت می‌شود', count( $capped ) <= AjaxSearch::MAX_RESULTS );

	foreach ( array( $project, $article, $draft_p, $draft_a ) as $pid ) { wp_delete_post( $pid, true ); }
	if ( $city_id ) { wp_delete_term( $city_id, 'city' ); }
	if ( $type_id ) { wp_delete_term( $type_id, 'project_type' ); }
	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
