<?php
/**
 * P04-T001 — ثبت چهار موقعیت منو
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Menus;

es_task( 'P04-T001', 'ثبت چهار موقعیت منو' );

es_check( 'target: inc/Core/Menus.php', es_file( 'inc/Core/Menus.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$menus = get_registered_nav_menus();

	// AC-01 / SC-01: the four exact locations are registered.
	foreach ( array( 'main-mega-menu', 'mobile-menu', 'tolidat-sherkat', 'landing' ) as $loc ) {
		es_check( "AC-01/SC-01: location «{$loc}» ثبت شده", isset( $menus[ $loc ] ) );
	}

	// AC-03: admin labels are Persian.
	es_check( 'AC-03: نام main-mega-menu فارسی است', 1 === preg_match( '/[\x{0600}-\x{06FF}]/u', $menus['main-mega-menu'] ?? '' ) );
	es_check( 'AC-03: نام mobile-menu فارسی است', 1 === preg_match( '/[\x{0600}-\x{06FF}]/u', $menus['mobile-menu'] ?? '' ) );
	es_check( 'AC-03: نام tolidat-sherkat فارسی است', 1 === preg_match( '/[\x{0600}-\x{06FF}]/u', $menus['tolidat-sherkat'] ?? '' ) );
	es_check( 'AC-03: نام landing فارسی است', 1 === preg_match( '/[\x{0600}-\x{06FF}]/u', $menus['landing'] ?? '' ) );

	// AC-02: fallback renders real links when no menu is assigned.
	$old_locs = get_theme_mod( 'nav_menu_locations', array() );
	set_theme_mod( 'nav_menu_locations', array() );
	$fallback = Menus::render( 'tolidat-sherkat', array( 'echo' => false ) );
	es_check( 'AC-02: fallback بدون منو خروجی دارد', is_string( $fallback ) && '' !== trim( $fallback ) );
	es_check( 'AC-02: fallback لینک واقعی دارد', false !== strpos( $fallback, '<a ' ) && false !== strpos( $fallback, 'href=' ) );

	// SC-02: assigning a menu from admin works and is consumed.
	$menu_id = wp_update_nav_menu_object( 0, array( 'menu-name' => 'منوی تست فاز ۴' ) );
	$page_id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'صفحه تست منو' ) );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => 'آیتم تست',
		'menu-item-object'    => 'page',
		'menu-item-object-id' => $page_id,
		'menu-item-type'      => 'post_type',
		'menu-item-status'    => 'publish',
	) );
	$locs = $old_locs;
	$locs['tolidat-sherkat'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locs );

	es_check( 'SC-02: has_nav_menu پس از اختصاص true است', has_nav_menu( 'tolidat-sherkat' ) );
	$assigned = Menus::render( 'tolidat-sherkat', array( 'echo' => false ) );
	es_check( 'SC-02: منوی اختصاص‌داده‌شده رندر می‌شود', false !== strpos( $assigned, 'آیتم تست' ) );

	// Fallback items are filterable (live-site customization).
	add_filter( 'es_menu_fallback_items', function ( $items, $location ) {
		if ( 'landing' === $location ) {
			$items[] = array( 'label' => 'لینک افزوده', 'url' => '#added' );
		}
		return $items;
	}, 10, 2 );
	$filtered = Menus::fallback_items( 'landing' );
	es_check( 'fallback فیلترپذیر است', in_array( 'لینک افزوده', wp_list_pluck( $filtered, 'label' ), true ) );
	remove_all_filters( 'es_menu_fallback_items' );

	// Cleanup fixtures and restore locations.
	wp_delete_nav_menu( $menu_id );
	wp_delete_post( $page_id, true );
	set_theme_mod( 'nav_menu_locations', $old_locs );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
