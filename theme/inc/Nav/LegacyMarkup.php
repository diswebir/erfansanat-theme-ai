<?php
/** Resolves static-mirror URLs while keeping the legacy markup intact. */

namespace ErfanSanat\Theme\Nav;

defined( 'ABSPATH' ) || exit;

/** Converts legacy `.html` links into current WordPress permalinks. */
final class LegacyMarkup {

	/** Rewrite only href attributes; classes, IDs, and data hooks remain untouched. */
	public static function rewrite_links( string $html ): string {
		$html = (string) preg_replace_callback(
			'/\s+custom=((["\']).*?\2)/i',
			static function () { return ''; },
			$html
		);
		$html = (string) preg_replace( '/\s+data-initial-qty=("[^"]*"|\'[^\']*\')/i', '', $html );
		return (string) preg_replace_callback(
			'/\bhref=("|\')([^"\']+)\1/i',
			static function ( array $match ): string {
				$url = self::resolve( html_entity_decode( $match[2], ENT_QUOTES, 'UTF-8' ) );
				$escaped = function_exists( 'esc_url' ) ? esc_url( $url ) : htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
				return 'href=' . $match[1] . $escaped . $match[1];
			},
			$html
		);
	}

	/** Map a legacy mirror URL to a live route where possible. */
	public static function resolve( string $legacy_url ): string {
		if ( '' === $legacy_url || preg_match( '~^(?:[a-z][a-z0-9+.-]*:|//|#)~i', $legacy_url ) ) {
			return $legacy_url;
		}
		$parts = parse_url( $legacy_url );
		$path = ltrim( (string) ( $parts['path'] ?? '' ), '/' );
		if ( '' === $path || 'index.html' === $path ) {
			$target = function_exists( 'home_url' ) ? home_url( '/' ) : '/';
		} else {
			$path = preg_replace( '/\.html$/i', '', $path );
			$segments = array_values( array_filter( explode( '/', $path ), 'strlen' ) );
			$family = $segments[0] ?? '';
			$slug = end( $segments );
			$target = self::resolve_content( $family, (string) $slug, $path );
		}
		if ( isset( $parts['query'] ) ) { $target .= '?' . $parts['query']; }
		if ( isset( $parts['fragment'] ) ) { $target .= '#' . $parts['fragment']; }
		return (string) $target;
	}

	private static function resolve_content( string $family, string $slug, string $path ): string {
		if ( in_array( $family, array( 'product_cat', 'product-tag', 'city', 'project_type' ), true ) && function_exists( 'get_term_by' ) ) {
			$taxonomy = 'product-tag' === $family ? 'product_tag' : $family;
			$term = get_term_by( 'slug', $slug, $taxonomy );
			if ( $term && ! ( function_exists( 'is_wp_error' ) && is_wp_error( $term ) ) && function_exists( 'get_term_link' ) ) {
				$link = get_term_link( $term, $taxonomy );
				if ( ! ( function_exists( 'is_wp_error' ) && is_wp_error( $link ) ) && is_string( $link ) ) { return $link; }
			}
		}

		if ( in_array( $family, array( 'product', 'products' ), true ) && function_exists( 'get_page_by_path' ) ) {
			$post = get_page_by_path( $slug, defined( 'OBJECT' ) ? OBJECT : 'OBJECT', 'product' );
			if ( $post && function_exists( 'get_permalink' ) ) { return (string) get_permalink( $post ); }
			return self::home_path( 'product/' . rawurlencode( $slug ) . '/' );
		}
		if ( 'projects' === $family && count( explode( '/', $path ) ) > 1 && function_exists( 'get_page_by_path' ) ) {
			$post = get_page_by_path( $slug, defined( 'OBJECT' ) ? OBJECT : 'OBJECT', 'projects' );
			if ( $post && function_exists( 'get_permalink' ) ) { return (string) get_permalink( $post ); }
			return self::home_path( 'projects/' . rawurlencode( $slug ) . '/' );
		}
		if ( 'projects' === $family && function_exists( 'get_post_type_archive_link' ) ) {
			$link = get_post_type_archive_link( 'projects' );
			if ( $link ) { return (string) $link; }
		}
		if ( in_array( $family, array( 'shop', 'products' ), true ) && 1 === count( explode( '/', $path ) ) && function_exists( 'get_post_type_archive_link' ) ) {
			$link = get_post_type_archive_link( 'product' );
			if ( $link ) { return (string) $link; }
		}
		if ( 'blog' === $family && function_exists( 'get_option' ) && function_exists( 'get_permalink' ) ) {
			$page_id = (int) get_option( 'page_for_posts' );
			if ( $page_id ) { return (string) get_permalink( $page_id ); }
			return self::home_path( '' );
		}
		$page_path = preg_replace( '/\.(?:html|php)$/i', '', $path );
		if ( function_exists( 'get_page_by_path' ) ) {
			$page = get_page_by_path( $page_path );
			if ( $page && function_exists( 'get_permalink' ) ) { return (string) get_permalink( $page ); }
		}
		return self::home_path( rtrim( $page_path, '/' ) . '/' );
	}

	private static function home_path( string $path ): string {
		if ( function_exists( 'home_url' ) ) { return (string) home_url( '/' . ltrim( $path, '/' ) ); }
		return '/' . ltrim( $path, '/' );
	}
}
