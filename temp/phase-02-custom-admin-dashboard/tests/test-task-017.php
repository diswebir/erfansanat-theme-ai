<?php
/**
 * P02-T017 — SectionManager: ویرایش محتوای سکشن
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\SectionManager;

es_task( 'P02-T017', 'SectionManager: ویرایش محتوای سکشن' );

es_check( 'target: inc/Sections/SectionManager.php', es_file( 'inc/Sections/SectionManager.php' ) );
es_check( 'AC-03: از Sanitizer مشترک استفاده می‌شود', es_contains( 'inc/Sections/SectionManager.php', 'Sanitizer::sanitize_field' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$page_id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'صفحه ویرایش سکشن' ) );

	$find = function ( array $sections, string $id ) {
		foreach ( $sections as $s ) {
			if ( $s['id'] === $id ) {
				return $s;
			}
		}
		return null;
	};

	// SC-01: edit a section's heading + media (video) and persist.
	SectionManager::save_section_fields( $page_id, 'hero', array( 'heading' => 'عنوان جدید هیرو', 'video_id' => 42 ), 'home' );
	$hero = $find( SectionManager::get_sections( $page_id, 'home' ), 'hero' );
	es_check( 'SC-01: عنوان سکشن ذخیره شد', 'عنوان جدید هیرو' === $hero['fields']['heading'] );
	es_check( 'SC-01: رسانه (ویدیو) ذخیره شد', 42 === $hero['fields']['video_id'] );

	// AC-01: a non-schema field is dropped.
	SectionManager::save_section_fields( $page_id, 'hero', array( 'heading' => 'x', 'evil_key' => 'hack' ), 'home' );
	$hero = $find( SectionManager::get_sections( $page_id, 'home' ), 'hero' );
	es_check( 'AC-01: فیلد خارج از schema حذف می‌شود', ! array_key_exists( 'evil_key', $hero['fields'] ) );

	// AC-02: empty value keeps the field; unsubmitted fields are preserved.
	SectionManager::save_section_fields( $page_id, 'hero', array( 'heading' => '' ), 'home' );
	$hero = $find( SectionManager::get_sections( $page_id, 'home' ), 'hero' );
	es_check( 'AC-02: مقدار خالی فیلد را حذف نمی‌کند', array_key_exists( 'heading', $hero['fields'] ) && '' === $hero['fields']['heading'] );
	es_check( 'AC-02: فیلد ارسال‌نشده حفظ می‌شود', 42 === $hero['fields']['video_id'] );

	// SC-02: clients repeater saves correctly.
	SectionManager::save_section_fields( $page_id, 'clients', array(
		'logos' => array(
			array( 'image' => 11, 'name' => 'شهرداری اصفهان' ),
			array( 'image' => 12, 'name' => 'شرکت گاز' ),
		),
	), 'home' );
	$clients = $find( SectionManager::get_sections( $page_id, 'home' ), 'clients' );
	es_check( 'SC-02: تعداد آیتم‌های repeater درست است', 2 === count( $clients['fields']['logos'] ) );
	es_check( 'SC-02: محتوای repeater ذخیره شد', 'شهرداری اصفهان' === $clients['fields']['logos'][0]['name'] && 11 === $clients['fields']['logos'][0]['image'] );

	// AC-03: icon inside a repeater is sanitized (invalid -> default).
	SectionManager::save_section_fields( $page_id, 'standards', array(
		'items' => array(
			array( 'title' => 'ISO', 'icon' => 'shield' ),
			array( 'title' => 'بد', 'icon' => 'not_an_icon' ),
		),
	), 'home' );
	$standards = $find( SectionManager::get_sections( $page_id, 'home' ), 'standards' );
	es_check( 'AC-03: آیکون معتبر حفظ می‌شود', 'shield' === $standards['fields']['items'][0]['icon'] );
	es_check( 'AC-03: آیکون نامعتبر به پیش‌فرض برمی‌گردد', 'check' === $standards['fields']['items'][1]['icon'] );

	// AC-03: url field (arya cta) rejects javascript, accepts https.
	$arya_page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'صفحه آریا' ) );
	SectionManager::save_section_fields( $arya_page, 'cta', array( 'button_url' => 'javascript:alert(1)' ), 'arya' );
	$cta = $find( SectionManager::get_sections( $arya_page, 'arya' ), 'cta' );
	es_check( 'AC-03: لینک javascript رد می‌شود', '' === $cta['fields']['button_url'] );
	SectionManager::save_section_fields( $arya_page, 'cta', array( 'button_url' => 'https://example.com/buy' ), 'arya' );
	$cta = $find( SectionManager::get_sections( $arya_page, 'arya' ), 'cta' );
	es_check( 'AC-03: لینک معتبر ذخیره می‌شود', 'https://example.com/buy' === $cta['fields']['button_url'] );

	// Unknown section id returns false (nothing saved).
	$missing = SectionManager::save_section_fields( $page_id, 'no_such_section', array( 'heading' => 'x' ), 'home' );
	es_check( 'شناسه سکشن ناشناخته false برمی‌گرداند', false === $missing );

	wp_delete_post( $page_id, true );
	wp_delete_post( $arya_page, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
