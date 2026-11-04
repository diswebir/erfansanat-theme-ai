<?php
/** Task 3.13 — secure Quick Edit of product identity fields. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_postmeta'] = array( 813 => array( 'gs1_id' => '111', 'national_code' => 'N-1', 'unrelated_meta' => 'leave-me', 'english_titl' => 'Old name' ) );
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key = '', $single = false ) { return '' === $key ? ( $GLOBALS['es_phase3_postmeta'][$id] ?? array() ) : ( $GLOBALS['es_phase3_postmeta'][$id][$key] ?? '' ); } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['es_phase3_postmeta'][$id][$key] = $value; return true; } }
if ( ! function_exists( 'wp_verify_nonce' ) ) { function wp_verify_nonce( $nonce, $action ) { return 'valid' === $nonce && 'es_product_quick_edit' === $action; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $cap, $id = null ) { return 'edit_post' === $cap && 813 === (int) $id; } }
if ( ! function_exists( 'wp_unslash' ) ) { function wp_unslash( $v ) { return is_array($v) ? array_map('wp_unslash', $v) : stripslashes((string)$v); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return trim(strip_tags((string)$v)); } }
if ( ! function_exists( 'wp_create_nonce' ) ) { function wp_create_nonce( $a ) { return 'valid'; } }
if ( ! function_exists( 'wp_json_encode' ) ) { function wp_json_encode( $v ) { return json_encode($v); } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $v, $d = null ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/QuickEdit.php';
$class = '\\ErfanSanat\\Theme\\Admin\\QuickEdit';
$post = (object) array( 'ID' => 813, 'post_type' => 'product' );
$actions = $class::row_data( array(), $post );
ob_start(); $class::render_box( 'title', 'product' ); $fields_markup = ob_get_clean();
$_POST = array( 'es_product_quick_edit_nonce' => 'valid', 'es_product_quick_edit' => array( 'gs1_id' => '222', 'national_code' => 'N-2', 'hs_id' => 'H-2', 'isic_id' => 'I-2', 'cpc_id' => 'C-2', 'product_id' => 'P-2', 'zireonvan' => 'Subtitle', 'english_titl' => 'New name', 'true_sale' => 'true' ) );
$saved = $class::save( 813, $post );
$bad_post = (object) array( 'ID' => 813, 'post_type' => 'post' );
$_POST['es_product_quick_edit']['gs1_id'] = 'wrong-type'; $class::save( 813, $bad_post );
check( 'Quick Edit مقادیر whitelisted را ذخیره می‌کند', fn() => $saved && '222' === $GLOBALS['es_phase3_postmeta'][813]['gs1_id'] && 'N-2' === $GLOBALS['es_phase3_postmeta'][813]['national_code'] && 'H-2' === $GLOBALS['es_phase3_postmeta'][813]['hs_id'] && 'I-2' === $GLOBALS['es_phase3_postmeta'][813]['isic_id'] && 'C-2' === $GLOBALS['es_phase3_postmeta'][813]['cpc_id'] && 'P-2' === $GLOBALS['es_phase3_postmeta'][813]['product_id'] && 'Subtitle' === $GLOBALS['es_phase3_postmeta'][813]['zireonvan'] && 'true' === $GLOBALS['es_phase3_postmeta'][813]['true_sale'] && 'New name' === $GLOBALS['es_phase3_postmeta'][813]['english_titl'] );
check( 'مقادیر فعلی برای JS و ورودی‌های دقیق پیش‌پر می‌شوند', fn() => isset( $actions['es-quick-edit-values'] ) && str_contains( $actions['es-quick-edit-values'], 'Old name' ) && str_contains( $fields_markup, 'es_product_quick_edit[english_titl]' ) && str_contains( $fields_markup, 'es_product_quick_edit_nonce' ) );
check( 'متاهای نامرتبط تغییر نمی‌کنند و post type اشتباه رد می‌شود', fn() => 'leave-me' === $GLOBALS['es_phase3_postmeta'][813]['unrelated_meta'] && '222' === $GLOBALS['es_phase3_postmeta'][813]['gs1_id'] );
summary();
