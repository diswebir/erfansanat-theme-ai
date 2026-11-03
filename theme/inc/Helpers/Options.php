<?php
/**
 * Single-option settings repository with request-local caching and dot paths.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Helpers {

defined( 'ABSPATH' ) || exit;

/**
 * Reads and updates the single theme settings option.
 */
final class Options {

	private const OPTION_NAME = 'erfansanat_theme_options';

	/** @var array<string,mixed>|null */
	private static ?array $stored = null;

	/** @var array<string,mixed>|null */
	private static ?array $merged = null;

	/** Register invalidation for integrations that update settings elsewhere. */
	public static function register(): void {
		if ( function_exists( 'add_action' ) ) {
			add_action( 'es_options_saved', array( self::class, 'reset' ) );
		}
	}

	/**
	 * Get the merged settings tree. The database option is read once per request.
	 *
	 * @return array<string,mixed>
	 */
	private static function all(): array {
		if ( null === self::$merged ) {
			$value       = get_option( self::OPTION_NAME, array() );
			self::$stored = is_array( $value ) ? $value : array();
			self::$merged = self::merge( Defaults::all(), self::$stored );
		}

		return self::$merged;
	}

	/**
	 * Read a value by dot-separated path.
	 *
	 * @param string $key     Settings path, e.g. `header.phone`.
	 * @param mixed  $default Optional fallback overriding the built-in fallback.
	 * @return mixed
	 */
	public static function get( string $key, $default = null ) {
		$segments = self::segments( $key );
		if ( ! $segments ) {
			return $default;
		}

		$value = self::all();
		foreach ( $segments as $segment ) {
			if ( ! is_array( $value ) || ! array_key_exists( $segment, $value ) ) {
				return $default;
			}
			$value = $value[ $segment ];
		}

		return $value;
	}

	/**
	 * Merge one path into the single stored option.
	 *
	 * @param string $key   Settings path.
	 * @param mixed  $value New value.
	 * @return bool Whether WordPress changed the database value.
	 */
	public static function update( string $key, $value ): bool {
		return self::update_many( array( $key => $value ) );
	}

	/**
	 * Update several dot paths in one database write.
	 *
	 * @param array<string,mixed> $values Values keyed by schema paths.
	 */
	public static function update_many( array $values ): bool {
		if ( ! $values ) {
			return false;
		}
		self::all();
		$updated = self::$stored ?? array();
		$saved   = array();

		foreach ( $values as $key => $value ) {
			if ( ! is_string( $key ) ) {
				continue;
			}
			$segments = self::segments( $key );
			if ( ! $segments ) {
				continue;
			}
			$cursor =& $updated;
			$last    = array_pop( $segments );
			foreach ( $segments as $segment ) {
				if ( ! isset( $cursor[ $segment ] ) || ! is_array( $cursor[ $segment ] ) ) {
					$cursor[ $segment ] = array();
				}
				$cursor =& $cursor[ $segment ];
			}
			$cursor[ $last ] = $value;
			unset( $cursor );
			$saved[] = $key;
		}

		if ( ! $saved ) {
			return false;
		}
		$result = update_option( self::OPTION_NAME, $updated, false );
		if ( function_exists( 'do_action' ) ) {
			do_action( 'es_options_saved', $updated, $saved );
		}
		self::$stored = $updated;
		self::$merged = self::merge( Defaults::all(), $updated );
		return (bool) $result;
	}

	/** Clear request-local cache; intended for the save hook and tests. */
	public static function reset(): void {
		self::$stored = null;
		self::$merged = null;
	}

	/** @return string[] */
	private static function segments( string $key ): array {
		$segments = explode( '.', trim( $key ) );
		foreach ( $segments as $segment ) {
			if ( '' === $segment || ! preg_match( '/^[a-zA-Z0-9_-]+$/', $segment ) ) {
				return array();
			}
		}
		return $segments;
	}

	/** Recursively overlay user values on top of static-site defaults. */
	private static function merge( array $defaults, array $overrides ): array {
		foreach ( $overrides as $key => $value ) {
			if ( is_array( $value ) && isset( $defaults[ $key ] ) && is_array( $defaults[ $key ] ) ) {
				$defaults[ $key ] = self::merge( $defaults[ $key ], $value );
			} else {
				$defaults[ $key ] = $value;
			}
		}
		return $defaults;
	}
}
}

namespace {
	if ( ! function_exists( 'es_option' ) ) {
		/** Read theme settings using a dot-separated key. */
		function es_option( string $key, $default = null ) {
			return \ErfanSanat\Theme\Helpers\Options::get( $key, $default );
		}
	}
	if ( ! function_exists( 'es_update_option' ) ) {
		/** Update a setting inside `erfansanat_theme_options`. */
		function es_update_option( string $key, $value ): bool {
			return \ErfanSanat\Theme\Helpers\Options::update( $key, $value );
		}
	}
	if ( ! function_exists( 'es_option_reset' ) ) {
		/** Clear the option repository's request-local cache. */
		function es_option_reset(): void {
			\ErfanSanat\Theme\Helpers\Options::reset();
		}
	}
}
