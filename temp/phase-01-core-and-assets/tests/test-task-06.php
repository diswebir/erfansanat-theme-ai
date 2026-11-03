<?php
/**
 * Test — Task 1.6: DeBloat
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

use function EsTest\php_code;

echo "== Task 1.6: DeBloat ==\n";

$src = (string) php_code( 'inc/Performance/DeBloat.php' );

check( 'debloat module exists', fn() => '' !== $src );
check( 'returns early in admin', fn() => str_contains( $src, 'if ( is_admin()' ) );
foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'global-styles', 'classic-theme-styles' ) as $h ) {
	check( "dequeues $h", fn() => str_contains( $src, "'" . $h . "'" ) );
}
check( 'removes wp-embed', fn() => str_contains( $src, "'wp-embed'" ) );
check( 'removes emoji actions', fn() => str_contains( $src, 'print_emoji_detection_script' ) && str_contains( $src, 'print_emoji_styles' ) );
check( 'removes generator/rsd/wlw/shortlink/rest meta', fn() => str_contains( $src, 'wp_generator' ) && str_contains( $src, 'rsd_link' ) && str_contains( $src, 'wlwmanifest_link' ) && str_contains( $src, 'wp_shortlink_wp_head' ) && str_contains( $src, 'rest_output_link_wp_head' ) );
check( 'removes oEmbed discovery links', fn() => str_contains( $src, 'wp_oembed_add_discovery_links' ) );
check( 'dashicons only for logged-in users', fn() => str_contains( $src, 'is_admin_bar_showing' ) );
check( 'drops the wp-embed-responsive body class', fn() => str_contains( $src, 'wp-embed-responsive' ) );

// runtime: rendered document must be free of block/emoji/embed payloads
$html = is_file( '/tmp/home.html' ) ? (string) file_get_contents( '/tmp/home.html' ) : '';
check( 'a rendered snapshot is available', fn() => '' !== $html );
if ( '' !== $html ) {
	check( 'rendered home has no block-library asset', fn() => ! str_contains( $html, 'block-library' ) );
	check( 'rendered home has no wp-emoji asset', fn() => ! str_contains( $html, 'wp-emoji' ) );
	check( 'rendered home has no wp-embed script', fn() => ! str_contains( $html, 'wp-embed' ) );
	check( 'rendered home has no generator meta', fn() => ! str_contains( $html, 'name="generator"' ) );
	check( 'rendered home has no global-styles', fn() => ! str_contains( $html, 'global-styles' ) );
	check( 'rendered home loads exactly one theme stylesheet', fn() => 1 === substr_count( $html, 'assets/css/styles.css' ) );
}

summary();
