<?php
/**
 * P05-T005 — تنظیمات ویدیوی اصلی هیرو
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;
use ErfanSanat\Theme\Options\Tabs\HomeTab;
use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T005', 'تنظیمات ویدیوی اصلی هیرو' );

es_check( 'target: Options/Tabs/HomeTab.php', es_file( 'inc/Options/Tabs/HomeTab.php' ) );
es_check( 'target: بخش هیرو ساخته شده', es_file( 'template-parts/sections/home/section-hero.php' ) );
es_check( 'AC-01: فیلد media ویدیو در Schema ثبت شده', es_contains( 'inc/Options/Schema.php', "'hero_video_id'" ) );
es_check( 'AC-03: preload در Schema ثبت شده', es_contains( 'inc/Options/Schema.php', "'hero_video_preload'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'hero', 'type' => 'hero', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// ── Tab + field registration ──────────────────────────────────────────.
	$tabs = Schema::tabs();
	es_check( 'تب home در تنظیمات ثبت شده', isset( $tabs['home'] ) );
	es_check( 'AC-01: hero_video_id از فیلدهای تب home است', in_array( 'hero_video_id', HomeTab::fields(), true ) );
	es_check( 'AC-01: ویدیو مستقل از اسلایدهاست (slides در HomeTab نیست)', ! in_array( 'slides', HomeTab::fields(), true ) );
	$f = Schema::fields();
	es_check( 'AC-01: نوع فیلد ویدیو media است', 'media' === ( $f['hero_video_id']['type'] ?? '' ) );

	// ── SC-02 / AC-02: no video -> image fallback, hero intact ────────────.
	Store::delete( 'hero_video_id' );
	$html = $render();
	es_check( 'SC-02: بدون ویدیو هیرو fatal نمی‌دهد', '' !== trim( $html ) && false !== strpos( $html, 'id="home"' ) );
	es_check( 'AC-02: بدون ویدیو تگ video نیست', false === strpos( $html, '<video' ) );
	es_check( 'AC-02: بدون ویدیو تصویر fallback دارد', false !== strpos( $html, '<img' ) && false !== strpos( $html, 'images/hero.jpg' ) );
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="hero"' ) );

	// ── SC-01: selected video loads in hero ───────────────────────────────.
	$vid = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'hero-video', 'post_mime_type' => 'video/mp4' ) );
	update_post_meta( $vid, '_wp_attached_file', '2026/11/hero-video.mp4' );
	Store::set( 'hero_video_id', $vid );
	$html = $render();
	es_check( 'SC-01: ویدیوی انتخابی در هیرو لود می‌شود', false !== strpos( $html, '<video' ) && false !== strpos( $html, 'hero-video.mp4' ) );
	es_check( 'SC-01: ویژگی data-home1-inline-video حفظ شده', false !== strpos( $html, 'data-home1-inline-video="true"' ) );
	es_check( 'SC-01: ویدیو autoplay/muted/loop/playsinline دارد', false !== strpos( $html, 'autoplay' ) && false !== strpos( $html, 'muted' ) && false !== strpos( $html, 'loop' ) && false !== strpos( $html, 'playsinline' ) );

	// ── AC-03: poster + preload configurable ──────────────────────────────.
	$poster = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'poster', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $poster, '_wp_attached_file', '2026/11/hero-poster.jpg' );
	Store::set( 'hero_video_poster', $poster );
	Store::set( 'hero_video_preload', 'none' );
	$html = $render();
	es_check( 'AC-03: پوستر سفارشی اعمال می‌شود', false !== strpos( $html, 'poster="' ) && false !== strpos( $html, 'hero-poster.jpg' ) );
	es_check( 'AC-03: preload سفارشی اعمال می‌شود', false !== strpos( $html, 'preload="none"' ) );
	Store::set( 'hero_video_preload', 'bogus' );
	es_check( 'AC-03: مقدار preload نامعتبر رد می‌شود', 'metadata' === HomeTab::hero_video_preload() );

	// ── AC-02: dedicated fallback image option ────────────────────────────.
	$fb = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'fallback', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $fb, '_wp_attached_file', '2026/11/hero-fallback.jpg' );
	Store::set( 'hero_video_fallback', $fb );
	Store::delete( 'hero_video_id' );
	$html = $render();
	es_check( 'AC-02: تصویر جایگزین سفارشی اعمال می‌شود', false !== strpos( $html, 'hero-fallback.jpg' ) && false === strpos( $html, '<video' ) );

	// ── Heading escaped (P02-T018 regression contract) ────────────────────.
	$html = $render( array( 'heading' => '<b>bold</b>' ) );
	es_check( 'عنوان هیرو escape می‌شود', false === strpos( $html, '<b>bold</b>' ) && false !== strpos( $html, '&lt;b&gt;' ) );

	// Cleanup.
	Store::delete( 'hero_video_id' );
	Store::delete( 'hero_video_poster' );
	Store::delete( 'hero_video_preload' );
	Store::delete( 'hero_video_fallback' );
	foreach ( array( $vid, $poster, $fb ) as $id ) { wp_delete_post( $id, true ); }
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
