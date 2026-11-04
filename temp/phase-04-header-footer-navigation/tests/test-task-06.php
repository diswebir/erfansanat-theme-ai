<?php
/** Task 4.6 — native menu-item fields, whitelist, and capability/nonce checks. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_phase4_admin_hooks'] = array();
$GLOBALS['es_phase4_menu_meta'] = array();
$GLOBALS['es_phase4_meta_updates'] = 0;
$GLOBALS['es_phase4_enqueued'] = array();
$GLOBALS['es_phase4_nonce_ok'] = true;
function add_action( $hook, $callback, $priority = 10, $accepted = 1 ) { $GLOBALS['es_phase4_admin_hooks'][ $hook ][] = $callback; }
function current_user_can( $cap ) { return 'edit_theme_options' === $cap; }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function selected( $a, $b, $echo = true ) { return (string) $a === (string) $b ? 'selected="selected"' : ''; }
function checked( $value, $current = true, $echo = true ) { return $value === $current ? 'checked="checked"' : ''; }
function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['es_phase4_menu_meta'][ $id ][ $key ] ?? ''; }
function wp_unslash( $value ) { return $value; }
function wp_verify_nonce( $nonce, $action ) { return $GLOBALS['es_phase4_nonce_ok'] && 'menu-nonce' === $nonce && 'update-nav_menu' === $action; }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function absint( $value ) { return abs( (int) $value ); }
function get_post( $id ) { return (object) array( 'ID' => $id, 'post_type' => 'attachment' ); }
function wp_attachment_is_image( $id ) { return 15 === (int) $id; }
function update_post_meta( $id, $key, $value ) { ++$GLOBALS['es_phase4_meta_updates']; $GLOBALS['es_phase4_menu_meta'][ $id ][ $key ] = $value; return true; }
function delete_post_meta( $id, $key ) { unset( $GLOBALS['es_phase4_menu_meta'][ $id ][ $key ] ); return true; }
function get_template_directory_uri() { return 'https://example.test/theme'; }
function wp_enqueue_style( $handle, $src, $deps = array(), $ver = false ) { $GLOBALS['es_phase4_enqueued'][] = $handle; }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/Icons.php';
require_once ES_REPO_ROOT . '/theme/inc/Nav/MenuItemFields.php';
$module = new \ErfanSanat\Theme\Nav\MenuItemFields();
$module->register();
ob_start(); \ErfanSanat\Theme\Nav\MenuItemFields::render_fields( 7, null, 0 ); $fields = ob_get_clean();
ob_start(); \ErfanSanat\Theme\Nav\MenuItemFields::render_fields( 7, null, 2 ); $deep_fields = ob_get_clean();
$_POST = array(
	'update-nav-menu-nonce' => 'menu-nonce',
	'menu-item-es-panel-type' => array( 8 => 'products' ),
	'menu-item-es-column' => array( 8 => '3' ),
	'menu-item-es-icon' => array( 8 => 'cpu' ),
	'menu-item-es-label' => array( 8 => '<b>ویژه</b>' ),
	'menu-item-es-image' => array( 8 => '15' ),
	'menu-item-es-tab' => array( 8 => '1' ),
	'menu-item-es-column-width' => array( 8 => '4' ),
	'menu-item-es-description' => array( 8 => 'توضیح' ),
);
\ErfanSanat\Theme\Nav\MenuItemFields::save_fields( 4, 8, array() );
$valid_meta = $GLOBALS['es_phase4_menu_meta'][8] ?? array();
$_POST['menu-item-es-icon'][8] = 'evil-svg';
\ErfanSanat\Theme\Nav\MenuItemFields::save_fields( 4, 8, array() );
$icon_after_invalid = $GLOBALS['es_phase4_menu_meta'][8]['_menu_item_es_icon'] ?? '';
$updates_before_bad_nonce = $GLOBALS['es_phase4_meta_updates'];
$GLOBALS['es_phase4_nonce_ok'] = false;
$_POST['menu-item-es-label'][8] = 'نباید ذخیره شود';
\ErfanSanat\Theme\Nav\MenuItemFields::save_fields( 4, 8, array() );
$blocked = $updates_before_bad_nonce === $GLOBALS['es_phase4_meta_updates'];
\ErfanSanat\Theme\Nav\MenuItemFields::enqueue_assets( 'nav-menus.php' );
\ErfanSanat\Theme\Nav\MenuItemFields::enqueue_assets( 'admin.php' );
check( 'فیلدها در هوک بومی Appearance → Menus رندر می‌شوند', fn() => isset( $GLOBALS['es_phase4_admin_hooks']['wp_nav_menu_item_custom_fields'] ) && str_contains( $fields, 'menu-item-es-panel-type[7]' ) && str_contains( $fields, 'menu-item-es-column-width[7]' ) && '' === $deep_fields );
check( 'مقادیر whitelist شده پس از ذخیره و بازیابی حفظ می‌شوند', fn() => ( $valid_meta['_menu_item_es_panel_type'] ?? '' ) === 'products' && ( $valid_meta['_menu_item_es_icon'] ?? '' ) === 'cpu' && ( $valid_meta['_menu_item_es_label'] ?? '' ) === 'ویژه' && ( $valid_meta['_menu_item_es_image'] ?? '' ) === '15' && ( $valid_meta['_menu_item_es_tab'] ?? '' ) === '1' && ( $valid_meta['_menu_item_es_column_width'] ?? '' ) === '4' );
check( 'صفحه مدیریت جدا ساخته نمی‌شود و stylesheet فقط در منوها enqueue می‌شود', fn() => ! isset( $GLOBALS['es_phase4_admin_hooks']['admin_menu'] ) && in_array( 'es-nav-fields', $GLOBALS['es_phase4_enqueued'], true ) && 1 === count( $GLOBALS['es_phase4_enqueued'] ) );
check( 'آیکون ناشناخته و ذخیره بدون nonce رد می‌شوند', fn() => 'cpu' === $icon_after_invalid && $blocked );
summary();
