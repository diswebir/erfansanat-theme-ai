<?php
/**
 * Real WordPress search-modal fixture for P08-T004 JSDOM tests.
 * Usage: php build-search-fixture.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';
if ( ! es_load_wp() ) { exit( 1 ); }
require_once ES_THEME . '/functions.php';
require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';

ob_start();
include ES_THEME . '/template-parts/header/search-modal.php';
$html = ob_get_clean();
echo '<!doctype html><html><head></head><body>' . $html . '</body></html>';
