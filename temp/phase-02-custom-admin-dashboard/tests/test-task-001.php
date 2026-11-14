<?php
/**
 * P02-T001 — Schema و ذخیره‌گاه آپشن‌ها
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;

es_task( 'P02-T001', 'Schema و ذخیره‌گاه آپشن‌ها' );

es_check( 'target: inc/Options/Schema.php', es_file( 'inc/Options/Schema.php' ) );
es_check( 'target: inc/Options/Store.php', es_file( 'inc/Options/Store.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// AC-01: single option name + schema version.
	es_check( 'AC-01: نام آپشن erfansanat_theme_options', 'erfansanat_theme_options' === Store::OPTION );
	es_check( 'AC-01: schema_version عددی مثبت', Schema::version() >= 1 );

	// AC-03: every field has a default.
	$fields = Schema::fields();
	$all_have_default = ! empty( $fields );
	foreach ( $fields as $key => $field ) {
		$all_have_default = $all_have_default && array_key_exists( 'default', $field ) && isset( $field['type'] );
	}
	es_check( 'AC-03: همه فیلدها type و default دارند', $all_have_default );
	es_check( 'AC-03: defaults با fields هم‌کلید است', array_keys( Schema::defaults() ) === array_keys( $fields ) );

	// AC-02: unknown key is not known.
	es_check( 'AC-02: کلید ناشناخته شناخته نمی‌شود', ! Schema::is_known( 'totally_unknown_key_xyz' ) );
	es_check( 'AC-02: کلید شناخته‌شده معتبر است', Schema::is_known( 'announcement_enabled' ) );

	// SC-01: round-trip read/write/delete of a known key.
	Store::reset();
	$before = Store::get( 'brand_name' );
	es_check( 'SC-01: پیش‌فرض brand_name از mirror', 'عرفان صنعت اصفهان' === $before );
	Store::set( 'brand_name', 'نام آزمایشی' );
	es_check( 'SC-01: نوشتن/خواندن round-trip', 'نام آزمایشی' === Store::get( 'brand_name' ) );
	Store::delete( 'brand_name' );
	es_check( 'SC-01: حذف کلید به پیش‌فرض برمی‌گردد', 'عرفان صنعت اصفهان' === Store::get( 'brand_name' ) );

	// AC-02: writing an unknown key is rejected (not persisted).
	$rejected = Store::set( 'totally_unknown_key_xyz', 'x' );
	$raw = get_option( Store::OPTION, array() );
	es_check( 'AC-02: کلید ناشناخته ذخیره نشد', false === $rejected && ! isset( $raw['totally_unknown_key_xyz'] ) );

	// AC-01: stored option carries schema_version.
	Store::set( 'brand_name', 'نسخه‌دار' );
	$raw = get_option( Store::OPTION, array() );
	es_check( 'AC-01: آپشن ذخیره‌شده schema_version دارد', isset( $raw['schema_version'] ) && Schema::version() === (int) $raw['schema_version'] );

	// SC-02: corrupted option falls back to safe defaults.
	update_option( Store::OPTION, 'this-is-not-an-array' );
	$all = Store::all();
	es_check( 'SC-02: آپشن خراب به پیش‌فرض fallback می‌کند', is_array( $all ) && 'عرفان صنعت اصفهان' === $all['brand_name'] );
	es_check( 'SC-02: get روی آپشن خراب default می‌دهد', 'عرفان صنعت اصفهان' === Store::get( 'brand_name' ) );

	// Clean up so later tests start from defaults.
	Store::reset();
	es_check( 'پاک‌سازی: آپشن به حالت پیش‌فرض برگشت', array() === get_option( Store::OPTION, array() ) || ! get_option( Store::OPTION ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
