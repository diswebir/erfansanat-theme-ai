<?php
/** Task 3.5 — versioned rewrite flushing and privacy-conscious URL audit. */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! defined( 'ES_REWRITE_VERSION' ) ) { define( 'ES_REWRITE_VERSION', '2' ); }
$GLOBALS['es_phase3_actions'] = $GLOBALS['es_phase3_actions'] ?? array();
$GLOBALS['es_phase3_options'] = array( 'es_rewrite_version' => '1' );
$GLOBALS['es_phase3_flush_calls'] = 0;
$GLOBALS['es_phase3_submenus'] = array();
$GLOBALS['es_phase3_is_404'] = false;
if ( ! function_exists( 'add_action' ) ) { function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_actions'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
if ( ! function_exists( 'add_filter' ) ) { function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_filters'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $name, $default = false ) { return $GLOBALS['es_phase3_options'][ $name ] ?? $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $name, $value, $autoload = null ) { $GLOBALS['es_phase3_options'][ $name ] = $value; return true; } }
if ( ! function_exists( 'flush_rewrite_rules' ) ) { function flush_rewrite_rules( $hard = true ) { $GLOBALS['es_phase3_flush_calls']++; } }
if ( ! function_exists( 'is_404' ) ) { function is_404() { return ! empty( $GLOBALS['es_phase3_is_404'] ); } }
if ( ! function_exists( 'is_admin' ) ) { function is_admin() { return ! empty( $GLOBALS['es_phase3_admin'] ); } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability, $object_id = null ) { return in_array( $capability, array( 'manage_options', 'edit_post', 'manage_product_terms', 'edit_terms', 'edit_posts' ), true ); } }
if ( ! function_exists( 'add_submenu_page' ) ) { function add_submenu_page( $parent, $page_title, $menu_title, $capability, $slug, $callback ) { $GLOBALS['es_phase3_submenus'][ $slug ] = compact( 'parent', 'page_title', 'menu_title', 'capability', 'slug', 'callback' ); return $slug; } }
if ( ! function_exists( 'home_url' ) ) { function home_url( $path = '/' ) { return 'https://example.test' . '/' . ltrim( $path, '/' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $value, $domain = 'default' ) { return esc_html( $value ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : ''; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'admin_url' ) ) { function admin_url( $path = '' ) { return '/wp-admin/' . ltrim( $path, '/' ); } }
if ( ! function_exists( 'wp_nonce_field' ) ) { function wp_nonce_field( $action, $name ) { return '<input type="hidden" name="' . esc_html( $name ) . '" value="test">'; } }
if ( ! function_exists( 'wp_nonce_url' ) ) { function wp_nonce_url( $url, $action ) { return $url . '&_wpnonce=test'; } }
if ( ! function_exists( '__' ) ) { function __( $text, $domain = 'default' ) { return $text; } }

$rewrite_file = ES_REPO_ROOT . '/theme/inc/Content/Rewrites.php';
$audit_file = ES_REPO_ROOT . '/theme/inc/Admin/UrlAudit.php';
$available = is_file( $rewrite_file ) && is_file( $audit_file );
if ( $available ) {
	require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
	require_once $rewrite_file;
	require_once $audit_file;
	( new \ErfanSanat\Theme\Content\Rewrites() )->register();
	( new \ErfanSanat\Theme\Admin\UrlAudit() )->register();
}

if ( $available ) { \ErfanSanat\Theme\Content\Rewrites::maybe_flush_rewrites(); \ErfanSanat\Theme\Content\Rewrites::maybe_flush_rewrites(); }
$report = $available ? \ErfanSanat\Theme\Admin\UrlAudit::render() : '';
$_SERVER['REQUEST_URI'] = '/missing/archive/?email=private@example.test';
$GLOBALS['es_phase3_is_404'] = true;
if ( $available ) { \ErfanSanat\Theme\Admin\UrlAudit::record_404(); }
$logs = $GLOBALS['es_phase3_options']['es_404_audit'] ?? array();

check( 'rewrite version change flushes once and stores the version', fn() => $available && 1 === $GLOBALS['es_phase3_flush_calls'] && ES_REWRITE_VERSION === $GLOBALS['es_phase3_options']['es_rewrite_version'] );
check( 'a repeated activation/version check does not flush again', fn() => 1 === $GLOBALS['es_phase3_flush_calls'] && count( $GLOBALS['es_phase3_actions']['after_switch_theme'] ?? array() ) > 0 );
check( 'URL audit report covers all six stable URL families', function () use ( $report ) { foreach ( array( 'shop', 'product_cat', 'projects', 'city', 'project_type', 'product' ) as $family ) { if ( ! str_contains( $report, $family ) ) { return false; } } return true; } );
check( '404 log keeps only a sanitized path and never records query, cookies, or IP', function () use ( $logs ) { $encoded = json_encode( $logs ); return is_array( $logs ) && isset( $logs['/missing/archive/']['path'] ) && '/missing/archive/' === $logs['/missing/archive/']['path'] && ! str_contains( $encoded, 'private@example.test' ) && ! str_contains( $encoded, 'REMOTE_ADDR' ); } );
summary();
