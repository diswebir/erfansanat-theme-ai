<?php
/**
 * Shared bootstrap for the Phase 1 acceptance contracts.
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

/*
 * The contracts render templates in-process, so they need the request values the
 * PHP-Wasm development server would normally provide. Without them core raises
 * "Undefined array key" warnings that have nothing to do with the theme.
 */
$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? '127.0.0.1';
$_SERVER['HTTP_HOST']   = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8080';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

require_once $erfansanat_wp . '/wp-load.php';

unset( $erfansanat_root, $erfansanat_cache, $erfansanat_wp );
