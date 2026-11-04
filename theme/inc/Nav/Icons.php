<?php
/** Small, explicit SVG icon allow-list for menu item fields. */

namespace ErfanSanat\Theme\Nav;

defined( 'ABSPATH' ) || exit;

final class Icons {

	/** @return array<string,string> */
	public static function allowed(): array {
		return array(
			'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
			'cpu'          => '<rect x="4" y="4" width="16" height="16" rx="2"/><rect x="8" y="8" width="8" height="8" rx="1"/><path d="M9 2v2m6-2v2M9 20v2m6-2v2M2 9h2m-2 6h2m16-6h2m-2 6h2"/>',
			'sparkles'     => '<path d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z"/><path d="M19 14v6m3-3h-6"/>',
			'trees'        => '<path d="m5 12 3-6 3 6H5Zm8 3 4-8 4 8h-8ZM8 12v9m9-6v6"/>',
			'waves'        => '<path d="M2 7c2.5 0 2.5 2 5 2s2.5-2 5-2 2.5 2 5 2 2.5-2 5-2M2 12c2.5 0 2.5 2 5 2s2.5-2 5-2 2.5 2 5 2 2.5-2 5-2M2 17c2.5 0 2.5 2 5 2s2.5-2 5-2 2.5 2 5 2 2.5-2 5-2"/>',
			'lightbulb'    => '<path d="M9 18h6m-5 4h4m-5-7a6 6 0 1 1 6 0c-.8.6-1 1.3-1 3h-4c0-1.7-.2-2.4-1-3Z"/>',
			'layers'       => '<path d="m12 3 9 5-9 5-9-5 9-5Zm-9 9 9 5 9-5m-18 5 9 5 9-5"/>',
			'building'     => '<path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1m-6 4h1m4 0h1m-5 8v-4h2v4"/>',
			'map-pin'      => '<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
			'grid'         => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
		);
	}

	/** Render only registered SVG path data; never accepts arbitrary markup. */
	public static function svg( string $name, string $class = 'h-4 w-4' ): string {
		$icons = self::allowed();
		if ( ! isset( $icons[ $name ] ) ) { return ''; }
		$safe_class = function_exists( 'esc_attr' ) ? esc_attr( $class ) : htmlspecialchars( $class, ENT_QUOTES, 'UTF-8' );
		return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $safe_class . '" aria-hidden="true">' . $icons[ $name ] . '</svg>';
	}
}
