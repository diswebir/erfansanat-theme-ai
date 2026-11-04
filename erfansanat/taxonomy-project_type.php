<?php
/**
 * Project type archive (P07-T007). Reuses the shared project archive shell with
 * the queried term's title/description and a term-scoped published query. Persian
 * terms/subterms resolve through the WordPress queried object (AC-01); the term
 * count/description come from the term itself (AC-02); standard project cards are
 * rendered by the shell (AC-03).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_term = get_queried_object();

$es_type_args = apply_filters(
	'es_project_type_archive_query_args',
	array(
		'post_type'      => 'projects',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
		'tax_query'      => array(
			array(
				'taxonomy'         => 'project_type',
				'field'            => 'term_id',
				'terms'            => ( $es_term instanceof WP_Term ) ? (int) $es_term->term_id : 0,
				'include_children' => true,
			),
		),
	)
);
$es_projects = new WP_Query( $es_type_args );

get_template_part( 'template-parts/archive/project-archive-shell', null, array(
	'query'        => $es_projects,
	'heading'      => ( $es_term instanceof WP_Term ) ? $es_term->name : __( 'پروژه‌های نورپردازی شهری', 'erfansanat' ),
	'subtitle'     => ( $es_term instanceof WP_Term && '' !== trim( (string) $es_term->description ) ) ? $es_term->description : __( 'پروژه‌های اجراشده عرفان صنعت در این دسته.', 'erfansanat' ),
	'initial_type' => ( $es_term instanceof WP_Term ) ? $es_term->name : 'همه',
	'term'         => ( $es_term instanceof WP_Term ) ? $es_term : null,
) );

wp_reset_postdata();
get_footer();
