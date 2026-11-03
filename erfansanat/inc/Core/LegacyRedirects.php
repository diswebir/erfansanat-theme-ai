<?php
/**
 * 301 redirects from legacy mirror aliases to the canonical live structure.
 *
 * Alias groups (see temp/STATIC_TEMPLATE_MAP.md):
 *   products/<slug>            → product/<slug>
 *   products/category-<slug>   → product_cat/<canonical>
 *   products(.html)            → shop
 *
 * Maps are filterable so the live Persian term slugs (decision D3) can be
 * configured at deploy time without code changes. Query strings are preserved
 * and canonical URLs are never redirected (no loops).
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class LegacyRedirects {

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'template_redirect', array( __CLASS__, 'handle' ), 1 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Legacy product-category alias → canonical product_cat slug.
	 *
	 * @return array<string,string>
	 */
	public static function category_map(): array {
		$default = array(
			'category-led-string-lights'    => 'strings-and-lighting-decorations',
			'category-led-wall-washer'      => 'wall-washer',
			'category-point-light-pixel'    => 'point-light',
			'category-smart-light-trees'    => 'light-tree',
			'category-urban-chandeliers'    => 'light-chandelier',
			'category-urban-light-elements' => 'urban-lighting-elements',
			'category-urban-sunbursts'      => 'sun-light-elements',
		);

		/**
		 * Configure the legacy category alias map from live data (AC-04).
		 *
		 * @param array<string,string> $default alias => canonical slug.
		 */
		return apply_filters( 'es_legacy_redirects_product_categories', $default );
	}

	/**
	 * Resolve a request path to a canonical redirect target, or null.
	 */
	public static function resolve( string $path ): ?string {
		$query = '';
		$qpos  = strpos( $path, '?' );
		if ( false !== $qpos ) {
			$query = substr( $path, $qpos );
			$path  = substr( $path, 0, $qpos );
		}

		$path = trim( $path, '/' );
		if ( str_ends_with( $path, '.html' ) ) {
			$path = substr( $path, 0, -5 );
		}
		if ( '' === $path ) {
			return null;
		}

		$segments = explode( '/', $path );

		if ( 'products' === $segments[0] ) {
			return self::with_query( self::resolve_products( $segments ), $query );
		}

		return null;
	}

	/**
	 * Resolve the products/* alias group (P03-T009).
	 *
	 * @param array<int,string> $segments Path segments.
	 */
	private static function resolve_products( array $segments ): ?string {
		if ( count( $segments ) === 1 ) {
			return self::shop_url(); // products → shop.
		}

		$slug = $segments[1];

		if ( str_starts_with( $slug, 'category-' ) ) {
			$map         = self::category_map();
			$target_slug = $map[ $slug ] ?? substr( $slug, strlen( 'category-' ) );
			return home_url( '/product_cat/' . $target_slug . '/' );
		}

		return home_url( '/product/' . $slug . '/' );
	}

	/**
	 * Canonical shop URL (WooCommerce shop page, or /shop/ fallback).
	 */
	private static function shop_url(): string {
		if ( function_exists( 'wc_get_page_permalink' ) ) {
			$url = wc_get_page_permalink( 'shop' );
			if ( $url ) {
				return $url;
			}
		}

		$page = get_page_by_path( 'shop' );
		if ( $page ) {
			return (string) get_permalink( $page );
		}

		return home_url( '/shop/' );
	}

	private static function with_query( ?string $url, string $query ): ?string {
		if ( null === $url || '' === $query ) {
			return $url;
		}
		$separator = ( false === strpos( $url, '?' ) ) ? '?' : '&';
		return $url . $separator . ltrim( $query, '?' );
	}

	/**
	 * Front-end handler: 301 to the canonical target when an alias is matched.
	 */
	public static function handle(): void {
		if ( is_admin() || wp_doing_ajax() ) {
			return;
		}

		$uri   = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
		$path  = (string) ( parse_url( $uri, PHP_URL_PATH ) ?: '' );
		$query = isset( $_SERVER['QUERY_STRING'] ) && '' !== $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';

		$target = self::resolve( $path . $query );
		if ( null === $target ) {
			return;
		}

		// No-loop guard: never redirect a URL to itself (ignoring the query).
		$target_path = (string) ( parse_url( $target, PHP_URL_PATH ) ?: '' );
		if ( untrailingslashit( $target_path ) === untrailingslashit( $path ) ) {
			return;
		}

		wp_redirect( $target, 301 );
		exit;
	}
}
