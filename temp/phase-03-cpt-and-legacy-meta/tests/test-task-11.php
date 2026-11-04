<?php
/** Task 3.11 — product relation validation and legacy format preservation. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_postmeta'] = array( 811 => array( 'project-ids' => '{"item-0":501}', 'usage_countr' => 'a:1:{i:0;i:71;}' ) );
$GLOBALS['es_phase3_posttypes'] = array( 501 => 'projects', 502 => 'post', 811 => 'product' );
$GLOBALS['es_phase3_terms'] = array( 71 => (object) array( 'term_id' => 71, 'taxonomy' => 'project_type' ), 72 => (object) array( 'term_id' => 72, 'taxonomy' => 'city' ) );
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key = '', $single = false ) { return '' === $key ? ( $GLOBALS['es_phase3_postmeta'][$id] ?? array() ) : ( $GLOBALS['es_phase3_postmeta'][$id][$key] ?? '' ); } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['es_phase3_postmeta'][$id][$key] = $value; return true; } }
if ( ! function_exists( 'delete_post_meta' ) ) { function delete_post_meta( $id, $key ) { unset( $GLOBALS['es_phase3_postmeta'][$id][$key] ); return true; } }
if ( ! function_exists( 'get_post_type' ) ) { function get_post_type( $id ) { return $GLOBALS['es_phase3_posttypes'][$id] ?? false; } }
if ( ! function_exists( 'is_wp_error' ) ) { function is_wp_error( $v ) { return false; } }
if ( ! function_exists( 'get_term' ) ) { function get_term( $id, $tax = '' ) { $t = $GLOBALS['es_phase3_terms'][$id] ?? null; return $t && $t->taxonomy === $tax ? $t : null; } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs( (int) $v ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return trim(strip_tags((string)$v)); } }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/RepeaterCodec.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/MetaRepository.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/Tabs/ProductRelationsTab.php';
$meta = '\\ErfanSanat\\Theme\\Meta\\MetaRepository';
$tab = '\\ErfanSanat\\Theme\\Admin\\Metaboxes\\Tabs\\ProductRelationsTab';
$meta::clear_cache();
$tab::save( 811, array( 'project-ids' => array( '501', '502', '999', '501' ), 'usage_countr' => array( '71', '72', '999' ) ) );
$projects_raw = $GLOBALS['es_phase3_postmeta'][811]['project-ids'];
$uses_raw = $GLOBALS['es_phase3_postmeta'][811]['usage_countr'];
$project_round_trip = json_decode( $projects_raw, true );
$usage_round_trip = unserialize( $uses_raw, array( 'allowed_classes' => false ) );
$args = $tab::search_args( '<script>lamp</script>' );
check( 'CRUD روابط پروژه و کاربرد فقط با شناسه‌های معتبر', fn() => array( 'item-0' => 501 ) === $project_round_trip && array( 0 => 71 ) === $usage_round_trip );
check( 'شناسه پست با post type اشتباه و term نامعتبر رد می‌شود', fn() => ! in_array( 502, $project_round_trip, true ) && ! in_array( 72, $usage_round_trip, true ) && ! in_array( 999, $project_round_trip, true ) );
check( 'جستجوی ایجکس محدود به پروژه‌های منتشرشده', fn() => 'projects' === $args['post_type'] && 'publish' === $args['post_status'] && 20 === $args['posts_per_page'] && 'lamp' === $args['s'] );
check( 'فرمت ذخیره موجود JSON و serialized حفظ می‌شود', fn() => str_starts_with( $projects_raw, '{' ) && str_starts_with( $uses_raw, 'a:' ) );
summary();
