<?php
/**
 * Live AJAX search engines (P08-T002 products; projects/posts join in T003).
 *
 * Registered on the shared Ajax contract (P08-T001): unique action, own
 * nonce, typed whitelist. Product matching covers title/content (WP search),
 * legacy meta (english_titl / zireonvan / _sku) and product_cat names, with a
 * hard result cap and publish+visible-only policy. Rows carry a `type` flag
 * and real permalinks so the front end never guesses slugs.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class AjaxSearch {

	public const MAX_RESULTS = 12;

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		Ajax::register( 'product_search', array( __CLASS__, 'ajax_product_search' ), array(
			'public' => true,
			'inputs' => array(
				'term'  => array( 'type' => 'text', 'default' => '' ),
				'limit' => array( 'type' => 'int', 'default' => 6, 'min' => 1, 'max' => self::MAX_RESULTS ),
			),
		) );
	}

	/**
	 * Endpoint callback: sanitized input → engine → envelope data.
	 *
	 * @param array<string,mixed> $input Whitelisted input.
	 * @return array<string,mixed>
	 */
	public static function ajax_product_search( array $input ): array {
		$rows = self::search_products( (string) $input['term'], (int) $input['limit'] );
		return array(
			'results' => $rows,
			'total'   => count( $rows ),
			'message' => $rows ? '' : __( 'نتیجه‌ای یافت نشد.', 'erfansanat' ),
		);
	}

	/**
	 * Product search: title/content + legacy meta + category names.
	 * Publish-only; catalog-hidden products excluded when Woo provides the
	 * visibility taxonomy. Ordered: title matches first, then meta/category.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function search_products( string $term, int $limit = 6 ): array {
		$term = self::normalize( $term );
		// AC-03: no query for very short terms (1 char, any script).
		if ( preg_match_all( '/./us', $term ) < 2 || ! post_type_exists( 'product' ) ) {
			return array();
		}
		$cap  = max( 1, min( self::MAX_RESULTS, $limit ) );
		$base = array(
			'post_type'              => 'product',
			'post_status'            => 'publish', // AC-02: never draft/private/pending.
			'posts_per_page'         => $cap,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'ignore_sticky_posts'    => true,
		);

		// AC-02: respect Woo catalog/search visibility when present.
		$visibility = self::visibility_exclusion();

		$ids = array();

		// 1) Core search: title + content (Persian and English alike).
		$q = new \WP_Query( array_merge( $base, array( 's' => $term ), $visibility ) );
		$ids = array_merge( $ids, array_map( 'intval', $q->posts ) );

		// 2) Legacy meta: English title, subtitle, SKU.
		if ( count( $ids ) < $cap ) {
			$q = new \WP_Query( array_merge( $base, array(
				'meta_query' => array(
					'relation' => 'OR',
					array( 'key' => 'english_titl', 'value' => $term, 'compare' => 'LIKE' ),
					array( 'key' => 'zireonvan', 'value' => $term, 'compare' => 'LIKE' ),
					array( 'key' => '_sku', 'value' => $term, 'compare' => 'LIKE' ),
				),
			), $visibility ) );
			$ids = array_merge( $ids, array_map( 'intval', $q->posts ) );
		}

		// 3) Category names (product_cat).
		if ( count( $ids ) < $cap && taxonomy_exists( 'product_cat' ) ) {
			$terms = get_terms( array(
				'taxonomy'   => 'product_cat',
				'name__like' => $term,
				'hide_empty' => false,
				'fields'     => 'ids',
				'number'     => 10,
			) );
			if ( is_array( $terms ) && $terms ) {
				$tax = array( array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => array_map( 'intval', $terms ) ) );
				if ( isset( $visibility['tax_query'] ) ) {
					$tax = array_merge( $tax, $visibility['tax_query'] );
				}
				$q   = new \WP_Query( array_merge( $base, array( 'tax_query' => $tax ) ) );
				$ids = array_merge( $ids, array_map( 'intval', $q->posts ) );
			}
		}

		$ids = array_slice( array_values( array_unique( $ids ) ), 0, $cap );
		$out = array();
		foreach ( $ids as $id ) {
			$out[] = self::product_row( $id );
		}
		return $out;
	}

	/**
	 * One result row for the search modal (type-flagged, real permalink).
	 *
	 * @return array<string,mixed>
	 */
	public static function product_row( int $id ): array {
		$subtitle = '';
		if ( taxonomy_exists( 'product_cat' ) ) {
			$terms = get_the_terms( $id, 'product_cat' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$subtitle = implode( ' · ', wp_list_pluck( $terms, 'name' ) );
			}
		}
		$thumb = get_the_post_thumbnail_url( $id, 'thumbnail' );
		return array(
			'id'        => $id,
			'type'      => 'product',
			'title'     => get_the_title( $id ),
			'url'       => (string) get_permalink( $id ),
			'subtitle'  => $subtitle,
			'image'     => $thumb ? (string) $thumb : ES_ASSETS_URI . 'images/hero.jpg',
			'true_sale' => 'true' === (string) get_post_meta( $id, 'true_sale', true ),
		);
	}

	/** Normalize Arabic ye/kaf variants and collapse whitespace. */
	public static function normalize( string $term ): string {
		$term = str_replace( array( 'ي', 'ك', '‌' ), array( 'ی', 'ک', ' ' ), $term );
		$term = preg_replace( '/\s+/u', ' ', $term );
		return trim( (string) $term );
	}

	/**
	 * tax_query fragment excluding Woo hidden products, when the visibility
	 * taxonomy exists (no-op under the QA shim / without Woo).
	 *
	 * @return array<string,mixed>
	 */
	private static function visibility_exclusion(): array {
		if ( ! taxonomy_exists( 'product_visibility' ) ) {
			return array();
		}
		$exclude = array();
		foreach ( array( 'exclude-from-search', 'exclude-from-catalog' ) as $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_visibility' );
			if ( $term instanceof \WP_Term ) {
				$exclude[] = (int) $term->term_id;
			}
		}
		if ( ! $exclude ) {
			return array();
		}
		return array( 'tax_query' => array(
			array( 'taxonomy' => 'product_visibility', 'field' => 'term_id', 'terms' => $exclude, 'operator' => 'NOT IN' ),
		) );
	}
}
