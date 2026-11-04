<?php
/** Task 4.8 — dynamic multi-level mobile drawer with static fallback. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_phase4_mobile_items'] = array(
	(object) array( 'ID' => 71, 'menu_item_parent' => 0, 'title' => 'محصولات', 'url' => '/products/' ),
	(object) array( 'ID' => 72, 'menu_item_parent' => 71, 'title' => 'چراغ شهری', 'url' => '/urban/' ),
	(object) array( 'ID' => 73, 'menu_item_parent' => 72, 'title' => 'چراغ خیابانی', 'url' => '/street/' ),
	(object) array( 'ID' => 74, 'menu_item_parent' => 0, 'title' => 'تماس', 'url' => '/contact/' ),
);
function absint( $value ) { return abs( (int) $value ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value, $protocols = null ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function has_nav_menu( $location ) { return 'mobile-menu' === $location; }
function get_nav_menu_locations() { return array( 'mobile-menu' => 88 ); }
function wp_get_nav_menu_items( $id, $args = array() ) { return $GLOBALS['es_phase4_mobile_items']; }
require_once ES_REPO_ROOT . '/theme/inc/Nav/Icons.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/MobileMenuWalker.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/LegacyMarkup.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/HeaderMarkup.php';
$static = '<header data-navbar-header="true"><div data-mobile-menu="true" aria-hidden="true" class="hidden"><div data-mobile-backdrop="true"></div><aside data-mobile-sidebar="true" aria-label="منوی موبایل"><button data-mobile-close="true" type="button">بستن</button><div class="flex-1 space-y-2.5 overflow-y-auto overscroll-contain px-3.5 py-3.5"><a href="/fallback/">لینک پیش‌فرض</a></div></aside></div></header>';
$dynamic = \ErfanSanat\Theme\Nav\HeaderMarkup::render( $static );
$js = (string) file_get_contents( ES_REPO_ROOT . '/theme/assets/js/main.js' );
$GLOBALS['es_phase4_mobile_items'] = array();
$fallback = \ErfanSanat\Theme\Nav\HeaderMarkup::render( $static );
check( 'دکمه، aside، backdrop و aria hooks برای main.js حفظ می‌شوند', fn() => str_contains( $dynamic, 'data-mobile-menu="true" aria-hidden="true" class="hidden"' ) && str_contains( $dynamic, 'data-mobile-sidebar="true"' ) && str_contains( $dynamic, 'data-mobile-backdrop="true"' ) && str_contains( $dynamic, 'data-mobile-close="true"' ) && str_contains( $js, '[data-mobile-toggle="true"]' ) && str_contains( $js, '[data-mobile-close="true"]' ) );
check( 'منوی موبایل چندسطحی با data-mobile-acc hooks ساخته می‌شود', fn() => str_contains( $dynamic, 'data-mobile-acc-btn="71"' ) && str_contains( $dynamic, 'data-mobile-acc-panel="72"' ) && str_contains( $dynamic, 'چراغ خیابانی' ) && str_contains( $dynamic, 'href="/street/"' ) );
check( 'وقتی mobile-menu خالی یا تخصیص‌نیافته باشد، لینک استاتیک باقی می‌ماند', fn() => str_contains( $fallback, 'لینک پیش‌فرض' ) && ! str_contains( $fallback, 'data-mobile-dynamic-menu="true"' ) && '' === \ErfanSanat\Theme\Nav\MobileMenuWalker::render( array() ) );
summary();
