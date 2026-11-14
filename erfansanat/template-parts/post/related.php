<?php
/**
 * Related articles (P07-T016). Reference: related grid in
 * blog/types-of-light-tunnel.html. Ranks published posts by shared category +
 * tag overlap (newest first on ties), never includes the current post, count
 * is filterable (`es_related_posts_count` / args), renders nothing when empty.
 * Cards reuse the standard card-post compact variant (dependency P05-T004).
 *
 * @package ErfanSanat
 * @var array $args { @type int $post_id @type int $count }
 */

defined( 'ABSPATH' ) || exit;

$es_post_id = (int) ( $args['post_id'] ?? get_the_ID() );
if ( $es_post_id <= 0 || 'post' !== get_post_type( $es_post_id ) || post_password_required( $es_post_id ) ) {
	return;
}

$es_count = isset( $args['count'] ) ? (int) $args['count'] : 3;
$es_count = max( 1, min( 12, (int) apply_filters( 'es_related_posts_count', $es_count, $es_post_id ) ) );

$es_cats = wp_list_pluck( get_the_category( $es_post_id ) ?: array(), 'term_id' );
$es_tags = wp_list_pluck( get_the_tags( $es_post_id ) ?: array(), 'term_id' );

$es_query_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'has_password'        => false,
	'posts_per_page'      => max( 12, $es_count * 4 ),
	'post__not_in'        => array( $es_post_id ),
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
);
if ( $es_cats || $es_tags ) {
	$es_query_args['tax_query'] = array( 'relation' => 'OR' );
	if ( $es_cats ) {
		$es_query_args['tax_query'][] = array( 'taxonomy' => 'category', 'field' => 'term_id', 'terms' => $es_cats );
	}
	if ( $es_tags ) {
		$es_query_args['tax_query'][] = array( 'taxonomy' => 'post_tag', 'field' => 'term_id', 'terms' => $es_tags );
	}
}
$es_candidates = new WP_Query( apply_filters( 'es_related_posts_query_args', $es_query_args, $es_post_id ) );

// Fallback only when the post has no taxonomy signal at all: newest posts.
// A post with categories/tags but zero matches renders no section (AC-03).
if ( ! $es_candidates->have_posts() && ! $es_cats && ! $es_tags ) {
	unset( $es_query_args['tax_query'] );
	$es_query_args['posts_per_page'] = $es_count;
	$es_candidates = new WP_Query( apply_filters( 'es_related_posts_query_args', $es_query_args, $es_post_id ) );
}
if ( ! $es_candidates->have_posts() ) {
	return;
}

$es_ranked = array();
foreach ( $es_candidates->posts as $es_candidate ) {
	$es_cid    = (int) $es_candidate->ID;
	$es_score  = count( array_intersect( $es_cats, wp_list_pluck( get_the_category( $es_cid ) ?: array(), 'term_id' ) ) )
		+ count( array_intersect( $es_tags, wp_list_pluck( get_the_tags( $es_cid ) ?: array(), 'term_id' ) ) );
	$es_ranked[] = array( 'post' => $es_candidate, 'score' => $es_score );
}
usort(
	$es_ranked,
	static function ( $a, $b ) {
		if ( $a['score'] === $b['score'] ) {
			return strcmp( $b['post']->post_date, $a['post']->post_date );
		}
		return $b['score'] <=> $a['score'];
	}
);
$es_ranked = array_slice( $es_ranked, 0, $es_count );
$es_blog_url = home_url( '/blog/' );
?>
<section class="border-t border-emerald-100 bg-white py-14 lg:py-16" data-related-posts="true">
	<div class="mx-auto max-w-7xl px-5 lg:px-8">
		<div>
			<div class="mb-8 flex items-center justify-between">
				<h3 class="text-2xl font-extrabold text-slate-900"><?php esc_html_e( 'مقالات مرتبط', 'erfansanat' ); ?></h3>
				<a class="group flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-800" href="<?php echo esc_url( $es_blog_url ); ?>"><?php esc_html_e( 'همه مقالات', 'erfansanat' ); ?><svg aria-hidden="true" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a>
			</div>
		</div>
		<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $es_ranked as $es_item ) : ?>
				<div><?php get_template_part( 'template-parts/cards/card-post', null, array( 'post_id' => (int) $es_item['post']->ID, 'compact' => true ) ); ?></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
