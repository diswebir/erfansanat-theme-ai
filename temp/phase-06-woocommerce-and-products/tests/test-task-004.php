<?php
/**
 * P06-T004 — عنوان/زیرعنوان/انگلیسی
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T004', 'عنوان/زیرعنوان/انگلیسی' );

es_check( 'target: summary.php', es_file( 'template-parts/product/summary.php' ) );
es_check( 'AC-01: املای english_titl حفظ شده', es_contains( 'template-parts/product/summary.php', "'english_titl'" ) );
es_check( 'AC-01: کلید zireonvan خوانده می‌شود', es_contains( 'template-parts/product/summary.php', "'zireonvan'" ) );
es_check( 'AC-03: عنوان با esc_html', es_contains( 'template-parts/product/summary.php', 'esc_html( $es_title )' ) );
es_check( 'shell به summary.php واگذار کرده (وابستگی ثبت‌شده)', es_contains( 'woocommerce/content-single-product.php', "get_template_part( 'template-parts/product/summary' )" ) );

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
		include ES_THEME . '/template-parts/product/summary.php';
		return (string) ob_get_clean();
	};
	$mkprod = static function ( $title, $excerpt = '' ) {
		return (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => $title, 'post_excerpt' => $excerpt ) );
	};

	$cat = wp_insert_term( 'درخت نوری', 'product_cat', array( 'slug' => 'lt' ) );

	// ── SC-01: product WITH subtitle + English title ──────────────────────.
	$p = $mkprod( 'درخت نوری دلسا', 'سازه فولادی با نورپردازی هوشمند.' );
	wp_set_object_terms( $p, array( (int) $cat['term_id'] ), 'product_cat' );
	update_post_meta( $p, 'zireonvan', 'سازه روشنایی شهری' );
	update_post_meta( $p, 'english_titl', 'Delsa Light Tree' );
	$html = $render( $p );
	es_check( 'SC-01: نام محصول در h1', false !== strpos( $html, 'درخت نوری دلسا</h1>' ) );
	es_check( 'SC-01: زیرعنوان zireonvan رندر می‌شود', false !== strpos( $html, 'سازه روشنایی شهری' ) );
	es_check( 'SC-01: عنوان انگلیسی english_titl رندر می‌شود', false !== strpos( $html, 'Delsa Light Tree' ) );
	es_check( 'SC-01: عنوان انگلیسی dir=ltr', 1 === preg_match_all( '/dir="ltr"[^>]*>Delsa Light Tree</', $html ) );
	es_check( 'SC-01: دسته محصول به‌صورت badge', false !== strpos( $html, 'درخت نوری</span>' ) );
	es_check( 'SC-01: excerpt رندر می‌شود', false !== strpos( $html, 'سازه فولادی با نورپردازی هوشمند.' ) );

	// ── AC-02: empty subtitle / English add no DOM ────────────────────────.
	$p2 = $mkprod( 'محصول بدون زیرعنوان' );
	$html2 = $render( $p2 );
	es_check( 'AC-02: بدون زیرعنوان → DOM اضافه ندارد', false === strpos( $html2, 'text-emerald-700 lg:text-[16px]' ) );
	es_check( 'AC-02: بدون عنوان انگلیسی → dir=ltr ندارد', false === strpos( $html2, 'dir="ltr"' ) );
	es_check( 'AC-02: همچنان h1 رندر می‌شود', false !== strpos( $html2, 'محصول بدون زیرعنوان</h1>' ) );

	// ── AC-03: escaping ───────────────────────────────────────────────────.
	$p3 = $mkprod( 'محصول تست' );
	update_post_meta( $p3, 'zireonvan', '<script>alert(1)</script>' );
	update_post_meta( $p3, 'english_titl', 'A & B "Co"' );
	$html3 = $render( $p3 );
	es_check( 'AC-03: زیرعنوان escape می‌شود', false !== strpos( $html3, '&lt;script&gt;' ) && false === strpos( $html3, '<script>alert' ) );
	es_check( 'AC-03: عنوان انگلیسی escape می‌شود', false !== strpos( $html3, 'A &amp; B &quot;Co&quot;' ) );

	// ── invalid product returns early ─────────────────────────────────────.
	$page = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'برگه' ) );
	es_check( 'پست غیرمحصول خروجی نمی‌دهد', '' === trim( $render( $page ) ) );

	foreach ( array( $p, $p2, $p3, $page ) as $x ) {
		wp_delete_post( $x, true );
	}
	wp_delete_term( (int) $cat['term_id'], 'product_cat' );
	wp_reset_postdata();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
