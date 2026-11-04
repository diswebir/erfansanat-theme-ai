<?php
/** Cached data access for homepage cards and product-category shelves. */

namespace ErfanSanat\Theme\Query;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Keeps homepage database work bounded and invalidates on relevant content changes. */
final class HomeQueries implements ModuleInterface {


	public function register(): void {
		add_action( 'save_post', array( self::class, 'on_save_post' ), 20, 3 );
		add_action( 'deleted_post', array( self::class, 'invalidate' ) );
		add_action( 'edited_term', array( self::class, 'invalidate' ) );
		add_action( 'set_object_terms', array( self::class, 'invalidate' ) );
	}

	/** Return the featured product categories used by home shelves. */
	public static function product_categories( int $limit = 7 ): array {
		$limit = max( 1, min( 12, $limit ) );
		return self::remember( 'categories_' . $limit, static function () use ( $limit ) {
			if ( ! function_exists( 'get_terms' ) ) { return array(); }
			$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => $limit, 'orderby' => 'count', 'order' => 'DESC' ) );
			return is_array( $terms ) ? $terms : array();
		} );
	}

	/** Recent published projects, with post meta priming disabled for lean cards. */
	public static function projects( int $limit = 6 ): array {
		return self::posts( 'projects', $limit );
	}

	/** Recent published projects for one of the gallery's stable project-type tabs. */
	public static function projects_by_type( string $slug, int $limit = 6 ): array {
		if ( ! in_array( $slug, array( 'tunnel', 'urban' ), true ) ) { return array(); }
		$limit = max( 1, min( 12, $limit ) );
		$key = 'project_type_' . $slug . '_' . $limit;
		return self::remember( $key, static function () use ( $slug, $limit ) {
			if ( ! class_exists( \WP_Query::class ) ) { return array(); }
			$query = new \WP_Query( array(
				'post_type' => 'projects', 'post_status' => 'publish', 'posts_per_page' => $limit,
				'no_found_rows' => true, 'update_post_meta_cache' => false, 'update_post_term_cache' => false,
				'ignore_sticky_posts' => true,
				'tax_query' => array( array( 'taxonomy' => 'project_type', 'field' => 'slug', 'terms' => $slug ) ),
			) );
			return is_array( $query->posts ?? null ) ? $query->posts : array();
		} );
	}

	/** Recent published blog posts. */
	public static function articles( int $limit = 4 ): array {
		return self::posts( 'post', $limit );
	}

	/** Recent published products. */
	public static function products( int $limit = 4 ): array {
		return self::posts( 'product', $limit );
	}

	/** Invalidate only content caches affected by the saved post. */
	public static function on_save_post( $post_id, $post = null, $update = false ): void {
		$type = is_object( $post ) ? (string) ( $post->post_type ?? '' ) : ( function_exists( 'get_post_type' ) ? (string) get_post_type( (int) $post_id ) : '' );
		if ( in_array( $type, array( 'post', 'product', 'projects' ), true ) ) { self::invalidate(); }
	}

	/** Clear the known, bounded homepage cache keys. */
	public static function invalidate(): void {
		if ( ! function_exists( 'delete_transient' ) ) { return; }
		foreach ( self::cache_keys() as $key ) { delete_transient( self::cache_key( $key ) ); }
	}

	/** @return array<int,mixed> */
	private static function posts( string $type, int $limit ): array {
		$limit = max( 1, min( 12, $limit ) );
		$key = ( 'projects' === $type ? 'projects_' : ( 'post' === $type ? 'posts_' : 'products_' ) ) . $limit;
		return self::remember( $key, static function () use ( $type, $limit ) {
			if ( ! class_exists( \WP_Query::class ) ) { return array(); }
			$query = new \WP_Query( array(
				'post_type' => $type,
				'post_status' => 'publish',
				'posts_per_page' => $limit,
				'no_found_rows' => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts' => true,
			) );
			return is_array( $query->posts ?? null ) ? $query->posts : array();
		} );
	}

	/** Cache a bounded data set unless explicitly disabled for an admin smoke check. */
	private static function remember( string $key, callable $loader ): array {
		if ( self::cache_disabled() || ! function_exists( 'get_transient' ) || ! function_exists( 'set_transient' ) ) {
			$data = $loader();
			return is_array( $data ) ? $data : array();
		}
		$cache_key = self::cache_key( $key );
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) { return $cached; }
		$data = $loader();
		$data = is_array( $data ) ? $data : array();
		set_transient( $cache_key, $data, defined( 'HOUR_IN_SECONDS' ) ? HOUR_IN_SECONDS : 3600 );
		return $data;
	}

	/** Enumerate every supported bounded query key so all variants invalidate together. */
	private static function cache_keys(): array {
		$keys = array();
		foreach ( array( 'categories_', 'projects_', 'posts_', 'products_' ) as $prefix ) {
			for ( $limit = 1; $limit <= 12; ++$limit ) { $keys[] = $prefix . $limit; }
		}
		foreach ( array( 'tunnel', 'urban' ) as $slug ) {
			for ( $limit = 1; $limit <= 12; ++$limit ) { $keys[] = 'project_type_' . $slug . '_' . $limit; }
		}
		return $keys;
	}

	private static function cache_key( string $key ): string {
		$version = defined( 'ES_THEME_VERSION' ) ? preg_replace( '/[^A-Za-z0-9_-]/', '_', (string) ES_THEME_VERSION ) : '1';
		return 'es_home_v' . $version . '_' . $key;
	}

	private static function cache_disabled(): bool {
		return ( defined( 'ES_DISABLE_HOME_CACHE' ) && ES_DISABLE_HOME_CACHE )
			|| ( isset( $_GET['es_no_cache'] ) && function_exists( 'current_user_can' ) && current_user_can( 'manage_options' ) );
	}
}
