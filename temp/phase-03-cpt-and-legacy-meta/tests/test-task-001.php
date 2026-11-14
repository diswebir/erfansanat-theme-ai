<?php
/**
 * P03-T001 — ثبت CPT projects
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\PostTypes;

es_task( 'P03-T001', 'ثبت CPT projects' );

es_check( 'target: inc/Core/PostTypes.php', es_file( 'inc/Core/PostTypes.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// AC-01/02/03: registration args match the blueprint exactly.
	es_check( 'projects ثبت شده است', post_type_exists( 'projects' ) );
	$obj = get_post_type_object( 'projects' );

	es_check( 'AC-01: public=true', true === $obj->public );
	es_check( 'AC-01: has_archive=true', true === $obj->has_archive );
	es_check( 'AC-01: hierarchical=false', false === $obj->hierarchical );
	es_check( 'AC-01: menu_icon دقیق', 'dashicons-screenoptions' === $obj->menu_icon );
	$supports = array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions' );
	$all_supported = true;
	foreach ( $supports as $s ) {
		$all_supported = $all_supported && post_type_supports( 'projects', $s );
	}
	es_check( 'AC-01: supports مطابق بلوپرینت', $all_supported );
	es_check( 'AC-02: show_in_rest=true', true === $obj->show_in_rest );
	es_check( 'AC-03: rewrite slug=projects', is_array( $obj->rewrite ) && 'projects' === $obj->rewrite['slug'] );
	es_check( 'AC-03: with_front=false', is_array( $obj->rewrite ) && false === $obj->rewrite['with_front'] );

	// SC-01: a project's permalink is /projects/slug/.
	$pid = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه تست تونل', 'post_name' => 'test-tunnel-t001' ) );
	$link = get_permalink( $pid );
	es_check( 'SC-01: permalink = /projects/slug/', false !== strpos( $link, '/projects/test-tunnel-t001/' ) );

	// SC-02: the projects archive is active.
	$archive = get_post_type_archive_link( 'projects' );
	es_check( 'SC-02: لینک آرشیو /projects/', is_string( $archive ) && false !== strpos( $archive, '/projects/' ) );
	$q = new WP_Query( array( 'post_type' => 'projects', 'p' => $pid ) );
	es_check( 'SC-02: کوئری آرشیو پروژه را برمی‌گرداند', 1 === $q->found_posts );

	// No duplicate registration: calling register() again is a safe no-op.
	PostTypes::register();
	es_check( 'ثبت مجدد fatal/تغییر نمی‌دهد', post_type_exists( 'projects' ) && 'projects' === get_post_type_object( 'projects' )->rewrite['slug'] );
	es_check( 'AC-03: سورس guard ثبت تکراری دارد', es_contains( 'inc/Core/PostTypes.php', 'post_type_exists' ) );

	wp_delete_post( $pid, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
