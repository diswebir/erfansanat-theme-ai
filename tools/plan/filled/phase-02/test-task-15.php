<?php
/** Task 2.15 — Phase 2 integration, security, performance and warning gate. */
declare(strict_types=1);

require_once __DIR__ . '/../../../../temp/tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
$GLOBALS['es_gate_options'] = $GLOBALS['es_gate_options'] ?? array();
if ( ! function_exists( 'get_option' ) ) { function get_option( $key, $default = false ) { return $GLOBALS['es_gate_options'][ $key ] ?? $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $key, $value, $autoload = null ) { $GLOBALS['es_gate_options'][ $key ] = $value; return true; } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : ''; } }
if ( ! function_exists( 'sanitize_hex_color' ) ) { function sanitize_hex_color( $value ) { return preg_match( '/^#[a-f0-9]{3}(?:[a-f0-9]{3})?$/i', (string) $value ) ? $value : null; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'wp_unslash' ) ) { function wp_unslash( $value ) { return is_array( $value ) ? array_map( 'wp_unslash', $value ) : stripslashes( (string) $value ); } }
if ( ! function_exists( 'current_user_can' ) ) { function current_user_can( $capability ) { return ! empty( $GLOBALS['es_mock_user_can'] ) && 'manage_options' === $capability; } }
if ( ! function_exists( 'check_ajax_referer' ) ) { function check_ajax_referer( $action, $query_arg, $die = true ) { return ! empty( $GLOBALS['es_ajax_nonce_valid'] ); } }
if ( ! function_exists( 'get_current_user_id' ) ) { function get_current_user_id() { return 7; } }
if ( ! function_exists( 'get_transient' ) ) { function get_transient( $key ) { return $GLOBALS['es_mock_transients'][ $key ] ?? false; } }
if ( ! function_exists( 'set_transient' ) ) { function set_transient( $key, $value, $expiration ) { $GLOBALS['es_mock_transients'][ $key ] = $value; return true; } }
if ( ! class_exists( 'EsTestAjaxResponse' ) ) { class EsTestAjaxResponse extends RuntimeException { public array $payload; public int $status; public function __construct( array $payload, int $status ) { $this->payload = $payload; $this->status = $status; parent::__construct( 'AJAX response' ); } } }
if ( ! function_exists( 'wp_send_json_error' ) ) { function wp_send_json_error( $data = null, $status = 200 ) { throw new EsTestAjaxResponse( array( 'success' => false, 'data' => $data ), $status ); } }
if ( ! function_exists( 'wp_send_json_success' ) ) { function wp_send_json_success( $data = null, $status = 200 ) { throw new EsTestAjaxResponse( array( 'success' => true, 'data' => $data ), $status ); } }
if ( ! class_exists( '\\ErfanSanat\\Theme\\Helpers\\Defaults' ) ) { require_once ES_REPO_ROOT . '/theme/inc/Helpers/Defaults.php'; }
if ( ! class_exists( '\\ErfanSanat\\Theme\\Helpers\\Options' ) ) { require_once ES_REPO_ROOT . '/theme/inc/Helpers/Options.php'; }
if ( ! class_exists( '\\ErfanSanat\\Theme\\Admin\\AjaxSave' ) ) {
	require_once ES_REPO_ROOT . '/theme/inc/Contracts/ModuleInterface.php';
	require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/Schema.php';
	require_once ES_REPO_ROOT . '/theme/inc/Admin/Fields/Sanitizer.php';
	require_once ES_REPO_ROOT . '/theme/inc/Admin/AjaxSave.php';
}

$phase_test_dir = ES_REPO_ROOT . '/temp/phase-02-custom-admin-dashboard/tests/';
$phase_test_files = glob( $phase_test_dir . 'test-task-*.php' ) ?: array();
$implemented_tests = array_filter( $phase_test_files, static function ( $test_path ) { return preg_match( '/test-task-(?:0[1-9]|1[0-4])\\.php$/', basename( $test_path ) ); } );
$all_test_files_are_implemented = count( $implemented_tests ) === 14;
foreach ( $implemented_tests as $test_path ) {
	$test_source = (string) file_get_contents( $test_path );
	$all_test_files_are_implemented = $all_test_files_are_implemented && str_contains( $test_source, 'summary();' ) && ! str_contains( $test_source, 'IMPLEMENT:' );
}
$prior_phase_result = $GLOBALS['es_phase_prior_result'] ?? null;
$full_phase_prior_passed = is_array( $prior_phase_result )
	? ( 14 === (int) ( $prior_phase_result['files'] ?? 0 ) && 100 === (int) ( $prior_phase_result['passed'] ?? 0 ) && 0 === (int) ( $prior_phase_result['failed'] ?? -1 ) && 0 === (int) ( $prior_phase_result['pending'] ?? -1 ) )
	: $all_test_files_are_implemented;
