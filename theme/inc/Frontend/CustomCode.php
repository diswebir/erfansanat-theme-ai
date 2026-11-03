<?php
/** Conditional frontend output for administrator-supplied script snippets. */

namespace ErfanSanat\Theme\Frontend;

use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\CustomCodeSanitizer;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

/** Adds sanitized snippets at the requested WordPress lifecycle locations. */
final class CustomCode implements ModuleInterface {

	/** Register only standard head/footer output hooks. */
	public function register(): void {
		add_action( 'wp_head', array( self::class, 'output_head' ), 99 );
		add_action( 'wp_footer', array( self::class, 'output_footer' ), 99 );
	}

	/** Return safe head markup on public requests, otherwise nothing. */
	public static function head_markup(): string {
		return self::markup( 'scripts.head' );
	}

	/** Return safe footer markup on public requests, otherwise nothing. */
	public static function footer_markup(): string {
		return self::markup( 'scripts.footer' );
	}

	/** Echo the head snippet after normal head content. */
	public static function output_head(): void {
		echo self::head_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Limited KSES allow-list retains only approved script markup.
	}

	/** Echo the footer snippet before the closing body tag. */
	public static function output_footer(): void {
		echo self::footer_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Limited KSES allow-list retains only approved script markup.
	}

	/** Read and re-sanitize stored data at output to fail closed on legacy values. */
	private static function markup( string $key ): string {
		if ( function_exists( 'is_admin' ) && is_admin() ) {
			return '';
		}
		$value = Options::get( $key, '' );
		return is_string( $value ) ? CustomCodeSanitizer::sanitize( $value ) : '';
	}
}
