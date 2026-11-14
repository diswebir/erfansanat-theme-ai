<?php
/**
 * Contact hero (P07-T021). DOM parity with contact.html section 1.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\FooterTab;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'hero' );

$es_heading   = (string) ( $es_fields['heading'] ?? 'تماس با ما؛ پاسخگوی شما هستیم' );
$es_highlight = (string) ( $es_fields['highlight'] ?? 'پاسخگوی شما هستیم' );
$es_badge     = (string) ( $es_fields['badge'] ?? 'مشاوره و استعلام قیمت رایگان' );
$es_content   = (string) ( $es_fields['content'] ?? '' );
if ( '' === trim( wp_strip_all_tags( $es_content ) ) ) {
	$es_content = '<p>راه‌های ارتباط و ثبت سفارش در شرکت دانش‌بنیان عرفان صنعت اصفهان؛ مشاوره رایگان، استعلام قیمت روز و راهنمایی کامل نصب و اجرا</p>';
}
$es_contact = FooterTab::contact();
$es_fa      = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_phone   = (string) $es_contact['phone'];
$es_mobile  = (string) $es_contact['mobile'];
$es_heading_html = esc_html( $es_heading );
if ( '' !== $es_highlight && false !== strpos( $es_heading, $es_highlight ) ) {
	$es_heading_html = str_replace( esc_html( $es_highlight ), '<span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent">' . esc_html( $es_highlight ) . '</span>', $es_heading_html );
}
?>
<section class="relative bg-[#f6f8f7] pt-28 lg:pt-36" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="hero">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
		<div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-50"></div>
		<div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/50 blur-[110px]"></div>
		<div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/70 blur-[110px]"></div>
		<span class="absolute -bottom-10 left-0 select-none whitespace-nowrap text-[10rem] font-black leading-none text-emerald-950/[0.03] lg:text-[14rem]"><?php esc_html_e( 'عرفان صنعت', 'erfansanat' ); ?></span>
	</div>
	<div class="relative z-10 mx-auto max-w-7xl px-5 pb-16 lg:px-8">
		<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a>
			<span class="flex min-w-0 items-center gap-1.5"><svg aria-hidden="true" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php esc_html_e( 'تماس با ما', 'erfansanat' ); ?></span></span>
		</nav>
		<div class="mx-auto max-w-3xl pt-12 text-center">
			<div class="inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white py-1.5 pl-5 pr-1.5 shadow-sm"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-700 text-white shadow-[0_0_20px_rgba(0,125,45,0.35)]"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg></span><span class="text-[13px] font-extrabold text-emerald-800"><?php echo esc_html( $es_badge ); ?></span></div>
			<h1 class="mt-6 text-[2.2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[3rem] lg:text-[3.4rem]"><?php echo $es_heading_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?></h1>
			<div class="mx-auto mt-5 max-w-2xl text-[15px] font-medium leading-9 text-slate-600"><?php echo wp_kses_post( $es_content ); ?></div>
			<div class="mt-8 flex flex-wrap items-center justify-center gap-3">
				<?php if ( '' !== $es_phone ) : ?>
					<a href="<?php echo esc_url( 'tel:' . preg_replace( '/\s+/', '', $es_phone ) ); ?>" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-6 py-3.5 text-[13.5px] font-black text-white shadow-sm transition-colors hover:bg-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><?php echo esc_html( strtr( str_replace( '-', ' - ', $es_phone ), $es_fa ) ); ?> <span class="text-[11px] font-bold opacity-80"><?php esc_html_e( '(۱۰ خط ویژه)', 'erfansanat' ); ?></span></a>
				<?php endif; ?>
				<?php if ( '' !== $es_mobile ) : ?>
					<a href="<?php echo esc_url( 'tel:' . preg_replace( '/\s+/', '', $es_mobile ) ); ?>" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-5 py-3.5 text-[13px] font-extrabold text-emerald-800 transition-colors hover:border-emerald-400 hover:bg-emerald-50"><?php echo esc_html( sprintf( __( 'همراه فروش (%s)', 'erfansanat' ), strtr( $es_mobile, $es_fa ) ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div aria-hidden="true" class="relative z-10 h-10 rounded-t-[2.5rem] bg-[#f8faf9]"></div>
</section>
