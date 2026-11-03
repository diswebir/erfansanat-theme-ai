<?php
/**
 * P06-T001 — Shim سازگاری ووکامرس برای QA
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T001', 'Shim سازگاری ووکامرس برای QA' );

es_check( 'target: temp/tests/woocommerce-shim.php', es_file('tests/woocommerce-shim.php', ES_ROOT) );

es_pending( 'SC-01: تست محصول بدون Woo اجرا شود.' );
es_pending( 'SC-02: با Woo واقعی shim غیرفعال بماند.' );

es_summary();
