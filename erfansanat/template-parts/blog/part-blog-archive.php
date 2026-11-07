<?php
/**
 * Shared body of the blog index and the category archive.
 *
 * The two references differ only in the hero wording and the active chip, so one
 * body serves both. Paging is the main query's own `?page=N`, which keeps the
 * archive usable without JavaScript.
 *
 * @package ErfanSanat
 *
 * @var string $erfansanat_active_category Category name to preselect, empty on the index.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_active_category = isset( $erfansanat_active_category ) ? (string) $erfansanat_active_category : '';
$erfansanat_is_category     = '' !== $erfansanat_active_category;

$erfansanat_per_page = (int) get_option( 'posts_per_page', 6 );
$erfansanat_per_page = max( 1, (int) apply_filters( 'erfansanat_blog_per_page', $erfansanat_per_page ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Theme's own filter.
$erfansanat_page     = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

$erfansanat_query = $GLOBALS['wp_query'] ?? null;
$erfansanat_total = $erfansanat_query instanceof \WP_Query ? (int) $erfansanat_query->found_posts : 0;

$erfansanat_posts = array();
if ( $erfansanat_query instanceof \WP_Query ) {
	foreach ( (array) $erfansanat_query->posts as $erfansanat_post ) {
		if ( $erfansanat_post instanceof \WP_Post ) {
			$erfansanat_posts[] = $erfansanat_post;
		}
	}
}

$erfansanat_categories = get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
$erfansanat_categories = is_array( $erfansanat_categories ) ? $erfansanat_categories : array();

$erfansanat_total_posts = (int) wp_count_posts( 'post' )->publish;
$erfansanat_blog_id     = (int) get_option( 'page_for_posts' );
$erfansanat_base_url    = trailingslashit( (string) get_permalink( $erfansanat_blog_id ) );
if ( $erfansanat_is_category ) {
	$erfansanat_term = get_queried_object();
	if ( $erfansanat_term instanceof \WP_Term ) {
		$erfansanat_base_url = trailingslashit( \ErfanSanat\Theme\term_url( $erfansanat_term ) );
	}
}
?>


<?php
require get_template_directory() . '/template-parts/blog/part-blog-hero.php';
require get_template_directory() . '/template-parts/blog/part-blog-grid.php';
