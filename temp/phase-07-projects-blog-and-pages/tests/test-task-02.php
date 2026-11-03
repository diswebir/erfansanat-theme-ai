<?php
/**
 * Task 7.2 — وبلاگ + تک‌نوشته (TOC/دیدگاه) + جستجو + 404
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-02.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 7.2: وبلاگ + تک‌نوشته (TOC/دیدگاه) + جستجو + 404 ==\n";

es_check( 'home.php', (bool) (es_file('home.php')) );
es_check( 'single.php', (bool) (es_file('single.php')) );
es_check( 'search.php', (bool) (es_file('search.php')) );
es_check( '404.php', (bool) (es_file('404.php')) );
es_check( 'TOC part', (bool) (es_file('template-parts/post/toc.php')) );
es_check( 'views counter used', (bool) (es_contains('single.php','number2-viewc')) );

es_summary();
