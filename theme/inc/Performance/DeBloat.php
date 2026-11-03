<?php
/**
 * Front-end bloat removal.
 *
 * Removes Gutenberg block CSS/JS, global styles, emoji, oEmbed, embed script,
 * generator/rsd/wlwmanifest/shortlink/rest meta and dashicons for guests.
 * Admin and the editor are never touched.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Performance;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/**
 * DeBloat module.
 */
final class DeBloat implements ModuleInterface {

	/**
	 * Handles removed from the front-end.
	 *
	 * @var string[]
	 */
	private const DEQUEUE_STYLES = array(
		'wp-block-library',
		'wp-block-library-theme',
		'wc-blocks-style',
		'wc-blocks-style-all-products',
		'global-styles',
		'classic-theme-styles',
	);

	/**
	 * {@inheritDoc}
	 */
	public function register(): void {
		if ( is_admin() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_styles' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_scripts' ), 100 );
		add_action( 'wp_footer', array( $this, 'dequeue_late' ), 1 );
		add_action( 'init', array( $this, 'remove_head_actions' ) );
		add_action( 'init', array( $this, 'disable_emojis' ) );
		add_filter( 'wp_resource_hints', array( $this, 'remove_emoji_hints' ), 10, 2 );
		add_filter( 'body_class', array( $this, 'remove_embed_body_class' ) );
		add_action( 'wp_print_styles', array( $this, 'remove_global_styles_hooks' ), 1 );
	}

	/**
	 * Remove block related stylesheets.
	 */
	public function dequeue_styles(): void {
		foreach ( self::DEQUEUE_STYLES as $handle ) {
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}

		// Dashicons are only needed for logged-in users (admin bar).
		if ( ! is_admin_bar_showing() ) {
			wp_dequeue_style( 'dashicons' );
		}
	}

	/**
	 * Remove block related scripts and the embed script.
	 */
	public function dequeue_scripts(): void {
		foreach ( array( 'wp-embed', 'wp-block-library' ) as $handle ) {
			wp_dequeue_script( $handle );
			wp_deregister_script( $handle );
		}
	}

	/**
	 * Some block styles are printed very late; make sure they stay out.
	 */
	public function dequeue_late(): void {
		foreach ( self::DEQUEUE_STYLES as $handle ) {
			wp_dequeue_style( $handle );
		}
	}

	/**
	 * Drop global styles callbacks registered by core.
	 */
	public function remove_global_styles_hooks(): void {
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
		remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles' );
		remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
		remove_action( 'enqueue_block_assets', 'wp_enqueue_registered_block_scripts_and_styles' );
	}

	/**
	 * Remove head meta that adds no value for this site.
	 */
	public function remove_head_actions(): void {
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'template_redirect', 'rest_output_link_header', 11 );
		remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
	}

	/**
	 * Disable the emoji detection script and styles.
	 */
	public function disable_emojis(): void {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'tiny_mce_plugins', array( $this, 'remove_emoji_tinymce_plugin' ) );
	}

	/**
	 * Remove the emoji plugin from TinyMCE.
	 *
	 * @param array $plugins TinyMCE plugins.
	 * @return array
	 */
	public function remove_emoji_tinymce_plugin( $plugins ): array {
		return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
	}

	/**
	 * Drop the core `wp-embed-responsive` body class (embed support is removed).
	 *
	 * @param array $classes Body classes.
	 * @return array
	 */
	public function remove_embed_body_class( $classes ): array {
		return array_values( array_diff( (array) $classes, array( 'wp-embed-responsive' ) ) );
	}

	/**
	 * Remove the emoji DNS prefetch hint.
	 *
	 * @param array  $urls          Hint URLs.
	 * @param string $relation_type Relation type.
	 * @return array
	 */
	public function remove_emoji_hints( $urls, $relation_type ): array {
		if ( 'dns-prefetch' === $relation_type ) {
			$urls = array_filter(
				(array) $urls,
				static fn( $url ) => false === strpos( (string) $url, 's.w.org' )
			);
		}

		return (array) $urls;
	}
}
