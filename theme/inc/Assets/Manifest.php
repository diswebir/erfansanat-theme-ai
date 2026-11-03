<?php
/**
 * Declarative asset manifest: "which asset for which screen".
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Assets;

defined( 'ABSPATH' ) || exit;

/**
 * Asset definitions. Every entry is resolved by AssetManager.
 */
final class Manifest {

	/**
	 * Stylesheet entries.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function styles(): array {
		return array(
			'es-main' => array(
				'file'      => 'assets/css/styles.css',
				'deps'      => array(),
				'condition' => static fn(): bool => true, // the static mirror ships one CSS file for every page.
				'priority'  => 10,
			),
		);
	}

	/**
	 * Script entries.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function scripts(): array {
		return array(
			'es-main' => array(
				'file'      => 'assets/js/main.js',
				'deps'      => array(),
				'condition' => static fn(): bool => true,
				'in_footer' => false, // the static markup loads it in <head> with defer.
				'defer'     => true,
				'priority'  => 10,
			),
		);
	}

	/**
	 * Fonts that are referenced from CSS (kept for preload + integrity tests).
	 *
	 * @return string[]
	 */
	public static function fonts(): array {
		return array(
			'assets/fonts/vazirmatn-arabic.woff2',
			'assets/fonts/vazirmatn-latin.woff2',
			'assets/fonts/vazirmatn-latin-ext.woff2',
		);
	}
}
