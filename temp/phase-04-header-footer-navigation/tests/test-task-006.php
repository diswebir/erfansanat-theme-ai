<?php
/**
 * P04-T006 — مگامنوی محصولات
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Store;

es_task( 'P04-T006', 'مگامنوی محصولات' );

es_check( 'target: template-parts/header/mega-products.php', es_file( 'template-parts/header/mega-products.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		register_taxonomy( 'product_cat', 'product', array( 'hierarchical' => true, 'public' => true ) );
	}

	// Fixtures: two top-level categories, first with three children.
	$top1 = wp_insert_term( 'پوینت لایت', 'product_cat' );
	$top2 = wp_insert_term( 'ریسه‌ها', 'product_cat' );
	$t1   = $top1['term_id'];
	$c1   = wp_insert_term( 'آلفا', 'product_cat', array( 'parent' => $t1 ) );
	$c2   = wp_insert_term( 'بتا', 'product_cat', array( 'parent' => $t1 ) );
	$c3   = wp_insert_term( 'گاما', 'product_cat', array( 'parent' => $t1 ) );

	$render = static function () {
		ob_start();
		get_template_part( 'template-parts/header/mega-products' );
		return (string) ob_get_clean();
	};

	Store::set( 'mega_products_per_tab', 6 );
	$html = $render();

	// AC-01: preserved data attributes.
	es_check( 'AC-01: data-desktop-mega-panel="products"', false !== strpos( $html, 'data-desktop-mega-panel="products"' ) );
	es_check( 'AC-01: data-mega-prod-tab="0"', false !== strpos( $html, 'data-mega-prod-tab="0"' ) );
	es_check( 'AC-01: data-mega-prod-panel="0"', false !== strpos( $html, 'data-mega-prod-panel="0"' ) );

	// AC-02: categories come from the DB.
	es_check( 'AC-02: نام دسته از DB می‌آید', false !== strpos( $html, 'پوینت لایت' ) && false !== strpos( $html, 'ریسه‌ها' ) );
	es_check( 'AC-02: زیردسته‌ها رندر می‌شوند', false !== strpos( $html, 'آلفا' ) && false !== strpos( $html, 'گاما' ) );

	// SC-01: first tab/panel active, second hidden.
	es_check( 'SC-01: تب اول فعال است', false !== strpos( $html, 'bg-white text-emerald-950 shadow-2xs ring-1 ring-emerald-200' ) );
	es_check( 'SC-01: پنل دوم hidden است', false !== strpos( $html, 'data-mega-prod-panel="1" class="hidden"' ) );

	// SC-02: Persian term gets a real (encoded) term link.
	$link = get_term_link( (int) $t1, 'product_cat' );
	es_check( 'SC-02: لینک ترم فارسی درست است', ! is_wp_error( $link ) && false !== strpos( $html, esc_url( $link ) ) );

	// AC-03: per-tab item count follows the option.
	Store::set( 'mega_products_per_tab', 2 );
	$html = $render();
	es_check( 'AC-03: per_tab=2 آیتم سوم را حذف می‌کند', false !== strpos( $html, 'آلفا' ) && false === strpos( $html, 'گاما' ) );
	Store::delete( 'mega_products_per_tab' );

	// Cleanup fixtures.
	foreach ( array( $c1, $c2, $c3, $top1, $top2 ) as $term ) {
		if ( ! is_wp_error( $term ) ) {
			wp_delete_term( $term['term_id'], 'product_cat' );
		}
	}
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
