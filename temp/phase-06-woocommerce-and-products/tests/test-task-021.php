<?php
/**
 * P06-T021 — toolbar/pagination/result-count
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-021.php
 * Behaviour (sort + page 2 with real main.js): test-task-021-dom.mjs
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T021', 'toolbar/pagination/result-count' );

es_check( 'target: toolbar.php', es_file( 'template-parts/archive/toolbar.php' ) );
es_check( 'target: pagination.php', es_file( 'template-parts/archive/pagination.php' ) );
es_check( 'shell از toolbar/pagination استفاده می‌کند', es_contains( 'template-parts/archive/product-archive-shell.php', "archive/toolbar" ) && es_contains( 'template-parts/archive/product-archive-shell.php', "archive/pagination" ) );
es_check( 'AC-01: گزینه‌های مرتب‌سازی قیمت/امتیاز/محبوبیت', es_contains( 'template-parts/archive/toolbar.php', 'value="price-desc"' ) && es_contains( 'template-parts/archive/toolbar.php', 'value="popular"' ) && es_contains( 'template-parts/archive/toolbar.php', 'value="rating"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$fa = static function ( $v ) {
		$map = array();
		for ( $d = 0; $d < 10; $d++ ) {
			$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
		}
		return strtr( (string) $v, $map );
	};

	// AC-03: result count is the real total passed in.
	ob_start();
	get_template_part( 'template-parts/archive/toolbar', null, array( 'total' => 20, 'toolbar_title' => 'همه محصولات' ) );
	$toolbar = (string) ob_get_clean();
	es_check( 'AC-03: result-count واقعی (۲۰)', false !== strpos( $toolbar, 'data-product-count="true"' ) && false !== strpos( $toolbar, $fa( 20 ) ) );
	es_check( 'AC-01: select مرتب‌سازی data-product-sort', false !== strpos( $toolbar, 'data-product-sort="true"' ) );

	// AC-02: pagination numbers from real total/per-page.
	ob_start();
	get_template_part( 'template-parts/archive/pagination', null, array( 'total' => 20, 'per_page' => 9 ) );
	$pag = (string) ob_get_clean();
	es_check( 'AC-02: ۳ دکمه صفحه برای ۲۰/۹', 3 === substr_count( $pag, 'data-page-btn=' ) );
	es_check( 'AC-02: data-per-page=9', false !== strpos( $pag, 'data-per-page="9"' ) );
	es_check( 'AC-02: خلاصه صفحه صحیح', false !== strpos( $pag, sprintf( 'صفحه %s از %s (%s مورد)', $fa( 1 ), $fa( 3 ), $fa( 20 ) ) ) );
	es_check( 'AC-02: دکمه قبلی/بعدی', false !== strpos( $pag, 'data-page-prev="true"' ) && false !== strpos( $pag, 'data-page-next="true"' ) );

	// Single page → nav hidden.
	ob_start();
	get_template_part( 'template-parts/archive/pagination', null, array( 'total' => 5, 'per_page' => 9 ) );
	$pag1 = (string) ob_get_clean();
	es_check( 'AC-02: تک‌صفحه → nav پنهان', false !== strpos( $pag1, 'data-pagination-root="true"' ) && 1 === substr_count( $pag1, 'data-page-btn=' ) && preg_match( '/data-pagination-root="true"[^>]*class="hidden/', $pag1 ) );

	// Integration: shell with 12 products → toolbar 12, pagination 2 pages.
	$ids = array();
	for ( $i = 1; $i <= 12; $i++ ) {
		$ids[] = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول ' . $i, 'post_name' => 't21-prod-' . $i ) );
	}
	$q = new WP_Query( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'post__in' => $ids, 'orderby' => 'post__in', 'no_found_rows' => true ) );
	ob_start();
	get_template_part( 'template-parts/archive/product', 'archive-shell', array( 'query' => $q, 'show_hero' => false, 'per_page' => 9 ) );
	$shell = (string) ob_get_clean();
	es_check( 'AC-03: شمارنده shell = ۱۲', false !== strpos( $shell, 'data-product-count="true"' ) && false !== strpos( $shell, $fa( 12 ) ) );
	es_check( 'SC-01: صفحه‌بندی shell ۲ صفحه', 2 === substr_count( $shell, 'data-page-btn=' ) );

	foreach ( $ids as $pid ) {
		wp_delete_post( $pid, true );
	}
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
