<?php
/**
 * Shared blog archive layout (P07-T009). Sliced from static-mirror/blog.html.
 * All filtering is performed by the existing main.js; the complete published
 * collection is rendered, with real category links and a matching empty state.
 *
 * @package ErfanSanat
 * @var array $args { @type WP_Query $query @type int $per_page }
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\ArchiveTab;

$es_query      = isset( $args['query'] ) && $args['query'] instanceof WP_Query ? $args['query'] : new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => -1, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
$es_posts      = $es_query->posts;
$es_total      = count( $es_posts );
$es_per_page   = max( 1, (int) ( $args['per_page'] ?? ArchiveTab::posts_per_page_for( 'blog_posts_per_page' ) ) );
$es_categories = get_categories( array( 'hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC' ) );
$es_categories = is_wp_error( $es_categories ) ? array() : $es_categories;
$es_blog_url   = (int) get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/blog/' );
$es_context    = (string) ( $args['context'] ?? 'home' );
$es_title      = (string) ( $args['title'] ?? __( 'اخبار و مقالات', 'erfansanat' ) );
$es_description = (string) ( $args['description'] ?? '' );
$es_term       = isset( $args['term'] ) && $args['term'] instanceof WP_Term ? $args['term'] : null;
$es_badge      = (string) ( $args['badge_label'] ?? __( 'مجله فنی و آموزشی عرفان صنعت', 'erfansanat' ) );
$es_toolbar    = (string) ( $args['toolbar_title'] ?? __( 'آخرین مقالات و اخبار', 'erfansanat' ) );
$es_count_suffix = (string) ( $args['count_suffix'] ?? __( 'مقاله', 'erfansanat' ) );
$es_featured   = $es_posts ? $es_posts[0] : null;
foreach ( $es_posts as $es_candidate ) {
	if ( is_sticky( $es_candidate->ID ) ) {
		$es_featured = $es_candidate;
		break;
	}
}
$es_fa = static function ( $value ) {
	return strtr( (string) $value, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
};
?>
<main class="min-h-screen bg-[#f8faf9]">
	<?php get_template_part( 'template-parts/archive/blog-hero', null, array( 'post' => $es_featured, 'total' => $es_total, 'categories' => $es_categories, 'fa' => $es_fa, 'context' => $es_context, 'title' => $es_title, 'description' => $es_description, 'term' => $es_term, 'badge_label' => $es_badge, 'blog_url' => $es_blog_url ) ); ?>

	<section id="articles" class="bg-white py-14 lg:py-16" data-blog-archive="true" data-items-per-page="<?php echo (int) $es_per_page; ?>">
		<div class="mx-auto max-w-7xl scroll-mt-24 px-5 lg:px-8">
			<div>
				<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
					<div>
						<h2 class="text-2xl font-extrabold text-slate-900" data-blog-heading="true"><?php echo esc_html( $es_toolbar ); ?></h2>
						<p class="mt-2 text-[13.5px] font-medium text-slate-500" data-blog-summary="true"><span data-blog-count="true"><?php echo esc_html( $es_fa( $es_total ) ); ?></span> <?php echo esc_html( $es_count_suffix ); ?></p>
					</div>
					<div class="flex flex-wrap items-center gap-2">
						<?php get_template_part( 'template-parts/archive/blog-filter', null, array( 'categories' => $es_categories, 'context' => $es_context, 'term' => $es_term, 'blog_url' => $es_blog_url, 'variant' => 'toolbar' ) ); ?>
					</div>
				</div>
			</div>

			<div data-blog-grid="true" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2 <?php echo 0 === $es_total ? 'hidden' : ''; ?>">
				<?php foreach ( $es_posts as $es_article ) : ?>
					<?php get_template_part( 'template-parts/cards/card-post', null, array( 'post_id' => (int) $es_article->ID, 'archive_item' => true ) ); ?>
				<?php endforeach; ?>
			</div>

			<?php get_template_part( 'template-parts/archive/pagination', null, array( 'total' => $es_total, 'per_page' => $es_per_page, 'label' => __( 'صفحه‌بندی مقالات', 'erfansanat' ) ) ); ?>

			<div data-blog-empty="true" class="<?php echo 0 === $es_total ? 'block' : 'hidden'; ?> rounded-2xl border border-dashed border-slate-300 bg-slate-50/60 py-16 text-center"><p class="text-[14px] font-bold text-slate-500"><?php esc_html_e( 'مقاله‌ای با این جستجو یافت نشد.', 'erfansanat' ); ?></p></div>
		</div>
	</section>
</main>
