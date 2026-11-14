<?php
/**
 * P02-T015 — SectionManager: schema و ذخیره متا
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Schema;
use ErfanSanat\Theme\Sections\SectionManager;

es_task( 'P02-T015', 'SectionManager: schema و ذخیره متا' );

es_check( 'target: inc/Sections/SectionManager.php', es_file( 'inc/Sections/SectionManager.php' ) );
es_check( 'target: inc/Sections/Schema.php', es_file( 'inc/Sections/Schema.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// Schema covers the 13 homepage section types from the mirror map.
	es_check( 'AC-01: کلید متا نسخه‌دار است', '_es_sections_v1' === Schema::meta_key() );
	es_check( 'schema: ۱۳ سکشن صفحه اصلی', 13 === count( Schema::sections( 'home' ) ) );
	es_check( 'schema: contextهای about/contact/arya', array() !== Schema::sections( 'about' ) && array() !== Schema::sections( 'contact' ) && array() !== Schema::sections( 'arya' ) );

	$page_id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'تست سکشن‌ها' ) );

	$sections = array(
		array( 'id' => 'hero', 'type' => 'hero', 'enabled' => true, 'fields' => array( 'heading' => 'هیرو تست' ) ),
		array( 'id' => 'about', 'type' => 'about', 'enabled' => false, 'fields' => array( 'heading' => 'درباره تست' ) ),
	);

	// SC-01: save two sections and re-read stably.
	SectionManager::save_sections( $page_id, $sections, 'home' );
	$read = SectionManager::get_sections( $page_id, 'home' );
	es_check( 'SC-01: دو سکشن ذخیره و خوانده می‌شود', 2 === count( $read ) );
	es_check( 'SC-01: خواندن مجدد پایدار است', 'hero' === $read[0]['type'] && 'about' === $read[1]['type'] );
	es_check( 'SC-01: فیلدها حفظ می‌شوند', 'هیرو تست' === $read[0]['fields']['heading'] );

	// AC-01: stored under the versioned meta key.
	$raw = get_post_meta( $page_id, Schema::meta_key(), true );
	es_check( 'AC-01: متا با کلید نسخه‌دار ذخیره شد', is_array( $raw ) && 2 === count( $raw ) );

	// AC-03: order + enabled preserved.
	es_check( 'AC-03: enabled حفظ می‌شود', true === $read[0]['enabled'] && false === $read[1]['enabled'] );
	es_check( 'AC-03: ترتیب حفظ می‌شود', 0 === $read[0]['order'] && 1 === $read[1]['order'] );

	// AC-02: unknown section types are rejected.
	$with_unknown = array( array( 'type' => 'hero' ), array( 'type' => 'totally_unknown' ), array( 'type' => 'about' ) );
	$norm         = SectionManager::normalize( $with_unknown, 'home' );
	es_check( 'AC-02: سکشن ناشناخته رد می‌شود', 2 === count( $norm ) );

	// SC-02: duplicate instance ids become unique.
	$dup   = array( array( 'id' => 'hero', 'type' => 'hero' ), array( 'id' => 'hero', 'type' => 'hero' ) );
	$norm2 = SectionManager::normalize( $dup, 'home' );
	es_check( 'SC-02: شناسه تکراری یکتا می‌شود', $norm2[0]['id'] !== $norm2[1]['id'] );

	// Reordering persists.
	SectionManager::save_sections( $page_id, array( $sections[1], $sections[0] ), 'home' );
	$read2 = SectionManager::get_sections( $page_id, 'home' );
	es_check( 'AC-03: تغییر ترتیب اعمال می‌شود', 'about' === $read2[0]['type'] && 'hero' === $read2[1]['type'] );

	wp_delete_post( $page_id, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
