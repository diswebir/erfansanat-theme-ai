<?php
/**
 * Real WordPress FAQ page fixture for P07-T023 JSDOM tests.
 * Usage: php build-faq-fixture.php
 * Renders the FAQ template with the reference questions; fixture deleted after.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';
if ( ! es_load_wp() ) { exit( 1 ); }
require_once ES_THEME . '/functions.php';

use ErfanSanat\Theme\Options\Store;

Store::delete( 'faq_items' );
$pid = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'سوالات متداول', 'post_name' => 'faq-dom-' . uniqid() ) );
update_post_meta( $pid, '_wp_page_template', 'page-templates/faq.php' );
$q = new WP_Query( array( 'page_id' => $pid, 'post_type' => 'page' ) );
$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
ob_start();
include ES_THEME . '/page-templates/faq.php';
$html = ob_get_clean();
wp_delete_post( $pid, true );
echo $html;
