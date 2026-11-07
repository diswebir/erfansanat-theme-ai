<?php
/**
 * Single blog post.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\PostData;

get_header();

while ( have_posts() ) :
	the_post();

	$erfansanat_post       = get_post();
	$erfansanat_post_id    = $erfansanat_post instanceof \WP_Post ? (int) $erfansanat_post->ID : 0;
	$erfansanat_categories = PostData::categories( $erfansanat_post_id );
	$erfansanat_reading    = PostData::reading_time( $erfansanat_post_id );
	$erfansanat_blog_id    = (int) get_option( 'page_for_posts' );
	$erfansanat_blog_url   = $erfansanat_blog_id > 0 ? (string) get_permalink( $erfansanat_blog_id ) : '';

	$erfansanat_crumbs = array(
		array(
			'label' => __( 'صفحه اصلی', 'erfansanat' ),
			'url'   => home_url( '/' ),
		),
	);
	if ( '' !== $erfansanat_blog_url ) {
		$erfansanat_crumbs[] = array(
			'label' => __( 'اخبار و مقالات', 'erfansanat' ),
			'url'   => $erfansanat_blog_url,
		);
	}
	$erfansanat_crumbs[] = array(
		'label' => (string) get_the_title( $erfansanat_post_id ),
		'url'   => '',
	);

	require get_template_directory() . '/template-parts/blog/part-post-header.php';
	require get_template_directory() . '/template-parts/blog/part-post-body.php';
	comments_template();
	require get_template_directory() . '/template-parts/blog/part-post-related.php';

endwhile;

get_footer();
