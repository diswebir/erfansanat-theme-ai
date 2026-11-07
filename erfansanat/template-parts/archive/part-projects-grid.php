<?php
/**
 * Projects toolbar, grid, client pager and empty state.
 *
 * The grid carries the payload `initProjectsArchive()` reads: type, all types,
 * city, view count and a pre-joined search string. The pager is the reference's
 * `data-pagination-root` contract, with server-side links underneath so the
 * archive still pages without JavaScript.
 *
 * @package ErfanSanat
 *
 * @var array<int, \WP_Post> $erfansanat_projects Projects of the current page.
 * @var int                  $erfansanat_total    Published project count.
 * @var int                  $erfansanat_page     Current page, 1-based.
 * @var int                  $erfansanat_per_page Items per page.
 * @var string               $erfansanat_base_url Archive base URL for paging.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\ProjectData;
use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_projects = isset( $erfansanat_projects ) && is_array( $erfansanat_projects ) ? $erfansanat_projects : array();
$erfansanat_total    = isset( $erfansanat_total ) ? (int) $erfansanat_total : 0;
$erfansanat_page     = isset( $erfansanat_page ) ? max( 1, (int) $erfansanat_page ) : 1;
$erfansanat_per_page = isset( $erfansanat_per_page ) ? max( 1, (int) $erfansanat_per_page ) : 6;
$erfansanat_base_url = isset( $erfansanat_base_url ) ? (string) $erfansanat_base_url : '';

$erfansanat_pages = (int) max( 1, (int) ceil( $erfansanat_total / $erfansanat_per_page ) );
$erfansanat_sorts = array(
	'default' => __( 'جدیدترین پروژه‌ها', 'erfansanat' ),
	'popular' => __( 'پربازدیدترین', 'erfansanat' ),
	'rating'  => __( 'بالاترین امتیاز', 'erfansanat' ),
);
?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3.5 shadow-2xs sm:px-5">
	<div class="flex flex-wrap items-center gap-2">
		<span class="text-[13.5px] font-black text-slate-900"><?php esc_html_e( 'کارنامه پروژه‌ها', 'erfansanat' ); ?></span>
		<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-extrabold text-emerald-800">
			<span data-project-count="true"><?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) ); ?></span>
			<?php esc_html_e( 'پروژه', 'erfansanat' ); ?>
		</span>
	</div>
	<div class="flex flex-wrap items-center gap-2">
		<div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 text-emerald-700" aria-hidden="true"><path d="m21 16-4 4-4-4"></path><path d="M17 20V4"></path><path d="m3 8 4-4 4 4"></path><path d="M7 4v16"></path></svg>
			<label for="project-sort-select" class="text-[11.5px] font-bold text-slate-500"><?php esc_html_e( 'مرتب‌سازی:', 'erfansanat' ); ?></label>
			<select id="project-sort-select" data-project-sort="true" class="cursor-pointer bg-transparent text-[12px] font-extrabold text-slate-800 focus:outline-none">
				<?php foreach ( $erfansanat_sorts as $erfansanat_value => $erfansanat_label ) : ?>
					<option value="<?php echo esc_attr( $erfansanat_value ); ?>" <?php selected( 'default', $erfansanat_value ); ?>><?php echo esc_html( $erfansanat_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<button type="button" data-archive-drawer-open="true" class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-emerald-700 px-3.5 py-2 text-[12px] font-extrabold text-white shadow-xs transition-colors hover:bg-emerald-800 lg:hidden">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><line x1="21" y1="4" x2="14" y2="4"></line><line x1="10" y1="4" x2="3" y2="4"></line><line x1="21" y1="12" x2="12" y2="12"></line><line x1="8" y1="12" x2="3" y2="12"></line><line x1="21" y1="20" x2="16" y2="20"></line><line x1="12" y1="20" x2="3" y2="20"></line></svg>
			<span><?php esc_html_e( 'فیلتر و آرشیو', 'erfansanat' ); ?></span>
		</button>
	</div>
</div>

<?php if ( array() === $erfansanat_projects ) : ?>
	<div data-project-empty="true" class="block rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 py-16 text-center">
		<p class="text-[14.5px] font-extrabold text-slate-700"><?php esc_html_e( 'پروژه‌ای با این مشخصات یافت نشد.', 'erfansanat' ); ?></p>
		<p class="mt-1.5 text-[12.5px] text-slate-500"><?php esc_html_e( 'عبارت جستجو را تغییر دهید یا فیلترهای فعال را پاک کنید.', 'erfansanat' ); ?></p>
	</div>
<?php else : ?>
	<div data-project-grid="true" class="grid gap-6 sm:grid-cols-2">
		<?php foreach ( $erfansanat_projects as $erfansanat_project ) : ?>
			<?php
			if ( ! $erfansanat_project instanceof \WP_Post ) {
				continue;
			}
			$erfansanat_payload = ProjectData::archive_payload( $erfansanat_project );
			$erfansanat_terms   = ProjectData::types( (int) $erfansanat_project->ID );
			$erfansanat_cities  = ProjectData::cities( (int) $erfansanat_project->ID );
			$erfansanat_thumb   = (string) get_the_post_thumbnail_url( (int) $erfansanat_project->ID, 'erfansanat-card' );
			?>
			<div
				data-project-item="true"
				data-project-type="<?php echo esc_attr( $erfansanat_payload['type'] ); ?>"
				data-project-types="<?php echo esc_attr( $erfansanat_payload['types'] ); ?>"
				data-project-location="<?php echo esc_attr( $erfansanat_payload['location'] ); ?>"
				data-project-views="<?php echo esc_attr( $erfansanat_payload['views'] ); ?>"
				data-project-rating="<?php echo esc_attr( $erfansanat_payload['rating'] ); ?>"
				data-project-date="<?php echo esc_attr( $erfansanat_payload['date'] ); ?>"
				data-project-search-text="<?php echo esc_attr( $erfansanat_payload['search_text'] ); ?>"
				class="block"
			>
				<a href="<?php echo esc_url( (string) get_permalink( (int) $erfansanat_project->ID ) ); ?>" class="group relative block overflow-hidden rounded-[1.5rem] border border-emerald-100 bg-slate-900 shadow-sm transition-shadow hover:shadow-xl">
					<div class="relative h-60 overflow-hidden">
						<?php if ( '' !== $erfansanat_thumb ) : ?>
							<img src="<?php echo esc_url( $erfansanat_thumb ); ?>" alt="<?php echo esc_attr( (string) get_the_title( (int) $erfansanat_project->ID ) ); ?>" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-[1.2s] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110" />
						<?php endif; ?>
						<div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent"></div>
						<?php if ( array() !== $erfansanat_terms ) : ?>
							<span class="absolute right-4 top-4 rounded-full bg-emerald-950/75 px-3.5 py-1.5 text-[11.5px] font-bold text-emerald-100 backdrop-blur-md"><?php echo esc_html( (string) $erfansanat_terms[0] ); ?></span>
						<?php endif; ?>
						<span class="absolute left-4 top-4 grid h-10 w-10 place-items-center rounded-full border border-white/30 bg-emerald-950/70 backdrop-blur-md transition-all duration-500 group-hover:rotate-45 group-hover:bg-emerald-500">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5 text-white" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg>
						</span>
						<div class="absolute inset-x-0 bottom-0 p-5">
							<h2 class="text-lg font-extrabold leading-7 text-white [text-shadow:0_2px_12px_rgba(0,0,0,.8)]"><?php echo esc_html( (string) get_the_title( (int) $erfansanat_project->ID ) ); ?></h2>
						</div>
					</div>
					<div class="flex flex-wrap items-center justify-between gap-2 bg-white px-5 py-4">
						<span class="flex items-center gap-1.5 text-[12.5px] font-bold text-slate-700">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
							<?php echo esc_html( array() !== $erfansanat_cities ? (string) $erfansanat_cities[0] : __( 'نامشخص', 'erfansanat' ) ); ?>
						</span>
						<div class="flex items-center gap-3 text-[11.5px] font-bold text-slate-500">
							<span class="flex items-center gap-1 text-emerald-800">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
								<?php echo esc_html( SectionParts::fa_digits( $erfansanat_payload['views'] ) ); ?>
							</span>
						</div>
					</div>
				</a>
			</div>
		<?php endforeach; ?>
	</div>

	<div data-project-empty="true" class="hidden rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 py-16 text-center">
		<p class="text-[14.5px] font-extrabold text-slate-700"><?php esc_html_e( 'پروژه‌ای با این مشخصات یافت نشد.', 'erfansanat' ); ?></p>
		<p class="mt-1.5 text-[12.5px] text-slate-500"><?php esc_html_e( 'عبارت جستجو را تغییر دهید یا فیلترهای فعال را پاک کنید.', 'erfansanat' ); ?></p>
	</div>

	<nav
		aria-label="<?php esc_attr_e( 'صفحه‌بندی پروژه‌ها', 'erfansanat' ); ?>"
		data-pagination-root="projects"
		data-per-page="<?php echo esc_attr( (string) $erfansanat_per_page ); ?>"
		data-pagination-server="true"
		class="<?php echo $erfansanat_pages > 1 ? 'flex' : 'hidden'; ?> mt-10 flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/90 bg-white px-5 py-4 shadow-2xs"
	>
		<div class="text-[12.5px] font-bold text-slate-600">
			<?php esc_html_e( 'نمایش صفحه', 'erfansanat' ); ?>
			<span data-pagination-summary="true" class="font-black text-emerald-800">
				<?php
				printf(
					/* translators: 1: current page, 2: total pages, 3: total projects. */
					esc_html__( 'صفحه %1$s از %2$s (%3$s مورد)', 'erfansanat' ),
					esc_html( SectionParts::fa_digits( (string) $erfansanat_page ) ),
					esc_html( SectionParts::fa_digits( (string) $erfansanat_pages ) ),
					esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) )
				);
				?>
			</span>
		</div>
		<div class="flex items-center gap-1.5" data-pagination-buttons="true">
			<?php
			$erfansanat_prev_url = $erfansanat_page > 1
				? $erfansanat_base_url . ( $erfansanat_page - 1 > 1 ? 'page/' . ( $erfansanat_page - 1 ) . '/' : '' )
				: '';
			?>
			<a href="<?php echo esc_url( '' !== $erfansanat_prev_url ? $erfansanat_prev_url : '#' ); ?>" data-page-prev="true" <?php echo '' === $erfansanat_prev_url ? 'aria-disabled="true" tabindex="-1"' : ''; ?> class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 <?php echo '' === $erfansanat_prev_url ? 'pointer-events-none opacity-40' : ''; ?>">
				<?php SectionParts::arrow( 'h-3.5 w-3.5 rotate-180' ); ?>
				<span><?php esc_html_e( 'قبلی', 'erfansanat' ); ?></span>
			</a>
			<?php $erfansanat_last_page = min( 5, $erfansanat_pages ); ?>
			<?php for ( $erfansanat_number = 1; $erfansanat_number <= $erfansanat_last_page; $erfansanat_number++ ) : ?>
				<?php
				$erfansanat_number_url = 1 === $erfansanat_number ? $erfansanat_base_url : $erfansanat_base_url . 'page/' . $erfansanat_number . '/';
				?>
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
