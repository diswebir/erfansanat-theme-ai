<?php
/** Task 3.15 — product category image and FAQ term metadata. */
declare(strict_types=1);
require_once __DIR__ . '/../../../../temp/tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_termmeta'] = array( 815 => array() ); $GLOBALS['es_phase3_posttypes'] = array( 915 => 'attachment' ); $GLOBALS['es_phase3_image_ids'] = array( 915 => true );
if ( ! function_exists( 'get_term_meta' ) ) { function get_term_meta( $id, $key = '', $single = false ) { return '' === $key ? ( $GLOBALS['es_phase3_termmeta'][$id] ?? array() ) : ( $GLOBALS['es_phase3_termmeta'][$id][$key] ?? '' ); } }
if ( ! function_exists( 'update_term_meta' ) ) { function update_term_meta( $id, $key, $value ) { $GLOBALS['es_phase3_termmeta'][$id][$key] = $value; return true; } }
if ( ! function_exists( 'delete_term_meta' ) ) { function delete_term_meta( $id, $key ) { unset( $GLOBALS['es_phase3_termmeta'][$id][$key] ); return true; } }
if ( ! function_exists( 'get_post_type' ) ) { function get_post_type( $id ) { return $GLOBALS['es_phase3_posttypes'][$id] ?? false; } }
if ( ! function_exists( 'wp_attachment_is_image' ) ) { function wp_attachment_is_image( $id ) { return 915 === (int) $id; } }
if ( ! function_exists( 'wp_verify_nonce' ) ) { function wp_verify_nonce( $n, $a ) { return 'valid' === $n && 'es_product_cat_meta' === $a; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $cap ) { return 'manage_product_terms' === $cap; } }
if ( ! function_exists( 'wp_unslash' ) ) { function wp_unslash( $v ) { return is_array($v) ? array_map('wp_unslash', $v) : stripslashes((string)$v); } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $v ) { return strip_tags((string)$v, '<p><strong><em><a><br>'); } }
if ( ! function_exists( 'wp_strip_all_tags' ) ) { function wp_strip_all_tags( $v ) { return strip_tags((string)$v); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return trim(strip_tags((string)$v)); } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs((int)$v); } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $v, $d = null ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_textarea' ) ) { function esc_textarea( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'wp_create_nonce' ) ) { function wp_create_nonce( $a ) { return 'valid'; } }
if ( ! function_exists( 'wp_get_attachment_image' ) ) { function wp_get_attachment_image( $id, $size = 'thumbnail' ) { return '<img src="image-' . (int) $id . '">'; } }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/RepeaterCodec.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/MetaRepository.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/functions.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/TermMeta.php';
$class = '\\ErfanSanat\\Theme\\Admin\\Metaboxes\\TermMeta'; $meta = '\\ErfanSanat\\Theme\\Meta\\MetaRepository'; $meta::clear_cache();
$_POST = array( 'es_product_cat_meta_nonce' => 'valid', 'es_product_cat_meta' => array( 'thumbnail' => '915', 'faq' => array( 'item-0' => array( 'name_question' => 'Dimmable?', 'answer_question' => '<p><strong>Yes</strong><script>bad()</script></p>' ) ) ) );
$saved = $class::save( 815 ); $faq_raw = $GLOBALS['es_phase3_termmeta'][815]['faq']; $faq = is_string( $faq_raw ) ? json_decode( $faq_raw, true ) : $faq_raw;
ob_start(); $class::add_fields(); $empty_form = ob_get_clean();
$_POST['es_product_cat_meta']['faq'] = array( 'item-0' => array( 'name_question' => '', 'answer_question' => '' ) ); $class::save( 815 );
check( 'thumbnail و FAQ ذخیره و بازیابی می‌شوند', fn() => $saved && 915 === $GLOBALS['es_phase3_termmeta'][815]['thumbnail'] && 'Dimmable?' === $faq['item-0']['name_question'] );
check( 'دسته بدون تصویر placeholder نشان می‌دهد', fn() => str_contains( $empty_form, 'تصویری انتخاب نشده است' ) && str_contains( $empty_form, 'es_product_cat_meta[thumbnail]' ) );
check( 'زیرفیلدهای FAQ دقیق و پاسخ sanitize می‌شود', fn() => isset( $faq['item-0']['answer_question'] ) && str_contains( $faq['item-0']['answer_question'], '<strong>Yes</strong>' ) && ! str_contains( $faq['item-0']['answer_question'], '<script>' ) && str_contains( $empty_form, 'name_question' ) && str_contains( $empty_form, 'answer_question' ) );
summary();
