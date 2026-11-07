<?php
/**
 * Warranty & after-sales page (P07-T024).
 * Reference: static-mirror/refund_returns.html.
 *
 * Cards (icon/title/text), covered/excluded lists and process steps come from
 * the editable repeaters (PagesTab::warranty()). Contact/inquiry links use the
 * shared contact options. The global contact CTA is rendered by the footer —
 * never duplicated here.
 *
 * Template Name: شرایط گارانتی و خدمات پس از فروش
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\PagesTab;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

get_header();

$es_heading = (string) es_option( 'warranty_heading' );
if ( '' === trim( $es_heading ) ) {
	$es_heading = 'شرایط گارانتی و خدمات پس از فروش';
}
$es_intro   = (string) es_option( 'warranty_intro' );
$es_data    = PagesTab::warranty();
$es_contact = FooterTab::contact();
$es_fa      = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_phone   = (string) $es_contact['phone'];
$es_tel     = preg_replace( '/\s+/', '', $es_phone );
$es_contact_url = home_url( '/contact/' );

// Editable card icons (validated against the option icon allow-list). Static
// lucide SVG markup keyed by icon name; unknown keys fall back to shield.
$es_card_icons = array(
	'shield' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-6 w-6" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>',
	'check'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-6 w-6" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>',
	'star'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-6 w-6" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.12 2.12 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.12 2.12 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.12 2.12 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.12 2.12 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.12 2.12 0 0 0 1.597-1.16z"></path></svg>',
	'truck'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck h-6 w-6" aria-hidden="true"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg>',
	'clock'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-6 w-6" aria-hidden="true"><path d="M12 6v6l4 2"></path><circle cx="12" cy="12" r="10"></circle></svg>',
);
$es_check_svg   = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check mt-1 h-4.5 w-4.5 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>';
$es_alert_svg   = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert mt-1 h-4.5 w-4.5 shrink-0 text-amber-600" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>';
$es_phone_svg   = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>';
?>
<main class="min-h-screen bg-[#f8faf9]">
	<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div><div class="light-dot-grid absolute inset-0 opacity-45"></div><div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div></div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php echo esc_html( $es_heading ); ?></span></span></nav>
			<div class="pt-6">
				<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs"><span class="h-2 w-2 rounded-full bg-emerald-600"></span><span><?php esc_html_e( 'ضمانت‌نامه رسمی کارخانه دانش‌بنیان عرفان صنعت', 'erfansanat' ); ?></span></div>
				<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $es_heading ); ?> <span class="text-emerald-700"><?php esc_html_e( 'محصولات', 'erfansanat' ); ?></span></h1>
				<?php if ( '' !== trim( $es_intro ) ) : ?><p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( $es_intro ); ?></p><?php endif; ?>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8 lg:py-20">
		<?php if ( $es_data['cards'] ) : ?>
			<div class="grid gap-6 md:grid-cols-3">
				<?php foreach ( $es_data['cards'] as $es_card ) : ?>
					<div><div class="card-light flex h-full flex-col rounded-[1.75rem] border border-emerald-100 bg-white p-6"><span class="mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-emerald-700 text-white shadow-sm"><?php echo $es_card_icons[ $es_card['icon'] ] ?? $es_card_icons['shield']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG, key validated. ?></span><h2 class="text-[17px] font-black text-slate-900"><?php echo esc_html( $es_card['title'] ); ?></h2><?php if ( '' !== $es_card['description'] ) : ?><p class="mt-3 text-[13.5px] leading-7 text-slate-600"><?php echo esc_html( $es_card['description'] ); ?></p><?php endif; ?></div></div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="mt-12 grid gap-8 lg:grid-cols-2">
			<div>
				<div class="h-full rounded-[2rem] border border-emerald-200/90 bg-white p-7 shadow-xs sm:p-8">
					<div class="mb-5 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-100 text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-5.5 w-5.5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg></span><div><h2 class="text-lg font-black text-slate-900"><?php esc_html_e( 'موارد تحت پوشش گارانتی تعویض ۲۴ ماهه', 'erfansanat' ); ?></h2><p class="text-[12px] font-semibold text-emerald-700"><?php esc_html_e( 'تعویض بی‌قید و شرط قطعه بدون دریافت هزینه', 'erfansanat' ); ?></p></div></div>
					<ul class="space-y-4">
						<?php foreach ( $es_data['covered'] as $es_text ) : ?>
							<li class="flex items-start gap-3 text-[13.5px] font-medium leading-7 text-slate-700"><?php echo $es_check_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span><?php echo esc_html( $es_text ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<div>
				<div class="h-full rounded-[2rem] border border-amber-200/90 bg-white p-7 shadow-xs sm:p-8">
					<div class="mb-5 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-100 text-amber-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert h-5.5 w-5.5" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg></span><div><h2 class="text-lg font-black text-slate-900"><?php esc_html_e( 'موارد خارج از شمول گارانتی رایگان', 'erfansanat' ); ?></h2><p class="text-[12px] font-semibold text-amber-800"><?php esc_html_e( 'مشمول خدمات تعمیرات و تأمین قطعات ۵ ساله با تعرفه کارخانه', 'erfansanat' ); ?></p></div></div>
					<ul class="space-y-4">
						<?php foreach ( $es_data['excluded'] as $es_text ) : ?>
							<li class="flex items-start gap-3 text-[13.5px] font-medium leading-7 text-slate-700"><?php echo $es_alert_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span><?php echo esc_html( $es_text ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>

		<?php if ( $es_data['steps'] ) : ?>
			<div class="mt-12">
				<div class="rounded-[2rem] border border-slate-200 bg-slate-50/80 p-7 sm:p-10">
					<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
						<div><span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-3 py-1 text-[11.5px] font-extrabold text-emerald-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wrench h-3.5 w-3.5" aria-hidden="true"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"></path></svg><?php esc_html_e( 'فرآیند اجرایی پشتیبانی فنی', 'erfansanat' ); ?></span><h2 class="mt-2 text-xl font-black text-slate-950 sm:text-2xl"><?php esc_html_e( 'مراحل ثبت درخواست گارانتی و خدمات پس از فروش', 'erfansanat' ); ?></h2></div>
						<?php if ( '' !== $es_phone ) : ?><a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="inline-flex items-center gap-2 rounded-xl bg-emerald-800 px-5 py-3 text-[13px] font-black text-white transition-colors hover:bg-emerald-900 whitespace-nowrap"><?php echo $es_phone_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span><?php echo esc_html( sprintf( __( 'واحد خدمات پس از فروش: %s', 'erfansanat' ), strtr( str_replace( '-', ' - ', $es_phone ), $es_fa ) ) ); ?></span></a><?php endif; ?>
					</div>
					<div class="grid gap-6 md:grid-cols-3">
						<?php foreach ( $es_data['steps'] as $es_i => $es_step ) : ?>
							<div class="rounded-2xl border border-slate-200/90 bg-white p-6"><div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-700 text-[15px] font-black text-white"><?php echo esc_html( strtr( (string) ( $es_i + 1 ), $es_fa ) ); ?></div><h3 class="text-[15.5px] font-black text-slate-900"><?php echo esc_html( $es_step['title'] ); ?></h3><?php if ( '' !== $es_step['description'] ) : ?><p class="mt-2 text-[13px] leading-7 text-slate-600"><?php echo esc_html( $es_step['description'] ); ?></p><?php endif; ?></div>
						<?php endforeach; ?>
					</div>
					<div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 pt-6 text-[13px] font-bold text-slate-600">
						<span><?php echo esc_html( sprintf( __( 'آدرس کارخانه جهت ارسال مرسولات گارانتی: %s', 'erfansanat' ), $es_contact['address'] ) ); ?></span>
						<a href="<?php echo esc_url( $es_contact_url ); ?>" class="text-emerald-700 underline-offset-4 hover:underline"><?php esc_html_e( 'ثبت آنلاین درخواست پشتیبانی ←', 'erfansanat' ); ?></a>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</section>
</main>
<?php
get_footer();
