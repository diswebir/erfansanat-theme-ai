<?php
/**
 * Brand / colors / custom code tab.
 *
 * Colors are emitted only as a fixed allowlist of CSS variables. Custom
 * header/footer code is editable only by manage_options (enforced by the AJAX
 * layer) and is printed only in its designated front-end location — never in
 * wp-admin.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options\Tabs;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Schema;

final class BrandTab {

	public const ID = 'brand';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'wp_head', array( __CLASS__, 'head_output' ), 99 );
		add_action( 'wp_footer', array( __CLASS__, 'footer_output' ), 99 );
	}

	public static function id(): string {
		return self::ID;
	}

	/** @return array<int,string> */
	public static function fields(): array {
		return array_keys(
			array_filter(
				Schema::fields(),
				static function ( $field ) {
					return ( $field['tab'] ?? '' ) === self::ID;
				}
			)
		);
	}

	/** @return array<string,mixed> */
	public static function defaults(): array {
		$defaults = array();
		foreach ( self::fields() as $key ) {
			$defaults[ $key ] = Schema::default( $key );
		}
		return $defaults;
	}

	/**
	 * Fixed allowlist: option key => CSS variable name.
	 *
	 * @return array<string,string>
	 */
	public static function variable_map(): array {
		return array(
			'brand_primary'      => '--es-brand-primary',
			'brand_primary_dark' => '--es-brand-primary-dark',
			'brand_accent'       => '--es-brand-accent',
			'brand_surface'      => '--es-brand-surface',
		);
	}

	/**
	 * Validated CSS variables (invalid colors fall back to their default).
	 *
	 * @return array<string,string>
	 */
	public static function color_variables(): array {
		$vars = array();
		foreach ( self::variable_map() as $key => $name ) {
			$hex = sanitize_hex_color( (string) es_option( $key ) );
			if ( ! $hex ) {
				$hex = sanitize_hex_color( (string) Schema::default( $key ) );
			}
			$vars[ $name ] = (string) $hex;
		}
		return $vars;
	}

	/**
	 * Full front-end CSS: allowlisted variables + sanitized custom CSS.
	 */
	public static function css_variables(): string {
		$css = ':root{';
		foreach ( self::color_variables() as $name => $hex ) {
			$css .= $name . ':' . $hex . ';';
		}
		$css .= '}';

		$custom = self::neutralize_style_breakout( (string) es_option( 'custom_css' ) );
		if ( '' !== trim( $custom ) ) {
			$css .= "\n" . $custom;
		}

		return $css;
	}

	/**
	 * Prevent a stored value from closing the <style> tag early.
	 */
	private static function neutralize_style_breakout( string $css ): string {
		return preg_replace( '/<\/\s*style/i', '<\\/style', $css );
	}

	/**
	 * Print brand CSS + custom header code in wp_head (front-end only).
	 */
	public static function head_output(): void {
		if ( is_admin() ) {
			return;
		}

		echo '<style id="es-brand-vars">' . self::css_variables() . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput

		$header_code = (string) es_option( 'custom_header_script' );
		if ( '' !== trim( $header_code ) ) {
			echo $header_code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- admin-only custom code.
		}
	}

	/**
	 * Print custom footer code in wp_footer (front-end only).
	 */
	public static function footer_output(): void {
		if ( is_admin() ) {
			return;
		}

		$footer_code = (string) es_option( 'custom_footer_script' );
		if ( '' !== trim( $footer_code ) ) {
			echo $footer_code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- admin-only custom code.
		}
	}
}
