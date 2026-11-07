<?php
/**
 * P08-T002 — live product search: Persian/English/meta/category matching,
 * publish+visible only, capped results, <200ms QA budget.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Ajax;
use ErfanSanat\Theme\Core\AjaxSearch;

ob_start();

es_task( 'P08-T002', 'جستجوی زنده محصولات' );

es_check( 'target: inc/Core/AjaxSearch.php', es_file( 'inc/Core/AjaxSearch.php' ) );
es_check( 'boot در functions.php', es_contains( 'functions.php', 'Core\AjaxSearch::boot()' ) );

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
	wp_set_current_user( 0 ); // public endpoint, guest.

	es_check( 'AC-01: endpoint عمومی es_product_search ثبت شده', false !== has_action( 'wp_ajax_nopriv_es_product_search' ) && false !== has_action( 'wp_ajax_es_product_search' ) );

	// Fixtures: published (fa title / en meta / sku / category), draft, private.
	$cat_id = 0;
	if ( taxonomy_exists( 'product_cat' ) ) {
		$term = wp_insert_term( 'ریسه آزمایشی T002', 'product_cat' );
		if ( is_array( $term ) ) { $cat_id = (int) $term['term_id']; }
	}
	$pub = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'ریسه سوزنی طلایی T002' ) );
	update_post_meta( $pub, 'english_titl', 'Golden Needle String T002' );
	update_post_meta( $pub, 'zireonvan', 'زیرعنوان آزمایشی' );
	update_post_meta( $pub, '_sku', 'T002-SKU-77' );
	update_post_meta( $pub, '_price', '150000' );
	update_post_meta( $pub, 'true_sale', 'true' );
	if ( $cat_id ) { wp_set_object_terms( $pub, array( $cat_id ), 'product_cat' ); }
	$en_only = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'چراغ آزمایشی T002' ) );
	update_post_meta( $en_only, 'english_titl', 'Moonbeam Lantern T002' );
	$draft = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'draft', 'post_title' => 'ریسه سوزنی پیش‌نویس T002' ) );
	$priv  = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'private', 'post_title' => 'ریسه سوزنی خصوصی T002' ) );
	$many = array();
	for ( $i = 0; $i < 9; $i++ ) {
		$many[] = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'پوینت لایت انبوه T002-' . $i ) );
	}

	// SC-01: Persian title search via the real endpoint.
	$_REQUEST = array( 'nonce' => wp_create_nonce( 'es_ajax_product_search' ), 'term' => 'ریسه سوزنی', 'limit' => 6 );
	$res = es_capture_ajax( static function () { Ajax::dispatch( 'product_search' ); } );
	$rows = $res['json']['data']['results'] ?? array();
	$ids  = array_column( $rows, 'id' );
	es_check( 'SC-01: جستجوی فارسی محصول منتشرشده را برمی‌گرداند', 200 === $res['status'] && true === ( $res['json']['success'] ?? false ) && in_array( $pub, $ids, true ) );
	es_check( 'AC-01: نوع نتیجه product مشخص است', ! empty( $rows ) && 'product' === $rows[0]['type'] );
	es_check( 'AC-02: لینک از permalink واقعی است (slug-agnostic)', ! empty( $rows ) && in_array( get_permalink( $pub ), array_column( $rows, 'url' ), true ) );

	// SC-02: draft/private never returned.
	es_check( 'SC-02: پیش‌نویس/خصوصی برنمی‌گردند', ! in_array( $draft, $ids, true ) && ! in_array( $priv, $ids, true ) );

	// AC-01: English meta + SKU + category matching.
	$en = AjaxSearch::search_products( 'Moonbeam', 6 );
	es_check( 'AC-01: عنوان انگلیسی (متا) نتیجه می‌دهد', in_array( $en_only, array_column( $en, 'id' ), true ) );
	$sku = AjaxSearch::search_products( 'T002-SKU-77', 6 );
	es_check( 'AC-01: SKU نتیجه می‌دهد', in_array( $pub, array_column( $sku, 'id' ), true ) );
	if ( $cat_id ) {
		$cat = AjaxSearch::search_products( 'ریسه آزمایشی', 6 );
		es_check( 'AC-01: نام دسته نتیجه می‌دهد', in_array( $pub, array_column( $cat, 'id' ), true ) );
	} else {
		es_check( 'AC-01: نام دسته نتیجه می‌دهد', false );
	}

	// AC-03: hard cap + short-term guard.
	$capped = AjaxSearch::search_products( 'پوینت لایت انبوه', 50 );
	es_check( 'AC-03: سقف نتایج (۱۲) حتی با limit بزرگ‌تر', count( $capped ) <= 12 && count( $capped ) >= 1 );
	$short = AjaxSearch::search_products( 'ر', 6 );
	es_check( 'AC-03: عبارت خیلی کوتاه کوئری نمی‌زند', array() === $short );
	$empty = AjaxSearch::search_products( 'چیزناموجود T002', 6 );
	es_check( 'AC-03: نتیجه خالی آرایه خالی است', array() === $empty );

	// SC-03: warm engine call under the 200ms QA budget.
	AjaxSearch::search_products( 'ریسه سوزنی', 6 );
	$t0 = hrtime( true );
	AjaxSearch::search_products( 'ریسه سوزنی', 6 );
	$ms = ( hrtime( true ) - $t0 ) / 1e6;
	es_check( 'SC-03: پاسخ موتور زیر ۲۰۰ms (' . round( $ms, 1 ) . 'ms)', $ms < 200 );

	foreach ( array_merge( array( $pub, $en_only, $draft, $priv ), $many ) as $pid ) { wp_delete_post( $pid, true ); }
	if ( $cat_id ) { wp_delete_term( $cat_id, 'product_cat' ); }
	$_REQUEST = array();
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
