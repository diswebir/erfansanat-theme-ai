<?php
/**
 * Atomic CRUD over the single theme option erfansanat_theme_options.
 *
 * - Only schema-known keys are persisted (unknown keys are rejected).
 * - A corrupted/non-array option falls back to safe defaults.
 * - Every write bumps schema_version and fires erfansanat_options_updated
 *   so caches (es_option, section renderer) can invalidate.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options;

defined( 'ABSPATH' ) || exit;

final class Store {

	public const OPTION = 'erfansanat_theme_options';

	/**
	 * Raw stored values, validated to be an array. Unknown keys stripped.
	 *
	 * @return array<string,mixed>
	 */
	public static function raw(): array {
		$stored = get_option( self::OPTION, array() );

		if ( ! is_array( $stored ) ) {
			return array();
		}

		$clean = array();
		foreach ( $stored as $key => $value ) {
			if ( 'schema_version' === $key || Schema::is_known( (string) $key ) ) {
				$clean[ $key ] = $value;
			}
		}

		return $clean;
	}

	/**
	 * Full option merged over defaults (always safe to read).
	 *
	 * @return array<string,mixed>
	 */
	public static function all(): array {
		$raw      = self::raw();
		$defaults = Schema::defaults();
		$merged   = array_merge( $defaults, array_intersect_key( $raw, $defaults ) );

		$merged['schema_version'] = Schema::version();

		return $merged;
	}

	/**
	 * Read one key. Unknown keys return the caller-provided default.
	 *
	 * @param mixed $default Caller fallback when key unknown.
	 * @return mixed
	 */
	public static function get( string $key, $default = null ) {
		if ( ! Schema::is_known( $key ) ) {
			return $default;
		}

		$raw = self::raw();

		if ( array_key_exists( $key, $raw ) ) {
			return $raw[ $key ];
		}

		$schema_default = Schema::default( $key );

		return null === $schema_default ? $default : $schema_default;
	}

	/**
	 * Persist one known key atomically. Returns false for unknown keys.
	 *
	 * @param mixed $value Raw value (sanitization happens in Sanitizer layer).
	 */
	public static function set( string $key, $value ): bool {
		if ( ! Schema::is_known( $key ) ) {
			return false;
		}

		return self::write( array( $key => $value ) );
	}

	/**
	 * Merge several known keys atomically. Unknown keys are ignored.
	 *
	 * @param array<string,mixed> $values Candidate values.
	 * @return bool True when a write occurred.
	 */
	public static function update( array $values ): bool {
		$known = array();
		foreach ( $values as $key => $value ) {
			if ( Schema::is_known( (string) $key ) ) {
				$known[ $key ] = $value;
			}
		}

		if ( empty( $known ) ) {
			return false;
		}

		return self::write( $known );
	}

	/**
	 * Remove a key so reads fall back to its schema default.
	 */
	public static function delete( string $key ): bool {
		if ( ! Schema::is_known( $key ) ) {
			return false;
		}

		$raw = self::raw();
		if ( ! array_key_exists( $key, $raw ) ) {
			return true;
		}

		unset( $raw[ $key ] );

		return self::persist( $raw );
	}

	/**
	 * Delete the whole option (back to pure defaults).
	 */
	public static function reset(): bool {
		delete_option( self::OPTION );
		do_action( 'erfansanat_options_updated', array(), 'reset' );
		return true;
	}

	/**
	 * Merge candidate values over raw storage and persist.
	 *
	 * @param array<string,mixed> $values Known values to merge.
	 */
	private static function write( array $values ): bool {
		$raw    = self::raw();
		$merged = array_merge( $raw, $values );

		return self::persist( $merged );
	}

	/**
	 * Persist the array with schema_version and fire invalidation.
	 *
	 * @param array<string,mixed> $raw Values to store.
	 */
	private static function persist( array $raw ): bool {
		$raw['schema_version'] = Schema::version();

		// autoload=false keeps the option out of the alloptions cache payload.
		$result = update_option( self::OPTION, $raw, false );

		do_action( 'erfansanat_options_updated', $raw, 'save' );

		return (bool) $result;
	}
}
