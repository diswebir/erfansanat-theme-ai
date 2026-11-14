<?php
/**
 * P06-T005 — شناسه‌های قانونی
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T005', 'شناسه‌های قانونی' );

es_check( 'target: legal-ids.php', es_file( 'template-parts/product/legal-ids.php' ) );
es_check( 'AC-01: هر شش کلید خوانده می‌شوند', es_contains( 'template-parts/product/legal-ids.php', "'gs1_id'" ) && es_contains( 'template-parts/product/legal-ids.php', "'national_code'" ) && es_contains( 'template-parts/product/legal-ids.php', "'hs_id'" ) && es_contains( 'template-parts/product/legal-ids.php', "'isic_id'" ) && es_contains( 'template-parts/product/legal-ids.php', "'cpc_id'" ) && es_contains( 'template-parts/product/legal-ids.php', "'product_id'" ) );

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
		include ES_THEME . '/template-parts/product/legal-ids.php';
		return (string) ob_get_clean();
	};
	$mkprod = static function ( $title ) {
		return (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => $title ) );
	};

	// ── SC-01 + AC-01: all six identifiers ────────────────────────────────.
	$p = $mkprod( 'درخت نوری دلسا' );
	update_post_meta( $p, '_sku', 'ESE-TR-DELSA' );
	update_post_meta( $p, 'product_id', 'ESE-TR-DELSA' );
	update_post_meta( $p, 'gs1_id', '6260491807210' );
	update_post_meta( $p, 'national_code', '2901140800301' );
	update_post_meta( $p, 'hs_id', '94054290' );
	update_post_meta( $p, 'isic_id', '2740' );
	update_post_meta( $p, 'cpc_id', '46539' );
	$html = $render( $p );
	es_check( 'SC-01: سکشن شناسه‌ها رندر می‌شود', false !== strpos( $html, 'شناسه‌های رسمی کالا' ) );
	es_check( 'AC-01: شش کارت شناسه', 6 === preg_match_all( '/rounded-xl border border-emerald-100 bg-white p-3\.5/', $html ) );
	es_check( 'AC-01: برچسب GTIN', false !== strpos( $html, 'کد بین‌الملل GS1 (GTIN)' ) );
	es_check( 'AC-01: برچسب ایران‌کد', false !== strpos( $html, 'کد ملی کالا (ایران‌کد)' ) );
	es_check( 'AC-01: برچسب HS Code', false !== strpos( $html, 'کد تعرفه هماهنگ گمرکی (HS Code)' ) );
	es_check( 'AC-01: برچسب ISIC', false !== strpos( $html, 'کد آیسیک صنعتی (ISIC)' ) );
	es_check( 'AC-01: برچسب CPC', false !== strpos( $html, 'کد طبقه‌بندی مرکزی محصول (CPC)' ) );
	es_check( 'AC-01: برچسب شناسه فنی کارخانه', false !== strpos( $html, 'شناسه فنی محصول در کارخانه' ) );
	es_check( 'AC-01: مقدار GTIN درست', false !== strpos( $html, '6260491807210' ) );
	es_check( 'AC-01: مقادیر dir=ltr و mono', 6 === preg_match_all( '/dir="ltr" class="mt-1 block font-mono/', $html ) );

	// ── AC-03: leading zeros preserved ────────────────────────────────────.
	update_post_meta( $p, 'gs1_id', '06260491807210' );
	update_post_meta( $p, 'hs_id', '094054290' );
	$html_z = $render( $p );
	es_check( 'AC-03: صفر پیشرو GTIN حفظ می‌شود', false !== strpos( $html_z, '>06260491807210<' ) );
	es_check( 'AC-03: صفر پیشرو HS حفظ می‌شود', false !== strpos( $html_z, '>094054290<' ) );

	// ── AC-02: empty value -> no card ─────────────────────────────────────.
	$p2 = $mkprod( 'محصول با شناسه ناقص' );
	update_post_meta( $p2, 'gs1_id', '6260491807210' );
	update_post_meta( $p2, 'isic_id', '2740' );
	$html2 = $render( $p2 );
	es_check( 'AC-02: فقط دو کارت برای دو مقدار غیرخالی', 2 === preg_match_all( '/rounded-xl border border-emerald-100 bg-white p-3\.5/', $html2 ) );
	es_check( 'AC-02: برچسب خالی رندر نمی‌شود (CPC)', false === strpos( $html2, 'کد طبقه‌بندی مرکزی محصول' ) );

	// ── SC-02: no identifiers -> no empty section ─────────────────────────.
	$p3 = $mkprod( 'بدون شناسه' );
	$html3 = $render( $p3 );
	es_check( 'SC-02: بدون شناسه → سکشن خالی نمی‌دهد', '' === trim( $html3 ) );

	// ── product_id falls back to SKU ──────────────────────────────────────.
	$p4 = $mkprod( 'شناسه فنی از SKU' );
	update_post_meta( $p4, '_sku', 'ESE-FALLBACK' );
	update_post_meta( $p4, 'gs1_id', '123' );
	$html4 = $render( $p4 );
	es_check( 'product_id خالی → fallback به SKU', false !== strpos( $html4, 'ESE-FALLBACK' ) && false !== strpos( $html4, 'شناسه فنی محصول در کارخانه' ) );

	// ── Wiring: specs panel hook renders legal-ids ────────────────────────.
	$GLOBALS['post'] = get_post( $p );
	setup_postdata( $GLOBALS['post'] );
	ob_start();
	do_action( 'es_product_specs_panel' );
	$hooked = (string) ob_get_clean();
	es_check( 'wiring: هوک es_product_specs_panel شناسه‌ها را رندر می‌کند', false !== strpos( $hooked, 'شناسه‌های رسمی کالا' ) );

	foreach ( array( $p, $p2, $p3, $p4 ) as $x ) {
		wp_delete_post( $x, true );
	}
	wp_reset_postdata();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
