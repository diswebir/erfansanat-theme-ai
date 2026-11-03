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
