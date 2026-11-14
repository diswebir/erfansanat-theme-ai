<?php
/**
 * City archive (P07-T008). Reuses the shared project archive shell with the
 * queried city's title/description and a term-scoped published query. Persian
 * slugs resolve through the WordPress queried object without hardcoded English
 * slugs (AC-01); the term description/count come from the term (AC-02); a city
 * with no projects renders the shell's safe empty state (AC-03).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_term = get_queried_object();

$es_city_args = apply_filters(
	'es_city_archive_query_args',
	array(
		'post_type'      => 'projects',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
		'tax_query'      => array(
			array(
				'taxonomy'         => 'city',
				'field'            => 'term_id',
				'terms'            => ( $es_term instanceof WP_Term ) ? (int) $es_term->term_id : 0,
				'include_children' => true,
			),
		),
	)
);
$es_projects = new WP_Query( $es_city_args );

get_template_part( 'template-parts/archive/project-archive-shell', null, array(
	'query'            => $es_projects,
	'heading'          => ( $es_term instanceof WP_Term ) ? $es_term->name : __( 'پروژه‌های نورپردازی شهری', 'erfansanat' ),
	'subtitle'         => ( $es_term instanceof WP_Term && '' !== trim( (string) $es_term->description ) ) ? $es_term->description : __( 'پروژه‌های اجراشده عرفان صنعت در این شهر.', 'erfansanat' ),
	'initial_location' => ( $es_term instanceof WP_Term ) ? $es_term->name : 'همه',
	'term'             => ( $es_term instanceof WP_Term ) ? $es_term : null,
) );

wp_reset_postdata();
get_footer();
