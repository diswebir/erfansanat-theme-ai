<?php
/**
 * Task 6.2 — آرشیو محصولات + FAQ دسته + صفحه‌بندی
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-02.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 6.2: آرشیو محصولات + FAQ دسته + صفحه‌بندی ==\n";

es_check( 'archive-product.php', (bool) (es_file('woocommerce/archive-product.php')) );
es_check( 'taxonomy-product_cat.php', (bool) (es_file('woocommerce/taxonomy-product_cat.php')) );
es_check( 'taxonomy-product_tag.php', (bool) (es_file('woocommerce/taxonomy-product_tag.php')) );
es_check( 'category FAQ', (bool) (es_contains('woocommerce/taxonomy-product_cat.php','faq')) );

es_summary();
