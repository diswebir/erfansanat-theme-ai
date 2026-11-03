<?php
/**
 * P06-T019 — FAQ و توضیحات دسته
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T019', 'FAQ و توضیحات دسته' );

es_check( 'target: erfansanat/template-parts/archive/category-faq.php', es_file('template-parts/archive/category-faq.php', ES_THEME) );

es_pending( 'SC-01: دسته با/بدون FAQ تست شود.' );

es_summary();
