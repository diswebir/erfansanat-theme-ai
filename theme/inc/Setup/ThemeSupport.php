<?php
/**
 * Theme supports and text domain.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Setup;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Registers add_theme_support() calls and the translation domain.
 */
final class ThemeSupport implements ModuleInterface {

	/**
	 * {@inheritDoc}
	 */
	public function register(): void {
		add_action( 'after_setup_theme', array( $this, 'setup' ), 5 );
	}

	/**
	 * Apply theme supports. Produces no output.
	 */
	public function setup(): void {
		load_theme_textdomain( 'erfansanat', ES_THEME_DIR . '/languages' );

		// Deliberately NOT adding 'align-wide' / 'block-templates': the static
		// markup is a fixed design and block layout supports would only add CSS.
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 64,
				'width'       => 64,
				'flex-height' => true,
				'flex-width'  => true,
				'header-text' => false,
			)
		);

		if ( class_exists( 'WooCommerce' ) ) {
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
		}
	}
}
