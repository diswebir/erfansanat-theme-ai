<?php
/**
 * P06-T011 — کاربردهای محصول و ترم‌های نوع پروژه
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T011', 'کاربردهای محصول' );

es_check( 'target: usage.php', es_file( 'template-parts/product/usage.php' ) );
es_check( 'AC: کلیدهای legacy usage_product/usage_countr', es_contains( 'template-parts/product/usage.php', 'usage_product' ) && es_contains( 'template-parts/product/usage.php', 'usage_countr' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		$product = wc_get_product( $pid );
		ob_start();
		include ES_THEME . '/template-parts/product/usage.php';
		return (string) ob_get_clean();
	};
	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'کاربرد', 'post_name' => 'usage-prod' ) );

	es_check( 'AC-03: خالی → سکشن نمی‌سازد', '' === trim( $render( $pid ) ) );

	$t1 = es_insert_term( 'درخت نوری', 'project_type', array( 'slug' => 'light-tree' ) );
	$t2 = es_insert_term( 'المان‌های نورانی', 'project_type', array( 'slug' => 'luminous-elements' ) );
	$usage = array(
		'item-0' => array( 'add-new-usage' => 'پارک‌ها و بوستان‌ها' ),
		'item-1' => array( 'add-new-usage' => 'پیاده‌راه‌های شهری' ),
		'item-2' => array( 'add-new-usage' => '' ),
	);
	update_post_meta( $pid, 'usage_product', json_encode( $usage, JSON_UNESCAPED_UNICODE ) );
	update_post_meta( $pid, 'usage_countr', json_encode( array( (int) $t1['term_id'], (int) $t2['term_id'], 999999 ), JSON_UNESCAPED_UNICODE ) );
	$html = $render( $pid );

	es_check( 'SC-01: کاربردهای متنی رندر می‌شوند', false !== strpos( $html, 'data-product-usage' ) && false !== strpos( $html, 'پارک‌ها و بوستان‌ها' ) && false !== strpos( $html, 'پیاده‌راه‌های شهری' ) );
	es_check( 'AC-03: کاربرد خالی چیپ نساخت', 2 === substr_count( $html, 'shadow-2xs' ) );
	es_check( 'SC-01: لینک نوع پروژه رندر می‌شود', false !== strpos( $html, 'پروژه‌های درخت نوری' ) && false !== strpos( $html, 'light-tree' ) );
	es_check( 'AC-02: ترم فارسی لینک صحیح دارد', false !== strpos( $html, 'luminous-elements' ) && false !== strpos( $html, 'href="' ) );
	es_check( 'AC-01: ترم نامعتبر رندر نشد', 2 === substr_count( $html, 'inline-flex items-center gap-1.5 rounded-xl border border-emerald-200' ) );

	// Only term links, no usages.
	delete_post_meta( $pid, 'usage_product' );
	$html2 = $render( $pid );
	es_check( 'AC-03: فقط لینک‌ها → سکشن لینک می‌ماند', false !== strpos( $html2, 'light-tree' ) && false === strpos( $html2, 'کاربردهای اصلی در پروژه‌ها' ) );

	wp_delete_post( $pid, true );
	wp_delete_term( (int) $t1['term_id'], 'project_type' );
	wp_delete_term( (int) $t2['term_id'], 'project_type' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
