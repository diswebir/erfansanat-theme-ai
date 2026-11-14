<?php
/**
 * Shared anti-spam guard (P08-T018).
 *
 * One common mechanism for the three defenses every public form relies on:
 * honeypot, per-endpoint nonce verification and a per-IP+scope rate limit.
 * Forms and the Ajax registry delegate here instead of rolling their own.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Forms;

defined( 'ABSPATH' ) || exit;

final class SpamGuard {

	/** Max submissions per IP + scope inside one window (SC-01). */
	public const RATE_MAX = 10;

	/** Rate window in seconds. */
	public const RATE_WINDOW = 600;

	/** Transient key prefix (option_name: _transient_{PREFIX}{md5}). */
	private const KEY_PREFIX = 'es_rl_';

	/**
	 * AC-02: a filled honeypot field means a bot. Empty/missing is allowed.
	 *
	 * @param mixed $value Raw honeypot value.
	 */
	public static function honeypot_ok( $value ): bool {
		return '' === trim( (string) $value );
	}

	/**
	 * AC-02: shared nonce verification (Ajax registry routes through this).
	 */
	public static function nonce_ok( string $nonce, string $action ): bool {
		if ( '' === $nonce || '' === $action ) {
			return false;
		}
		return false !== wp_verify_nonce( $nonce, $action );
	}

	/** Best-effort client IP for rate limiting (CLI/tests fall back to 0.0.0.0). */
	public static function client_ip(): string {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['REMOTE_ADDR'] ) ) : '';
		return '' !== $ip ? $ip : '0.0.0.0';
	}

	private static function key( string $scope, string $ip ): string {
		return self::KEY_PREFIX . md5( $scope . '|' . $ip );
	}

	/**
	 * SC-01: refuse when this IP already consumed its budget for the scope.
	 *
	 * @param string   $scope  Form scope (contact|quote|recruitment|feedback).
	 * @param string   $ip     Client IP (defaults to current).
	 * @param int|null $max    Budget override (tests/admin).
	 * @param int|null $window Window override (tests/admin).
	 * @return true|WP_Error
	 */
	public static function enforce_rate( string $scope, ?string $ip = null, ?int $max = null, ?int $window = null ) {
		$scope = sanitize_key( $scope );
		$ip    = null === $ip ? self::client_ip() : $ip;
		$max   = $max ?? self::RATE_MAX;
		$count = get_transient( self::key( $scope, $ip ) );
		if ( false !== $count && (int) $count >= $max ) {
			return new \WP_Error(
				'es_rate_limited',
				'تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.',
				array( 'status' => 429 )
			);
		}
		return true;
	}

	/**
	 * Count one accepted submission against the IP + scope budget (SC-01).
	 * The window is fixed from the first hit, so bursts can never extend it.
	 */
	public static function hit( string $scope, ?string $ip = null, ?int $window = null ): void {
		$scope  = sanitize_key( $scope );
		$ip     = null === $ip ? self::client_ip() : $ip;
		$window = $window ?? self::RATE_WINDOW;
		$key    = self::key( $scope, $ip );
		$count  = get_transient( $key );
		$count  = false === $count ? 1 : ( (int) $count + 1 );
		set_transient( $key, $count, $window );
	}

	/**
	 * Clear every rate window (admin maintenance + QA harness teardown).
	 */
	public static function reset_all(): void {
		global $wpdb;
		$names = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				'%transient_' . self::KEY_PREFIX . '%',
				'%transient_timeout_' . self::KEY_PREFIX . '%'
			)
		);
		foreach ( (array) $names as $name ) { // delete_transient clears cache + timeout together.
			$bare = preg_replace( '/^(_transient_timeout_|_transient_)/', '', (string) $name );
			if ( '' !== $bare ) {
				delete_transient( $bare );
			}
		}
	}
}
