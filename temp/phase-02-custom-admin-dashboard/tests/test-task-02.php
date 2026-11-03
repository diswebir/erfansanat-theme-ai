<?php
/**
 * Task 2.2 — تب‌های مدیریتی سراسری
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-02.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 2.2: تب‌های مدیریتی سراسری ==\n";

es_check( 'Defaults module', (bool) (es_file('inc/Defaults.php')) );
es_check( 'header tab', (bool) (es_contains('inc/Admin/ThemeOptions.php','header')) );
es_check( 'footer tab', (bool) (es_contains('inc/Admin/ThemeOptions.php','footer')) );
es_check( 'contact tab', (bool) (es_contains('inc/Admin/ThemeOptions.php','contact')) );
es_check( 'brand colors', (bool) (es_contains('inc/Admin/ThemeOptions.php','color')) );

es_summary();