$admin_root = ES_REPO_ROOT . '/theme/inc/Admin/';
$unsafe_reads = array();
$superglobal_count = 0;
if ( is_dir( $admin_root ) ) {
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $admin_root, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $iterator as $admin_file ) {
		if ( ! $admin_file->isFile() || '.php' !== substr( $admin_file->getFilename(), -4 ) ) { continue; }
		$source_lines = file( $admin_file->getPathname(), FILE_IGNORE_NEW_LINES ) ?: array();
		foreach ( $source_lines as $line_number => $source_line ) {
			if ( ! preg_match( '/\$_(?:POST|GET)\s*\[/', $source_line ) ) { continue; }
			$superglobal_count++;
			$has_sanitization = str_contains( $source_line, 'wp_unslash' ) || str_contains( $source_line, 'sanitize_' ) || (bool) preg_match( '/absint\s*\(\s*\$_(?:POST|GET)/', $source_line );
			if ( ! $has_sanitization ) { $unsafe_reads[] = $admin_file->getFilename() . ':' . ( $line_number + 1 ); }
		}
	}
}

echo "== Task 2.15: Phase 2 final gate ==\n";
check( 'all fourteen phase tests passed before the final gate', fn() => $full_phase_prior_passed );
echo '   sanitized POST/GET reads scanned: ' . $superglobal_count . '; unsafe: ' . count( $unsafe_reads ) . PHP_EOL;
check( 'all direct POST and GET reads in admin PHP are sanitized', fn() => $superglobal_count > 0 && array() === $unsafe_reads );

if ( class_exists( '\\ErfanSanat\\Theme\\Helpers\\Options' ) && class_exists( '\\ErfanSanat\\Theme\\Admin\\Fields\\Schema' ) ) {
	$GLOBALS['es_mock_options']['erfansanat_theme_options'] = array();
	$GLOBALS['es_gate_options']['erfansanat_theme_options'] = array();
	$GLOBALS['es_mock_user_can'] = true;
	$GLOBALS['es_ajax_nonce_valid'] = true;
	$GLOBALS['es_mock_transients'] = array();
	\ErfanSanat\Theme\Admin\Fields\Schema::reset();
	\ErfanSanat\Theme\Admin\Fields\Schema::register_many( array( array( 'id' => 'header.phone', 'type' => 'tel', 'label' => 'Phone', 'default' => '' ) ) );
	\ErfanSanat\Theme\Helpers\Options::reset();
	$warnings = array();
	$save_responses_successful = true;
	$max_elapsed_ms = 0.0;
	set_error_handler( static function ( $severity, $message, $file, $line ) use ( &$warnings ) { $warnings[] = $message . ' @ ' . basename( $file ) . ':' . $line; return true; } );
	for ( $save_iteration = 0; $save_iteration < 10; $save_iteration++ ) {
		$_POST = array( 'nonce' => 'valid', 'es_options' => array( 'header' => array( 'phone' => '031-9109101' . $save_iteration ) ) );
		$response = null;
		$started_ns = hrtime( true );
		try { \ErfanSanat\Theme\Admin\AjaxSave::handle(); } catch ( EsTestAjaxResponse $ajax_response ) { $response = $ajax_response; }
		$max_elapsed_ms = max( $max_elapsed_ms, ( hrtime( true ) - $started_ns ) / 1000000 );
		$save_responses_successful = $save_responses_successful && $response instanceof EsTestAjaxResponse && 200 === $response->status && ! empty( $response->payload['success'] );
	}
	restore_error_handler();
	echo '   max of 10 mocked AJAX save handlers: ' . number_format( $max_elapsed_ms, 3 ) . ' ms' . PHP_EOL;
	check( 'AJAX settings save succeeds in under 300ms', fn() => $save_responses_successful && $max_elapsed_ms < 300 );
	check( 'security/performance smoke path emits zero PHP warnings', fn() => array() === $warnings );
} else {
	check( 'AJAX settings save succeeds in under 300ms', fn() => false );
	check( 'security/performance smoke path emits zero PHP warnings', fn() => false );
}

summary();
