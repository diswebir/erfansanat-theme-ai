<?php
/**
 * Task 8.2 — سیستم مدیریت فرم‌ها
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-02.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 8.2: سیستم مدیریت فرم‌ها ==\n";

es_check( 'Forms module', (bool) (es_file('inc/Core/Forms.php')) );
es_check( 'FormEntries admin', (bool) (es_file('inc/Admin/FormEntries.php')) );
es_check( 'honeypot', (bool) (es_contains('inc/Core/Forms.php','honeypot')) );
es_check( 'nonce', (bool) (es_contains('inc/Core/Forms.php','nonce')) );
es_check( 'wp_mail', (bool) (es_contains('inc/Core/Forms.php','wp_mail')) );

es_summary();
