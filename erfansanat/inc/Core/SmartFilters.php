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

	/** Register the public filter endpoint on the shared Ajax contract (P08-T006). */
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
		$args = self::build_product_query( $state );
		$args['fields'] = 'ids';
		$q = new \WP_Query( $args );
		return array(
			'ids'   => array_map( 'intval', $q->posts ),
			'total' => (int) $q->found_posts,
			'pages' => (int) $q->max_num_pages,
			'page'  => max( 1, (int) ( $args['paged'] ?? 1 ) ),
			'args'  => $args,
		);
	}
}
