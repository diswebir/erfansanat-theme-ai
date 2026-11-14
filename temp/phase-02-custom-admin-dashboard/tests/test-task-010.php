<?php
/**
 * P02-T010 — تب هدر/اعلان/لوگو
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Tabs\HeaderTab;
use ErfanSanat\Theme\Options\Store;

es_task( 'P02-T010', 'تب هدر/اعلان/لوگو' );

es_check( 'target: inc/Options/Tabs/HeaderTab.php', es_file( 'inc/Options/Tabs/HeaderTab.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	Store::reset();

	$mirror   = (string) file_get_contents( ES_MIRROR . '/index.html' );
	$defaults = HeaderTab::defaults();

	// AC-01: defaults are extracted verbatim from the mirror.
	es_check( 'AC-01: پیش‌فرض عنوان اعلان از mirror', false !== strpos( $mirror, $defaults['announcement_title'] ) );
	es_check( 'AC-01: پیش‌فرض زیرعنوان اعلان از mirror', false !== strpos( $mirror, $defaults['announcement_subtitle'] ) );
	es_check( 'AC-01: پیش‌فرض نام برند از mirror', false !== strpos( $mirror, $defaults['brand_name'] ) );
	es_check( 'AC-01: پیش‌فرض شعار برند از mirror', false !== strpos( $mirror, $defaults['brand_tagline'] ) );
	es_check( 'AC-01: تب هدر فیلد announcement_enabled دارد', in_array( 'announcement_enabled', HeaderTab::fields(), true ) );

	// SC-01: changing the announcement text is reflected in the front-end data source.
	Store::set( 'announcement_title', 'اعلان جدید آزمایشی' );
	$ann = HeaderTab::announcement();
	es_check( 'SC-01: تغییر متن اعلان اعمال می‌شود', 'اعلان جدید آزمایشی' === $ann['title'] );
	Store::reset();

	// AC-02: the announcement bar can be disabled.
	Store::set( 'announcement_enabled', false );
	es_check( 'AC-02: خاموش‌کردن نوار اعلان ممکن است', false === HeaderTab::announcement_enabled() );
	Store::set( 'announcement_enabled', true );
	es_check( 'AC-02: روشن‌کردن مجدد نوار اعلان', true === HeaderTab::announcement_enabled() );

	// AC-03 / SC-02: logo falls back to the bundled SVG when removed.
	Store::delete( 'logo_id' );
	$fallback = HeaderTab::logo_url();
	es_check( 'AC-03: لوگوی پیش‌فرض fallback دارد', false !== strpos( $fallback, 'images/ese-logo.svg' ) );
	es_check( 'SC-02: حذف لوگو پیش‌فرض را برمی‌گرداند', HeaderTab::logo_url() === $fallback );

	Store::reset();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
