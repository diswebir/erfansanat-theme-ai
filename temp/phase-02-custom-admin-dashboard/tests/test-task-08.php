<?php
/** Task 2.8 — safe brand color option and conditional inline CSS. */
declare(strict_types=1);

$bootstrap_candidates = array( __DIR__ . '/../../../../temp/tests/bootstrap.php', __DIR__ . '/../../tests/bootstrap.php' );
foreach ( $bootstrap_candidates as $bootstrap ) { if ( is_file( $bootstrap ) ) { require_once $bootstrap; break; } }
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $s, $domain = null ) { return esc_html( $s ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $s ) { return preg_replace('/[^a-z0-9_\\-]/', '', strtolower((string)$s)); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return is_scalar( $v ) ? trim(strip_tags((string)$v)) : ''; } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $v ) { return strip_tags((string)$v, '<p><a><strong><em><br>'); } }
if ( ! function_exists( 'sanitize_hex_color' ) ) { function sanitize_hex_color( $v ) { return preg_match('/^#[a-f0-9]{3}(?:[a-f0-9]{3})?$/i', (string)$v) ? (string)$v : null; } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $v ) { return preg_match('~^https?://~i', (string)$v) ? filter_var($v, FILTER_SANITIZE_URL) : ''; } }
if ( ! function_exists( 'sanitize_email' ) ) { function sanitize_email( $v ) { return filter_var((string)$v, FILTER_SANITIZE_EMAIL) ?: ''; } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs((int)$v); } }
if ( ! function_exists( 'sanitize_html_class' ) ) { function sanitize_html_class( $v ) { return preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$v); } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $key, $default = false ) { return $GLOBALS['es_mock_options'][$key] ?? $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $key, $value, $autoload = null ) { $GLOBALS['es_mock_options'][$key] = $value; return true; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability ) { return 'manage_options' === $capability; } }
if ( ! function_exists( 'is_rtl' ) ) { function is_rtl() { return true; } }
if ( ! function_exists( 'wp_nonce_field' ) ) { function wp_nonce_field( $action, $name, $referer = true, $display = true ) { return '<input type="hidden" name="' . esc_attr( $name ) . '" value="nonce">'; } }

$root = ES_REPO_ROOT . '/theme/';
$admin = $root . 'inc/Admin/';
$available = is_file( $admin . 'Tabs/BrandTab.php' ) && is_file( $root . 'inc/Frontend/InlineCss.php' );
if ( $available ) {
	require_once $root . 'inc/Helpers/Defaults.php';
	require_once $root . 'inc/Helpers/Options.php';
	require_once $root . 'inc/Contracts/ModuleInterface.php';
	require_once $admin . 'Fields/Schema.php';
	require_once $admin . 'Fields/Sanitizer.php';
	require_once $admin . 'Fields/FieldRenderer.php';
	require_once $admin . 'Tabs/BrandTab.php';
	require_once $admin . 'AjaxSave.php';
	require_once $root . 'inc/Frontend/InlineCss.php';
}

echo "== Task 2.8: brand color inline CSS ==\n";
check( 'brand settings and conditional inline CSS modules exist', fn() => $available );
if ( $available ) {
	\ErfanSanat\Theme\Admin\Fields\Schema::reset();
	( new \ErfanSanat\Theme\Admin\Tabs\BrandTab() )->register();
	$GLOBALS['es_mock_options']['erfansanat_theme_options'] = array();
	\ErfanSanat\Theme\Helpers\Options::reset();
	check( 'static emerald default emits no extra style tag', fn() => \ErfanSanat\Theme\Frontend\InlineCss::render() === '' );
	$GLOBALS['es_mock_user_can'] = true;
	$settings = \ErfanSanat\Theme\Admin\Tabs\BrandTab::render_settings();
	check( 'brand color option appears in an accessible nonce-protected form', fn() => str_contains( $settings, 'data-es-options-form' ) && str_contains( $settings, 'es-settings-panel-brand' ) && str_contains( $settings, 'name="nonce"' ) && str_contains( $settings, 'brand][color' ) );
	\ErfanSanat\Theme\Helpers\Options::update( 'brand.color', '#2563eb' );
	$css = \ErfanSanat\Theme\Frontend\InlineCss::render();
	check( 'custom valid color emits only a scoped CSS variable without important', fn() => str_contains( $css, '--es-brand:#2563eb' ) && str_contains( $css, '#es-page.es-site' ) && ! str_contains( $css, '!important' ) );
	$clean = \ErfanSanat\Theme\Admin\AjaxSave::whitelisted_values( array( 'brand' => array( 'color' => 'url(javascript:alert(1))' ) ) );
	check( 'invalid color is omitted from AJAX updates and previous stored color remains', fn() => ! array_key_exists( 'brand.color', $clean ) && \ErfanSanat\Theme\Helpers\Options::get( 'brand.color' ) === '#2563eb' );
} else {
	for ( $i = 1; $i <= 4; $i++ ) { check( 'brand color condition ' . $i, fn() => false ); }
}
summary();
