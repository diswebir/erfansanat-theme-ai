<?php
/** Task 2.3 — declarative field schema, safe rendering and type sanitizers. */
declare(strict_types=1);

$bootstrap_candidates = array( __DIR__ . '/../../../../temp/tests/bootstrap.php', __DIR__ . '/../../tests/bootstrap.php' );
foreach ( $bootstrap_candidates as $bootstrap ) { if ( is_file( $bootstrap ) ) { require_once $bootstrap; break; } }
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_textarea' ) ) { function esc_textarea( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_html__' ) ) { function esc_html__( $s, $domain = null ) { return esc_html( $s ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $s ) { return preg_replace('/[^a-z0-9_\\-]/', '', strtolower((string)$s)); } }
if ( ! function_exists( 'sanitize_html_class' ) ) { function sanitize_html_class( $s ) { return preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$s); } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs((int)$v); } }
if ( ! function_exists( 'get_post' ) ) { function get_post( $id ) { return 12 === (int) $id ? (object) array( 'ID' => 12, 'post_type' => 'attachment' ) : null; } }
if ( ! function_exists( 'wp_attachment_is_image' ) ) { function wp_attachment_is_image( $id ) { return 12 === (int) $id; } }
if ( ! function_exists( 'wp_nonce_field' ) ) { function wp_nonce_field( $action, $name ) { return '<input type="hidden" name="' . esc_attr( $name ) . '" value="nonce">'; } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return is_scalar( $v ) ? trim(strip_tags((string)$v)) : ''; } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $v ) { return strip_tags((string)$v, '<p><a><strong><em><br>'); } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $v ) { return preg_match('~^https?://~i', (string)$v) ? filter_var($v, FILTER_SANITIZE_URL) : ''; } }
if ( ! function_exists( 'sanitize_email' ) ) { function sanitize_email( $v ) { return filter_var((string)$v, FILTER_SANITIZE_EMAIL) ?: ''; } }
if ( ! function_exists( 'sanitize_hex_color' ) ) { function sanitize_hex_color( $v ) { return preg_match('/^#[a-f0-9]{3}(?:[a-f0-9]{3})?$/i', (string)$v) ? $v : null; } }

$fields_dir = ES_REPO_ROOT . '/theme/inc/Admin/Fields/';
$files_exist = is_file( $fields_dir . 'Schema.php' ) && is_file( $fields_dir . 'FieldRenderer.php' ) && is_file( $fields_dir . 'Sanitizer.php' );
if ( $files_exist ) {
	require_once $fields_dir . 'Schema.php';
	require_once $fields_dir . 'Sanitizer.php';
	require_once $fields_dir . 'FieldRenderer.php';
}

function es_test_field( $id, $type, $extra = array() ) {
	return array_merge( array( 'id' => $id, 'type' => $type, 'label' => ucfirst( $id ), 'default' => '' ), $extra );
}

echo "== Task 2.3: field framework ==\n";
check( 'schema, sanitizer and renderer exist', fn() => $files_exist );
if ( $files_exist ) {
	\ErfanSanat\Theme\Admin\Fields\Schema::reset();
	$types = array( 'text', 'textarea', 'url', 'email', 'tel', 'number', 'color', 'media', 'toggle', 'select', 'checkbox-group', 'repeater' );
	$register_ok = true;
	foreach ( $types as $i => $type ) {
		$f = es_test_field( 'test.field_' . $i, $type, array( 'options' => array( 'one' => 'One', 'two' => 'Two' ), 'fields' => array( array( 'id' => 'label', 'type' => 'text', 'label' => 'Label' ) ) ) );
		$register_ok = $register_ok && \ErfanSanat\Theme\Admin\Fields\Schema::register( $f );
	}
	check( 'schema accepts every supported field type', fn() => $register_ok && count( \ErfanSanat\Theme\Admin\Fields\Schema::all() ) === count( $types ) );
	check( 'schema rejects unknown types and malformed IDs', fn() => ! \ErfanSanat\Theme\Admin\Fields\Schema::register( es_test_field( 'bad.id', 'arbitrary-html' ) ) && ! \ErfanSanat\Theme\Admin\Fields\Schema::register( es_test_field( '../x', 'text' ) ) );
	check( 'renderer emits every field type and a per-page nonce', function () use ( $types ) {
		$html = \ErfanSanat\Theme\Admin\Fields\FieldRenderer::render_fields( \ErfanSanat\Theme\Admin\Fields\Schema::all(), 'test-page' );
		return str_contains( $html, 'es_options_nonce_test-page' ) && substr_count( $html, 'class="es-field es-field--' ) >= count( $types ) && str_contains( $html, 'data-es-media' ) && str_contains( $html, 'data-es-repeater' );
	} );
	check( 'renderer escapes labels, values, and generated option names', function () {
		$f = es_test_field( 'header.title', 'text', array( 'label' => 'Title <script>', 'default' => '"><img src=x onerror=1>' ) );
		$html = \ErfanSanat\Theme\Admin\Fields\FieldRenderer::render( $f );
		return str_contains( $html, 'es_options[header][title]' ) && ! str_contains( $html, '<script>' ) && ! str_contains( $html, '<img' ) && str_contains( $html, '&quot;&gt;');
	} );
	check( 'text sanitizer strips markup and script payloads', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'text' ), '<script>alert(1)</script>Safe' ) === 'alert(1)Safe' );
	check( 'textarea sanitizer keeps allowed formatting but strips script tags', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'textarea' ), '<p>ok</p><script>x</script>' ) === '<p>ok</p>x' );
	check( 'URL sanitizer rejects javascript protocols', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'url' ), 'javascript:alert(1)' ) === '' );
	check( 'email and telephone sanitizer remove unsafe markup', function () { $email = \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'email' ), 'a@b.test<script>' ); $tel = \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'tel' ), '۰۳۱-۱۲۳"><script>' ); return str_starts_with( $email, 'a@b.test' ) && ! str_contains( $email, '<' ) && $tel === '۰۳۱-۱۲۳'; } );
	check( 'number and color sanitizer enforce their formats', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'number' ), '12.5px' ) === '' && \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'color', array( 'default' => '#fff' ) ), 'red' ) === '#fff' );
	check( 'select rejects a value outside its whitelist', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'select', array( 'options' => array( 'a' => 'A', 'b' => 'B' ), 'default' => 'a' ) ), 'javascript:' ) === 'a' );
	check( 'toggle stores only a boolean', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'toggle' ), 'yes' ) === true && \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'toggle' ), '0' ) === false );
	check( 'checkbox-group whitelists and deduplicates values', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'checkbox-group', array( 'options' => array( 'a' => 'A', 'b' => 'B' ) ) ), array( 'a', 'bad', 'a' ) ) === array( 'a' ) );
	check( 'media sanitization accepts only a positive attachment identifier', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'media', array( 'default' => 0 ) ), '12' ) === 12 && \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'media', array( 'default' => 0 ) ), '999999' ) === 0 );
	check( 'repeater sanitizer filters nested fields and drops empty rows', fn() => \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( es_test_field( 'x', 'repeater', array( 'fields' => array( array( 'id' => 'label', 'type' => 'text', 'label' => 'Label' ) ) ) ), array( 'item-0' => array( 'label' => 'ok<script>', 'surprise' => 'bad' ), 'item-1' => array( 'label' => '' ) ) ) === array( 'item-0' => array( 'label' => 'ok' ) ) );
} else {
	for ( $i = 1; $i < 13; $i++ ) { check( 'field framework condition ' . $i, fn() => false ); }
}

summary();
