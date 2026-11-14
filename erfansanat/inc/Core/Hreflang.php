<?php
/**
 * Front-end hreflang output (P04-T017).
 *
 * Replaces the static mirror's non-standard `<meta name="hreflang:xx">` tags
 * with standard `<link rel="alternate" hreflang="xx" href="…">` tags, built from
 * the legacy per-object meta `hrefmeta_fa` / `hrefmeta_en` / `hrefmeta_ar`
 * (the shared `same_key_hrefl` key groups translations for the hreflangpch
 * plugin / admin column). Empty values never produce a tag (SC-02).
 *
 * Compatibility (AC-03): when SEOPress or hreflangpch is active the theme
 * defers to the plugin to avoid duplicate tags; the `es_hreflang_enabled`
 * filter can force the behaviour either way.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class Hreflang {

	/** @var array<string,string> lang => legacy meta key. */
	public const LANGS = array(
		'fa' => 'hrefmeta_fa',
		'en' => 'hrefmeta_en',
		'ar' => 'hrefmeta_ar',
	);

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'wp_head', array( __CLASS__, 'render' ), 5 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Whether the theme should print hreflang tags. Defers to SEOPress /
	 * hreflangpch when present (AC-03); filterable via es_hreflang_enabled.
	 */
	public static function should_render(): bool {
		$enabled = ! self::plugin_handles_hreflang();

		/**
		 * Filter whether the theme outputs hreflang tags.
		 *
		 * @param bool $enabled
		 */
		return (bool) apply_filters( 'es_hreflang_enabled', $enabled );
	}

	/**
	 * Detect an active plugin that already manages hreflang.
	 */
	private static function plugin_handles_hreflang(): bool {
		if ( defined( 'SEOPRESS_VERSION' ) || function_exists( 'seopress_init' ) ) {
			return true;
		}
		if ( defined( 'HREFLANGPCH_VERSION' ) || class_exists( 'HreflangPch' ) || function_exists( 'hreflangpch_init' ) ) {
			return true;
		}

		$active = (array) get_option( 'active_plugins', array() );
		foreach ( $active as $plugin ) {
			$plugin = (string) $plugin;
			if ( false !== strpos( $plugin, 'seopress' ) || false !== strpos( $plugin, 'hreflang' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Collect the current object's hreflang URLs (non-empty only).
	 *
	 * @return array<string,string> lang => URL.
	 */
	public static function current_urls(): array {
		$urls = array();

		if ( is_singular() ) {
			$post_id = (int) get_queried_object_id();
			if ( $post_id > 0 ) {
				foreach ( self::LANGS as $lang => $key ) {
					$url = esc_url_raw( (string) get_post_meta( $post_id, $key, true ) );
					if ( '' !== $url ) {
						$urls[ $lang ] = $url;
					}
				}
			}
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term instanceof \WP_Term ) {
				foreach ( self::LANGS as $lang => $key ) {
					$url = esc_url_raw( (string) get_term_meta( $term->term_id, $key, true ) );
					if ( '' !== $url ) {
						$urls[ $lang ] = $url;
					}
				}
			}
		}

		/**
		 * Filter the hreflang URLs for the current object.
		 *
		 * @param array<string,string> $urls lang => URL.
		 */
		return (array) apply_filters( 'es_hreflang_links', $urls );
	}

	/**
	 * Print the standard link tags into wp_head.
	 */
	public static function render(): void {
		if ( ! self::should_render() ) {
			return;
		}

		$urls = self::current_urls();
		if ( empty( $urls ) ) {
			return; // SC-02: nothing set → no tags.
		}

		foreach ( $urls as $lang => $url ) {
			printf(
				'<link rel="alternate" hreflang="%s" href="%s" />' . "\n",
				esc_attr( $lang ),
				esc_url( $url )
			);
		}

		// x-default points to the Persian version (falls back to the first URL).
		$default = $urls['fa'] ?? reset( $urls );
		printf(
			'<link rel="alternate" hreflang="x-default" href="%s" />' . "\n",
			esc_url( $default )
		);
	}
}
