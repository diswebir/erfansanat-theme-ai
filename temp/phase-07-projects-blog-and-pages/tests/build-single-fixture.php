<?php
/**
 * Real WordPress single-post fixture for P07-T013+ JSDOM tests.
 * Usage: php build-single-fixture.php [headings|none|protected]
 * Fixture content is deleted after buffering the rendered template.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';
if ( ! es_load_wp() ) { exit( 1 ); }
require_once ES_THEME . '/functions.php';

$mode = (string) ( $argv[1] ?? 'headings' );
$content = '<h2>تونل نوری کمانی</h2><p>متن بخش یک</p><h3>زیربخش فنی</h3><p>متن زیربخش</p><h2>تونل نوری مربعی</h2><p>متن بخش دو</p><h2>تونل نوری کمانی</h2><p>بخش تکراری</p>';
if ( 'none' === $mode ) { $content = '<p>مقاله بدون هدینگ</p>'; }
$cat = es_insert_term( 'مجله علمی', 'category', array( 'slug' => 'single-dom-' . uniqid() ) )['term_id'];
$pid = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'مقاله_DOM_فهرست', 'post_excerpt' => 'خلاصه مقاله', 'post_content' => $content, 'post_name' => 'single-dom-' . uniqid(), 'post_password' => 'protected' === $mode ? 'secret' : '' ) );
wp_set_object_terms( $pid, array( (int) $cat ), 'category' );
$q = new WP_Query( array( 'p' => $pid, 'post_type' => 'post' ) );
$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
ob_start();
include ES_THEME . '/single.php';
$html = ob_get_clean();
wp_delete_post( $pid, true );
wp_delete_term( $cat, 'category' );
echo $html;
