<?php
/** Small source-test helpers shared by the phase-six PHP-WASM checks. */
declare(strict_types=1);

if ( ! function_exists( 'es_p6_source' ) ) {
	function es_p6_source( string $path ): string {
		return (string) \EsTest\theme_code( $path );
	}
}
if ( ! function_exists( 'es_p6_has_all' ) ) {
	function es_p6_has_all( string $source, array $needles ): bool {
		foreach ( $needles as $needle ) { if ( ! is_string( $needle ) || '' === $needle || ! str_contains( $source, $needle ) ) { return false; } }
		return true;
	}
}
if ( ! function_exists( 'es_p6_has_any_pending_placeholders' ) ) {
	function es_p6_has_any_pending_placeholders(): bool {
		$needle = 'return ' . 'null; /* IMPLEMENT:';
		foreach ( glob( __DIR__ . '/test-task-*.php' ) as $test_file ) {
			if ( str_contains( (string) file_get_contents( $test_file ), $needle ) ) { return true; }
		}
		return false;
	}
}
if ( ! function_exists( 'es_p6_has_files' ) ) {
	function es_p6_has_files( array $paths ): bool {
		foreach ( $paths as $path ) { if ( ! \EsTest\thexists( $path ) ) { return false; } }
		return true;
	}
}
