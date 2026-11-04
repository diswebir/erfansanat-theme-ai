<?php
/** Stable WooCommerce and product-brand permalink contracts. */

namespace ErfanSanat\Theme\Content;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Adjusts rewrite arguments without flushing rules during ordinary requests. */
final class Rewrites implements ModuleInterface {

	/** Attach rewrite filters and register the fallback brand taxonomy. */
	public function register(): void {
		add_filter( 'register_taxonomy_args', array( self::class, 'filter_taxonomy_args' ), 99, 3 );
		add_filter( 'register_post_type_args', array( self::class, 'filter_product_args' ), 99, 2 );
		add_action( 'init', array( self::class, 'register_product_brand' ), 20 );
		add_action( 'after_switch_theme', array( self::class, 'maybe_flush_rewrites' ), 10 );
	}

	/** Apply the legacy category/tag paths before WordPress creates the taxonomy object. */
	public static function filter_taxonomy_args( array $args, string $taxonomy, $object_type = array() ): array {
		if ( 'product_cat' === $taxonomy ) {
			$args['hierarchical'] = true;
			$args['rewrite']     = array(
				'slug'         => 'product_cat',
				'with_front'   => true,
				'hierarchical' => true,
			);
		} elseif ( 'product_tag' === $taxonomy ) {
			$args['rewrite'] = array(
				'slug'       => 'product-tag',
				'with_front' => false,
			);
		}

		return $args;
	}

	/** Keep WooCommerce single-product permalinks at /product/{slug}/. */
	public static function filter_product_args( array $args, string $post_type ): array {
		if ( 'product' === $post_type ) {
			$rewrite = is_array( $args['rewrite'] ?? null ) ? $args['rewrite'] : array();
			$args['rewrite'] = array_merge(
				$rewrite,
				array(
					'slug'       => 'product',
					'with_front' => false,
				)
			);
		}

		return $args;
	}

	/** Soft-flush once after activation when the rewrite contract version changes. */
	public static function maybe_flush_rewrites(): bool {
		$version = defined( 'ES_REWRITE_VERSION' ) ? (string) ES_REWRITE_VERSION : '1';
		if ( ! function_exists( 'flush_rewrite_rules' ) || ! function_exists( 'get_option' ) || ! function_exists( 'update_option' ) || $version === (string) get_option( 'es_rewrite_version', '' ) ) {
			return false;
		}

		flush_rewrite_rules( false );
		update_option( 'es_rewrite_version', $version, false );
		return true;
	}

	/** Register product_brand only if WooCommerce's product type is available. */
	public static function register_product_brand(): void {
		if ( taxonomy_exists( 'product_brand' ) || ! post_type_exists( 'product' ) ) {
			return;
		}

		register_taxonomy(
			'product_brand',
			array( 'product' ),
			array(
				'labels'            => array(
					'name'          => __( 'برندها', 'erfansanat-theme' ),
					'singular_name' => __( 'برند', 'erfansanat-theme' ),
					'menu_name'     => __( 'برندها', 'erfansanat-theme' ),
					'all_items'     => __( 'همه برندها', 'erfansanat-theme' ),
					'edit_item'     => __( 'ویرایش برند', 'erfansanat-theme' ),
					'add_new_item'  => __( 'افزودن برند', 'erfansanat-theme' ),
					'not_found'     => __( 'برندی پیدا نشد.', 'erfansanat-theme' ),
				),
				'public'            => true,
				'publicly_queryable' => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'rewrite'           => array( 'slug' => 'product_brand', 'with_front' => false ),
			)
		);
	}
}
