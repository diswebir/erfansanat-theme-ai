<?php
/**
 * Test — Task 1.10: Preload (فونت + LCP)
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

use function EsTest\php_code;

echo "== Task 1.10: preload / LCP ==\n";

$src = (string) php_code( 'inc/Assets/Preload.php' );

check( 'preload module exists', fn() => '' !== $src );
check( 'hooks into wp_head', fn() => str_contains( $src, "'wp_head'" ) );
check( 'preloads the arabic font', fn() => str_contains( $src, 'vazirmatn-arabic.woff2' ) );
check( 'font hint declares as="font"', fn() => str_contains( $src, 'as="font"' ) );
check( 'font preload is crossorigin anonymous', fn() => str_contains( $src, 'crossorigin="anonymous"' ) );
check( 'prefers webp for the hero LCP image', fn() => str_contains( $src, 'hero.webp' ) );
check( 'marks the hero preload fetchpriority=high', fn() => str_contains( $src, 'fetchpriority="high"' ) );
check( 'only preloads the hero on the front page', fn() => str_contains( $src, 'is_front_page' ) );
check( 'falls back to the jpg when webp is missing', fn() => str_contains( $src, 'hero.jpg' ) );
check( 'skips preloading in the admin', fn() => str_contains( $src, 'is_admin' ) );

$html = is_file( '/tmp/home.html' ) ? (string) file_get_contents( '/tmp/home.html' ) : '';
check( 'a rendered snapshot is available', fn() => '' !== $html );
if ( '' !== $html ) {
	check( 'rendered home preloads the arabic woff2', fn() => str_contains( $html, 'vazirmatn-arabic.woff2' ) );
	check( 'rendered home preloads hero.webp with high priority', fn() => (bool) preg_match( '#<link rel="preload"[^>]*hero\.webp[^>]*fetchpriority="high"#', $html ) || (bool) preg_match( '#<link rel="preload"[^>]*fetchpriority="high"[^>]*hero\.webp#', $html ) );
	check( 'rendered home has no duplicate font preload', fn() => 1 === substr_count( $html, 'vazirmatn-arabic.woff2' ) );
	check( 'rendered home has at most 2 preload hints', fn() => substr_count( $html, 'rel="preload"' ) <= 2 );
}

summary();
