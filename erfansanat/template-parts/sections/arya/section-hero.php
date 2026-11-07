<?php
/**
 * Arya landing hero (P07-T028). Reuses the shared design system (index/services
 * hero vocabulary). Heading/subheading/media are editable section fields.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'hero' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'درب بازکن هوشمند آریا' );
$es_subheading = (string) ( $es_fields['subheading'] ?? '' );
if ( '' === trim( $es_subheading ) ) {
	$es_subheading = 'تردد آسان، امنیت هوشمند؛ آریا درب پارکینگ شما را با ریموت اختصاصی، اپلیکیشن موبایل و سنسورهای ایمنی کنترل می‌کند — طراحی و تولید کارخانه عرفان صنعت اصفهان با ۲۴ ماه گارانتی تعویض.';
}
$es_image_id = (int) ( $es_fields['image'] ?? 0 );
$es_image    = $es_image_id > 0 ? (string) wp_get_attachment_image_url( $es_image_id, 'large' ) : '';
if ( ! $es_image ) {
	$es_image = ES_ASSETS_URI . 'images/light-element.jpg';
}
$es_video_id  = (int) ( $es_fields['video_id'] ?? 0 );
$es_video_url = $es_video_id > 0 ? (string) wp_get_attachment_url( $es_video_id ) : '';
?>
<section id="<?php echo esc_attr( $es_id ); ?>" class="relative scroll-mt-28 overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="hero">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-50"></div><div class="absolute left-[12%] top-20 h-64 w-64 rounded-full bg-emerald-200/50 blur-[100px]"></div><div class="absolute bottom-10 right-[8%] h-56 w-56 rounded-full bg-teal-100/70 blur-[90px]"></div></div>
	<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
		<div class="grid items-center gap-10 pb-14 lg:grid-cols-[1.1fr_0.9fr] lg:pb-20">
			<div>
				<div class="mb-6 inline-flex items-center gap-2 rounded-full bg-emerald-700 px-4 py-1.5 text-[12px] font-black text-white shadow-[0_0_24px_rgba(0,125,45,0.3)]"><span class="h-2 w-2 rounded-full bg-emerald-300"></span><?php esc_html_e( 'محصول هوشمند عرفان صنعت', 'erfansanat' ); ?></div>
				<h1 class="text-[2.4rem] font-black leading-[1.28] text-slate-950 sm:text-[3.1rem] lg:text-[3.7rem] lg:leading-[1.22]"><?php echo esc_html( $es_heading ); ?></h1>
				<p class="mt-6 max-w-[36rem] border-r-2 border-emerald-500/60 pr-5 text-[15px] font-medium leading-9 text-slate-600 lg:text-[16px]"><?php echo esc_html( $es_subheading ); ?></p>
				<div class="mt-8 flex flex-wrap gap-3">
					<a href="#cta" class="flex items-center gap-2.5 rounded-2xl bg-emerald-700 px-7 py-3.5 text-[14.5px] font-black text-white shadow-[0_16px_40px_-12px_rgba(0,125,45,0.45)] transition-all hover:bg-emerald-800"><?php esc_html_e( 'درخواست خرید آریا', 'erfansanat' ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a>
					<a href="#specs" class="flex items-center gap-2 rounded-2xl border border-emerald-200 bg-white px-7 py-3.5 text-[14px] font-bold text-emerald-800 shadow-sm transition-colors hover:border-emerald-400 hover:bg-emerald-50"><?php esc_html_e( 'مشخصات فنی', 'erfansanat' ); ?></a>
				</div>
			</div>
			<div class="relative mx-auto w-full max-w-[30rem]">
				<div aria-hidden="true" class="absolute -inset-3 rounded-[2.5rem] border border-emerald-200/80 bg-emerald-100/35"></div>
				<div class="relative overflow-hidden rounded-[2.25rem] border border-emerald-100 bg-white p-2 shadow-[0_36px_80px_-30px_rgba(0,80,40,0.28)]">
					<div class="relative overflow-hidden rounded-[1.85rem]">
						<?php if ( '' !== $es_video_url ) : ?>
							<video class="h-[22rem] w-full object-cover sm:h-[25rem]" controls preload="metadata" poster="<?php echo esc_url( $es_image ); ?>"><source src="<?php echo esc_url( $es_video_url ); ?>"/></video>
						<?php else : ?>
							<img src="<?php echo esc_url( $es_image ); ?>" alt="<?php echo esc_attr( $es_heading ); ?>" class="h-[22rem] w-full object-cover sm:h-[25rem]"/>
						<?php endif; ?>
						<div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
						<div class="pointer-events-none absolute bottom-4 inset-x-4 flex items-center justify-between rounded-2xl border border-white/80 bg-white/95 px-4 py-3 shadow-lg backdrop-blur-md"><span class="text-[12.5px] font-bold text-slate-900"><?php echo esc_html( $es_heading ); ?></span><span class="rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-1 text-[11px] font-black text-emerald-800"><?php esc_html_e( '۲۴ ماه گارانتی', 'erfansanat' ); ?></span></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-[#f8faf9]"></div>
</section>
