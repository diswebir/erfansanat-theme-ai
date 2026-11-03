<?php
/**
 * Read/write adapter for native arrays, JetEngine item-N JSON and PHP serialized repeaters.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin\Fields;

defined( 'ABSPATH' ) || exit;

/**
 * Decodes repeater values safely and preserves their original storage encoding.
 */
final class RepeaterCodec {

	/** Decode arrays, JSON strings and PHP-serialized arrays. */
	public static function decode( $value ): array {
		if ( is_string( $value ) ) {
			$format = self::format( $value );
			if ( 'json' === $format ) {
				$decoded = json_decode( $value, true );
				$value   = is_array( $decoded ) ? $decoded : array();
			} elseif ( 'serialized' === $format ) {
				$decoded = @unserialize( $value, array( 'allowed_classes' => false ) );
				$value   = is_array( $decoded ) ? $decoded : array();
			} else {
				$value = array();
			}
		} elseif ( is_object( $value ) ) {
			$value = (array) $value;
		}
		if ( ! is_array( $value ) ) {
			return array();
		}
		return self::normalize( $value );
	}

	/** Identify the original representation. */
	public static function format( $value ): string {
		if ( is_array( $value ) ) {
			return 'array';
		}
		if ( ! is_string( $value ) ) {
			return 'array';
		}
		$trimmed = trim( $value );
		if ( '' !== $trimmed && ( '{' === $trimmed[0] || '[' === $trimmed[0] ) ) {
			$decoded = json_decode( $trimmed, true );
			if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ) {
				return 'json';
			}
		}
		if ( preg_match( '/^(?:a|O|s|i|b|d|N):/', $trimmed ) || 'N;' === $trimmed ) {
			return 'serialized';
		}
		return 'unknown';
	}

	/** Encode rows in the specified representation. */
	public static function encode( array $rows, string $format = 'array' ) {
		$normalized = self::normalize( $rows );
		if ( 'json' === $format ) {
			$json = json_encode( $normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			return is_string( $json ) ? $json : '{}';
		}
		if ( 'serialized' === $format ) {
			return serialize( $normalized );
		}
		return $normalized;
	}

	/** Encode an updated value using the source value's representation. */
	public static function preserve_format( $original, array $rows ) {
		$format = self::format( $original );
		return self::encode( $rows, 'unknown' === $format ? 'array' : $format );
	}

	/** Normalize arbitrary row indexes to the JetEngine `item-N` contract. */
	private static function normalize( array $rows ): array {
		$normalized = array();
		$counter    = 0;
		foreach ( $rows as $key => $row ) {
			if ( ! is_array( $row ) && ! is_object( $row ) ) {
				continue;
			}
			if ( is_object( $row ) ) {
				$row = (array) $row;
			}
			$row_key = is_string( $key ) && preg_match( '/^item-\d+$/', $key ) ? $key : 'item-' . $counter;
			$normalized[ $row_key ] = $row;
			$counter++;
		}
		return $normalized;
	}
}
