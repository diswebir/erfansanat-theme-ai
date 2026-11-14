<?php
/**
 * ErfanSanat Theme AI — shared QA test bootstrap.
 * Plain-PHP assertion harness (no PHPUnit dependency; runs on the WASM PHP 8.4).
 *
 * Usage in a task test:
 *   require dirname(__DIR__, 2) . '/tests/bootstrap.php';
 *   es_check('label', (bool) $condition);
 *   es_summary(); // prints report, exits 0 (all pass) or 1 (any fail)
 */

define( 'ES_ROOT', dirname( __DIR__, 2 ) );          // repo root
define( 'ES_THEME', ES_ROOT . '/erfansanat' );        // theme source folder
define( 'ES_MIRROR', ES_ROOT . '/static-mirror' );    // static reference
define( 'ES_WP', ES_ROOT . '/temp/wp' );              // QA WordPress install

$GLOBALS['es_pass'] = 0;
$GLOBALS['es_fail'] = 0;
$GLOBALS['es_skip'] = 0;
$GLOBALS['es_fail_labels'] = array();

function es_check( string $label, bool $ok ): void {
	if ( $ok ) {
		$GLOBALS['es_pass']++;
		echo "  [PASS] {$label}\n";
	} else {
		$GLOBALS['es_fail']++;
		$GLOBALS['es_fail_labels'][] = $label;
		echo "  [FAIL] {$label}\n";
	}
}

function es_skip( string $label, string $why = '' ): void {
	$GLOBALS['es_skip']++;
	echo "  [SKIP] {$label}" . ( $why ? " — {$why}" : '' ) . "\n";
}

function es_note( string $msg ): void {
	echo "  [NOTE] {$msg}\n";
}

/**
 * Register a required behavioral scenario that has not been automated yet.
 * Stage-1 skeletons use this so a task cannot silently pass while its real
 * acceptance scenarios are still untested. Strict summaries treat it as a gap.
 */
function es_pending( string $label, string $why = 'behavioral test not automated yet' ): void {
	$GLOBALS['es_skip']++;
	echo "  [PENDING] {$label} — {$why}\n";
}

function es_task( string $id, string $title ): void {
	echo "== {$id}: {$title} ==\n";
}

/** File exists (path relative to ES_THEME, or absolute). */
function es_file( string $rel, string $base = ES_THEME ): bool {
	$p = ( $rel !== '' && $rel[0] === '/' ) ? $rel : $base . '/' . $rel;
	return is_file( $p );
}

/** Directory exists (relative to ES_THEME, or absolute). */
function es_dir( string $rel, string $base = ES_THEME ): bool {
	$p = ( $rel !== '' && $rel[0] === '/' ) ? $rel : $base . '/' . $rel;
	return is_dir( $p );
}

/** File contains a needle (relative to ES_THEME, or absolute). */
function es_contains( string $rel, string $needle, string $base = ES_THEME ): bool {
	$p = ( $rel !== '' && $rel[0] === '/' ) ? $rel : $base . '/' . $rel;
	if ( ! is_file( $p ) ) {
		return false;
	}
	return false !== strpos( (string) file_get_contents( $p ), $needle );
}

/**
 * Idempotent term creation for tests: returns the existing term (by slug, then
 * name) instead of a WP_Error when a fixed-slug fixture term already exists.
 * Drop-in replacement for wp_insert_term() returning the same array shape.
 */
function es_insert_term( string $name, string $taxonomy, array $args = array() ): array {
	$slug = isset( $args['slug'] ) ? (string) $args['slug'] : '';
	if ( '' !== $slug && taxonomy_exists( $taxonomy ) ) {
		$existing = get_term_by( 'slug', $slug, $taxonomy );
		if ( $existing instanceof WP_Term ) {
			return array( 'term_id' => (int) $existing->term_id, 'term_taxonomy_id' => (int) $existing->term_taxonomy_id );
		}
	}
	$result = wp_insert_term( $name, $taxonomy, $args );
	if ( is_wp_error( $result ) ) {
		$existing = get_term_by( 'name', $name, $taxonomy );
		if ( $existing instanceof WP_Term ) {
			return array( 'term_id' => (int) $existing->term_id, 'term_taxonomy_id' => (int) $existing->term_taxonomy_id );
		}
		return array( 'term_id' => 0, 'term_taxonomy_id' => 0 );
	}
	return $result;
}

/** Load the QA WordPress (once). Returns true on success. */
function es_load_wp(): bool {
	if ( defined( 'ABSPATH' ) ) {
		return true;
	}
	$load = ES_WP . '/wp-load.php';
	if ( ! is_file( $load ) ) {
		es_note( 'QA WordPress missing — run temp/tools/setup-harness.sh' );
		return false;
	}
	require $load;
	return defined( 'ABSPATH' );
}

/** Activate the in-repo theme in the QA install (idempotent). */
function es_activate_theme(): bool {
	if ( ! es_load_wp() ) {
		return false;
	}
	if ( ! is_file( ES_THEME . '/style.css' ) ) {
		return false;
	}
	if ( get_option( 'stylesheet' ) !== 'erfansanat' ) {
		switch_theme( 'erfansanat' );
	}
	return get_option( 'stylesheet' ) === 'erfansanat';
}

function es_theme_active(): bool {
	return es_load_wp() && get_option( 'stylesheet' ) === 'erfansanat';
}

function es_summary( bool $strict = true ): void {
	$p = $GLOBALS['es_pass'];
	$f = $GLOBALS['es_fail'];
	$s = $GLOBALS['es_skip'];
	echo "\n  RESULT: {$p} passed, {$f} failed, {$s} skipped/pending\n";
	if ( $f > 0 ) {
		echo "  Failed checks:\n";
		foreach ( $GLOBALS['es_fail_labels'] as $label ) {
			echo "   - {$label}\n";
		}
	}
	if ( $f > 0 || ( $strict && $s > 0 ) ) {
		exit( 1 );
	}
	exit( 0 );
}
