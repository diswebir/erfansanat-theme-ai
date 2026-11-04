<?php
/**
 * Core theme setup.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Registers native theme features and WooCommerce support.
 */
final class Theme {

	/**
	 * Shared theme instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Singleton accessor.
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Attach setup hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );

		$this->boot_modules();

		if ( is_admin() ) {
			\ErfanSanat\Theme\Admin\ThemeOptions::register();
			\ErfanSanat\Theme\Admin\SectionManager::register();
			\ErfanSanat\Theme\Admin\ProductMetaboxes::register();
			\ErfanSanat\Theme\Admin\ProjectMetaboxes::register();
			\ErfanSanat\Theme\Admin\TermMetaboxes::register();
			\ErfanSanat\Theme\Admin\ContentMetaboxes::register();
			add_action( 'admin_init', array( \ErfanSanat\Theme\Admin\ThemeOptions::class, 'register_ajax' ) );
		}
	}

	/**
	 * Native feature registration without page-builder runtime dependencies.
	 *
	 * @return void
	 */
	public function setup(): void {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		add_image_size( 'erfansanat-card', 640, 480, true );
		add_image_size( 'erfansanat-hero', 1600, 900, true );

		if ( ! isset( $GLOBALS['content_width'] ) ) {
			$GLOBALS['content_width'] = 1300;
		}

		register_nav_menus(
			array(
				'main-mega-menu'  => __( 'مگامنوی اصلی', 'erfansanat' ),
				'mobile-menu'     => __( 'منوی موبایل', 'erfansanat' ),
				'tolidat-sherkat' => __( 'تولیدات شرکت', 'erfansanat' ),
				'landing'         => __( 'منوی لندینگ', 'erfansanat' ),
			)
		);
	}

	/**
	 * Module instances attached to this theme.
	 *
	 * @return void
	 */
	public function boot_modules(): void {
		DeBloat::register();
		AssetManager::register();
		ContentTypes::register();
		Rewrites::register();

		// WooCommerce boots its own classes after the theme, so the bridge is
		// attached to `init` instead of being checked for existence here.
		add_action( 'init', array( WooCommerce::class, 'register' ), 5 );

		// Navigation is stateless data plus renderers, so nothing is hooked
		// here; touching the classes keeps the autoloader map honest and makes
		// a broken navigation class fail loudly at boot instead of mid-render.
		class_exists( \ErfanSanat\Theme\Navigation\MenuTree::class );
		class_exists( \ErfanSanat\Theme\Navigation\MegaMenu::class );
		class_exists( \ErfanSanat\Theme\Navigation\MobileMenu::class );
	}

	/**
	 * Load translations when present.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_theme_textdomain( 'erfansanat', ERFANSANAT_THEME_DIR . '/languages' );
	}
}
