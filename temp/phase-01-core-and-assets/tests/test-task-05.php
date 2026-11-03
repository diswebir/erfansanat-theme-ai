<?php
/**
 * Test — Task 1.5: harness renderer (tools/harness/fetch.php + Node wrappers)
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.5: render harness ==\n";

$php = is_file( ES_REPO_ROOT . '/tools/harness/fetch.php' )
	? (string) file_get_contents( ES_REPO_ROOT . '/tools/harness/fetch.php' )
	: null;

check( 'fetch.php exists', fn() => null !== $php );
check( 'fetch.php emits a JSON payload', fn() => str_contains( (string) $php, 'ES_JSON' ) );
check( 'fetch.php reports queries', fn() => str_contains( (string) $php, 'num_queries' ) );
check( 'fetch.php reports warnings', fn() => str_contains( (string) $php, "'warnings'" ) );
check( 'fetch.php reports the resolved template', fn() => str_contains( (string) $php, "'template'" ) );
check( 'fetch.php reports theme render time (theme_ms)', fn() => str_contains( (string) $php, "'theme_ms'" ) );
check( 'fetch.php boots through wp-blog-header (canonical flow)', fn() => str_contains( (string) $php, 'wp-blog-header.php' ) );
check( 'fetch.php collects warnings without printing them', fn() => str_contains( (string) $php, 'set_error_handler' ) );
check( 'fetch.php lives outside the shipped theme', fn() => ! is_file( ES_REPO_ROOT . '/theme/tools/harness/fetch.php' ) );

check( 'node wrapper exists', fn() => is_file( ES_REPO_ROOT . '/tools/harness/fetch.mjs' ) );
check( 'perf reporter exists', fn() => is_file( ES_REPO_ROOT . '/tools/harness/perf.mjs' ) );
check( 'single-process phase runner exists', fn() => is_file( ES_REPO_ROOT . '/tools/harness/run-phase.mjs' ) );
check( 'budget checker exists', fn() => is_file( ES_REPO_ROOT . '/tools/harness/gate.php' ) );

summary();
