<?php
/**
 * P04-T007 — مگامنوی پروژه‌ها
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Store;

es_task( 'P04-T007', 'مگامنوی پروژه‌ها' );

es_check( 'target: template-parts/header/mega-projects.php', es_file( 'template-parts/header/mega-projects.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	es_check( 'تکسونومی project_type ثبت شده', taxonomy_exists( 'project_type' ) );
	es_check( 'تکسونومی city ثبت شده', taxonomy_exists( 'city' ) );

	$type_term = wp_insert_term( 'نورپردازی میدان', 'project_type' );
	$city_esf  = wp_insert_term( 'اصفهان', 'city' );
	$city_teh  = wp_insert_term( 'تهران', 'city' );
	$type_id   = $type_term['term_id'];
	$esf_id    = $city_esf['term_id'];

	// Two published projects in the type + Esfahan city.
	$p1 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه میدان الف' ) );
	$p2 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه میدان ب' ) );
	wp_set_object_terms( $p1, array( $type_id ), 'project_type' );
	wp_set_object_terms( $p2, array( $type_id ), 'project_type' );
	wp_set_object_terms( $p1, array( $esf_id ), 'city' );
	wp_set_object_terms( $p2, array( $esf_id ), 'city' );

	$render = static function () {
		ob_start();
		get_template_part( 'template-parts/header/mega-projects' );
		return (string) ob_get_clean();
	};

	Store::set( 'mega_projects_group', 'project_type' );
	$html = $render();

	// AC-01: preserved data attributes.
	es_check( 'AC-01: data-mega-proj-tab="type"', false !== strpos( $html, 'data-mega-proj-tab="type"' ) );
	es_check( 'AC-01: data-mega-proj-tab="city"', false !== strpos( $html, 'data-mega-proj-tab="city"' ) );
	es_check( 'AC-01: data-mega-proj-panel="type"', false !== strpos( $html, 'data-mega-proj-panel="type"' ) );
	es_check( 'AC-01: data-project-count حاضر است', false !== strpos( $html, 'data-project-count=' ) );

	// AC-02: terms from real taxonomies.
	es_check( 'AC-02: نوع پروژه از taxonomy می‌آید', false !== strpos( $html, 'نورپردازی میدان' ) );
	es_check( 'AC-02: شهر از taxonomy می‌آید', false !== strpos( $html, 'اصفهان' ) && false !== strpos( $html, 'تهران' ) );

	// AC-03 / SC-02: counter equals the real project count (2).
	es_check( 'AC-03/SC-02: شمارنده نوع برابر ۲ است', false !== strpos( $html, 'data-project-count="2"' ) );
	$esf_term = get_term( $esf_id, 'city' );
	es_check( 'SC-02: شمارنده شهر اصفهان واقعی است', 2 === (int) $esf_term->count );

	// SC-01: default group=type → type panel block, city hidden.
	es_check( 'SC-01: پنل نوع پیش‌فرض block است', false !== strpos( $html, 'data-mega-proj-panel="type" class="block"' ) );
	es_check( 'SC-01: پنل شهر پیش‌فرض hidden است', false !== strpos( $html, 'data-mega-proj-panel="city" class="hidden"' ) );

	// SC-01: switching group to city swaps active panel.
	Store::set( 'mega_projects_group', 'city' );
	$html = $render();
	es_check( 'SC-01: با group=city پنل شهر block می‌شود', false !== strpos( $html, 'data-mega-proj-panel="city" class="block"' ) );
	Store::delete( 'mega_projects_group' );

	// Cleanup.
	wp_delete_post( $p1, true );
	wp_delete_post( $p2, true );
	wp_delete_term( $type_id, 'project_type' );
	wp_delete_term( $esf_id, 'city' );
	wp_delete_term( $city_teh['term_id'], 'city' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
