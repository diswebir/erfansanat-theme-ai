<?php
/**
 * Template-facing helpers for theme options.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Read one theme option with the schema default as fallback.
 *
 * @param string $key      Field name.
 * @param mixed  $fallback Optional fallback for unknown keys.
 * @return mixed
 */
function es_option( string $key, $fallback = null ) {
	return Settings::get( $key, $fallback );
}

/**
 * Escaped text option.
 *
 * @param string $key Field name.
 * @return string
 */
function es_text( string $key ): string {
	$value = Settings::get( $key, '' );
	return is_scalar( $value ) ? esc_html( (string) $value ) : '';
}

/**
 * Escaped URL option.
 *
 * @param string $key Field name.
 * @return string
 */
function es_url( string $key ): string {
	$value = Settings::get( $key, '' );
	return is_scalar( $value ) ? esc_url( (string) $value ) : '';
}

/**
 * Whether a boolean option is on.
 *
 * @param string $key Field name.
 * @return bool
 */
function es_enabled( string $key ): bool {
	return (bool) Settings::get( $key, false );
}

/**
 * Public URL for a theme asset. Thin wrapper so templates never build paths.
 *
 * @param string $relative Path inside assets/.
 * @return string
 */
function es_asset( string $relative ): string {
	return AssetManager::uri( $relative );
}

/**
 * Archive URL for a term, or an empty string when the term has no usable link.
 *
 * `get_term_link()` returns a `WP_Error` for a term whose taxonomy is not
 * registered on this request (or a term that vanished between the query and the
 * template), and casting that object to a string is a fatal error. Every place
 * the theme links to a term goes through here so a single bad term degrades to
 * "no link" instead of a white screen.
 *
 * @param \WP_Term|null $term Term to link, if any.
 * @return string
 */
function term_url( $term ): string {
	if ( ! $term instanceof \WP_Term ) {
		return '';
	}
	$link = get_term_link( $term );
	return is_string( $link ) ? $link : '';
}
