<?php
/**
 * P03-T003 — ثبت تکسونومی project_type
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T003', 'ثبت تکسونومی project_type' );

es_check( 'target: inc/Core/Taxonomies.php', es_file( 'inc/Core/Taxonomies.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	es_check( 'project_type ثبت شده است', taxonomy_exists( 'project_type' ) );
	$tax = get_taxonomy( 'project_type' );

	// AC-01: hierarchical + exact rewrite.
	es_check( 'AC-01: hierarchical=true', true === $tax->hierarchical );
	es_check( 'AC-01: rewrite slug=project_type', is_array( $tax->rewrite ) && 'project_type' === $tax->rewrite['slug'] );
	es_check( 'AC-01: with_front=false', is_array( $tax->rewrite ) && false === $tax->rewrite['with_front'] );
	es_check( 'AC-01: rewrite hierarchical=false', is_array( $tax->rewrite ) && false === $tax->rewrite['hierarchical'] );
	es_check( 'AC-01: show_in_rest=true', true === $tax->show_in_rest );

	// AC-02: attached to projects.
	es_check( 'AC-02: به projects متصل است', in_array( 'projects', (array) $tax->object_type, true ) );

	// SC-01 + AC-03: Persian term keeps its own slug and gets a correct link.
	$parent = wp_insert_term( 'تونل نوری', 'project_type' );
	es_check( 'SC-01: ترم فارسی ساخته می‌شود', ! is_wp_error( $parent ) );
	$parent_term = get_term( $parent['term_id'], 'project_type' );
	$link        = get_term_link( $parent_term );
	es_check( 'SC-01: لینک ترم شامل project_type است', ! is_wp_error( $link ) && false !== strpos( $link, 'project_type' ) );
	es_check( 'AC-03: اسلاگ فارسی دست‌نخورده می‌ماند', ! is_wp_error( $link ) && false !== strpos( $link, $parent_term->slug ) );

	// SC-02: a child term resolves (flat rewrite, no 404 / WP_Error).
	$child = wp_insert_term( 'تونل نوری شهری', 'project_type', array( 'parent' => $parent['term_id'] ) );
	es_check( 'SC-02: زیرترم ساخته می‌شود', ! is_wp_error( $child ) );
	$child_term = get_term( $child['term_id'], 'project_type' );
	$child_link = get_term_link( $child_term );
	es_check( 'SC-02: لینک زیرترم بدون خطا', ! is_wp_error( $child_link ) && false !== strpos( $child_link, $child_term->slug ) );
	es_check( 'SC-02: rewrite تخت (بدون مسیر والد)', ! is_wp_error( $child_link ) && false === strpos( $child_link, $parent_term->slug . '/' ) );

	// A projects query filtered by the Persian term works.
	$pid = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه تونل تست' ) );
	wp_set_object_terms( $pid, array( (int) $parent['term_id'] ), 'project_type' );
	$q = new WP_Query( array( 'post_type' => 'projects', 'tax_query' => array( array( 'taxonomy' => 'project_type', 'field' => 'term_id', 'terms' => array( $parent['term_id'] ) ) ) ) );
	es_check( 'SC-01: کوئری با ترم فارسی کار می‌کند', 1 === $q->found_posts );

	wp_delete_post( $pid, true );
	wp_delete_term( $child['term_id'], 'project_type' );
	wp_delete_term( $parent['term_id'], 'project_type' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
