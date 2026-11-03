<?php
/**
 * Test — Task 1.12: گیت فاز ۱ (اجرای کل فاز + بودجه عملکرد)
 *
 * The gate is executed by tools/harness/phase-gate.mjs (it needs a real render,
 * which cannot happen inside php-wasm). This file asserts the artefacts the
 * gate relies on and the last recorded measurements.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.12: phase 1 gate ==\n";

$report_file = ES_REPO_ROOT . '/tools/harness/perf-report.json';
$report      = is_file( $report_file ) ? json_decode( (string) file_get_contents( $report_file ), true ) : null;

check( 'gate runner exists', fn() => is_file( ES_REPO_ROOT . '/tools/harness/phase-gate.mjs' ) );
check( 'perf reporter exists', fn() => is_file( ES_REPO_ROOT . '/tools/harness/perf.mjs' ) );
check( 'budget checker exists', fn() => is_file( ES_REPO_ROOT . '/tools/harness/gate.php' ) );
check( 'a perf report has been recorded', fn() => is_array( $report ) && ! empty( $report['rows'] ) );

$verdict_file = ES_REPO_ROOT . '/tools/harness/last-phase-result.json';
$verdict      = is_file( $verdict_file ) ? json_decode( (string) file_get_contents( $verdict_file ), true ) : null;

check( 'the phase runner recorded a verdict', fn() => is_array( $verdict ) );
check( 'P1 — every other Phase 1 test file is green', fn() => is_array( $verdict ) && (int) ( $verdict['failed'] ?? 99 ) === 0 && (int) ( $verdict['files'] ?? 0 ) >= 11 );
check( 'P1 — zero pending assertions anywhere in the phase', fn() => is_array( $verdict ) && 0 === (int) ( $verdict['pending'] ?? 99 ) );

check( 'P2 — at most 2 css + 2 js requests per page', fn() => is_array( $report )
	&& (int) ( $report['totals']['max_stylesheets'] ?? 99 ) <= 2
	&& (int) ( $report['totals']['max_scripts'] ?? 99 ) <= 2 );

check( 'P4 — zero PHP warnings on every measured page', fn() => is_array( $report ) && 0 === (int) ( $report['totals']['warnings'] ?? 99 ) );

check( 'every measured page answered 200', function () use ( $report ) {
	if ( ! is_array( $report ) ) { return false; }
	foreach ( $report['rows'] as $row ) {
		if ( 200 !== (int) $row['status'] ) { return false; }
	}
	return true;
} );

check( 'P3 — theme render time is recorded and within budget', fn() => is_array( $report )
	&& isset( $report['totals']['max_theme_ms'] )
	&& (float) $report['totals']['max_theme_ms'] < 500.0 );

check( 'the gate result is logged in QA_REFLECTION_LOG.md', function () {
	$log = ES_REPO_ROOT . '/temp/QA_REFLECTION_LOG.md';
	if ( ! is_file( $log ) ) { return false; }
	$text = (string) file_get_contents( $log );
	return str_contains( $text, 'PHASE 1 GATE' ) && str_contains( $text, 'theme_ms' );
} );

summary();
