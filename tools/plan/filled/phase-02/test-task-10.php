<?php
/** Task 2.10 — privileged custom scripts with safe, conditional frontend output. */
declare(strict_types=1);

$bootstrap_candidates = array( __DIR__ . '/../../../../temp/tests/bootstrap.php', __DIR__ . '/../../tests/bootstrap.php' );
foreach ( $bootstrap_candidates as $bootstrap ) { if ( is_file( $bootstrap ) ) { require_once $bootstrap; break; } }
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $s, $domain = null ) { return esc_html( $s ); } }
if ( ! function_exists( 'esc_textarea' ) ) { function esc_textarea( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $s ) { return preg_replace('/[^a-z0-9_\\-]/', '', strtolower((string)$s)); } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs((int)$v); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return is_scalar( $v ) ? trim(strip_tags((string)$v)) : ''; } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $v ) { return strip_tags((string)$v, '<p><a><strong><em><br>'); } }
if ( ! function_exists( 'wp_kses' ) ) { function wp_kses( $v, $allowed_html, $allowed_protocols = array() ) { return is_scalar( $v ) ? strip_tags( (string) $v, '<script>' ) : ''; } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $key, $default = false ) { return $GLOBALS['es_mock_options'][$key] ?? $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $key, $value, $autoload = null ) { $GLOBALS['es_mock_options'][$key] = $value; return true; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability ) { return ! empty( $GLOBALS['es_mock_user_can'] ) && 'manage_options' === $capability; } }
if ( ! function_exists( 'is_admin' ) ) { function is_admin() { return ! empty( $GLOBALS['es_mock_admin_context'] ); } }
if ( ! function_exists( 'is_rtl' ) ) { function is_rtl() { return true; } }
if ( ! function_exists( 'wp_nonce_field' ) ) { function wp_nonce_field( $action, $name, $referer = true, $display = true ) { return '<input type="hidden" name="' . esc_attr( $name ) . '" value="nonce">'; } }

$root = ES_REPO_ROOT . '/theme/';
$admin = $root . 'inc/Admin/';
$available = is_file( $admin . 'Tabs/ScriptsTab.php' ) && is_file( $root . 'inc/Frontend/CustomCode.php' );
if ( $available ) {
	require_once $root . 'inc/Helpers/Defaults.php';
	require_once $root . 'inc/Helpers/Options.php';
	require_once $root . 'inc/Helpers/CustomCodeSanitizer.php';
	require_once $root . 'inc/Contracts/ModuleInterface.php';
	require_once $admin . 'Fields/Schema.php';
	require_once $admin . 'Fields/Sanitizer.php';
	require_once $admin . 'Fields/FieldRenderer.php';
	require_once $admin . 'Tabs/ScriptsTab.php';
	require_once $admin . 'AjaxSave.php';
	require_once $root . 'inc/Frontend/CustomCode.php';
}

echo "== Task 2.10: custom scripts ==\n";
check( 'scripts settings and frontend injector modules exist', fn() => $available );
if ( $available ) {
	\ErfanSanat\Theme\Admin\Fields\Schema::reset();
	( new \ErfanSanat\Theme\Admin\Tabs\ScriptsTab() )->register();
	$GLOBALS['es_mock_options']['erfansanat_theme_options'] = array();
	\ErfanSanat\Theme\Helpers\Options::reset();
	$empty = \ErfanSanat\Theme\Frontend\CustomCode::head_markup() === '' && \ErfanSanat\Theme\Frontend\CustomCode::footer_markup() === '';
	check( 'empty script settings generate zero frontend output', fn() => $empty );
	$GLOBALS['es_mock_options']['erfansanat_theme_options'] = array( 'scripts' => array( 'head' => '<script>window.analyticsReady = true;</script>', 'footer' => '<script>window.widgetReady = true;</script>' ) );
	\ErfanSanat\Theme\Helpers\Options::reset();
	$head = \ErfanSanat\Theme\Frontend\CustomCode::head_markup();
	$foot = \ErfanSanat\Theme\Frontend\CustomCode::footer_markup();
	$GLOBALS['es_mock_admin_context'] = true;
	$admin_head = \ErfanSanat\Theme\Frontend\CustomCode::head_markup();
	$GLOBALS['es_mock_admin_context'] = false;
	check( 'saved scripts render in head/footer slots on frontend only', fn() => str_contains( $head, 'window.analyticsReady' ) && str_contains( $foot, 'window.widgetReady' ) && $admin_head === '' );
	$injector_source = (string) file_get_contents( $root . 'inc/Frontend/CustomCode.php' );
	check( 'head and footer snippets attach to wp_head and wp_footer at late priority', fn() => str_contains( $injector_source, "add_action( 'wp_head'" ) && str_contains( $injector_source, "add_action( 'wp_footer'" ) && substr_count( $injector_source, ', 99 )' ) === 2 );
	$filtered = \ErfanSanat\Theme\Helpers\CustomCodeSanitizer::sanitize( '<script>window.safe = true;</script><img src=x onerror=alert(1)>' );
	check( 'custom KSES policy retains script tags while dropping unrelated HTML', fn() => str_contains( $filtered, '<script>' ) && ! str_contains( $filtered, '<img' ) && ! str_contains( $filtered, 'onerror=' ) );
	$GLOBALS['es_mock_user_can'] = false;
	$blocked = \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( \ErfanSanat\Theme\Admin\Fields\Schema::get( 'scripts.head' ), '<script>window.nope=true;</script>' );
	$ajax_source = (string) file_get_contents( $admin . 'AjaxSave.php' );
	$GLOBALS['es_mock_user_can'] = true;
	check( 'custom code sanitizer and AJAX endpoint require manage_options', fn() => $blocked === '' && str_contains( $ajax_source, "current_user_can( 'manage_options' )" ) );
	$GLOBALS['es_mock_admin_context'] = false;
	$GLOBALS['es_mock_options']['erfansanat_theme_options'] = array();
	\ErfanSanat\Theme\Helpers\Options::reset();
	$screen = \ErfanSanat\Theme\Admin\Tabs\ScriptsTab::render_settings();
	check( 'script form is nonce-protected and renders both custom code fields', fn() => str_contains( $screen, 'name="nonce"' ) && str_contains( $screen, 'data-es-options-form' ) && str_contains( $screen, 'scripts][head' ) && str_contains( $screen, 'scripts][footer' ) );
} else {
	for ( $i = 1; $i <= 4; $i++ ) { check( 'custom script condition ' . $i, fn() => false ); }
}
summary();
