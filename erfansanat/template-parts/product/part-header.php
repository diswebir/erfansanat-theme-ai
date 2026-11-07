<?php
/**
 * Product page header: breadcrumb, category chip, title and subtitle.
 *
 * The breadcrumb is built from the real category hierarchy rather than from the
 * mirror's relative `../` links, so a shop page moved to another slug keeps
 * working.
 *
 * @package ErfanSanat
 *
 * @var \WC_Product $erfansanat_product Product being viewed.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\LegacyMeta;
use ErfanSanat\Theme\Data\ProductData;

if ( ! isset( $erfansanat_product ) || ! $erfansanat_product instanceof \WC_Product ) {
	return;
}

$erfansanat_id   = (int) $erfansanat_product->get_id();
$erfansanat_name = $erfansanat_product->get_name();
$erfansanat_sub  = LegacyMeta::text( $erfansanat_id, 'zireonvan' );
$erfansanat_cats = ProductData::categories( $erfansanat_product );
$erfansanat_cat  = array() !== $erfansanat_cats ? $erfansanat_cats[0] : null;
$erfansanat_shop = function_exists( 'wc_get_page_permalink' ) ? (string) wc_get_page_permalink( 'shop' ) : '';
?>
<section data-prod-header="true" class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,176,64,0.09), transparent 62%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		<div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div>
	</div>
	<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
		<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)] backdrop-blur">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">
				<span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3" aria-hidden="true"><path d="M3 11 12 3l9 8"></path><path d="M5 10v10h14V10"></path></svg>
				</span>
				<?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?>
			</a>
			<?php if ( '' !== $erfansanat_shop ) : ?>
				<span class="flex min-w-0 items-center gap-1.5">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
					<a href="<?php echo esc_url( $erfansanat_shop ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php esc_html_e( 'فروشگاه محصولات', 'erfansanat' ); ?></a>
				</span>
			<?php endif; ?>
			<?php if ( null !== $erfansanat_cat ) : ?>
				<?php $erfansanat_cat_link = get_term_link( $erfansanat_cat ); ?>
				<?php if ( is_string( $erfansanat_cat_link ) ) : ?>
					<span class="flex min-w-0 items-center gap-1.5">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
						<a href="<?php echo esc_url( $erfansanat_cat_link ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php echo esc_html( $erfansanat_cat->name ); ?></a>
					</span>
				<?php endif; ?>
			<?php endif; ?>
			<span class="flex min-w-0 items-center gap-1.5">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
				<span class="max-w-[16rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php echo esc_html( $erfansanat_name ); ?></span>
			</span>
		</nav>

		<div class="pt-6">
			<?php if ( null !== $erfansanat_cat ) : ?>
				<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
					<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( $erfansanat_cat->name ); ?></span>
				</div>
			<?php endif; ?>
			<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $erfansanat_name ); ?></h1>
			<?php if ( '' !== trim( $erfansanat_sub ) ) : ?>
				<p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( $erfansanat_sub ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
