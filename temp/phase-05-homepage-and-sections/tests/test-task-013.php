<?php
/**
 * P05-T013 — سکشن منتخب محصولات
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\AttributeMap;
use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T013', 'سکشن منتخب محصولات' );

es_check( 'target: بخش محصولات ساخته شده', es_file( 'template-parts/sections/home/section-products.php' ) );
es_check( 'AC-01: id=products در قالب', es_contains( 'template-parts/sections/home/section-products.php', 'id="products"' ) );
es_check( 'AC-03: فیلتر موجودی انبار در قالب', es_contains( 'template-parts/sections/home/section-products.php', '_stock_status' ) );
es_check( 'کارت استاندارد محصول بازاستفاده می‌شود', es_contains( 'template-parts/sections/home/section-products.php', 'cards/card-product' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}
	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		register_taxonomy( 'product_cat', 'product', array( 'public' => true, 'hierarchical' => true ) );
	}
	AttributeMap::register_missing();

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'products', 'type' => 'products', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// Fixtures: 4 published in-stock + 1 draft + 1 out-of-stock, two categories.
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'prod', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/product.jpg' );
	$cat_a = wp_insert_term( 'پوینت لایت', 'product_cat', array( 'slug' => 'point-light' ) );
	$cat_b = wp_insert_term( 'درخت نوری', 'product_cat', array( 'slug' => 'light-tree' ) );
	$pubs = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$p = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول منتشر ' . $i ) );
		set_post_thumbnail( $p, $att );
		wp_set_object_terms( $p, array( (int) ( $i <= 2 ? $cat_a['term_id'] : $cat_b['term_id'] ) ), 'product_cat' );
		update_post_meta( $p, '_stock_status', 'instock' );
		update_post_meta( $p, '_price', '100000' );
		$pubs[] = $p;
	}
	$draft = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'draft', 'post_title' => 'محصول پیش‌نویس مخفی' ) );
	$oos   = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول ناموجود مخفی' ) );
	update_post_meta( $oos, '_stock_status', 'outofstock' );

	// ── AC-01 / SC-01: id + count from setting ────────────────────────────.
	$html = $render( array( 'count' => 3 ) );
	es_check( 'AC-01: id=products حفظ شده', false !== strpos( $html, 'id="products"' ) );
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="products"' ) );
	es_check( 'SC-01: تعداد کارت با تنظیم برابر است', 3 === substr_count( $html, 'data-product-item="true"' ) );

	// ── AC-03: draft + out-of-stock excluded ──────────────────────────────.
	es_check( 'AC-03: محصول پیش‌نویس نمایش داده نمی‌شود', false === strpos( $html, 'محصول پیش‌نویس مخفی' ) );
	es_check( 'AC-03: محصول ناموجود نمایش داده نمی‌شود', false === strpos( $html, 'محصول ناموجود مخفی' ) );
	$html_all = $render( array( 'count' => 24 ) );
	es_check( 'AC-03: فقط ۴ محصول منتشرشده موجود', 4 === substr_count( $html_all, 'data-product-item="true"' ) );

	// ── SC-02: product card carries full data-attr set ────────────────────.
	es_check( 'SC-02: کارت data-product-item دارد', false !== strpos( $html, 'data-product-item="true"' ) );
	es_check( 'SC-02: کارت data-product-category دارد', false !== strpos( $html, 'data-product-category=' ) );
	es_check( 'SC-02: کارت data-product-price دارد', false !== strpos( $html, 'data-product-price=' ) );
	es_check( 'SC-02: کارت data-product-search-text دارد', false !== strpos( $html, 'data-product-search-text=' ) );

	// ── AC-02: category filter from settings ──────────────────────────────.
	add_filter( 'es_products_section_category', static function () { return 'point-light'; } );
	$html_cat = $render( array( 'count' => 24 ) );
	es_check( 'AC-02: فیلتر دسته فقط محصولات همان دسته', 2 === substr_count( $html_cat, 'data-product-item="true"' ) );
	es_check( 'AC-02: عنوان دسته درست در کارت', false !== strpos( $html_cat, 'data-product-category="پوینت لایت"' ) );
	remove_all_filters( 'es_products_section_category' );

	// ── Empty state + escaping ────────────────────────────────────────────.
	foreach ( $pubs as $p ) { wp_delete_post( $p, true ); }
	wp_delete_post( $draft, true );
	wp_delete_post( $oos, true );
	$html_empty = $render();
	es_check( 'حالت خالی DOM را نمی‌شکند', false !== strpos( $html_empty, 'id="products"' ) && false !== strpos( $html_empty, 'محصولی برای نمایش وجود ندارد' ) );
	$html_esc = $render( array( 'heading' => '<b>bold</b>' ) );
	es_check( 'عنوان escape می‌شود', false === strpos( $html_esc, '<b>bold</b>' ) && false !== strpos( $html_esc, '&lt;b&gt;' ) );

	wp_delete_post( $att, true );
	wp_delete_term( $cat_a['term_id'], 'product_cat' );
	wp_delete_term( $cat_b['term_id'], 'product_cat' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
