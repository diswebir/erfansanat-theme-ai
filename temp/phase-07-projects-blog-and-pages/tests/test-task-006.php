<?php
/**
 * P07-T006 — آرشیو پروژه‌ها (archive-projects.php)
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-006.php
 * Client-side pagination: test-task-006-dom.mjs
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T006', 'آرشیو پروژه‌ها' );

es_check( 'target: archive-projects.php', es_file( 'archive-projects.php', ES_THEME ) );
es_check( 'AC-01: id=archive و data-projects-archive', es_contains( 'template-parts/archive/project-archive-shell.php', 'id="archive"' ) && es_contains( 'template-parts/archive/project-archive-shell.php', 'data-projects-archive="true"' ) );
es_check( 'AC-01: قرارداد data-project-*', es_contains( 'template-parts/archive/project-archive-shell.php', 'data-project-grid="true"' ) && es_contains( 'template-parts/archive/project-archive-shell.php', 'data-project-search="true"' ) && es_contains( 'template-parts/archive/project-archive-shell.php', 'data-project-sort="true"' ) );
es_check( 'AC-02: کوئری فقط منتشرشده', es_contains( 'archive-projects.php', "'post_status'    => 'publish'" ) && es_contains( 'archive-projects.php', "'post_type'      => 'projects'" ) );
es_check( 'AC-03: صفحه‌بندی', es_contains( 'template-parts/archive/project-archive-shell.php', "get_template_part( 'template-parts/archive/pagination'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$ids = array();
	for ( $i = 1; $i <= 8; $i++ ) {
		$ids[] = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه ' . $i, 'post_name' => 'arch-p-' . $i . '-' . uniqid() ) );
	}
	$draft = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'draft', 'post_title' => 'پروژه پیش‌نویس', 'post_name' => 'arch-draft-' . uniqid() ) );
	$all   = array_merge( $ids, array( $draft ) );

	// Scope to fixtures (includes the draft to prove post_status filtering).
	add_filter( 'es_projects_archive_query_args', static function ( $args ) use ( $all ) {
		$args['post__in'] = $all;
		$args['orderby']  = 'post__in';
		return $args;
	} );

	ob_start();
	include ES_THEME . '/archive-projects.php';
	$html = (string) ob_get_clean();

	es_check( 'SC-01: آرشیو کارت‌ها را رندر می‌کند', false !== strpos( $html, 'id="archive"' ) && 8 === substr_count( $html, 'data-project-item="true"' ) );
	es_check( 'AC-02: پیش‌نویس رندر نشد', false === strpos( $html, 'پروژه پیش‌نویس' ) && 8 === substr_count( $html, 'data-project-item="true"' ) );
	es_check( 'AC-01: گرید و شمارنده', false !== strpos( $html, 'data-project-grid="true"' ) && false !== strpos( $html, 'data-project-count="true"' ) );
	es_check( 'AC-03: صفحه‌بندی دو صفحه (۸/۶)', false !== strpos( $html, 'data-pagination-root="true"' ) && 2 === substr_count( $html, 'data-page-btn=' ) );
	es_check( 'AC-03: data-per-page=6', false !== strpos( $html, 'data-per-page="6"' ) );
	es_check( 'AC-01: کارت تاریخ برای مرتب‌سازی دارد', false !== strpos( $html, 'data-project-date=' ) );

	// Empty archive renders empty state without fatal.
	remove_all_filters( 'es_projects_archive_query_args' );
	add_filter( 'es_projects_archive_query_args', static function ( $args ) { $args['post__in'] = array( 0 ); return $args; } );
	ob_start();
	include ES_THEME . '/archive-projects.php';
	$empty_html = (string) ob_get_clean();
	es_check( 'AC-02: آرشیو خالی حالت خالی می‌دهد', false !== strpos( $empty_html, 'data-project-empty="true" class="block' ) && 0 === substr_count( $empty_html, 'data-project-item="true"' ) );

	foreach ( $all as $pid ) {
		wp_delete_post( $pid, true );
	}
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
