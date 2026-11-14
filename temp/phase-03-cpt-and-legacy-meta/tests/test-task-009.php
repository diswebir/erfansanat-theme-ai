<?php
/**
 * P03-T009 — ریدایرکت aliasهای محصول/دسته
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\LegacyRedirects;

es_task( 'P03-T009', 'ریدایرکت aliasهای محصول/دسته' );

es_check( 'target: inc/Core/LegacyRedirects.php', es_file( 'inc/Core/LegacyRedirects.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// AC-01: products/<slug> → product/<slug>.
	es_check( 'AC-01: alias تک‌محصول → /product/', home_url( '/product/test-lamp/' ) === LegacyRedirects::resolve( '/products/test-lamp/' ) );

	// AC-02: all 7 category aliases map to their canonical product_cat slug.
	$pairs = array(
		'category-led-string-lights'     => 'strings-and-lighting-decorations',
		'category-led-wall-washer'       => 'wall-washer',
		'category-point-light-pixel'     => 'point-light',
		'category-smart-light-trees'     => 'light-tree',
		'category-urban-chandeliers'     => 'light-chandelier',
		'category-urban-light-elements'  => 'urban-lighting-elements',
		'category-urban-sunbursts'       => 'sun-light-elements',
	);
	$all_ok = true;
	foreach ( $pairs as $alias => $canonical ) {
		$all_ok = $all_ok && ( home_url( '/product_cat/' . $canonical . '/' ) === LegacyRedirects::resolve( '/products/' . $alias . '/' ) );
	}
	es_check( 'AC-02: هر ۷ alias دسته به product_cat متناظر', $all_ok );

	// AC-03: products / products.html → shop.
	$shop = LegacyRedirects::resolve( '/products/' );
	es_check( 'AC-03: products → shop', is_string( $shop ) && false !== strpos( $shop, '/shop' ) );
	es_check( 'AC-03: products.html → shop', LegacyRedirects::resolve( '/products.html' ) === $shop );

	// AC-04: the category map is configurable from live data via a filter.
	$cb = function ( $map ) { $map['category-led-string-lights'] = 'اسلاگ-فارسی'; return $map; };
	add_filter( 'es_legacy_redirects_product_categories', $cb );
	es_check( 'AC-04: نگاشت با فیلتر قابل تنظیم است', home_url( '/product_cat/اسلاگ-فارسی/' ) === LegacyRedirects::resolve( '/products/category-led-string-lights/' ) );
	remove_filter( 'es_legacy_redirects_product_categories', $cb );

	// SC-02: query string is preserved.
	$with_q = LegacyRedirects::resolve( '/products/test-lamp/?utm_source=x&ref=2' );
	es_check( 'SC-02: query string حفظ می‌شود', false !== strpos( $with_q, 'utm_source=x' ) && false !== strpos( $with_q, 'ref=2' ) );

	// SC-03: canonical URLs are never redirected (no loop).
	es_check( 'SC-03: مسیر canonical ریدایرکت نمی‌شود', null === LegacyRedirects::resolve( '/product/test-lamp/' ) );
	es_check( 'SC-03: مسیر دسته canonical ریدایرکت نمی‌شود', null === LegacyRedirects::resolve( '/product_cat/wall-washer/' ) );

	// SC-01: handle() issues a 301 to the canonical destination.
	$capture = function ( string $uri, string $qs = '' ) {
		$_SERVER['REQUEST_URI']  = $uri;
		$_SERVER['QUERY_STRING'] = $qs;
		$got = null;
		$cb  = function ( $location, $status ) use ( &$got ) {
			$got = array( 'location' => $location, 'status' => $status );
			throw new RuntimeException( 'redirect' );
		};
		add_filter( 'wp_redirect', $cb, 10, 2 );
		try {
			LegacyRedirects::handle();
		} catch ( RuntimeException $e ) {
			// Expected.
		}
		remove_filter( 'wp_redirect', $cb );
		return $got;
	};

	$r = $capture( '/products/category-led-wall-washer/' );
	es_check( 'SC-01: پاسخ 301 است', is_array( $r ) && 301 === $r['status'] );
	es_check( 'SC-01: مقصد canonical است', is_array( $r ) && home_url( '/product_cat/wall-washer/' ) === $r['location'] );

	// SC-03: handle() on a canonical path does not redirect.
	$r2 = $capture( '/product/test-lamp/' );
	es_check( 'SC-03: handle روی canonical ریدایرکت نمی‌کند', null === $r2 );

	unset( $_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING'] );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
