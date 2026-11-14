<?php
/**
 * P01-T011 — AssetManager: enqueue شرطی
 * Run: php temp/phase-01-core-and-assets/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T011', 'AssetManager: enqueue شرطی' );

es_check( 'target: inc/Core/AssetManager.php', es_file( 'inc/Core/AssetManager.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$profiles = \ErfanSanat\Theme\Core\AssetManager::profiles();
	foreach ( array( 'home', 'single', 'archive', 'checkout', 'account', 'admin' ) as $key ) {
		es_check( "AC-01: پروفایل {$key} وجود دارد", array_key_exists( $key, $profiles ) );
	}
	es_check( 'AC-03: پروفایل admin خالی است', array() === $profiles['admin'] );

	// SC-01: home profile enqueues the shared design-system assets.
	\ErfanSanat\Theme\Core\AssetManager::enqueue_for_profile( 'home' );
	es_check( 'SC-01: home استایل را می‌گیرد', wp_style_is( 'es-styles', 'enqueued' ) );
	es_check( 'SC-01/AC-02: home main.js را می‌گیرد', wp_script_is( 'es-main', 'enqueued' ) );

	// SC-02: single profile also receives the interaction script.
	wp_dequeue_style( 'es-styles' );
	wp_dequeue_script( 'es-main' );
	\ErfanSanat\Theme\Core\AssetManager::enqueue_for_profile( 'single' );
	es_check( 'SC-02: single asset محصول را می‌گیرد', wp_style_is( 'es-styles', 'enqueued' ) && wp_script_is( 'es-main', 'enqueued' ) );

	// SC-03: checkout keeps WooCommerce scripts.
	wp_enqueue_script( 'wc-checkout-test', 'https://example.com/wc-checkout.js', array(), '1', true );
	\ErfanSanat\Theme\Core\AssetManager::enqueue_for_profile( 'checkout' );
	es_check( 'SC-03: checkout اسکریپت Woo را از دست نمی‌دهد', wp_script_is( 'wc-checkout-test', 'enqueued' ) );

	// Admin must not leak front-end assets.
	wp_dequeue_style( 'es-styles' );
	wp_dequeue_script( 'es-main' );
	\ErfanSanat\Theme\Core\AssetManager::enqueue_for_profile( 'admin' );
	es_check( 'AC-03: admin asset فرانت‌اند نمی‌گیرد', ! wp_style_is( 'es-styles', 'enqueued' ) && ! wp_script_is( 'es-main', 'enqueued' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
