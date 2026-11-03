<?php
/** Task 2.4 — secure AJAX save endpoint with schema allow-list. */
declare(strict_types=1);

$bootstrap_candidates = array( __DIR__ . '/../../../../temp/tests/bootstrap.php', __DIR__ . '/../../tests/bootstrap.php' );
foreach ( $bootstrap_candidates as $bootstrap ) { if ( is_file( $bootstrap ) ) { require_once $bootstrap; break; } }
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_mock_options'] = $GLOBALS['es_mock_options'] ?? array();
$GLOBALS['es_option_update_calls'] = 0;
$GLOBALS['es_mock_user_can'] = true;
$GLOBALS['es_ajax_nonce_valid'] = true;
$GLOBALS['es_mock_transients'] = array();
$GLOBALS['es_mock_actions'] = $GLOBALS['es_mock_actions'] ?? array();
if ( ! function_exists( 'add_action' ) ) { function add_action( $name, $callback ) { $GLOBALS['es_mock_actions'][ $name ][] = $callback; } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $v ) { return is_scalar( $v ) ? trim( strip_tags( (string) $v ) ) : ''; } }
if ( ! function_exists( 'sanitize_email' ) ) { function sanitize_email( $v ) { return filter_var( (string) $v, FILTER_SANITIZE_EMAIL ) ?: ''; } }
if ( ! function_exists( 'sanitize_hex_color' ) ) { function sanitize_hex_color( $v ) { return preg_match( '/^#[a-f0-9]{3}(?:[a-f0-9]{3})?$/i', (string) $v ) ? $v : null; } }
if ( ! function_exists( 'absint' ) ) { function absint( $v ) { return abs( (int) $v ); } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $name, $default = false ) { return $GLOBALS['es_mock_options'][ $name ] ?? $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $name, $value, $autoload = null ) { $GLOBALS['es_option_update_calls']++; $changed = ( $GLOBALS['es_mock_options'][ $name ] ?? null ) !== $value; $GLOBALS['es_mock_options'][ $name ] = $value; return $changed; } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $cap ) { return ! empty( $GLOBALS['es_mock_user_can'] ) && 'manage_options' === $cap; } }
if ( ! function_exists( 'check_ajax_referer' ) ) { function check_ajax_referer( $action, $query_arg, $die = true ) { return ! empty( $GLOBALS['es_ajax_nonce_valid'] ); } }
if ( ! function_exists( 'wp_unslash' ) ) { function wp_unslash( $v ) { return is_array( $v ) ? array_map( 'wp_unslash', $v ) : stripslashes( (string) $v ); } }
if ( ! function_exists( 'get_current_user_id' ) ) { function get_current_user_id() { return 7; } }
if ( ! function_exists( 'get_transient' ) ) { function get_transient( $key ) { return $GLOBALS['es_mock_transients'][ $key ] ?? false; } }
if ( ! function_exists( 'set_transient' ) ) { function set_transient( $key, $value, $expiration ) { $GLOBALS['es_mock_transients'][ $key ] = $value; return true; } }
if ( ! class_exists( 'EsTestAjaxResponse' ) ) { class EsTestAjaxResponse extends RuntimeException { public array $payload; public int $status; public function __construct( array $payload, int $status ) { $this->payload = $payload; $this->status = $status; parent::__construct( 'AJAX response' ); } } }
if ( ! function_exists( 'wp_send_json_error' ) ) { function wp_send_json_error( $data = null, $status = 200 ) { throw new EsTestAjaxResponse( array( 'success' => false, 'data' => $data ), $status ); } }
if ( ! function_exists( 'wp_send_json_success' ) ) { function wp_send_json_success( $data = null, $status = 200 ) { throw new EsTestAjaxResponse( array( 'success' => true, 'data' => $data ), $status ); } }

$dependency_files = array( ES_REPO_ROOT . '/theme/inc/Helpers/Options.php', ES_REPO_ROOT . '/theme/inc/Admin/Fields/Schema.php', ES_REPO_ROOT . '/theme/inc/Admin/Fields/Sanitizer.php', ES_REPO_ROOT . '/theme/inc/Admin/AjaxSave.php' );
$available = ! in_array( false, array_map( 'is_file', $dependency_files ), true );
if ( $available ) {
	require_once ES_REPO_ROOT . '/theme/inc/Helpers/Defaults.php';
	require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
	foreach ( array_slice( $dependency_files, 1 ) as $dep_file ) { require_once $dep_file; }
	require_once $dependency_files[0];
}

