<?php
/**
 * P04-T004 — ردیف برند و اکشن‌های هدر
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Store;

es_task( 'P04-T004', 'ردیف برند و اکشن‌های هدر' );

es_check( 'target: template-parts/header/brand-row.php', es_file( 'template-parts/header/brand-row.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$render = static function () {
		ob_start();
		get_template_part( 'template-parts/header/brand-row' );
		return (string) ob_get_clean();
	};

	$html = $render();

	// AC-01: default monogram + brand name/tagline + home link.
	es_check( 'AC-01: مونوگرام ESE پیش‌فرض', false !== strpos( $html, '>ESE</div>' ) );
	es_check( 'AC-01: نام برند از تنظیمات', false !== strpos( $html, 'عرفان صنعت اصفهان' ) );
	es_check( 'AC-01: شعار برند از تنظیمات', false !== strpos( $html, 'نورپردازی شهری و المان‌های نوری' ) );
	es_check( 'AC-01: لینک لوگو به خانه', 1 === preg_match( '#<a href="' . preg_quote( home_url( '/' ), '#' ) . '" class="group flex shrink-0#', $html ) );

	// AC-02: preserved data attributes.
	es_check( 'AC-02: data-header-cart-badge حفظ شده', false !== strpos( $html, 'data-header-cart-badge="true"' ) );
	es_check( 'AC-02: data-mobile-toggle حفظ شده', false !== strpos( $html, 'data-mobile-toggle="true"' ) );
	es_check( 'AC-02: data-search-modal-open حفظ شده', false !== strpos( $html, 'data-search-modal-open="true"' ) );
	es_check( 'AC-02: data-cart-modal-open حفظ شده', false !== strpos( $html, 'data-cart-modal-open="true"' ) );

	// Nav container parity.
	es_check( 'nav دسکتاپ hidden lg:flex', false !== strpos( $html, '<nav aria-label="منوی اصلی" class="hidden items-center gap-1 lg:flex">' ) );
	es_check( 'دکمه استعلام قیمت حاضر است', false !== strpos( $html, 'استعلام قیمت' ) );

	// AC-03 / SC-02: badge reflects the (filterable) real cart count.
	es_check( 'AC-03: شمارنده پیش‌فرض صفر است', 1 === preg_match( '/data-header-cart-badge="true"[^>]*>0</', $html ) );
	$cb = static function () { return 5; };
	add_filter( 'es_header_cart_count', $cb );
	$html = $render();
	es_check( 'SC-02: شمارنده با تعداد آیتم برابر است', 1 === preg_match( '/data-header-cart-badge="true"[^>]*>5</', $html ) );
	remove_filter( 'es_header_cart_count', $cb );

	// SC-01: a custom logo renders an <img> with alt from settings.
	$att_id = wp_insert_attachment(
		array( 'post_title' => 'لوگوی تست', 'post_mime_type' => 'image/png', 'post_status' => 'inherit' ),
		'es-test-logo.png'
	);
	wp_update_attachment_metadata( $att_id, array( 'width' => 120, 'height' => 120, 'file' => 'es-test-logo.png' ) );
	Store::set( 'logo_id', $att_id );
	$html = $render();
	es_check( 'SC-01: لوگوی سفارشی به‌صورت img رندر می‌شود', 1 === preg_match( '/<img src="[^"]+" alt="عرفان صنعت اصفهان"/', $html ) );
	Store::delete( 'logo_id' );
	wp_delete_attachment( $att_id, true );

	// Restored default monogram.
	$html = $render();
	es_check( 'پس از حذف لوگو، مونوگرام بازمی‌گردد', false !== strpos( $html, '>ESE</div>' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
