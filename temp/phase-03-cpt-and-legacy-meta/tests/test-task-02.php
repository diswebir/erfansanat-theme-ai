<?php
/**
 * Task 3.2 — موتور متاباکس محصولات (سازگار با JetEngine)
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-02.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 3.2: موتور متاباکس محصولات (سازگار با JetEngine) ==\n";

es_check( 'ProductMetaboxes exists', (bool) (es_file('inc/Admin/ProductMetaboxes.php')) );
es_check( 'LegacyMeta engine', (bool) (es_file('inc/Core/LegacyMeta.php')) );
es_check( 'key galerry_produ', (bool) (es_contains('inc/Admin/ProductMetaboxes.php','galerry_produ')) );
es_check( 'key english_titl', (bool) (es_contains('inc/Admin/ProductMetaboxes.php','english_titl')) );
es_check( 'key vaule_of_variable', (bool) (es_contains('inc/Admin/ProductMetaboxes.php','vaule_of_variable')) );
es_check( 'repeater item-0 format', (bool) (es_contains('inc/Core/LegacyMeta.php','item-0')) );

es_summary();
