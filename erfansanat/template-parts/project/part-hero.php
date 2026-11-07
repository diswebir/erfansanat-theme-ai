<?php
/**
 * Project hero: breadcrumb, pill, title and lead.
 *
 * @package ErfanSanat
 *
 * @var array<int, array{label: string, url: string}> $erfansanat_crumbs Breadcrumb trail, last item is current.
 * @var string                                        $erfansanat_eyebrow Small pill text.
 * @var string                                        $erfansanat_title   Project title.
 * @var string                                        $erfansanat_lead    Lead sentence.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_crumbs  = isset( $erfansanat_crumbs ) && is_array( $erfansanat_crumbs ) ? $erfansanat_crumbs : array();
$erfansanat_eyebrow = isset( $erfansanat_eyebrow ) ? (string) $erfansanat_eyebrow : '';
$erfansanat_title   = isset( $erfansanat_title ) ? (string) $erfansanat_title : '';
$erfansanat_lead    = isset( $erfansanat_lead ) ? (string) $erfansanat_lead : '';
?>
<section data-project-hero="true" class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		<div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div>
	</div>
	<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
		<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)] backdrop-blur">
			<?php foreach ( $erfansanat_crumbs as $erfansanat_index => $erfansanat_crumb ) : ?>
				<?php if ( $erfansanat_index > 0 ) : ?>
					<span class="flex min-w-0 items-center gap-1.5">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
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

		<div class="pt-6">
			<?php if ( '' !== $erfansanat_eyebrow ) : ?>
				<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
					<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( $erfansanat_eyebrow ); ?></span>
				</div>
			<?php endif; ?>
			<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $erfansanat_title ); ?></h1>
			<?php if ( '' !== $erfansanat_lead ) : ?>
				<p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( $erfansanat_lead ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
