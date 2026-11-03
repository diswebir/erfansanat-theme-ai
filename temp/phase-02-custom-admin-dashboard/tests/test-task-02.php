<?php
/** Task 2.2 — privileged admin menu and scoped RTL dashboard assets. */
declare(strict_types=1);

$bootstrap_candidates = array(
	__DIR__ . '/../../../../temp/tests/bootstrap.php',
	__DIR__ . '/../../tests/bootstrap.php',
);
foreach ( $bootstrap_candidates as $bootstrap ) { if ( is_file( $bootstrap ) ) { require_once $bootstrap; break; } }
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }

$GLOBALS['es_mock_user_can'] = true;
$GLOBALS['es_mock_admin_pages'] = array();
$GLOBALS['es_mock_enqueued'] = array();
$GLOBALS['es_wp_media_enqueued'] = 0;
if ( ! function_exists( 'wp_enqueue_media' ) ) { function wp_enqueue_media() { $GLOBALS['es_wp_media_enqueued']++; } }
$GLOBALS['es_mock_actions'] = $GLOBALS['es_mock_actions'] ?? array();
if ( ! function_exists( 'add_action' ) ) { function add_action( $name, $callback ) { $GLOBALS['es_mock_actions'][ $name ][] = $callback; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $cap ) { return ! empty( $GLOBALS['es_mock_user_can'] ) && 'manage_options' === $cap; } }
if ( ! function_exists( 'add_menu_page' ) ) { function add_menu_page( ...$args ) { $GLOBALS['es_mock_admin_pages'][] = array( 'kind' => 'top', 'args' => $args ); return 'toplevel_page_' . $args[3]; } }
if ( ! function_exists( 'add_submenu_page' ) ) { function add_submenu_page( ...$args ) { $GLOBALS['es_mock_admin_pages'][] = array( 'kind' => 'sub', 'args' => $args ); return 'erfansanat-theme_page_' . $args[4]; } }
if ( ! function_exists( 'wp_enqueue_style' ) ) { function wp_enqueue_style( ...$args ) { $GLOBALS['es_mock_enqueued'][] = array( 'style', $args ); } }
if ( ! function_exists( 'wp_enqueue_script' ) ) { function wp_enqueue_script( ...$args ) { $GLOBALS['es_mock_enqueued'][] = array( 'script', $args ); } }
if ( ! function_exists( 'is_rtl' ) ) { function is_rtl() { return true; } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $s, $domain = null ) { return esc_html( $s ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'admin_url' ) ) { function admin_url( $s = '' ) { return '/wp-admin/' . ltrim( $s, '/' ); } }
if ( ! function_exists( 'get_template_directory_uri' ) ) { function get_template_directory_uri() { return '/theme'; } }

$class_file = ES_REPO_ROOT . '/theme/inc/Admin/AdminMenu.php';
$class_available = is_file( $class_file );
if ( $class_available ) {
	require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
	require_once $class_file;
}

echo "== Task 2.2: admin menu and dashboard shell ==\n";
check( 'admin menu module exists', fn() => $class_available && class_exists( '\\ErfanSanat\\Theme\\Admin\\AdminMenu' ) );
check( 'admin CSS and JS assets exist', fn() => is_file( ES_REPO_ROOT . '/theme/assets/admin/admin.css' ) && is_file( ES_REPO_ROOT . '/theme/assets/admin/admin.js' ) );

if ( $class_available && class_exists( '\\ErfanSanat\\Theme\\Admin\\AdminMenu' ) ) {
	$class = '\\ErfanSanat\\Theme\\Admin\\AdminMenu';
	$module = new $class();
	$module->register();
	check( 'registers only the admin menu and conditional asset hooks', fn() => count( $GLOBALS['es_mock_actions']['admin_menu'] ?? array() ) > 0 && count( $GLOBALS['es_mock_actions']['admin_enqueue_scripts'] ?? array() ) > 0 );
	$class::register_pages();
	$top = array_values( array_filter( $GLOBALS['es_mock_admin_pages'], fn( $p ) => 'top' === $p['kind'] ) );
	$subs = array_values( array_filter( $GLOBALS['es_mock_admin_pages'], fn( $p ) => 'sub' === $p['kind'] ) );
	check( 'top-level menu requires manage_options and has the expected slug', fn() => count( $top ) === 1 && $top[0]['args'][2] === 'manage_options' && $top[0]['args'][3] === 'erfansanat-theme' );
	check( 'dashboard, global, brand, archives, sections, forms and tools submenus are registered', function () use ( $subs ) {
		$slugs = array_map( fn( $p ) => $p['args'][4], $subs );
		return count( array_intersect( array( 'erfansanat-theme', 'es-theme-settings', 'es-theme-brand', 'es-theme-archives', 'es-theme-sections', 'es-theme-forms', 'es-theme-tools' ), $slugs ) ) === 7
			&& count( $subs ) === 7 && array_reduce( $subs, fn( $ok, $p ) => $ok && 'manage_options' === $p['args'][3], true );
	} );
	$GLOBALS['es_mock_admin_pages'] = array();
	$GLOBALS['es_mock_user_can'] = false;
	$class::register_pages();
	check( 'unauthorized user does not register admin pages', fn() => count( $GLOBALS['es_mock_admin_pages'] ) === 0 );
	$GLOBALS['es_mock_user_can'] = true;

	$class::enqueue_assets( 'plugins.php' );
	check( 'theme assets and media library stay off unrelated admin pages', fn() => count( $GLOBALS['es_mock_enqueued'] ) === 0 && $GLOBALS['es_wp_media_enqueued'] === 0 );
	$class::enqueue_assets( 'toplevel_page_erfansanat-theme' );
	check( 'admin CSS, dashboard, media and repeater JS enqueue only on the theme dashboard', fn() => count( $GLOBALS['es_mock_enqueued'] ) === 4 && $GLOBALS['es_mock_enqueued'][0][0] === 'style' && $GLOBALS['es_mock_enqueued'][1][0] === 'script' && $GLOBALS['es_mock_enqueued'][2][1][0] === 'es-admin-media' && $GLOBALS['es_mock_enqueued'][3][1][0] === 'es-admin-repeater' && $GLOBALS['es_wp_media_enqueued'] === 1 );
	$callback = $top[0]['args'][4] ?? null;
	if ( is_callable( $callback ) ) { ob_start(); $callback(); $html = ob_get_clean(); } else { $html = ''; }
	check( 'dashboard renderer outputs an escaped RTL admin shell', fn() => str_contains( $html, 'class="es-admin' ) && str_contains( $html, 'dir="rtl"' ) );
} else {
	foreach ( array( 'registers only the admin menu and conditional asset hooks', 'top-level menu requires manage_options and has the expected slug', 'dashboard, global, brand, archives, sections, forms and tools submenus are registered', 'unauthorized user does not register admin pages', 'assets are not enqueued on unrelated admin pages', 'CSS and JS enqueue on the theme dashboard screen', 'dashboard renderer outputs an escaped RTL admin shell' ) as $label ) { check( $label, fn() => false ); }
}

function capability_index() { return 2; }
summary();
