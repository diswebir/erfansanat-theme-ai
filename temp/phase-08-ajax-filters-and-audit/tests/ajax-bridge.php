<?php
/**
 * CLI bridge: run a real theme AJAX endpoint for JSDOM tests.
 * Usage: php ajax-bridge.php '<json {"action":"es_product_filters","request":{...}}>'
 *        php ajax-bridge.php '<json {"cleanup":{"posts":[ids],"terms":[[id,"tax"],...]}}>'
 * Prints the endpoint JSON envelope (status line first: HTTPSTATUS:<code>).
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';
if ( ! es_load_wp() ) { exit( 1 ); }
require_once ES_THEME . '/functions.php';
require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';
\ErfanSanat\Theme\Core\AttributeMap::register_missing();
if ( ! defined( 'DOING_AJAX' ) ) { define( 'DOING_AJAX', true ); }

$payload = json_decode( (string) ( $argv[1] ?? '{}' ), true );
if ( ! is_array( $payload ) ) { echo "HTTPSTATUS:400\n{}"; exit; }

if ( isset( $payload['cleanup'] ) ) {
	foreach ( (array) ( $payload['cleanup']['posts'] ?? array() ) as $pid ) { wp_delete_post( (int) $pid, true ); }
	foreach ( (array) ( $payload['cleanup']['terms'] ?? array() ) as $t ) { if ( is_array( $t ) ) { wp_delete_term( (int) $t[0], (string) $t[1] ); } }
	echo "HTTPSTATUS:200\n{\"cleaned\":true}";
	exit;
}

$action = (string) ( $payload['action'] ?? '' );
$_REQUEST = (array) ( $payload['request'] ?? array() );
wp_set_current_user( 0 );

$status = 200;
add_filter( 'status_header', function ( $header, $code ) use ( &$status ) { $status = (int) $code; return $header; }, 10, 2 );
$die = function () { return function () { throw new RuntimeException( 'wp_die' ); }; };
add_filter( 'wp_die_ajax_handler', $die );
add_filter( 'wp_die_handler', $die );

ob_start();
try {
	\ErfanSanat\Theme\Core\Ajax::dispatch( substr( $action, 3 ) ); // strip es_.
} catch ( Throwable $e ) {
	// Expected wp_die after JSON output.
}
$json = ob_get_clean();
echo 'HTTPSTATUS:' . $status . "\n" . $json;
