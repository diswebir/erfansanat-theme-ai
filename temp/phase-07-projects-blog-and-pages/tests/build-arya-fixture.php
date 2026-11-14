<?php
/**
 * Real WordPress Arya landing fixture for P07-T028 JSDOM tests.
 * Usage: php build-arya-fixture.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';
if ( ! es_load_wp() ) { exit( 1 ); }
require_once ES_THEME . '/functions.php';

$old_locs = get_theme_mod( 'nav_menu_locations', array() );
$locs = $old_locs; unset( $locs['landing'] ); set_theme_mod( 'nav_menu_locations', $locs );

$pid = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'آریا', 'post_name' => 'arya-dom-' . uniqid() ) );
update_post_meta( $pid, '_wp_page_template', 'page-templates/landing-arya.php' );
$q = new WP_Query( array( 'page_id' => $pid, 'post_type' => 'page' ) );
$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
ob_start();
include ES_THEME . '/page-templates/landing-arya.php';
$html = ob_get_clean();
wp_delete_post( $pid, true );
set_theme_mod( 'nav_menu_locations', $old_locs );
echo $html;
