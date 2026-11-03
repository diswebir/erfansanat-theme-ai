<?php
/**
 * P03-T002 — ثبت CPT services
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T002', 'ثبت CPT services' );

es_check( 'target: inc/Core/PostTypes.php', es_file( 'inc/Core/PostTypes.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	es_check( 'services ثبت شده است', post_type_exists( 'services' ) );
	$obj = get_post_type_object( 'services' );

	// AC-01: no independent archive; rewrite slug=services.
	es_check( 'AC-01: has_archive=false', false === $obj->has_archive );
	es_check( 'AC-01: rewrite slug=services', is_array( $obj->rewrite ) && 'services' === $obj->rewrite['slug'] );
	es_check( 'AC-01: with_front=false', is_array( $obj->rewrite ) && false === $obj->rewrite['with_front'] );
	es_check( 'AC-01: public=true', true === $obj->public );

	// AC-02: supports per decision D2.
	$supports = array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' );
	$all = true;
	foreach ( $supports as $s ) {
		$all = $all && post_type_supports( 'services', $s );
	}
	es_check( 'AC-02: supports مطابق D2', $all );
	es_check( 'AC-02: show_in_rest=true', true === $obj->show_in_rest );

	// SC-01: single service permalink /services/slug/.
	$sid = wp_insert_post( array( 'post_type' => 'services', 'post_status' => 'publish', 'post_title' => 'نورپردازی پل تست', 'post_name' => 'test-bridge-t002' ) );
	es_check( 'SC-01: permalink = /services/slug/', false !== strpos( get_permalink( $sid ), '/services/test-bridge-t002/' ) );

	// SC-02: no independent archive (archive link is false).
	es_check( 'SC-02: آرشیو مستقل ندارد', false === get_post_type_archive_link( 'services' ) );

	// AC-03: coexists with the smartelement page (no slug/rewrite conflict).
	$page_id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'خدمات شرکت', 'post_name' => 'smartelement' ) );
	$page_link = get_permalink( $page_id );
	es_check( 'AC-03: برگه smartelement مستقل resolve می‌شود', false !== strpos( $page_link, '/smartelement/' ) && false === strpos( $page_link, '/services/' ) );
	$by_path = get_page_by_path( 'smartelement' );
	es_check( 'AC-03: تداخل مسیر با برگه ندارد', $by_path && (int) $by_path->ID === (int) $page_id );

	wp_delete_post( $sid, true );
	wp_delete_post( $page_id, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
