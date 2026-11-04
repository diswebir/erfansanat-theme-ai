<?php
/** Task 2.14 — safe settings export/import and scoped reset. */
declare(strict_types=1);

require_once __DIR__ . '/../../../../temp/tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_ie_options'] = $GLOBALS['es_ie_options'] ?? array();
$GLOBALS['es_ie_postmeta'] = $GLOBALS['es_ie_postmeta'] ?? array();
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability, $object_id = null ) { return ! empty( $GLOBALS['es_ie_can_manage'] ) && 'manage_options' === $capability; } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $key, $default = false ) { return $GLOBALS['es_ie_options'][ $key ] ?? $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $key, $value, $autoload = null ) { $GLOBALS['es_ie_options'][ $key ] = $value; return true; } }
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $post_id, $key, $single = false ) { return $GLOBALS['es_ie_postmeta'][ $post_id ][ $key ] ?? ( $single ? '' : array() ); } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $post_id, $key, $value ) { $GLOBALS['es_ie_postmeta'][ $post_id ][ $key ] = $value; return true; } }
if ( ! function_exists( 'delete_post_meta' ) ) { function delete_post_meta( $post_id, $key ) { unset( $GLOBALS['es_ie_postmeta'][ $post_id ][ $key ] ); return true; } }
if ( ! function_exists( 'get_page_by_path' ) ) { function get_page_by_path( $slug ) { $ids = array( 'home' => 101, 'about' => 102, 'contact' => 103, 'faq' => 104, 'licenses-and-honors' => 105, 'refund_returns' => 106, 'recruitment' => 107 ); return isset( $ids[ $slug ] ) ? (object) array( 'ID' => $ids[ $slug ], 'post_type' => 'page' ) : null; } }
if ( ! function_exists( 'wp_nonce_field' ) ) { function wp_nonce_field( $action, $name, $referer = true, $display = true ) { return '<input type="hidden" name="' . esc_attr( $name ) . '" value="nonce">'; } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $value, $domain = null ) { return esc_html( $value ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'admin_url' ) ) { function admin_url( $path = '' ) { return '/wp-admin/' . ltrim( $path, '/' ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : ''; } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $value ) { return strip_tags( (string) $value, '<p><a><strong><em><br>' ); } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $value, $protocols = array() ) { return is_scalar( $value ) ? trim( (string) $value ) : ''; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'add_action' ) ) { function add_action( $tag, $callback ) { $GLOBALS['es_ie_actions'][ $tag ][] = $callback; } }

$root = ES_REPO_ROOT . '/theme/';
$admin = $root . 'inc/Admin/';
$required_files = array( $admin . 'ImportExport.php', $root . 'inc/Contracts/ModuleInterface.php', $root . 'inc/Helpers/Defaults.php', $root . 'inc/Helpers/Options.php', $root . 'inc/Admin/Fields/Schema.php', $root . 'inc/Admin/Fields/Sanitizer.php', $root . 'inc/Sections/Registry.php', $root . 'inc/Sections/Store.php' );
$available = true;
foreach ( $required_files as $required_path ) { $available = $available && is_file( $required_path ); }
$menu_source = (string) @file_get_contents( $admin . 'AdminMenu.php' );
$theme_source = (string) @file_get_contents( $root . 'inc/Theme.php' );
$available = $available && str_contains( $menu_source, 'ImportExport::render_settings' ) && str_contains( $theme_source, 'new ImportExport()' );
if ( $available ) {
	require_once $root . 'inc/Contracts/ModuleInterface.php';
	require_once $root . 'inc/Helpers/Defaults.php';
	require_once $root . 'inc/Helpers/Options.php';
	require_once $admin . 'Fields/Schema.php';
	require_once $admin . 'Fields/Sanitizer.php';
	require_once $root . 'inc/Sections/Registry.php';
	require_once $root . 'inc/Sections/Store.php';
	require_once $admin . 'AjaxSave.php';
	require_once $admin . 'ImportExport.php';
}

