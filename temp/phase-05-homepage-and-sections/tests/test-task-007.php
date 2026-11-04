<?php
/**
 * P05-T007 — Markup و JS هیرو (home1)
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-007.php
 *
 * No browser is available, so SC-01/SC-02 are verified structurally: the DOM
 * carries every data-home1-* / data-scene-* hook the immutable main.js
 * (initHomeHeroVariants / initHeroVideo) queries, with matching scene indices.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Store;
use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T007', 'Markup و JS هیرو (home1)' );

es_check( 'AC-01: id=home در قالب', es_contains( 'template-parts/sections/home/section-hero.php', 'id="home"' ) );
es_check( 'AC-01: eseCircleTextPath در قالب', es_contains( 'template-parts/sections/home/section-hero.php', 'eseCircleTextPath' ) );
es_check( 'AC-01: home1-categories در قالب', es_contains( 'template-parts/sections/home/section-hero.php', 'id="home1-categories"' ) );
es_check( 'AC-02: data-scene-is-video در قالب', es_contains( 'template-parts/sections/home/section-hero.php', 'data-scene-is-video' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}
	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		register_taxonomy( 'product_cat', 'product', array( 'public' => true, 'hierarchical' => true ) );
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'hero', 'type' => 'hero', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// Fixtures: video + two published projects (with city) + product_cat terms.
	$vid = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'hero-video', 'post_mime_type' => 'video/mp4' ) );
	update_post_meta( $vid, '_wp_attached_file', '2026/11/hero-video.mp4' );
	Store::set( 'hero_video_id', $vid );

	$city = wp_insert_term( 'استان البرز', 'city' );
	$p1   = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه الف' ) );
	$p2   = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه ب' ) );
	wp_set_object_terms( $p1, array( (int) $city['term_id'] ), 'city' );
	Store::set( 'hero_slides', array( array( 'project_id' => $p1 ), array( 'project_id' => $p2 ) ) );

	$cat1 = wp_insert_term( 'پوینت لایت پیکسل', 'product_cat', array( 'slug' => 'point-light', 'description' => '۴ و ۶ سانتی پلی‌کربنات' ) );
	$cat2 = wp_insert_term( 'درخت نوری', 'product_cat', array( 'slug' => 'light-tree', 'description' => 'درختان نوری هوشمند' ) );

	$html = $render();

	// ── AC-01: identifiers preserved ──────────────────────────────────────.
	es_check( 'AC-01: id=home رندر می‌شود', false !== strpos( $html, 'id="home"' ) );
	es_check( 'AC-01: eseCircleTextPath (متن دایره‌ای) رندر می‌شود', false !== strpos( $html, 'id="eseCircleTextPath"' ) && false !== strpos( $html, 'ERFAN SANAT ESFAHAN' ) );
	es_check( 'AC-01: home1-categories رندر می‌شود', false !== strpos( $html, 'id="home1-categories"' ) );

	// ── AC-03: every data-home1-* hook main.js queries is present ─────────.
	$hooks = array(
		'data-home1-scene-btn', 'data-home1-scene-img', 'data-home1-inline-video="true"',
		'data-home1-play-trigger="true"', 'data-home1-video-close="true"', 'data-home1-video-mute="true"',
		'data-home1-video-controls="true"', 'data-home1-atmosphere="true"', 'data-home1-overlay-item="true"',
		'data-home1-bottom-tab="true"', 'data-home1-bottom-fillet="true"', 'data-home1-categories-grid="true"',
		'data-home1-scene-title="true"', 'data-home1-scene-cta="true"', 'data-home1-scene-link="true"',
	);
	$missing = array();
	foreach ( $hooks as $hook ) {
		if ( false === strpos( $html, $hook ) ) {
			$missing[] = $hook;
		}
	}
	es_check( 'AC-03: همهٔ قلاب‌های data-home1-main.js موجودند', empty( $missing ) );

	// ── AC-02 / SC-01: scene buttons carry data-scene-* + matching indices ─.
	es_check( 'AC-02: دکمه ویدیو data-scene-is-video=true دارد', false !== strpos( $html, 'data-home1-scene-btn="0"' ) && 1 === preg_match( '/data-home1-scene-btn="0"[^>]*data-scene-is-video="true"/', $html ) );
	es_check( 'AC-02: دکمه پروژه data-scene-title دارد', false !== strpos( $html, 'data-scene-title="پروژه الف"' ) );
	es_check( 'AC-02: دکمه پروژه data-scene-href = permalink پروژه دارد', false !== strpos( $html, 'data-scene-href="' . esc_attr( get_permalink( $p1 ) ) . '"' ) );
	es_check( 'AC-02: دکمه پروژه data-scene-cta دارد', false !== strpos( $html, 'data-scene-cta="مشاهده پروژه"' ) );
	es_check( 'AC-02: دکمه پروژه data-scene-is-video=false دارد', 1 === preg_match( '/data-home1-scene-btn="1"[^>]*data-scene-is-video="false"/', $html ) );
	preg_match_all( '/data-home1-scene-btn="(\d+)"/', $html, $btns );
	preg_match_all( '/data-home1-scene-img="(\d+)"/', $html, $imgs );
	es_check( 'SC-01: اندیس دکمه‌ها و تصاویر صحنه منطبق است (۰،۱،۲)', array( '0', '1', '2' ) === $btns[1] && array( '0', '1', '2' ) === $imgs[1] );
	es_check( 'SC-01: برچسب دکمه پروژه از شهر می‌آید', false !== strpos( $html, '<span>استان البرز</span>' ) );

	// ── SC-02: inline video play/close/mute controls present ──────────────.
	es_check( 'SC-02: دکمه پخش (play-trigger) موجود است', false !== strpos( $html, 'data-home1-play-trigger="true"' ) );
	es_check( 'SC-02: دکمه بستن ویدیو موجود است', false !== strpos( $html, 'data-home1-video-close="true"' ) && false !== strpos( $html, 'توقف ویدیو و بازگشت به نمای طرح' ) );
	es_check( 'SC-02: دکمه قطع صدا موجود است', false !== strpos( $html, 'data-home1-video-mute="true"' ) );

	// ── Categories grid sourced from product_cat ──────────────────────────.
	es_check( 'دسته‌بندی: عنوان ترم رندر می‌شود', false !== strpos( $html, 'پوینت لایت پیکسل' ) && false !== strpos( $html, 'درخت نوری' ) );
	es_check( 'دسته‌بندی: آیکون lucide متناسب (cpu برای point-light)', false !== strpos( $html, 'lucide-cpu' ) );
	es_check( 'دسته‌بندی: زیرعنوان از description ترم', false !== strpos( $html, '۴ و ۶ سانتی پلی‌کربنات' ) );
	es_check( 'دسته‌بندی: شمارش مدل فارسی', 1 === preg_match( '/[۰-۹]+ مدل/u', $html ) );

	// ── Heading escaped (P02-T018 contract) ───────────────────────────────.
	$html2 = $render( array( 'heading' => '<b>bold</b>' ) );
	es_check( 'عنوان هیرو escape می‌شود', false === strpos( $html2, '<b>bold</b>' ) && false !== strpos( $html2, '&lt;b&gt;' ) );

	// Cleanup.
	Store::delete( 'hero_video_id' );
	Store::delete( 'hero_slides' );
	foreach ( array( $vid, $p1, $p2 ) as $id ) { wp_delete_post( $id, true ); }
	wp_delete_term( $city['term_id'], 'city' );
	wp_delete_term( $cat1['term_id'], 'product_cat' );
	wp_delete_term( $cat2['term_id'], 'product_cat' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
