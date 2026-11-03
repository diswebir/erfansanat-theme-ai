<?php
declare(strict_types=1);

namespace EsTest;

/**
 * Lazy WordPress bootstrap for tests.
 * Uses a local, machine-specific config that is NOT committed:
 *   temp/tests/harness.local.php  ->  <?php return ['wp_root' => '/path/to/wp', 'theme_slug' => 'erfansanat'];
 */
function config(): array {
	static $cfg = null;
	if ( $cfg !== null ) {
		return $cfg;
	}
	$local = __DIR__ . '/../harness.local.php';
	$cfg   = is_file( $local ) ? require $local : [
		'wp_root'    => getenv( 'ES_WP_ROOT' ) ?: '/home/user/.cache/harness/wpn',
		'theme_slug' => 'erfansanat',
	];
	return $cfg;
}

function theme_root(): string {
	return ES_REPO_ROOT;
}

function wp_root(): ?string {
	$c = config();
	return is_dir( $c['wp_root'] ) ? rtrim( $c['wp_root'], '/' ) : null;
}

/** Boot WordPress once (function_exists guard). Returns true when available. */
function wp_boot(): bool {
	static $booted = null;
	if ( $booted !== null ) {
		return $booted;
	}
	$root = wp_root();
	if ( ! $root ) {
		return $booted = false;
	}
	if ( ! defined( 'ABSPATH' ) ) {
		define( 'WP_USE_THEMES', false );
		define( 'WP_ADMIN', false );
		require_once $root . '/wp-load.php';
	}
	return $booted = function_exists( 'get_bloginfo' );
}

function is_wp(): bool {
	return wp_boot();
}

/** Convenience: does a relative file exist inside the theme? */
function theme_has( string $rel ): bool {
	return is_file( ES_REPO_ROOT . '/' . ltrim( $rel, '/' ) );
}

function theme_code( string $rel ): ?string {
	$p = ES_REPO_ROOT . '/' . ltrim( $rel, '/' );
	return is_file( $p ) ? (string) file_get_contents( $p ) : null;
}

/** Recursively list theme-relative files, optionally filtered by extension. */
function theme_files( string $ext = '' , array $skip = [ '.git', 'temp', 'tools', 'reference', 'node_modules' ] ): array {
	$out = [];
	$it  = new \RecursiveIteratorIterator(
		new \RecursiveCallbackFilterIterator(
			new \RecursiveDirectoryIterator( ES_REPO_ROOT, \FilesystemIterator::SKIP_DOTS ),
			function ( $f ) use ( $skip ) {
				return ! in_array( $f->getFilename(), $skip, true );
			}
		)
	);
	foreach ( $it as $f ) {
		if ( ! $f->isFile() ) {
			continue;
		}
		if ( $ext && substr( $f->getFilename(), -strlen( $ext ) ) !== $ext ) {
			continue;
		}
		$out[] = substr( $f->getPathname(), strlen( ES_REPO_ROOT ) + 1 );
	}
	sort( $out );
	return $out;
}

/** Return files whose content matches a regex. */
function grep_theme( string $pattern, string $ext = '.php', array $skip = [] ): array {
	$hits = [];
	foreach ( theme_files( $ext, $skip ) as $f ) {
		$c = (string) @file_get_contents( ES_REPO_ROOT . '/' . $f );
		if ( preg_match( $pattern, $c ) ) {
			$hits[] = $f;
		}
	}
	return $hits;
}
