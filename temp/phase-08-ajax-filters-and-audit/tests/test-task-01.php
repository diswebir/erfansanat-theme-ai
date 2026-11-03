<?php
/**
 * Task 8.1 — Live AJAX Search + Smart Filters
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-01.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 8.1: Live AJAX Search + Smart Filters ==\n";

es_check( 'AjaxSearch module', (bool) (es_file('inc/Core/AjaxSearch.php')) );
es_check( 'SmartFilters module', (bool) (es_file('inc/Core/SmartFilters.php')) );
es_check( 'nonce in search', (bool) (es_contains('inc/Core/AjaxSearch.php','nonce')) );
es_check( 'true_sale filter', (bool) (es_contains('inc/Core/SmartFilters.php','true_sale')) );
es_check( 'city filter', (bool) (es_contains('inc/Core/SmartFilters.php','city')) );
// WP-dependent checks run ONLY when our theme is active (avoids false
// positives from the default theme that ships with the QA install).
if ( es_activate_theme() ) {
  es_check( 'ajax search hook', (bool) (has_action('wp_ajax_es_ajax_search')) );
  es_check( 'ajax filters hook', (bool) (has_action('wp_ajax_es_ajax_filters')) );
} else {
  es_skip( 'ajax search hook', 'erfansanat theme not active yet' );
  es_skip( 'ajax filters hook', 'erfansanat theme not active yet' );
}
es_summary();
