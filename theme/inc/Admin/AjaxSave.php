<?php
/**
 * Secure AJAX endpoint for schema-backed theme settings.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Admin\Fields\Sanitizer;
use ErfanSanat\Theme\Admin\Fields\Schema;
use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Saves only registered settings after nonce, capability and rate checks.
 */
final class AjaxSave implements ModuleInterface {

	private const RATE_LIMIT = 30;
	private const RATE_WINDOW = 60;

	/** Register an authenticated-only admin AJAX action. */
	public function register(): void {
		add_action( 'wp_ajax_es_save_options', array( self::class, 'handle' ) );
	}

	/** Validate, sanitize and persist the submitted settings. */
	public static function handle(): void {
		if ( ! check_ajax_referer( 'es_options', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => 'درخواست معتبر نیست.' ), 403 );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'اجازه انجام این عملیات را ندارید.' ), 403 );
		}
		if ( self::rate_limited() ) {
			wp_send_json_error( array( 'message' => 'تعداد درخواست‌ها بیش از حد مجاز است.' ), 429 );
		}

		$payload = isset( $_POST['es_options'] ) && is_array( $_POST['es_options'] ) ? wp_unslash( $_POST['es_options'] ) : array();
		$values  = self::whitelisted_values( $payload );
		if ( ! $values ) {
			wp_send_json_error( array( 'message' => 'هیچ فیلد مجازی برای ذخیره ارسال نشد.' ), 400 );
		}

		Options::update_many( $values );
		$keys = array_keys( $values );
		if ( function_exists( 'do_action' ) ) {
			do_action( 'es_options_changed', $keys, get_current_user_id() );
		}
		wp_send_json_success( array( 'saved' => $keys ), 200 );
	}

	/**
	 * Extract only registered field paths and sanitize values with their schema.
	 *
	 * @param array<string,mixed> $payload Nested es_options request tree.
	 * @return array<string,mixed>
	 */
	public static function whitelisted_values( array $payload ): array {
		$values = array();
		foreach ( Schema::all() as $id => $field ) {
			$exists = false;
			$value  = self::read_path( $payload, explode( '.', $id ), $exists );
			if ( $exists ) {
				if ( ! empty( $field['reject_invalid_save'] ) && 'color' === ( $field['type'] ?? '' ) && ( ! is_scalar( $value ) || null === sanitize_hex_color( (string) $value ) ) ) {
					continue;
				}
				$values[ $id ] = Sanitizer::sanitize( $field, $value );
			}
		}
		return $values;
	}

	/** Enforce a per-user limit without creating or changing theme settings. */
	private static function rate_limited(): bool {
		if ( ! function_exists( 'get_transient' ) || ! function_exists( 'set_transient' ) ) {
			return false;
		}
		$user_id = function_exists( 'get_current_user_id' ) ? absint( get_current_user_id() ) : 0;
		$key     = 'es_options_rate_' . $user_id;
		$now     = time();
		$state   = get_transient( $key );
		if ( ! is_array( $state ) || ! isset( $state['window'], $state['count'] ) || $now - (int) $state['window'] >= self::RATE_WINDOW ) {
			$state = array( 'window' => $now, 'count' => 0 );
		}
		if ( (int) $state['count'] >= self::RATE_LIMIT ) {
			return true;
		}
		$state['count']++;
		set_transient( $key, $state, self::RATE_WINDOW );
		return false;
	}

	/** Read a nested payload without treating missing values as null submissions. */
	private static function read_path( array $payload, array $segments, bool &$exists ) {
		$value  = $payload;
		$exists = true;
		foreach ( $segments as $segment ) {
			if ( ! is_array( $value ) || ! array_key_exists( $segment, $value ) ) {
				$exists = false;
				return null;
			}
			$value = $value[ $segment ];
		}
		return $value;
	}
}
