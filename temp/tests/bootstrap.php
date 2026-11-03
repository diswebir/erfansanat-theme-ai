<?php
/**
 * Test bootstrap for the ErfanSanat theme harness.
 * Runs inside PHP (CLI / php-wasm). Provides a tiny assertion framework
 * plus a lazy WordPress bootstrap.
 *
 * STATUS codes: PASS | FAIL | PENDING (implementation not written yet)
 */
declare(strict_types=1);

if ( ! defined( 'ES_REPO_ROOT' ) ) {
	define( 'ES_REPO_ROOT', dirname( __DIR__, 2 ) );
}

require_once __DIR__ . '/lib/assert.php';
require_once __DIR__ . '/lib/wp.php';

\EsTest\banner( basename( $_SERVER['argv'][1] ?? 'test' ) );
