<?php
/**
 * Front-end clean-up of block, global style and emoji output.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Removes page-builder-era payload from the public front end without touching
 * the administration screens or the assets other active plugins depend on.
 *
 * Deliberately kept:
 * - the block editor and its styles inside wp-admin;
 * - WooCommerce block assets such as wc-blocks-*, because cart and checkout
 *   blocks are built by WooCommerce and are not part of the theme;
 * - variation swatch, SMS login and user panel output, which the theme does not
 *   own and must not deregister;
 * - feeds, robots, canonical, hreflang and SEO output.
 */
final class DeBloat {

	/**
	 * Stylesheets owned by core's block layer.
	 *
	 * @var string[]
	 */
	private const BLOCK_STYLES = array(
		'wp-block-library',
		'wp-block-library-theme',
		'wc-blocks-style',
		'global-styles',
		'classic-theme-styles',
	);

	/**
	 * Scripts that only exist for the emoji fallback and the oEmbed iframe.
	 *
	 * @var string[]
	 */
	private const LEGACY_SCRIPTS = array(
		'wp-emoji',
		'wp-embed',
	);

	/**
	 * Head actions that expose meta that the SEO plugin owns instead.
	 *
	 * @var string[]
	 */
	private const LEGACY_HEAD_ACTIONS = array(
		'wp_generator',
		'rsd_link',
		'wlwmanifest_link',
		'wp_shortlink_wp_head',
		'wp_oembed_add_discovery_links',
		'wp_oembed_add_host_js',
		'rest_output_link_wp_head',
	);

	/**
	 * Whether front-end pruning applies to the current request.
	 *
	 * Administration, REST, AJAX, feeds, embeds and previews keep their assets so
	 * the editor stays functional and machine-readable output keeps metadata.
	 *
	 * @return bool
	 */
	public static function should_clean(): bool {
		if ( is_admin() || wp_is_json_request() ) {
			return false;
		}
		if ( wp_doing_ajax() || is_feed() || is_embed() || is_preview() ) {
			return false;
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}
		if ( function_exists( 'wp_is_serving_rest_request' ) && wp_is_serving_rest_request() ) {
			return false;
		}

		/**
		 * Allow a site to keep the core block layer on the front end, for example
		 * while a page still contains blocks that need it.
		 *
		 * @param bool $clean Whether to remove core block payload.
		 */
		return (bool) apply_filters( 'erfansanat_debloat_front_end', true );
	}

	/**
	 * Attach the clean-up hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'drop_block_styles' ), 100 );
		add_action( 'wp_enqueue_scripts', array( self::class, 'drop_legacy_scripts' ), 100 );
		add_action( 'wp_print_styles', array( self::class, 'drop_block_styles' ), 100 );
		add_action( 'init', array( self::class, 'drop_emoji_hooks' ), 20 );
		add_action( 'wp_head', array( self::class, 'start_head_capture' ), 0 );
		add_action( 'wp_head', array( self::class, 'end_head_capture' ), PHP_INT_MAX );
		add_filter( 'should_load_separate_core_block_assets', array( self::class, 'filter_separate_block_assets' ) );
		add_filter( 'should_load_block_assets_on_demand', array( self::class, 'filter_block_assets_on_demand' ) );
		add_filter( 'wp_resource_hints', array( self::class, 'filter_resource_hints' ), 10, 2 );
		add_filter( 'tiny_mce_plugins', array( self::class, 'filter_tiny_mce_plugins' ) );
	}

	/**
	 * Remove block stylesheets from the front-end queue.
	 *
	 * WooCommerce's own wc-blocks-* handles are matched separately so that a
	 * future WooCommerce file name cannot be removed by accident.
	 *
	 * @return void
	 */
	public static function drop_block_styles(): void {
		if ( ! self::should_clean() ) {
			return;
		}
		foreach ( self::BLOCK_STYLES as $handle ) {
			if ( 'wc-blocks-style' === $handle ) {
				continue;
			}
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}
	}

	/**
	 * Remove emoji and oEmbed runtime scripts from the front end.
	 *
	 * @return void
	 */
	public static function drop_legacy_scripts(): void {
		if ( ! self::should_clean() ) {
			return;
		}
		foreach ( self::LEGACY_SCRIPTS as $handle ) {
			wp_dequeue_script( $handle );
			wp_deregister_script( $handle );
		}
	}

