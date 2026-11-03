<?php
/**
 * Theme lifecycle and feature supports.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class Theme {

	private static ?self $instance = null;

	private bool $setup_done = false;

	public static function boot(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
			add_action( 'after_setup_theme', array( self::$instance, 'setup' ) );
			add_action( 'after_switch_theme', array( self::$instance, 'after_switch_theme' ) );
		}

		return self::$instance;
	}

	public static function instance(): ?self {
		return self::$instance;
	}

	public function setup(): void {
		if ( $this->setup_done ) {
			return;
		}

		$this->setup_done = true;

		load_theme_textdomain( 'erfansanat', ES_THEME_DIR . 'languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'woocommerce' );
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
		);
		add_theme_support( 'custom-logo' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'automatic-feed-links' );

		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 1300;
		}
	}

	public function after_switch_theme(): void {
		// Rewrite flushing is owned by Phase 3. Activation only needs a clean setup.
		$this->setup();
	}

	public function has_setup_run(): bool {
		return $this->setup_done;
	}
}
