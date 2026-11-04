<?php
/**
 * Run every test file of one phase inside a single PHP process.
 *
 * The job file (tools/harness/phase-run.json) carries:
 *   { "dir": "temp/phase-01-core-and-assets/tests", "pattern": "test-task-*.php" }
 */
declare(strict_types=1);

$root = '/home/user/erfansanat-theme-ai';
$cfg  = json_decode( (string) file_get_contents( __DIR__ . '/phase-run.json' ), true ) ?: array();
@unlink( __DIR__ . '/phase-run.json' );

$dir     = $root . '/' . trim( (string) ( $cfg['dir'] ?? '' ), '/' );
$phase   = basename( dirname( $dir ) ); // e.g. phase-01-core-and-assets
$pattern = (string) ( $cfg['pattern'] ?? 'test-task-*.php' );

if ( ! is_dir( $dir ) ) {
	echo "MISSING DIR: $dir\n";
	exit( 2 );
}

$files = glob( $dir . '/' . $pattern );
sort( $files );

require_once $root . '/temp/tests/bootstrap.php';

$rows   = array();
$failed = 0;

foreach ( $files as $file ) {
	\EsTest\reset_results();
	$prior_pass = $prior_fail = $prior_pending = 0;
	foreach ( $rows as $prior_row ) {
		$prior_pass += $prior_row[1];
		$prior_fail += $prior_row[2];
		$prior_pending += $prior_row[3];
	}
	$GLOBALS['es_phase_prior_result'] = array( 'files' => count( $rows ), 'passed' => $prior_pass, 'failed' => $prior_fail, 'pending' => $prior_pending );
	$before = ob_get_level();

	echo "\n" . str_repeat( '─', 64 ) . "\n";
	echo '▶ ' . basename( $file ) . "\n";

	ob_start();
	try {
		include $file;
	} catch ( \Throwable $e ) {
		while ( ob_get_level() > $before ) { ob_end_flush(); }
		echo "  \033[31mFATAL\033[0m " . $e->getMessage() . "\n";
		$rows[] = array( basename( $file ), 0, 1, 0 );
		$failed++;
		continue;
	}
	$captured = (string) ob_get_clean();
	echo $captured;

	$c      = \EsTest\counts();
	$rows[] = array( basename( $file ), $c['PASS'], $c['FAIL'], $c['PENDING'] );

	if ( $c['FAIL'] > 0 || $c['PENDING'] > 0 || 0 === $c['PASS'] ) {
		$failed++;
	}

	// Cumulative verdict, written after every file: the gate task (which runs
	// last) reads the verdict of all the files before it.
	$cum_pass = $cum_fail = $cum_pending = 0;
	$cum_failed_files = 0;
	foreach ( $rows as $r ) {
		$cum_pass    += $r[1];
		$cum_fail    += $r[2];
		$cum_pending += $r[3];
		if ( $r[2] > 0 || $r[3] > 0 || 0 === $r[1] ) {
			++$cum_failed_files;
		}
	}
	@file_put_contents(
		$root . '/tools/harness/last-phase-result.json',
		json_encode(
			array(
				'phase'    => $phase,
				'files'    => count( $rows ),
				'passed'   => $cum_pass,
				'failed'   => $cum_failed_files,
				'pending'  => $cum_pending,
				'at'       => gmdate( 'c' ),
			),
			JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
		)
	);
}

echo "\n" . str_repeat( '═', 64 ) . "\n";
printf( "%-24s %6s %6s %8s\n", 'TEST FILE', 'PASS', 'FAIL', 'PENDING' );
echo str_repeat( '─', 64 ) . "\n";

$tp = $tf = $tpend = 0;
foreach ( $rows as $r ) {
	printf( "%-24s %6d %6d %8d\n", $r[0], $r[1], $r[2], $r[3] );
	$tp += $r[1];
	$tf += $r[2];
	$tpend += $r[3];
}
echo str_repeat( '─', 64 ) . "\n";
printf( "%-24s %6d %6d %8d\n", 'TOTAL', $tp, $tf, $tpend );

echo "\n";
if ( 0 === $failed ) {
	echo "PHASE RESULT: \033[32mOK\033[0m (" . count( $files ) . " test files, {$tp} assertions)\n";
	exit( 0 );
}

echo "PHASE RESULT: \033[31mNOT OK\033[0m ({$failed} of " . count( $files ) . " test files failed)\n";
exit( 1 );
