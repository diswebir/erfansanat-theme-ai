<?php
/**
 * Admin typography loader (P02-T019). Enqueues the self-hosted Vazirmatn
 * stylesheet and tags the admin body ONLY on theme-owned screens: the custom
 * dashboard, the theme metabox editors (product / projects / page / post /
 * services) and the theme term screens. Nothing leaks to the front-end or to
 * unrelated wp-admin screens.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

final class Typography {

	public const HANDLE     = 'es-admin-typography';
	public const BODY_CLASS = 'es-admin-vazirmatn';

	/** Post types that carry theme metaboxes / section builder. */
	private const POST_TYPES = array( 'product', 'projects', 'page', 'post', 'services' );

	/** Taxonomies that carry theme term metaboxes. */
	private const TAXONOMIES = array( 'product_cat', 'product_tag', 'product_brand', 'project_type', 'city' );

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		// Priority 5 so the font is registered before dashboard/metabox assets.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 5 );
		add_filter( 'admin_body_class', array( __CLASS__, 'body_class' ) );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Whether the current admin screen belongs to the theme panel.
	 *
	 * @param string $hook_suffix Admin page hook (optional; falls back to screen).
	 */
	public static function is_theme_screen( string $hook_suffix = '' ): bool {
		if ( '' !== $hook_suffix && false !== strpos( $hook_suffix, ThemeOptions::SLUG ) ) {
			return true;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen ) {
			return false;
		}

		if ( false !== strpos( (string) $screen->id, ThemeOptions::SLUG ) ) {
			return true;
		}

		if ( 'post' === $screen->base
			&& in_array( $screen->post_type, self::POST_TYPES, true ) ) {
			return true;
		}

		if ( in_array( $screen->base, array( 'edit-tags', 'term' ), true )
			&& in_array( $screen->taxonomy, self::TAXONOMIES, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Enqueue the shared font stylesheet on theme admin screens only.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public static function enqueue_assets( $hook_suffix ): void {
		if ( ! self::is_theme_screen( (string) $hook_suffix ) ) {
			return;
		}

		$css = ES_ASSETS_DIR . 'admin/font.css';
		wp_enqueue_style(
			self::HANDLE,
			ES_ASSETS_URI . 'admin/font.css',
			array(),
			is_file( $css ) ? (string) filemtime( $css ) : ES_VERSION
		);
	}

	/**
	 * Tag the admin body so the scoped typography rules apply (including the
	 * media modal, which is appended outside the metabox DOM).
	 *
	 * @param string $classes Space-separated body classes.
	 */
	public static function body_class( $classes ): string {
		if ( self::is_theme_screen() ) {
			$classes .= ' ' . self::BODY_CLASS;
		}
		return $classes;
	}
}
