<?php
/**
 * Global contact CTA (id=contact), shared across pages (P04-T016).
 *
 * Content (eyebrow/heading/description/button labels/hours) comes from theme
 * options; phone/email reuse the shared contact settings. The whole section is
 * toggleable (AC-03): when disabled it renders nothing. All output escaped.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

$es_cta = FooterTab::cta();
if ( ! $es_cta['enabled'] ) {
	return;
}

$es_contact   = FooterTab::contact();
$es_tel       = preg_replace( '/\s+/', '', (string) $es_contact['phone'] );
$es_quote_url = '' !== $es_cta['quote_url'] ? $es_cta['quote_url'] : Menus::page_url( 'contact' );

// Persian-digit phone display: 031-91091011 -> ۰۳۱ - ۹۱۰۹۱۰۱۱.
$es_fa_digits     = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_phone_display = strtr( str_replace( '-', ' - ', (string) $es_contact['phone'] ), $es_fa_digits );

// Heading with an optional emerald highlight span (kept editable).
$es_heading_html = esc_html( $es_cta['heading'] );
if ( '' !== $es_cta['heading_highlight'] && false !== strpos( $es_cta['heading'], $es_cta['heading_highlight'] ) ) {
	$es_heading_html = str_replace(
		esc_html( $es_cta['heading_highlight'] ),
		'<span class="text-emerald-700">' . esc_html( $es_cta['heading_highlight'] ) . '</span>',
		$es_heading_html
	);
}
?>
<section id="contact" class="relative scroll-mt-24 px-4 pt-10 pb-6 sm:px-6 sm:pt-14 sm:pb-8 lg:px-8">
	<div class="mx-auto max-w-[1300px] overflow-hidden rounded-3xl border border-slate-200/85 bg-white p-6 shadow-xs sm:p-10 lg:p-12">
		<div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-10">

			<div class="flex flex-col items-start gap-4 lg:col-span-7">
				<div>
					<div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]">
						<span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
						<span><?php echo esc_html( $es_cta['eyebrow'] ); ?></span>
					</div>
				</div>
				<div>
					<h2 class="text-2xl font-black leading-[1.35] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]"><?php echo $es_heading_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				</div>
				<div>
					<p class="max-w-xl text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8"><?php echo esc_html( $es_cta['description'] ); ?></p>
				</div>
				<div class="flex w-full flex-col gap-3 pt-2 sm:w-auto sm:flex-row sm:items-center">
					<a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="group inline-flex items-center justify-center gap-2.5 rounded-xl bg-emerald-700 px-6 py-3.5 text-xs sm:text-sm font-black text-white transition-colors hover:bg-emerald-800">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>
						<span><?php echo esc_html( $es_cta['phone_label'] ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg>
					</a>
					<a href="<?php echo esc_url( $es_quote_url ); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-[#f6f8f7] px-5 py-3.5 text-xs sm:text-sm font-extrabold text-slate-800 transition-colors hover:border-emerald-300 hover:bg-emerald-50/60">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check2 lucide-file-check-2 h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="m3 15 2 2 4-4"></path></svg>
						<span><?php echo esc_html( $es_cta['quote_label'] ); ?></span>
					</a>
				</div>
			</div>

			<div class="flex flex-col gap-3 lg:col-span-5">
				<div>
					<div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4 transition-colors hover:border-emerald-300 hover:bg-white">
						<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-5 w-5" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg></span>
						<div class="min-w-0 flex-1">
							<div class="text-[11.5px] font-semibold text-slate-500">تماس مستقیم با واحد فروش و مهندسی</div>
							<a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="mt-0.5 block truncate text-sm sm:text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700"><?php echo esc_html( $es_phone_display ); ?></a>
							<div class="mt-0.5 text-[11px] font-medium text-slate-500">۱۰ خط ویژه</div>
						</div>
					</div>
				</div>
				<div>
					<div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4 transition-colors hover:border-emerald-300 hover:bg-white">
						<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail h-5 w-5" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg></span>
						<div class="min-w-0 flex-1">
							<div class="text-[11.5px] font-semibold text-slate-500">پست الکترونیک سازمانی</div>
							<a href="<?php echo esc_url( 'mailto:' . $es_contact['email'] ); ?>" dir="ltr" class="mt-0.5 block truncate text-sm sm:text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700"><?php echo esc_html( $es_contact['email'] ); ?></a>
							<div class="mt-0.5 text-[11px] font-medium text-slate-500">دریافت استعلام فنی و پیش‌فاکتور رسمی</div>
						</div>
					</div>
				</div>
				<div>
					<div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4 transition-colors hover:border-emerald-300 hover:bg-white">
						<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock3 lucide-clock-3 h-5 w-5" aria-hidden="true"><path d="M12 6v6h4"></path><circle cx="12" cy="12" r="10"></circle></svg></span>
						<div class="min-w-0 flex-1">
							<div class="text-[11.5px] font-semibold text-slate-500">ساعات پاسخگویی کارخانه</div>
							<div class="mt-0.5 text-sm sm:text-base font-black text-slate-900 tabular-nums"><?php echo esc_html( $es_cta['hours'] ); ?></div>
							<div class="mt-0.5 text-[11px] font-medium text-slate-500">پشتیبانی تلفنی پروژه‌ها در تمام ایام هفته</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>
