<?php
/**
 * P06-T016 — Shell آرشیو فروشگاه
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T016', 'Shell آرشیو فروشگاه' );

es_check( 'target: archive-product.php', es_file( 'woocommerce/archive-product.php', ES_THEME ) );
es_check( 'target: product-archive-shell.php', es_file( 'template-parts/archive/product-archive-shell.php' ) );
es_check( 'AC-02: یکپارچه‌سازی shop/آرشیو (template_include)', es_contains( 'functions.php', "is_post_type_archive( 'product' )" ) && es_contains( 'functions.php', 'is_shop()' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$mkprod = static function ( $title, $slug ) {
		return (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug ) );
	};
	$cat = es_insert_term( 'درخت نوری', 'product_cat', array( 'slug' => 'light-tree-shop' ) );
	$p1  = $mkprod( 'درخت نوری دلسا', 'delsa-shop' );
	$p2  = $mkprod( 'ریسه بلوطی', 'baloti-shop' );
	$p3  = $mkprod( 'پوینت لایت', 'point-shop' );
	wp_set_object_terms( $p1, array( (int) $cat['term_id'] ), 'product_cat' );
	update_post_meta( $p1, 'true_sale', 'true' );
	update_post_meta( $p1, '_price', '100000' );

	// Scope the archive query to this test's fixtures (isolates from DB state).
	add_filter( 'es_shop_archive_query_args', static function ( $args ) use ( $p1, $p2, $p3 ) {
		$args['post__in'] = array( $p1, $p2, $p3 );
		$args['orderby']  = 'post__in';
		return $args;
	} );

	// SC-01: render the full archive template.
	ob_start();
	include ES_THEME . '/woocommerce/archive-product.php';
	$html = (string) ob_get_clean();

	es_check( 'AC-01: id=archive و data-products-archive', false !== strpos( $html, 'id="archive"' ) && false !== strpos( $html, 'data-products-archive="true"' ) );
	es_check( 'SC-01: گرید و کارت‌ها رندر می‌شوند', false !== strpos( $html, 'data-product-grid="true"' ) && 3 === substr_count( $html, 'data-product-item="true"' ) );
	es_check( 'AC-01: سایدبار فیلتر (جستجو/مرتب‌سازی)', false !== strpos( $html, 'data-product-search="true"' ) && false !== strpos( $html, 'data-product-sort="true"' ) );
	es_check( 'AC-01: فیلتر خرید/قیمت/IP/ولتاژ', false !== strpos( $html, 'data-product-purchase-mode="all"' ) && false !== strpos( $html, 'data-product-purchase-mode="call"' ) && false !== strpos( $html, 'data-product-price-range="true"' ) && false !== strpos( $html, 'data-product-ip-filter="IP68"' ) && false !== strpos( $html, 'data-product-voltage-filter="24V"' ) );
	es_check( 'AC-01: ناوبری دسته‌بندی product_cat', false !== strpos( $html, 'درخت نوری' ) && false !== strpos( $html, 'همه محصولات تولیدی' ) );
	es_check( 'AC-02: صفحه‌بندی client-side (data-pagination-root=true)', false !== strpos( $html, 'data-pagination-root="true"' ) && false !== strpos( $html, 'data-per-page="9"' ) );
	es_check( 'AC-01: شمارنده محصول واقعی', false !== strpos( $html, 'data-product-count="true"' ) );
	es_check( 'AC-01: hero با h1 فروشگاه', false !== strpos( $html, 'فروشگاه محصولات' ) && false !== strpos( $html, 'نورپردازی شهری و صنعتی' ) );
	es_check( 'AC-01: drawer موبایل فیلتر', false !== strpos( $html, 'data-archive-drawer="true"' ) && false !== strpos( $html, 'data-archive-drawer-open="true"' ) );
	es_check( 'AC-03: حالت خالی پنهان وقتی محصول هست', false !== strpos( $html, 'data-product-empty="true" class="hidden' ) );

	// AC-02: template_include routes product archive to the theme template.
	$GLOBALS['wp_query']->is_post_type_archive = true;
	$GLOBALS['wp_query']->query_vars['post_type'] = 'product';
	$routed = apply_filters( 'template_include', '/default/template.php' );
	es_check( 'AC-02: template_include → archive-product.php', false !== strpos( $routed, 'woocommerce/archive-product.php' ) );
	$GLOBALS['wp_query']->is_post_type_archive = false;
	$GLOBALS['wp_query']->query_vars['post_type'] = '';

	// SC-02: empty archive renders empty state, no fatal (shell with empty query).
	$empty_q = new WP_Query( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'post__in' => array( 0 ) ) );
	ob_start();
	get_template_part( 'template-parts/archive/product', 'archive-shell', array( 'query' => $empty_q, 'show_hero' => false ) );
	$empty_html = (string) ob_get_clean();
	es_check( 'SC-02: آرشیو خالی fatal نمی‌دهد', is_string( $empty_html ) );
	es_check( 'AC-03: آرشیو خالی پیام درست می‌دهد', false !== strpos( $empty_html, 'data-product-empty="true" class="block' ) && false !== strpos( $empty_html, 'محصولی با این مشخصات یافت نشد' ) );
	es_check( 'SC-02: آرشیو خالی کارت ندارد', false === strpos( $empty_html, 'data-product-item="true"' ) );

	foreach ( array( $p1, $p2, $p3 ) as $p ) {
		wp_delete_post( $p, true );
	}
	wp_delete_term( (int) $cat['term_id'], 'product_cat' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
