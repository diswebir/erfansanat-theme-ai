<?php
/**
 * Archive settings tab: cards per page, pagination, default sort/filter.
 *
 * Applies the configured posts_per_page (and shop default sort) to the main
 * archive query via pre_get_posts. Invalid values fall back to schema defaults.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options\Tabs;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Schema;

final class ArchiveTab {

	public const ID = 'archive';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'pre_get_posts', array( __CLASS__, 'apply_to_query' ) );
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
	 * Safe posts_per_page for an option key (invalid → schema default).
	 */
	public static function posts_per_page_for( string $key ): int {
		$value = (int) es_option( $key );
		return $value >= 1 ? $value : (int) Schema::default( $key );
	}

	public static function pagination_type(): string {
		return (string) es_option( 'pagination_type' );
	}

	public static function archive_layout(): string {
		return (string) es_option( 'archive_layout' );
	}

	/**
	 * Shop default sort query args.
	 *
	 * @return array<string,mixed>
	 */
	public static function shop_sort_args(): array {
		switch ( (string) es_option( 'shop_default_sort' ) ) {
			case 'price_asc':
				return array( 'orderby' => 'meta_value_num', 'meta_key' => '_price', 'order' => 'ASC' );
			case 'price_desc':
				return array( 'orderby' => 'meta_value_num', 'meta_key' => '_price', 'order' => 'DESC' );
			case 'popular':
				return array( 'orderby' => 'comment_count', 'order' => 'DESC' );
			case 'latest':
			default:
				return array( 'orderby' => 'date', 'order' => 'DESC' );
		}
	}

	/**
	 * Apply archive settings to the main front-end query.
	 *
	 * @param \WP_Query $query Current query.
	 */
	public static function apply_to_query( $query ): void {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$is_shop    = $query->is_post_type_archive( 'product' ) || $query->is_tax( array( 'product_cat', 'product_tag' ) );
		$is_project = $query->is_post_type_archive( 'projects' ) || $query->is_tax( array( 'project_type', 'city' ) );

		if ( $is_shop ) {
			$query->set( 'posts_per_page', self::posts_per_page_for( 'shop_posts_per_page' ) );
			foreach ( self::shop_sort_args() as $key => $value ) {
				$query->set( $key, $value );
			}
			return;
		}

		if ( $is_project ) {
			$query->set( 'posts_per_page', self::posts_per_page_for( 'projects_posts_per_page' ) );
			return;
		}

		if ( $query->is_home() || $query->is_archive() || $query->is_search() ) {
			$query->set( 'posts_per_page', self::posts_per_page_for( 'blog_posts_per_page' ) );
		}
	}
}
