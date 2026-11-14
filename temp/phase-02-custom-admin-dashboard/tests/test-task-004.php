<?php
/**
 * P02-T004 — پوسته UI تب‌بندی RTL
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ThemeOptions;
use ErfanSanat\Theme\Core\AssetManager;
use ErfanSanat\Theme\Options\Schema;

es_task( 'P02-T004', 'پوسته UI تب‌بندی RTL' );

es_check( 'target: inc/Admin/ThemeOptions.php', es_file( 'inc/Admin/ThemeOptions.php' ) );
es_check( 'target: assets/admin/options.css', es_file( 'assets/admin/options.css' ) );
es_check( 'target: assets/admin/options.js', es_file( 'assets/admin/options.js' ) );

$css = (string) file_get_contents( ES_THEME . '/assets/admin/options.css' );
$js  = (string) file_get_contents( ES_THEME . '/assets/admin/options.js' );

// AC-01: RTL + Persian font.
es_check( 'AC-01: CSS جهت RTL دارد', false !== strpos( $css, 'direction: rtl' ) );
es_check( 'AC-01: CSS فونت وزیرمتن دارد', false !== strpos( $css, 'Vazirmatn' ) );

// AC-02: keyboard + ARIA in JS.
foreach ( array( 'ArrowDown', 'ArrowUp', 'ArrowLeft', 'ArrowRight', 'Home', 'End' ) as $key ) {
	es_check( "AC-02: JS کلید {$key} را پشتیبانی می‌کند", false !== strpos( $js, '"' . $key . '"' ) );
}
es_check( 'AC-02/SC-02: JS aria-selected را به‌روز می‌کند', false !== strpos( $js, 'aria-selected' ) );
es_check( 'SC-01: JS تب را بدون reload عوض می‌کند', false !== strpos( $js, 'preventDefault' ) && false !== strpos( $js, 'hidden' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	wp_set_current_user( 1 );

	ob_start();
	ThemeOptions::render_dashboard();
	$html = ob_get_clean();

	es_check( 'AC-01: پوسته dir=rtl دارد', false !== strpos( $html, 'dir="rtl"' ) );
	es_check( 'AC-02: tablist با role درست', false !== strpos( $html, 'role="tablist"' ) );

	$tab_count   = preg_match_all( '/role="tab"/', $html );
	$panel_count = preg_match_all( '/role="tabpanel"/', $html );
	$expected    = count( Schema::tabs() );
	es_check( 'AC-02: تعداد تب‌ها برابر schema است', $tab_count === $expected );
	es_check( 'AC-02: تعداد پنل‌ها برابر تب‌هاست', $panel_count === $expected );
	es_check( 'AC-02: هر تب aria-controls دارد', substr_count( $html, 'aria-controls="es-panel-' ) === $expected );
	es_check( 'AC-02: هر پنل aria-labelledby دارد', substr_count( $html, 'aria-labelledby="es-tab-' ) === $expected );
	es_check( 'SC-02: فقط یک تب initially selected است', 1 === preg_match_all( '/aria-selected="true"/', $html ) );

	// AC-03: admin assets only on the dashboard hook, never front-end.
	ThemeOptions::enqueue_assets( 'toplevel_page_' . ThemeOptions::SLUG );
	es_check( 'AC-03: CSS ادمین در صفحه داشبورد enqueue می‌شود', wp_style_is( 'es-admin-options', 'enqueued' ) );
	es_check( 'AC-03: JS ادمین در صفحه داشبورد enqueue می‌شود', wp_script_is( 'es-admin-options', 'enqueued' ) );

	wp_dequeue_style( 'es-admin-options' );
	wp_dequeue_script( 'es-admin-options' );
	ThemeOptions::enqueue_assets( 'edit.php' );
	es_check( 'AC-03: در صفحه غیرداشبورد enqueue نمی‌شود', ! wp_style_is( 'es-admin-options', 'enqueued' ) && ! wp_script_is( 'es-admin-options', 'enqueued' ) );

	$frontend_handles = array();
	foreach ( AssetManager::profiles() as $handles ) {
		$frontend_handles = array_merge( $frontend_handles, $handles );
	}
	es_check( 'AC-03: asset ادمین در پروفایل فرانت‌اند نیست', ! in_array( 'es-admin-options', $frontend_handles, true ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
