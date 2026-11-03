<?php
/**
 * Front-end de-bloat: block library assets, emoji and extra head tags.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class DeBloat {

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}

		self::$booted = true;

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'dequeue_block_assets' ), 100 );
		add_action( 'init', array( __CLASS__, 'clean_head' ) );
	}

	public static function dequeue_block_assets(): void {
		if ( is_admin() ) {
			return;
		}

		$styles = array(
			'wp-block-library',
			'wp-block-library-theme',
			'global-styles',
			'classic-theme-styles',
		);

		foreach ( $styles as $handle ) {
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}

		$scripts = array(
			'wp-block-library',
			'wp-block-navigation-view',
		);

		foreach ( $scripts as $handle ) {
			wp_dequeue_script( $handle );
			wp_deregister_script( $handle );
		}
	}

	public static function clean_head(): void {
		if ( is_admin() ) {
			return;
		}

		// Emoji.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'emoji_svg_url', '__return_false' );

		// Extra head tags. Keep feeds, REST/oEmbed and SEO plugin output intact.
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'parent_post_rel_link', 10 );
		remove_action( 'wp_head', 'start_post_rel_link', 10 );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}
}
