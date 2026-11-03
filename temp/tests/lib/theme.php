<?php
declare(strict_types=1);

namespace EsTest;

/** Absolute path of a theme file. */
function tpath( string $rel ): string {
	return ES_REPO_ROOT . '/theme/' . ltrim( $rel, '/' );
}

/** Read a theme file (null when missing). */
function tfile( string $rel ): ?string {
	return is_file( tpath( $rel ) ) ? (string) file_get_contents( tpath( $rel ) ) : null;
}

function thexists( string $rel ): bool {
	return is_file( tpath( $rel ) );
}

/** md5 of a reference (static mirror) file. */
function ref_md5( string $rel ): ?string {
	$p = ES_REPO_ROOT . '/reference/static-mirror/' . ltrim( $rel, '/' );
	return is_file( $p ) ? md5_file( $p ) : null;
}

/** md5 of a theme file. */
function theme_md5( string $rel ): ?string {
	$p = tpath( $rel );
	return is_file( $p ) ? md5_file( $p ) : null;
}

/**
 * Parse a WordPress style.css / plugin header without needing WP core loaded.
 *
 * @return array<string,string>
 */
function parse_headers( string $rel, array $wanted ): array {
	$src = tfile( $rel );
	if ( null === $src ) {
		return array();
	}
	// WordPress only scans the first 8 KB.
	$src  = substr( $src, 0, 8192 );
	$out  = array();
	foreach ( $wanted as $label => $field ) {
		$re = '/^[ \t\/*#@]*' . preg_quote( $field, '/' ) . ':(.*)$/mi';
		if ( preg_match( $re, $src, $m ) ) {
			$out[ $label ] = trim( preg_replace( '/\s*(?:\*\/|\?>).*/', '', $m[1] ) );
		} else {
			$out[ $label ] = '';
		}
	}
	return $out;
}

/** Strip PHP comments so grep-style tests are reliable. */
function php_code( string $rel ): ?string {
	$src = tfile( $rel );
	if ( null === $src ) {
		return null;
	}
	$tokens = token_get_all( $src );
	$out    = '';
	foreach ( $tokens as $t ) {
		if ( is_array( $t ) && in_array( $t[0], array( T_COMMENT, T_DOC_COMMENT ), true ) ) {
			continue;
		}
		$out .= is_array( $t ) ? $t[1] : $t;
	}
	return $out;
}

/** All theme php files (theme-relative). */
function theme_php_files(): array {
	$out = array();
	if ( ! is_dir( ES_REPO_ROOT . '/theme' ) ) {
		return $out;
	}
	$it = new \RecursiveIteratorIterator(
		new \RecursiveDirectoryIterator( ES_REPO_ROOT . '/theme', \FilesystemIterator::SKIP_DOTS )
	);
	foreach ( $it as $f ) {
		if ( $f->isFile() && '.php' === substr( $f->getFilename(), -4 ) ) {
			$out[] = substr( $f->getPathname(), strlen( ES_REPO_ROOT . '/theme/' ) );
		}
	}
	sort( $out );
	return $out;
}

/** Token-based lint: unbalanced braces/parens/brackets. Returns null when fine. */
function php_lint( string $path ): ?string {
	$src = @file_get_contents( $path );
	if ( false === $src ) {
		return 'unreadable';
	}
	$tokens = @token_get_all( $src );
	$depth  = array( 'brace' => 0, 'paren' => 0, 'bracket' => 0 );
	foreach ( $tokens as $t ) {
		if ( is_array( $t ) ) {
			continue;
		}
		switch ( $t ) {
			case '{': $depth['brace']++;   break;
			case '}': $depth['brace']--;   break;
			case '(': $depth['paren']++;   break;
			case ')': $depth['paren']--;   break;
			case '[': $depth['bracket']++; break;
			case ']': $depth['bracket']--; break;
		}
		if ( min( $depth ) < 0 ) {
			return 'unbalanced near ' . $t;
		}
	}
	return 0 === array_sum( $depth ) ? null : 'unbalanced: ' . json_encode( $depth );
}
