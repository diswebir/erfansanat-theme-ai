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
						<span class="flex items-center gap-1.5 text-[12px] font-extrabold text-slate-600"><svg aria-hidden="true" class="lucide lucide-layers h-4 w-4 text-emerald-700" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"></path><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"></path><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"></path></svg><span><?php esc_html_e( 'آرشیو دسته‌بندی‌ها:', 'erfansanat' ); ?></span></span>
						<a class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors <?php echo null === $es_term ? 'bg-emerald-700 text-white' : 'border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300'; ?>" href="<?php echo esc_url( $es_blog_url ); ?>"><?php esc_html_e( 'همه مطالب', 'erfansanat' ); ?></a>
						<?php foreach ( $es_categories as $es_category ) : ?>
							<a class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors <?php echo $es_term && (int) $es_term->term_id === (int) $es_category->term_id ? 'bg-emerald-700 text-white' : 'border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300'; ?>" href="<?php echo esc_url( get_category_link( $es_category ) ); ?>"><?php echo esc_html( $es_category->name ); ?></a>
						<?php endforeach; ?>
						<button class="hidden rounded-xl border border-slate-200 px-4 py-2 text-[12px] font-bold text-slate-600 transition-colors hover:border-emerald-400 hover:text-emerald-700 cursor-pointer" data-blog-reset="true" type="button"><?php esc_html_e( 'حذف فیلترها', 'erfansanat' ); ?></button>
					</div>
				</div>
			</div>

			<div data-blog-grid="true" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2 <?php echo 0 === $es_total ? 'hidden' : ''; ?>">
				<?php foreach ( $es_posts as $es_article ) : ?>
					<?php
					$es_article_id    = (int) $es_article->ID;
					$es_article_cats  = get_the_category( $es_article_id );
					$es_article_cat   = $es_article_cats ? $es_article_cats[0]->name : '';
					$es_article_text  = get_the_title( $es_article_id ) . ' ' . ( '' !== trim( $es_article->post_excerpt ) ? $es_article->post_excerpt : wp_strip_all_tags( $es_article->post_content ) );
					foreach ( $es_article_cats as $es_article_category ) {
						$es_article_text .= ' ' . $es_article_category->name;
					}
					foreach ( get_the_tags( $es_article_id ) ?: array() as $es_tag ) {
						$es_article_text .= ' ' . $es_tag->name;
					}
					$es_article_text = mb_strtolower( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $es_article_text ) ), 'UTF-8' );
					?>
					<div class="block" data-blog-item="true" data-blog-category="<?php echo esc_attr( $es_article_cat ); ?>" data-blog-search-text="<?php echo esc_attr( $es_article_text ); ?>">
						<div>
							<?php get_template_part( 'template-parts/cards/card-post', null, array( 'post_id' => $es_article_id ) ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php get_template_part( 'template-parts/archive/pagination', null, array( 'total' => $es_total, 'per_page' => $es_per_page, 'label' => __( 'صفحه‌بندی مقالات', 'erfansanat' ) ) ); ?>

			<div data-blog-empty="true" class="<?php echo 0 === $es_total ? 'block' : 'hidden'; ?> rounded-2xl border border-dashed border-slate-300 bg-slate-50/60 py-16 text-center"><p class="text-[14px] font-bold text-slate-500"><?php esc_html_e( 'مقاله‌ای با این جستجو یافت نشد.', 'erfansanat' ); ?></p></div>
		</div>
	</section>
</main>
