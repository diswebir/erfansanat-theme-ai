<?php
/**
 * Task 7.1 — تک‌پروژه + آرشیوهای پروژه
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-01.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 7.1: تک‌پروژه + آرشیوهای پروژه ==\n";

es_check( 'single-projects.php', (bool) (es_file('single-projects.php')) );
es_check( 'archive-projects.php', (bool) (es_file('archive-projects.php')) );
es_check( 'taxonomy-project_type.php', (bool) (es_file('taxonomy-project_type.php')) );
es_check( 'taxonomy-city.php', (bool) (es_file('taxonomy-city.php')) );
es_check( 'foure-image used', (bool) (es_contains('single-projects.php','foure-image')) );
es_check( 'mortabet used', (bool) (es_contains('single-projects.php','mortabet_mahsole_2')) );

es_summary();
