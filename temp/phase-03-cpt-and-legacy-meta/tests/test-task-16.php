<?php
/** Task 3.16 — hreflang validation, sorting and term values. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_postmeta'] = array( 816 => array( 'hrefmeta_en' => 'https://old.example/en' ) ); $GLOBALS['es_phase3_termmeta'] = array( 816 => array() );
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key = '', $single = false ) { return '' === $key ? ( $GLOBALS['es_phase3_postmeta'][$id] ?? array() ) : ( $GLOBALS['es_phase3_postmeta'][$id][$key] ?? '' ); } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['es_phase3_postmeta'][$id][$key] = $value; return true; } }
if ( ! function_exists( 'delete_post_meta' ) ) { function delete_post_meta( $id, $key ) { unset( $GLOBALS['es_phase3_postmeta'][$id][$key] ); return true; } }
if ( ! function_exists( 'get_term_meta' ) ) { function get_term_meta( $id, $key = '', $single = false ) { return '' === $key ? ( $GLOBALS['es_phase3_termmeta'][$id] ?? array() ) : ( $GLOBALS['es_phase3_termmeta'][$id][$key] ?? '' ); } }
if ( ! function_exists( 'update_term_meta' ) ) { function update_term_meta( $id, $key, $value ) { $GLOBALS['es_phase3_termmeta'][$id][$key] = $value; return true; } }
if ( ! function_exists( 'delete_term_meta' ) ) { function delete_term_meta( $id, $key ) { unset( $GLOBALS['es_phase3_termmeta'][$id][$key] ); return true; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $cap, $id = null ) { return in_array( $cap, array( 'edit_post', 'edit_terms' ), true ); } }
if ( ! function_exists( 'wp_verify_nonce' ) ) { function wp_verify_nonce( $n, $a ) { return 'valid' === $n; } }
if ( ! function_exists( 'wp_unslash' ) ) { function wp_unslash( $v ) { return is_array($v) ? array_map('wp_unslash', $v) : stripslashes((string)$v); } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $v ) { return filter_var((string)$v, FILTER_VALIDATE_URL) ? (string)$v : ''; } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $v, $d = null ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return trim(strip_tags((string)$v)); } }
if ( ! function_exists( 'wp_create_nonce' ) ) { function wp_create_nonce( $a ) { return 'valid'; } }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/RepeaterCodec.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/MetaRepository.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/Hreflang.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/ListTables.php';
$class = '\\ErfanSanat\\Theme\\Admin\\Metaboxes\\Hreflang'; $list = '\\ErfanSanat\\Theme\\Admin\\ListTables';
$valid = $class::valid_url( 'https://example.test/en' ); $invalid = $class::valid_url( 'javascript:alert(1)' );
$_POST = array( 'es_hreflang_term_nonce' => 'valid', 'es_hreflang_term' => array( 'hrefmeta_fa' => 'https://fa.example.test/p', 'hrefmeta_en' => 'https://en.example.test/p', 'hrefmeta_ar' => '', 'same_key_hrefl' => 'cluster-7' ) );
$saved = $class::save_term( 816 );
$term_values = $GLOBALS['es_phase3_termmeta'][816];
$_POST['es_hreflang_term']['hrefmeta_en'] = 'javascript:bad'; $class::save_term( 816 );
$bad_preserved = 'https://en.example.test/p' === $GLOBALS['es_phase3_termmeta'][816]['hrefmeta_en'];
$sort = $list::sortable_columns_for_post_type( array(), 'page' ); $term_sort = $class::sortable_term_columns( array() ); $term_query = (object) array( 'query_vars' => array( 'orderby' => 'same_key_hrefl' ) ); $class::sort_term_query( $term_query );
check( 'URL معتبر پذیرفته و URL نامعتبر رد می‌شود؛ فیلد خالی مجاز است', fn() => $valid && ! $invalid && $saved && ! isset( $term_values['hrefmeta_ar'] ) && $bad_preserved );
check( 'same_key_hrefl در ستون‌های لیست مرتب‌سازی می‌شود', fn() => 'same_key_hrefl' === $sort['same_key_hrefl'] && 'same_key_hrefl' === $term_sort['same_key_hrefl'] && 'same_key_hrefl' === $term_query->query_vars['meta_key'] && 'meta_value' === $term_query->query_vars['orderby'] );
check( 'hreflang URLs و کلید گروه روی ترم ذخیره می‌شوند', fn() => 'https://fa.example.test/p' === $term_values['hrefmeta_fa'] && 'cluster-7' === $term_values['same_key_hrefl'] && str_contains( $class::render_alternates( 816 ), 'hreflang="en"' ) );
summary();
