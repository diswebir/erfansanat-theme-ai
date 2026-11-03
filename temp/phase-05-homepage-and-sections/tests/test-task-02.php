<?php
/**
 * Task 5.2 — اتصال داینامیک سکشن‌ها + کارت‌های استاندارد
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-02.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 5.2: اتصال داینامیک سکشن‌ها + کارت‌های استاندارد ==\n";

es_check( 'card-product exists', (bool) (es_file('template-parts/cards/card-product.php')) );
es_check( 'card-project exists', (bool) (es_file('template-parts/cards/card-project.php')) );
es_check( 'card-post exists', (bool) (es_file('template-parts/cards/card-post.php')) );
es_check( 'hero project selection', (bool) (es_contains('template-parts/home/section-hero.php','project')) );
es_check( 'dynamic product data attr', (bool) (es_contains('template-parts/cards/card-product.php','data-product-price')) );

es_summary();
