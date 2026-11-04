<?php
/**
 * P06-T001 — Shim سازگاری ووکامرس برای QA
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T001', 'Shim سازگاری ووکامرس برای QA' );

$shim_rel = ES_ROOT . '/temp/tests/woocommerce-shim.php';
es_check( 'target: woocommerce-shim.php ساخته شده', is_file( $shim_rel ) );
es_check( 'AC-01: گارد فقط-QA (ES_ROOT)', es_contains( $shim_rel, "defined( 'ES_ROOT' )" ) );
es_check( 'AC-03: تشخیص Woo واقعی (WC_VERSION/class)', es_contains( $shim_rel, "defined( 'WC_VERSION' ) || class_exists( 'WooCommerce' )" ) );
es_check( 'AC-03: بوت مشروط به es_wc_shim_should_load', es_contains( $shim_rel, 'if ( ! es_wc_shim_should_load() )' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require $shim_rel;

	// ── SC-01: shim loads in QA without real Woo ──────────────────────────.
	es_check( 'SC-01: shim در QA بارگذاری شد', defined( 'ES_WC_SHIM_LOADED' ) );
	es_check( 'AC-02: CPT محصول ثبت شد', post_type_exists( 'product' ) );
	es_check( 'AC-02: تکسونومی product_cat', taxonomy_exists( 'product_cat' ) );
	es_check( 'AC-02: تکسونومی product_tag', taxonomy_exists( 'product_tag' ) );
	es_check( 'AC-02: تکسونومی product_brand', taxonomy_exists( 'product_brand' ) );
	es_check( 'AC-02: تابع wc_get_product', function_exists( 'wc_get_product' ) );
	es_check( 'AC-02: تابع wc_price', function_exists( 'wc_price' ) );
	es_check( 'AC-02: تابع WC()', function_exists( 'WC' ) );

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) { unset( $_POST[ $k ] ); }
	}

	// ── SC-01: product test runs without real Woo ─────────────────────────.
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'p', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/p.jpg' );
	$pid = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول شیم تست' ) );
	set_post_thumbnail( $pid, $att );
	update_post_meta( $pid, '_regular_price', '200000' );
	update_post_meta( $pid, '_sale_price', '150000' );
	update_post_meta( $pid, '_price', '150000' );
	update_post_meta( $pid, '_stock_status', 'instock' );
	update_post_meta( $pid, '_sku', 'ESE-TEST-001' );

	$product = wc_get_product( $pid );
	es_check( 'SC-01: wc_get_product شیء محصول می‌دهد', $product instanceof ES_Shim_Product );
	es_check( 'AC-02: نام محصول', $product && 'محصول شیم تست' === $product->get_name() );
	es_check( 'AC-02: قیمت فروش', $product && '150000' === $product->get_price() );
	es_check( 'AC-02: قیمت اصلی', $product && '200000' === $product->get_regular_price() );
	es_check( 'AC-02: تشخیص حراج', $product && $product->is_on_sale() );
	es_check( 'AC-02: موجودی انبار', $product && $product->is_in_stock() );
	es_check( 'AC-02: SKU', $product && 'ESE-TEST-001' === $product->get_sku() );
	es_check( 'AC-02: price_html شامل تومان', $product && false !== strpos( $product->get_price_html(), 'تومان' ) );
	es_check( 'AC-02: wc_get_product برای غیرمحصول false', false === wc_get_product( $att ) );
	es_check( 'AC-02: wc_price قالب‌بندی تومان', false !== strpos( wc_price( 150000 ), 'تومان' ) && false !== strpos( wc_price( 150000 ), '150,000' ) );

	// ── AC-02: basic cart ─────────────────────────────────────────────────.
	WC()->cart->empty_cart();
	$key = WC()->cart->add_to_cart( $pid, 2 );
	es_check( 'AC-02: افزودن به سبد کلید می‌دهد', '' !== $key );
	es_check( 'AC-02: شمارش سبد = ۲', 2 === WC()->cart->get_cart_contents_count() );
	es_check( 'AC-02: جمع سبد (۲×۱۵۰۰۰۰)', false !== strpos( WC()->cart->get_cart_total(), '300,000' ) );
	$items = WC()->cart->get_cart();
	es_check( 'AC-02: آیتم سبد data محصول دارد', isset( $items[ $key ]['data'] ) && $items[ $key ]['data'] instanceof ES_Shim_Product );
	es_check( 'AC-02: subtotal ردیف', false !== strpos( WC()->cart->get_product_subtotal( $product, 2 ), '300,000' ) );

	// ── SC-01: theme cart modal renders with the shim cart ────────────────.
	ob_start();
	get_template_part( 'template-parts/header/cart-modal' );
	$modal = (string) ob_get_clean();
	es_check( 'SC-01: مودال سبد با shim رندر می‌شود', false !== strpos( $modal, 'محصول شیم تست' ) );

	// ── wc_get_page_permalink fallback ────────────────────────────────────.
	es_check( 'AC-02: لینک shop (fallback)', false !== strpos( wc_get_page_permalink( 'shop' ), '/shop/' ) );

	// ── SC-02: with real Woo present the shim would stay inactive ─────────.
	es_check( 'SC-02: قبل از Woo واقعی should_load=true', es_wc_shim_should_load() );
	eval( 'class WooCommerce {}' ); // simulate real WooCommerce presence.
	es_check( 'SC-02: تشخیص Woo واقعی پس از حضور', es_wc_shim_real_woo_present() );
	es_check( 'SC-02: با Woo واقعی should_load=false (shim غیرفعال می‌ماند)', ! es_wc_shim_should_load() );

	WC()->cart->empty_cart();
	wp_delete_post( $pid, true );
	wp_delete_post( $att, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
