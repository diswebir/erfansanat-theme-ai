<?php
/**
 * Search results.
 *
 * WordPress searches every post type at once, but a visitor looking for
 * "تونل نوری" does not care that one hit is a product and another is a project.
 * The results are therefore grouped by type and each group is labelled, instead
 * of the default undifferentiated list. The query itself stays WordPress's own,
 * so paging, `s` handling and the SQL are not reimplemented.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

get_header();

$erfansanat_search_term = (string) get_search_query();
$erfansanat_query       = $GLOBALS['wp_query'] ?? null;
$erfansanat_total       = $erfansanat_query instanceof \WP_Query ? (int) $erfansanat_query->found_posts : 0;
$erfansanat_page        = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

// Group the current page's results by post type, keeping the query's order.
$erfansanat_groups = array();
if ( $erfansanat_query instanceof \WP_Query ) {
	foreach ( (array) $erfansanat_query->posts as $erfansanat_post ) {
		if ( $erfansanat_post instanceof \WP_Post ) {
			$erfansanat_groups[ (string) $erfansanat_post->post_type ][] = $erfansanat_post;
		}
	}
}

$erfansanat_labels = array(
	'product'  => __( 'محصولات', 'erfansanat' ),
	'projects' => __( 'پروژه‌ها', 'erfansanat' ),
	'services' => __( 'خدمات', 'erfansanat' ),
	'post'     => __( 'مقالات', 'erfansanat' ),
	'page'     => __( 'صفحات', 'erfansanat' ),
);
?>
<section data-search-hero="true" class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-34">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 50% -10%, rgba(0,177,64,0.1), transparent 60%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-45"></div>
	</div>
	<div class="relative z-10 mx-auto max-w-5xl px-5 lg:px-8">
		<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
			<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
			<span><?php esc_html_e( 'نتایج جستجو در کل سایت', 'erfansanat' ); ?></span>
		</div>
		<h1 class="text-[1.9rem] font-black leading-[1.32] tracking-[-0.015em] text-slate-950 sm:text-[2.4rem]">
			<?php if ( '' !== $erfansanat_search_term ) : ?>
				<?php
				printf(
					/* translators: %s: search phrase. */
					esc_html__( 'نتایج برای «%s»', 'erfansanat' ),
					esc_html( $erfansanat_search_term )
				);
				?>
			<?php else : ?>
				<?php esc_html_e( 'جستجو در سایت', 'erfansanat' ); ?>
			<?php endif; ?>
		</h1>
		<p class="mt-3 text-[13.5px] font-semibold text-slate-500">
			<?php
			printf(
				/* translators: %s: number of results. */
				esc_html__( '%s نتیجه یافت شد.', 'erfansanat' ),
				esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) )
			);
			?>
		</p>

		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative mt-6 max-w-[32rem]">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute right-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
			<label for="search-page-input" class="sr-only"><?php esc_html_e( 'جستجو', 'erfansanat' ); ?></label>
			<input id="search-page-input" type="search" name="s" value="<?php echo esc_attr( $erfansanat_search_term ); ?>" placeholder="<?php esc_attr_e( 'محصول، پروژه یا مقاله…', 'erfansanat' ); ?>" class="w-full rounded-2xl border border-emerald-200 bg-white py-4 pl-5 pr-13 text-[14px] font-medium text-slate-800 shadow-[0_18px_40px_-22px_rgba(0,80,40,0.18)] outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15" />
		</form>
	</div>
</section>

<section class="mx-auto max-w-5xl px-5 py-12 lg:px-8 lg:py-16">
	<?php if ( empty( $erfansanat_groups ) ) : ?>
		<div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 px-6 py-14 text-center">
			<p class="text-[15px] font-extrabold text-slate-700"><?php esc_html_e( 'نتیجه‌ای برای این جستجو پیدا نشد.', 'erfansanat' ); ?></p>
			<p class="mt-2 text-[13px] text-slate-500"><?php esc_html_e( 'املای عبارت را بررسی کنید یا از عبارت کوتاه‌تری استفاده کنید.', 'erfansanat' ); ?></p>
			<div class="mt-5 flex flex-wrap justify-center gap-2">
				<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? (string) wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>" class="rounded-xl bg-emerald-700 px-4 py-2.5 text-[12.5px] font-extrabold text-white transition-colors hover:bg-emerald-800"><?php esc_html_e( 'فروشگاه محصولات', 'erfansanat' ); ?></a>
				<a href="<?php echo esc_url( (string) get_post_type_archive_link( 'projects' ) ); ?>" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[12.5px] font-extrabold text-slate-700 transition-colors hover:border-emerald-300 hover:text-emerald-800"><?php esc_html_e( 'پروژه‌ها', 'erfansanat' ); ?></a>
			</div>
		</div>
	<?php else : ?>
		<?php foreach ( $erfansanat_groups as $erfansanat_type => $erfansanat_items ) : ?>
			<div class="mb-10">
				<h2 class="mb-4 flex items-center gap-2.5 text-[15px] font-black text-slate-900">
					<span class="h-5 w-1.5 rounded-full bg-emerald-600"></span>
					<?php echo esc_html( $erfansanat_labels[ $erfansanat_type ] ?? $erfansanat_type ); ?>
					<span class="rounded-lg bg-emerald-50 px-2 py-0.5 text-[11.5px] font-extrabold text-emerald-800"><?php echo esc_html( SectionParts::fa_digits( (string) count( $erfansanat_items ) ) ); ?></span>
				</h2>

				<?php if ( 'product' === $erfansanat_type ) : ?>
					<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
						<?php foreach ( $erfansanat_items as $erfansanat_post ) : ?>
							<?php
							$erfansanat_product = wc_get_product( (int) $erfansanat_post->ID );
							if ( ! $erfansanat_product instanceof \WC_Product ) {
								continue;
							}
							include get_template_directory() . '/template-parts/cards/card-product.php';
							?>
						<?php endforeach; ?>
					</div>
				<?php elseif ( 'projects' === $erfansanat_type ) : ?>
					<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
						<?php foreach ( $erfansanat_items as $erfansanat_project ) : ?>
							<?php include get_template_directory() . '/template-parts/cards/card-project.php'; ?>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
						<?php foreach ( $erfansanat_items as $erfansanat_post ) : ?>
							<?php include get_template_directory() . '/template-parts/cards/card-post.php'; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

		<?php
		$erfansanat_pages = (int) ( $erfansanat_query instanceof \WP_Query ? $erfansanat_query->max_num_pages : 1 );
		if ( $erfansanat_pages > 1 ) :
			?>
			<nav aria-label="<?php esc_attr_e( 'صفحه‌بندی نتایج', 'erfansanat' ); ?>" class="mt-8 flex items-center justify-center gap-1.5">
				<?php
				echo wp_kses_post(
					(string) paginate_links(
						array(
							'total'     => $erfansanat_pages,
							'current'   => $erfansanat_page,
							'prev_text' => '‹',
							'next_text' => '›',
							'type'      => 'plain',
						)
					)
				);
				?>
			</nav>
		<?php endif; ?>
	<?php endif; ?>
</section>

<?php
get_footer();
