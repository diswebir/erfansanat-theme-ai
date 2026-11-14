<?php
/**
 * Legacy meta codec (replaces JetEngine storage handling).
 *
 * Reads and writes the three live storage formats without destroying them:
 *   - PHP serialized arrays (WordPress auto-serializes arrays),
 *   - JSON objects keyed `item-0`, `item-1`, … (JetEngine repeaters),
 *   - JSON plain lists.
 *
 * On re-save the original format is preserved (AC-03). Corrupt data falls back
 * safely (AC-04). Only the exact (historically-spelled) key is ever written —
 * no corrected-spelling variant keys are created (SC-02). Works for both post
 * and term meta.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class LegacyMeta {

	public const FMT_PHP   = 'php';
	public const FMT_JSON  = 'json';
	public const FMT_EMPTY = 'empty';

	/**
	 * Read a post meta value as a normalized list of items.
	 *
	 * @param array<int,mixed> $default Fallback for empty/corrupt values.
	 * @return array<int,mixed>
	 */
	public static function get( int $post_id, string $key, array $default = array() ): array {
		return self::decode( get_metadata( 'post', $post_id, $key, true ), $default );
	}

	/**
	 * Write a post meta value, preserving its existing storage format.
	 *
	 * @param array<int,mixed> $items  Items to store.
	 * @param string           $format Force a format (optional).
	 */
	public static function set( int $post_id, string $key, array $items, string $format = '' ): bool {
		return self::write( 'post', $post_id, $key, $items, $format );
	}

	/**
	 * Read a term meta value as a normalized list of items.
	 *
	 * @param array<int,mixed> $default Fallback.
	 * @return array<int,mixed>
	 */
	public static function get_for_term( int $term_id, string $key, array $default = array() ): array {
		return self::decode( get_metadata( 'term', $term_id, $key, true ), $default );
	}

	/**
	 * Write a term meta value, preserving its existing storage format.
	 *
	 * @param array<int,mixed> $items  Items.
	 * @param string           $format Force a format (optional).
	 */
	public static function set_for_term( int $term_id, string $key, array $items, string $format = '' ): bool {
		return self::write( 'term', $term_id, $key, $items, $format );
	}

	/**
	 * Decode a raw meta value into a sequential list of items.
	 *
	 * @param mixed            $raw     Raw stored value.
	 * @param array<int,mixed> $default Fallback.
	 * @return array<int,mixed>
	 */
	public static function decode( $raw, array $default = array() ): array {
		if ( is_array( $raw ) ) {
			return self::normalize_items( $raw );
		}

		if ( is_string( $raw ) && '' !== trim( $raw ) ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				return self::normalize_items( $decoded );
			}
			$unserialized = maybe_unserialize( $raw );
			if ( is_array( $unserialized ) ) {
				return self::normalize_items( $unserialized );
			}
			return $default; // Corrupt string.
		}

		return $default; // Empty.
	}

	/**
	 * Detect the storage format of a raw value.
	 */
	public static function detect_format( $raw ): string {
		if ( is_array( $raw ) ) {
			return self::FMT_PHP;
		}
		if ( is_string( $raw ) && '' !== trim( $raw ) && is_array( json_decode( $raw, true ) ) ) {
			return self::FMT_JSON;
		}
		return self::FMT_EMPTY;
	}

	/**
	 * Is this array an `item-N` keyed map?
	 *
	 * @param array<mixed> $data Data.
	 */
	public static function is_item_map( array $data ): bool {
		foreach ( array_keys( $data ) as $k ) {
			if ( is_string( $k ) && preg_match( '/^item-\d+$/', $k ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Normalize an item-map or list into a sequential list (preserves order).
	 *
	 * @param array<mixed> $data Data.
	 * @return array<int,mixed>
	 */
	public static function normalize_items( array $data ): array {
		if ( self::is_item_map( $data ) ) {
			$items = array();
			foreach ( $data as $k => $v ) {
				if ( is_string( $k ) && preg_match( '/^item-(\d+)$/', $k, $m ) ) {
					$items[ (int) $m[1] ] = $v;
				}
			}
			ksort( $items );
			return array_values( $items );
		}
		return array_values( $data );
	}

	/**
	 * Build an `item-N` keyed map from a list.
	 *
	 * @param array<int,mixed> $items Items.
	 * @return array<string,mixed>
	 */
	public static function to_item_map( array $items ): array {
		$out = array();
		foreach ( array_values( $items ) as $i => $item ) {
			$out[ 'item-' . $i ] = $item;
		}
		return $out;
	}

	/**
	 * Write meta preserving the detected (or forced) format.
	 *
	 * @param array<int,mixed> $items Items.
	 */
	private static function write( string $meta_type, int $object_id, string $key, array $items, string $format = '' ): bool {
		$raw = get_metadata( $meta_type, $object_id, $key, true );

		if ( '' === $format ) {
			$format = self::detect_format( $raw );
		}
		if ( self::FMT_EMPTY === $format ) {
			$format = self::FMT_PHP; // New values default to a PHP array.
		}

		$items = array_values( $items );

		if ( self::FMT_JSON === $format ) {
			$existing     = is_string( $raw ) ? json_decode( $raw, true ) : null;
			$use_item_map = is_array( $existing ) ? self::is_item_map( $existing ) : true;
			$payload      = $use_item_map ? self::to_item_map( $items ) : $items;
			$value        = wp_json_encode( $payload );
		} else {
			$value = $items; // PHP array (WordPress serializes on store).
		}

		// update_metadata() wp_unslash()es the value, so slash it first to keep
		// JSON backslash escapes (\uXXXX, \") and array strings intact.
		return false !== update_metadata( $meta_type, $object_id, $key, wp_slash( $value ) );
	}
}
