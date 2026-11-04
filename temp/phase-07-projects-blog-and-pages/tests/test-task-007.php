<?php
/**
 * P07-T007 — آرشیو نوع پروژه (taxonomy-project_type.php)
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T007', 'آرشیو نوع پروژه' );

es_check( 'target: taxonomy-project_type.php', es_file( 'taxonomy-project_type.php', ES_THEME ) );
es_check( 'AC-03: از shell و کارت استاندارد استفاده می‌کند', es_contains( 'taxonomy-project_type.php', "get_template_part( 'template-parts/archive/project-archive-shell'" ) );
es_check( 'AC-01: tax_query نوع با زیرترم', es_contains( 'taxonomy-project_type.php', "'taxonomy'         => 'project_type'" ) && es_contains( 'taxonomy-project_type.php', "'include_children' => true" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$type_term  = es_insert_term( 'تونل نوری', 'project_type', array( 'slug' => 'tunnel-' . uniqid() ) )['term_id'];
	$other_term = es_insert_term( 'سایر', 'project_type', array( 'slug' => 'other-' . uniqid() ) )['term_id'];
	wp_update_term( $type_term, 'project_type', array( 'description' => 'پروژه‌های تونل نوری عرفان صنعت' ) );

	$p1 = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'تونل یک', 'post_name' => 'tun1-' . uniqid() ) );
	$p2 = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'تونل دو', 'post_name' => 'tun2-' . uniqid() ) );
	$p3 = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه دیگر', 'post_name' => 'oth-' . uniqid() ) );
	wp_set_object_terms( $p1, array( (int) $type_term ), 'project_type' );
	wp_set_object_terms( $p2, array( (int) $type_term ), 'project_type' );
	wp_set_object_terms( $p3, array( (int) $other_term ), 'project_type' );

	// Simulate the taxonomy archive query so get_queried_object() returns the term.
	$term_query = new WP_Query( array( 'post_type' => 'projects', 'tax_query' => array( array( 'taxonomy' => 'project_type', 'field' => 'term_id', 'terms' => (int) $type_term ) ) ) );
	$GLOBALS['wp_query']     = $term_query;
	$GLOBALS['wp_the_query'] = $term_query;

	$queried = get_queried_object();
	es_check( 'AC-01: ترم فارسی resolve شد', $queried instanceof WP_Term && 'تونل نوری' === $queried->name );

	ob_start();
	include ES_THEME . '/taxonomy-project_type.php';
	$html = (string) ob_get_clean();

	es_check( 'SC-01: آرشیو نوع رندر شد', false !== strpos( $html, 'id="archive"' ) );
	$es_grid_start = strpos( $html, 'data-project-grid="true"' );
	$es_grid_end   = strpos( $html, 'data-project-empty="true"' );
	$es_grid_html  = ( false !== $es_grid_start && false !== $es_grid_end ) ? substr( $html, $es_grid_start, $es_grid_end - $es_grid_start ) : '';
	es_check( 'AC-03: فقط پروژه‌های ترم در گرید (۲ کارت)', 2 === substr_count( $es_grid_html, 'data-project-item="true"' ) && false === strpos( $es_grid_html, 'پروژه دیگر' ) );
	es_check( 'AC-02: عنوان ترم در هیرو', false !== strpos( $html, 'تونل نوری' ) );
	es_check( 'AC-02: توضیح ترم نمایش داده شد', false !== strpos( $html, 'data-term-description="true"' ) && false !== strpos( $html, 'پروژه‌های تونل نوری عرفان صنعت' ) );
	es_check( 'AC-01: فیلتر اولیه نوع = نام ترم', false !== strpos( $html, 'data-initial-type="تونل نوری"' ) );
	es_check( 'SC-01: لینک ترم اسلاگ فارسی/واقعی دارد', false !== strpos( get_term_link( (int) $type_term, 'project_type' ), 'project_type' ) );

	foreach ( array( $p1, $p2, $p3 ) as $pid ) {
		wp_delete_post( $pid, true );
	}
	wp_delete_term( $type_term, 'project_type' );
	wp_delete_term( $other_term, 'project_type' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
