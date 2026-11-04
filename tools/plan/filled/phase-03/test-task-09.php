<?php
/** Task 3.9 — secure product identity metadata box. */
declare(strict_types=1);

require_once __DIR__ . '/../../../../temp/tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_actions'] = $GLOBALS['es_phase3_actions'] ?? array();
$GLOBALS['es_phase3_meta_updates'] = array();
$GLOBALS['es_phase3_product_meta'] = array( 444 => array( 'gs1_id' => 'GTIN-OLD', 'english_titl' => 'Old title', 'true_sale' => 'false' ) );
$GLOBALS['es_phase3_meta_nonce'] = 'valid';
$GLOBALS['es_phase3_edit_allowed'] = true;
if ( ! function_exists( 'add_action' ) ) { function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['es_phase3_actions'][ $hook ][] = array( $callback, $priority, $accepted_args ); } }
if ( ! function_exists( 'add_meta_box' ) ) { function add_meta_box( ...$args ) { $GLOBALS['es_phase3_metaboxes'][] = $args; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability, $object_id = null ) { return ! empty( $GLOBALS['es_phase3_edit_allowed'] ) && in_array( $capability, array( 'manage_options', 'edit_post' ), true ); } }
if ( ! function_exists( 'wp_verify_nonce' ) ) { function wp_verify_nonce( $nonce, $action ) { return $nonce === $GLOBALS['es_phase3_meta_nonce']; } }
if ( ! function_exists( 'wp_nonce_field' ) ) { function wp_nonce_field( $action, $name, $referer = true, $display = true ) { return '<input type="hidden" name="' . esc_attr( $name ) . '" value="valid">'; } }
if ( ! function_exists( 'wp_unslash' ) ) { function wp_unslash( $value ) { return is_array( $value ) ? array_map( 'wp_unslash', $value ) : stripslashes( (string) $value ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : ''; } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( '__' ) ) { function __( $value, $domain = 'default' ) { return $value; } }
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key = '', $single = false ) { if ( '' === $key ) { return $GLOBALS['es_phase3_product_meta'][ $id ] ?? array(); } return $GLOBALS['es_phase3_product_meta'][ $id ][ $key ] ?? ''; } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['es_phase3_meta_updates'][] = array( $id, $key, $value ); $GLOBALS['es_phase3_product_meta'][ $id ][ $key ] = $value; return true; } }

if ( ! function_exists( 'esc_attr__' ) ) { function esc_attr__( $value, $domain = null ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $value, $domain = null ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $value ) { return filter_var( (string) $value, FILTER_VALIDATE_URL ) ? (string) $value : ''; } }
if ( ! function_exists( 'esc_textarea' ) ) { function esc_textarea( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'wp_basename' ) ) { function wp_basename( $value ) { return basename( (string) $value ); } }
if ( ! function_exists( 'wp_create_nonce' ) ) { function wp_create_nonce( $action ) { return 'valid'; } }
if ( ! function_exists( 'get_terms' ) ) { function get_terms( $args = array() ) { return array(); } }
require_once ES_REPO_ROOT . '/theme/inc/Meta/RepeaterCodec.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/MetaRepository.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/functions.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/Tabs/ProductMediaTab.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/Tabs/ProductRelationsTab.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/Tabs/ProductRepeatersTab.php';
require_once ES_REPO_ROOT . '/theme/inc/Woo/ProductGallery.php';
$meta_file = ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/ProductMeta.php';
$tab_file = ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/Tabs/ProductIdentityTab.php';
$available = is_file( $meta_file ) && is_file( $tab_file );
if ( $available ) { require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php'; require_once $tab_file; require_once $meta_file; ( new \ErfanSanat\Theme\Admin\Metaboxes\ProductMeta() )->register(); }
$keys = array( 'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id', 'zireonvan', 'english_titl', 'true_sale', 'before-sale-c' );
$_POST = array( 'es_product_meta_nonce' => 'valid', 'es_product_meta' => array( 'gs1_id' => 'GTIN-123', 'national_code' => 'N-42', 'hs_id' => 'HS', 'isic_id' => 'ISIC', 'cpc_id' => 'CPC', 'product_id' => 'P-9', 'zireonvan' => 'Subtitle <b>safe</b>', 'english_titl' => 'English Name', 'true_sale' => 'true', 'before-sale-c' => 'Notice' ) );
if ( $available ) { \ErfanSanat\Theme\Admin\Metaboxes\ProductMeta::save( 444 ); }
$all_saved = true; foreach ( $keys as $key ) { $all_saved = $all_saved && array_key_exists( $key, $GLOBALS['es_phase3_product_meta'][444] ); }
$all_saved_values = true; foreach ( $keys as $key ) { $all_saved_values = $all_saved_values && in_array( $key, array_column( $GLOBALS['es_phase3_meta_updates'], 1 ), true ); }
$rendered = $available ? \ErfanSanat\Theme\Admin\Metaboxes\ProductMeta::render_content( (object) array( 'ID' => 444 ) ) : '';

check( 'all ten exact product identity metadata keys save and render', fn() => $available && $all_saved && $all_saved_values && str_contains( $rendered, 'name="es_product_meta[english_titl]"' ) && ! str_contains( $rendered, 'english_title' ) );
check( 'historical english_titl spelling is preserved without an alias key', fn() => 'English Name' === $GLOBALS['es_phase3_product_meta'][444]['english_titl'] && ! array_key_exists( 'english_title', $GLOBALS['es_phase3_product_meta'][444] ) );
check( 'true_sale accepts only the exact true/false strings', function () use ( $available ) { $_POST['es_product_meta']['true_sale'] = 'yes'; if ( $available ) { \ErfanSanat\Theme\Admin\Metaboxes\ProductMeta::save( 444 ); } return 'true' === $GLOBALS['es_phase3_product_meta'][444]['true_sale']; } );
check( 'missing nonce blocks every metadata write', function () use ( $available ) { $before = count( $GLOBALS['es_phase3_meta_updates'] ); unset( $_POST['es_product_meta_nonce'] ); if ( $available ) { \ErfanSanat\Theme\Admin\Metaboxes\ProductMeta::save( 444 ); } return $before === count( $GLOBALS['es_phase3_meta_updates'] ); } );
summary();
