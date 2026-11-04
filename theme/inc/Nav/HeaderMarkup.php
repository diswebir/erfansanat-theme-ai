<?php
/** Applies the shared announcement option without changing its static wrapper. */

namespace ErfanSanat\Theme\Nav;

defined( 'ABSPATH' ) || exit;

/** Dashboard-backed announcement rendering with safe URL handling. */
final class HeaderMarkup {

	public static function render( string $html ): string {
		$text = function_exists( 'es_option' ) ? es_option( 'header.announcement.text', 'فروشگاه آنلاین (ارسال سریع)' ) : 'فروشگاه آنلاین (ارسال سریع)';
		$url = function_exists( 'es_option' ) ? es_option( 'header.announcement.url', '' ) : '';
		$text = is_scalar( $text ) ? trim( (string) $text ) : '';
		$url = is_scalar( $url ) ? trim( (string) $url ) : '';
		if ( '' === $text ) { $text = 'فروشگاه آنلاین (ارسال سریع)'; }
		$safe_text = function_exists( 'esc_html' ) ? esc_html( $text ) : htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
		$replacement = $safe_text;
		if ( '' !== $url && self::valid_url( $url ) ) {
			$safe_url = function_exists( 'esc_url' ) ? esc_url( $url, array( 'http', 'https' ) ) : htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
			$replacement = '<a href="' . $safe_url . '" class="hover:text-emerald-700">' . $safe_text . '</a>';
		}
		$html = (string) preg_replace_callback(
			'/<span>گواهینامه ISO 9001:2015 و کدهای رسمی سامانه ستاد و GS1<\/span>/',
			static fn( $m ) => '<span>' . $replacement . '</span>',
			$html,
			1
		);
		if ( class_exists( \ErfanSanat\Theme\Frontend\NavConfig::class ) ) { $html = \ErfanSanat\Theme\Frontend\NavConfig::apply_markup( $html ); }
		$mega_enabled = ! class_exists( \ErfanSanat\Theme\Frontend\NavConfig::class ) || \ErfanSanat\Theme\Frontend\NavConfig::mega_enabled();
		if ( $mega_enabled && function_exists( 'has_nav_menu' ) && has_nav_menu( 'tolidat-sherkat' ) ) {
			$products = self::render_partial( 'template-parts/header/mega-products', static fn() => MegaMenuWalker::for_location( 'tolidat-sherkat' ) );
			if ( '' !== $products ) { $html = self::replace_div( $html, 'data-mega-menu-products="true"', $products ); }
		}
		if ( $mega_enabled && function_exists( 'get_terms' ) ) {
			$projects = self::render_partial( 'template-parts/header/mega-projects', static fn() => ProjectsMegaWalker::render() );
			if ( '' !== $projects ) { $html = self::replace_div( $html, 'data-mega-menu-projects="true"', $projects ); }
		}
		if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'mobile-menu' ) && class_exists( MobileMenuWalker::class ) ) {
			$mobile = MobileMenuWalker::for_location( 'mobile-menu' );
			if ( '' !== $mobile ) { $html = self::replace_div( $html, 'class="flex-1 space-y-2.5 overflow-y-auto overscroll-contain px-3.5 py-3.5"', $mobile ); }
		}
		return LegacyMarkup::rewrite_links( $html );
	}

	/** Render the optional template part, falling back to the class renderer outside WordPress. */
	private static function render_partial( string $slug, callable $fallback ): string {
		if ( ! function_exists( 'get_template_part' ) ) { return (string) $fallback(); }
		ob_start();
		get_template_part( $slug );
		$markup = (string) ob_get_clean();
		return '' !== $markup ? $markup : (string) $fallback();
	}

	/** Replace one balanced DIV selected by a stable data attribute. */
	private static function replace_div( string $html, string $marker, string $replacement ): string {
		$position = strpos( $html, $marker );
		if ( false === $position ) { return $html; }
		$start = strrpos( substr( $html, 0, $position ), '<div' );
		if ( false === $start ) { return $html; }
		$depth = 0;
		if ( ! preg_match_all( '/<\/?div\b[^>]*>/i', substr( $html, $start ), $matches, PREG_OFFSET_CAPTURE ) ) { return $html; }
		foreach ( $matches[0] as $token ) {
			if ( 0 === strpos( $token[0], '</' ) ) { --$depth; } else { ++$depth; }
			if ( 0 === $depth ) {
				$end = $start + $token[1] + strlen( $token[0] );
				return substr( $html, 0, $start ) . $replacement . substr( $html, $end );
			}
		}
		return $html;
	}

	private static function valid_url( string $url ): bool {
		$scheme = strtolower( (string) parse_url( $url, PHP_URL_SCHEME ) );
		return in_array( $scheme, array( 'http', 'https' ), true );
	}
}
