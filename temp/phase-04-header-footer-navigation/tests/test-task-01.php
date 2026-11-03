<?php
/**
 * Task 4.1 — برش هدر/فوتر + اتصال به تنظیمات + Hreflang
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-01.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 4.1: برش هدر/فوتر + اتصال به تنظیمات + Hreflang ==\n";

es_check( 'header.php exists', (bool) (es_file('header.php')) );
es_check( 'footer.php exists', (bool) (es_file('footer.php')) );
es_check( 'wp_head call', (bool) (es_contains('header.php','wp_head')) );
es_check( 'body_class call', (bool) (es_contains('header.php','body_class')) );
es_check( 'wp_body_open call', (bool) (es_contains('header.php','wp_body_open')) );
es_check( 'wp_footer call', (bool) (es_contains('footer.php','wp_footer')) );
es_check( 'data-navbar-header preserved', (bool) (es_contains('header.php','data-navbar-header')) );

es_summary();
