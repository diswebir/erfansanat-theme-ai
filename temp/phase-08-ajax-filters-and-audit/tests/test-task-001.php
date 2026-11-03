<?php
/**
 * P08-T001 — قرارداد و امنیت AJAX
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P08-T001', 'قرارداد و امنیت AJAX' );

es_check( 'target: erfansanat/inc/Core/Ajax.php', es_file('inc/Core/Ajax.php', ES_THEME) );

es_pending( 'SC-01: nonce نامعتبر 403 بدهد.' );
es_pending( 'SC-02: ورودی مخرب XSS/SQLi رد شود.' );
es_pending( 'SC-03: پاسخ معتبر JSON schema داشته باشد.' );

es_summary();
