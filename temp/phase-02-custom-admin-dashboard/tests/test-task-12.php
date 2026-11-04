<?php
/** Task 2.12 — accessible admin section builder. */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability ) { return ! empty( $GLOBALS['es_sections_can_manage'] ) && 'manage_options' === $capability; } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $key, $default = false ) { return 'page_on_front' === $key ? 451 : $default; } }
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $post_id, $key, $single = false ) { return $GLOBALS['es_sections_test_state'] ?? ( $single ? '' : array() ); } }
if ( ! function_exists( 'wp_nonce_field' ) ) { function wp_nonce_field( $action, $name, $referer = true, $display = true ) { return '<input type="hidden" name="' . esc_attr( $name ) . '" value="nonce">'; } }
if ( ! function_exists( 'checked' ) ) { function checked( $checked, $current = true, $display = true ) { return (string) $checked === (string) $current ? ' checked="checked"' : ''; } }
if ( ! function_exists( 'admin_url' ) ) { function admin_url( $path = '' ) { return '/wp-admin/' . ltrim( $path, '/' ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $value, $domain = null ) { return esc_html( $value ); } }
if ( ! function_exists( 'esc_textarea' ) ) { function esc_textarea( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $value ) ); } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : ''; } }

$root = ES_REPO_ROOT . '/theme/';
$admin = $root . 'inc/Admin/';
	$required = array(
	$root . 'inc/Sections/Registry.php', $root . 'inc/Sections/Store.php',
	$admin . 'Fields/Schema.php', $admin . 'Fields/FieldRenderer.php', $admin . 'SectionsPage.php',
);
$available = true;
foreach ( $required as $required_path ) { $available = $available && is_file( $required_path ); }
$menu_source = (string) file_get_contents( $admin . 'AdminMenu.php' );
$theme_source = (string) file_get_contents( $root . 'inc/Theme.php' );
$page_source = (string) @file_get_contents( $admin . 'SectionsPage.php' );
$available = $available && str_contains( $menu_source, 'SectionsPage::render_settings' ) && str_contains( $theme_source, 'new SectionsPage()' ) && str_contains( $page_source, "check_ajax_referer( 'es_sections'" ) && str_contains( $page_source, "current_user_can( 'manage_options' )" );
if ( $available ) {
	require_once $root . 'inc/Contracts/ModuleInterface.php';
	require_once $root . 'inc/Sections/Registry.php';
	require_once $root . 'inc/Sections/Store.php';
	require_once $admin . 'Fields/Schema.php';
	require_once $admin . 'Fields/FieldRenderer.php';
	require_once $admin . 'SectionsPage.php';
}

echo "== Task 2.12: accessible section builder ==\n";
check( 'section editor modules and assets exist', fn() => $available && is_file( $root . 'assets/admin/sections.js' ) && is_file( $root . 'assets/admin/sections.css' ) );
if ( $available ) {
	$GLOBALS['es_sections_can_manage'] = true;
	$GLOBALS['es_mock_user_can'] = true;
	$GLOBALS['es_mock_options']['page_on_front'] = 451;
	$keys = array_keys( \ErfanSanat\Theme\Sections\Registry::get( 'home' ) );
	$state = array(
		'order' => array_reverse( $keys ),
		'enabled' => array_fill_keys( $keys, true ),
		'data' => array( 'hero' => array( 'title' => 'Saved headline' ) ),
		'available' => array( 'gallery' ),
	);
	$GLOBALS['es_sections_test_state'] = $state;
	$GLOBALS['es_section_meta'][451]['_es_sections'] = $state;
	$html = \ErfanSanat\Theme\Admin\SectionsPage::render_settings();
	$first = array_reverse( $keys )[0];
	$first_position = strpos( $html, 'data-es-section-key="' . esc_attr( $first ) . '"' );
	$all_cards = substr_count( $html, 'data-es-section-key="' );
	$has_actions = str_contains( $html, 'data-es-section-add' ) && str_contains( $html, 'data-es-section-remove' ) && str_contains( $html, 'data-es-section-enabled' ) && str_contains( $html, 'draggable="true"' );
	$has_save_security = str_contains( $html, 'name="nonce"' ) && str_contains( $html, 'data-es-sections-form' ) && str_contains( $html, 'name="post_id"' );
	check( 'four section actions render with a nonce-protected editor', fn() => $all_cards === 15 && $has_actions && $has_save_security );
	$available_position = strpos( $html, 'data-es-section-available' );
	$available_card_position = strpos( $html, 'data-es-section-key="gallery"' );
	check( 'section builder renders stored order and persists the available-section bucket', fn() => false !== $first_position && $first_position < strpos( $html, 'data-es-section-key="' . esc_attr( array_reverse( $keys )[1] ) . '"' ) && str_contains( $html, 'Saved headline' ) && false !== $available_position && $available_card_position > $available_position );
	$js = (string) file_get_contents( $root . 'assets/admin/sections.js' );
	check( 'keyboard controls are implemented for section reordering', fn() => str_contains( $js, "'ArrowUp'" ) && str_contains( $js, "'ArrowDown'" ) && str_contains( $js, 'data-es-section-handle' ) );
} else {
	check( 'four section actions render with a nonce-protected editor', fn() => false );
	check( 'section builder renders cards in the exact stored order', fn() => false );
	check( 'keyboard controls are implemented for section reordering', fn() => false );
}
summary();
