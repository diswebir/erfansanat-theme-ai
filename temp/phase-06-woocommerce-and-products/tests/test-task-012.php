<?php
/**
 * P06-T012 — پروژه‌های مرتبط محصول
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T012', 'پروژه‌های مرتبط' );

es_check( 'target: related-projects.php', es_file( 'template-parts/product/related-projects.php' ) );
es_check( 'AC-03: از کارت استاندارد پروژه استفاده می‌کند', es_contains( 'template-parts/product/related-projects.php', "cards/card', 'project'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		$product = wc_get_product( $pid );
		ob_start();
		include ES_THEME . '/template-parts/product/related-projects.php';
		return (string) ob_get_clean();
	};
	$mkproj = static function ( $title, $slug, $status = 'publish' ) {
		return (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => $status, 'post_title' => $title, 'post_name' => $slug ) );
	};
	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'درخت نوری دلسا', 'post_name' => 'relproj-prod' ) );
	$p1  = $mkproj( 'پروژه آرتام', 'artam-tree' );
	$p2  = $mkproj( 'پروژه بیرجند', 'birjand-trees' );
	$pd  = $mkproj( 'پروژه پیش‌نویس', 'draft-proj', 'draft' );

	es_check( 'AC-01: بدون رابطه → سکشن نمی‌سازد', '' === trim( $render( $pid ) ) );

	update_post_meta( $pid, 'project-ids', json_encode( array( $p2, $p1, $pd, 999999 ), JSON_UNESCAPED_UNICODE ) );
	$html = $render( $pid );

	es_check( 'SC-01: سکشن پروژه‌های مرتبط رندر می‌شود', false !== strpos( $html, 'data-product-related-projects' ) && false !== strpos( $html, 'پروژه‌های اجراشده با درخت نوری دلسا' ) );
	es_check( 'AC-01: فقط پروژه‌های منتشرشده (۲ کارت)', 2 === substr_count( $html, 'data-project-item="true"' ) );
	es_check( 'SC-02: پروژه پیش‌نویس/حذف‌شده رندر نشد', false === strpos( $html, 'پروژه پیش‌نویس' ) );
	$pos2 = strpos( $html, 'پروژه بیرجند' );
	$pos1 = strpos( $html, 'پروژه آرتام' );
	es_check( 'AC-02: ترتیب روابط حفظ شده (بیرجند قبل آرتام)', false !== $pos2 && false !== $pos1 && $pos2 < $pos1 );
	es_check( 'AC-03: کارت استاندارد پروژه (data-project-item)', false !== strpos( $html, 'data-project-search-text' ) );
	es_check( 'AC: لینک آرشیو پروژه‌ها', false !== strpos( $html, 'مشاهده همه پروژه‌ها' ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $p1, true );
	wp_delete_post( $p2, true );
	wp_delete_post( $pd, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
