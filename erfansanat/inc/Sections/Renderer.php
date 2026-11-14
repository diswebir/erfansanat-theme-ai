<?php
/**
 * Section renderer: ordered, enabled-only output with a lightweight per-page
 * cache and a safe fallback when a template part has not been sliced yet.
 *
 * Template parts live at template-parts/sections/{context}/section-{type}.php
 * and are sliced from the static mirror in Phases 5/7. Until then the renderer
 * emits a minimal, escaped structural fallback so ordering/enable logic is
 * fully functional and never fatals on a missing part.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Sections;

defined( 'ABSPATH' ) || exit;

final class Renderer {

	private static bool $booted = false;

	/**
	 * Rendered HTML cache keyed by post id then context.
	 *
	 * @var array<int,array<string,string>>
	 */
	private static array $cache = array();

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		// AC-03: invalidate the cache whenever a page's sections are saved.
		add_action( 'erfansanat_sections_updated', array( __CLASS__, 'invalidate' ), 10, 2 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Drop the cached output for a page (all contexts).
	 *
	 * @param int   $post_id  Page id.
	 * @param mixed $sections Saved sections (unused, part of the action contract).
	 */
	public static function invalidate( $post_id, $sections = array() ): void {
		unset( self::$cache[ (int) $post_id ] );
	}

	/**
	 * Render a page's sections in order, skipping disabled ones.
	 */
	public static function render( int $post_id, string $context = '' ): string {
		$key = $context;
		if ( isset( self::$cache[ $post_id ][ $key ] ) ) {
			return self::$cache[ $post_id ][ $key ];
		}

		$sections = SectionManager::get_sections( $post_id, $context );
		$html     = '';

		foreach ( $sections as $section ) {
			if ( empty( $section['enabled'] ) ) {
				continue; // AC-01: disabled sections are not rendered.
			}
			$html .= self::render_section( $section, $context );
		}

		self::$cache[ $post_id ][ $key ] = $html;

		return $html;
	}

	/**
	 * Render one section instance via its template part, or the fallback.
	 *
	 * @param array<string,mixed> $section Normalized section instance.
	 */
	public static function render_section( array $section, string $context ): string {
		$type = (string) ( $section['type'] ?? '' );
		$def  = Schema::get_section( $context, $type );

		if ( null === $def ) {
			return ''; // SC-02: unknown section types are ignored.
		}

		$relative = 'template-parts/sections/' . $def['template'] . '.php';
		$located  = locate_template( $relative );

		if ( $located ) {
			$es_section = $section;
			$es_fields  = isset( $section['fields'] ) && is_array( $section['fields'] ) ? $section['fields'] : array();

			ob_start();
			include $located;
			$output = ob_get_clean();

			return false === $output ? '' : $output;
		}

		// AC-02: a missing template part never fatals; use the safe fallback.
		return self::fallback( $section, $def );
	}

	/**
	 * Minimal escaped structural fallback used until the real template part is
	 * sliced. Preserves the section identity (id/type) and its heading.
	 *
	 * @param array<string,mixed> $section Section instance.
	 * @param array<string,mixed> $def     Section schema definition.
	 */
	private static function fallback( array $section, array $def ): string {
		$id      = (string) ( $section['id'] ?? '' );
		$type    = (string) ( $section['type'] ?? '' );
		$heading = isset( $section['fields']['heading'] ) ? (string) $section['fields']['heading'] : '';

		$html  = '<section class="es-section es-section--' . esc_attr( $type ) . '"';
		$html .= ' data-es-section="' . esc_attr( $id ) . '"';
		$html .= ' data-es-section-type="' . esc_attr( $type ) . '">';
		if ( '' !== $heading ) {
			$html .= '<h2 class="es-section__heading">' . esc_html( $heading ) . '</h2>';
		}
		$html .= '</section>';

		return $html;
	}
}
