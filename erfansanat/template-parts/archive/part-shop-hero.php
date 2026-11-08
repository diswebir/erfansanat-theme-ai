<?php
/**
 * Shop and product-category hero.
 *
 * One template serves the shop archive and every category page because the two
 * references differ only in wording and in the breadcrumb tail; both are read
 * from real data rather than hard-coded per page.
 *
 * @package ErfanSanat
 *
 * @var array<int, array{label: string, url: string}> $erfansanat_crumbs   Breadcrumb trail, last item is the current page.
 * @var string                                        $erfansanat_eyebrow  Small pill text above the title.
 * @var string                                        $erfansanat_title    Plain part of the headline.
 * @var string                                        $erfansanat_highlight  Gradient part of the headline.
 * @var string                                        $erfansanat_lead     Intro paragraph.
 * @var array<int, array{icon: string, label: string}> $erfansanat_badges  Four trust chips.
 * @var string                                        $erfansanat_image    Hero image URL, empty for none.
 * @var string                                        $erfansanat_image_alt Image alt text.
 * @var int                                           $erfansanat_count    Published product count.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_crumbs    = isset( $erfansanat_crumbs ) && is_array( $erfansanat_crumbs ) ? $erfansanat_crumbs : array();
$erfansanat_eyebrow   = isset( $erfansanat_eyebrow ) ? (string) $erfansanat_eyebrow : '';
$erfansanat_title     = isset( $erfansanat_title ) ? (string) $erfansanat_title : '';
$erfansanat_highlight = isset( $erfansanat_highlight ) ? (string) $erfansanat_highlight : '';
$erfansanat_lead      = isset( $erfansanat_lead ) ? (string) $erfansanat_lead : '';
$erfansanat_badges    = isset( $erfansanat_badges ) && is_array( $erfansanat_badges ) ? $erfansanat_badges : array();
$erfansanat_image     = isset( $erfansanat_image ) ? (string) $erfansanat_image : '';
$erfansanat_image_alt = isset( $erfansanat_image_alt ) ? (string) $erfansanat_image_alt : '';
$erfansanat_count     = isset( $erfansanat_count ) ? (int) $erfansanat_count : 0;
?>
<section data-shop-hero="true" class="relative overflow-hidden bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-36">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0" style="background-image:radial-gradient(1000px 500px at 78% -6%, rgba(0,177,64,0.1), transparent 60%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-50"></div>
		<div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-emerald-200/50 blur-[110px]"></div>
		<div class="absolute -left-28 bottom-10 h-72 w-72 rounded-full bg-teal-100/60 blur-[110px]"></div>
		<div class="absolute left-[10%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div>
	</div>
	<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
		<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)] backdrop-blur">
			<?php foreach ( $erfansanat_crumbs as $erfansanat_index => $erfansanat_crumb ) : ?>
				<?php if ( $erfansanat_index > 0 ) : ?>
					<span class="flex min-w-0 items-center gap-1.5">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-400" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
				<?php endif; ?>
				<?php if ( isset( $erfansanat_crumb['url'] ) && '' !== $erfansanat_crumb['url'] ) : ?>
					<a href="<?php echo esc_url( (string) $erfansanat_crumb['url'] ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php echo esc_html( (string) $erfansanat_crumb['label'] ); ?></a>
				<?php else : ?>
					<span class="max-w-[16rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php echo esc_html( (string) $erfansanat_crumb['label'] ); ?></span>
				<?php endif; ?>
				<?php if ( $erfansanat_index > 0 ) : ?>
					</span>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>

		<div class="grid items-center gap-12 pb-8 pt-10 lg:grid-cols-[1.02fr_0.98fr] lg:gap-14 lg:pb-12 lg:pt-12">
			<div>
				<?php if ( '' !== $erfansanat_eyebrow ) : ?>
					<div class="mb-6 inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white py-1.5 pl-5 pr-1.5 shadow-sm">
						<span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-600 text-white shadow-md shadow-emerald-600/25">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
						</span>
						<span class="text-[13px] font-extrabold text-emerald-800"><?php echo esc_html( $erfansanat_eyebrow ); ?></span>
					</div>
				<?php endif; ?>

				<h1 class="text-[2.3rem] font-black leading-[1.28] tracking-[-0.015em] text-slate-950 sm:text-[3rem] lg:text-[3.4rem] lg:leading-[1.22]">
					<?php echo esc_html( $erfansanat_title ); ?>
					<?php if ( '' !== $erfansanat_highlight ) : ?>
						<br />
						<span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent"><?php echo esc_html( $erfansanat_highlight ); ?></span>
					<?php endif; ?>
				</h1>

				<?php if ( '' !== $erfansanat_lead ) : ?>
					<p class="mt-6 max-w-[36rem] text-[15.5px] font-medium leading-9 text-slate-600 lg:text-[16px]"><?php echo esc_html( $erfansanat_lead ); ?></p>
				<?php endif; ?>

				<?php if ( array() !== $erfansanat_badges ) : ?>
					<div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
						<?php foreach ( $erfansanat_badges as $erfansanat_badge ) : ?>
							<div class="flex items-center gap-2.5 rounded-2xl border border-emerald-100 bg-white px-3 py-2.5 shadow-xs">
								<span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-700">
									<?php SectionParts::icon( (string) $erfansanat_badge['icon'], 'h-4 w-4' ); ?>
								</span>
								<span class="text-[11.5px] font-extrabold leading-4 text-slate-700"><?php echo esc_html( (string) $erfansanat_badge['label'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $erfansanat_image ) : ?>
				<div class="relative mx-auto w-full max-w-[30rem]">
					<div aria-hidden="true" class="absolute left-1/2 top-1/2 h-[70%] w-[70%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-200/50 blur-[80px]"></div>
					<div class="relative overflow-hidden rounded-[2.5rem] border-4 border-white shadow-[0_40px_80px_-30px_rgba(0,80,40,0.35)]">
						<img src="<?php echo esc_url( $erfansanat_image ); ?>" alt="<?php echo esc_attr( $erfansanat_image_alt ); ?>" class="h-[22rem] w-full object-cover sm:h-[25rem] lg:h-[28rem]" />
						<div class="absolute inset-0 bg-gradient-to-t from-emerald-950/45 via-transparent to-transparent"></div>
						<div class="absolute inset-x-4 bottom-4 flex items-center justify-between rounded-2xl border border-white/50 bg-white/85 px-4 py-3 backdrop-blur-md">
							<div class="flex flex-col">
								<span class="text-[11px] font-bold text-slate-500"><?php esc_html_e( 'پیشنهاد ویژه کارخانه', 'erfansanat' ); ?></span>
								<span class="text-[14px] font-black text-slate-900"><?php esc_html_e( 'استعلام قیمت روز + مشاوره رایگان', 'erfansanat' ); ?></span>
							</div>
							<a href="<?php echo esc_url( SectionParts::phone_href() ); ?>" class="rounded-xl bg-emerald-700 px-3.5 py-2 text-[12.5px] font-black text-white shadow-lg transition-colors hover:bg-emerald-800"><?php esc_html_e( 'تماس بگیرید', 'erfansanat' ); ?></a>
						</div>
					</div>
					<?php if ( $erfansanat_count > 0 ) : ?>
						<span class="absolute -top-4 right-8 flex items-center gap-2 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 shadow-xl">
							<span class="relative flex h-2.5 w-2.5">
								<span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-70"></span>
								<span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
							</span>
							<span class="text-[12.5px] font-extrabold text-slate-900">
								<?php
								printf(
									/* translators: %s: number of published products. */
									esc_html__( '%s محصول در انبار کارخانه', 'erfansanat' ),
									esc_html( SectionParts::fa_digits( (string) $erfansanat_count ) )
								);
								?>
							</span>
						</span>
					<?php endif; ?>
					<span class="absolute -bottom-5 left-6 flex items-center gap-2.5 rounded-2xl bg-emerald-800 px-4 py-3 text-white shadow-[0_20px_40px_-16px_rgba(0,80,40,0.5)]">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
						<span class="flex flex-col leading-tight">
							<span class="text-[13px] font-black"><?php esc_html_e( '۲۴ ماه گارانتی', 'erfansanat' ); ?></span>
							<span class="text-[10.5px] font-medium text-emerald-200"><?php esc_html_e( '۵ سال خدمات پس از فروش', 'erfansanat' ); ?></span>
						</span>
					</span>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div>
</section>
