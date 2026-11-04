<?php
/** Small, validated CSS variable overrides for brand settings. */

namespace ErfanSanat\Theme\Frontend;

use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Defaults;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

/** Injects only an explicitly changed, hex-validated brand color. */
final class InlineCss implements ModuleInterface {

	/** Hook the tiny override into the document head. */
	public function register(): void {
		add_action( 'wp_head', array( self::class, 'render_output' ), 99 );
	}

	/** Return no output for the built-in palette or any malformed stored value. */
	public static function render(): string {
		$stored = Options::get( 'brand.color', Defaults::BRAND_COLOR );
		if ( ! is_string( $stored ) || '' === $stored ) {
			return '';
		}
		$color = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $stored ) : null;
		if ( ! is_string( $color ) || strtolower( $color ) === strtolower( Defaults::BRAND_COLOR ) ) {
			return '';
		}
		return '<style id="es-brand-inline-css">#es-page.es-site{--es-brand:' . esc_html( $color ) . ';}</style>';
	}

	/** Echo only validated output when a custom color is active. */
	public static function render_output(): void {
		echo self::render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Color is strictly hex-validated before CSS output.
	}
}
