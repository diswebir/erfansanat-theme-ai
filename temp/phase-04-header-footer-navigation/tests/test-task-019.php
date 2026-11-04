<?php
/**
 * P04-T019 — Walker منوی لندینگ
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\NavWalkers\LandingWalker;

es_task( 'P04-T019', 'Walker منوی لندینگ' );

es_check( 'target: LandingWalker در NavWalkers.php', es_contains( 'inc/Core/NavWalkers.php', 'class LandingWalker' ) );
es_check( 'boot در functions.php', es_contains( 'functions.php', 'LandingWalker::boot()' ) );
es_check( 'AC-01: اتریبیوت data-landing-link تولید می‌شود', es_contains( 'inc/Core/NavWalkers.php', 'data-landing-link="true"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$old_locs = get_theme_mod( 'nav_menu_locations', array() );
	$locs     = $old_locs;
	unset( $locs['landing'] );
	set_theme_mod( 'nav_menu_locations', $locs );

	// ── AC-03: no assigned menu → empty string, no fatal ──────────────────.
	es_check( 'AC-03: بدون منوی اختصاصی fatal نمی‌دهد و خالی است', '' === LandingWalker::render() );

	// ── SC-02: assigned but empty menu renders nothing ────────────────────.
	$empty_menu = wp_update_nav_menu_object( 0, array( 'menu-name' => 'لندینگ خالی T019' ) );
	$locs['landing'] = $empty_menu;
	set_theme_mod( 'nav_menu_locations', $locs );
	es_check( 'SC-02: منوی خالی رندر نمی‌شود', '' === LandingWalker::render() );
	wp_delete_nav_menu( $empty_menu );

	// ── AC-01 / SC-01: real menu with section anchors ─────────────────────.
	$menu_id = wp_update_nav_menu_object( 0, array( 'menu-name' => 'لندینگ T019' ) );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => 'محصولات',
		'menu-item-url'    => '#products',
		'menu-item-type'   => 'custom',
		'menu-item-status' => 'publish',
	) );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => 'خدمات',
		'menu-item-url'    => 'https://erfansanat.com/arya/#services',
		'menu-item-type'   => 'custom',
		'menu-item-status' => 'publish',
	) );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => '<b>تماس</b><script>x</script>',
		'menu-item-url'    => '#contact',
		'menu-item-type'   => 'custom',
		'menu-item-status' => 'publish',
	) );
	$locs['landing'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locs );

	$html = LandingWalker::render();
	es_check( 'SC-01: منوی لندینگ رندر می‌شود', '' !== $html && false !== strpos( $html, 'data-landing-menu="true"' ) );
	es_check( 'AC-01: لینک #products با data-landing-link', false !== strpos( $html, '<a href="#products" data-landing-link="true"' ) );
	es_check( 'AC-01: URL کامل به لنگر #services نرمال می‌شود', false !== strpos( $html, '<a href="#services" data-landing-link="true"' ) );
	es_check( 'AC-01: لینک #contact حفظ شده', false !== strpos( $html, 'href="#contact"' ) );
	es_check( 'DOM تخت است (یک ul)', 1 === substr_count( $html, '<ul' ) );
	es_check( 'خروجی escape می‌شود', false === strpos( $html, '<script>x</script>' ) && false !== strpos( $html, 'تماس' ) );

	// section_href unit behavior.
	es_check( 'AC-01: لنگر خالی → رشته خالی', '' === LandingWalker::section_href( '' ) );
	es_check( 'AC-01: URL بدون fragment واقعی می‌ماند', 'https://erfansanat.com/shop/' === LandingWalker::section_href( 'https://erfansanat.com/shop/' ) );

	// Walker skips items without usable href/title (no stray </li>).
	$walker = new LandingWalker();
	$fake   = (object) array( 'ID' => 999991, 'title' => '', 'url' => '#x', 'classes' => array() );
	$out    = '';
	$walker->start_el( $out, $fake, 0, null );
	$walker->end_el( $out, $fake, 0, null );
	es_check( 'آیتم بدون عنوان رندر نمی‌شود', '' === $out );

	// ── AC-02: scroll-spy contract in navigation.js + active CSS ──────────.
	$nav_js = (string) file_get_contents( ES_THEME . '/assets/js/navigation.js' );
	es_check( 'AC-02: scroll-spy روی data-landing-link', false !== strpos( $nav_js, '[data-landing-link]' ) && false !== strpos( $nav_js, 'IntersectionObserver' ) );
	es_check( 'AC-02: کلاس is-active و aria-current toggled می‌شود', false !== strpos( $nav_js, '"is-active"' ) && false !== strpos( $nav_js, 'aria-current' ) );

	do_action( 'wp_enqueue_scripts' );
	$after = wp_styles()->get_data( 'es-styles', 'after' );
	$after = is_array( $after ) ? implode( "\n", $after ) : (string) $after;
	es_check( 'AC-02: CSS حالت active به استایل تم اضافه شد', false !== strpos( $after, '.es-landing-link.is-active' ) );

	// SC-01: native smooth anchor scrolling ships in the mirror stylesheet.
	es_check( 'SC-01: اسکرول نرم بومی در styles.css موجود است', es_contains( 'assets/css/styles.css', 'scroll-behavior:smooth' ) );

	// Cleanup.
	set_theme_mod( 'nav_menu_locations', $old_locs );
	wp_delete_nav_menu( $menu_id );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
