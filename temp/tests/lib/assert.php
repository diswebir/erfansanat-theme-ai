<?php
declare(strict_types=1);

namespace EsTest {

$GLOBALS['es_results'] = [ 'PASS' => 0, 'FAIL' => 0, 'PENDING' => 0 ];
$GLOBALS['es_failures'] = [];
$GLOBALS['es_pendings'] = [];

function banner( string $name ): void {
	echo "\n\033[1m### {$name}\033[0m\n";
}

/**
 * Register an assertion. The callable must return true (pass), false (fail),
 * or null (pending — behaviour not implemented yet).
 */
function check( string $label, callable $fn ): void {
	try {
		$r = $fn();
	} catch ( \Throwable $e ) {
		$r = false;
		$label .= ' — exception: ' . $e->getMessage();
	}
	if ( $r === null ) {
		$GLOBALS['es_results']['PENDING']++;
		$GLOBALS['es_pendings'][] = $label;
		echo "  \033[33mPENDING\033[0m {$label}\n";
		return;
	}
	if ( $r ) {
		$GLOBALS['es_results']['PASS']++;
		echo "  \033[32mPASS\033[0m {$label}\n";
		return;
	}
	$GLOBALS['es_results']['FAIL']++;
	$GLOBALS['es_failures'][] = $label;
	echo "  \033[31mFAIL\033[0m {$label}\n";
}

function pending( string $label ): void {
	$GLOBALS['es_results']['PENDING']++;
	$GLOBALS['es_pendings'][] = $label;
	echo "  \033[33mPENDING\033[0m {$label}\n";
}

function summary(): int {
	$r = $GLOBALS['es_results'];
	printf(
		"\n  → PASS=%d  FAIL=%d  PENDING=%d\n",
		$r['PASS'], $r['FAIL'], $r['PENDING']
	);
	if ( $r['FAIL'] > 0 ) {
		echo "  FAILED:\n";
		foreach ( $GLOBALS['es_failures'] as $f ) {
			echo "    - {$f}\n";
		}
	}
	if ( $r['PENDING'] > 0 ) {
		echo "  PENDING (not implemented yet):\n";
		foreach ( $GLOBALS['es_pendings'] as $p ) {
			echo "    - {$p}\n";
		}
	}
	echo $r['FAIL'] === 0 && $r['PENDING'] === 0 ? "  RESULT: \033[32mOK\033[0m\n" : "  RESULT: \033[31mNOT OK\033[0m\n";
	return ( $r['FAIL'] === 0 && $r['PENDING'] === 0 ) ? 0 : 1;
}
}

// ---- global namespace aliases (test files are written in the global namespace) ----
namespace {
	if ( ! function_exists( 'check' ) ) {
		function check( string $label, callable $fn ): void { \EsTest\check( $label, $fn ); }
	}
	if ( ! function_exists( 'pending' ) ) {
		function pending( string $label ): void { \EsTest\pending( $label ); }
	}
	if ( ! function_exists( 'summary' ) ) {
		function summary(): int { return \EsTest\summary(); }
	}
	if ( ! function_exists( 'banner' ) ) {
		function banner( string $name ): void { \EsTest\banner( $name ); }
	}
}
