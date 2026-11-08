<?php
/**
 * Home page queries.
 *
 * Every list on the home page is real data: products come from WooCommerce,
 * projects and services from their own post types, and posts from WordPress.
 * The rules that keep those queries honest live here rather than in the
 * templates, so one place decides ordering, limits and the empty fallback.
 *
 * N+1 policy: each query primes its own cache in a single `update_meta_cache()`
 * call, and terms are primed with `\update_termmeta_cache()` per taxonomy. A
 * template then calls only `\get_post_meta()` / `get_term_meta()` reads that are
 * already in memory.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Cached, sanitised home page queries.
 */
final class Queries {

	/**
	 * Featured products for the home grid.
	 *
	 * WooCommerce products are queried through `wc_get_products()` so price,
	 * stock and purchasability come from the real objects rather than from
	 * `postmeta` lookups. Only published, purchasable products are returned.
	 *
	 * @param int $limit How many products to return.
	 * @return array<int, \WC_Product>
	 */
	public static function products( int $limit = 4 ): array {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return array();
		}
		$limit = max( 1, min( 12, $limit ) );

		$products = wc_get_products(
			array(
				'status'       => 'publish',
				'limit'        => $limit,
				'orderby'      => 'date',
				'order'        => 'DESC',
				'stock_status' => 'instock',
				'return'       => 'objects',
			)
		);

		if ( ! is_array( $products ) ) {
			return array();
		}

		// Keep only products visitors can actually buy right now.
		$products = array_values(
			array_filter(
				$products,
				static function ( $product ): bool {
					return $product instanceof \WC_Product && $product->is_visible();
				}
			)
		);

		if ( array() !== $products ) {
			\update_meta_cache( 'post', array_map( static fn( $p ) => $p->get_id(), $products ) );
		}

		return $products;
	}

	/**
	 * Projects, newest first, with city and type terms primed.
	 *
	 * @param int    $limit How many projects.
	 * @param string $type  Optional `project_type` slug filter.
	 * @param string $city  Optional `city` slug filter.
	 * @return array<int, \WP_Post>
	 */
	public static function projects( int $limit = 10, string $type = '', string $city = '' ): array {
		$limit     = max( 1, min( 30, $limit ) );
		$args      = array(
			'post_type'           => 'projects',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		);
		$tax_query = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Required to filter by real terms.

		if ( '' !== $type ) {
			$tax_query[] = array(
				'taxonomy' => 'project_type',
				'field'    => 'slug',
				'terms'    => \sanitize_title( $type ),
			);
		}
		if ( '' !== $city ) {
			$tax_query[] = array(
				'taxonomy' => 'city',
				'field'    => 'slug',
				'terms'    => \sanitize_title( $city ),
			);
		}
		if ( array() !== $tax_query ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		$posts = \get_posts( $args );
		if ( ! is_array( $posts ) || array() === $posts ) {
			return array();
		}

		self::prime( $posts, array( 'city', 'project_type' ) );
		return $posts;
	}

	/**
	 * Services, in the menu order an editor set.
	 *
	 * @param int $limit How many services.
	 * @return array<int, \WP_Post>
	 */
	public static function services( int $limit = 8 ): array {
		$posts = \get_posts(
			array(
				'post_type'        => 'services',
				'post_status'      => 'publish',
				'posts_per_page'   => max( 1, min( 20, $limit ) ),
				'orderby'          => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'no_found_rows'    => true,
				'suppress_filters' => false,
			)
		);

		if ( ! is_array( $posts ) || array() === $posts ) {
			return array();
		}

		self::prime( $posts );
		return $posts;
	}

	/**
	 * Latest posts with category terms primed.
	 *
	 * @param int $limit How many posts.
	 * @return array<int, \WP_Post>
	 */
	public static function posts( int $limit = 4 ): array {
		$posts = \get_posts(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => max( 1, min( 12, $limit ) ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);

		if ( ! is_array( $posts ) || array() === $posts ) {
			return array();
		}

		// Prime the author and term caches so cards never query per post.
		\update_meta_cache( 'post', \wp_list_pluck( $posts, 'ID' ) );
		\update_post_thumbnail_cache(
			new \WP_Query(
				array(
					'post__in'       => \wp_list_pluck( $posts, 'ID' ),
					'post_type'      => 'any',
					'posts_per_page' => count( $posts ),
					'no_found_rows'  => true,
				)
			)
		);
		\update_post_caches( $posts, 'post', true, true );

		return $posts;
	}

	/**
	 * Projects grouped for the tabbed gallery.
	 *
	 * @param int $limit Per-group limit.
	 * @return array<string, array<int, \WP_Post>>
	 */
	public static function project_groups( int $limit = 5 ): array {
		$type_slugs = array();
		foreach ( (array) \get_terms(
			array(
				'taxonomy'   => 'project_type',
				'hide_empty' => true,
				'number'     => 3,
			)
		) as $term ) {
			if ( $term instanceof \WP_Term ) {
				$type_slugs[ (string) $term->slug ] = (string) $term->name;
			}
		}

		$groups = array( 'all' => self::projects( $limit ) );
		foreach ( $type_slugs as $slug => $name ) {
			$groups[ $slug ] = self::projects( $limit, $slug );
		}

		return $groups;
	}

	/**
	 * Prime the caches one list of posts needs.
	 *
	 * @param array<int, \WP_Post> $posts     Posts.
	 * @param array<int, string>   $taxonomies Taxonomies to touch.
	 * @return void
	 */
	private static function prime( array $posts, array $taxonomies = array() ): void {
		$ids = array();
		foreach ( $posts as $post ) {
			if ( $post instanceof \WP_Post ) {
				$ids[] = (int) $post->ID;
			}
		}
		if ( array() === $ids ) {
			return;
		}
		\update_meta_cache( 'post', $ids );

		// `get_the_post_thumbnail_url()` and `get_the_terms()`, which the cards
		// call per post, read from the thumbnail and term caches. Without these
		// two primings the first render costs two queries per card.
		\update_post_thumbnail_cache(
			new \WP_Query(
				array(
					'post__in'       => $ids,
					'post_type'      => 'any',
					'posts_per_page' => count( $ids ),
					'no_found_rows'  => true,
				)
			)
		);
		\update_post_caches( $posts, 'post', true, true );
		if ( array() !== $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms = \get_terms(
					array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => false,
						'number'     => 50,
					)
				);
				if ( is_array( $terms ) && array() !== $terms ) {
					\update_termmeta_cache( \wp_list_pluck( $terms, 'term_id' ) );
				}
			}
		}
	}

	/**
	 * Number of SQL queries a callback executes.
	 *
	 * Used by the tests to prove a list does not grow its query count with the
	 * number of cards (N+1 regression guard).
	 *
	 * @param callable $callback Work to measure.
	 * @return array{queries: int, result: mixed}
	 */
	public static function measure( callable $callback ): array {
		global $wpdb;
		$before = (int) $wpdb->num_queries;
		$result = $callback();
		return array(
			'queries' => (int) $wpdb->num_queries - $before,
			'result'  => $result,
		);
	}
}
