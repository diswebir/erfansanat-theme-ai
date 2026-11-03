<?php
/**
 * Mega menu tab: products/projects tab configuration and query sources.
 *
 * Term sources are slug-agnostic (queried by taxonomy, never by hardcoded
 * English slugs) so live Persian term slugs such as /city/اصفهان/ work.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options\Tabs;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Schema;

final class MegaMenuTab {

	public const ID = 'megamenu';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;
	}

	public static function id(): string {
		return self::ID;
	}

	/** @return array<int,string> */
	public static function fields(): array {
		return array_keys(
			array_filter(
				Schema::fields(),
				static function ( $field ) {
					return ( $field['tab'] ?? '' ) === self::ID;
				}
			)
		);
	}

	/** @return array<string,mixed> */
	public static function defaults(): array {
		$defaults = array();
		foreach ( self::fields() as $key ) {
			$defaults[ $key ] = Schema::default( $key );
		}
		return $defaults;
	}

	/**
	 * Products mega menu configuration.
	 *
	 * @return array{enabled:bool,per_tab:int,taxonomy:string}
	 */
	public static function products_config(): array {
		return array(
			'enabled'  => (bool) es_option( 'mega_products_enabled' ),
			'per_tab'  => (int) es_option( 'mega_products_per_tab' ),
			'taxonomy' => 'product_cat',
		);
	}

	/**
	 * Projects mega menu configuration (group by type or city).
	 *
	 * @return array{enabled:bool,group:string,per_tab:int}
	 */
	public static function projects_config(): array {
		return array(
			'enabled' => (bool) es_option( 'mega_projects_enabled' ),
			'group'   => (string) es_option( 'mega_projects_group' ),
			'per_tab' => (int) es_option( 'mega_projects_per_tab' ),
		);
	}

	/**
	 * Slug-agnostic term source. Returns up to $limit terms of a taxonomy.
	 *
	 * @return array<int,\WP_Term>
	 */
	public static function get_terms_for( string $taxonomy, int $limit ): array {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return array();
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'number'     => max( 1, $limit ),
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		return is_wp_error( $terms ) ? array() : $terms;
	}

	/**
	 * Term links for the mega menu (Persian slugs resolve correctly).
	 *
	 * @return array<int,array{term_id:int,name:string,slug:string,url:string}>
	 */
	public static function term_links( string $taxonomy, int $limit ): array {
		$out = array();
		foreach ( self::get_terms_for( $taxonomy, $limit ) as $term ) {
			$url   = get_term_link( $term );
			$out[] = array(
				'term_id' => (int) $term->term_id,
				'name'    => $term->name,
				'slug'    => $term->slug,
				'url'     => is_wp_error( $url ) ? '' : $url,
			);
		}
		return $out;
	}

	/**
	 * Menu items with a safe fallback when the taxonomy is empty (AC-03).
	 *
	 * @return array<int,array{term_id:int,name:string,slug:string,url:string}>
	 */
	public static function menu_items( string $taxonomy, int $limit ): array {
		$items = self::term_links( $taxonomy, $limit );
		if ( ! empty( $items ) ) {
			return $items;
		}

		return array(
			array(
				'term_id' => 0,
				'name'    => __( 'همه موارد', 'erfansanat' ),
				'slug'    => '',
				'url'     => home_url( '/' ),
			),
		);
	}
}
