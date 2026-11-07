<?php
/**
 * Blog hero: pill, title, lead, live search and the category filter rail.
 *
 * The search box and the category buttons are the hooks `initBlogArchive()`
 * binds. The category buttons print the site's real categories, so a category
 * with no posts never appears and renaming one renames the button.
 *
 * @package ErfanSanat
 *
 * @var array<int, \WP_Term> $erfansanat_categories Published categories with posts.
 * @var int                  $erfansanat_total      Published post count.
 * @var bool                 $erfansanat_is_category True on a category archive.
 * @var string               $erfansanat_active_category Current category name.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_categories      = isset( $erfansanat_categories ) && is_array( $erfansanat_categories ) ? $erfansanat_categories : array();
$erfansanat_total           = isset( $erfansanat_total ) ? (int) $erfansanat_total : 0;
$erfansanat_is_category     = isset( $erfansanat_is_category ) && (bool) $erfansanat_is_category;
$erfansanat_active_category = isset( $erfansanat_active_category ) ? (string) $erfansanat_active_category : '';

$erfansanat_chip_off = 'border border-slate-200 bg-white text-slate-600 hover:border-emerald-400 hover:text-emerald-700';
$erfansanat_chip_on  = 'bg-emerald-700 text-white shadow-md shadow-emerald-700/25';
?>
<section data-blog-hero="true" class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-34">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0" style="background-image:radial-gradient(900px 460px at 78% -8%, rgba(0,177,64,0.1), transparent 62%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		<div class="absolute -right-24 top-6 h-72 w-72 rounded-full bg-emerald-200/45 blur-[110px]"></div>
		<div class="absolute -left-24 bottom-0 h-64 w-64 rounded-full bg-teal-100/60 blur-[100px]"></div>
	</div>
	<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
		<div class="grid items-center gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-12">
			<div>
				<div class="mb-5 inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white py-1.5 pl-5 pr-1.5 shadow-sm">
					<span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-600 text-white shadow-md shadow-emerald-600/25">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"></path><rect width="8" height="4" x="10" y="6" rx="1"></rect></svg>
					</span>
					<span class="text-[13px] font-extrabold text-emerald-800">
						<?php
						printf(
							/* translators: %s: published post count in Persian digits. */
							esc_html__( 'مجله فنی و آموزشی عرفان صنعت · %s مقاله', 'erfansanat' ),
							esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) )
						);
						?>
					</span>
				</div>

				<h1 class="text-[2.5rem] font-black leading-[1.25] tracking-[-0.015em] text-slate-950 sm:text-[3.3rem] lg:text-[3.8rem] lg:leading-[1.18]">
					<?php esc_html_e( 'اخبار و مقالات', 'erfansanat' ); ?><br />
					<span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent"><?php esc_html_e( 'نورپردازی شهری', 'erfansanat' ); ?></span>
				</h1>
				<p class="mt-6 max-w-[34rem] text-[15.5px] font-medium leading-9 text-slate-600"><?php esc_html_e( 'مقالات تخصصی، راهنماهای خرید، اخبار و رویدادها و دانش فنی صنعت روشنایی شهری و صنعتی؛ نوشته‌شده توسط کارشناسان فنی شرکت دانش‌بنیان عرفان صنعت اصفهان.', 'erfansanat' ); ?></p>

				<div class="relative mt-8 max-w-[30rem]">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute right-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
					<label for="blog-search" class="sr-only"><?php esc_html_e( 'جستجو در مقالات', 'erfansanat' ); ?></label>
					<input id="blog-search" type="search" data-blog-search="true" placeholder="<?php esc_attr_e( 'جستجو در مقالات…', 'erfansanat' ); ?>" value="" class="w-full rounded-2xl border border-emerald-200 bg-white py-4 pl-5 pr-13 text-[14px] font-medium text-slate-800 shadow-[0_18px_40px_-22px_rgba(0,80,40,0.18)] outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15" />
				</div>

				<?php if ( array() !== $erfansanat_categories ) : ?>
					<div class="mt-5 flex flex-wrap gap-2">
						<button type="button" data-blog-cat-btn="<?php esc_attr_e( 'همه', 'erfansanat' ); ?>" class="cursor-pointer rounded-xl px-4 py-2.5 text-[12.5px] font-bold transition-all <?php echo $erfansanat_is_category ? esc_attr( $erfansanat_chip_off ) : esc_attr( $erfansanat_chip_on ); ?>"><?php esc_html_e( 'همه', 'erfansanat' ); ?></button>
						<?php foreach ( $erfansanat_categories as $erfansanat_category ) : ?>
							<?php $erfansanat_is_active = $erfansanat_is_category && $erfansanat_active_category === $erfansanat_category->name; ?>
							<button type="button" data-blog-cat-btn="<?php echo esc_attr( $erfansanat_category->name ); ?>" class="cursor-pointer rounded-xl px-4 py-2.5 text-[12.5px] font-bold transition-all <?php echo esc_attr( $erfansanat_is_active ? $erfansanat_chip_on : $erfansanat_chip_off ); ?>"><?php echo esc_html( $erfansanat_category->name ); ?></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="relative mx-auto hidden w-full max-w-[30rem] sm:block">
				<div aria-hidden="true" class="absolute left-1/2 top-1/2 h-[70%] w-[70%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-200/50 blur-[80px]"></div>
				<div class="relative overflow-hidden rounded-[2.5rem] border-4 border-white shadow-[0_40px_80px_-30px_rgba(0,80,40,0.35)]">
					<img src="<?php echo esc_url( SectionParts::asset( 'images/light-tunnel.jpg' ) ); ?>" alt="" class="h-[22rem] w-full object-cover lg:h-[26rem]" />
					<div class="absolute inset-0 bg-gradient-to-t from-emerald-950/45 via-transparent to-transparent"></div>
				</div>
			</div>
		</div>
	</div>
	<div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div>
</section>