function es_test_ajax_call( $post ) {
	$_POST = $post;
	try { \ErfanSanat\Theme\Admin\AjaxSave::handle(); } catch ( EsTestAjaxResponse $response ) { return $response; }
	return null;
}

echo "== Task 2.4: secure AJAX save ==\n";
check( 'AJAX save module and dependencies exist', fn() => $available );
if ( $available ) {
	$module = new \ErfanSanat\Theme\Admin\AjaxSave();
	$module->register();
	check( 'authenticated AJAX action is registered', fn() => count( $GLOBALS['es_mock_actions']['wp_ajax_es_save_options'] ?? array() ) === 1 );
	\ErfanSanat\Theme\Admin\Fields\Schema::reset();
	\ErfanSanat\Theme\Admin\Fields\Schema::register_many( array(
		array( 'id' => 'header.phone', 'type' => 'tel', 'label' => 'Phone', 'default' => '' ),
		array( 'id' => 'header.enabled', 'type' => 'toggle', 'label' => 'Enabled', 'default' => false ),
		array( 'id' => 'header.color', 'type' => 'color', 'label' => 'Color', 'default' => '#fff' ),
	) );
	\ErfanSanat\Theme\Helpers\Options::reset();
	$GLOBALS['es_option_update_calls'] = 0;
	$GLOBALS['es_ajax_nonce_valid'] = false;
	$bad_nonce = es_test_ajax_call( array( 'nonce' => 'bad', 'es_options' => array( 'header' => array( 'phone' => '999' ) ) ) );
	check( 'missing or invalid nonce returns 403 without writing', fn() => $bad_nonce instanceof EsTestAjaxResponse && $bad_nonce->status === 403 && $GLOBALS['es_option_update_calls'] === 0 );

	$GLOBALS['es_ajax_nonce_valid'] = true;
	$GLOBALS['es_mock_user_can'] = false;
	$denied = es_test_ajax_call( array( 'nonce' => 'valid', 'es_options' => array( 'header' => array( 'phone' => '999' ) ) ) );
	check( 'user without manage_options receives 403', fn() => $denied instanceof EsTestAjaxResponse && $denied->status === 403 && $GLOBALS['es_option_update_calls'] === 0 );
	$GLOBALS['es_mock_user_can'] = true;

	$es_updates_before = $GLOBALS['es_option_update_calls'];
	$ok = es_test_ajax_call( array( 'nonce' => 'valid', 'es_options' => array( 'header' => array( 'phone' => '۰۳۱-۱۲۳"><script>', 'enabled' => '1', 'color' => 'red', 'unknown' => 'drop' ), 'rogue' => array( 'payload' => 'xss' ) ) ) );
	check( 'valid request returns a standard JSON-ready success envelope', fn() => $ok instanceof EsTestAjaxResponse && $ok->status === 200 && $ok->payload['success'] === true && is_array( $ok->payload['data']['saved'] ?? null ) );
	check( 'unknown fields and groups are ignored while values are sanitized', function () {
		$stored = $GLOBALS['es_mock_options']['erfansanat_theme_options'] ?? array();
		return ($stored['header']['phone'] ?? null) === '۰۳۱-۱۲۳' && ($stored['header']['enabled'] ?? null) === true && ($stored['header']['color'] ?? null) === '#fff' && ! isset( $stored['rogue'] ) && ! isset( $stored['header']['unknown'] );
	} );
	check( 'multiple whitelisted changes produce only one update_option call', fn() => $GLOBALS['es_option_update_calls'] - $es_updates_before === 1 );
	check( 'response can be JSON encoded and decoded without loss', function () use ( $ok ) { $json = json_encode( $ok->payload ); $decoded = json_decode( $json, true ); return JSON_ERROR_NONE === json_last_error() && $decoded['success'] === true && count( $decoded['data']['saved'] ) === 3; } );

	$rate_key = 'es_options_rate_7';
	$GLOBALS['es_mock_transients'][ $rate_key ] = array( 'window' => time(), 'count' => 30 );
	$rate = es_test_ajax_call( array( 'nonce' => 'valid', 'es_options' => array( 'header' => array( 'phone' => '123' ) ) ) );
	check( 'rate limit returns 429 without persisting', fn() => $rate instanceof EsTestAjaxResponse && $rate->status === 429 && $GLOBALS['es_option_update_calls'] - $es_updates_before === 1 );
} else {
	for ( $i = 1; $i <= 7; $i++ ) { check( 'secure endpoint condition ' . $i, fn() => false ); }
}
summary();
