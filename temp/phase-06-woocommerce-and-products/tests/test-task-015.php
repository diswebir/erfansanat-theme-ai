<?php
/**
 * P06-T015 — محصولات مشابه و مکمل
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T015', 'محصولات مشابه' );

es_check( 'target: related-products.php', es_file( 'template-parts/product/related-products.php' ) );
es_check( 'AC-03: تعداد کارت فیلترپذیر است', es_contains( 'template-parts/product/related-products.php', "apply_filters( 'es_related_products_count'" ) );
es_check( 'AC-02: محصول جاری حذف می‌شود', es_contains( 'template-parts/product/related-products.php', 'post__not_in' ) );
es_check( 'AC: کارت استاندارد محصول', es_contains( 'template-parts/product/related-products.php', "cards/card', 'product'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		$product = wc_get_product( $pid );
		ob_start();
		include ES_THEME . '/template-parts/product/related-products.php';
		return (string) ob_get_clean();
	};
	$mkprod = static function ( $title, $slug, $status = 'publish' ) {
		return (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => $status, 'post_title' => $title, 'post_name' => $slug ) );
	};

	$cat  = es_insert_term( 'درخت نوری', 'product_cat', array( 'slug' => 'light-tree-cat' ) );
	$cat2 = es_insert_term( 'ریسه', 'product_cat', array( 'slug' => 'string-cat' ) );
	$cid  = (int) $cat['term_id'];

	$current = $mkprod( 'درخت نوری دلسا', 'delsa-current' );
	wp_set_object_terms( $current, array( $cid ), 'product_cat' );

	// No related yet (only current in category) → no section.
	es_check( 'AC: بدون مرتبط → سکشن نمی‌سازد', '' === trim( $render( $current ) ) );

	$related = array();
	for ( $i = 1; $i <= 5; $i++ ) {
		$p = $mkprod( 'محصول مرتبط ' . $i, 'related-' . $i );
		wp_set_object_terms( $p, array( $cid ), 'product_cat' );
		$related[] = $p;
	}
	$other = $mkprod( 'محصول دسته دیگر', 'other-cat-prod' );
	wp_set_object_terms( $other, array( (int) $cat2['term_id'] ), 'product_cat' );
	$draft = $mkprod( 'پیش‌نویس مرتبط', 'related-draft', 'draft' );
	wp_set_object_terms( $draft, array( $cid ), 'product_cat' );

	$html = $render( $current );
	es_check( 'SC-01: سکشن محصولات مشابه رندر می‌شود', false !== strpos( $html, 'data-product-related-products' ) && false !== strpos( $html, 'محصولات مشابه و مکمل' ) );
	es_check( 'SC-01: پیش‌فرض ۴ کارت', 4 === substr_count( $html, 'data-product-item="true"' ) );
	es_check( 'AC-02: محصول جاری در کارت‌ها نیست', false === strpos( $html, 'delsa-current' ) );
	es_check( 'AC-01: فقط هم‌دسته (دسته دیگر حذف)', false === strpos( $html, 'محصول دسته دیگر' ) );
	es_check( 'AC-01: پیش‌نویس رندر نشد', false === strpos( $html, 'پیش‌نویس مرتبط' ) );
	es_check( 'AC: لینک فروشگاه', false !== strpos( $html, 'مشاهده همه محصولات' ) );

	// AC-03: count filter.
	$cb = static function () { return 2; };
	add_filter( 'es_related_products_count', $cb );
	$html2 = $render( $current );
	remove_filter( 'es_related_products_count', $cb );
	es_check( 'AC-03: فیلتر تعداد → ۲ کارت', 2 === substr_count( $html2, 'data-product-item="true"' ) );

	foreach ( array_merge( $related, array( $current, $other, $draft ) ) as $p ) {
		wp_delete_post( $p, true );
	}
	wp_delete_term( $cid, 'product_cat' );
	wp_delete_term( (int) $cat2['term_id'], 'product_cat' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
