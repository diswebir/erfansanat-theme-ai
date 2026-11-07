<?php
/**
 * Blog grid, client-side filter/paging and the empty state.
 *
 * Server-side paging is real (`?page=N` over the main query); the script layers
 * its search and category filter on the current page. `data-pagination-root`
 * carries the same "projects"-style server contract as the other archives.
 *
 * @package ErfanSanat
 *
 * @var array<int, \WP_Post> $erfansanat_posts     Posts of the current page.
 * @var int                  $erfansanat_total     Total posts in this archive.
 * @var int                  $erfansanat_page      Current page, 1-based.
 * @var int                  $erfansanat_per_page  Items per page.
 * @var string               $erfansanat_base_url  Archive base for paging.
 * @var bool                 $erfansanat_is_category True on a category archive.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\PostData;
use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_posts       = isset( $erfansanat_posts ) && is_array( $erfansanat_posts ) ? $erfansanat_posts : array();
$erfansanat_total       = isset( $erfansanat_total ) ? (int) $erfansanat_total : 0;
$erfansanat_page        = isset( $erfansanat_page ) ? max( 1, (int) $erfansanat_page ) : 1;
$erfansanat_per_page    = isset( $erfansanat_per_page ) ? max( 1, (int) $erfansanat_per_page ) : 6;
$erfansanat_base_url    = isset( $erfansanat_base_url ) ? (string) $erfansanat_base_url : '';
$erfansanat_is_category = isset( $erfansanat_is_category ) && (bool) $erfansanat_is_category;

$erfansanat_pages = (int) max( 1, (int) ceil( $erfansanat_total / $erfansanat_per_page ) );
?>
<section id="archive" data-blog-archive="true" data-items-per-page="<?php echo esc_attr( (string) $erfansanat_per_page ); ?>" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-12 lg:px-8 lg:py-16">
	<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
		<div>
			<h2 data-blog-heading="true" class="text-xl font-black text-slate-900 sm:text-2xl"><?php echo esc_html( $erfansanat_is_category ? __( 'مقالات این دسته', 'erfansanat' ) : __( 'آخرین مقالات و اخبار', 'erfansanat' ) ); ?></h2>
			<p class="mt-1 text-[12.5px] font-semibold text-slate-500">
				<span data-blog-count="true"><?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) ); ?></span>
				<?php esc_html_e( 'مقاله', 'erfansanat' ); ?>
			</p>
		</div>
		<button type="button" data-blog-reset="true" class="hidden cursor-pointer items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-[12px] font-extrabold text-slate-600 transition-colors hover:border-emerald-400 hover:text-emerald-700">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
			<span><?php esc_html_e( 'حذف فیلترها', 'erfansanat' ); ?></span>
		</button>
	</div>

	<?php if ( array() === $erfansanat_posts ) : ?>
		<div data-blog-empty="true" class="block rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 py-16 text-center">
			<p class="text-[14.5px] font-extrabold text-slate-700"><?php esc_html_e( 'مقاله‌ای با این مشخصات یافت نشد.', 'erfansanat' ); ?></p>
			<p class="mt-1.5 text-[12.5px] text-slate-500"><?php esc_html_e( 'عبارت جستجو را تغییر دهید یا فیلتر دسته را پاک کنید.', 'erfansanat' ); ?></p>
		</div>
	<?php else : ?>
		<div data-blog-grid="true" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $erfansanat_posts as $erfansanat_post ) : ?>
				<?php
				if ( ! $erfansanat_post instanceof \WP_Post ) {
					continue;
				}
				$erfansanat_payload = PostData::archive_payload( $erfansanat_post );
				$erfansanat_thumb   = (string) get_the_post_thumbnail_url( (int) $erfansanat_post->ID, 'erfansanat-card' );
				if ( '' === $erfansanat_thumb ) {
					$erfansanat_thumb = PostData::fallback_thumb( (int) $erfansanat_post->ID );
				}
				?>
				<div
					data-blog-item="true"
					data-blog-category="<?php echo esc_attr( $erfansanat_payload['category'] ); ?>"
					data-blog-search-text="<?php echo esc_attr( $erfansanat_payload['search_text'] ); ?>"
					class="block"
				>
					<a href="<?php echo esc_url( (string) get_permalink( (int) $erfansanat_post->ID ) ); ?>" class="card-light group flex h-full flex-col justify-between rounded-2xl p-3.5">
						<div>
							<div class="relative aspect-[16/10] w-full overflow-hidden rounded-xl bg-slate-900">
								<?php if ( '' !== $erfansanat_thumb ) : ?>
									<img src="<?php echo esc_url( $erfansanat_thumb ); ?>" alt="<?php echo esc_attr( (string) get_the_title( (int) $erfansanat_post->ID ) ); ?>" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
								<?php endif; ?>
								<div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
								<?php if ( '' !== $erfansanat_payload['category'] ) : ?>
									<span class="absolute right-3 top-3 rounded-lg bg-emerald-950/75 px-2.5 py-1 text-[10.5px] font-bold text-emerald-100 backdrop-blur-md"><?php echo esc_html( $erfansanat_payload['category'] ); ?></span>
								<?php endif; ?>
							</div>
							<div class="mt-3 flex items-center gap-2 text-[11px] font-bold text-slate-500 tabular-nums">
								<span><?php echo esc_html( get_the_date( 'j F Y', (int) $erfansanat_post->ID ) ); ?></span>
								<span>·</span>
								<span>
									<?php
									printf(
										/* translators: %s: reading time in minutes. */
										esc_html__( '%s دقیقه مطالعه', 'erfansanat' ),
										esc_html( SectionParts::fa_digits( (string) PostData::reading_time( (int) $erfansanat_post->ID ) ) )
									);
									?>
								</span>
							</div>
							<h3 class="mt-1.5 line-clamp-2 text-right text-sm font-extrabold leading-6 text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( (string) get_the_title( (int) $erfansanat_post->ID ) ); ?></h3>
							<?php $erfansanat_excerpt = get_the_excerpt( $erfansanat_post ); ?>
							<?php if ( '' !== (string) $erfansanat_excerpt ) : ?>
								<p class="mt-1.5 line-clamp-2 text-right text-xs font-medium leading-6 text-slate-500"><?php echo esc_html( (string) $erfansanat_excerpt ); ?></p>
							<?php endif; ?>
						</div>
						<div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-2.5 text-xs font-extrabold text-emerald-700">
							<span><?php esc_html_e( 'مطالعه مقاله', 'erfansanat' ); ?></span>
							<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>

		<div data-blog-empty="true" class="hidden rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 py-16 text-center">
			<p class="text-[14.5px] font-extrabold text-slate-700"><?php esc_html_e( 'مقاله‌ای با این مشخصات یافت نشد.', 'erfansanat' ); ?></p>
			<p class="mt-1.5 text-[12.5px] text-slate-500"><?php esc_html_e( 'عبارت جستجو را تغییر دهید یا فیلتر دسته را پاک کنید.', 'erfansanat' ); ?></p>
		</div>

		<nav
			aria-label="<?php esc_attr_e( 'صفحه‌بندی مقالات', 'erfansanat' ); ?>"
			data-pagination-root="blog"
			data-per-page="<?php echo esc_attr( (string) $erfansanat_per_page ); ?>"
			data-pagination-server="true"
			class="<?php echo $erfansanat_pages > 1 ? 'flex' : 'hidden'; ?> mt-10 flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/90 bg-white px-5 py-4 shadow-2xs"
		>
			<div class="text-[12.5px] font-bold text-slate-600">
				<?php esc_html_e( 'نمایش صفحه', 'erfansanat' ); ?>
				<span class="font-black text-emerald-800">
					<?php
					printf(
						/* translators: 1: current page, 2: total pages, 3: total posts. */
						esc_html__( 'صفحه %1$s از %2$s (%3$s مورد)', 'erfansanat' ),
						esc_html( SectionParts::fa_digits( (string) $erfansanat_page ) ),
						esc_html( SectionParts::fa_digits( (string) $erfansanat_pages ) ),
						esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) )
					);
					?>
				</span>
			</div>
			<div class="flex items-center gap-1.5" data-pagination-buttons="true">
				<?php $erfansanat_prev_url = $erfansanat_page > 1 ? $erfansanat_base_url . ( $erfansanat_page - 1 > 1 ? 'page/' . ( $erfansanat_page - 1 ) . '/' : '' ) : ''; ?>
				<a href="<?php echo esc_url( '' !== $erfansanat_prev_url ? $erfansanat_prev_url : '#' ); ?>" data-page-prev="true" <?php echo '' === $erfansanat_prev_url ? 'aria-disabled="true" tabindex="-1"' : ''; ?> class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 <?php echo '' === $erfansanat_prev_url ? 'pointer-events-none opacity-40' : ''; ?>">
					<?php SectionParts::arrow( 'h-3.5 w-3.5 rotate-180' ); ?>
					<span><?php esc_html_e( 'قبلی', 'erfansanat' ); ?></span>
				</a>
				<?php $erfansanat_last_page = min( 5, $erfansanat_pages ); ?>
				<?php for ( $erfansanat_number = 1; $erfansanat_number <= $erfansanat_last_page; $erfansanat_number++ ) : ?>
					<?php $erfansanat_number_url = 1 === $erfansanat_number ? $erfansanat_base_url : $erfansanat_base_url . 'page/' . $erfansanat_number . '/'; ?>
					<a href="<?php echo esc_url( $erfansanat_number_url ); ?>" data-page-num="<?php echo esc_attr( (string) $erfansanat_number ); ?>" <?php echo $erfansanat_number === $erfansanat_page ? 'aria-current="page"' : ''; ?> class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all <?php echo $erfansanat_number === $erfansanat_page ? 'bg-emerald-700 text-white shadow-sm shadow-emerald-700/25' : 'border border-slate-200 bg-white text-slate-700 hover:border-emerald-400 hover:text-emerald-700'; ?>"><?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_number ) ); ?></a>
				<?php endfor; ?>
				<?php $erfansanat_next_url = $erfansanat_page < $erfansanat_pages ? $erfansanat_base_url . 'page/' . ( $erfansanat_page + 1 ) . '/' : ''; ?>
				<a href="<?php echo esc_url( '' !== $erfansanat_next_url ? $erfansanat_next_url : '#' ); ?>" data-page-next="true" <?php echo '' === $erfansanat_next_url ? 'aria-disabled="true" tabindex="-1"' : ''; ?> class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 <?php echo '' === $erfansanat_next_url ? 'pointer-events-none opacity-40' : ''; ?>">
					<span><?php esc_html_e( 'بعدی', 'erfansanat' ); ?></span>
					<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
				</a>
			</div>
		</nav>
	<?php endif; ?>
</section>
