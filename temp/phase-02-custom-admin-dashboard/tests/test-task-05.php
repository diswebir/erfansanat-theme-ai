<?php
/** Task 2.5 — wp.media picker, attachment validation and CSV galleries. */
declare(strict_types=1);

$bootstrap_candidates = array( __DIR__ . '/../../../../temp/tests/bootstrap.php', __DIR__ . '/../../tests/bootstrap.php' );
foreach ( $bootstrap_candidates as $bootstrap ) { if ( is_file( $bootstrap ) ) { require_once $bootstrap; break; } }
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $s, $domain = null ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs( (int) $v ); } }
if ( ! function_exists( 'get_post' ) ) { function get_post( $id ) { return 12 === (int) $id ? (object) array( 'ID' => 12, 'post_type' => 'attachment' ) : null; } }
if ( ! function_exists( 'wp_attachment_is_image' ) ) { function wp_attachment_is_image( $id ) { return 12 === (int) $id; } }
if ( ! function_exists( 'wp_get_attachment_image_url' ) ) { function wp_get_attachment_image_url( $id, $size = 'thumbnail' ) { return 12 === (int) $id ? 'https://media.test/image.jpg' : false; } }

$media_php = ES_REPO_ROOT . '/theme/inc/Admin/Fields/types/media.php';
$media_js  = ES_REPO_ROOT . '/theme/assets/admin/media.js';
$sanitizer = ES_REPO_ROOT . '/theme/inc/Admin/Fields/Sanitizer.php';
$available = is_file( $media_php ) && is_file( $media_js ) && is_file( $sanitizer );
if ( $available ) { require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/Schema.php'; require_once $sanitizer; require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/Types/MediaField.php'; }

echo "== Task 2.5: media picker ==\n";
check( 'media renderer template and picker script exist', fn() => $available );
if ( $available ) {
	$field = array( 'id' => 'header.logo', 'type' => 'media', 'label' => 'لوگو', 'default' => 0, 'media_kind' => 'image' );
	$html = \ErfanSanat\Theme\Admin\Fields\Types\MediaField::render( $field, 12, 'es_options[header][logo]', 'es-logo' );
	check( 'single picker renders the selected ID, preview and remove button', fn() => str_contains( $html, 'value="12"' ) && str_contains( $html, 'https://media.test/image.jpg' ) && str_contains( $html, 'data-es-media-select' ) && str_contains( $html, 'data-es-media-remove' ) );
	check( 'server rejects non-attachment IDs and keeps a valid image ID', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( $field, '12' ) === 12 && \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( $field, '999999' ) === 0 );
	$multi = array_merge( $field, array( 'multiple' => true, 'default' => '' ) );
	check( 'multiple selection sanitizes to unique attachment-ID CSV', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( $multi, '12,999999,12,abc' ) === '12' );
	$multi_html = \ErfanSanat\Theme\Admin\Fields\Types\MediaField::render( $multi, '12,999999,12', 'es_options[gallery]', 'es-gallery' );
	check( 'multiple picker stores a valid CSV and removal clears instead of undefined', fn() => str_contains( $multi_html, 'value="12"' ) && str_contains( $multi_html, 'data-es-media-mode="multiple"' ) && str_contains( (string) file_get_contents( $media_js ), "input.value = '';" ) && ! str_contains( (string) file_get_contents( $media_js ), 'undefined' ) );
} else {
	for ( $i = 1; $i <= 4; $i++ ) { check( 'media picker condition ' . $i, fn() => false ); }
}
summary();
