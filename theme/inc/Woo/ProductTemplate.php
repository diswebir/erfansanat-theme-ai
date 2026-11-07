<?php
/** Safe accessors shared by the classic WooCommerce templates. */

namespace ErfanSanat\Theme\Woo;

use ErfanSanat\Theme\Meta\RepeaterCodec;

defined( 'ABSPATH' ) || exit;

/** Normalizes legacy meta values without changing their persisted key names. */
final class ProductTemplate {

	/** Resolve the current product from a supplied value, the global, or WooCommerce. */
	public static function product( $candidate = null ) {
		if ( is_object( $candidate ) && method_exists( $candidate, 'get_id' ) ) {
			return $candidate;
		}
		global $product;
		if ( is_object( $product ) && method_exists( $product, 'get_id' ) ) {
			return $product;
		}
		if ( function_exists( 'wc_get_product' ) && function_exists( 'get_the_ID' ) ) {
			$resolved = wc_get_product( get_the_ID() );
			return is_object( $resolved ) ? $resolved : null;
		}
		return null;
	}

	/** Return a positive current product ID, or zero when no product is available. */
	public static function product_id( $candidate = null ): int {
		$product = self::product( $candidate );
		return $product && method_exists( $product, 'get_id' ) ? absint( $product->get_id() ) : 0;
	}

	/** Read decoded theme metadata with WordPress as a safe fallback. */
	public static function meta( int $post_id, string $key, $fallback = '' ) {
		if ( $post_id < 1 || '' === $key ) {
			return $fallback;
		}
		if ( function_exists( 'es_meta' ) ) {
			$value = es_meta( $post_id, $key, $fallback );
		} elseif ( function_exists( 'get_post_meta' ) ) {
			$value = get_post_meta( $post_id, $key, true );
			if ( is_string( $value ) && class_exists( RepeaterCodec::class ) ) {
				$value = RepeaterCodec::decode( $value );
			}
		} else {
			$value = $fallback;
		}
		return null === $value || '' === $value ? $fallback : $value;
	}

	/** Return only well-formed keyed rows from a legacy JetEngine repeater. */
	public static function rows( int $post_id, string $key ): array {
		$value = self::meta( $post_id, $key, array() );
		if ( ! is_array( $value ) && is_string( $value ) && class_exists( RepeaterCodec::class ) ) {
			$value = RepeaterCodec::decode( $value );
		}
		if ( ! is_array( $value ) ) {
			return array();
		}
		$rows = array();
		foreach ( $value as $row_key => $row ) {
			if ( is_array( $row ) ) {
				$rows[ (string) $row_key ] = $row;
			}
		}
		return $rows;
	}

	/** Return a trimmed scalar legacy value; arrays and objects are never cast. */
	public static function text( int $post_id, string $key, string $fallback = '' ): string {
		$value = self::meta( $post_id, $key, $fallback );
		return is_scalar( $value ) ? trim( (string) $value ) : $fallback;
	}

	/** Validate an external or local media URL before template output. */
	public static function url( $value ): string {
		if ( ! is_scalar( $value ) ) {
			return '';
		}
		$url = function_exists( 'esc_url' ) ? esc_url( (string) $value ) : filter_var( (string) $value, FILTER_SANITIZE_URL );
		return is_string( $url ) ? $url : '';
	}

	/** Return a post permalink only for an extant published object. */
	public static function permalink( int $post_id ): string {
		if ( $post_id < 1 || ! function_exists( 'get_post_status' ) || 'publish' !== get_post_status( $post_id ) || ! function_exists( 'get_permalink' ) ) {
			return '';
		}
		$url = get_permalink( $post_id );
		return is_string( $url ) ? self::url( $url ) : '';
	}
}
