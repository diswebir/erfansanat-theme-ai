<?php
/**
 * P05-T006 — اسلایدهای انتخابی هیرو از پروژه‌ها
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;
use ErfanSanat\Theme\Options\Tabs\HomeTab;
use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T006', 'اسلایدهای انتخابی هیرو از پروژه‌ها' );

es_check( 'AC-01: repeater اسلاید در Schema ثبت شده', es_contains( 'inc/Options/Schema.php', "'hero_slides'" ) );
es_check( 'هلپر resolve_slides در HomeTab', es_contains( 'inc/Options/Tabs/HomeTab.php', 'function resolve_slides' ) );

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
	$scene_ids = static function ( $html ) {
		preg_match_all( '/data-home1-scene-img="(\d+)"/', $html, $m );
		return $m[1];
	};
	$mk_img = static function ( $file ) {
		$a = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => $file, 'post_mime_type' => 'image/jpeg' ) );
		update_post_meta( $a, '_wp_attached_file', '2026/11/' . $file );
		return $a;
	};

	$f      = Schema::fields();
	es_check( 'AC-01: hero_slides از نوع repeater است', 'repeater' === ( $f['hero_slides']['type'] ?? '' ) );
	es_check( 'AC-01: subfield پروژه دارد', isset( $f['hero_slides']['subfields']['project_id'] ) );

	// ── Fixtures: two published projects + one draft ──────────────────────.
	$img1 = $mk_img( 'proj-one.jpg' );
	$img2 = $mk_img( 'proj-two.jpg' );
	$p1   = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه الف' ) );
	$p2   = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه ب' ) );
	$pd   = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'draft', 'post_title' => 'پروژه پیش‌نویس' ) );
	set_post_thumbnail( $p1, $img1 );
	set_post_thumbnail( $p2, $img2 );

	// ── SC-01 / AC-02: two projects rendered in order ─────────────────────.
	Store::set( 'hero_slides', array( array( 'project_id' => $p1, 'image' => 0 ), array( 'project_id' => $p2, 'image' => 0 ) ) );
	$html = $render();
	$ids  = $scene_ids( $html );
	es_check( 'SC-01: دو اسلاید رندر می‌شوند', array( '0', '1' ) === $ids );
	es_check( 'AC-02: ترتیب اسلایدها حفظ شده', strpos( $html, 'پروژه الف' ) < strpos( $html, 'پروژه ب' ) );
	es_check( 'AC-04: عنوان از پروژه می‌آید (alt)', false !== strpos( $html, 'alt="پروژه الف"' ) && false !== strpos( $html, 'alt="پروژه ب"' ) );
	es_check( 'AC-04: تصویر از پروژه می‌آید', false !== strpos( $html, 'proj-one.jpg' ) && false !== strpos( $html, 'proj-two.jpg' ) );

	// AC-04: link comes from the project (resolved data).
	$resolved = HomeTab::hero_slides_resolved();
	es_check( 'AC-04: لینک از permalink پروژه می‌آید', isset( $resolved[0]['link'] ) && $resolved[0]['link'] === get_permalink( $p1 ) );
	es_check( 'AC-04: عنوان resolve شده از پروژه است', isset( $resolved[0]['title'] ) && 'پروژه الف' === $resolved[0]['title'] );

	// ── SC-02 / AC-01: draft project is not shown ─────────────────────────.
	Store::set( 'hero_slides', array( array( 'project_id' => $p1 ), array( 'project_id' => $pd ), array( 'project_id' => $p2 ) ) );
	$html = $render();
	es_check( 'SC-02: پروژه پیش‌نویس نمایش داده نمی‌شود', false === strpos( $html, 'پروژه پیش‌نویس' ) );
	es_check( 'AC-01: فقط منتشرشده‌ها اسلاید می‌مانند', array( '0', '1' ) === $scene_ids( $html ) );

	// ── AC-03: deleted / missing project does not break the slider ────────.
	Store::set( 'hero_slides', array( array( 'project_id' => 999999 ), array( 'project_id' => $p1 ), array( 'project_id' => $p2 ) ) );
	$html = $render();
	es_check( 'AC-03: پروژه حذف‌شده اسلاید را خراب نمی‌کند', '' !== trim( $html ) && array( '0', '1' ) === $scene_ids( $html ) );

	// ── AC-02: reversing the rows reverses the order ──────────────────────.
	Store::set( 'hero_slides', array( array( 'project_id' => $p2 ), array( 'project_id' => $p1 ) ) );
	$html = $render();
	es_check( 'AC-02: تغییر ترتیب repeater در خروجی دیده می‌شود', strpos( $html, 'پروژه ب' ) < strpos( $html, 'پروژه الف' ) );

	// ── Per-slide image override ──────────────────────────────────────────.
	$ov = $mk_img( 'override.jpg' );
	Store::set( 'hero_slides', array( array( 'project_id' => $p1, 'image' => $ov ) ) );
	$html = $render();
	es_check( 'تصویر سفارشی اسلاید override می‌شود', false !== strpos( $html, 'override.jpg' ) && false === strpos( $html, 'proj-one.jpg' ) );

	// ── Per-page section slides override the global option ────────────────.
	$html = $render( array( 'slides' => array( array( 'project_id' => $p2 ) ) ) );
	es_check( 'اسلاید سطح صفحه بر تنظیم سراسری اولویت دارد', array( '0' ) === $scene_ids( $html ) && false !== strpos( $html, 'پروژه ب' ) && false === strpos( $html, 'پروژه الف' ) );

	// Cleanup.
	Store::delete( 'hero_slides' );
	foreach ( array( $p1, $p2, $pd, $img1, $img2, $ov ) as $id ) { wp_delete_post( $id, true ); }
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
