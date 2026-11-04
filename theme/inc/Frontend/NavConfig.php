<?php
/** Whitelisted display settings for the shared navigation. */

namespace ErfanSanat\Theme\Frontend;

use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

/** Applies only known navigation modes; it never stores arbitrary CSS/classes. */
final class NavConfig implements ModuleInterface {

	public function register(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_assets' ), 30 );
		add_filter( 'body_class', array( self::class, 'body_classes' ), 20 );
	}

	public static function mega_enabled(): bool { return (bool) Options::get( 'nav.mega.enabled', true ); }
	public static function open_mode(): string { $value = Options::get( 'nav.open_mode', 'hover' ); return in_array( $value, array( 'hover', 'click' ), true ) ? $value : 'hover'; }
	public static function header_size(): string { $value = Options::get( 'nav.header_height', 'standard' ); return in_array( $value, array( 'standard', 'compact' ), true ) ? $value : 'standard'; }
	public static function sticky(): bool { return (bool) Options::get( 'nav.sticky', true ); }

	/** Add only non-default, whitelisted settings to static-compatible markup. */
	public static function apply_markup( string $html ): string {
		if ( ! self::mega_enabled() ) {
			$html = self::replace_root( $html, 'data-mega-menu-products="true"', self::simple_link( 'محصولات', self::archive_url( 'product', '/shop/' ) ) );
			$html = self::replace_root( $html, 'data-mega-menu-projects="true"', self::simple_link( 'پروژه‌ها', self::archive_url( 'projects', '/projects/' ) ) );
		}
		if ( 'click' === self::open_mode() ) { $html = self::set_header_attribute( $html, 'data-nav-open', 'click' ); }
		if ( 'compact' === self::header_size() ) { $html = self::set_header_attribute( $html, 'data-header-size', 'compact' ); }
		if ( ! self::sticky() ) {
			$html = (string) preg_replace_callback( '/(<header(?=[^>]*data-navbar-header="true")[^>]*\bclass=")([^"]*)(")/i', static function ( $m ) { $classes = preg_split( '/\s+/', trim( $m[2] ) ); $classes = array_values( array_diff( $classes, array( 'fixed' ) ) ); return $m[1] . implode( ' ', $classes ) . $m[3]; }, $html, 1 );
		}
		return $html;
	}

	/** Add approved body classes only when the user opts away from defaults. */
	public static function body_classes( $classes ) {
		$classes = is_array( $classes ) ? $classes : array();
		if ( ! self::mega_enabled() ) { $classes[] = 'es-mega-menu-disabled'; }
		if ( 'click' === self::open_mode() ) { $classes[] = 'es-nav-click'; }
		if ( 'compact' === self::header_size() ) { $classes[] = 'es-header-compact'; }
		if ( ! self::sticky() ) { $classes[] = 'es-header-not-sticky'; }
		return array_values( array_unique( $classes ) );
	}

	/** Load tiny adapters after main.js; the protected asset itself remains untouched. */
	public static function enqueue_assets(): void {
		if ( function_exists( 'is_admin' ) && is_admin() ) { return; }
		$base = function_exists( 'get_template_directory_uri' ) ? get_template_directory_uri() : '';
		$version = defined( 'ES_THEME_VERSION' ) ? ES_THEME_VERSION : '1.0.0';
		if ( function_exists( 'wp_enqueue_script' ) ) { wp_enqueue_script( 'es-nav-config', $base . '/assets/js/nav-config.js', array( 'es-main' ), $version, true ); }
		if ( 'compact' === self::header_size() && function_exists( 'wp_enqueue_style' ) ) { wp_enqueue_style( 'es-nav-config', $base . '/assets/css/nav-config.css', array( 'es-main' ), $version ); }
	}

	private static function set_header_attribute( string $html, string $attribute, string $value ): string {
		$safe = function_exists( 'esc_attr' ) ? esc_attr( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
		return (string) preg_replace_callback( '/<header(?=[^>]*data-navbar-header="true")[^>]*>/i', static function ( $m ) use ( $attribute, $safe ) {
			$tag = preg_replace( '/\s+' . preg_quote( $attribute, '/' ) . '="[^"]*"/i', '', $m[0] );
			return substr( $tag, 0, -1 ) . ' ' . $attribute . '="' . $safe . '">';
		}, $html, 1 );
	}

	private static function replace_root( string $html, string $marker, string $replacement ): string {
		$position = strpos( $html, $marker );
		if ( false === $position ) { return $html; }
		$start = strrpos( substr( $html, 0, $position ), '<div' );
		if ( false === $start ) { return $html; }
		$depth = 0;
		if ( ! preg_match_all( '/<\/?div\b[^>]*>/i', substr( $html, $start ), $tokens, PREG_OFFSET_CAPTURE ) ) { return $html; }
		foreach ( $tokens[0] as $token ) {
			if ( 0 === strpos( $token[0], '</' ) ) { --$depth; } else { ++$depth; }
			if ( 0 === $depth ) { $end = $start + $token[1] + strlen( $token[0] ); return substr( $html, 0, $start ) . $replacement . substr( $html, $end ); }
		}
		return $html;
	}

	private static function simple_link( string $label, string $url ): string {
		$href = function_exists( 'esc_url' ) ? esc_url( $url ) : htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
		$text = function_exists( 'esc_html' ) ? esc_html( $label ) : htmlspecialchars( $label, ENT_QUOTES, 'UTF-8' );
		return '<a href="' . $href . '" class="rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700">' . $text . '</a>';
	}

	private static function archive_url( string $post_type, string $fallback ): string {
		$url = function_exists( 'get_post_type_archive_link' ) ? get_post_type_archive_link( $post_type ) : false;
		return is_string( $url ) && '' !== $url ? $url : ( function_exists( 'home_url' ) ? home_url( $fallback ) : $fallback );
	}
}
