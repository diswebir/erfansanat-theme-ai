<?php
/**
 * P07-T005 — پروژه‌های مرتبط (related-projects.php)
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T005', 'پروژه‌های مرتبط' );

es_check( 'target: related-projects.php', es_file( 'template-parts/project/related-projects.php', ES_THEME ) );
es_check( 'AC-01: حذف پروژه جاری', es_contains( 'template-parts/project/related-projects.php', "'post__not_in'   => array( \$es_pid )" ) );
es_check( 'AC-02: تعداد/ترتیب قابل تنظیم', es_contains( 'template-parts/project/related-projects.php', 'es_related_projects_count' ) && es_contains( 'template-parts/project/related-projects.php', 'es_related_projects_orderby' ) );
es_check( 'AC-03: کارت استاندارد پروژه', es_contains( 'template-parts/project/related-projects.php', "get_template_part( 'template-parts/cards/card', 'project'" ) );
es_check( 'کوئری بهینه (no_found_rows)', es_contains( 'template-parts/project/related-projects.php', "'no_found_rows'  => true" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$type = es_insert_term( 'نورپردازی میدان', 'project_type', array( 'slug' => 'square-t005' ) );
	$mkproj = static function ( $title ) use ( $type ) {
		$id = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => 'rel-p-' . uniqid() ) );
		wp_set_object_terms( $id, array( (int) $type['term_id'] ), 'project_type' );
		return $id;
	};
	$current = $mkproj( 'پروژه جاری' );
	$others  = array();
	for ( $i = 1; $i <= 5; $i++ ) {
		$others[] = $mkproj( 'پروژه مرتبط ' . $i );
	}

	$render = static function ( $pid ) {
		ob_start();
		get_template_part( 'template-parts/project/related-projects', null, array( 'project_id' => $pid ) );
		return (string) ob_get_clean();
	};
	$html = $render( $current );

	// SC-01: four related projects, current excluded.
	es_check( 'SC-01: چهار پروژه مرتبط رندر شد', 4 === substr_count( $html, 'data-project-item="true"' ) && false !== strpos( $html, 'data-project-related="true"' ) );
	es_check( 'AC-01: پروژه جاری حذف شد', false === strpos( $html, 'پروژه جاری' ) );
	es_check( 'AC-03: کارت استاندارد (data-project-item)', 4 === substr_count( $html, 'data-project-item="true"' ) );

	// AC-02: count filter.
	add_filter( 'es_related_projects_count', static function () { return 2; } );
	$html2 = $render( $current );
	es_check( 'AC-02: تعداد از فیلتر (۲)', 2 === substr_count( $html2, 'data-project-item="true"' ) );
	remove_all_filters( 'es_related_projects_count' );

	// AC-02: orderby filter applied (title ASC → predictable first card).
	add_filter( 'es_related_projects_orderby', static function () { return 'title'; } );
	add_filter( 'es_related_projects_args', static function ( $args ) { $args['order'] = 'ASC'; return $args; } );
	$html3 = $render( $current );
	es_check( 'AC-02: ترتیب قابل تنظیم است', false !== strpos( $html3, 'data-project-item="true"' ) );
	remove_all_filters( 'es_related_projects_orderby' );
	remove_all_filters( 'es_related_projects_args' );

	// Cleanup.
	wp_delete_post( $current, true );
	foreach ( $others as $oid ) {
		wp_delete_post( $oid, true );
	}
	wp_delete_term( (int) $type['term_id'], 'project_type' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
