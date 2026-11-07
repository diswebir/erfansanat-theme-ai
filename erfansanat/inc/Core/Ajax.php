<?php
/**
 * Shared AJAX contract (P08-T001).
 *
 * One registry for all front/admin theme endpoints: every action gets a
 * unique `es_*` hook name and its own nonce action (`es_ajax_*`), a public or
 * capability policy, a typed input whitelist, and a standard JSON envelope
 * (`wp_send_json_success` / `wp_send_json_error` with {code,message}).
 * Exceptions never leak internal details or stack traces (AC-03).
 *
 * Usage (P08-T002+ engines):
 *   Ajax::register( 'product_search', $callback, array(
 *       'public' => true,
 *       'inputs' => array( 'term' => array( 'type' => 'text', 'default' => '' ) ),
 *   ) );
 * The callback receives ONLY whitelisted, sanitized inputs and returns an
 * array (success data) or WP_Error (structured failure).
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class Ajax {

	private static bool $booted = false;

	/** @var array<string,array{callback:callable,args:array<string,mixed>}> */
	private static array $registry = array();

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inline_config' ), 30 );
	}

	/**
	 * Register a unique endpoint.
	 *
	 * @param string               $action   Snake_case name without the es_ prefix.
	 * @param callable             $callback fn(array $input): array|\WP_Error.
	 * @param array<string,mixed>  $args     public(bool)|capability(string)|inputs(map).
	 * @return bool True on success; false for duplicate/invalid names or callbacks.
	 */
	public static function register( string $action, callable $callback, array $args = array() ): bool {
		if ( ! preg_match( '/^[a-z0-9_]{1,64}$/', $action ) || isset( self::$registry[ $action ] ) ) {
			return false;
		}
		self::$registry[ $action ] = array( 'callback' => $callback, 'args' => $args );

		$hook = static function () use ( $action ) {
			self::dispatch( $action );
		};
		add_action( 'wp_ajax_es_' . $action, $hook );
		if ( ! empty( $args['public'] ) ) {
			add_action( 'wp_ajax_nopriv_es_' . $action, $hook );
		}
		return true;
	}

	/** Unique nonce action per endpoint (AC-01). */
	public static function nonce_action( string $action ): string {
		return 'es_ajax_' . $action;
	}

	/**
	 * Run one endpoint: nonce → policy → whitelist → callback → JSON envelope.
	 * Public so tests and hooks share the exact same path.
	 */
	public static function dispatch( string $action ): void {
		if ( ! isset( self::$registry[ $action ] ) ) {
			self::fail( 'unknown_action', __( 'درخواست ناشناخته است.', 'erfansanat' ), 400 );
		}
		$entry = self::$registry[ $action ];
		$args  = $entry['args'];

		// AC-01 / SC-01: every endpoint verifies its own nonce.
		$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, self::nonce_action( $action ) ) ) {
			self::fail( 'invalid_nonce', __( 'درخواست نامعتبر است (nonce).', 'erfansanat' ), 403 );
		}

		// Capability policy: private endpoints require an explicit capability.
		if ( empty( $args['public'] ) ) {
			$cap = (string) ( $args['capability'] ?? 'manage_options' );
			if ( ! current_user_can( $cap ) ) {
				self::fail( 'forbidden', __( 'شما دسترسی انجام این درخواست را ندارید.', 'erfansanat' ), 403 );
			}
		}

		$input = self::collect( (array) ( $args['inputs'] ?? array() ) );

		try {
			$result = call_user_func( $entry['callback'], $input );
		} catch ( \Throwable $e ) {
			// AC-03: structured 500, never the exception message/trace.
			self::fail( 'internal_error', __( 'خطای داخلی رخ داد. دوباره تلاش کنید.', 'erfansanat' ), 500 );
			return;
		}

		if ( is_wp_error( $result ) ) {
			$data   = $result->get_error_data();
			$status = is_array( $data ) && isset( $data['status'] ) ? (int) $data['status'] : 400;
			self::fail( (string) $result->get_error_code(), $result->get_error_message(), $status );
		}

		wp_send_json_success( is_array( $result ) ? $result : array() );
	}

	/**
	 * Whitelist + sanitize request inputs (AC-02 / SC-02). Only declared keys
	 * pass; every type has a strict normalizer; enums fall back to default.
	 *
	 * @param array<string,array<string,mixed>> $schema Input definitions.
	 * @return array<string,mixed>
	 */
	public static function collect( array $schema ): array {
		$out = array();
		foreach ( $schema as $key => $def ) {
			$type    = (string) ( $def['type'] ?? 'text' );
			$default = $def['default'] ?? '';
			$raw     = isset( $_REQUEST[ $key ] ) ? wp_unslash( $_REQUEST[ $key ] ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- normalized per type below.
			if ( null === $raw ) {
				$out[ $key ] = $default;
				continue;
			}
			switch ( $type ) {
				case 'int':
					$value = is_scalar( $raw ) ? (int) $raw : (int) $default;
					if ( isset( $def['min'] ) ) { $value = max( (int) $def['min'], $value ); }
					if ( isset( $def['max'] ) ) { $value = min( (int) $def['max'], $value ); }
					$out[ $key ] = $value;
					break;
				case 'float':
					$out[ $key ] = is_scalar( $raw ) ? (float) $raw : (float) $default;
					break;
				case 'bool':
					$out[ $key ] = (bool) rest_sanitize_boolean( is_scalar( $raw ) ? $raw : false );
					break;
				case 'enum':
					$choices     = (array) ( $def['choices'] ?? array() );
					$value       = is_scalar( $raw ) ? (string) $raw : '';
					$out[ $key ] = in_array( $value, $choices, true ) ? $value : $default;
					break;
				case 'key':
					$out[ $key ] = is_scalar( $raw ) ? sanitize_key( (string) $raw ) : '';
					break;
				case 'key_list':
					$items       = is_array( $raw ) ? $raw : explode( ',', (string) ( is_scalar( $raw ) ? $raw : '' ) );
					$items       = array_values( array_filter( array_map( 'sanitize_key', array_map( 'strval', $items ) ) ) );
					$out[ $key ] = array_slice( $items, 0, (int) ( $def['max'] ?? 50 ) );
					break;
				case 'int_list':
					$items       = is_array( $raw ) ? $raw : explode( ',', (string) ( is_scalar( $raw ) ? $raw : '' ) );
					$items       = array_values( array_filter( array_map( 'intval', $items ), static function ( $i ) { return $i > 0; } ) );
					$out[ $key ] = array_slice( $items, 0, (int) ( $def['max'] ?? 50 ) );
					break;
				case 'textarea':
					$out[ $key ] = sanitize_textarea_field( (string) ( is_scalar( $raw ) ? $raw : '' ) );
					break;
				case 'email':
					$value       = sanitize_email( (string) ( is_scalar( $raw ) ? $raw : '' ) );
					$out[ $key ] = is_email( $value ) ? $value : '';
					break;
				case 'url':
					$out[ $key ] = esc_url_raw( (string) ( is_scalar( $raw ) ? $raw : '' ) );
					break;
				case 'text':
				default:
					$out[ $key ] = sanitize_text_field( (string) ( is_scalar( $raw ) ? $raw : '' ) );
					break;
			}
		}
		return $out;
	}

	/** Structured error envelope (AC-03): {success:false,data:{code,message}}. */
	private static function fail( string $code, string $message, int $status ): void {
		wp_send_json_error( array( 'code' => $code, 'message' => $message ), $status );
	}

	/**
	 * Front-end config: endpoint URL + a nonce per registered action.
	 *
	 * @return array{url:string,nonces:array<string,string>}
	 */
	public static function config(): array {
		$nonces = array();
		foreach ( array_keys( self::$registry ) as $action ) {
			$nonces[ $action ] = wp_create_nonce( self::nonce_action( $action ) );
		}
		return array(
			'url'    => admin_url( 'admin-ajax.php' ),
			'nonces' => $nonces,
		);
	}

	/** Attach the config to the theme script (same pattern as CartFragments). */
	public static function inline_config(): void {
		if ( ! wp_script_is( AssetManager::SCRIPT_HANDLE, 'enqueued' ) || ! self::$registry ) {
			return;
		}
		wp_add_inline_script(
			AssetManager::SCRIPT_HANDLE,
			'window.ES_AJAX = ' . wp_json_encode( self::config() ) . ';',
			'before'
		);
	}
}
