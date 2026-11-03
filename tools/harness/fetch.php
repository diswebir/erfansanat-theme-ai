<?php
/**
 * Render one front-end URL through the canonical WP flow and emit JSON.
 * Config comes from tools/harness/fetch.json (php-wasm has no argv/env).
 */
declare(strict_types=1);

$root = '/home/user/erfansanat-theme-ai';
$cfg  = json_decode( (string) file_get_contents( __DIR__ . '/fetch.json' ), true ) ?: array();
@unlink( __DIR__ . '/fetch.json' );

$path    = $cfg['path'] ?? '/';
$wantHtml = $cfg['html'] ?? true;

$warnings = array();
set_error_handler(
	static function ( int $errno, string $errstr, string $errfile = '', int $errline = 0 ) use ( &$warnings ): bool {
		if ( error_reporting() & $errno ) {
			$warnings[] = sprintf( '%s in %s:%d', $errstr, basename( $errfile ), $errline );
		}
		return true;
	}
);

$qs = str_contains( $path, '?' ) ? substr( $path, strpos( $path, '?' ) + 1 ) : '';
$_GET_RAW = array();
if ( $qs ) { parse_str( $qs, $_GET_RAW ); }
$_SERVER['REQUEST_URI']     = $path;
$_SERVER['HTTP_HOST']       = 'localhost:8080';
$_SERVER['SERVER_NAME']     = 'localhost';
$_SERVER['SERVER_PORT']     = '8080';
$_SERVER['REQUEST_METHOD']  = 'GET';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['SCRIPT_NAME']     = '/index.php';
$_SERVER['PHP_SELF']        = '/index.php';
$_SERVER['QUERY_STRING']    = $qs;
$_GET                       = $_GET_RAW;

define( 'WP_USE_THEMES', true );

$start = microtime( true );
ob_start();
try {
	require '/home/user/.cache/harness/wpn/wp-blog-header.php';
} catch ( Throwable $e ) {
	$warnings[] = 'EXCEPTION: ' . $e->getMessage() . ' @ ' . basename( $e->getFile() ) . ':' . $e->getLine();
}
$html    = (string) ob_get_clean();
$elapsed = ( microtime( true ) - $start ) * 1000;

global $wpdb, $template;

$timing = (array) ( $GLOBALS['es_timing'] ?? array() );
$theme_ms = 0.0;
if ( ! empty( $timing['template_redirect'] ) ) {
	$theme_ms = round( ( microtime( true ) - (float) $timing['template_redirect'] ) * 1000, 2 );
}

$payload = array(
	'path'         => $path,
	'theme_ms'     => $theme_ms,
	'bootstrap_ms' => round( $elapsed - $theme_ms, 2 ),
	'status'   => http_response_code() ?: 200,
	'template' => is_string( $template ) ? basename( $template ) : '',
	'bytes'    => strlen( $html ),
	'queries'  => (int) ( $wpdb->num_queries ?? 0 ),
	'time_ms'  => round( $elapsed, 2 ),
	'warnings' => array_values( array_unique( $warnings ) ),
	'html'     => $wantHtml ? $html : '',
);

echo "\n<<<ES_JSON>>>" . wp_json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "<<<END>>>";
