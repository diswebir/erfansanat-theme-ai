<?php
/**
 * Task 5.1 — برش ۱۵ سکشن + front-page.php با SectionManager
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-01.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 5.1: برش ۱۵ سکشن + front-page.php با SectionManager ==\n";

es_check( 'front-page.php exists', (bool) (es_file('front-page.php')) );
es_check( 'hero section', (bool) (es_file('template-parts/home/section-hero.php')) );
es_check( 'projects section', (bool) (es_file('template-parts/home/section-featured-projects.php')) );
es_check( 'products section', (bool) (es_file('template-parts/home/section-products.php')) );
es_check( 'blog section', (bool) (es_file('template-parts/home/section-blog.php')) );
es_check( 'SectionManager loop', (bool) (es_contains('front-page.php','SectionManager')) );

es_summary();
