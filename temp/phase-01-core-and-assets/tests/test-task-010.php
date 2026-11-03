<?php
/**
 * P01-T010 — AssetManager: ثبت و نسخه‌گذاری
 * Run: php temp/phase-01-core-and-assets/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T010', 'AssetManager: ثبت و نسخه‌گذاری' );

es_check( 'target: inc/Core/AssetManager.php', es_file( 'inc/Core/AssetManager.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	\ErfanSanat\Theme\Core\AssetManager::register_assets();

	$style = wp_styles()->query( \ErfanSanat\Theme\Core\AssetManager::STYLE_HANDLE );
	$script = wp_scripts()->query( \ErfanSanat\Theme\Core\AssetManager::SCRIPT_HANDLE );

	es_check( 'AC-01: handle استایل ثبت شده', (bool) $style );
	es_check( 'AC-01: handle اسکریپت ثبت شده', (bool) $script );

	$css_path = ES_ASSETS_DIR . 'css/styles.css';
	$js_path  = ES_ASSETS_DIR . 'js/main.js';
	es_check( 'AC-02: نسخه استایل = filemtime', $style && (string) filemtime( $css_path ) === (string) $style->ver );
	es_check( 'AC-02: نسخه اسکریپت = filemtime', $script && (string) filemtime( $js_path ) === (string) $script->ver );
	es_check( 'AC-03: وابستگی‌ها آرایه هستند', $style && is_array( $style->deps ) && $script && is_array( $script->deps ) );

	// SC-02: duplicate registration does not change the object/version.
	$before = $style->ver;
	\ErfanSanat\Theme\Core\AssetManager::register_assets();
	$after = wp_styles()->query( \ErfanSanat\Theme\Core\AssetManager::STYLE_HANDLE )->ver;
	es_check( 'SC-02: ثبت تکراری نسخه را عوض نمی‌کند', $before === $after );

	// SC-01: touching the file changes the version on next registration.
	$original_mtime = filemtime( $css_path );
	touch( $css_path, $original_mtime + 10 );
	wp_deregister_style( \ErfanSanat\Theme\Core\AssetManager::STYLE_HANDLE );
	\ErfanSanat\Theme\Core\AssetManager::register_assets();
	$new_ver = wp_styles()->query( \ErfanSanat\Theme\Core\AssetManager::STYLE_HANDLE )->ver;
	touch( $css_path, $original_mtime );
	es_check( 'SC-01: تغییر فایل نسخه را عوض می‌کند', (string) ( $original_mtime + 10 ) === (string) $new_ver );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
