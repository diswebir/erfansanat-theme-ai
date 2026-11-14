<?php
/**
 * P03-T012 — Codec متاهای Legacy
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\LegacyMeta;

es_task( 'P03-T012', 'Codec متاهای Legacy' );

es_check( 'target: inc/Core/LegacyMeta.php', es_file( 'inc/Core/LegacyMeta.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$pid = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'کدک تست' ) );
	$items = array( array( 'a' => 1 ), array( 'a' => 2 ) );

	// AC-01: PHP serialized array is read.
	update_post_meta( $pid, 'test-php', $items );
	es_check( 'AC-01: آرایه PHP خوانده می‌شود', LegacyMeta::get( $pid, 'test-php' ) === $items );

	// AC-02: JSON item-0 object is read.
	update_post_meta( $pid, 'test-json', '{"item-0":{"a":1},"item-1":{"a":2}}' );
	es_check( 'AC-02: JSON item-0 خوانده می‌شود', LegacyMeta::get( $pid, 'test-json' ) === $items );

	// JSON plain list is read too.
	update_post_meta( $pid, 'test-list', '[{"a":1},{"a":2}]' );
	es_check( 'لیست JSON خوانده می‌شود', LegacyMeta::get( $pid, 'test-list' ) === $items );

	// SC-01 / AC-03: round-trip preserves the storage format.
	LegacyMeta::set( $pid, 'test-php', $items );
	es_check( 'AC-03: round-trip PHP آرایه می‌ماند', is_array( get_post_meta( $pid, 'test-php', true ) ) );

	LegacyMeta::set( $pid, 'test-json', $items );
	$raw_json = get_post_meta( $pid, 'test-json', true );
	es_check( 'AC-03: round-trip JSON رشته item-0 می‌ماند', is_string( $raw_json ) && false !== strpos( $raw_json, 'item-0' ) );
	es_check( 'SC-01: معنای JSON حفظ می‌شود', LegacyMeta::get( $pid, 'test-json' ) === $items );

	LegacyMeta::set( $pid, 'test-list', $items );
	$raw_list = get_post_meta( $pid, 'test-list', true );
	es_check( 'AC-03: لیست JSON لیست می‌ماند (بدون item-0)', is_string( $raw_list ) && false === strpos( $raw_list, 'item-0' ) );

	// AC-04: corrupt data falls back safely.
	update_post_meta( $pid, 'test-corrupt', 'not-json-{' );
	es_check( 'AC-04: داده خراب fallback پیش‌فرض', array() === LegacyMeta::get( $pid, 'test-corrupt' ) );
	es_check( 'AC-04: fallback سفارشی', array( 'x' ) === LegacyMeta::get( $pid, 'test-corrupt', array( 'x' ) ) );

	// ID lists (scalars) survive round-trip.
	update_post_meta( $pid, 'test-ids', array( 12, 34 ) );
	es_check( 'لیست ID خوانده می‌شود', LegacyMeta::get( $pid, 'test-ids' ) === array( 12, 34 ) );
	LegacyMeta::set( $pid, 'test-ids', array( 12, 34 ) );
	es_check( 'لیست ID آرایه PHP می‌ماند', get_post_meta( $pid, 'test-ids', true ) === array( 12, 34 ) );

	// SC-02: no new historical (corrected-spelling) key is created.
	LegacyMeta::set( $pid, 'galerry_produ', array( 1, 2 ) );
	es_check( 'SC-02: کلید با املای اصلاح‌شده ساخته نمی‌شود', '' === get_post_meta( $pid, 'gallery_produ', true ) );
	es_check( 'SC-02: کلید تاریخی دست‌نخورده نوشته می‌شود', LegacyMeta::get( $pid, 'galerry_produ' ) === array( 1, 2 ) );

	// Term meta uses the same codec.
	$term = wp_insert_term( 'ترم کدک', 'city', array( 'slug' => 'codec-term-t012' ) );
	LegacyMeta::set_for_term( $term['term_id'], 'faq', array( array( 'name_question' => 'سوال', 'answer_question' => 'جواب' ) ), LegacyMeta::FMT_JSON );
	$term_raw = get_term_meta( $term['term_id'], 'faq', true );
	es_check( 'تر‌م‌متا JSON item-0 ذخیره می‌شود', is_string( $term_raw ) && false !== strpos( $term_raw, 'item-0' ) );
	es_check( 'تر‌م‌متا round-trip می‌شود', LegacyMeta::get_for_term( $term['term_id'], 'faq' ) === array( array( 'name_question' => 'سوال', 'answer_question' => 'جواب' ) ) );

	wp_delete_term( $term['term_id'], 'city' );
	wp_delete_post( $pid, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
