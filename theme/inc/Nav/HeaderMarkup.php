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
		return LegacyMarkup::rewrite_links( $html );
	}

	private static function valid_url( string $url ): bool {
		$scheme = strtolower( (string) parse_url( $url, PHP_URL_SCHEME ) );
		return in_array( $scheme, array( 'http', 'https' ), true );
	}
}
