<?php
/**
 * Frontend renderer for registered, enabled sections.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Sections;

defined( 'ABSPATH' ) || exit;

/** Resolves templates safely and passes schema-sanitized data as template args. */
final class Renderer {

	/**
	 * Render enabled sections in the order stored for the selected page.
	 *
	 * @param string   $page    Registry page key.
	 * @param int|null $post_id Source page ID; inferred from the current request when omitted.
	 */
	public static function render( string $page = 'home', ?int $post_id = null ): void {
		if ( ! function_exists( 'locate_template' ) || ! function_exists( 'get_template_part' ) ) {
			return;
		}
		if ( null === $post_id ) {
			if ( 'home' === $page && function_exists( 'get_option' ) ) {
				$post_id = absint( get_option( 'page_on_front', 0 ) );
			} elseif ( function_exists( 'get_queried_object_id' ) ) {
				$post_id = absint( get_queried_object_id() );
			} else {
				$post_id = 0;
			}
		}

		$sections = Registry::get( $page );
		if ( ! $sections ) {
			return;
		}
		$state = Store::get( max( 0, $post_id ), $page );
		foreach ( $state['order'] as $key ) {
			if ( ! isset( $sections[ $key ] ) || empty( $state['enabled'][ $key ] ) ) {
				continue;
			}
			$template = $sections[ $key ]['template'];
			$located = locate_template( array( $template ), false, false );
			if ( ! is_string( $located ) || '' === $located ) {
				continue;
			}

			$slug = substr( $template, 0, -4 );
			$data = is_array( $state['data'][ $key ] ?? null ) ? $state['data'][ $key ] : array();
			$args = array(
				'section' => $key,
				'page'    => $page,
				'post_id' => max( 0, $post_id ),
				'data'    => $data,
			);
			$buffer_level = ob_get_level();
			ob_start();
			try {
				if ( function_exists( 'do_action' ) ) {
					do_action( 'es_before_section', $key, $data, $page );
				}
				get_template_part( $slug, null, $args );
				if ( function_exists( 'do_action' ) ) {
					do_action( 'es_after_section', $key, $data, $page );
				}
				$output = (string) ob_get_clean();
				echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template output is authored PHP markup.
			} catch ( \Throwable $error ) {
				while ( ob_get_level() > $buffer_level ) {
					ob_end_clean();
				}
				if ( function_exists( 'do_action' ) ) {
					do_action( 'es_section_render_error', $key, $error, $page );
				}
			}
		}
	}
}
