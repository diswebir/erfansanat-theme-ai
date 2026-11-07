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

		// Price range + purchase mode → numeric/char meta clauses.
		$meta_query = array();
		$min = isset( $state['price_min'] ) ? (float) $state['price_min'] : null;
		$max = isset( $state['price_max'] ) ? (float) $state['price_max'] : null;
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
		$mode = (string) ( $state['sale_mode'] ?? 'all' );
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
