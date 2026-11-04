<?php
/**
 * Read/write layer for the historical JetEngine meta keys.
 *
 * The live site stores some values as JSON, some as PHP-serialised arrays, some
 * as comma separated strings and some as plain text. The theme must round-trip
 * whatever is already there without rewriting it, so every read goes through a
 * format detector and every write keeps the format that was found.
 *
 * Keys keep their historical spelling (`foure-image`, `galerry_produ`,
 * `usage_countr`, `english_titl`, `vaule_of_variable`) and must never be
 * "corrected".
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Format-aware access to legacy meta.
 */
final class LegacyMeta {

	/**
	 * Repeater keys and the sub-fields each one uses.
	 *
	 * @return array<string, array<int, string>>
	 */
	public static function repeater_fields(): array {
		return array(
			'product-feature'       => array( 'add-new-feature' ),
			'product-feature2_copy' => array( 'add-new-feature', 'vaule_of_variable' ),
			'usage_product'         => array( 'add-new-usage' ),
			'faq'                   => array( 'name_question', 'answer_question' ),
		);
	}

	/**
	 * Keys stored as a list of ids (or term ids).
	 *
	 * @return array<int, string>
	 */
	public static function relation_keys(): array {
		return array( 'project-ids', 'mortabet_mahsole_2', 'usage_countr' );
	}

	/**
	 * Image-slot keys of the project gallery, in display order.
	 *
	 * @return array<int, string>
	 */
	public static function project_image_slots(): array {
		return array( 'second-image', 'three-image', 'foure-image', 'five-image', 'six-image' );
	}

	/**
	 * Detect the storage format of a raw value.
	 *
	 * @param mixed $raw Stored value.
	 * @return string One of json|serialized|comma|array|empty|scalar.
	 */
	public static function detect_format( $raw ): string {
		if ( null === $raw || '' === $raw || array() === $raw ) {
			return 'empty';
		}
		if ( is_array( $raw ) ) {
			return 'array';
		}
		if ( ! is_string( $raw ) ) {
			return 'scalar';
		}
		$trimmed = trim( $raw );
		if ( '' === $trimmed ) {
			return 'empty';
		}
		$first = $trimmed[0];
		if ( '{' === $first || '[' === $first ) {
			$decoded = json_decode( $trimmed, true );
			if ( JSON_ERROR_NONE === json_last_error() && ( is_array( $decoded ) || is_object( $decoded ) ) ) {
				return 'json';
			}
		}
		if ( 0 === strpos( $trimmed, 'a:' ) && is_array( maybe_unserialize( $trimmed ) ) ) {
			return 'serialized';
		}
		if ( false !== strpos( $trimmed, ',' ) ) {
			return 'comma';
		}
		return 'scalar';
	}

	/**
	 * Decode any supported storage format into a PHP array.
	 *
	 * @param mixed $raw Stored value.
	 * @return array<mixed>
	 */
	public static function to_array( $raw ): array {
		switch ( self::detect_format( $raw ) ) {
			case 'array':
				return (array) $raw;
			case 'json':
				$decoded = is_string( $raw ) ? json_decode( $raw, true ) : null;
				return is_array( $decoded ) ? $decoded : array();
			case 'serialized':
				$decoded = maybe_unserialize( $raw );
				return is_array( $decoded ) ? $decoded : array();
			case 'comma':
				return array_values( array_filter( array_map( 'trim', explode( ',', (string) $raw ) ), static fn( $value ) => '' !== $value ) );
			case 'scalar':
				return array( (string) $raw );
			default:
				return array();
		}
	}

