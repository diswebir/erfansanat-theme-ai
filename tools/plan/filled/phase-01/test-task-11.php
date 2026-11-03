<?php
/**
 * Test — Task 1.11: منوها + text domain
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

use function EsTest\php_code;

echo "== Task 1.11: menus + textdomain ==\n";

$src = (string) php_code( 'inc/Setup/Menus.php' );

check( 'menus module exists', fn() => '' !== $src );
check( 'hooks register_nav_menus on after_setup_theme', fn() => str_contains( $src, 'register_nav_menus' ) && str_contains( $src, 'after_setup_theme' ) );
check( 'declares main-mega-menu', fn() => str_contains( $src, "'main-mega-menu'" ) );
check( 'declares mobile-menu', fn() => str_contains( $src, "'mobile-menu'" ) );
check( 'declares tolidat-sherkat', fn() => str_contains( $src, "'tolidat-sherkat'" ) );
check( 'declares landing menu', fn() => str_contains( $src, "'landing'" ) );
check( 'location labels are translatable', fn() => substr_count( $src, 'esc_html__(' ) + substr_count( $src, '__(' ) >= 4 );
check( 'text domain is erfansanat', fn() => substr_count( $src, "'erfansanat'" ) >= 4 );
check( 'menu module does not hardcode <ul> markup', fn() => ! str_contains( $src, '<ul' ) );

check( 'runtime: four locations are registered', function () {
	if ( ! EsTest\activate_theme() ) { return false; }
	$registered = get_registered_nav_menus();
	return 4 === count( $registered )
		&& isset( $registered['main-mega-menu'], $registered['mobile-menu'], $registered['tolidat-sherkat'], $registered['landing'] );
} );

check( 'languages directory exists for translations', fn() => is_dir( EsTest\tpath( 'languages' ) ) );
check( 'no .pot is shipped yet (generated in Phase 8)', fn() => array() === glob( EsTest\tpath( 'languages/*.pot' ) ) );

summary();
