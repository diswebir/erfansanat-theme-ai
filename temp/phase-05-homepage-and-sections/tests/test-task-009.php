<?php
/**
 * P05-T009 — سکشن نمونه‌کارهای اجراشده
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-009.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T009', 'سکشن نمونه‌کارهای اجراشده' );

es_check( 'target: بخش نمونه‌کارها ساخته شده', es_file( 'template-parts/sections/home/section-featured-projects.php' ) );
es_check( 'AC-02: no_found_rows استفاده می‌شود', es_contains( 'template-parts/sections/home/section-featured-projects.php', "'no_found_rows'  => true" ) );
es_check( 'AC-01: فقط پروژه‌های منتشرشده', es_contains( 'template-parts/sections/home/section-featured-projects.php', "'post_status'    => 'publish'" ) );
es_check( 'کارت استاندارد پروژه بازاستفاده می‌شود', es_contains( 'template-parts/sections/home/section-featured-projects.php', 'cards/card-project' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}
	if ( ! taxonomy_exists( 'project_type' ) ) {
		register_taxonomy( 'project_type', 'projects', array( 'public' => true, 'hierarchical' => true ) );
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'featured-projects', 'type' => 'featured-projects', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// Fixtures: 4 published projects + 1 draft, two project types.
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'fp', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/featured.jpg' );
	$type_a = wp_insert_term( 'تونل نوری', 'project_type', array( 'slug' => 'tunnel' ) );
	$type_b = wp_insert_term( 'میادین', 'project_type', array( 'slug' => 'urban' ) );
	$pubs = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$p = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه منتشر ' . $i ) );
		set_post_thumbnail( $p, $att );
		wp_set_object_terms( $p, array( (int) ( $i <= 2 ? $type_a['term_id'] : $type_b['term_id'] ) ), 'project_type' );
		$pubs[] = $p;
	}
	$draft = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'draft', 'post_title' => 'پروژه پیش‌نویس مخفی' ) );

	// ── SC-01: card count matches the setting (isolate the "all" panel) ───.
	$only_all = static function () {
		return array( array( 'key' => 'all', 'label' => 'همه', 'type' => '', 'count' => 4 ) );
	};
	add_filter( 'es_featured_projects_tabs', $only_all );
	$html = $render( array( 'count' => 3 ) );
	es_check( 'SC-01: تعداد کارت با تنظیم برابر است', 3 === substr_count( $html, 'data-project-item="true"' ) );
	$html2 = $render( array( 'count' => 2 ) );
	es_check( 'SC-01: تغییر count تعداد را عوض می‌کند', 2 === substr_count( $html2, 'data-project-item="true"' ) );

	// ── SC-02 / AC-01: draft is not shown ─────────────────────────────────.
	es_check( 'SC-02: پروژه پیش‌نویس نمایش داده نمی‌شود', false === strpos( $html, 'پروژه پیش‌نویس مخفی' ) );
	es_check( 'AC-01: پروژه منتشرشده نمایش داده می‌شود', false !== strpos( $html, 'پروژه منتشر' ) );
	remove_all_filters( 'es_featured_projects_tabs' );

	// ── Tabs + panels (main.js hooks) + category filtering ────────────────.
	$html3 = $render();
	es_check( 'تب‌ها: data-home2-port-tab موجود است', false !== strpos( $html3, 'data-home2-port-tab="all"' ) );
	es_check( 'پنل‌ها: data-home2-port-panel موجود است', false !== strpos( $html3, 'data-home2-port-panel="all"' ) );
	es_check( 'تب دسته از project_type ساخته می‌شود', false !== strpos( $html3, 'data-home2-port-tab="tunnel"' ) && false !== strpos( $html3, 'data-home2-port-tab="urban"' ) );
	// Extract the tunnel panel and confirm it only holds tunnel projects.
	if ( preg_match( '/data-home2-port-panel="tunnel"(.*?)data-home2-port-panel="urban"/s', $html3, $mm ) ) {
		$tunnel_panel = $mm[1];
		es_check( 'پنل دسته فقط پروژه‌های همان نوع را دارد', false !== strpos( $tunnel_panel, 'data-project-item' ) && false === strpos( $tunnel_panel, 'پروژه منتشر 3' ) && false === strpos( $tunnel_panel, 'پروژه منتشر 4' ) );
	} else {
		es_check( 'پنل دسته فقط پروژه‌های همان نوع را دارد', false );
	}

	// ── AC-03: empty state keeps the DOM intact ───────────────────────────.
	foreach ( $pubs as $p ) { wp_delete_post( $p, true ); }
	wp_delete_post( $draft, true );
	add_filter( 'es_featured_projects_tabs', $only_all );
	$html4 = $render();
	es_check( 'AC-03: حالت خالی DOM را نمی‌شکند', false !== strpos( $html4, 'data-es-section="featured-projects"' ) && false !== strpos( $html4, 'data-home2-port-panel="all"' ) );
	es_check( 'AC-03: پیام خالی نمایش داده می‌شود', false !== strpos( $html4, 'پروژه‌ای برای نمایش در این دسته وجود ندارد' ) );
	remove_all_filters( 'es_featured_projects_tabs' );

	// ── Heading escaped ───────────────────────────────────────────────────.
	$html5 = $render( array( 'heading' => '<b>bold</b>' ) );
	es_check( 'عنوان escape می‌شود', false === strpos( $html5, '<b>bold</b>' ) && false !== strpos( $html5, '&lt;b&gt;' ) );

	// Cleanup.
	wp_delete_post( $att, true );
	wp_delete_term( $type_a['term_id'], 'project_type' );
	wp_delete_term( $type_b['term_id'], 'project_type' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
