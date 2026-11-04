<?php
/**
 * P04-T005 — Walker ناوبری دسکتاپ
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Core\NavWalkers\DesktopWalker;

es_task( 'P04-T005', 'Walker ناوبری دسکتاپ' );

es_check( 'target: inc/Core/NavWalkers.php', es_file( 'inc/Core/NavWalkers.php' ) );
es_check( 'AC-01: کلاس‌های hidden lg:flex در brand-row حفظ شده', es_contains( 'template-parts/header/brand-row.php', 'hidden items-center gap-1 lg:flex' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	es_check( 'DesktopWalker کلاس موجود است', class_exists( DesktopWalker::class ) );

	$old_locs = get_theme_mod( 'nav_menu_locations', array() );
	$menu_id  = wp_update_nav_menu_object( 0, array( 'menu-name' => 'منوی دسکتاپ تست' ) );

	$blog_id = wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'   => 'وبلاگ',
		'menu-item-url'     => home_url( '/blog/' ),
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
	$child_id = wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => 'نورپردازی میدان',
		'menu-item-url'       => home_url( '/services/square-lighting/' ),
		'menu-item-type'      => 'custom',
		'menu-item-status'    => 'publish',
		'menu-item-parent-id' => $services_id,
	) );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => 'زیرمجموعه تست',
		'menu-item-url'       => home_url( '/services/square-lighting/sub/' ),
		'menu-item-type'      => 'custom',
		'menu-item-status'    => 'publish',
		'menu-item-parent-id' => $child_id,
	) );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => 'محصولات',
		'menu-item-url'    => home_url( '/shop/' ),
		'menu-item-type'   => 'custom',
		'menu-item-status' => 'publish',
	) );

	$locs                     = $old_locs;
	$locs['main-mega-menu']   = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locs );

	$html = (string) Menus::render( 'main-mega-menu', array(
		'walker'     => new DesktopWalker(),
		'items_wrap' => '%3$s',
		'echo'       => false,
	) );

	// AC-02: current item gets the active classes + aria-current.
	es_check( 'AC-02: آیتم جاری کلاس فعال می‌گیرد', false !== strpos( $html, 'bg-emerald-50 text-emerald-800' ) );
	es_check( 'AC-02: آیتم جاری aria-current دارد', false !== strpos( $html, 'aria-current="page"' ) );

	// AC-03 / SC-01: simple dropdown (services) with nested levels.
	es_check( 'AC-03: زیرمنوی ساده پنل دارد', false !== strpos( $html, 'data-desktop-mega-panel="services"' ) );
	es_check( 'SC-01: لینک فرزند رندر می‌شود', false !== strpos( $html, 'نورپردازی میدان' ) );
	es_check( 'SC-01: لینک نوه (سطح سوم) رندر می‌شود', false !== strpos( $html, 'زیرمجموعه تست' ) );
	es_check( 'SC-01: دکمه تریگر خدمات حاضر است', false !== strpos( $html, 'data-desktop-mega-trigger="services"' ) );

	// Mega wrapper for products.
	es_check( 'مگامنوی محصولات wrapper دارد', false !== strpos( $html, 'data-mega-menu-products="true"' ) );
	es_check( 'مگامنوی محصولات trigger دارد', false !== strpos( $html, 'data-desktop-mega-trigger="products"' ) );

	// Plain link rendered.
	es_check( 'لینک ساده وبلاگ رندر می‌شود', false !== strpos( $html, '>وبلاگ</a>' ) );

	// SC-02: empty menu falls back to real links.
	set_theme_mod( 'nav_menu_locations', array() );
	$fallback = (string) Menus::render( 'main-mega-menu', array( 'walker' => new DesktopWalker(), 'echo' => false ) );
	es_check( 'SC-02: منوی خالی fallback نشان می‌دهد', false !== strpos( $fallback, '<a ' ) && false !== strpos( $fallback, 'صفحه اصلی' ) );

	// Cleanup.
	wp_delete_nav_menu( $menu_id );
	set_theme_mod( 'nav_menu_locations', $old_locs );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
