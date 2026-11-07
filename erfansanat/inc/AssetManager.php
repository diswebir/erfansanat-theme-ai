<?php
/**
 * Conditional front-end asset loading.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues the approved static stylesheet and behaviour script with filemtime
 * versions, and exposes helpers for the template layers that follow.
 *
 * The static reference ships one stylesheet and one script shared by all 128
 * pages, so both are loaded for every front-end view. The video and the large
 * artwork stay out of the asset queue: templates print them where the matching
 * section is rendered, which keeps unrelated pages free of the 6.7 MB media.
 */
final class AssetManager {

	/**
	 * Stylesheet handle.
	 */
	public const STYLE_HANDLE = 'erfansanat-styles';

	/**
	 * Behaviour script handle.
	 */
	public const SCRIPT_HANDLE = 'erfansanat-main';

	/**
	 * Article typography handle.
	 */
	public const ARTICLE_STYLE_HANDLE = 'erfansanat-article';

	/**
	 * Accessibility helpers handle.
	 *
	 * `styles.css` is the approved build artefact and is never edited by hand, so
	 * the two rules the reference never needed live in their own small file.
	 */
	public const A11Y_STYLE_HANDLE = 'erfansanat-a11y';

	/**
	 * WordPress dashicons are not needed on the front end.
	 */
	private const UNNEEDED_STYLES = array( 'dashicons' );

	/**
	 * Attach the enqueue hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_frontend' ), 20 );
		add_action( 'wp_enqueue_scripts', array( self::class, 'drop_unneeded_styles' ), 100 );
		add_action( 'wp_head', array( self::class, 'print_font_preloads' ), 2 );
	}

	/**
	 * Permalink-safe version for a theme asset.
	 *
	 * @param string $relative Path inside assets/, for example css/styles.css.
	 * @return string
	 */
	public static function version( string $relative ): string {
		$path = self::path( $relative );
		if ( '' === $path || ! is_file( $path ) ) {
			return ERFANSANAT_THEME_VERSION;
		}
		clearstatcache( true, $path );
		return (string) filemtime( $path );
	}

	/**
	 * Public URL for a theme asset.
	 *
	 * @param string $relative Path inside assets/.
	 * @return string
	 */
	public static function uri( string $relative ): string {
		return trailingslashit( ERFANSANAT_THEME_URI ) . 'assets/' . ltrim( $relative, '/' );
	}

	/**
	 * Absolute path for a theme asset, or an empty string when it escapes assets/.
	 *
	 * @param string $relative Path inside assets/.
	 * @return string
	 */
	public static function path( string $relative ): string {
		$relative = ltrim( str_replace( '\\', '/', $relative ), '/' );
		if ( '' === $relative || str_contains( $relative, '..' ) ) {
			return '';
		}
		$base = realpath( ERFANSANAT_THEME_DIR . '/assets' );
		$file = realpath( ERFANSANAT_THEME_DIR . '/assets/' . $relative );
		if ( false === $base || false === $file || ! str_starts_with( $file, $base . DIRECTORY_SEPARATOR ) ) {
			return '';
		}
		return $file;
	}

	/**
	 * Whether a file exists inside assets/.
	 *
	 * @param string $relative Path inside assets/.
	 * @return bool
	 */
	public static function has( string $relative ): bool {
		return '' !== self::path( $relative );
	}

	/**
	 * Enqueue the shared stylesheet and behaviour script.
	 *
	 * @return void
	 */
	public static function enqueue_frontend(): void {
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_style(
			self::STYLE_HANDLE,
			self::uri( 'css/styles.css' ),
			array(),
			self::version( 'css/styles.css' )
		);

		// Loaded after styles.css and depending on it: the skip link and
		// `.screen-reader-text` are not in the compiled build.
		wp_enqueue_style(
			self::A11Y_STYLE_HANDLE,
			self::uri( 'css/a11y.css' ),
			array( self::STYLE_HANDLE ),
			self::version( 'css/a11y.css' )
		);

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			self::uri( 'js/main.js' ),
			array(),
			self::version( 'js/main.js' ),
			true
		);

		// Live search and native filters read this. It is localised rather than
		// inlined so the nonce and the endpoint live in one place the PHP side owns.
		wp_localize_script(
			self::SCRIPT_HANDLE,
			'erfansanatLiveSearch',
			\ErfanSanat\Theme\LiveSearch::script_data()
		);

		// Article typography is only needed where an editor's post content is
		// printed, so it never costs a page that has no article on it.
		if ( is_singular( 'post' ) ) {
			wp_enqueue_style(
				self::ARTICLE_STYLE_HANDLE,
				self::uri( 'css/article.css' ),
				array( self::STYLE_HANDLE ),
				self::version( 'css/article.css' )
			);
		}
	}

	/**
	 * Remove dashicons from the front end for logged-out visitors.
	 *
	 * The admin bar needs them, so logged-in requests keep the handle.
	 *
	 * @return void
	 */
	public static function drop_unneeded_styles(): void {
		if ( is_user_logged_in() ) {
			return;
		}
		foreach ( self::UNNEEDED_STYLES as $handle ) {
			wp_dequeue_style( $handle );
		}
	}

	/**
	 * Preload the two font subsets that every page starts with.
	 *
	 * Only files that exist are printed, and the crossorigin attribute matches
	 * the anonymous CORS mode browsers require for font preloads.
	 *
	 * @return void
	 */
	public static function print_font_preloads(): void {
		if ( is_admin() || ! self::has( 'fonts/vazirmatn-arabic.woff2' ) ) {
			return;
		}
		$fonts = array( 'fonts/vazirmatn-arabic.woff2', 'fonts/vazirmatn-latin.woff2' );
		foreach ( $fonts as $font ) {
			if ( ! self::has( $font ) ) {
				continue;
			}
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin />' . "\n",
				esc_url( self::uri( $font ) )
			);
		}
	}

	/**
	 * URL and dimensions for media that templates print outside the asset queue.
	 *
	 * Returns an empty array when the file is missing or the size cannot be read,
	 * so callers can skip the section instead of printing a broken tag.
	 *
	 * @param string $relative Path inside assets/.
	 * @return array{url: string, width: int, height: int}|array{}
	 */
	public static function media( string $relative ): array {
		$path = self::path( $relative );
		if ( '' === $path ) {
			return array();
		}
		$size = @getimagesize( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Missing or unreadable media must not break a template.
		if ( ! is_array( $size ) ) {
			return array();
		}
		return array(
			'url'    => self::uri( $relative ),
			'width'  => (int) $size[0],
			'height' => (int) $size[1],
		);
	}
}
