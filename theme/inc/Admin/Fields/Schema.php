<?php
/**
 * Declarative allow-list for admin settings fields.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin\Fields;

defined( 'ABSPATH' ) || exit;

/**
 * Stores validated field definitions used by rendering and secure saves.
 */
final class Schema {

	/** @var array<string,array<string,mixed>> */
	private static array $fields = array();

	/** @var string[] */
	private const TYPES = array( 'text', 'textarea', 'code', 'url', 'email', 'tel', 'number', 'color', 'media', 'toggle', 'select', 'checkbox-group', 'repeater' );

	/** Register one field definition. */
	public static function register( array $field ): bool {
		$id   = isset( $field['id'] ) && is_string( $field['id'] ) ? trim( $field['id'] ) : '';
		$type = isset( $field['type'] ) && is_string( $field['type'] ) ? $field['type'] : '';
		if ( ! self::valid_path( $id ) || ! in_array( $type, self::TYPES, true ) || ! isset( $field['label'] ) || ! is_scalar( $field['label'] ) ) {
			return false;
		}
		if ( in_array( $type, array( 'select', 'checkbox-group' ), true ) && ( empty( $field['options'] ) || ! is_array( $field['options'] ) ) ) {
			return false;
		}
		if ( 'repeater' === $type && ( empty( $field['fields'] ) || ! is_array( $field['fields'] ) ) ) {
			return false;
		}

		$field['id']    = $id;
		$field['label'] = (string) $field['label'];
		$field['type']  = $type;
		$field['default'] = $field['default'] ?? '';
		self::$fields[ $id ] = $field;
		return true;
	}

	/** Register a list atomically: malformed definitions are not added. */
	public static function register_many( array $fields ): bool {
		$before = self::$fields;
		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) || ! self::register( $field ) ) {
				self::$fields = $before;
				return false;
			}
		}
		return true;
	}

	/** Get all registered definitions. */
	public static function all(): array {
		return self::$fields;
	}

	/** Get a field definition by its dot path. */
	public static function get( string $id ): ?array {
		return self::$fields[ $id ] ?? null;
	}

	/** Return supported field types. */
	public static function types(): array {
		return self::TYPES;
	}

	/** Clear schema definitions; primarily useful to isolate test or bootstrap contexts. */
	public static function reset(): void {
		self::$fields = array();
	}

	/** Check a safe settings path; dot segments become nested option keys. */
	public static function valid_path( string $id ): bool {
		return 1 === preg_match( '/^[A-Za-z][A-Za-z0-9_-]*(?:\.[A-Za-z][A-Za-z0-9_-]*)*$/', $id );
	}
}
