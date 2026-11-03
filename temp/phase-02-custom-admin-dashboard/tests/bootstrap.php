<?php
/**
 * Shared bootstrap for the Phase 2 acceptance contracts.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

$erfansanat_root = dirname( __DIR__, 3 );
$erfansanat_cache = getenv( 'ERFANSANAT_TOOL_CACHE' ) ?: getenv( 'HOME' ) . '/.cache/erfansanat-tools';
require_once $erfansanat_cache . '/phpunit-vendor/autoload.php';

$erfansanat_wp = getenv( 'ERFANSANAT_WP_ROOT' ) ?: $erfansanat_root . '/.local/wordpress';
if ( ! is_file( $erfansanat_wp . '/wp-load.php' ) ) {
	throw new RuntimeException( 'Local WordPress is not installed. Run tools/setup-wordpress.sh first.' );
}

$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? '127.0.0.1';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8080';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$_SERVER['PHP_SELF'] = $_SERVER['PHP_SELF'] ?? '/wp-admin/admin.php';
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['SCRIPT_FILENAME'] ?? $erfansanat_wp . '/wp-admin/admin.php';

require_once $erfansanat_wp . '/wp-load.php';

/*
 * The admin screens are loaded on demand by the contracts that need them; the
 * plugin bootstrap is not part of wp-load.php and calling admin functions
 * without it raises undefined-function errors.
 */
if ( ! function_exists( 'get_current_screen' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/*
 * PHPUnit cannot intercept wp_send_json_*(), which prints and calls wp_die().
 * These test doubles record the same envelope and stop execution the same way,
 * so the real endpoint logic runs unmodified inside the suite.
 */
if ( ! function_exists( 'erfansanat_test_json_capture' ) ) {
	/**
	 * Shared capture buffer for the JSON helpers below.
	 *
	 * @return object
	 */
	function erfansanat_test_json_capture() {
		static $capture = null;
		if ( null === $capture ) {
			$capture = (object) array(
				'response' => null,
				'status' => 200,
			);
		}
		return $capture;
	}

	/**
	 * Record the envelope and stop the request.
	 *
	 * @param array<string, mixed> $data    Envelope.
	 * @param int                  $status  HTTP status.
	 * @return void
	 */
	function erfansanat_test_send_json( $data, $status = 200 ) {
		$capture = erfansanat_test_json_capture();
		$capture->response = $data;
		$capture->status = (int) $status;
		throw new RuntimeException( 'json-response-sent' );
	}
}

unset( $erfansanat_root, $erfansanat_cache, $erfansanat_wp );
