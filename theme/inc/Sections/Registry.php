<?php
/**
 * Registry of editable theme sections and their declarative field schemas.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Sections;

defined( 'ABSPATH' ) || exit;

/** Resolves current section schemas, including WordPress extension filters. */
final class Registry {

	/** @var array<string,string> */
	private const SCHEMA_FILES = array(
		'home'       => 'home',
		'about'      => 'about',
		'contact'    => 'contact',
		'faq'        => 'faq',
		'licenses'   => 'licenses',
		'warranty'   => 'warranty',
		'recruitment'=> 'recruitment',
	);

	/** Return the section schema map for one page key. */
	public static function get( string $page = 'home' ): array {
		$all = self::all();
		return isset( $all[ $page ] ) && is_array( $all[ $page ] ) ? $all[ $page ] : array();
	}

	/** Return all page schemas; extensions may add, remove, or amend sections. */
	public static function all(): array {
		$schemas = array();
		foreach ( self::SCHEMA_FILES as $page => $file ) {
			$path = __DIR__ . '/Schema/' . $file . '.php';
			$items = is_file( $path ) ? require $path : array();
			$schemas[ $page ] = is_array( $items ) ? $items : array();
		}

		if ( function_exists( 'apply_filters' ) ) {
			$filtered = apply_filters( 'es_sections_registry', $schemas );
			if ( is_array( $filtered ) ) {
				$schemas = $filtered;
			}
		}

		foreach ( $schemas as $page => $sections ) {
			if ( ! is_string( $page ) || ! self::valid_key( $page ) || ! is_array( $sections ) ) {
				unset( $schemas[ $page ] );
				continue;
			}
			$schemas[ $page ] = self::validate_sections( $sections );
		}
		return $schemas;
	}

	/** Return the canonical page keys. */
	public static function pages(): array {
		return array_keys( self::all() );
	}

	/** Validate extension-provided sections and normalize their map keys. */
	private static function validate_sections( array $sections ): array {
		$valid = array();
		foreach ( $sections as $key => $section ) {
			if ( ! is_string( $key ) || ! self::valid_key( $key ) || ! is_array( $section ) ) {
				continue;
			}
			$section_key = $section['key'] ?? $key;
			if ( $section_key !== $key || ! isset( $section['label'], $section['template'] ) || ! is_scalar( $section['label'] ) || ! is_string( $section['template'] ) || ! preg_match( '~^template-parts/[A-Za-z0-9_/-]+\\.php$~', $section['template'] ) || str_contains( $section['template'], '..' ) ) {
				continue;
			}
			$fields = is_array( $section['fields'] ?? null ) ? $section['fields'] : array();
			$data = is_array( $section['default_data'] ?? null ) ? $section['default_data'] : array();
			$valid[ $key ] = array(
				'key'          => $key,
				'label'        => (string) $section['label'],
				'template'     => $section['template'],
				'fields'       => self::validate_fields( $fields, $data ),
				'default_data' => $data,
			);
		}
		return $valid;
	}

	/** Retain only field definitions with a safe identifier and supported type. */
	private static function validate_fields( array $fields, array $default_data ): array {
		$valid = array();
		$types = array( 'text', 'textarea', 'code', 'url', 'email', 'tel', 'number', 'color', 'media', 'toggle', 'select', 'checkbox-group', 'repeater' );
		$seen = array();
		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) || ! isset( $field['id'], $field['type'], $field['label'] ) || ! is_string( $field['id'] ) || ! preg_match( '/^[A-Za-z][A-Za-z0-9_-]*$/', $field['id'] ) || isset( $seen[ $field['id'] ] ) || ! in_array( $field['type'], $types, true ) || ! is_scalar( $field['label'] ) ) {
				continue;
			}
			$field['default'] = $field['default'] ?? ( $default_data[ $field['id'] ] ?? '' );
			$valid[] = $field;
			$seen[ $field['id'] ] = true;
		}
		return $valid;
	}

	/** Restrict identifiers used as registry/page keys. */
	private static function valid_key( string $key ): bool {
		return 1 === preg_match( '/^[a-z][a-z0-9_-]*$/', $key );
	}
}
