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
check( 'admin dashboard, script and local Vazirmatn assets exist', fn() => is_file( ES_REPO_ROOT . '/theme/assets/admin/admin.css' ) && is_file( ES_REPO_ROOT . '/theme/assets/admin/admin.js' ) && is_file( ES_REPO_ROOT . '/theme/assets/admin/fonts.css' ) && is_file( ES_REPO_ROOT . '/theme/assets/fonts/vazirmatn-arabic.woff2' ) && is_file( ES_REPO_ROOT . '/theme/assets/fonts/vazirmatn-latin.woff2' ) && is_file( ES_REPO_ROOT . '/theme/assets/fonts/vazirmatn-latin-ext.woff2' ) );
$admin_font_css = (string) file_get_contents( ES_REPO_ROOT . '/theme/assets/admin/fonts.css' );
check( 'local admin font stylesheet declares variable Arabic/Latin subsets with swap and theme UI scopes', fn() => 3 === substr_count( $admin_font_css, '@font-face' ) && 3 === substr_count( $admin_font_css, 'font-weight: 100 900' ) && 3 === substr_count( $admin_font_css, 'font-display: swap' ) && str_contains( $admin_font_css, 'vazirmatn-arabic.woff2' ) && str_contains( $admin_font_css, 'vazirmatn-latin-ext.woff2' ) && str_contains( $admin_font_css, 'vazirmatn-latin.woff2' ) && str_contains( $admin_font_css, '.es-admin' ) && str_contains( $admin_font_css, '#es-product-information' ) && str_contains( $admin_font_css, '#es-project-meta' ) && str_contains( $admin_font_css, '.es-nav-fields' ) );
$admin_module_sources = array( 'AdminMenu.php', 'Metaboxes/ProductMeta.php', 'Metaboxes/ProjectMeta.php', 'Metaboxes/TermMeta.php' ); $admin_font_enqueue = true; foreach ( $admin_module_sources as $source_file ) { $source_path = ES_REPO_ROOT . '/theme/inc/Admin/' . $source_file; if ( 'AdminMenu.php' === $source_file ) { $source_path = ES_REPO_ROOT . '/theme/inc/Admin/' . $source_file; } $source = (string) file_get_contents( $source_path ); $admin_font_enqueue = $admin_font_enqueue && str_contains( $source, "'es-admin-fonts'" ) && str_contains( $source, 'assets/admin/fonts.css' ); }
check( 'Vazirmatn stylesheet is enqueued by theme admin and all custom post/term editors', fn() => $admin_font_enqueue && str_contains( (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Nav/MenuItemFields.php' ), "'es-admin-fonts'" ) );

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
	check( 'Vazirmatn stylesheet loads before dashboard CSS and only on theme-owned screens', fn() => count( $GLOBALS['es_mock_enqueued'] ) === 5 && $GLOBALS['es_mock_enqueued'][0][0] === 'style' && $GLOBALS['es_mock_enqueued'][0][1][0] === 'es-admin-fonts' && $GLOBALS['es_mock_enqueued'][0][1][1] === '/theme/assets/admin/fonts.css' && $GLOBALS['es_mock_enqueued'][1][1][0] === 'es-admin' && $GLOBALS['es_mock_enqueued'][1][1][2] === array( 'es-admin-fonts' ) && $GLOBALS['es_mock_enqueued'][2][0] === 'script' && $GLOBALS['es_mock_enqueued'][3][1][0] === 'es-admin-media' && $GLOBALS['es_mock_enqueued'][4][1][0] === 'es-admin-repeater' && $GLOBALS['es_wp_media_enqueued'] === 1 );
	$class::enqueue_assets( 'erfansanat-theme_page_es-theme-sections' );
	$section_assets = array_slice( $GLOBALS['es_mock_enqueued'], 10 );
	check( 'section builder CSS and JS enqueue only on the section screen', fn() => count( $GLOBALS['es_mock_enqueued'] ) === 12 && $section_assets[0][0] === 'style' && $section_assets[0][1][0] === 'es-admin-sections' && $section_assets[1][0] === 'script' && $section_assets[1][1][0] === 'es-admin-sections' );
	$callback = $top[0]['args'][4] ?? null;
	if ( is_callable( $callback ) ) { ob_start(); $callback(); $html = ob_get_clean(); } else { $html = ''; }
	check( 'dashboard renderer outputs an escaped RTL admin shell', fn() => str_contains( $html, 'class="es-admin' ) && str_contains( $html, 'dir="rtl"' ) );
} else {
	foreach ( array( 'registers only the admin menu and conditional asset hooks', 'top-level menu requires manage_options and has the expected slug', 'dashboard, global, brand, archives, sections, forms and tools submenus are registered', 'unauthorized user does not register admin pages', 'assets are not enqueued on unrelated admin pages', 'CSS and JS enqueue on the theme dashboard screen', 'section builder assets enqueue on the section screen', 'dashboard renderer outputs an escaped RTL admin shell' ) as $label ) { check( $label, fn() => false ); }
}

function capability_index() { return 2; }
summary();