echo "== Task 2.14: safe settings export/import and scoped reset ==\n";
check( 'import/export module exists', fn() => $available );
if ( $available ) {
	$GLOBALS['es_ie_can_manage'] = true;
	\ErfanSanat\Theme\Admin\Fields\Schema::reset();
	\ErfanSanat\Theme\Admin\Fields\Schema::register_many( array(
		array( 'id' => 'header.phone', 'type' => 'text', 'label' => 'Phone', 'default' => '' ),
		array( 'id' => 'footer.address', 'type' => 'text', 'label' => 'Address', 'default' => '' ),
	) );
	$GLOBALS['es_ie_can_manage'] = true;
	$GLOBALS['es_mock_user_can'] = true;
	$GLOBALS['es_mock_options']['page_on_front'] = 101;
	update_option( 'page_on_front', 101, false );
	update_option( 'erfansanat_theme_options', array( 'header' => array( 'phone' => '555-0100' ), 'footer' => array( 'address' => 'Old address' ), 'unknown_group' => array( 'bad' => true ) ), false );
	update_post_meta( 101, '_es_sections', array( 'order' => array( 'projects', 'hero' ), 'enabled' => array( 'hero' => false ), 'data' => array( 'hero' => array( 'title' => 'Exported headline' ) ), 'available' => array() ) );
	\ErfanSanat\Theme\Helpers\Options::reset();
	$export = \ErfanSanat\Theme\Admin\ImportExport::export_json();
	$payload = json_decode( $export, true );
	update_option( 'erfansanat_theme_options', array( 'header' => array( 'phone' => '999' ), 'footer' => array( 'address' => 'Keep me' ) ), false );
	update_post_meta( 101, '_es_sections', array() );
	\ErfanSanat\Theme\Helpers\Options::reset();
	$imported = \ErfanSanat\Theme\Admin\ImportExport::import_json( $export );
	$round_trip_options = get_option( 'erfansanat_theme_options', array() );
	$round_trip_sections = get_post_meta( 101, '_es_sections', true );
	check( 'export and import round-trip settings plus section state and create a backup', fn() => 'erfansanat-theme-settings' === ( $payload['format'] ?? '' ) && $imported && $round_trip_options === array( 'header' => array( 'phone' => '555-0100' ), 'footer' => array( 'address' => 'Old address' ) ) && false === $round_trip_sections['enabled']['hero'] && 'Exported headline' === $round_trip_sections['data']['hero']['title'] && '999' === get_option( 'erfansanat_theme_options_backup', array() )['header']['phone'] );

	$malicious = json_encode( array( 'format' => 'erfansanat-theme-settings', 'version' => 1, 'options' => array( 'header' => array( 'phone' => array( 'bad' ) ), 'unknown_group' => array( 'payload' => '<script>' ) ), 'sections' => array( 'not-registered' => array( 'data' => array( 'x' => '<script>' ) ) ) ) );
	$before_bad = get_option( 'erfansanat_theme_options', array() );
	$bad_result = \ErfanSanat\Theme\Admin\ImportExport::import_json( (string) $malicious );
	$invalid_result = \ErfanSanat\Theme\Admin\ImportExport::import_json( '{broken json' );
	check( 'malformed JSON and unknown or wrongly typed values fail safely', fn() => $bad_result && ! $invalid_result && get_option( 'erfansanat_theme_options', array() ) === $before_bad && ! isset( get_option( 'erfansanat_theme_options', array() )['unknown_group'] ) );

	update_option( 'erfansanat_theme_options', array( 'header' => array( 'phone' => '999' ), 'footer' => array( 'address' => 'Preserve footer' ) ), false );
	\ErfanSanat\Theme\Helpers\Options::reset();
	$reset = \ErfanSanat\Theme\Admin\ImportExport::reset_group( 'header' );
	$header_default = \ErfanSanat\Theme\Helpers\Options::get( 'header.phone' );
	$footer_preserved = \ErfanSanat\Theme\Helpers\Options::get( 'footer.address' );
	$screen = \ErfanSanat\Theme\Admin\ImportExport::render_settings();
	check( 'reset affects only the chosen group and asks for confirmation', fn() => $reset && '031-91091011' === $header_default && 'Preserve footer' === $footer_preserved && str_contains( $screen, 'required' ) && str_contains( $screen, 'name="nonce"' ) );
} else {
	check( 'export and import round-trip settings plus section state and create a backup', fn() => false );
	check( 'malformed JSON and unknown or wrongly typed values fail safely', fn() => false );
	check( 'reset affects only the chosen group and asks for confirmation', fn() => false );
}
summary();
