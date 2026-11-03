<?php
/** Task 2.6 — JetEngine-compatible repeater codec and sortable field UI. */
declare(strict_types=1);

$bootstrap_candidates = array( __DIR__ . '/../../../../temp/tests/bootstrap.php', __DIR__ . '/../../tests/bootstrap.php' );
foreach ( $bootstrap_candidates as $bootstrap ) { if ( is_file( $bootstrap ) ) { require_once $bootstrap; break; } }
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $s, $domain = null ) { return esc_html( $s ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_textarea' ) ) { function esc_textarea( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs( (int) $v ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return is_scalar( $v ) ? trim( strip_tags( (string) $v ) ) : ''; } }

$codec_file = ES_REPO_ROOT . '/theme/inc/Admin/Fields/RepeaterCodec.php';
$ui_file = ES_REPO_ROOT . '/theme/inc/Admin/Fields/types/repeater.php';
$js_file = ES_REPO_ROOT . '/theme/assets/admin/repeater.js';
$js_test = ES_REPO_ROOT . '/tools/harness/test-repeater.mjs';
$sanitizer_file = ES_REPO_ROOT . '/theme/inc/Admin/Fields/Sanitizer.php';
$available = is_file( $codec_file ) && is_file( $ui_file ) && is_file( $js_file ) && is_file( $js_test ) && is_file( $sanitizer_file );
if ( $available ) {
	require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/Schema.php';
	require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/FieldRenderer.php';
	require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/RepeaterCodec.php';
	require_once $sanitizer_file;
	require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/Types/RepeaterField.php';
}

echo "== Task 2.6: repeater codec and editor ==\n";
check( 'repeater codec, template and JavaScript exist', fn() => $available );
if ( $available ) {
	$es_repeater_rows = array( 'item-0' => array( 'label' => 'first' ), 'item-1' => array( 'label' => 'second' ) );
	$json = json_encode( $es_repeater_rows, JSON_UNESCAPED_UNICODE );
	$serialized = serialize( $es_repeater_rows );
	check( 'JetEngine item-N arrays decode without losing order', fn() => \ErfanSanat\Theme\Admin\Fields\RepeaterCodec::decode( $es_repeater_rows ) === $es_repeater_rows );
	check( 'JSON and PHP-serialized formats decode to the same repeater rows', fn() => \ErfanSanat\Theme\Admin\Fields\RepeaterCodec::decode( $json ) === $es_repeater_rows && \ErfanSanat\Theme\Admin\Fields\RepeaterCodec::decode( $serialized ) === $es_repeater_rows );
	check( 'round-trip encoding preserves array, JSON and serialized source formats', function () use ( $es_repeater_rows, $json, $serialized ) {
		$changed = array( 'item-0' => array( 'label' => 'second' ), 'item-1' => array( 'label' => 'first' ) );
		return \ErfanSanat\Theme\Admin\Fields\RepeaterCodec::preserve_format( $es_repeater_rows, $changed ) === $changed
			&& json_decode( \ErfanSanat\Theme\Admin\Fields\RepeaterCodec::preserve_format( $json, $changed ), true ) === $changed
			&& unserialize( \ErfanSanat\Theme\Admin\Fields\RepeaterCodec::preserve_format( $serialized, $changed ), array( 'allowed_classes' => false ) ) === $changed;
	} );
	$field = array( 'id' => 'social.links', 'type' => 'repeater', 'label' => 'لینک‌ها', 'fields' => array( array( 'id' => 'label', 'type' => 'text', 'label' => 'عنوان' ) ) );
	$html = \ErfanSanat\Theme\Admin\Fields\Types\RepeaterField::render( $field, $es_repeater_rows, 'es_options[social][links]', 'es-social-links' );
	check( 'repeater renders persistent keyed rows, drag handle, keyboard controls and an add template', fn() => str_contains( $html, 'data-es-repeater-item="item-0"' ) && str_contains( $html, 'data-es-repeater-handle' ) && str_contains( $html, 'data-es-repeater-template' ) && str_contains( $html, 'data-es-repeater-add' ) );
	$clean = \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( $field, serialize( array( 'item-0' => array( 'label' => '' ), 'item-1' => array( 'label' => 'kept<script>' ) ) ) );
	check( 'serialized empty rows are dropped and remaining values sanitized', fn() => $clean === array( 'item-1' => array( 'label' => 'kept' ) ) );
	$js = (string) file_get_contents( $js_file );
	check( 'repeater UI is dependency-free and supports keyboard reordering', fn() => ! str_contains( strtolower( $js ), 'jquery' ) && str_contains( $js, 'ArrowUp' ) && str_contains( $js, 'ArrowDown' ) && str_contains( $js, 'data-es-repeater-remove' ) );
} else {
	for ( $i = 1; $i <= 5; $i++ ) { check( 'repeater editor condition ' . $i, fn() => false ); }
}
summary();
