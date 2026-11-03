<?php
/**
 * P01-T006 — DeBloat: صف‌های بلوک
 * Run: php temp/phase-01-core-and-assets/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T006', 'DeBloat: صف‌های بلوک' );

es_check( 'target: inc/Core/DeBloat.php', es_file( 'inc/Core/DeBloat.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	es_check( 'AC-01: hook dequeue ثبت شده', false !== has_action( 'wp_enqueue_scripts', 'ErfanSanat\\Theme\\Core\\DeBloat::dequeue_block_assets' ) );

	// Front-end: block styles must be removed.
	wp_enqueue_style( 'wp-block-library', 'https://example.com/block.css', array(), '1' );
	wp_enqueue_style( 'global-styles', 'https://example.com/global.css', array(), '1' );
	\ErfanSanat\Theme\Core\DeBloat::dequeue_block_assets();
	es_check( 'AC-01: wp-block-library dequeue شد', ! wp_style_is( 'wp-block-library', 'enqueued' ) );
	es_check( 'AC-01: global-styles dequeue شد', ! wp_style_is( 'global-styles', 'enqueued' ) );

	// Admin: a plugin/admin style must remain untouched. Simulate wp-admin.
	if ( ! defined( 'WP_ADMIN' ) ) {
		define( 'WP_ADMIN', true );
	}
	wp_enqueue_style( 'es-test-admin-style', 'https://example.com/admin.css', array(), '1' );
	\ErfanSanat\Theme\Core\DeBloat::dequeue_block_assets();
	es_check( 'AC-03/SC-02: صف admin دست‌نخورده ماند', wp_style_is( 'es-test-admin-style', 'enqueued' ) );

	// AC-02: unrelated plugin scripts are not removed.
	wp_enqueue_script( 'es-test-plugin-script', 'https://example.com/plugin.js', array(), '1', true );
	\ErfanSanat\Theme\Core\DeBloat::dequeue_block_assets();
	es_check( 'AC-02: اسکریپت افزونه دیگر حذف نشد', wp_script_is( 'es-test-plugin-script', 'enqueued' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
