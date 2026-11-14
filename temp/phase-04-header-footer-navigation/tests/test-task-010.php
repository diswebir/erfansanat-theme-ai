<?php
/**
 * P04-T010 — Walker آکاردئون موبایل
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Core\NavWalkers\MobileWalker;

es_task( 'P04-T010', 'Walker آکاردئون موبایل' );

es_check( 'target: inc/Core/NavWalkers.php (MobileWalker)', es_contains( 'inc/Core/NavWalkers.php', 'class MobileWalker' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	es_check( 'MobileWalker کلاس موجود است', class_exists( MobileWalker::class ) );

	$old_locs = get_theme_mod( 'nav_menu_locations', array() );
	$menu_id  = wp_update_nav_menu_object( 0, array( 'menu-name' => 'منوی موبایل تست' ) );

	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'   => 'صفحه اصلی',
		'menu-item-url'     => home_url( '/' ),
		'menu-item-type'    => 'custom',
		'menu-item-status'  => 'publish',
		'menu-item-classes' => 'current-menu-item',
	) );
	$services_id = wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => 'خدمات',
		'menu-item-url'    => home_url( '/services/' ),
		'menu-item-type'   => 'custom',
		'menu-item-status' => 'publish',
	) );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => 'نورپردازی پل',
		'menu-item-url'       => home_url( '/services/bridge-lighting/' ),
		'menu-item-type'      => 'custom',
		'menu-item-status'    => 'publish',
		'menu-item-parent-id' => $services_id,
	) );

	$locs                  = $old_locs;
	$locs['mobile-menu']   = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locs );

	$html = (string) Menus::render( 'mobile-menu', array(
		'walker'     => new MobileWalker(),
		'items_wrap' => '%3$s',
		'echo'       => false,
	) );

	// AC-01: accordion data attributes preserved.
	es_check( 'AC-01: data-mobile-acc-btn حفظ شده', false !== strpos( $html, 'data-mobile-acc-btn="services"' ) );
	es_check( 'AC-01: data-mobile-acc-icon حفظ شده', false !== strpos( $html, 'data-mobile-acc-icon="true"' ) );
	es_check( 'AC-01: data-mobile-acc-panel حفظ شده', false !== strpos( $html, 'data-mobile-acc-panel="services"' ) );

	// AC-02: submenu is a collapsible panel (hidden flex-col).
	es_check( 'AC-02: پنل آکاردئونی hidden flex-col', false !== strpos( $html, 'data-mobile-acc-panel="services" class="hidden flex-col' ) );
	es_check( 'AC-02: لینک فرزند در پنل رندر می‌شود', false !== strpos( $html, 'نورپردازی پل' ) );

	// AC-03: current link identifiable.
	es_check( 'AC-03: لینک جاری کلاس فعال دارد', false !== strpos( $html, 'border-emerald-200 bg-emerald-50 text-emerald-900' ) );
	es_check( 'AC-03: لینک جاری aria-current دارد', false !== strpos( $html, 'aria-current="page"' ) );

	// SC-01: button key matches panel key (main.js toggles by key).
	es_check( 'SC-01: کلید دکمه و پنل یکسان است', false !== strpos( $html, 'data-mobile-acc-btn="services"' ) && false !== strpos( $html, 'data-mobile-acc-panel="services"' ) );

	// SC-02: wrapper DOM parity.
	es_check( 'SC-02: wrapper آکاردئون rounded-xl border', false !== strpos( $html, 'overflow-hidden rounded-xl border border-slate-200/80 bg-white' ) );
	es_check( 'SC-02: aria-expanded روی دکمه', false !== strpos( $html, 'aria-expanded="false"' ) );

	// Cleanup.
	wp_delete_nav_menu( $menu_id );
	set_theme_mod( 'nav_menu_locations', $old_locs );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
