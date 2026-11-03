<?php
/**
 * Task 3.3 — متاباکس پروژه‌ها + FAQ دسته + Hreflang + بازدید
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-03.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 3.3: متاباکس پروژه‌ها + FAQ دسته + Hreflang + بازدید ==\n";

es_check( 'ProjectMetaboxes exists', (bool) (es_file('inc/Admin/ProjectMetaboxes.php')) );
es_check( 'key foure-image', (bool) (es_contains('inc/Admin/ProjectMetaboxes.php','foure-image')) );
es_check( 'mortabet_mahsole_2', (bool) (es_contains('inc/Admin/ProjectMetaboxes.php','mortabet_mahsole_2')) );
es_check( 'TermMetaboxes exists', (bool) (es_file('inc/Admin/TermMetaboxes.php')) );
es_check( 'HreflangMetabox exists', (bool) (es_file('inc/Admin/HreflangMetabox.php')) );
es_check( 'hrefmeta keys', (bool) (es_contains('inc/Admin/HreflangMetabox.php','hrefmeta_fa')) );
es_check( 'ViewCounter exists', (bool) (es_file('inc/Core/ViewCounter.php')) );
es_check( 'number2-viewc fallback', (bool) (es_contains('inc/Core/ViewCounter.php','number-viewc')) );

es_summary();
