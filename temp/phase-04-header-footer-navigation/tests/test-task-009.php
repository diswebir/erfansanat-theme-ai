<?php
/**
 * P04-T009 — Shell منوی موبایل Off-canvas
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T009', 'Shell منوی موبایل Off-canvas' );

es_check( 'target: template-parts/header/mobile-menu.php', es_file( 'template-parts/header/mobile-menu.php' ) );
es_check( 'target: assets/js/navigation.js', es_file( 'assets/js/navigation.js' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	ob_start();
	get_template_part( 'template-parts/header/mobile-menu' );
	$html = (string) ob_get_clean();

	// AC-01: data-mobile-sidebar + inset/y/width classes preserved.
	es_check( 'AC-01: data-mobile-sidebar حفظ شده', false !== strpos( $html, 'data-mobile-sidebar="true"' ) );
	es_check( 'AC-01: کلاس‌های inset-y-0 right-0 حفظ شده', false !== strpos( $html, 'fixed inset-y-0 right-0 z-[95]' ) );
	es_check( 'AC-01: عرض w-[88vw] max-w-[380px] حفظ شده', false !== strpos( $html, 'w-[88vw] max-w-[380px]' ) );
	es_check( 'AC-01: wrapper data-mobile-menu حفظ شده', false !== strpos( $html, 'data-mobile-menu="true"' ) );
	es_check( 'AC-01: backdrop حفظ شده', false !== strpos( $html, 'data-mobile-backdrop="true"' ) );
	es_check( 'AC-01: دکمه بستن حفظ شده', false !== strpos( $html, 'data-mobile-close="true"' ) );

	// AC-03: ARIA present.
	es_check( 'AC-03: aside aria-label دارد', false !== strpos( $html, 'aria-label="منوی موبایل"' ) );
	es_check( 'AC-03: wrapper aria-hidden دارد', false !== strpos( $html, 'aria-hidden="true"' ) );

	// AC-02: navigation.js enqueued (scroll-lock/open-close lives in main.js).
	es_check( 'AC-02: navigation.js صف شده', wp_script_is( 'es-navigation', 'enqueued' ) );

	// SC-01 / SC-02: a11y logic present in navigation.js (browser-free assertion).
	$js = (string) file_get_contents( ES_THEME . '/assets/js/navigation.js' );
	es_check( 'SC-02: Escape منو را می‌بندد', false !== strpos( $js, '"Escape"' ) && false !== strpos( $js, 'closeMenu' ) );
	es_check( 'SC-01: focus به drawer می‌رود', false !== strpos( $js, 'closeBtn.focus()' ) );
	es_check( 'AC-03: focus trap پیاده شده', false !== strpos( $js, 'e.shiftKey' ) && false !== strpos( $js, 'last.focus()' ) );

	// Drawer footer actions.
	es_check( 'دکمه سبد در drawer هست', false !== strpos( $html, 'مشاهده سبد خرید و پیش‌فاکتور' ) );
	es_check( 'لینک تماس کارخانه هست', false !== strpos( $html, 'tel:' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
