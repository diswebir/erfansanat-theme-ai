<?php
/** Task 3.10 — product media persistence, legacy gallery reads, and synchronization. */
declare(strict_types=1);
require_once __DIR__ . '/../../../../temp/tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_phase3_postmeta'] = $GLOBALS['es_phase3_postmeta'] ?? array();
$GLOBALS['es_phase3_posts'] = array( 801 => 'product', 901 => 'attachment', 902 => 'attachment', 903 => 'attachment', 904 => 'attachment', 905 => 'attachment', 906 => 'attachment', 950 => 'attachment' );
$GLOBALS['es_phase3_attachment_types'] = array( 901 => 'image/jpeg', 902 => 'image/jpeg', 903 => 'image/jpeg', 904 => 'image/jpeg', 905 => 'image/jpeg', 906 => 'image/jpeg', 950 => 'application/pdf' );
$GLOBALS['es_phase3_attachment_urls'] = array( 'https://example.test/catalog.pdf' => 950 );
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key = '', $single = false ) { return '' === $key ? ( $GLOBALS['es_phase3_postmeta'][$id] ?? array() ) : ( $GLOBALS['es_phase3_postmeta'][$id][$key] ?? '' ); } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['es_phase3_postmeta'][$id][$key] = $value; return true; } }
if ( ! function_exists( 'delete_post_meta' ) ) { function delete_post_meta( $id, $key ) { unset( $GLOBALS['es_phase3_postmeta'][$id][$key] ); return true; } }
if ( ! function_exists( 'get_post_type' ) ) { function get_post_type( $id ) { return $GLOBALS['es_phase3_posttypes'][$id] ?? $GLOBALS['es_phase3_posts'][$id] ?? false; } }
if ( ! function_exists( 'wp_attachment_is_image' ) ) { function wp_attachment_is_image( $id ) { if ( isset( $GLOBALS['es_phase3_attachment_types'][$id] ) ) { return str_starts_with( $GLOBALS['es_phase3_attachment_types'][$id], 'image/' ); } return ! empty( $GLOBALS['es_phase3_image_ids'][$id] ); } }
if ( ! function_exists( 'attachment_url_to_postid' ) ) { function attachment_url_to_postid( $url ) { return $GLOBALS['es_phase3_attachment_urls'][$url] ?? 0; } }
if ( ! function_exists( 'get_post_mime_type' ) ) { function get_post_mime_type( $id ) { return $GLOBALS['es_phase3_attachment_types'][$id] ?? ''; } }
if ( ! function_exists( 'wp_get_attachment_url' ) ) { function wp_get_attachment_url( $id ) { return 950 === $id ? 'https://example.test/catalog.pdf' : ''; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $value ) { return filter_var( $value, FILTER_VALIDATE_URL ) ? $value : ''; } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $value, $domain = null ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'wp_basename' ) ) { function wp_basename( $path ) { return basename( (string) $path ); } }
if ( ! function_exists( 'get_the_ID' ) ) { function get_the_ID() { return 801; } }
require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/RepeaterCodec.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/MetaRepository.php';
require_once ES_REPO_ROOT . '/theme/inc/Meta/functions.php';
require_once ES_REPO_ROOT . '/theme/inc/Admin/Metaboxes/Tabs/ProductMediaTab.php';
require_once ES_REPO_ROOT . '/theme/inc/Woo/ProductGallery.php';

\ErfanSanat\Theme\Meta\MetaRepository::clear_cache();
\ErfanSanat\Theme\Admin\Metaboxes\Tabs\ProductMediaTab::save( 801, array( 'galerry_produ' => '901,902,901,999,903' ) );
\ErfanSanat\Theme\Woo\ProductGallery::sync_meta( 1, 801, 'galerry_produ', $GLOBALS['es_phase3_postmeta'][801]['galerry_produ'] );
$gallery_sync = '901,902,903' === $GLOBALS['es_phase3_postmeta'][801]['galerry_produ'] && $GLOBALS['es_phase3_postmeta'][801]['galerry_produ'] === $GLOBALS['es_phase3_postmeta'][801]['_product_image_gallery'];
$GLOBALS['es_phase3_posts'][805] = 'product'; $GLOBALS['es_phase3_postmeta'][805]['_product_image_gallery'] = '906,905'; \ErfanSanat\Theme\Woo\ProductGallery::sync_meta( 2, 805, '_product_image_gallery', '906,905' );
$reverse_sync = '906,905' === $GLOBALS['es_phase3_postmeta'][805]['galerry_produ'];
$GLOBALS['es_phase3_postmeta'][802] = array( 'gallery-filed' => '904,905', 'image-2' => '906' );
$GLOBALS['es_phase3_postmeta'][803] = array( 'image-2' => '901', 'image-3' => '902', 'image-4' => '', 'image-5' => '901', 'image-6' => '999' );
$legacy_gallery = \ErfanSanat\Theme\Woo\ProductGallery::get( 802 );
$old_single_images = \ErfanSanat\Theme\Woo\ProductGallery::get( 803 );
\ErfanSanat\Theme\Admin\Metaboxes\Tabs\ProductMediaTab::save( 801, array( 'product-catalog-file' => 'https://example.test/catalog.pdf' ) );
$catalog_valid = 'https://example.test/catalog.pdf' === ( $GLOBALS['es_phase3_postmeta'][801]['product-catalog-file'] ?? '' );
$invalid_catalog_before = $GLOBALS['es_phase3_postmeta'][801]['product-catalog-file'];
\ErfanSanat\Theme\Admin\Metaboxes\Tabs\ProductMediaTab::save( 801, array( 'product-catalog-file' => 'https://evil.test/not-attached.pdf' ) );
$invalid_catalog_preserved = $invalid_catalog_before === $GLOBALS['es_phase3_postmeta'][801]['product-catalog-file'];
$GLOBALS['es_phase3_postmeta'][804] = array();
$empty_gallery = \ErfanSanat\Theme\Woo\ProductGallery::get( 804 );
$rendered_media = \ErfanSanat\Theme\Admin\Metaboxes\Tabs\ProductMediaTab::render( 801 );

check( 'همگام‌سازی galerry_produ و گالری ووکامرس همراه با اعتبارسنجی تصویر', fn() => $gallery_sync && $reverse_sync && '901,902,903' === $GLOBALS['es_phase3_postmeta'][801]['_product_image_gallery'] );
check( 'fallback قدیمی از gallery-filed و image-2..6', fn() => array( 904, 905 ) === $legacy_gallery && array( 901, 902 ) === $old_single_images );
check( 'URL کاتالوگ فقط برای attachment PDF پذیرفته می‌شود', fn() => $catalog_valid && $invalid_catalog_preserved );
check( 'بدون گالری آرایه خالی است و es_product_gallery عمومی کار می‌کند', fn() => array() === $empty_gallery && $legacy_gallery === es_product_gallery( 802 ) && str_contains( $rendered_media, 'product-catalog-file' ) && str_contains( $rendered_media, 'galerry_produ' ) );
summary();
