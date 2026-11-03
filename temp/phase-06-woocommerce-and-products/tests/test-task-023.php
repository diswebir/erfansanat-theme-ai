<?php
/**
 * P06-T023 — Mini-cart و AJAX سبد
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-023.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T023', 'Mini-cart و AJAX سبد' );

es_check( 'target: erfansanat/template-parts/header/cart-modal.php', es_file('template-parts/header/cart-modal.php', ES_THEME) );
es_check( 'target: erfansanat/inc/Core/CartFragments.php', es_file('inc/Core/CartFragments.php', ES_THEME) );

es_pending( 'SC-01: افزودن محصول badge و مودال را به‌روز کند.' );
es_pending( 'SC-02: حذف ردیف جمع را صفر کند.' );

es_summary();
