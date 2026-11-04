<?php
/**
 * P06-T006 — قیمت/true_sale/خرید
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T006', 'قیمت/true_sale/خرید' );

es_check( 'target: purchase.php', es_file( 'template-parts/product/purchase.php' ) );
es_check( 'AC-01: دکمه data-pdp-add-to-cart', es_contains( 'template-parts/product/purchase.php', 'data-pdp-add-to-cart="true"' ) );
es_check( 'AC-02: CTA استعلام/پیش‌فاکتور', es_contains( 'template-parts/product/purchase.php', 'درخواست پیش‌فاکتور رسمی و استعلام قیمت' ) );

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
		include ES_THEME . '/template-parts/product/purchase.php';
		return (string) ob_get_clean();
	};
	$mkprod = static function ( $title, $slug ) {
		return (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug ) );
	};

	// Persian-digit expectations are generated, never hand-typed.
	$es_fa_map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$es_fa_map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	$es_fa_str = static function ( $v ) use ( $es_fa_map ) { return strtr( (string) $v, $es_fa_map ); };
	$price_sale_expected    = $es_fa_str( number_format( 1450000, 0, '.', ',' ) ) . ' تومان';
	$price_regular_expected = $es_fa_str( number_format( 1620000, 0, '.', ',' ) ) . ' تومان';

	$cat = wp_insert_term( 'ریسه بلوطی LED', 'product_cat', array( 'slug' => 'baloti' ) );
	$att = (int) wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'img', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/b.jpg' );

	// ── SC-01 / AC-01: ONLINE product (true_sale=true) ────────────────────.
	$on = $mkprod( 'ریسه بلوطی هفت رنگ', 'baloti-string' );
	set_post_thumbnail( $on, $att );
	wp_set_object_terms( $on, array( (int) $cat['term_id'] ), 'product_cat' );
	update_post_meta( $on, 'true_sale', 'true' );
	update_post_meta( $on, '_sku', 'ESE-ACR-7082' );
	update_post_meta( $on, '_regular_price', '1620000' );
	update_post_meta( $on, '_sale_price', '1450000' );
	update_post_meta( $on, '_price', '1450000' );
	update_post_meta( $on, '_stock_status', 'instock' );
	update_post_meta( $on, '_min_qty', '5' );
	update_post_meta( $on, '_unit', 'شاخه' );
	update_post_meta( $on, '_wc_average_rating', '4.94' );
	$html_on = $render( $on );
	es_check( 'AC-01: حالت آنلاین → دکمه افزودن به سبد', false !== strpos( $html_on, 'data-pdp-add-to-cart="true"' ) );
	es_check( 'AC-01: badge آماده ثبت سفارش آنلاین', false !== strpos( $html_on, 'آماده ثبت سفارش آنلاین' ) );
	es_check( 'AC-01: قیمت فروش فارسی نمایش داده می‌شود', false !== strpos( $html_on, $price_sale_expected ) );
	es_check( 'AC-01: قیمت اصلی خط‌خورده (تخفیف)', false !== strpos( $html_on, 'line-through' ) && false !== strpos( $html_on, $price_regular_expected ) );
	es_check( 'AC-01: حداقل سفارش ۵ شاخه', false !== strpos( $html_on, 'حداقل سفارش: ۵ شاخه' ) );
	es_check( 'SC-02: data-product-price عددی درست', 1 === preg_match_all( '/data-product-price="1450000"/', $html_on ) );
	es_check( 'SC-02: data-product-slug درست', false !== strpos( $html_on, 'data-product-slug="baloti-string"' ) );
	es_check( 'SC-02: data-product-min/step/category', false !== strpos( $html_on, 'data-product-min="5"' ) && false !== strpos( $html_on, 'data-product-step="1"' ) && false !== strpos( $html_on, 'data-product-category="ریسه بلوطی LED"' ) );
	es_check( 'SC-02: بنر افزودن به سبد (toast)', false !== strpos( $html_on, 'data-pdp-added-banner="true"' ) );
	es_check( 'AC-01: حالت آنلاین CTA استعلام ندارد', false === strpos( $html_on, 'درخواست پیش‌فاکتور رسمی و استعلام قیمت' ) );

	// ── SC-01 / AC-02: INQUIRY product (true_sale=false) ──────────────────.
	$inq = $mkprod( 'درخت نوری دلسا', 'delsa-light-tree' );
	update_post_meta( $inq, 'true_sale', 'false' );
	update_post_meta( $inq, '_sku', 'ESE-TR-DELSA' );
	update_post_meta( $inq, '_wc_average_rating', '4.93' );
	$html_inq = $render( $inq );
	es_check( 'AC-02: حالت استعلامی → CTA پیش‌فاکتور', false !== strpos( $html_inq, 'درخواست پیش‌فاکتور رسمی و استعلام قیمت' ) );
	es_check( 'AC-02: badge تولید سفارشی کارخانه', false !== strpos( $html_inq, 'تولید سفارشی کارخانه' ) );
	es_check( 'AC-03: محصول استعلامی → استعلام قیمت روز', false !== strpos( $html_inq, 'استعلام قیمت روز' ) );
	es_check( 'SC-02: حالت استعلامی دکمه سبد ندارد', false === strpos( $html_inq, 'data-pdp-add-to-cart="true"' ) );

	// ── AC-03: OUT OF STOCK (online but outofstock) ───────────────────────.
	$oos = $mkprod( 'محصول ناموجود', 'oos-prod' );
	update_post_meta( $oos, 'true_sale', 'true' );
	update_post_meta( $oos, '_price', '100000' );
	update_post_meta( $oos, '_stock_status', 'outofstock' );
	$html_oos = $render( $oos );
	es_check( 'AC-03: ناموجود → پیام ناموجود', false !== strpos( $html_oos, 'ناموجود' ) );
	es_check( 'SC-02: ناموجود دکمه سبد ندارد', false === strpos( $html_oos, 'data-pdp-add-to-cart="true"' ) );

	// ── AC-03: online but unpriced -> inquiry, not buy ────────────────────.
	$nop = $mkprod( 'آنلاین بدون قیمت', 'no-price' );
	update_post_meta( $nop, 'true_sale', 'true' );
	update_post_meta( $nop, '_stock_status', 'instock' );
	$html_nop = $render( $nop );
	es_check( 'AC-03: آنلاین بدون قیمت → استعلام (نه خرید)', false === strpos( $html_nop, 'data-pdp-add-to-cart="true"' ) && false !== strpos( $html_nop, 'استعلام قیمت روز' ) );

	// ── Inner hooks fire ──────────────────────────────────────────────────.
	add_action( 'es_product_summary_mid', static function () { echo '<!--ES-MID-->'; } );
	add_action( 'es_product_summary_after', static function () { echo '<!--ES-AFTER-->'; } );
	$html_hook = $render( $on );
	es_check( 'هوک es_product_summary_mid اجرا می‌شود', false !== strpos( $html_hook, '<!--ES-MID-->' ) );
	es_check( 'هوک es_product_summary_after اجرا می‌شود', false !== strpos( $html_hook, '<!--ES-AFTER-->' ) );
	remove_all_actions( 'es_product_summary_mid' );
	remove_all_actions( 'es_product_summary_after' );

	// ── Wiring: summary hook renders purchase card ────────────────────────.
	$GLOBALS['post'] = get_post( $on );
	setup_postdata( $GLOBALS['post'] );
	ob_start();
	do_action( 'woocommerce_single_product_summary' );
	$hooked = (string) ob_get_clean();
	es_check( 'wiring: هوک summary کارت خرید را رندر می‌کند', false !== strpos( $hooked, 'data-pdp-add-to-cart="true"' ) );

	foreach ( array( $on, $inq, $oos, $nop, $att ) as $x ) {
		wp_delete_post( $x, true );
	}
	wp_delete_term( (int) $cat['term_id'], 'product_cat' );
	wp_reset_postdata();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
