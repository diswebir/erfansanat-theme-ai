<?php
/**
 * P06-T010 — جدول مشخصات فنی از attributeهای واقعی
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\AttributeMap;

es_task( 'P06-T010', 'جدول مشخصات فنی' );

es_check( 'target: specs-table.php', es_file( 'template-parts/product/specs-table.php' ) );
es_check( 'AC: از AttributeMap استفاده می‌کند', es_contains( 'template-parts/product/specs-table.php', 'AttributeMap::all()' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';
	AttributeMap::register_missing();

	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		$product = wc_get_product( $pid );
		ob_start();
		include ES_THEME . '/template-parts/product/specs-table.php';
		return (string) ob_get_clean();
	};
	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'درخت نوری دلسا', 'post_name' => 'specs-prod' ) );

	es_check( 'SC-02: بدون ویژگی → جدول نمی‌سازد', '' === trim( $render( $pid ) ) );

	// Assign a subset (incl. colour) — order/labels must follow the map.
	$green = es_insert_term( 'سبز', 'pa_color', array( 'slug' => 'green' ) );
	$red   = es_insert_term( 'قرمز', 'pa_color', array( 'slug' => 'red' ) );
	wp_set_object_terms( $pid, array( (int) $green['term_id'], (int) $red['term_id'] ), 'pa_color' );
	$ip = es_insert_term( 'IP67', 'pa_ip-rating', array( 'slug' => 'ip67' ) );
	wp_set_object_terms( $pid, array( (int) $ip['term_id'] ), 'pa_ip-rating' );
	$watt = es_insert_term( '۲۴ وات', 'pa_wattage', array( 'slug' => '24w' ) );
	wp_set_object_terms( $pid, array( (int) $watt['term_id'] ), 'pa_wattage' );

	$html = $render( $pid );
	es_check( 'AC-01: فقط ویژگی‌های دارای ترم ردیف می‌سازند', 3 === substr_count( $html, 'text-xs font-bold text-slate-500' ) );
	es_check( 'AC-03: عنوان فارسی جدول + نام محصول', false !== strpos( $html, 'جدول کامل مشخصات فنی و مهندسی درخت نوری دلسا' ) );
	$pos_color = strpos( $html, 'رنگ نور' );
	$pos_ip    = strpos( $html, 'درجه حفاظت IP' );
	$pos_watt  = strpos( $html, 'توان مصرفی وات' );
	es_check( 'AC-03: ترتیب نقشه حفظ شده (color < ip < watt)', false !== $pos_color && false !== $pos_ip && false !== $pos_watt && $pos_color < $pos_ip && $pos_ip < $pos_watt );
	es_check( 'AC-02: pa_color سواچ رنگی دارد', false !== strpos( $html, 'bg-emerald-500' ) && false !== strpos( $html, 'bg-red-500' ) && false !== strpos( $html, 'rounded-full' ) );
	es_check( 'AC-02: نام رنگ‌ها نمایش داده شد', false !== strpos( $html, 'سبز' ) && false !== strpos( $html, 'قرمز' ) );
	es_check( 'SC-02: ویژگی خالی (مثلاً برند) ردیف نساخت', false === strpos( $html, '>برند<' ) );

	// SC-01: all 18 attributes present → 18 rows.
	$all_pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'همه ویژگی‌ها', 'post_name' => 'specs-all' ) );
	foreach ( array_keys( AttributeMap::all() ) as $tax ) {
		$t = es_insert_term( 'مقدار ' . $tax, $tax, array( 'slug' => 'val-' . $tax ) );
		if ( ! is_wp_error( $t ) ) {
			wp_set_object_terms( $all_pid, array( (int) $t['term_id'] ), $tax );
		}
	}
	$html_all = $render( $all_pid );
	es_check( 'SC-01: هر ۱۸ ویژگی ردیف می‌سازند', 18 === substr_count( $html_all, 'text-xs font-bold text-slate-500' ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $all_pid, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
