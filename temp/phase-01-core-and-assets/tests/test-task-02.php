<?php
/**
 * Task 1.2 — ماژول DeBloat (پاکسازی هسته)
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-01-core-and-assets/tests/test-task-02.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 1.2: ماژول DeBloat (پاکسازی هسته) ==\n";

es_check( 'DeBloat module exists', (bool) (es_file('inc/Core/DeBloat.php')) );
es_check( 'dequeues wp-block-library', (bool) (es_contains('inc/Core/DeBloat.php','wp-block-library')) );
es_check( 'removes emoji', (bool) (es_contains('inc/Core/DeBloat.php','print_emoji_detection_script')) );
es_check( 'cleans wp_head', (bool) (es_contains('inc/Core/DeBloat.php','wp_generator')) );
// WP-dependent checks run ONLY when our theme is active (avoids false
// positives from the default theme that ships with the QA install).
if ( es_activate_theme() ) {
  es_check( 'emoji script removed from wp_head', (bool) (!has_action('wp_head','print_emoji_detection_script')) );
  es_check( 'emoji styles removed', (bool) (!has_action('wp_print_styles','print_emoji_styles')) );
  es_check( 'generator removed', (bool) (!has_action('wp_head','wp_generator')) );
} else {
  es_skip( 'emoji script removed from wp_head', 'erfansanat theme not active yet' );
  es_skip( 'emoji styles removed', 'erfansanat theme not active yet' );
  es_skip( 'generator removed', 'erfansanat theme not active yet' );
}
es_summary();
