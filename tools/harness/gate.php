<?php
/**
 * Phase gate: read the JSON produced by tools/harness/perf.mjs and assert the
 * performance budget. Exit code 0 = budget met.
 */
declare(strict_types=1);

$root = '/home/user/erfansanat-theme-ai';
$file = $root . '/tools/harness/perf-report.json';

if ( ! is_file( $file ) ) {
	echo "MISSING {$file} — run node tools/harness/perf.mjs first\n";
	exit( 2 );
}

$report = json_decode( (string) file_get_contents( $file ), true );
$rows   = $report['rows'] ?? array();
$totals = $report['totals'] ?? array();

$pass = true;

foreach ( $rows as $row ) {
	$url = $row['url'];

	$checks = array(
		"HTTP 200 on {$url}"              => 200 === (int) $row['status'],
		"≤ 2 stylesheets on {$url}"       => (int) $row['stylesheets'] <= 2,
		"≤ 2 scripts on {$url}"           => (int) $row['scripts'] <= 2,
		"only theme assets are queued on {$url}" => (int) $row['theme_assets'] <= 4,
		"no PHP warnings on {$url}"       => 0 === count( $row['warnings'] ),
		"theme render < 500ms on {$url}"   => (float) ( $row['theme_ms'] ?? 0 ) < 500.0,
		"queries < 60 on {$url}"          => (int) $row['queries'] < 60,
	);

	foreach ( $checks as $label => $ok ) {
		printf( "  %s %s\n", $ok ? 'PASS' : 'FAIL', $label );
		$pass = $pass && $ok;
	}

	foreach ( $row['warnings'] as $w ) {
		echo "         ⚠ {$w}\n";
	}
}

echo "\n";
echo $pass ? "GATE BUDGET: OK\n" : "GATE BUDGET: NOT OK\n";
exit( $pass ? 0 : 1 );
