<?php
/**
 * P01-T007 — DeBloat: head و ایموجی
 * Run: php temp/phase-01-core-and-assets/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T007', 'DeBloat: head و ایموجی' );

es_check( 'target: inc/Core/DeBloat.php', es_file( 'inc/Core/DeBloat.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	\ErfanSanat\Theme\Core\DeBloat::clean_head();

	es_check( 'AC-01: emoji script حذف شد', ! has_action( 'wp_head', 'print_emoji_detection_script' ) );
	es_check( 'AC-01: emoji style حذف شد', ! has_action( 'wp_print_styles', 'print_emoji_styles' ) );
	es_check( 'AC-02: generator حذف شد', ! has_action( 'wp_head', 'wp_generator' ) );
	es_check( 'AC-02: rsd حذف شد', ! has_action( 'wp_head', 'rsd_link' ) );
	es_check( 'AC-02: wlwmanifest حذف شد', ! has_action( 'wp_head', 'wlwmanifest_link' ) );
	es_check( 'AC-02: shortlink حذف شد', ! has_action( 'wp_head', 'wp_shortlink_wp_head' ) );
	es_check( 'AC-03: feed_links حفظ شد', false !== has_action( 'wp_head', 'feed_links' ) );

	ob_start();
	do_action( 'wp_head' );
	$head = ob_get_clean();
	es_check( 'SC-01: wp_head بدون emoji است', false === strpos( $head, 'wp-emoji' ) );
	es_check( 'SC-01: wp_head بدون generator است', false === strpos( $head, 'name="generator"' ) );
	es_check( 'SC-02: فید/سئو سالم است', false !== has_action( 'wp_head', 'feed_links_extra' ) || false !== has_action( 'wp_head', 'feed_links' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
