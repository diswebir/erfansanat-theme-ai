<?php
/**
 * Task 8.3 — ممیزی نهایی + سوئیت E2E
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-03.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 8.3: ممیزی نهایی + سوئیت E2E ==\n";

es_check( 'Seo module', (bool) (es_file('inc/Core/Seo.php')) );
es_check( 'e2e runner', (bool) (es_file('temp/run-all-phases-e2e.sh', ES_ROOT)) );

es_summary();
