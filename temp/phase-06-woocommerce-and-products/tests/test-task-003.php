<?php
/**
 * P06-T003 — گالری محصول
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T003', 'گالری محصول' );

es_check( 'target: gallery.php', es_file( 'template-parts/product/gallery.php' ) );
es_check( 'AC-02: از get_gallery (fallbackهای legacy) استفاده می‌کند', es_contains( 'template-parts/product/gallery.php', 'ProductMetaboxes::get_gallery' ) );
es_check( 'SC-02: هوک لایت‌باکس data-gallery-lightbox', es_contains( 'template-parts/product/gallery.php', 'data-gallery-lightbox="true"' ) );
es_check( 'SC-02: هوک data-gallery-expand', es_contains( 'template-parts/product/gallery.php', 'data-gallery-expand="true"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) { unset( $_POST[ $k ] ); }
	}

	$mkimg = static function ( $name ) {
		$id = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => $name, 'post_mime_type' => 'image/jpeg' ) );
		update_post_meta( $id, '_wp_attached_file', '2026/11/' . $name . '.jpg' );
		return (int) $id;
	};
	$mkprod = static function ( $title ) {
		return (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => $title ) );
	};
	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		ob_start();
		include ES_THEME . '/template-parts/product/gallery.php';
		return (string) ob_get_clean();
	};

	$i1 = $mkimg( 'g1' );
	$i2 = $mkimg( 'g2' );
	$i3 = $mkimg( 'g3' );
	$i4 = $mkimg( 'g4' );
	$deleted = 999999; // non-existent attachment.

	// ── SC-01 + AC-01: legacy gallery (featured + galerry_produ), order kept ─.
	$p = $mkprod( 'درخت نوری دلسا' );
	set_post_thumbnail( $p, $i1 );
	update_post_meta( $p, 'galerry_produ', "$i2,$i3,$i4" );
	$html = $render( $p );
	es_check( 'SC-01: گالری legacy رندر می‌شود', false !== strpos( $html, 'data-gallery-root="true"' ) );
	es_check( 'AC-01: چهار تصویر (شاخص + ۳ گالری)', 4 === preg_match_all( '/data-gallery-thumb="\d+"/', $html ) );
	// Order: featured (g1) first, then g2,g3,g4.
	$srcs = array();
	preg_match_all( '/data-gallery-src="([^"]+)"/', $html, $mm );
	$srcs = $mm[1];
	es_check( 'AC-01: تصویر شاخص اول است', isset( $srcs[0] ) && false !== strpos( $srcs[0], 'g1.jpg' ) );
	es_check( 'AC-01: ترتیب گالری حفظ شده (g2,g3,g4)', isset( $srcs[1], $srcs[2], $srcs[3] ) && false !== strpos( $srcs[1], 'g2.jpg' ) && false !== strpos( $srcs[2], 'g3.jpg' ) && false !== strpos( $srcs[3], 'g4.jpg' ) );
	es_check( 'AC-01: تصویر اصلی = شاخص', 1 === preg_match_all( '/data-gallery-main-img="true"[^>]*>/', $html ) && false !== strpos( $html, 'g1.jpg' ) );
	es_check( 'AC-01: شمارنده ۱ / ۴', false !== strpos( $html, '۱ / ۴' ) );
	es_check( 'SC-02: دکمه‌های prev/next گالری', false !== strpos( $html, 'data-gallery-prev="true"' ) && false !== strpos( $html, 'data-gallery-next="true"' ) );
	es_check( 'SC-02: لایت‌باکس img + dots', false !== strpos( $html, 'data-gallery-lightbox-img="true"' ) && 4 === preg_match_all( '/data-gallery-lightbox-dot="\d+"/', $html ) );
	es_check( 'SC-02: هر thumb دارای data-gallery-src (کلیک → تعویض تصویر)', 4 === preg_match_all( '/data-gallery-thumb="\d+" data-gallery-src="[^"]+"/', $html ) );

	// ── AC-03: deleted image is not rendered ──────────────────────────────.
	update_post_meta( $p, 'galerry_produ', "$i2,$deleted,$i3" );
	$html_del = $render( $p );
	es_check( 'AC-03: تصویر حذف‌شده رندر نمی‌شود', 3 === preg_match_all( '/data-gallery-thumb="\d+"/', $html_del ) ); // featured + i2 + i3.

	// ── AC-02: fallback order galerry_produ wins over _product_image_gallery ─.
	$p2 = $mkprod( 'محصول fallback' );
	set_post_thumbnail( $p2, $i1 );
	update_post_meta( $p2, 'galerry_produ', "$i2" );
	update_post_meta( $p2, '_product_image_gallery', "$i3,$i4" );
	$html_fb = $render( $p2 );
	es_check( 'AC-02: galerry_produ بر _product_image_gallery اولویت دارد', 2 === preg_match_all( '/data-gallery-thumb="\d+"/', $html_fb ) && false !== strpos( $html_fb, 'g2.jpg' ) && false === strpos( $html_fb, 'g3.jpg' ) );

	// ── Fallback: no gallery -> featured only ─────────────────────────────.
	$p3 = $mkprod( 'فقط شاخص' );
	set_post_thumbnail( $p3, $i1 );
	$html_one = $render( $p3 );
	es_check( 'fallback: بدون گالری → فقط تصویر شاخص', 1 === preg_match_all( '/data-gallery-thumb="\d+"/', $html_one ) );

	// ── Fallback: no images at all -> theme image, no fatal ───────────────.
	$p4 = $mkprod( 'بدون تصویر' );
	$html_none = $render( $p4 );
	es_check( 'fallback: بدون تصویر → تصویر تم، بدون fatal', false !== strpos( $html_none, 'data-gallery-root="true"' ) && 1 === preg_match_all( '/data-gallery-thumb="\d+"/', $html_none ) );

	// ── Wiring: shell hook renders the gallery ────────────────────────────.
	$GLOBALS['post'] = get_post( $p );
	setup_postdata( $GLOBALS['post'] );
	ob_start();
	do_action( 'woocommerce_before_single_product_summary' );
	$hooked = (string) ob_get_clean();
	es_check( 'wiring: هوک woocommerce_before_single_product_summary گالری را رندر می‌کند', false !== strpos( $hooked, 'data-gallery-root="true"' ) );

	foreach ( array( $p, $p2, $p3, $p4, $i1, $i2, $i3, $i4 ) as $x ) {
		wp_delete_post( $x, true );
	}
	wp_reset_postdata();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
