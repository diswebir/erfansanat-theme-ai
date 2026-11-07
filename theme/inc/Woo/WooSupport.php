<?php
/** WooCommerce integration boundaries and compatibility declarations. */

namespace ErfanSanat\Theme\Woo;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Declares supported order storage and the classic (non-block) checkout surface. */
final class WooSupport implements ModuleInterface {

	/** Register integration only; every callback is safe when WooCommerce is absent. */
	public function register(): void {
		add_action( 'before_woocommerce_init', array( self::class, 'declare_compatibility' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_product_ui' ), 30 );
		add_action( 'wp', array( self::class, 'prepare_single_product_template' ), 20 );
	}

	/** Keep extension actions available while preventing duplicate core output. */
	public static function prepare_single_product_template(): void {
		$is_product = function_exists( 'is_product' ) && is_product();
		$is_archive = ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() );
		if ( ( ! $is_product && ! $is_archive ) || ! function_exists( 'remove_action' ) ) { return; }
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		if ( $is_archive ) {
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
		}
		if ( ! $is_product ) { return; }
		$defaults = array(
			array( 'woocommerce_show_product_sale_flash', 10, 'woocommerce_before_single_product_summary' ),
			array( 'woocommerce_show_product_images', 20, 'woocommerce_before_single_product_summary' ),
			array( 'woocommerce_template_single_title', 5, 'woocommerce_single_product_summary' ),
			array( 'woocommerce_template_single_rating', 10, 'woocommerce_single_product_summary' ),
			array( 'woocommerce_template_single_price', 10, 'woocommerce_single_product_summary' ),
			array( 'woocommerce_template_single_excerpt', 20, 'woocommerce_single_product_summary' ),
			array( 'woocommerce_template_single_add_to_cart', 30, 'woocommerce_single_product_summary' ),
			array( 'woocommerce_template_single_meta', 40, 'woocommerce_single_product_summary' ),
			array( 'woocommerce_template_single_sharing', 50, 'woocommerce_single_product_summary' ),
			array( 'woocommerce_output_product_data_tabs', 10, 'woocommerce_after_single_product_summary' ),
			array( 'woocommerce_upsell_display', 15, 'woocommerce_after_single_product_summary' ),
			array( 'woocommerce_output_related_products', 20, 'woocommerce_after_single_product_summary' ),
		);
		foreach ( $defaults as $default ) { remove_action( $default[2], $default[0], $default[1] ); }
	}

	/** Declare only compatibility implemented by the theme. */
	public static function declare_compatibility(): void {
		$features_util = '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil';
		if ( ! class_exists( $features_util ) || ! defined( 'ES_THEME_DIR' ) ) {
			return;
		}

		$theme_entry = ES_THEME_DIR . '/functions.php';
		$features_util::declare_compatibility( 'custom_order_tables', $theme_entry, true );
		// Cart/checkout blocks are deliberately not supported; the approved G3
		// design uses classic WooCommerce templates and preserves their hooks.
		$features_util::declare_compatibility( 'cart_checkout_blocks', $theme_entry, false );
	}

	/** Load small product-only enhancements without touching the protected main.js. */
	public static function enqueue_product_ui(): void {
		if ( ! function_exists( 'is_product' ) || ! is_product() || ! function_exists( 'wp_enqueue_script' ) ) {
			return;
		}
		$path = defined( 'ES_THEME_DIR' ) ? ES_THEME_DIR . '/assets/js/product-enhancements.js' : '';
		$url  = function_exists( 'get_template_directory_uri' ) ? get_template_directory_uri() . '/assets/js/product-enhancements.js' : '';
		if ( '' === $path || '' === $url || ! is_readable( $path ) ) {
			return;
		}
		$version = (string) filemtime( $path );
		wp_enqueue_script( 'es-product-enhancements', $url, array( 'es-main' ), $version, true );
	}
}
