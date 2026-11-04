<?php
/** Task 4.4 — product menu tabs, nested columns, and shared term cache. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_phase4_terms_queries'] = 0;
$GLOBALS['es_phase4_transients'] = array();
$GLOBALS['es_phase4_has_nav_menu'] = false;
$GLOBALS['es_phase4_menu_items'] = array();
function absint( $value ) { return abs( (int) $value ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value, $protocols = null ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function get_post_meta( $id, $key, $single = false ) { return (int) $id === 1 && '_menu_item_es_icon' === $key ? 'cpu' : 'untrusted-svg'; }
function get_transient( $key ) { return $GLOBALS['es_phase4_transients'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl = 0 ) { $GLOBALS['es_phase4_transients'][ $key ] = $value; return true; }
function get_terms( $args ) { ++$GLOBALS['es_phase4_terms_queries']; return array( (object) array( 'term_id' => 10, 'taxonomy' => 'product_cat', 'name' => 'پوینت لایت' ), (object) array( 'term_id' => 11, 'taxonomy' => 'product_cat', 'name' => 'ریسه‌ها' ) ); }
function has_nav_menu( $location ) { return $GLOBALS['es_phase4_has_nav_menu']; }
function get_nav_menu_locations() { return array( 'tolidat-sherkat' => 9 ); }
function wp_get_nav_menu_items( $id, $args = array() ) { return $GLOBALS['es_phase4_menu_items']; }
require_once ES_REPO_ROOT . '/theme/inc/Nav/Icons.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/NavTerms.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/LegacyMarkup.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/MegaMenuWalker.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/ProjectsMegaWalker.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/HeaderMarkup.php';
$items = array(
	(object) array( 'ID' => 1, 'menu_item_parent' => 0, 'title' => 'پوینت لایت', 'url' => 'product_cat/point-light.html' ),
	(object) array( 'ID' => 2, 'menu_item_parent' => 1, 'title' => 'مدل‌ها', 'url' => '#' ),
	(object) array( 'ID' => 3, 'menu_item_parent' => 2, 'title' => 'پیکسل چهار سانتی', 'url' => 'product/pixel-four.html' ),
	(object) array( 'ID' => 4, 'menu_item_parent' => 0, 'title' => 'ریسه‌ها', 'url' => 'product_cat/strings.html' ),
	(object) array( 'ID' => 5, 'menu_item_parent' => 4, 'title' => 'ریسه بلوطی', 'url' => 'product/acorn.html' ),
);
$html = \ErfanSanat\Theme\Nav\MegaMenuWalker::render( $items );
$js = (string) file_get_contents( ES_REPO_ROOT . '/theme/assets/js/main.js' );
$static = '<div data-mega-menu-products="true">static fallback</div><div data-mega-menu-projects="true">project fallback</div>';
$fallback = \ErfanSanat\Theme\Nav\HeaderMarkup::render( $static );
$GLOBALS['es_phase4_menu_items'] = $items;
$GLOBALS['es_phase4_has_nav_menu'] = true;
$dynamic = \ErfanSanat\Theme\Nav\HeaderMarkup::render( $static );
$GLOBALS['es_phase4_has_nav_menu'] = false;
check( 'تب‌ها، پنل‌ها و لینک‌های منو از درخت وردپرس ساخته می‌شوند', fn() => substr_count( $html, 'data-mega-prod-tab=' ) === 2 && substr_count( $html, 'data-mega-prod-panel=' ) === 2 && substr_count( $html, '<a href=' ) === 6 && str_contains( $html, 'پیکسل چهار سانتی' ) );
check( 'ساختار داده با selectorهای main.js سازگار است', fn() => str_contains( $html, 'data-desktop-mega-trigger="products"' ) && str_contains( $js, '[data-mega-prod-tab]' ) && str_contains( $js, '[data-mega-prod-panel]' ) );
check( 'آیکون‌های menu item فقط از SVG whitelist پذیرفته می‌شوند', fn() => str_contains( $html, 'viewBox="0 0 24 24"' ) && ! str_contains( $html, 'untrusted-svg' ) && ! str_contains( $html, '<script' ) );
check( 'نبود منوی سفارشی، مگامنوی استاتیک را حفظ می‌کند', fn() => '' === \ErfanSanat\Theme\Nav\MegaMenuWalker::render( array() ) && str_contains( $fallback, 'static fallback' ) );
check( 'منوی اختصاص‌یافته از Appearance → Menus پنل استاتیک را جایگزین می‌کند', fn() => ! str_contains( $dynamic, 'static fallback' ) && str_contains( $dynamic, 'data-mega-menu-products="true"' ) && str_contains( $dynamic, 'پیکسل چهار سانتی' ) );
check( 'ترم‌های محصول با یک کوئری اشتراکی و transient کش می‌شوند', fn() => count( \ErfanSanat\Theme\Nav\MegaMenuWalker::product_terms() ) === 2 && 1 === $GLOBALS['es_phase4_terms_queries'] && is_array( \ErfanSanat\Theme\Nav\NavTerms::taxonomy( 'city' ) ) );
summary();
