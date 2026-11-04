<?php
/**
 * P06-T017 — کارت محصول آرشیو (content-product)
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\AttributeMap;

es_task( 'P06-T017', 'کارت محصول آرشیو' );

es_check( 'target: content-product.php', es_file( 'woocommerce/content-product.php', ES_THEME ) );
es_check( 'AC: به کارت استاندارد متصل است', es_contains( 'woocommerce/content-product.php', "cards/card', 'product'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';
	AttributeMap::register_missing();

	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		$GLOBALS['product'] = wc_get_product( $pid );
		ob_start();
		include ES_THEME . '/woocommerce/content-product.php';
		return (string) ob_get_clean();
	};
	$mkprod = static function ( $title, $slug ) {
		return (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug, 'post_excerpt' => 'توضیح کوتاه محصول' ) );
	};

	$cat = es_insert_term( 'درخت نوری', 'product_cat', array( 'slug' => 'light-tree-card' ) );
	$ip  = es_insert_term( 'IP67', 'pa_ip-rating', array( 'slug' => 'ip67-card' ) );
	$vol = es_insert_term( '24V', 'pa_input-voltage', array( 'slug' => '24v-card' ) );

	// Online product.
	$on = $mkprod( 'درخت نوری دلسا', 'delsa-card' );
	wp_set_object_terms( $on, array( (int) $cat['term_id'] ), 'product_cat' );
	wp_set_object_terms( $on, array( (int) $ip['term_id'] ), 'pa_ip-rating' );
	wp_set_object_terms( $on, array( (int) $vol['term_id'] ), 'pa_input-voltage' );
	update_post_meta( $on, 'true_sale', 'true' );
	update_post_meta( $on, '_price', '1450000' );
	update_post_meta( $on, '_regular_price', '1620000' );
	update_post_meta( $on, '_sku', 'ESE-TR-DELSA' );
	update_post_meta( $on, '_wc_average_rating', '4.9' );
	update_post_meta( $on, 'number2-viewc', '321' );
	update_post_meta( $on, '_min_qty', '5' );
	$html_on = $render( $on );

	// AC-01: full data-product-* contract.
	foreach ( array( 'data-product-item', 'data-product-category', 'data-product-categories', 'data-product-purchasable', 'data-product-price', 'data-product-ip', 'data-product-voltage', 'data-product-views', 'data-product-rating', 'data-product-search-text' ) as $attr ) {
		es_check( 'AC-01: ' . $attr, false !== strpos( $html_on, $attr . '=' ) );
	}
	es_check( 'AC-02: قیمت واقعی (data-product-price=1450000)', false !== strpos( $html_on, 'data-product-price="1450000"' ) );
	es_check( 'AC-02: حالت خرید آنلاین', false !== strpos( $html_on, 'data-product-purchasable="true"' ) );
	es_check( 'AC-02: بازدید واقعی (۳۲۱)', false !== strpos( $html_on, 'data-product-views="321"' ) );
	es_check( 'AC-02: IP/ولتاژ واقعی', false !== strpos( $html_on, 'data-product-ip="IP67"' ) && false !== strpos( $html_on, 'data-product-voltage="24V"' ) );
	es_check( 'AC-03: کلاس‌های grid/hover کارت', false !== strpos( $html_on, 'card-light group flex h-full flex-col' ) && false !== strpos( $html_on, 'group-hover:scale-110' ) );
	es_check( 'SC-02: محصول آنلاین قیمت + حداقل سفارش دارد', false !== strpos( $html_on, 'حداقل:' ) && false !== strpos( $html_on, 'line-through' ) );

	// SC-01: snapshot vs reference shop card markers.
	$ref = (string) file_get_contents( ES_ROOT . '/static-mirror/shop.html' );
	$markers = array( 'card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]', 'group-hover:scale-110', 'group-hover:rotate-45', 'data-product-search-text' );
	$ok = true;
	foreach ( $markers as $m ) {
		if ( false === strpos( $ref, $m ) || false === strpos( $html_on, $m ) ) {
			$ok = false;
		}
	}
	es_check( 'SC-01: اسنپ‌شات کارت با مرجع shop.html', $ok );

	// Inquiry product.
	$inq = $mkprod( 'محصول استعلامی', 'inquiry-card' );
	wp_set_object_terms( $inq, array( (int) $cat['term_id'] ), 'product_cat' );
	update_post_meta( $inq, 'true_sale', 'false' );
	update_post_meta( $inq, '_price', '999000' );
	$html_inq = $render( $inq );
	es_check( 'SC-02: محصول استعلامی purchasable=false', false !== strpos( $html_inq, 'data-product-purchasable="false"' ) );
	es_check( 'SC-02: محصول استعلامی CTA استعلام دارد', false !== strpos( $html_inq, 'تولید سفارشی / استعلام قیمت' ) && false !== strpos( $html_inq, 'مشاوره فنی' ) );
	es_check( 'SC-02: محصول استعلامی قیمت نمایش نمی‌دهد', false === strpos( $html_inq, 'data-product-price="999000"' ) );

	// Non-product post renders nothing.
	$page = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'برگه' ) );
	es_check( 'AC: پست غیرمحصول کارت نمی‌سازد', '' === trim( $render( $page ) ) );

	foreach ( array( $on, $inq, $page ) as $p ) {
		wp_delete_post( $p, true );
	}
	wp_delete_term( (int) $cat['term_id'], 'product_cat' );
	wp_delete_term( (int) $ip['term_id'], 'pa_ip-rating' );
	wp_delete_term( (int) $vol['term_id'], 'pa_input-voltage' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
