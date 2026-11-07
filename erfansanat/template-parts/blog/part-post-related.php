<?php
/**
 * Related posts strip under a single post.
 *
 * Same-category first, topped up with the newest posts so the strip never shows
 * a half-empty row. Cards come from the shared post card.
 *
 * @package ErfanSanat
 *
 * @var int $erfansanat_post_id Post being viewed.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_post_id = isset( $erfansanat_post_id ) ? (int) $erfansanat_post_id : (int) get_the_ID();

$erfansanat_cats  = wp_get_post_categories( $erfansanat_post_id );
$erfansanat_posts = array();

if ( is_array( $erfansanat_cats ) && array() !== $erfansanat_cats ) {
	$erfansanat_posts = get_posts(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'post__not_in'        => array( $erfansanat_post_id ),
			'category__in'        => array_map( 'intval', $erfansanat_cats ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
}

if ( count( $erfansanat_posts ) < 3 ) {
	$erfansanat_fill  = get_posts(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'post__not_in'        => array_merge( array( $erfansanat_post_id ), wp_list_pluck( $erfansanat_posts, 'ID' ) ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	$erfansanat_posts = array_merge( $erfansanat_posts, $erfansanat_fill );
}

$erfansanat_posts = array_slice( $erfansanat_posts, 0, 3 );

if ( array() === $erfansanat_posts ) {
	return;
}

$erfansanat_blog_url = get_permalink( (int) get_option( 'page_for_posts' ) );
if ( ! is_string( $erfansanat_blog_url ) || '' === $erfansanat_blog_url ) {
	$erfansanat_blog_url = home_url( '/' );
}
?>
<section aria-labelledby="post-related-title" class="border-t border-slate-200/80 bg-white py-14">
	<div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
		<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
			<h2 id="post-related-title" class="text-xl font-black text-slate-900 sm:text-2xl"><?php esc_html_e( 'مقالات مرتبط', 'erfansanat' ); ?></h2>
			<a href="<?php echo esc_url( $erfansanat_blog_url ); ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-3.5 py-2 text-[12px] font-extrabold text-emerald-800 transition-colors hover:bg-emerald-50">
				<span><?php esc_html_e( 'همه مقالات', 'erfansanat' ); ?></span>
				<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			</a>
		</div>
		<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $erfansanat_posts as $erfansanat_post ) : ?>
				<?php
				if ( ! $erfansanat_post instanceof \WP_Post ) {
					continue;
				}
				include get_template_directory() . '/template-parts/cards/card-post.php';
				?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
