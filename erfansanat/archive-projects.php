<?php
/**
 * Projects archive (P07-T006). Delegates to the shared project archive shell.
 * The query fetches only published projects (AC-02); the shell preserves the
 * id=archive / data-project-* contract (AC-01) and client-side pagination (AC-03).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_archive_args = apply_filters(
	'es_projects_archive_query_args',
	array(
		'post_type'      => 'projects',
		'post_status'    => 'publish',
		'posts_per_page' => -1, // Client-side pagination needs the full set.
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	)
);
$es_projects = new WP_Query( $es_archive_args );

get_template_part( 'template-parts/archive/project-archive-shell', null, array(
	'query' => $es_projects,
) );

wp_reset_postdata();
get_footer();
