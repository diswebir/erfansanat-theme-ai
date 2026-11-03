<?php
/**
 * Task 1.1 — اسکلت پایه قالب + معماری OOP
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-01.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 1.1: اسکلت پایه قالب + معماری OOP ==\n";

es_check( 'style.css exists', (bool) (es_file('style.css')) );
es_check( 'style.css theme header', (bool) (es_contains('style.css','Theme Name:')) );
es_check( 'functions.php exists', (bool) (es_file('functions.php')) );
es_check( 'index.php exists', (bool) (es_file('index.php')) );
es_check( 'Autoloader exists', (bool) (es_file('inc/Autoloader.php')) );
es_check( 'Namespace ErfanSanat\Theme', (bool) (es_contains('inc/Autoloader.php','ErfanSanat')) );
// WP-dependent checks run ONLY when our theme is active (avoids false
// positives from the default theme that ships with the QA install).
if ( es_activate_theme() ) {
  es_check( 'theme activates', (bool) (es_activate_theme()) );
  es_check( 'supports title-tag', (bool) (current_theme_supports('title-tag')) );
  es_check( 'supports post-thumbnails', (bool) (current_theme_supports('post-thumbnails')) );
  es_check( 'supports woocommerce', (bool) (current_theme_supports('woocommerce')) );
  es_check( 'supports html5', (bool) (current_theme_supports('html5')) );
} else {
  es_skip( 'theme activates', 'erfansanat theme not active yet' );
  es_skip( 'supports title-tag', 'erfansanat theme not active yet' );
  es_skip( 'supports post-thumbnails', 'erfansanat theme not active yet' );
  es_skip( 'supports woocommerce', 'erfansanat theme not active yet' );
  es_skip( 'supports html5', 'erfansanat theme not active yet' );
}
es_summary();
