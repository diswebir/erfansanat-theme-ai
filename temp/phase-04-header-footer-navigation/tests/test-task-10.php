<?php
/** Task 4.10 — WooCommerce cart fragments and safe no-Woo fallback. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_phase4_cart_hooks'] = array();
$GLOBALS['es_phase4_scripts'] = array();
$GLOBALS['es_phase4_wc'] = null;
$GLOBALS['es_phase4_fragments_enqueued'] = false;
class Phase4FakeCart { public int $count = 0; public function get_cart_contents_count() { return $this->count; } }
function add_filter( $hook, $callback, $priority = 10, $accepted = 1 ) { $GLOBALS['es_phase4_cart_hooks']['filter'][ $hook ] = $callback; }
function add_action( $hook, $callback, $priority = 10, $accepted = 1 ) { $GLOBALS['es_phase4_cart_hooks']['action'][ $hook ] = $callback; }
function WC() { return $GLOBALS['es_phase4_wc']; }
function absint( $value ) { return abs( (int) $value ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function number_format_i18n( $number, $decimals = 0 ) { return strtr( (string) $number, array( '0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹' ) ); }
function is_admin() { return false; }
function get_template_directory_uri() { return 'https://example.test/theme'; }
function wp_script_is( $handle, $status = 'enqueued' ) { return 'wc-cart-fragments' === $handle && ( 'registered' === $status || ( 'enqueued' === $status && $GLOBALS['es_phase4_fragments_enqueued'] ) ); }
function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $footer = false ) { $GLOBALS['es_phase4_scripts'][] = $handle; if ( 'wc-cart-fragments' === $handle ) { $GLOBALS['es_phase4_fragments_enqueued'] = true; } }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Woo/HeaderCart.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/LegacyMarkup.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/HeaderMarkup.php';
$module = new \ErfanSanat\Theme\Woo\HeaderCart();
$module->register();
$cart = new Phase4FakeCart(); $cart->count = 5; $GLOBALS['es_phase4_wc'] = (object) array( 'cart' => $cart );
$added = \ErfanSanat\Theme\Woo\HeaderCart::fragments( array() );
$cart->count = 2;
$removed = \ErfanSanat\Theme\Woo\HeaderCart::fragments( array() );
$hydrated_modal = \ErfanSanat\Theme\Woo\HeaderCart::hydrate_modal( '<div><span data-cart-modal-count="true">old</span></div>' );
\ErfanSanat\Theme\Woo\HeaderCart::enqueue_assets();
\ErfanSanat\Theme\Woo\HeaderCart::enqueue_assets();
$GLOBALS['es_phase4_wc'] = null;
$no_wc_markup = \ErfanSanat\Theme\Nav\HeaderMarkup::render( '<header><span data-header-cart-badge="true" class="absolute">۲</span></header>' );
check( 'افزودن و حذف کالا شمارنده را از cart contents تازه می‌خواند', fn() => str_contains( $added['span[data-header-cart-badge="true"]'], 'data-wc-cart-count="۵"' ) && str_contains( $removed['span[data-header-cart-badge="true"]'], 'data-wc-cart-count="۲"' ) );
check( 'Fragments badge و شمارنده مودال را جداگانه با شمار واقعی جایگزین می‌کنند', fn() => isset( $added['span[data-header-cart-badge="true"]'], $added['[data-cart-modal-count="true"]'] ) && str_contains( $added['[data-cart-modal-count="true"]'], 'data-wc-cart-count="۵"' ) && str_contains( $hydrated_modal, 'data-wc-cart-count="۲"' ) && str_contains( $hydrated_modal, '۲ کالا' ) && isset( $GLOBALS['es_phase4_cart_hooks']['filter']['woocommerce_add_to_cart_fragments'] ) );
check( 'اسکریپت fragments فقط یک‌بار enqueue می‌شود و adapter به es-main وابسته است', fn() => 1 === count( array_filter( $GLOBALS['es_phase4_scripts'], fn( $h ) => 'wc-cart-fragments' === $h ) ) && 2 === count( array_filter( $GLOBALS['es_phase4_scripts'], fn( $h ) => 'es-header-cart' === $h ) ) );
check( 'نبود ووکامرس بدون خطا، شمارنده صفر و markup سالم می‌دهد', fn() => 0 === \ErfanSanat\Theme\Woo\HeaderCart::cart_count() && str_contains( $no_wc_markup, 'data-wc-cart-count="۰"' ) && str_contains( $no_wc_markup, 'class="absolute' ) );
summary();
