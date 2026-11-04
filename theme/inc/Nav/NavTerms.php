<?php
/** One shared, short-lived taxonomy cache for all header mega panels. */

namespace ErfanSanat\Theme\Nav;

defined( 'ABSPATH' ) || exit;

final class NavTerms {

	private static ?array $terms = null;

	/** @return array<int,object> */
	public static function all(): array {
		if ( null !== self::$terms ) { return self::$terms; }
		$key = 'es_header_mega_terms_v1';
		$cached = function_exists( 'get_transient' ) ? get_transient( $key ) : false;
		if ( is_array( $cached ) ) { self::$terms = $cached; return self::$terms; }
		$taxonomies = array_values( array_filter( array( 'product_cat', 'project_type', 'city' ), static function ( $taxonomy ) { return ! function_exists( 'taxonomy_exists' ) || taxonomy_exists( $taxonomy ); } ) );
		$terms = function_exists( 'get_terms' ) && $taxonomies ? get_terms( array( 'taxonomy' => $taxonomies, 'hide_empty' => false, 'number' => 0, 'orderby' => 'name', 'order' => 'ASC' ) ) : array();
		self::$terms = is_array( $terms ) && ! ( function_exists( 'is_wp_error' ) && is_wp_error( $terms ) ) ? $terms : array();
		if ( function_exists( 'set_transient' ) ) { set_transient( $key, self::$terms, defined( 'HOUR_IN_SECONDS' ) ? 12 * HOUR_IN_SECONDS : 43200 ); }
		return self::$terms;
	}

	/** @return array<int,object> */
	public static function taxonomy( string $taxonomy ): array {
		if ( ! in_array( $taxonomy, array( 'product_cat', 'project_type', 'city' ), true ) ) { return array(); }
		return array_values( array_filter( self::all(), static fn( $term ) => is_object( $term ) && isset( $term->taxonomy ) && $taxonomy === $term->taxonomy ) );
	}

	/** Test helper to clear request-local cache. */
	public static function reset(): void { self::$terms = null; }
}
