<?php
/**
 * Resource preload / preconnect hints.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Assets;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Preloads the Persian font everywhere and the LCP hero image on the front page.
 */
final class Preload implements ModuleInterface {

	/**
	 * {@inheritDoc}
	 */
	public function register(): void {
		add_action( 'wp_head', array( $this, 'print_hints' ), 1 );
	}

	/**
	 * Print the <link> hints. Never duplicates a hint.
	 */
	public function print_hints(): void {
		if ( is_admin() ) {
			return;
		}

		$hints = array();

		$font = 'assets/fonts/vazirmatn-arabic.woff2';
		if ( is_file( ES_THEME_DIR . '/' . $font ) ) {
			$preload_font_url = esc_url( ES_THEME_URI . '/' . $font );

			$hints[] = sprintf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin="anonymous">',
				$preload_font_url
			);
		}

		if ( is_front_page() ) {
			$hero = $this->hero_image();
			if ( $hero ) {
				$hints[] = sprintf(
					'<link rel="preload" href="%s" as="image" fetchpriority="high">',
					esc_url( $hero )
				);
			}
		}

		$hints = array_values( array_unique( $hints ) );

		if ( $hints ) {
			echo "\n" . implode( "\n", $hints ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		}
	}

	/**
	 * Resolve the hero (LCP) image URL: WebP when generated, JPEG otherwise.
	 */
	public function hero_image(): string {
		foreach ( array( 'assets/img/hero.webp', 'assets/img/hero.jpg' ) as $candidate ) {
			if ( is_file( ES_THEME_DIR . '/' . $candidate ) ) {
				return ES_THEME_URI . '/' . $candidate;
			}
		}

		return '';
	}
}
