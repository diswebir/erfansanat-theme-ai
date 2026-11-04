<?php
/**
 * P04-T003 — نوار اعلان بالای هدر
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Store;

es_task( 'P04-T003', 'نوار اعلان بالای هدر' );

es_check( 'target: template-parts/header/topbar.php', es_file( 'template-parts/header/topbar.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$render = static function () {
		ob_start();
		get_template_part( 'template-parts/header/topbar' );
		return (string) ob_get_clean();
	};

	// AC-01: defaults from options render.
	$html = $render();
	es_check( 'AC-01: عنوان پیش‌فرض رندر می‌شود', false !== strpos( $html, 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE)' ) );
	es_check( 'AC-01: زیرعنوان ISO رندر می‌شود', false !== strpos( $html, 'ISO 9001:2015' ) );
	es_check( 'AC-01: لینک مجوزها از options می‌آید', false !== strpos( $html, 'مجوزها و افتخارات' ) );
	es_check( 'AC-01: لینک استخدام resolve می‌شود', false !== strpos( $html, '/recruitment/' ) );

	// AC-03: lg:block visibility class preserved.
	es_check( 'AC-03: کلاس lg:block حفظ شده', false !== strpos( $html, 'lg:block' ) );

	// SC-01: changing the text in options changes the output.
	Store::set( 'announcement_title', 'عنوان سفارشی آزمایشی' );
	$html = $render();
	es_check( 'SC-01: تغییر متن در خروجی دیده می‌شود', false !== strpos( $html, 'عنوان سفارشی آزمایشی' ) );
	Store::delete( 'announcement_title' );

	// SC-02 / AC-02: disabling removes the whole bar.
	Store::set( 'announcement_enabled', false );
	$html = $render();
	es_check( 'SC-02/AC-02: خاموش‌کردن نوار DOM را حذف می‌کند', '' === trim( $html ) );
	Store::delete( 'announcement_enabled' );

	// Restored default renders again.
	$html = $render();
	es_check( 'پس از بازگردانی، نوار دوباره رندر می‌شود', false !== strpos( $html, 'lg:block' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
