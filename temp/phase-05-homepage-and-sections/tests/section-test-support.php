<?php
/** Shared PHP-WASM assertions for schema-backed static homepage sections. */
if ( ! function_exists( 'esc_html' ) ) { function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_attr' ) ) { function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'esc_url' ) ) { function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); } }
if ( ! function_exists( 'get_template_directory_uri' ) ) { function get_template_directory_uri() { return 'https://example.test/theme'; } }
require_once ES_REPO_ROOT . '/theme/inc/Sections/Registry.php';
require_once ES_REPO_ROOT . '/theme/inc/Sections/HomeSectionMarkup.php';
require_once ES_REPO_ROOT . '/tools/harness/dom-diff.php';

/** Run contract, schema-overwrite, fallback and warning checks for one extracted section. */
function run_p5_fragment_checks( string $section, string $template, int $reference_index, string $label, array $extra_data = array() ): void {
	$schema = \ErfanSanat\Theme\Sections\Registry::get( 'home' )[ $section ] ?? array();
	$json_path = ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/' . $section . '.json';
	$defaults = is_file( $json_path ) ? json_decode( (string) file_get_contents( $json_path ), true ) : array();
	$source = (string) file_get_contents( ES_REPO_ROOT . '/theme/' . $template );
	$args = array( 'section' => $section, 'data' => array_merge( array( 'content_json' => (string) file_get_contents( $json_path ) ), $extra_data ) );
	$warnings = array();
	set_error_handler( static function ( $severity, $message ) use ( &$warnings ) { $warnings[] = $message; return true; } );
	$render = static function () use ( &$args, $template ): string { ob_start(); include ES_REPO_ROOT . '/theme/' . $template; return (string) ob_get_clean(); };
	$output = $render();
	$reference = (string) file_get_contents( ES_REPO_ROOT . '/reference/static-mirror/index.html' );
	preg_match_all( '/<section\b[^>]*>|<\/section\s*>/i', $reference, $section_tags, PREG_OFFSET_CAPTURE );
	$depth = 0; $roots = array(); $root_start = null;
	foreach ( $section_tags[0] as $tag ) {
		if ( str_starts_with( strtolower( $tag[0] ), '</' ) ) {
			--$depth;
			if ( 0 === $depth && null !== $root_start ) { $roots[] = substr( $reference, $root_start, $tag[1] + strlen( $tag[0] ) - $root_start ); $root_start = null; }
		} else {
			if ( 0 === $depth ) { $root_start = $tag[1]; }
			++$depth;
		}
	}
	$reference_fragment = $roots[ $reference_index ] ?? '';
	$similarity = \ErfanSanat\Harness\DomDiff::similarity( $reference_fragment, $output );
	check( $label . ' — DOM با مرجع منطبق است', fn() => $similarity >= 99.0 && '' !== $reference_fragment );
	check( $label . ' — shell فاقد متن هاردکد و به markup renderer متصل است', fn() => ! preg_match( '/[\x{0600}-\x{06FF}]/u', $source ) && str_contains( $source, 'HomeSectionMarkup::render' ) && isset( $schema['fields'] ) );
	$token = '';
	foreach ( $defaults as $key => $value ) { if ( 'text' === ( $value['kind'] ?? '' ) ) { $token = (string) $key; break; } }
	$changed = $defaults;
	if ( '' !== $token ) { $changed[ $token ]['value'] = 'Dashboard edit ' . $section; }
	$args['data']['content_json'] = json_encode( $changed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	$edited = $render();
	check( $label . ' — ویرایش Schema روی خروجی اثر دارد', fn() => '' !== $token && str_contains( $edited, 'Dashboard edit ' . $section ) );
	$args['data']['content_json'] = '{invalid json';
	$fallback = $render();
	restore_error_handler();
	check( $label . ' — داده نامعتبر به fallback امن برمی‌گردد', fn() => str_contains( $fallback, '__ES_' ) === false && empty( $warnings ) && '' !== $fallback );
}
