<?php
/**
 * P07-T004 — محصولات استفاده‌شده در پروژه (used-products.php)
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T004', 'محصولات استفاده‌شده در پروژه' );

es_check( 'target: used-products.php', es_file( 'template-parts/project/used-products.php', ES_THEME ) );
es_check( 'AC-01: فقط منتشرشده/معتبر', es_contains( 'template-parts/project/used-products.php', "'publish' === get_post_status" ) && es_contains( 'template-parts/project/used-products.php', "'product' === get_post_type" ) );
es_check( 'AC-02: ترتیب رابطه (mortabet_mahsole_2)', es_contains( 'template-parts/project/used-products.php', 'get_related_products' ) );
es_check( 'AC-03: سکشن خالی حذف', es_contains( 'template-parts/project/used-products.php', 'if ( empty( $es_ids ) )' ) );
es_check( 'SC-01: کارت محصول واقعی', es_contains( 'template-parts/project/used-products.php', "get_template_part( 'template-parts/cards/card', 'product'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$pid = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه محصولات', 'post_name' => 'used-proj-' . uniqid() ) );
	$mkprod = static function ( $title, $status ) {
		$id = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => $status, 'post_title' => $title, 'post_name' => 'used-p-' . uniqid() ) );
		update_post_meta( $id, '_price', '100000' );
		update_post_meta( $id, 'true_sale', 'true' );
		return $id;
	};
	$p1 = $mkprod( 'محصول الف', 'publish' );
	$p2 = $mkprod( 'محصول ب', 'publish' );
	$p3 = $mkprod( 'محصول پیش‌نویس', 'draft' );

	\ErfanSanat\Theme\Core\LegacyMeta::set( $pid, 'mortabet_mahsole_2', array( $p1, $p2, $p3 ) );

	$render = static function ( $pid ) {
		ob_start();
		get_template_part( 'template-parts/project/used-products', null, array( 'project_id' => $pid ) );
		return (string) ob_get_clean();
	};
	$html = $render( $pid );

	// SC-01: two published products render as cards.
	es_check( 'SC-01: دو محصول استفاده‌شده نمایش داده شد', 2 === substr_count( $html, 'data-product-item="true"' ) && false !== strpos( $html, 'data-project-used-products="true"' ) );
	es_check( 'SC-01: نام محصولات چاپ شد', false !== strpos( $html, 'محصول الف' ) && false !== strpos( $html, 'محصول ب' ) );

	// AC-01: draft excluded.
	es_check( 'AC-01: محصول پیش‌نویس رندر نشد', false === strpos( $html, 'محصول پیش‌نویس' ) );

	// AC-02: order preserved (p1 before p2).
	$pos1 = strpos( $html, 'محصول الف' );
	$pos2 = strpos( $html, 'محصول ب' );
	es_check( 'AC-02: ترتیب رابطه حفظ شد', false !== $pos1 && false !== $pos2 && $pos1 < $pos2 );

	// SC-02: deleted product not rendered.
	wp_delete_post( $p2, true );
	$html2 = $render( $pid );
	es_check( 'SC-02: محصول حذف‌شده رندر نشد', 1 === substr_count( $html2, 'data-product-item="true"' ) && false === strpos( $html2, 'محصول ب' ) );

	// AC-03: empty relation → no output.
	$empty_pid = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'خالی', 'post_name' => 'used-empty-' . uniqid() ) );
	es_check( 'AC-03: رابطه خالی خروجی ندارد', '' === trim( $render( $empty_pid ) ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $empty_pid, true );
	wp_delete_post( $p1, true );
	wp_delete_post( $p3, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
