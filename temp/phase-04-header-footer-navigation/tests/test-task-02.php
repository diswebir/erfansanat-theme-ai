<?php
/**
 * Task 4.2 — Custom Nav Walkers
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-02.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 4.2: Custom Nav Walkers ==\n";

es_check( 'NavWalkers exists', (bool) (es_file('inc/Core/NavWalkers.php')) );
es_check( 'Menus module', (bool) (es_file('inc/Core/Menus.php')) );
es_check( 'main-mega-menu location', (bool) (es_contains('inc/Core/Menus.php','main-mega-menu')) );
es_check( 'mobile-menu location', (bool) (es_contains('inc/Core/Menus.php','mobile-menu')) );
es_check( 'tolidat-sherkat location', (bool) (es_contains('inc/Core/Menus.php','tolidat-sherkat')) );
es_check( 'landing location', (bool) (es_contains('inc/Core/Menus.php','landing')) );
// WP-dependent checks run ONLY when our theme is active (avoids false
// positives from the default theme that ships with the QA install).
if ( es_activate_theme() ) {
  es_check( 'menu locations registered', (bool) ((($m=get_registered_nav_menus()) && isset($m['main-mega-menu'],$m['mobile-menu'],$m['tolidat-sherkat'],$m['landing']))) );
} else {
  es_skip( 'menu locations registered', 'erfansanat theme not active yet' );
}
es_summary();
