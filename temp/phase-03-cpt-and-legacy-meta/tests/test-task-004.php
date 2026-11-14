<?php
/**
 * P03-T004 — ثبت تکسونومی city
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-004.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P03-T004', 'ثبت تکسونومی city' );

es_check( 'target: inc/Core/Taxonomies.php', es_file( 'inc/Core/Taxonomies.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	es_check( 'city ثبت شده است', taxonomy_exists( 'city' ) );
	$tax = get_taxonomy( 'city' );

	// AC-01: rewrite slug=city, with_front=false.
	es_check( 'AC-01: rewrite slug=city', is_array( $tax->rewrite ) && 'city' === $tax->rewrite['slug'] );
	es_check( 'AC-01: with_front=false', is_array( $tax->rewrite ) && false === $tax->rewrite['with_front'] );
	es_check( 'AC-01: hierarchical=true', true === $tax->hierarchical );
	es_check( 'AC-01: به projects متصل است', in_array( 'projects', (array) $tax->object_type, true ) );
	es_check( 'AC-03: show_admin_column برای فیلتر ادمین', true === $tax->show_admin_column );

	// SC-01 + AC-02: Persian term اصفهان → /city/<percent-encoded>/.
	$term = wp_insert_term( 'اصفهان', 'city' );
	es_check( 'SC-01: ترم اصفهان ساخته می‌شود', ! is_wp_error( $term ) );
	$term_obj = get_term( $term['term_id'], 'city' );
	$link     = get_term_link( $term_obj );
	es_check( 'SC-01: لینک /city/ دارد', ! is_wp_error( $link ) && false !== strpos( $link, '/city/' ) );
	es_check( 'AC-02: اسلاگ فارسی percent-encoded است', false !== strpos( $term_obj->slug, '%' ) );
	es_check( 'AC-02: لینک شامل اسلاگ encode‌شده است', ! is_wp_error( $link ) && false !== strpos( $link, $term_obj->slug ) );

	// A project in this city resolves via tax_query.
	$pid = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه اصفهان تست' ) );
	wp_set_object_terms( $pid, array( (int) $term['term_id'] ), 'city' );
	$q = new WP_Query( array( 'post_type' => 'projects', 'tax_query' => array( array( 'taxonomy' => 'city', 'field' => 'term_id', 'terms' => array( $term['term_id'] ) ) ) ) );
	es_check( 'SC-01: کوئری شهر فارسی کار می‌کند', 1 === $q->found_posts );

	// SC-02: renaming the term does not break its link (slug preserved).
	$slug_before = $term_obj->slug;
	wp_update_term( $term['term_id'], 'city', array( 'name' => 'اصفهان بزرگ' ) );
	$term_after = get_term( $term['term_id'], 'city' );
	$link_after = get_term_link( $term_after );
	es_check( 'SC-02: تغییر نام اسلاگ را نمی‌شکند', $term_after->slug === $slug_before );
	es_check( 'SC-02: لینک پس از تغییر نام سالم است', ! is_wp_error( $link_after ) && false !== strpos( $link_after, $slug_before ) );

	wp_delete_post( $pid, true );
	wp_delete_term( $term['term_id'], 'city' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
