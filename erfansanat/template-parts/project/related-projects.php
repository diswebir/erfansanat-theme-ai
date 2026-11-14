<?php
/**
 * Related projects (P07-T005). An optimised query (no_found_rows, bounded count)
 * that excludes the current project (AC-01), with filterable count/order
 * (AC-02), rendered through the standard project card (AC-03).
 *
 * @package ErfanSanat
 *
 * @var array $args { project_id: int }
 */

defined( 'ABSPATH' ) || exit;

$es_pid = isset( $args['project_id'] ) ? (int) $args['project_id'] : (int) get_the_ID();
if ( ! $es_pid || 'projects' !== get_post_type( $es_pid ) ) {
	return;
}

$es_count = (int) apply_filters( 'es_related_projects_count', 4, $es_pid );
$es_count = max( 1, $es_count );

$es_query_args = array(
	'post_type'      => 'projects',
	'post_status'    => 'publish',
	'posts_per_page' => $es_count,
	'post__not_in'   => array( $es_pid ),
	'orderby'        => apply_filters( 'es_related_projects_orderby', 'date', $es_pid ),
	'order'          => 'DESC',
	'no_found_rows'  => true,
);

// Prefer projects sharing the current project's type.
$es_types = get_the_terms( $es_pid, 'project_type' );
if ( $es_types && ! is_wp_error( $es_types ) ) {
	$es_query_args['tax_query'] = array(
		array(
			'taxonomy' => 'project_type',
			'field'    => 'term_id',
			'terms'    => wp_list_pluck( $es_types, 'term_id' ),
		),
	);
}

/** Filter the related-projects query args. */
$es_query_args = apply_filters( 'es_related_projects_args', $es_query_args, $es_pid );

$es_related = new WP_Query( $es_query_args );

// Fallback: if the type-scoped query is short, fill with other projects.
if ( $es_related->post_count < $es_count ) {
	$es_have = wp_list_pluck( $es_related->posts, 'ID' );
	$es_fill_args = $es_query_args;
	unset( $es_fill_args['tax_query'] );
	$es_fill_args['post__not_in'] = array_merge( array( $es_pid ), $es_have );
	$es_fill_args['posts_per_page'] = $es_count - $es_related->post_count;
	$es_fill = new WP_Query( $es_fill_args );
	if ( $es_fill->have_posts() ) {
		$es_related->posts = array_merge( $es_related->posts, $es_fill->posts );
		$es_related->post_count = count( $es_related->posts );
	}
}

if ( ! $es_related->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4" data-project-related="true">
	<?php
	foreach ( $es_related->posts as $es_related_post ) {
		get_template_part( 'template-parts/cards/card', 'project', array( 'project_id' => (int) $es_related_post->ID ) );
	}
	?>
</div>
<?php
wp_reset_postdata();
