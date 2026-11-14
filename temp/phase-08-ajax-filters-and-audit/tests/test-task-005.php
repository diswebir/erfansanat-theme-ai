<?php
/**
 * P08-T005 — SmartFilters product QueryBuilder: whitelist taxonomies/attrs,
 * correct meta/tax queries, no raw SQL; combined category+price+attribute.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\AttributeMap;
use ErfanSanat\Theme\Core\SmartFilters;

es_task( 'P08-T005', 'QueryBuilder فیلتر محصول' );

es_check( 'target: inc/Core/SmartFilters.php', es_file( 'inc/Core/SmartFilters.php' ) );
es_check( 'AC-03: بدون SQL مستقیم در سورس', ! es_contains( 'inc/Core/SmartFilters.php', '$wpdb->' ) && ! es_contains( 'inc/Core/SmartFilters.php', 'SELECT ' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';
	AttributeMap::register_missing();

	// Taxonomy whitelist (AC-01 / SC-02).
	$allowed = SmartFilters::allowed_taxonomies();
	es_check( 'AC-01: whitelist شامل product_cat و ۱۸ pa_* است', isset( $allowed['product_cat'] ) && count( array_filter( array_keys( $allowed ), static function ( $k ) { return 0 === strpos( $k, 'pa_' ); } ) ) >= 18 );
	es_check( 'SC-02: تاکسونومی ناشناخته رد می‌شود', ! isset( $allowed['post_tag'] ) && ! isset( $allowed['evil_tax'] ) );

	$args = SmartFilters::build_product_query( array( 'tax' => array( 'evil_tax' => array( 'x' ) ), 'orderby' => 'evil; DROP' ) );
	$tax_names = array();
	foreach ( (array) ( $args['tax_query'] ?? array() ) as $clause ) {
		if ( is_array( $clause ) && isset( $clause['taxonomy'] ) ) { $tax_names[] = $clause['taxonomy']; }
	}
	es_check( 'SC-02: کلید تاکسونومی ناشناخته وارد tax_query نمی‌شود', ! in_array( 'evil_tax', $tax_names, true ) );
	es_check( 'AC-01: orderby ناشناخته به پیش‌فرض امن برمی‌گردد', isset( $args['orderby'] ) && is_string( $args['orderby'] ) && false === strpos( $args['orderby'], 'DROP' ) );

	// Fixtures.
	$cat_a = wp_insert_term( 'دسته الف T005', 'product_cat' );
	$cat_b = wp_insert_term( 'دسته ب T005', 'product_cat' );
	$cat_a = (int) $cat_a['term_id']; $cat_b = (int) $cat_b['term_id'];
	$ip67 = es_insert_term( 'IP67 T005', 'pa_ip-rating', array( 'slug' => 'ip67-t005' ) );
	$ip20 = es_insert_term( 'IP20 T005', 'pa_ip-rating', array( 'slug' => 'ip20-t005' ) );
	$mk = static function ( $title, $cat, $price, $ip, $sale = 'true', $status = 'publish' ) {
		$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => $status, 'post_title' => $title ) );
		wp_set_object_terms( $pid, array( $cat ), 'product_cat' );
		wp_set_object_terms( $pid, array( $ip ), 'pa_ip-rating' );
		update_post_meta( $pid, '_price', (string) $price );
		update_post_meta( $pid, 'true_sale', $sale );
		return $pid;
	};
	$hit    = $mk( 'محصول هدف T005', $cat_a, 150000, (int) $ip67['term_id'] );
	$cheap  = $mk( 'ارزان T005', $cat_a, 50000, (int) $ip67['term_id'] );
	$wrongc = $mk( 'دسته دیگر T005', $cat_b, 150000, (int) $ip67['term_id'] );
	$wrongi = $mk( 'آی‌پی دیگر T005', $cat_a, 150000, (int) $ip20['term_id'] );
	$quote  = $mk( 'استعلامی T005', $cat_a, 150000, (int) $ip67['term_id'], 'false' );
	$draft  = $mk( 'پیش‌نویس T005', $cat_a, 150000, (int) $ip67['term_id'], 'true', 'draft' );

	// SC-01: combined category + price range + attribute.
	$res = SmartFilters::query_products( array(
		'tax'       => array( 'product_cat' => array( $cat_a ), 'pa_ip-rating' => array( 'ip67-t005' ) ),
		'price_min' => 100000,
		'price_max' => 200000,
	) );
	$ids = array_map( 'intval', $res['ids'] );
	es_check( 'SC-01: فیلتر ترکیبی فقط محصول هدف را می‌دهد', in_array( $hit, $ids, true ) && ! in_array( $cheap, $ids, true ) && ! in_array( $wrongc, $ids, true ) && ! in_array( $wrongi, $ids, true ) );
	es_check( 'AC-02: پیش‌نویس حذف می‌شود', ! in_array( $draft, $ids, true ) );
	es_check( 'AC-02: total/صفحه‌بندی در پاسخ هست', isset( $res['total'], $res['pages'], $res['page'] ) && $res['total'] >= 1 );

	// true_sale mode.
	$online = SmartFilters::query_products( array( 'tax' => array( 'product_cat' => array( $cat_a ) ), 'sale_mode' => 'online' ) );
	es_check( 'AC-02: حالت خرید آنلاین فقط true_sale=true', in_array( $hit, $online['ids'], true ) && ! in_array( $quote, $online['ids'], true ) );
	$inquiry = SmartFilters::query_products( array( 'tax' => array( 'product_cat' => array( $cat_a ) ), 'sale_mode' => 'inquiry' ) );
	es_check( 'AC-02: حالت استعلامی فقط true_sale=false', in_array( $quote, $inquiry['ids'], true ) && ! in_array( $hit, $inquiry['ids'], true ) );

	// Sorting whitelist.
	$sorted = SmartFilters::query_products( array( 'tax' => array( 'product_cat' => array( $cat_a ) ), 'orderby' => 'price_asc', 'per_page' => 10 ) );
	$pos_cheap = array_search( $cheap, array_map( 'intval', $sorted['ids'] ), true );
	$pos_hit   = array_search( $hit, array_map( 'intval', $sorted['ids'] ), true );
	es_check( 'AC-02: مرتب‌سازی قیمت صعودی درست عمل می‌کند', false !== $pos_cheap && false !== $pos_hit && $pos_cheap < $pos_hit );

	// Pagination clamps.
	$page = SmartFilters::query_products( array( 'per_page' => 2, 'page' => 2, 'tax' => array( 'product_cat' => array( $cat_a ) ) ) );
	es_check( 'AC-02: صفحه‌بندی per_page/page اعمال می‌شود', count( $page['ids'] ) <= 2 && 2 === $page['page'] );
	$clamped = SmartFilters::build_product_query( array( 'per_page' => 500, 'page' => -3 ) );
	es_check( 'سقف per_page و کف page رعایت می‌شود', $clamped['posts_per_page'] <= 48 && $clamped['paged'] >= 1 );

	// Text search clause uses WP s (no raw SQL).
	$txt = SmartFilters::query_products( array( 'term' => 'محصول هدف' ) );
	es_check( 'AC-03: جستجوی متنی از s وردپرس استفاده می‌کند', in_array( $hit, array_map( 'intval', $txt['ids'] ), true ) );

	foreach ( array( $hit, $cheap, $wrongc, $wrongi, $quote, $draft ) as $pid ) { wp_delete_post( $pid, true ); }
	wp_delete_term( $cat_a, 'product_cat' ); wp_delete_term( $cat_b, 'product_cat' );
	wp_delete_term( (int) $ip67['term_id'], 'pa_ip-rating' ); wp_delete_term( (int) $ip20['term_id'], 'pa_ip-rating' );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
