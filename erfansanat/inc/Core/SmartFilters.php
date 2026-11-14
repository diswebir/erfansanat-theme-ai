<?php
/**
 * Smart product filters (P08-T005 QueryBuilder; UI/endpoint join in T006+).
 *
 * Builds WP_Query args ONLY — never raw SQL (AC-03). Taxonomy keys are
 * whitelisted to product_cat + the 18 mapped pa_* attributes (AC-01); price
 * uses a numeric meta_query on `_price`; purchase mode maps to the legacy
 * `true_sale` meta; sorting/pagination are clamped whitelists (AC-02).
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class SmartFilters {

	public const MAX_PER_PAGE = 48;

	private static bool $booted = false;

	/**
	 * Register the public filter endpoint on the shared Ajax contract (P08-T006)
	 * and the content-change invalidation hooks (P08-T011 AC-02).
	 */
	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		if ( did_action( 'init' ) ) {
			self::register_endpoint();
		} else {
			add_action( 'init', array( __CLASS__, 'register_endpoint' ), 20 );
		}

		// P08-T011: any content/term change invalidates cached results.
		foreach ( array( 'save_post', 'deleted_post', 'set_object_terms', 'created_term', 'edited_term', 'deleted_term', 'edited_terms' ) as $es_hook ) {
			add_action( $es_hook, array( __CLASS__, 'bust_cache' ), 10, 0 );
		}
	}

	private const CACHE_GROUP = 'es_filters';

	private static ?int $cache_version = null;

	private static array $memory_cache = array();

	/** Monotonic cache version, persisted so invalidation survives requests. */
	public static function cache_version(): int {
		if ( null === self::$cache_version ) {
			self::$cache_version = (int) get_option( 'es_filters_cache_version', 1 );
		}
		return self::$cache_version;
	}

	/** Invalidate every cached filter/search result (content changed). */
	public static function bust_cache(): void {
		self::$cache_version = self::cache_version() + 1;
		update_option( 'es_filters_cache_version', self::$cache_version, false );
		if ( function_exists( 'wp_cache_flush_group' ) ) {
			wp_cache_flush_group( self::CACHE_GROUP );
		}
		self::$memory_cache = array();
	}

	/** Fetch a cached result set (version-keyed; stale entries never read). */
	public static function cache_get( string $key ) {
		$versioned = self::cache_version() . ':' . $key;
		if ( array_key_exists( $versioned, self::$memory_cache ) ) {
			return self::$memory_cache[ $versioned ];
		}
		$hit = wp_cache_get( $versioned, self::CACHE_GROUP );
		if ( false !== $hit ) {
			self::$memory_cache[ $versioned ] = $hit;
		}
		return $hit;
	}

	/** Store a result set for the current cache version. */
	public static function cache_set( string $key, $value ): void {
		$versioned = self::cache_version() . ':' . $key;
		self::$memory_cache[ $versioned ] = $value;
		wp_cache_set( $versioned, $value, self::CACHE_GROUP );
	}

	/**
	 * Endpoint inputs cover the QueryBuilder surface; UI controls land
	 * progressively (categories T006, price/mode T007, pa_* T008).
	 */
	public static function register_endpoint(): void {
		$inputs = array(
			'term'      => array( 'type' => 'text', 'default' => '' ),
			'cats'      => array( 'type' => 'int_list', 'default' => array(), 'max' => 50 ),
			'price_min' => array( 'type' => 'float', 'default' => 0 ),
			'price_max' => array( 'type' => 'float', 'default' => 0 ),
			'sale_mode' => array( 'type' => 'enum', 'default' => 'all', 'choices' => array( 'all', 'online', 'inquiry' ) ),
			'orderby'   => array( 'type' => 'enum', 'default' => 'latest', 'choices' => array_keys( self::ORDERBY ) ),
			'page'      => array( 'type' => 'int', 'default' => 1, 'min' => 1, 'max' => 1000 ),
			'per_page'  => array( 'type' => 'int', 'default' => 12, 'min' => 1, 'max' => self::MAX_PER_PAGE ),
		);
		foreach ( AttributeMap::all() as $tax => $def ) {
			$inputs[ $tax ] = array( 'type' => 'key_list', 'default' => array(), 'max' => 50 );
		}
		Ajax::register( 'product_filters', array( __CLASS__, 'ajax_product_filters' ), array(
			'public' => true,
			'inputs' => $inputs,
		) );

		// P08-T010: project_type/city filters with per-term counters.
		Ajax::register( 'project_filters', array( __CLASS__, 'ajax_project_filters' ), array(
			'public' => true,
			'inputs' => array(
				'term'     => array( 'type' => 'text', 'default' => '' ),
				'types'    => array( 'type' => 'int_list', 'default' => array(), 'max' => 50 ),
				'cities'   => array( 'type' => 'int_list', 'default' => array(), 'max' => 50 ),
				'orderby'  => array( 'type' => 'enum', 'default' => 'newest', 'choices' => array_keys( self::PROJECT_ORDERBY ) ),
				'page'     => array( 'type' => 'int', 'default' => 1, 'min' => 1, 'max' => 1000 ),
				'per_page' => array( 'type' => 'int', 'default' => 12, 'min' => 1, 'max' => self::MAX_PER_PAGE ),
			),
		) );
	}

	/**
	 * Endpoint callback: filter state → query → server-rendered reference cards.
	 *
	 * @param array<string,mixed> $input Whitelisted input.
	 * @return array<string,mixed>
	 */
	public static function ajax_product_filters( array $input ): array {
		$tax = array();
		if ( ! empty( $input['cats'] ) ) {
			$tax['product_cat'] = $input['cats'];
		}
		foreach ( AttributeMap::all() as $tax_key => $def ) {
			if ( ! empty( $input[ $tax_key ] ) ) {
				$tax[ $tax_key ] = $input[ $tax_key ];
			}
		}
		$result = self::query_products( array(
			'term'      => $input['term'],
			'tax'       => $tax,
			'price_min' => $input['price_min'],
			'price_max' => $input['price_max'],
			'sale_mode' => $input['sale_mode'],
			'orderby'   => $input['orderby'],
			'page'      => $input['page'],
			'per_page'  => $input['per_page'],
		) );

		// AC-03: prime posts + meta in ONE query — no per-card N+1.
		if ( $result['ids'] && function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( $result['ids'], true, true );
		}

		// Render the exact reference card (woocommerce/content-product.php).
		$template = locate_template( 'woocommerce/content-product.php' );
		$html     = '';
		if ( $template && $result['ids'] ) {
			ob_start();
			foreach ( $result['ids'] as $id ) {
				$GLOBALS['post'] = get_post( $id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
				setup_postdata( $GLOBALS['post'] );
				include $template; // include (not require_once): cards repeat per request.
			}
			wp_reset_postdata();
			$html = (string) ob_get_clean();
		}

		return array(
			'html'  => $html,
			'total' => $result['total'],
			'pages' => $result['pages'],
			'page'  => $result['page'],
		);
	}

	/** Sorting whitelist: request key → WP_Query orderby. */
	/** Sorting whitelist for project archives (P08-T010). */
	public const PROJECT_ORDERBY = array(
		'newest'  => 'date',
		'popular' => 'comment_count',
		'name'    => 'title',
	);

	public const ORDERBY = array(
		'latest'    => 'date',
		'oldest'    => 'date_old',
		'price_asc' => 'price_asc',
		'price_desc' => 'price_desc',
		'name'      => 'title',
		'popular'   => 'comment_count',
	);

	/**
	 * Allowed filter taxonomies: product_cat + mapped pa_* attributes that
	 * actually exist (AC-01). Values are Persian labels for the UI.
	 *
	 * @return array<string,string>
	 */
	public static function allowed_taxonomies(): array {
		$out = array();
		if ( taxonomy_exists( 'product_cat' ) ) {
			$out['product_cat'] = __( 'دسته‌بندی محصولات', 'erfansanat' );
		}
		foreach ( AttributeMap::all() as $tax => $def ) {
			if ( taxonomy_exists( $tax ) ) {
				$out[ $tax ] = (string) $def['label'];
			}
		}
		return $out;
	}

	/**
	 * Build safe WP_Query args from raw filter state (already is_array'd).
	 * Expected keys: term(string), tax(map tax→list of term ids/slugs),
	 * price_min/price_max(float), sale_mode(all|online|inquiry),
	 * orderby(key of ORDERBY), page/per_page(int).
	 *
	 * @param array<string,mixed> $state Filter state.
	 * @return array<string,mixed> WP_Query args.
	 */
	public static function build_product_query( array $state ): array {
		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, min( self::MAX_PER_PAGE, (int) ( $state['per_page'] ?? 12 ) ) ),
			'paged'          => max( 1, (int) ( $state['page'] ?? 1 ) ),
			'ignore_sticky_posts' => true,
		);

		$term = trim( (string) ( $state['term'] ?? '' ) );
		if ( '' !== $term ) {
			$args['s'] = sanitize_text_field( $term ); // AC-03: core search, no SQL.
		}

		// Taxonomy clauses — whitelist keys, terms as ids or slugs only.
		$allowed   = self::allowed_taxonomies();
		$tax_query = array();
		foreach ( (array) ( $state['tax'] ?? array() ) as $tax => $terms ) {
			$tax = (string) $tax;
			if ( ! isset( $allowed[ $tax ] ) ) {
				continue; // SC-02: unknown taxonomy dropped.
			}
			$clean = array();
			foreach ( (array) $terms as $t ) {
				if ( is_int( $t ) || ( is_string( $t ) && ctype_digit( $t ) ) ) {
					$id = (int) $t;
					if ( $id > 0 ) { $clean[] = $id; }
				} elseif ( is_string( $t ) ) {
					$slug = sanitize_title( $t );
					if ( '' !== $slug ) { $clean[] = $slug; }
				}
			}
			$clean = array_slice( array_values( array_unique( $clean ) ), 0, 50 );
			if ( ! $clean ) {
				continue;
			}
			$field       = is_int( $clean[0] ) ? 'term_id' : 'slug';
			$tax_query[] = array(
				'taxonomy' => $tax,
				'field'    => $field,
				'terms'    => $clean,
				'operator' => 'IN',
			);
		}

		// Respect Woo visibility when the taxonomy exists (hidden products out).
		if ( taxonomy_exists( 'product_visibility' ) ) {
			$exclude = array();
			foreach ( array( 'exclude-from-catalog', 'exclude-from-search' ) as $slug ) {
				$t = get_term_by( 'slug', $slug, 'product_visibility' );
				if ( $t instanceof \WP_Term ) { $exclude[] = (int) $t->term_id; }
			}
			if ( $exclude ) {
				$tax_query[] = array( 'taxonomy' => 'product_visibility', 'field' => 'term_id', 'terms' => $exclude, 'operator' => 'NOT IN' );
			}
		}
		if ( $tax_query ) {
			$args['tax_query'] = array_merge( array( 'relation' => 'AND' ), $tax_query );
		}

		// Price range + purchase mode → numeric/char meta clauses (P08-T007).
		$meta_query = array();
		$min = self::sanitize_price( $state['price_min'] ?? null );
		$max = self::sanitize_price( $state['price_max'] ?? null );
		if ( null !== $min && null !== $max && $min > $max ) {
			$tmp = $min; $min = $max; $max = $tmp;
		}
		if ( null !== $min && $min > 0 && null !== $max && $max > 0 ) {
			$meta_query[] = array( 'key' => '_price', 'value' => array( $min, $max ), 'type' => 'NUMERIC', 'compare' => 'BETWEEN' );
		} elseif ( null !== $min && $min > 0 ) {
			$meta_query[] = array( 'key' => '_price', 'value' => $min, 'type' => 'NUMERIC', 'compare' => '>=' );
		} elseif ( null !== $max && $max > 0 ) {
			$meta_query[] = array( 'key' => '_price', 'value' => $max, 'type' => 'NUMERIC', 'compare' => '<=' );
		}
		$mode = self::sanitize_sale_mode( $state['sale_mode'] ?? 'all' );
		if ( 'online' === $mode ) {
			$meta_query[] = array( 'key' => 'true_sale', 'value' => 'true', 'compare' => '=' );
		} elseif ( 'inquiry' === $mode ) {
			$meta_query[] = array( 'key' => 'true_sale', 'value' => 'false', 'compare' => '=' );
		}
		if ( $meta_query ) {
			$args['meta_query'] = array_merge( array( 'relation' => 'AND' ), $meta_query );
		}

		// Sorting whitelist (AC-01/AC-02).
		$orderby = (string) ( $state['orderby'] ?? 'latest' );
		if ( ! isset( self::ORDERBY[ $orderby ] ) ) {
			$orderby = 'latest';
		}
		switch ( $orderby ) {
			case 'price_asc':
			case 'price_desc':
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'price_asc' === $orderby ? 'ASC' : 'DESC';
				break;
			case 'oldest':
				$args['orderby'] = 'date';
				$args['order']   = 'ASC';
				break;
			case 'name':
				$args['orderby'] = 'title';
				$args['order']   = 'ASC';
				break;
			case 'popular':
				$args['orderby'] = 'comment_count';
				$args['order']   = 'DESC';
				break;
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
		}

		return $args;
	}

	/**
	 * Project archive taxonomies (P08-T010): type + city only.
	 *
	 * @return array<int,string>
	 */
	public static function allowed_project_taxonomies(): array {
		return array( 'project_type', 'city' );
	}

	/**
	 * Whitelisted WP_Query args for project archives. tax map: taxonomy →
	 * ids/slugs; AND across taxonomies, IN inside one taxonomy.
	 *
	 * @param array<string,mixed> $state Filter state.
	 * @return array<string,mixed>
	 */
	public static function build_project_query( array $state ): array {
		$args = array(
			'post_type'      => 'projects',
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, min( self::MAX_PER_PAGE, (int) ( $state['per_page'] ?? 12 ) ) ),
			'paged'          => max( 1, (int) ( $state['page'] ?? 1 ) ),
		);
		$term = trim( (string) ( $state['term'] ?? '' ) );
		if ( '' !== $term ) {
			$args['s'] = $term; // Escaped by WP_Query prepare.
		}
		$tax_query = array();
		foreach ( self::allowed_project_taxonomies() as $taxonomy ) {
			$values = $state['tax'][ $taxonomy ] ?? array();
			if ( ! is_array( $values ) || ! $values ) {
				continue;
			}
			$values = array_slice( array_values( array_filter( array_map( 'strval', $values ) ) ), 0, 50 );
			if ( ! $values ) {
				continue;
			}
			$is_ids = ! in_array( '0', preg_grep( '/[^0-9]/', $values ), true );
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => $is_ids ? 'term_id' : 'slug',
				'terms'    => $is_ids ? array_map( 'intval', $values ) : $values,
			);
		}
		if ( $tax_query ) {
			$tax_query['relation'] = 'AND';
			$args['tax_query']     = $tax_query;
		}
		$orderby = (string) ( $state['orderby'] ?? 'newest' );
		if ( ! isset( self::PROJECT_ORDERBY[ $orderby ] ) ) {
			$orderby = 'newest';
		}
		switch ( $orderby ) {
			case 'popular':
				$args['orderby'] = 'comment_count';
				$args['order']   = 'DESC';
				break;
			case 'name':
				$args['orderby'] = 'title';
				$args['order']   = 'ASC';
				break;
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
		}
		return $args;
	}

	/**
	 * Run the project filter query with the same last-page clamp as products.
	 *
	 * @param array<string,mixed> $state Filter state.
	 * @return array{ids:array<int,int>,total:int,pages:int,page:int}
	 */
	public static function query_projects( array $state ): array {
		// P08-T011: version-keyed result cache (invalidated on content change).
		$key = 'projects:' . md5( (string) wp_json_encode( self::cacheable_state( $state ) ) );
		$hit = self::cache_get( $key );
		if ( is_array( $hit ) ) {
			return $hit;
		}
		$result = self::run_paged_query( self::build_project_query( $state ) );
		self::cache_set( $key, $result );
		return $result;
	}

	/**
	 * Shared paged runner: ids/total/pages with out-of-range clamping (the
	 * SQLite QA-DB reports found_rows=0 past the last page — probe page 1).
	 *
	 * @param array<string,mixed> $args WP_Query args (fields forced to ids).
	 * @return array{ids:array<int,int>,total:int,pages:int,page:int}
	 */
	private static function run_paged_query( array $args ): array {
		$args['fields']        = 'ids';
		$args['no_found_rows'] = false;
		$q     = new \WP_Query( $args );
		$page  = max( 1, (int) ( $args['paged'] ?? 1 ) );
		$pages = (int) $q->max_num_pages;
		if ( ! $q->posts && $page > 1 ) {
			$probe_args          = $args;
			$probe_args['paged'] = 1;
			$probe               = new \WP_Query( $probe_args );
			$total               = (int) $probe->found_posts;
			$pages               = (int) ceil( $total / max( 1, (int) $probe_args['posts_per_page'] ) );
			if ( $pages > 0 && $page > $pages ) {
				$page          = $pages;
				$args['paged'] = $page;
				$q             = new \WP_Query( $args );
			}
		}
		return array(
			'ids'   => array_map( 'intval', $q->posts ),
			'total' => (int) $q->found_posts,
			'pages' => $pages,
			'page'  => $page,
		);
	}

	/**
	 * Per-term counters for the filter UI (agent.md 8.1): faceted — a taxonomy's
	 * counters respect the search term and the OTHER taxonomy's filter.
	 *
	 * @param array<string,mixed> $state Active filter state.
	 * @return array<string,array<int,int>> tax => termId => count
	 */
	public static function project_term_counts( array $state ): array {
		// P08-T011: version-keyed facet counters cache.
		$key = 'project-counts:' . md5( (string) wp_json_encode( $state ) );
		$hit = self::cache_get( $key );
		if ( is_array( $hit ) ) {
			return $hit;
		}
		$counts = self::project_term_counts_uncached( $state );
		self::cache_set( $key, $counts );
		return $counts;
	}

	private static function project_term_counts_uncached( array $state ): array {
		$counts = array();
		foreach ( self::allowed_project_taxonomies() as $taxonomy ) {
			$counts[ $taxonomy ] = array();
			$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
			if ( is_wp_error( $terms ) || ! $terms ) {
				continue;
			}
			$faceted           = $state;
			$faceted['tax']    = isset( $state['tax'] ) && is_array( $state['tax'] ) ? $state['tax'] : array();
			unset( $faceted['tax'][ $taxonomy ] ); // Same-taxonomy selection must not zero its own rows.
			$faceted['page']   = 1;
			$faceted['per_page'] = 1; // Only the count matters.
			foreach ( $terms as $term ) {
				$faceted['tax'][ $taxonomy ] = array( (int) $term->term_id );
				$result = self::query_projects( $faceted );
				$counts[ $taxonomy ][ (int) $term->term_id ] = (int) $result['total'];
			}
		}
		return $counts;
	}

	/**
	 * Endpoint callback (P08-T010): project filter state → query → reference
	 * cards + facet counters.
	 *
	 * @param array<string,mixed> $input Whitelisted input.
	 * @return array<string,mixed>
	 */
	public static function ajax_project_filters( array $input ): array {
		$tax    = array();
		$tax['project_type'] = $input['types'] ?? array();
		$tax['city']         = $input['cities'] ?? array();
		$result = self::query_projects( array(
			'term'     => $input['term'],
			'tax'      => $tax,
			'orderby'  => $input['orderby'],
			'page'     => $input['page'],
			'per_page' => $input['per_page'],
		) );

		if ( $result['ids'] && function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( $result['ids'], true, true ); // AC-03: no N+1.
		}
		$template = locate_template( 'template-parts/cards/card-project.php' );
		$html     = '';
		if ( $template && $result['ids'] ) {
			ob_start();
			foreach ( $result['ids'] as $id ) {
				$args = array( 'project_id' => (int) $id ); // Same contract as the shell grid.
				include $template; // include (not require_once): cards repeat per request.
			}
			$html = (string) ob_get_clean();
		}

		return array(
			'html'   => $html,
			'total'  => $result['total'],
			'pages'  => $result['pages'],
			'page'   => $result['page'],
			'counts' => self::project_term_counts( array(
				'term' => $input['term'],
				'tax'  => $tax,
			) ),
		);
	}

	/**
	 * Strict price normalizer (P08-T007 AC-01): numeric, non-negative only.
	 * Non-numeric/negative/array input → null (clause omitted, never fatal).
	 *
	 * @param mixed $value Raw price.
	 */
	public static function sanitize_price( $value ): ?float {
		if ( ! is_scalar( $value ) || ! is_numeric( $value ) ) {
			return null;
		}
		$price = (float) $value;
		return $price >= 0 ? $price : null;
	}

	/**
	 * Purchase-mode whitelist (P08-T007 AC-03): the legacy true_sale meta only
	 * stores 'true'/'false'; anything else means "no filter".
	 *
	 * @param mixed $value Raw mode.
	 */
	public static function sanitize_sale_mode( $value ): string {
		return in_array( $value, array( 'online', 'inquiry' ), true ) ? (string) $value : 'all';
	}

	/**
	 * Run the product filter query.
	 *
	 * @param array<string,mixed> $state Filter state.
	 * @return array{ids:array<int,int>,total:int,pages:int,page:int,args:array<string,mixed>}
	 */
	public static function query_products( array $state ): array {
		// P08-T011: version-keyed result cache (invalidated on content change).
		$key = 'products:' . md5( (string) wp_json_encode( self::cacheable_state( $state ) ) );
		$hit = self::cache_get( $key );
		if ( is_array( $hit ) ) {
			return $hit;
		}
		$result = self::run_paged_query( self::build_product_query( $state ) );
		self::cache_set( $key, $result );
		return $result;
	}
	/**
	 * State fingerprint for cache keys: only fields that change results.
	 *
	 * @param array<string,mixed> $state Raw state.
	 * @return array<string,mixed>
	 */
	private static function cacheable_state( array $state ): array {
		return array(
			'term'      => (string) ( $state['term'] ?? '' ),
			'tax'       => $state['tax'] ?? array(),
			'price_min' => (float) ( $state['price_min'] ?? 0 ),
			'price_max' => (float) ( $state['price_max'] ?? 0 ),
			'sale_mode' => (string) ( $state['sale_mode'] ?? 'all' ),
			'orderby'   => (string) ( $state['orderby'] ?? 'latest' ),
			'page'      => (int) ( $state['page'] ?? 1 ),
			'per_page'  => (int) ( $state['per_page'] ?? 12 ),
		);
	}
}
