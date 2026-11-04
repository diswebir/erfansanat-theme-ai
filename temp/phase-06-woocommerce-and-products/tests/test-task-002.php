<?php
/**
 * P06-T002 — Shell تک‌محصول
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T002', 'Shell تک‌محصول' );

es_check( 'target: single-product.php', es_file( 'woocommerce/single-product.php' ) );
es_check( 'target: content-single-product.php', es_file( 'woocommerce/content-single-product.php' ) );
es_check( 'AC-02: هوک woocommerce_before_main_content', es_contains( 'woocommerce/single-product.php', "do_action( 'woocommerce_before_main_content' )" ) );
es_check( 'AC-02: هوک woocommerce_after_main_content', es_contains( 'woocommerce/single-product.php', "do_action( 'woocommerce_after_main_content' )" ) );
es_check( 'AC-02: هوک woocommerce_before_single_product', es_contains( 'woocommerce/content-single-product.php', "do_action( 'woocommerce_before_single_product' )" ) );
es_check( 'AC-02: هوک woocommerce_single_product_summary', es_contains( 'woocommerce/content-single-product.php', "do_action( 'woocommerce_single_product_summary' )" ) );
es_check( 'AC-02: هوک woocommerce_before_single_product_summary', es_contains( 'woocommerce/content-single-product.php', "do_action( 'woocommerce_before_single_product_summary' )" ) );
es_check( 'AC-02: هوک woocommerce_after_single_product_summary', es_contains( 'woocommerce/content-single-product.php', "do_action( 'woocommerce_after_single_product_summary' )" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) { unset( $_POST[ $k ] ); }
	}

	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		ob_start();
		include ES_THEME . '/woocommerce/content-single-product.php';
		return (string) ob_get_clean();
	};

	// Fixtures.
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'img', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/p.jpg' );
	$cat = es_insert_term( 'درخت نوری', 'product_cat', array( 'slug' => 'light-tree' ) );
	$pid = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'درخت نوری دلسا', 'post_excerpt' => 'سازه فولادی با نورپردازی هوشمند.', 'post_content' => '<p>توضیحات کامل محصول دلسا.</p>' ) );
	set_post_thumbnail( $pid, $att );
	wp_set_object_terms( $pid, array( (int) $cat['term_id'] ), 'product_cat' );
	update_post_meta( $pid, '_sku', 'ESE-TR-DELSA' );
	update_post_meta( $pid, '_regular_price', '5000000' );
	update_post_meta( $pid, '_price', '5000000' );
	update_post_meta( $pid, '_stock_status', 'instock' );
	update_post_meta( $pid, 'true_sale', 'false' );

	// ── SC-01: a simple product renders ───────────────────────────────────.
	$html = $render( $pid );
	es_check( 'SC-01: محصول ساده رندر می‌شود', false !== strpos( $html, 'درخت نوری دلسا' ) );
	es_check( 'AC-01: breadcrumb (nav مسیر صفحه)', false !== strpos( $html, 'aria-label="مسیر صفحه"' ) );
	es_check( 'AC-01: عنوان h1', false !== strpos( $html, '<h1' ) && false !== strpos( $html, 'درخت نوری دلسا</h1>' ) );
	es_check( 'AC-01: گالری (data-gallery-root)', false !== strpos( $html, 'data-gallery-root="true"' ) );
	es_check( 'AC-01: کانتینر تب‌ها (data-prod-tabs-container)', false !== strpos( $html, 'data-prod-tabs-container="true"' ) );
	es_check( 'AC-01: id=product-details', false !== strpos( $html, 'id="product-details"' ) );

	// ── SC-02: main-section DOM parity + order ────────────────────────────.
	$p_crumb  = strpos( $html, 'aria-label="مسیر صفحه"' );
	$p_h1     = strpos( $html, '<h1' );
	$p_gallery = strpos( $html, 'data-gallery-root' );
	$p_tabs   = strpos( $html, 'data-prod-tabs-container' );
	es_check( 'AC-01: ترتیب breadcrumb → h1 → gallery → tabs', false !== $p_crumb && $p_crumb < $p_h1 && $p_h1 < $p_gallery && $p_gallery < $p_tabs );
	es_check( 'SC-02: سه دکمه تب desc/specs/reviews', 1 === substr_count( $html, 'data-prod-tab-btn="desc"' ) && 1 === substr_count( $html, 'data-prod-tab-btn="specs"' ) && 1 === substr_count( $html, 'data-prod-tab-btn="reviews"' ) );
	es_check( 'SC-02: سه پنل تب', 3 === preg_match_all( '/data-prod-tab-panel="(desc|specs|reviews)"/', $html ) );
	es_check( 'SC-02: پنل desc فعال (block) و بقیه hidden', 1 === preg_match_all( '/data-prod-tab-panel="desc" class="block"/', $html ) && 2 === preg_match_all( '/data-prod-tab-panel="(specs|reviews)" class="hidden"/', $html ) );
	es_check( 'SC-02: تب desc شامل محتوای محصول', false !== strpos( $html, 'توضیحات کامل محصول دلسا' ) );
	es_check( 'SC-02: خلاصه شامل SKU', false !== strpos( $html, 'ESE-TR-DELSA' ) );
	es_check( 'SC-02: محصول استعلامی → استعلام قیمت روز', false !== strpos( $html, 'استعلام قیمت روز' ) );
	es_check( 'breadcrumb شامل دسته محصول', false !== strpos( $html, 'درخت نوری</a>' ) );

	// ── AC-02: hooks fire and replace the shell fallback ──────────────────.
	// Isolate the mechanism: later Phase-6 tasks (T006) hook real content here,
	// so clear them first to test the shell's hook-or-fallback in isolation.
	remove_all_actions( 'woocommerce_single_product_summary' );
	add_action( 'woocommerce_single_product_summary', static function () { echo '<!--ES-SUMMARY-HOOK-->'; } );
	$html_hook = $render( $pid );
	es_check( 'AC-02: هوک summary اجرا می‌شود', false !== strpos( $html_hook, '<!--ES-SUMMARY-HOOK-->' ) );
	es_check( 'AC-02: با هوک، fallback خلاصه حذف می‌شود', false === strpos( $html_hook, 'تولید سفارشی کارخانه' ) );
	remove_all_actions( 'woocommerce_single_product_summary' );

	// ── AC-03: out-of-stock product renders without fatal ─────────────────.
	$oos = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول ناموجود' ) );
	update_post_meta( $oos, '_stock_status', 'outofstock' );
	$html_oos = $render( $oos );
	es_check( 'AC-03: محصول ناموجود fatal نمی‌دهد', false !== strpos( $html_oos, 'محصول ناموجود' ) );
	es_check( 'AC-03: پیام ناموجود نمایش داده می‌شود', false !== strpos( $html_oos, 'ناموجود است' ) );

	// ── AC-03: invalid (non-product) post returns early, no fatal ─────────.
	$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'برگه معمولی' ) );
	$html_invalid = $render( $page );
	es_check( 'AC-03: پست غیرمحصول خروجی نمی‌دهد (return زودهنگام)', '' === trim( $html_invalid ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $oos, true );
	wp_delete_post( $page, true );
	wp_delete_post( $att, true );
	wp_delete_term( (int) $cat['term_id'], 'product_cat' );
	wp_reset_postdata();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
