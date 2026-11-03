<?php
/**
 * P01-T004 — چرخه عمر و Theme Supports
 * Run: php temp/phase-01-core-and-assets/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T004', 'چرخه عمر و Theme Supports' );

es_check( 'target: inc/Core/Theme.php', es_file( 'inc/Core/Theme.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	es_check( 'AC-01: title-tag', current_theme_supports( 'title-tag' ) );
	es_check( 'AC-01: post-thumbnails', current_theme_supports( 'post-thumbnails' ) );
	es_check( 'AC-01: woocommerce', current_theme_supports( 'woocommerce' ) );
	es_check( 'AC-01: html5', current_theme_supports( 'html5' ) );

	global $content_width;
	es_check( 'AC-02: content_width تنظیم شده', isset( $content_width ) && 1300 === (int) $content_width );

	$theme = \ErfanSanat\Theme\Core\Theme::instance();
	es_check( 'AC-03: setup اجرا شده', $theme && $theme->has_setup_run() );

	// Switch away and back; no fatal/notice should occur.
	$previous = get_option( 'stylesheet' );
	switch_theme( 'twentytwentyfive' );
	switch_theme( 'erfansanat' );
	es_check( 'SC-02: رفت/برگشت پوسته بدون خطا', get_option( 'stylesheet' ) === 'erfansanat' );
	es_check( 'SC-01: supports پس از فعال‌سازی باقی است', current_theme_supports( 'title-tag' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
