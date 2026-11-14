<?php
/**
 * Real WordPress recruitment fixture for P07-T025 JSDOM tests.
 * Usage: php build-recruit-fixture.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';
if ( ! es_load_wp() ) { exit( 1 ); }
require_once ES_THEME . '/functions.php';

use ErfanSanat\Theme\Options\Store;

Store::delete( 'recruit_tabs' ); Store::delete( 'recruit_jobs' );
$pid = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'استخدام', 'post_name' => 'recruit-dom-' . uniqid() ) );
update_post_meta( $pid, '_wp_page_template', 'page-templates/recruitment.php' );
$q = new WP_Query( array( 'page_id' => $pid, 'post_type' => 'page' ) );
$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
ob_start();
include ES_THEME . '/page-templates/recruitment.php';
$html = ob_get_clean();
wp_delete_post( $pid, true );
echo $html;
