<?php
/**
 * Task 2.1 — پنل داشبورد اختصاصی (ThemeOptions)
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-01.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 2.1: پنل داشبورد اختصاصی (ThemeOptions) ==\n";

es_check( 'ThemeOptions exists', (bool) (es_file('inc/Admin/ThemeOptions.php')) );
es_check( 'capability check', (bool) (es_contains('inc/Admin/ThemeOptions.php','manage_options')) );
es_check( 'nonce check', (bool) (es_contains('inc/Admin/ThemeOptions.php','nonce')) );
es_check( 'single option key', (bool) (es_contains('inc/Admin/ThemeOptions.php','erfansanat_theme_options')) );
es_check( 'es_option helper', (bool) (es_contains('inc/Helpers.php','function es_option')) );
// WP-dependent checks run ONLY when our theme is active (avoids false
// positives from the default theme that ships with the QA install).
if ( es_activate_theme() ) {
  es_check( 'es_option returns default', (bool) (function_exists('es_option') && es_option('nonexistent_key_xyz','fallback')==='fallback') );
} else {
  es_skip( 'es_option returns default', 'erfansanat theme not active yet' );
}
es_summary();
