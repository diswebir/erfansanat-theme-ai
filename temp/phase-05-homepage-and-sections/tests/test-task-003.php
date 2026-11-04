<?php
/**
 * P05-T003 — کارت استاندارد محصول
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\AttributeMap;

es_task( 'P05-T003', 'کارت استاندارد محصول' );

es_check( 'target: cards/card-product.php', es_file( 'template-parts/cards/card-product.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}
	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		register_taxonomy( 'product_cat', 'product', array( 'public' => true, 'hierarchical' => true ) );
	}
	AttributeMap::register_missing();

	$render = static function ( $product_id ) {
		ob_start();
		get_template_part( 'template-parts/cards/card-product', null, array( 'product_id' => $product_id ) );
		return (string) ob_get_clean();
	};
	$attr = static function ( $html, $name ) {
		return preg_match( '/data-product-' . $name . '="([^"]*)"/', $html, $m ) ? $m[1] : null;
	};

	// ── SC-01: online product ─────────────────────────────────────────────.
	$pid  = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'پوینت لایت پیکسل ۴ سانتی فول‌کالر', 'post_excerpt' => 'پیکسل فول‌کالر با گواهی IP68 برای تونل‌های نوری' ) );
	$cat  = wp_insert_term( 'پوینت لایت پیکسل LED', 'product_cat' );
	wp_set_object_terms( $pid, array( (int) $cat['term_id'] ), 'product_cat' );
	$ip   = wp_insert_term( 'IP68', 'pa_ip-rating' );
	$volt = wp_insert_term( '12V/24V DC', 'pa_input-voltage' );
	wp_set_object_terms( $pid, array( (int) $ip['term_id'] ), 'pa_ip-rating' );
	wp_set_object_terms( $pid, array( (int) $volt['term_id'] ), 'pa_input-voltage' );
	update_post_meta( $pid, 'true_sale', 'true' );
	update_post_meta( $pid, '_price', '98000' );
	update_post_meta( $pid, '_regular_price', '112000' );
	update_post_meta( $pid, 'number2-viewc', 3840 );
	update_post_meta( $pid, '_wc_average_rating', '4.95' );
	update_post_meta( $pid, 'product_id', 'ESE-PLP-040' );
	update_post_meta( $pid, '_min_qty', '50' );
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'img', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/pixel.jpg' );
	set_post_thumbnail( $pid, $att );

	$html = $render( $pid );
	es_check( 'SC-01: محصول آنلاین رندر می‌شود', false !== strpos( $html, 'data-product-item="true"' ) && false !== strpos( $html, 'پوینت لایت پیکسل ۴ سانتی' ) );
	foreach ( array( 'item', 'category', 'categories', 'purchasable', 'price', 'ip', 'voltage', 'views', 'rating', 'search-text' ) as $a ) {
		es_check( 'AC-01: data-product-' . $a . ' حاضر است', false !== strpos( $html, 'data-product-' . $a . '=' ) );
	}
	es_check( 'AC-02: true_sale=true → purchasable=true', 'true' === $attr( $html, 'purchasable' ) );
	es_check( 'SC-02: data-product-price = قیمت واقعی', '98000' === $attr( $html, 'price' ) );
	es_check( 'AC-03: دسته واقعی', 'پوینت لایت پیکسل LED' === $attr( $html, 'category' ) );
	es_check( 'AC-03: IP از attribute واقعی', 'IP68' === $attr( $html, 'ip' ) );
	es_check( 'AC-03: ولتاژ از attribute واقعی', '12V/24V DC' === $attr( $html, 'voltage' ) );
	es_check( 'AC-03: تصویر از رسانه', false !== strpos( $html, 'pixel.jpg' ) );
	es_check( 'AC-03: لینک = permalink', false !== strpos( $html, 'href="' . esc_url( get_permalink( $pid ) ) . '"' ) );
	es_check( 'نمایش خرید آنلاین', false !== strpos( $html, '🛒 خرید آنلاین' ) );
	es_check( 'نمایش قیمت + تخفیف', false !== strpos( $html, '۹۸,۰۰۰ تومان' ) && false !== strpos( $html, '۱۱۲,۰۰۰ تومان' ) && false !== strpos( $html, 'line-through' ) );
	es_check( 'نمایش حداقل تعداد', false !== strpos( $html, 'حداقل: ۵۰ عدد' ) );
	es_check( 'SKU نمایش داده می‌شود', false !== strpos( $html, 'ESE-PLP-040' ) );

	// ── SC-01: inquiry product ────────────────────────────────────────────.
	$inq = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'درخت نوری دلسا' ) );
	update_post_meta( $inq, 'true_sale', 'false' );
	update_post_meta( $inq, '_price', '0' );
	$ip2 = wp_insert_term( 'IP67', 'pa_ip-rating' );
	wp_set_object_terms( $inq, array( (int) $ip2['term_id'] ), 'pa_ip-rating' );
	$html = $render( $inq );
	es_check( 'AC-02: true_sale=false → purchasable=false', 'false' === $attr( $html, 'purchasable' ) );
	es_check( 'SC-02: محصول استعلامی price=0', '0' === $attr( $html, 'price' ) );
	es_check( 'نمایش استعلام پروژه', false !== strpos( $html, 'استعلام پروژه' ) && false !== strpos( $html, 'تولید سفارشی / استعلام قیمت' ) );
	es_check( 'محصول استعلامی قیمت/تخفیف ندارد', false === strpos( $html, 'line-through' ) );

	// ── AC-03: bare product does not break ────────────────────────────────.
	$bare = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول بدون داده' ) );
	$html = $render( $bare );
	es_check( 'AC-03: محصول بدون داده DOM را نمی‌شکند', false !== strpos( $html, 'data-product-item="true"' ) && false !== strpos( $html, 'محصول بدون داده' ) );
	es_check( 'AC-03: پیش‌فرض استعلامی است', 'false' === $attr( $html, 'purchasable' ) );
	es_check( 'AC-03: تصویر fallback', false !== strpos( $html, 'images/light-tunnel.jpg' ) );

	// Non-product renders nothing.
	$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'برگه' ) );
	es_check( 'AC-03: نوع غیرمحصول خروجی ندارد', '' === trim( $render( $page ) ) );

	// Cleanup.
	foreach ( array( $pid, $inq, $bare, $page, $att ) as $id ) { wp_delete_post( $id, true ); }
	wp_delete_term( $cat['term_id'], 'product_cat' );
	wp_delete_term( $ip['term_id'], 'pa_ip-rating' );
	wp_delete_term( $ip2['term_id'], 'pa_ip-rating' );
	wp_delete_term( $volt['term_id'], 'pa_input-voltage' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