	/**
	 * Unhook core's emoji and stale head actions.
	 *
	 * Runs on init so plugins can still re-add any of them deliberately.
	 *
	 * @return void
	 */
	public static function drop_emoji_hooks(): void {
		if ( ! self::should_clean() ) {
			return;
		}
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		foreach ( self::LEGACY_HEAD_ACTIONS as $action ) {
			remove_action( 'wp_head', $action );
		}
	}

	/**
	 * Capture the head, then remove tags the SEO plugin or the theme owns.
	 *
	 * A captured buffer is used instead of filters because WordPress prints the
	 * generator, shortlink and oEmbed tags directly, with no filter available.
	 *
	 * @return void
	 */
	public static function start_head_capture(): void {
		if ( ! self::should_clean() ) {
			return;
		}
		ob_start( array( self::class, 'strip_head_tags' ) );
	}

	/**
	 * Close the head buffer opened in start_head_capture().
	 *
	 * @return void
	 */
	public static function end_head_capture(): void {
		if ( ! self::should_clean() ) {
			return;
		}
		if ( ob_get_level() > 0 ) {
			ob_end_flush();
		}
	}

	/**
	 * Remove generator, shortlink, oEmbed discovery and RSD tags from the head.
	 *
	 * @param string $head Captured head markup.
	 * @return string
	 */
	public static function strip_head_tags( string $head ): string {
		$patterns = array(
			'#<meta\s+name=(["\'])generator\1[^>]*>\s*#i',
			'#<meta\s+name=generator[^>]*>\s*#i',
			'#<link\s+rel=(["\'])shortlink\1[^>]*>\s*#i',
			'#<link\s+rel=(["\'])EditURI\1[^>]*>\s*#i',
			'#<link\s+rel=(["\'])wlwmanifest\1[^>]*>\s*#i',
			'#<link\s+rel=(["\'])alternate\1[^>]*type=(["\'])application/json\+oembed\2[^>]*>\s*#i',
			'#<link\s+rel=(["\'])alternate\1[^>]*type=(["\'])text/xml\+oembed\2[^>]*>\s*#i',
			'#<link\s+rel=(["\'])https://api\.w\.org/\1[^>]*>\s*#i',
		);
		return (string) preg_replace( $patterns, '', $head );
	}

	/**
	 * Opt out of core's separate block asset loading on the front end.
	 *
	 * @return bool
	 */
	public static function filter_separate_block_assets(): bool {
		return self::should_clean() ? false : (bool) apply_filters( 'erfansanat_default_separate_block_assets', true );
	}

	/**
	 * Opt out of on-demand block asset loading on the front end.
	 *
	 * @return bool
	 */
	public static function filter_block_assets_on_demand(): bool {
		return self::should_clean() ? false : (bool) apply_filters( 'erfansanat_default_block_assets_on_demand', true );
	}

	/**
	 * Drop emoji and s.w.org hints from resource hints.
	 *
	 * @param array<int, mixed> $urls          Hint URLs or arrays.
	 * @param string            $relation_type Relation being filtered.
	 * @return array<int, mixed>
	 */
	public static function filter_resource_hints( $urls, $relation_type ) {
		// The relation type is part of the filter signature; hints are filtered
		// the same way for preconnect, dns-prefetch and preload.
		unset( $relation_type );
		if ( ! is_array( $urls ) || ! self::should_clean() ) {
			return $urls;
		}
		$blocked = array( 'https://s.w.org/images/core/emoji/' );
		return array_values(
			array_filter(
				$urls,
				static function ( $url ) use ( $blocked ) {
					$target = is_array( $url ) ? ( $url['href'] ?? '' ) : (string) $url;
					foreach ( $blocked as $prefix ) {
						if ( str_starts_with( $target, $prefix ) ) {
							return false;
						}
					}
					return true;
				}
			)
		);
	}

	/**
	 * Remove the emoji TinyMCE plugin.
	 *
	 * @param array<int, mixed> $plugins Registered plugins.
	 * @return array<int, mixed>
	 */
	public static function filter_tiny_mce_plugins( $plugins ) {
		if ( ! is_array( $plugins ) ) {
			return $plugins;
		}
		return array_values( array_diff( $plugins, array( 'wpemoji' ) ) );
	}
}