	/**
	 * Read a relation key as a list of integers.
	 *
	 * Rows keep their order; duplicates and non-numeric values are dropped.
	 *
	 * @param int    $post_id Post id.
	 * @param string $key     Meta key.
	 * @return array<int, int>
	 */
	public static function read_ids( int $post_id, string $key ): array {
		$ids = array();
		foreach ( self::to_array( get_post_meta( $post_id, $key, true ) ) as $value ) {
			if ( is_array( $value ) ) {
				$value = reset( $value );
			}
			$id = absint( $value );
			if ( $id > 0 && ! in_array( $id, $ids, true ) ) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	/**
	 * Read a repeater as rows of sub-field => value.
	 *
	 * Handles both the JetEngine shape (`{"item-0":{"add-new-feature":"x"}}`)
	 * and a plain list of rows.
	 *
	 * @param int    $post_id Post id.
	 * @param string $key     Repeater key.
	 * @return array<int, array<string, string>>
	 */
	public static function read_repeater( int $post_id, string $key ): array {
		$fields = self::repeater_fields()[ $key ] ?? array();
		$rows   = array();
		foreach ( self::to_array( get_post_meta( $post_id, $key, true ) ) as $index => $row ) {
			unset( $index );
			if ( ! is_array( $row ) ) {
				continue;
			}
			$clean = array();
			$empty = true;
			foreach ( $fields as $field ) {
				$value = $row[ $field ] ?? '';
				if ( is_array( $value ) ) {
					$value = reset( $value );
				}
				$clean[ $field ] = is_scalar( $value ) ? (string) $value : '';
				if ( '' !== trim( $clean[ $field ] ) ) {
					$empty = false;
				}
			}
			if ( ! $empty ) {
				$rows[] = $clean;
			}
		}
		return $rows;
	}

	/**
	 * Encode rows into the shape JetEngine used.
	 *
	 * @param array<int, array<string, string>> $rows Rows.
	 * @return string JSON string.
	 */
	public static function encode_repeater( array $rows ): string {
		$payload = array();
		$index   = 0;
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$clean = array();
			foreach ( $row as $field => $value ) {
				if ( is_scalar( $value ) && '' !== trim( (string) $value ) ) {
					$clean[ (string) $field ] = (string) $value;
				}
			}
			if ( array() !== $clean ) {
				$payload[ 'item-' . $index ] = $clean;
				++$index;
			}
		}
		return (string) wp_json_encode( $payload );
	}

	/**
	 * Resolve the product gallery from all supported keys.
	 *
	 * Order of preference: `galerry_produ`, WooCommerce's own
	 * `_product_image_gallery`, then the legacy `gallery-filed` and
	 * `image-2..image-6` slots. Every source is merged once, in that order.
	 *
	 * @param int $product_id Product id.
	 * @return array<int, int>
	 */
	public static function product_gallery( int $product_id ): array {
		$sources   = array( 'galerry_produ' );
		$sources[] = '_product_image_gallery';
		$sources[] = 'gallery-filed';
		for ( $slot = 2; $slot <= 6; $slot++ ) {
			$sources[] = 'image-' . $slot;
		}

		$ids = array();
		foreach ( $sources as $key ) {
			foreach ( self::to_array( get_post_meta( $product_id, $key, true ) ) as $value ) {
				if ( is_array( $value ) ) {
					$value = reset( $value );
				}
				$id = absint( $value );
				if ( $id > 0 && ! in_array( $id, $ids, true ) ) {
					$ids[] = $id;
				}
			}
		}

		return array_values( array_filter( $ids, static fn( int $id ): bool => 'attachment' === get_post_type( $id ) ) );
	}

	/**
	 * Write the product gallery to `galerry_produ` and keep WooCommerce's copy.
	 *
	 * @param int             $product_id Product id.
	 * @param array<int, int> $ids        Attachment ids.
	 * @return void
	 */
	public static function save_product_gallery( int $product_id, array $ids ): void {
		$clean = array();
		foreach ( $ids as $id ) {
			$id = absint( $id );
			if ( $id > 0 && 'attachment' === get_post_type( $id ) && ! in_array( $id, $clean, true ) ) {
				$clean[] = $id;
			}
		}
		// update_metadata() unslashes its input, so every write is slashed here.
		update_post_meta( $product_id, 'galerry_produ', wp_slash( implode( ',', $clean ) ) );
		if ( post_type_exists( 'product' ) ) {
			update_post_meta( $product_id, '_product_image_gallery', wp_slash( implode( ',', $clean ) ) );
		}
	}

	/**
	 * Project gallery: the five legacy slots in order, plus `galerry_prod`.
	 *
	 * @param int $project_id Project id.
	 * @return array<int, int>
	 */
	public static function project_gallery( int $project_id ): array {
		$ids = array();
		foreach ( array_merge( array( 'galerry_prod' ), self::project_image_slots() ) as $key ) {
			$id = absint( get_post_meta( $project_id, $key, true ) );
			if ( $id > 0 && ! in_array( $id, $ids, true ) ) {
				$ids[] = $id;
			}
		}
		return array_values( array_filter( $ids, static fn( int $id ): bool => 'attachment' === get_post_type( $id ) ) );
	}

	/**
	 * Write the project gallery back into the five historical slot keys.
	 *
	 * Extra images beyond five are dropped rather than inventing a new key, so
	 * a future migration cannot silently grow the schema.
	 *
	 * @param int             $project_id Project id.
	 * @param array<int, int> $ids        Attachment ids.
	 * @return void
	 */
	public static function save_project_gallery( int $project_id, array $ids ): void {
		$slots = self::project_image_slots();
		$clean = array();
		foreach ( $ids as $id ) {
			$id = absint( $id );
			if ( $id > 0 && 'attachment' === get_post_type( $id ) && ! in_array( $id, $clean, true ) ) {
				$clean[] = $id;
			}
		}
		foreach ( $slots as $index => $key ) {
			$id = $clean[ $index ] ?? 0;
			if ( $id > 0 ) {
				update_post_meta( $project_id, $key, wp_slash( (string) $id ) );
			} else {
				delete_post_meta( $project_id, $key );
			}
		}
	}

	/**
	 * View counter with the documented fallback chain.
	 *
	 * @param int $post_id Post id.
	 * @return int
	 */
	public static function views( int $post_id ): int {
		$primary = get_post_meta( $post_id, 'number2-viewc', true );
		if ( is_numeric( $primary ) ) {
			return absint( $primary );
		}
		// A corrupt or absent new key falls back to the legacy counter, but a
		// stored "0" is a real zero, not corruption.
		$legacy = get_post_meta( $post_id, 'number-viewc', true );
		return is_numeric( $legacy ) ? absint( $legacy ) : 0;
	}

	/**
	 * Whether the new counter key exists and holds a numeric value.
	 *
	 * @param int $post_id Post id.
	 * @return bool
	 */
	public static function has_new_counter( int $post_id ): bool {
		$value = get_post_meta( $post_id, 'number2-viewc', true );
		return metadata_exists( 'post', $post_id, 'number2-viewc' ) && is_numeric( $value );
	}

	/**
	 * Increment the view counter without touching or inventing legacy keys.
	 *
	 * The value is written straight to the new key as a string, never through
	 * the old key, so a fallback count does not get copied over the legacy row.
	 *
	 * @param int $post_id Post id.
	 * @return int New value.
	 */
	public static function increment_views( int $post_id ): int {
		$current = self::has_new_counter( $post_id ) ? absint( get_post_meta( $post_id, 'number2-viewc', true ) ) : 0;
		$legacy  = get_post_meta( $post_id, 'number-viewc', true );
		if ( ! self::has_new_counter( $post_id ) && is_numeric( $legacy ) ) {
			$current = absint( $legacy );
		}
		$next = $current + 1;
		update_post_meta( $post_id, 'number2-viewc', wp_slash( (string) $next ) );
		return $next;
	}

	/**
	 * Whether the product is purchasable online or needs a quote.
	 *
	 * `true_sale` is stored as the literal strings "true"/"false" on the live
	 * site, so a loose cast is the only safe read.
	 *
	 * @param int $product_id Product id.
	 * @return bool
	 */
	public static function is_direct_sale( int $product_id ): bool {
		$flag = get_post_meta( $product_id, 'true_sale', true );
		if ( is_bool( $flag ) ) {
			return $flag;
		}
		if ( null === $flag || '' === $flag ) {
			return true;
		}
		return in_array( strtolower( trim( (string) $flag ) ), array( 'true', '1', 'yes', 'on' ), true );
	}

	/**
	 * Gallery string for the meta box: the stored `galerry_produ` verbatim.
	 *
	 * Kept separate from product_gallery() so opening and saving the editor
	 * never rewrites a legacy comma/JSON value the site already has.
	 *
	 * @param int $product_id Product id.
	 * @return string
	 */
	public static function get_gallery_string( int $product_id ): string {
		$value = get_post_meta( $product_id, 'galerry_produ', true );
		if ( is_string( $value ) ) {
			return $value;
		}
		if ( is_array( $value ) ) {
			return implode( ',', array_map( 'strval', self::to_array( $value ) ) );
		}
		return '';
	}

	/**
	 * Read the hreflang triplet for a post.
	 *
	 * An empty language simply means "no translation", which the output layer
	 * treats as "do not print that alternate link".
	 *
	 * @param int $post_id Post id.
	 * @return array<string, string>
	 */
	public static function hreflang( int $post_id ): array {
		$links = array();
		foreach ( array( 'fa', 'en', 'ar' ) as $language ) {
			$value              = get_post_meta( $post_id, 'hrefmeta_' . $language, true );
			$links[ $language ] = is_scalar( $value ) ? esc_url_raw( (string) $value ) : '';
		}
		return $links;
	}

	/**
	 * Prefix a term meta key, matching the historical `term_` convention.
	 *
	 * Term meta on the live site is stored under the plain key on `wp_termmeta`
	 * and is read with `get_term_meta()`; no prefix rewriting is applied.
	 *
	 * @param string $key Raw key.
	 * @return string
	 */
	public static function term_key( string $key ): string {
		return $key;
	}

	/**
	 * Read one scalar key, preserving an empty string as "not set".
	 *
	 * @param int    $post_id Post id.
	 * @param string $key     Meta key.
	 * @return string
	 */
	public static function text( int $post_id, string $key ): string {
		$value = get_post_meta( $post_id, $key, true );
		return is_scalar( $value ) ? (string) $value : '';
	}
}
