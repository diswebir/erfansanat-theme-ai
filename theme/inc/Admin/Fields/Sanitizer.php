<?php
/**
 * Type-aware sanitization for the theme's admin field schema.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin\Fields;

defined( 'ABSPATH' ) || exit;

/**
 * Sanitizes a posted field value against its registered schema.
 */
final class Sanitizer {

	/** Sanitize one value according to a field definition. */
	public static function sanitize( array $field, $value ) {
		$type    = $field['type'] ?? '';
		$default = $field['default'] ?? '';
		if ( ! in_array( $type, Schema::types(), true ) ) {
			return $default;
		}

		switch ( $type ) {
			case 'text':
				return is_scalar( $value ) ? sanitize_text_field( (string) $value ) : $default;

			case 'textarea':
				return is_scalar( $value ) ? wp_kses_post( (string) $value ) : $default;

			case 'url':
				return is_scalar( $value ) ? esc_url_raw( trim( (string) $value ) ) : $default;

			case 'email':
				return is_scalar( $value ) ? sanitize_email( (string) $value ) : $default;

			case 'tel':
				if ( ! is_scalar( $value ) ) {
					return $default;
				}
				$phone = sanitize_text_field( (string) $value );
				return preg_replace( '/[^\p{N}+().\-\s]/u', '', $phone ) ?? '';

			case 'number':
				if ( ! is_scalar( $value ) || '' === trim( (string) $value ) || ! is_numeric( $value ) ) {
					return $default;
				}
				$number = (float) $value;
				if ( isset( $field['min'] ) && $number < (float) $field['min'] ) {
					return $default;
				}
				if ( isset( $field['max'] ) && $number > (float) $field['max'] ) {
					return $default;
				}
				return ( isset( $field['step'] ) && '1' === (string) $field['step'] ) ? (int) $number : $number;

			case 'color':
				$color = is_scalar( $value ) ? sanitize_hex_color( (string) $value ) : null;
				return is_string( $color ) ? $color : $default;

			case 'toggle':
				return in_array( $value, array( true, 1, '1', 'on', 'yes', 'true' ), true );

			case 'select':
				$options = is_array( $field['options'] ?? null ) ? $field['options'] : array();
				$key     = is_scalar( $value ) ? (string) $value : '';
				if ( array_key_exists( $key, $options ) ) {
					return $key;
				}
				$fallback = is_scalar( $default ) ? (string) $default : '';
				return array_key_exists( $fallback, $options ) ? $fallback : (string) ( array_key_first( $options ) ?? '' );

			case 'checkbox-group':
				$options = is_array( $field['options'] ?? null ) ? $field['options'] : array();
				$values  = is_array( $value ) ? $value : array();
				$clean   = array();
				foreach ( $values as $item ) {
					$key = is_scalar( $item ) ? (string) $item : '';
					if ( array_key_exists( $key, $options ) && ! in_array( $key, $clean, true ) ) {
						$clean[] = $key;
					}
				}
				return $clean;

			case 'media':
				return is_scalar( $value ) ? absint( $value ) : 0;

			case 'repeater':
				return self::sanitize_repeater( $field, $value );
		}

		return $default;
	}

	/** Sanitize nested repeater rows while preserving JetEngine item-N keys. */
	private static function sanitize_repeater( array $field, $value ): array {
		if ( is_string( $value ) ) {
			$decoded = json_decode( $value, true );
			$value   = is_array( $decoded ) ? $decoded : array();
		}
		if ( ! is_array( $value ) ) {
			return array();
		}

		$definitions = is_array( $field['fields'] ?? null ) ? $field['fields'] : array();
		$rows        = array();
		$counter     = 0;
		foreach ( $value as $key => $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$clean = array();
			foreach ( $definitions as $subfield ) {
				if ( ! is_array( $subfield ) || empty( $subfield['id'] ) || ! array_key_exists( $subfield['id'], $row ) ) {
					continue;
				}
				$clean[ $subfield['id'] ] = self::sanitize( $subfield, $row[ $subfield['id'] ] );
			}
			$non_empty = array_filter( $clean, static function ( $item ) {
				return ! ( '' === $item || null === $item || array() === $item );
			} );
			if ( $non_empty ) {
				$row_key = is_string( $key ) && preg_match( '/^item-\d+$/', $key ) ? $key : 'item-' . $counter;
				$rows[ $row_key ] = $clean;
				$counter++;
			}
		}
		return $rows;
	}
}
