<?php
/** Task 3.14 — project legacy media, Jalali date, and product relations. */
declare(strict_types=1);
require_once __DIR__ . '/../../../../temp/tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_postmeta'] = array( 814 => array( 'finish-pro-time' => '1402/11/20', 'mortabet_mahsole_2' => serialize( array( 0 => 701 ) ) ) );
$GLOBALS['es_phase3_posttypes'] = array( 814 => 'projects', 701 => 'product', 702 => 'post', 910 => 'attachment', 911 => 'attachment', 912 => 'attachment', 913 => 'attachment', 914 => 'attachment' ); $GLOBALS['es_phase3_image_ids'] = array_fill_keys( array( 910, 911, 912, 913, 914 ), true );
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key = '', $single = false ) { return '' === $key ? ( $GLOBALS['es_phase3_postmeta'][$id] ?? array() ) : ( $GLOBALS['es_phase3_postmeta'][$id][$key] ?? '' ); } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['es_phase3_postmeta'][$id][$key] = $value; return true; } }
if ( ! function_exists( 'delete_post_meta' ) ) { function delete_post_meta( $id, $key ) { unset( $GLOBALS['es_phase3_postmeta'][$id][$key] ); return true; } }
if ( ! function_exists( 'get_post_type' ) ) { function get_post_type( $id ) { return $GLOBALS['es_phase3_posttypes'][$id] ?? false; } }
if ( ! function_exists( 'wp_attachment_is_image' ) ) { function wp_attachment_is_image( $id ) { return in_array( $id, array( 910, 911, 912, 913, 914 ), true ); } }
if ( ! function_exists( 'wp_is_post_revision' ) ) { function wp_is_post_revision( $id ) { return false; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $cap, $id = null ) { return 'edit_post' === $cap && 814 === (int) $id; } }
if ( ! function_exists( 'wp_verify_nonce' ) ) { function wp_verify_nonce( $nonce, $action ) { return 'valid' === $nonce && 'es_project_meta_814' === $action; } }
if ( ! function_exists( 'wp_unslash' ) ) { function wp_unslash( $v ) { return is_array($v) ? array_map('wp_unslash', $v) : stripslashes((string)$v); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return trim(strip_tags((string)$v)); } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs((int)$v); } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $v ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $v, $d = null ) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); } }
if ( ! function_exists( 'wp_get_attachment_image' ) ) { function wp_get_attachment_image( $id, $size = 'thumbnail' ) { return '<img src="image-' . (int) $id . '">'; } }
if ( ! function_exists( 'get_posts' ) ) { function get_posts( $args = array() ) { return array(); } }
if ( ! function_exists( 'wp_nonce_field' ) ) { function wp_nonce_field( $a, $n, $r = true, $e = true ) { return '<input name="' . $n . '">'; } }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/RepeaterCodec.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/MetaRepository.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/functions.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/ProjectMeta.php';
$class = '\\ErfanSanat\\Theme\\Admin\\Metaboxes\\ProjectMeta'; $meta = '\\ErfanSanat\\Theme\\Meta\\MetaRepository'; $meta::clear_cache();
$_POST = array( 'es_project_meta_nonce' => 'valid', 'es_project_meta' => array( 'second-image' => '910', 'three-image' => '911', 'foure-image' => '912', 'five-image' => '913', 'six-image' => '914', 'finish-pro-time' => '1403/12/30', 'mortabet_mahsole_2' => array( '701', '702', '701' ) ) );
$saved = $class::save( 814, (object) array( 'ID' => 814, 'post_type' => 'projects' ) );
$images_saved = $saved && array( 'second-image', 'three-image', 'foure-image', 'five-image', 'six-image' ) === array_values( array_filter( array( 'second-image', 'three-image', 'foure-image', 'five-image', 'six-image' ), fn( $k ) => (string) $GLOBALS['es_phase3_postmeta'][814][$k] === (string) ( 910 + array_search( $k, array( 'second-image', 'three-image', 'foure-image', 'five-image', 'six-image' ), true ) ) ) );
$old_date = $GLOBALS['es_phase3_postmeta'][814]['finish-pro-time'];
$_POST['es_project_meta']['finish-pro-time'] = '1402/12/30'; $class::save( 814, (object) array( 'ID' => 814, 'post_type' => 'projects' ) );
$date_preserved = $old_date === $GLOBALS['es_phase3_postmeta'][814]['finish-pro-time'];
$related_raw = $GLOBALS['es_phase3_postmeta'][814]['mortabet_mahsole_2']; $related = unserialize( $related_raw, array( 'allowed_classes' => false ) );
ob_start(); $rendered = $class::render_content( 814 ); ob_end_clean();
check( 'هر پنج کلید تصویر دقیقاً ذخیره می‌شوند، شامل foure-image', fn() => $images_saved && str_contains( $rendered, 'foure-image' ) );
check( 'تاریخ جلالی معتبر پذیرفته و تاریخ نامعتبر مقدار قبلی را حفظ می‌کند', fn() => '1403/12/30' === $old_date && $date_preserved && $class::valid_jalali_date( '1402/06/31' ) && ! $class::valid_jalali_date( '1402/07/31' ) );
check( 'فقط محصولات معتبر ذخیره می‌شوند و فرمت serialize قبلی باقی می‌ماند', fn() => array( 0 => 701 ) === $related && str_starts_with( $related_raw, 'a:' ) );
summary();
