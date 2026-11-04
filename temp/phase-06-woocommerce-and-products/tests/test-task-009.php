<?php
/**
 * P06-T009 — مشخصات و ویژگی‌های کلیدی محصول
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\LegacyMeta;

es_task( 'P06-T009', 'مشخصات و ویژگی‌های کلیدی محصول' );

es_check( 'target: features.php', es_file( 'template-parts/product/features.php' ) );
es_check( 'AC-02: کلید legacy vaule_of_variable خوانده می‌شود', es_contains( 'template-parts/product/features.php', 'vaule_of_variable' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		$product = wc_get_product( $pid );
		ob_start();
		include ES_THEME . '/template-parts/product/features.php';
		return (string) ob_get_clean();
	};
	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'ویژگی‌ها', 'post_name' => 'features-prod' ) );

	es_check( 'AC-03: هر دو خالی → DOM نمی‌سازد', '' === trim( $render( $pid ) ) );

	// SC-01: JetEngine JSON item-map for both repeaters.
	$features = array(
		'item-0' => array( 'add-new-feature' => 'ولتاژ ایمن ۲۴ ولت' ),
		'item-1' => array( 'add-new-feature' => 'سازه فولادی مقاوم' ),
		'item-2' => array( 'add-new-feature' => '   ' ), // empty → skipped.
	);
	$pairs = array(
		'item-0' => array( 'add-new-feature' => 'وزن سازه', 'vaule_of_variable' => 'حدود ۴۰۰ کیلوگرم' ),
		'item-1' => array( 'add-new-feature' => 'درجه حفاظت', 'vaule_of_variable' => 'IP67' ),
		'item-2' => array( 'add-new-feature' => 'خالی', 'vaule_of_variable' => '' ), // skipped.
	);
	update_post_meta( $pid, 'product-feature', json_encode( $features, JSON_UNESCAPED_UNICODE ) );
	update_post_meta( $pid, 'product-feature2_copy', json_encode( $pairs, JSON_UNESCAPED_UNICODE ) );
	$html = $render( $pid );

	es_check( 'SC-01: لیست ویژگی‌ها رندر می‌شود', false !== strpos( $html, 'data-product-features' ) && false !== strpos( $html, 'ولتاژ ایمن ۲۴ ولت' ) && false !== strpos( $html, 'سازه فولادی مقاوم' ) );
	es_check( 'AC-03: آیتم خالی لیست ردیف نساخت', 2 === substr_count( $html, 'mt-0.5 h-4 w-4 shrink-0 text-emerald-600' ) );
	es_check( 'SC-01: جفت نام/مقدار رندر می‌شود', false !== strpos( $html, 'data-product-feature-pairs' ) && false !== strpos( $html, 'وزن سازه' ) && false !== strpos( $html, 'حدود ۴۰۰ کیلوگرم' ) && false !== strpos( $html, 'IP67' ) );
	es_check( 'AC-02: مقدار vaule_of_variable نمایش داده شد', false !== strpos( $html, 'حدود ۴۰۰ کیلوگرم' ) );
	es_check( 'AC-03: جفت با مقدار خالی ردیف نساخت', 2 === substr_count( $html, 'text-xs font-bold text-slate-500' ) );

	// AC: PHP-serialized legacy storage also works.
	update_post_meta( $pid, 'product-feature', array( array( 'add-new-feature' => 'ویژگی serialize' ) ) );
	$html_php = $render( $pid );
	es_check( 'AC: ذخیره PHP serialized خوانده می‌شود', false !== strpos( $html_php, 'ویژگی serialize' ) );

	// XSS escaped.
	update_post_meta( $pid, 'product-feature', json_encode( array( 'item-0' => array( 'add-new-feature' => '<img src=x onerror=alert(1)>' ) ), JSON_UNESCAPED_UNICODE ) );
	es_check( 'AC-01: خروجی escape می‌شود', false === strpos( $render( $pid ), '<img src=x onerror=alert(1)>' ) );

	wp_delete_post( $pid, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
