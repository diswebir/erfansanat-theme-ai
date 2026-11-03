<?php
/**
 * Task 6.1 — قالب تک‌محصول (single-product)
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-01.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 6.1: قالب تک‌محصول (single-product) ==\n";

es_check( 'single-product.php exists', (bool) (es_file('woocommerce/single-product.php')) );
es_check( 'content-single-product.php', (bool) (es_file('woocommerce/content-single-product.php')) );
es_check( 'galerry_produ used', (bool) (es_contains('woocommerce/content-single-product.php','galerry_produ')) );
es_check( 'true_sale logic', (bool) (es_contains('woocommerce/content-single-product.php','true_sale')) );
es_check( 'english_titl used', (bool) (es_contains('woocommerce/content-single-product.php','english_titl')) );

es_summary();
