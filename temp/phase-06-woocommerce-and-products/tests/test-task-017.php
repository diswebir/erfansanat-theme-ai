<?php
/**
 * P06-T017 — Parity کارت محصول در آرشیو
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-017.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T017', 'Parity کارت محصول در آرشیو' );

es_check( 'target: erfansanat/woocommerce/content-product.php', es_file('woocommerce/content-product.php', ES_THEME) );

es_pending( 'SC-01: اسنپ‌شات کارت با مرجع مقایسه شود.' );
es_pending( 'SC-02: محصول استعلامی دکمه درست داشته باشد.' );

es_summary();
