<?php
/**
 * P06-T020 — آرشیو برچسب/برند
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-020.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T020', 'آرشیو برچسب/برند' );

es_check( 'target: erfansanat/woocommerce/taxonomy-product_tag.php', es_file('woocommerce/taxonomy-product_tag.php', ES_THEME) );
es_check( 'target: erfansanat/woocommerce/taxonomy-product_brand.php', es_file('woocommerce/taxonomy-product_brand.php', ES_THEME) );

es_pending( 'SC-01: آرشیو برند و برچسب 200 بدهند.' );

es_summary();
