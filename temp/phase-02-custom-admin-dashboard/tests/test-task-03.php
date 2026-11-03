<?php
/**
 * Task 2.3 — موتور SectionManager
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-03.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 2.3: موتور SectionManager ==\n";

es_check( 'SectionManager exists', (bool) (es_file('inc/Admin/SectionManager.php')) );
es_check( 'repeater support', (bool) (es_contains('inc/Admin/SectionManager.php','repeater')) );
es_check( 'enable/disable', (bool) (es_contains('inc/Admin/SectionManager.php','enabled')) );
es_check( 'order rendering', (bool) (es_contains('inc/Admin/SectionManager.php','order')) );

es_summary();
