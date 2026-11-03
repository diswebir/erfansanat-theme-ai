<?php
/**
 * Rewrite preservation for WooCommerce structures.
 *
 * The theme never re-registers `product` / `product_cat` (WooCommerce owns
 * them). Instead it corrects their rewrite args at registration time via the
 * core `register_post_type_args` / `register_taxonomy_args` filters, so the
 * live URL contract survives: product single = /product/, product_cat base =
 * product_cat (NOT product-category) with with_front=true and hierarchy.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class Rewrites {

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_filter( 'register_post_type_args', array( __CLASS__, 'filter_post_type_args' ), 10, 2 );
		add_filter( 'register_taxonomy_args', array( __CLASS__, 'filter_taxonomy_args' ), 10, 2 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Keep the product single base at /product/ (with_front=false).
	 *
	 * @param array<string,mixed> $args      Registration args.
	 * @param string            $post_type Post type slug.
	 * @return array<string,mixed>
	 */
	public static function filter_post_type_args( $args, $post_type ) {
		if ( 'product' === $post_type ) {
			$rewrite             = is_array( $args['rewrite'] ?? null ) ? $args['rewrite'] : array();
			$rewrite['slug']     = 'product';
			$rewrite['with_front'] = false;
			$args['rewrite']     = $rewrite;
		}

		return $args;
	}

	/**
	 * Keep product_cat base/hierarchy intact (P03-T005).
	 *
	 * @param array<string,mixed> $args     Registration args.
	 * @param string            $taxonomy Taxonomy slug.
	 * @return array<string,mixed>
	 */
	public static function filter_taxonomy_args( $args, $taxonomy ) {
		if ( 'product_cat' === $taxonomy ) {
			$rewrite                 = is_array( $args['rewrite'] ?? null ) ? $args['rewrite'] : array();
			$rewrite['slug']         = 'product_cat';
			$rewrite['with_front']   = true;
			$rewrite['hierarchical'] = true; // Keep nested category URLs (SC-02).
			$args['rewrite']         = $rewrite;
			$args['hierarchical']    = true;
		} elseif ( 'product_tag' === $taxonomy ) {
			// P03-T006: keep the product-tag base, front-less.
			$rewrite               = is_array( $args['rewrite'] ?? null ) ? $args['rewrite'] : array();
			$rewrite['slug']       = 'product-tag';
			$rewrite['with_front'] = false;
			$args['rewrite']       = $rewrite;
		} elseif ( 'product_brand' === $taxonomy ) {
			// P03-T006: brand stays hierarchical; its base is left untouched.
			$args['hierarchical'] = true;
		}

		return $args;
	}
}
