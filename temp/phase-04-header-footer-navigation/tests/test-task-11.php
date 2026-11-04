<?php
/** Task 4.11 — complete main.js DOM contract without changing the protected asset. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function number_format_i18n( $number, $decimals = 0 ) { return (string) $number; }
function absint( $value ) { return abs( (int) $value ); }
function has_nav_menu( $location ) { return false; }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Woo/HeaderCart.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/LegacyMarkup.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/HeaderMarkup.php';
$header = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/static-header.html' );
$mobile = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/header/mobile-menu.html' );
$markup = \ErfanSanat\Theme\Nav\HeaderMarkup::render( $header . '<div data-mobile-menu="true" aria-hidden="true" class="hidden">' . $mobile . '</div>' );
ob_start(); require ES_REPO_ROOT . '/theme/template-parts/header/cart-modal.php'; $cart_modal = (string) ob_get_clean();
$markup .= $cart_modal;
$main = (string) file_get_contents( ES_REPO_ROOT . '/theme/assets/js/main.js' );
$reference = (string) file_get_contents( ES_REPO_ROOT . '/reference/static-mirror/assets/js/main.js' );
$hooks = array(
	'data-navbar-header="true"', 'data-mobile-toggle="true"', 'data-mobile-menu="true"', 'data-mobile-sidebar="true"', 'data-mobile-backdrop="true"', 'data-mobile-close="true"',
	'data-desktop-mega-trigger=', 'data-desktop-mega-panel=', 'data-mega-prod-tab=', 'data-mega-prod-panel=', 'data-mega-proj-tab=', 'data-mega-proj-panel=',
	'data-mobile-acc-btn=', 'data-mobile-acc-panel=', 'data-mobile-acc-icon=', 'data-mobile-prod-tab=', 'data-mobile-prod-panel=', 'data-mobile-proj-tab=', 'data-mobile-proj-panel=',
	'data-search-modal-open=', 'data-search-modal-close=', 'data-search-modal="true"', 'data-global-search-input=', 'data-search-item=', 'data-header-cart-badge="true"',
	'data-cart-modal="true"', 'data-cart-modal-backdrop="true"', 'data-cart-modal-close="true"', 'data-cart-modal-count="true"',
);
$all_hooks = true; foreach ( $hooks as $hook ) { if ( ! str_contains( $markup, $hook ) ) { $all_hooks = false; break; } }
check( 'MD5 فایل اصلی دقیقاً با مرجع استاتیک برابر است', fn() => md5( $main ) === md5( $reference ) );
check( 'همه قراردادهای data-* ناوبار، مگامنو، موبایل، جستجو و مودال سبد حاضرند', fn() => $all_hooks && str_contains( $markup, 'data-cart-modal-count="true"' ) );
check( 'main.js و adapterها بدون خطای ساختاری/تغییر محافظت‌شده باقی مانده‌اند', fn() => ! str_contains( $main, 'console.error("Uncaught' ) && str_contains( $main, 'DOMContentLoaded' ) && file_exists( ES_REPO_ROOT . '/theme/assets/js/nav-config.js' ) && file_exists( ES_REPO_ROOT . '/theme/assets/js/header-cart.js' ) );
summary();
