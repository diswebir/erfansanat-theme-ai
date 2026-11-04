<?php
/**
 * Home section S14: the closing contact call to action.
 *
 * The phone and e-mail blocks read the live settings rather than the mirror's
 * fixed copy, so a change in the panel reaches the page without an edit.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_link   = (array) SectionParts::field( $erfansanat_fields, 'link', array() );
$erfansanat_link2  = (array) SectionParts::field( $erfansanat_fields, 'link2', array() );
$erfansanat_phone  = SectionParts::phone();
$erfansanat_tel    = SectionParts::phone_href();
$erfansanat_email  = SectionParts::email();
$erfansanat_hours  = (string) \ErfanSanat\Theme\Settings::get( 'hours', '' );

// The first button falls back to the phone when no explicit link is set.
if ( empty( $erfansanat_link['url'] ) && '' !== $erfansanat_tel ) {
	$erfansanat_link = array(
		'title' => '' !== (string) ( $erfansanat_link['title'] ?? '' ) ? (string) $erfansanat_link['title'] : __( 'تماس مستقیم با کارشناسان فروش', 'erfansanat' ),
		'url'   => $erfansanat_tel,
	);
}
?>
<div class="mx-auto max-w-[1300px] overflow-hidden rounded-3xl border border-slate-200/85 bg-white p-6 shadow-xs sm:p-10 lg:p-12">
	<div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-10">

		<div class="flex flex-col items-start gap-4 lg:col-span-7">
			<?php
			SectionParts::heading(
				array(
					'eyebrow'  => (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ),
					'title'    => (string) SectionParts::field( $erfansanat_fields, 'title', '' ),
					'subtitle' => (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ),
				)
			);
			?>
			<?php if ( ! empty( $erfansanat_link['url'] ) || ! empty( $erfansanat_link2['url'] ) ) : ?>
				<div class="flex w-full flex-col gap-3 pt-2 sm:w-auto sm:flex-row sm:items-center">
					<?php if ( ! empty( $erfansanat_link['url'] ) ) : ?>
						<a href="<?php echo esc_url( (string) $erfansanat_link['url'] ); ?>" class="group inline-flex items-center justify-center gap-2.5 rounded-xl bg-emerald-700 px-6 py-3.5 text-xs sm:text-sm font-black text-white transition-colors hover:bg-emerald-800">
							<span><?php echo esc_html( (string) ( $erfansanat_link['title'] ?? '' ) ); ?></span>
						</a>
					<?php endif; ?>
					<?php if ( ! empty( $erfansanat_link2['url'] ) ) : ?>
						<a href="<?php echo esc_url( (string) $erfansanat_link2['url'] ); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-[#f6f8f7] px-5 py-3.5 text-xs sm:text-sm font-extrabold text-slate-800 transition-colors hover:border-emerald-300 hover:bg-emerald-50/60">
							<span><?php echo esc_html( (string) ( $erfansanat_link2['title'] ?? '' ) ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="flex flex-col gap-3 lg:col-span-5">
			<?php if ( '' !== $erfansanat_tel ) : ?>
				<div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4 transition-colors hover:border-emerald-300 hover:bg-white">
					<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
					</span>
					<div class="min-w-0 flex-1">
						<div class="text-[11.5px] font-semibold text-slate-500"><?php esc_html_e( 'تماس مستقیم با واحد فروش و مهندسی', 'erfansanat' ); ?></div>
						<a href="<?php echo esc_url( $erfansanat_tel ); ?>" class="mt-0.5 block truncate text-sm sm:text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700"><?php echo esc_html( $erfansanat_phone ); ?></a>
						<div class="mt-0.5 text-[11px] font-medium text-slate-500"><?php esc_html_e( 'واحد فروش و پشتیبانی فنی', 'erfansanat' ); ?></div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $erfansanat_email ) : ?>
				<div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4 transition-colors hover:border-emerald-300 hover:bg-white">
					<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
					</span>
					<div class="min-w-0 flex-1">
						<div class="text-[11.5px] font-semibold text-slate-500"><?php esc_html_e( 'پست الکترونیک سازمانی', 'erfansanat' ); ?></div>
						<a href="mailto:<?php echo esc_attr( $erfansanat_email ); ?>" dir="ltr" class="mt-0.5 block truncate text-sm sm:text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700"><?php echo esc_html( $erfansanat_email ); ?></a>
						<div class="mt-0.5 text-[11px] font-medium text-slate-500"><?php esc_html_e( 'دریافت استعلام فنی و پیش‌فاکتور رسمی', 'erfansanat' ); ?></div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $erfansanat_hours ) : ?>
				<div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4 transition-colors hover:border-emerald-300 hover:bg-white">
					<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
					</span>
					<div class="min-w-0 flex-1">
						<div class="text-[11.5px] font-semibold text-slate-500"><?php esc_html_e( 'ساعات پاسخگویی کارخانه', 'erfansanat' ); ?></div>
						<div class="mt-0.5 text-sm sm:text-base font-black text-slate-900 tabular-nums"><?php echo esc_html( $erfansanat_hours ); ?></div>
						<div class="mt-0.5 text-[11px] font-medium text-slate-500"><?php esc_html_e( 'پشتیبانی تلفنی پروژه‌ها در تمام ایام هفته', 'erfansanat' ); ?></div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
