<?php
/**
 * P02-T019 — ادغام کامل وزیرمتن در پنل پیشخوان قالب
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\Typography;
use ErfanSanat\Theme\Admin\ThemeOptions;
use ErfanSanat\Theme\Core\AssetManager;

es_task( 'P02-T019', 'ادغام کامل وزیرمتن در پنل پیشخوان' );

es_check( 'target: Typography.php', es_file( 'inc/Admin/Typography.php' ) );
es_check( 'target: admin/font.css', es_file( 'assets/admin/font.css' ) );

$css = (string) file_get_contents( ES_THEME . '/assets/admin/font.css' );

// AC-01: three self-hosted subsets, variable weight, swap, unicode-range.
es_check( 'AC-01: سه @font-face وزیرمتن', 3 === substr_count( $css, '@font-face' ) );
es_check( 'AC-01: subset عربی/فارسی', false !== strpos( $css, 'vazirmatn-arabic.woff2' ) && false !== strpos( $css, 'U+0600-06FF' ) );
es_check( 'AC-01: subset latin-ext', false !== strpos( $css, 'vazirmatn-latin-ext.woff2' ) );
es_check( 'AC-01: subset latin', false !== strpos( $css, 'vazirmatn-latin.woff2' ) );
es_check( 'AC-01: وزن متغیر 100–900', 3 === substr_count( $css, 'font-weight: 100 900' ) );
es_check( 'AC-01: font-display: swap', 3 === substr_count( $css, 'font-display: swap' ) );
es_check( 'AC-01: unicode-range برای هر subset', 3 === substr_count( $css, 'unicode-range:' ) );
es_check( 'AC-01: فایل‌های فونت موجودند', es_file( 'assets/fonts/vazirmatn-arabic.woff2' ) && es_file( 'assets/fonts/vazirmatn-latin-ext.woff2' ) && es_file( 'assets/fonts/vazirmatn-latin.woff2' ) );

// AC-03: icon fonts + code preserved.
es_check( 'AC-03: حفظ dashicons', false !== strpos( $css, '.dashicons' ) && false !== strpos( $css, 'font-family: dashicons' ) );
es_check( 'AC-03: حفظ media-modal-icon', false !== strpos( $css, '.media-modal-icon' ) );
es_check( 'AC-03: فیلد کد monospace', false !== strpos( $css, 'ui-monospace' ) && false !== strpos( $css, 'textarea.es-code' ) );

// Centralised @font-face (no duplicate in options.css).
$options_css = (string) file_get_contents( ES_THEME . '/assets/admin/options.css' );
es_check( 'options.css دیگر @font-face تکراری ندارد', false === strpos( $options_css, '@font-face' ) );
es_check( 'ThemeOptions به handle فونت وابسته است', es_contains( 'inc/Admin/ThemeOptions.php', 'array( Typography::HANDLE )' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
	require_once ABSPATH . 'wp-admin/includes/screen.php';

	es_check( 'Typography بوت شده', Typography::is_booted() );

	$reset = static function () {
		wp_dequeue_style( Typography::HANDLE );
		wp_deregister_style( Typography::HANDLE );
	};

	// SC-01: dashboard screen.
	$dash_hook = 'toplevel_page_' . ThemeOptions::SLUG;
	Typography::enqueue_assets( $dash_hook );
	es_check( 'SC-01: فونت در داشبورد enqueue می‌شود', wp_style_is( Typography::HANDLE, 'enqueued' ) );
	set_current_screen( $dash_hook );
	es_check( 'SC-01: کلاس بدنه داشبورد اضافه می‌شود', false !== strpos( Typography::body_class( '' ), Typography::BODY_CLASS ) );
	$reset();

	// SC-01: product editor.
	set_current_screen( 'post.php' );
	$GLOBALS['current_screen']->post_type = 'product';
	$GLOBALS['current_screen']->base      = 'post';
	es_check( 'SC-01: ویرایش محصول صفحه قالب است', Typography::is_theme_screen( 'post.php' ) );
	Typography::enqueue_assets( 'post.php' );
	es_check( 'SC-01: فونت در ویرایشگر محصول enqueue می‌شود', wp_style_is( Typography::HANDLE, 'enqueued' ) );
	$reset();

	// SC-01: product_cat term screen.
	set_current_screen( 'edit-tags.php' );
	$GLOBALS['current_screen']->taxonomy = 'product_cat';
	$GLOBALS['current_screen']->base     = 'edit-tags';
	es_check( 'SC-01: ترم product_cat صفحه قالب است', Typography::is_theme_screen( 'edit-tags.php' ) );
	Typography::enqueue_assets( 'edit-tags.php' );
	es_check( 'SC-01: فونت در ترم product_cat enqueue می‌شود', wp_style_is( Typography::HANDLE, 'enqueued' ) );
	$reset();

	// SC-02: unrelated admin list table.
	set_current_screen( 'edit.php' );
	$GLOBALS['current_screen']->post_type = 'post';
	$GLOBALS['current_screen']->base      = 'edit';
	es_check( 'SC-02: edit.php عمومی صفحه قالب نیست', ! Typography::is_theme_screen( 'edit.php' ) );
	Typography::enqueue_assets( 'edit.php' );
	es_check( 'SC-02: فونت در edit.php عمومی enqueue نمی‌شود', ! wp_style_is( Typography::HANDLE, 'enqueued' ) );
	es_check( 'SC-02: کلاس بدنه در صفحه غیرقالب اضافه نمی‌شود', false === strpos( Typography::body_class( '' ), Typography::BODY_CLASS ) );
	$reset();

	// SC-02: no front-end leak.
	es_check( 'SC-02: پروفایل admin در AssetManager خالی است', array() === AssetManager::profiles()['admin'] );
	AssetManager::enqueue_for_profile( 'admin' );
	es_check( 'SC-02: فونت ادمین در فرانت‌اند enqueue نمی‌شود', ! wp_style_is( Typography::HANDLE, 'enqueued' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
