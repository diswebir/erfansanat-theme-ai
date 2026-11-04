<?php
/**
 * P04-T011 — تب‌های داخلی مگامنوی موبایل
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-011.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Core\NavWalkers\MobileWalker;
use ErfanSanat\Theme\Options\Store;

es_task( 'P04-T011', 'تب‌های داخلی مگامنوی موبایل' );

es_check( 'target: NavWalkers.php تب محصولات موبایل', es_contains( 'inc/Core/NavWalkers.php', 'data-mobile-prod-tab' ) );
es_check( 'target: NavWalkers.php تب پروژه موبایل', es_contains( 'inc/Core/NavWalkers.php', 'data-mobile-proj-tab' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		register_taxonomy( 'product_cat', 'product', array( 'hierarchical' => true, 'public' => true ) );
	}

	// Product fixtures.
	$top  = wp_insert_term( 'پوینت لایت', 'product_cat' );
	$tid  = $top['term_id'];
	$c1   = wp_insert_term( 'آلفا', 'product_cat', array( 'parent' => $tid ) );
	$c2   = wp_insert_term( 'بتا', 'product_cat', array( 'parent' => $tid ) );
	$c3   = wp_insert_term( 'گاما', 'product_cat', array( 'parent' => $tid ) );
	// Project fixtures.
	$pt   = wp_insert_term( 'تونل نوری', 'project_type' );
	$ct   = wp_insert_term( 'اصفهان', 'city' );

	$old_locs = get_theme_mod( 'nav_menu_locations', array() );
	$menu_id  = wp_update_nav_menu_object( 0, array( 'menu-name' => 'موبایل مگا تست' ) );
	wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'محصولات', 'menu-item-url' => home_url( '/shop/' ), 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
	wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'پروژه‌ها', 'menu-item-url' => home_url( '/projects/' ), 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
	$locs                  = $old_locs;
	$locs['mobile-menu']   = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locs );

	$render = static function () {
		return (string) Menus::render( 'mobile-menu', array( 'walker' => new MobileWalker(), 'items_wrap' => '%3$s', 'echo' => false ) );
	};

	Store::set( 'mega_products_per_tab', 6 );
	Store::set( 'mega_projects_group', 'project_type' );
	$html = $render();

	// AC-01: preserved mobile tab/panel data attributes.
	es_check( 'AC-01: data-mobile-prod-tab حفظ شده', false !== strpos( $html, 'data-mobile-prod-tab="0"' ) );
	es_check( 'AC-01: data-mobile-prod-panel حفظ شده', false !== strpos( $html, 'data-mobile-prod-panel="0"' ) );
	es_check( 'AC-01: data-mobile-proj-tab="type" حفظ شده', false !== strpos( $html, 'data-mobile-proj-tab="type"' ) );
	es_check( 'AC-01: data-mobile-proj-panel="city" حفظ شده', false !== strpos( $html, 'data-mobile-proj-panel="city"' ) );

	// AC-02: same data source as desktop (terms from DB).
	es_check( 'AC-02: دسته محصول از DB می‌آید', false !== strpos( $html, 'پوینت لایت' ) && false !== strpos( $html, 'آلفا' ) );
	es_check( 'AC-02: نوع/شهر پروژه از taxonomy می‌آید', false !== strpos( $html, 'تونل نوری' ) && false !== strpos( $html, 'اصفهان' ) );

	// AC-03 / SC-01: accordion keys + inner tab keys align for main.js.
	es_check( 'AC-03: آکاردئون محصولات کلید products دارد', false !== strpos( $html, 'data-mobile-acc-btn="products"' ) && false !== strpos( $html, 'data-mobile-acc-panel="products"' ) );
	es_check( 'SC-01: تب نوع/شهر در موبایل حاضر است', false !== strpos( $html, 'data-mobile-proj-tab="type"' ) && false !== strpos( $html, 'data-mobile-proj-tab="city"' ) );

	// SC-02: item count follows the per_tab option.
	Store::set( 'mega_products_per_tab', 2 );
	$html = $render();
	es_check( 'SC-02: per_tab=2 آیتم سوم را حذف می‌کند', false !== strpos( $html, 'آلفا' ) && false === strpos( $html, 'گاما' ) );
	Store::delete( 'mega_products_per_tab' );
	Store::delete( 'mega_projects_group' );

	// Cleanup.
	wp_delete_nav_menu( $menu_id );
	set_theme_mod( 'nav_menu_locations', $old_locs );
	foreach ( array( $c1, $c2, $c3, $top ) as $t ) { wp_delete_term( $t['term_id'], 'product_cat' ); }
	wp_delete_term( $pt['term_id'], 'project_type' );
	wp_delete_term( $ct['term_id'], 'city' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
