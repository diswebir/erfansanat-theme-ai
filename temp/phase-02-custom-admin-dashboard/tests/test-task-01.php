<?php
/** Task 2.1 — option repository, dot paths, defaults and one-option persistence. */
declare(strict_types=1);

$GLOBALS['es_mock_options'] = array();
$GLOBALS['es_mock_get_option_calls'] = 0;
$GLOBALS['es_option_update_calls'] = 0;
$GLOBALS['es_mock_actions'] = array();

function get_option( $name, $default = false ) {
	$GLOBALS['es_mock_get_option_calls']++;
	return array_key_exists( $name, $GLOBALS['es_mock_options'] ) ? $GLOBALS['es_mock_options'][ $name ] : $default;
}
function update_option( $name, $value, $autoload = null ) {
	$GLOBALS['es_option_update_calls'] = ( $GLOBALS['es_option_update_calls'] ?? 0 ) + 1;
	$changed = ! array_key_exists( $name, $GLOBALS['es_mock_options'] ) || $GLOBALS['es_mock_options'][ $name ] !== $value;
	$GLOBALS['es_mock_options'][ $name ] = $value;
	return $changed;
}
function add_action( $name, $callback ) { $GLOBALS['es_mock_actions'][ $name ][] = $callback; }
function do_action( $name, ...$args ) {
	foreach ( $GLOBALS['es_mock_actions'][ $name ] ?? array() as $callback ) { $callback( ...$args ); }
}

$bootstrap_candidates = array(
	__DIR__ . '/../../../../temp/tests/bootstrap.php',
	__DIR__ . '/../../tests/bootstrap.php',
);
foreach ( $bootstrap_candidates as $bootstrap ) {
	if ( is_file( $bootstrap ) ) { require_once $bootstrap; break; }
}
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', '/wp/' ); }
require_once ES_REPO_ROOT . '/theme/inc/Helpers/Defaults.php';
require_once ES_REPO_ROOT . '/theme/inc/Helpers/Options.php';
\ErfanSanat\Theme\Helpers\Options::register();

echo "== Task 2.1: options repository ==\n";

es_option_reset();
$GLOBALS['es_mock_get_option_calls'] = 0;
$repeated = true;
for ( $i = 0; $i < 100; $i++ ) { $repeated = $repeated && es_option( 'header.phone' ) === '031-91091011'; }
check( '100 reads hit get_option once and return the static fallback', fn() => $repeated && 1 === $GLOBALS['es_mock_get_option_calls'] );

$GLOBALS['es_mock_options']['erfansanat_theme_options'] = array( 'social' => array( 'instagram' => 'https://example.test/ig' ) );
es_option_reset();
check( 'dot notation reads a nested override', fn() => es_option( 'social.instagram' ) === 'https://example.test/ig' );
check( 'missing nested override falls back to static defaults', fn() => es_option( 'header.email' ) === 'info@erfansanat.com' );
check( 'explicit default is returned for unknown paths', fn() => es_option( 'not.registered', 'fallback' ) === 'fallback' );
check( 'invalid dot paths fail closed', fn() => es_option( 'header..phone', 'safe' ) === 'safe' );

$option_count = count( $GLOBALS['es_mock_options'] );
es_option_reset();
es_update_option( 'header.phone', '031-12345678' );
check( 'update merges the value into the one canonical option', fn() => isset( $GLOBALS['es_mock_options']['erfansanat_theme_options']['header']['phone'] ) && $GLOBALS['es_mock_options']['erfansanat_theme_options']['header']['phone'] === '031-12345678' );
check( 'update creates no second theme option', fn() => count( $GLOBALS['es_mock_options'] ) === $option_count );
check( 'the request cache reflects the updated value', fn() => es_option( 'header.phone' ) === '031-12345678' );
check( 'saved hook is fired for integrations', fn() => isset( $GLOBALS['es_mock_options']['erfansanat_theme_options'] ) && isset( $GLOBALS['es_mock_actions']['es_options_saved'] ) );

summary();
