<?php
/**
 * Home hero section — full home1 markup + JS hooks (P05-T007).
 *
 * Sliced from static-mirror/index.html (home1 variant). Scene 0 is the main
 * company video (HomeTab, P05-T005) when configured; project slides
 * (P05-T006) follow as scenes 1..N. Without a video the projects occupy
 * scenes 0..N-1. All data-home1-* / data-scene-* hooks consumed by the
 * immutable main.js (initHomeHeroVariants) are preserved, as are id=home,
 * eseCircleTextPath and home1-categories (AC-01/02/03).
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\HomeTab;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'hero' );
$es_type = (string) ( $es_section['type'] ?? 'hero' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'وقتی کیفیت و ماندگاری مهمه' );
$es_subheading = (string) ( $es_fields['subheading'] ?? 'طراح و تولیدکننده تجهیزات نورپردازی پیشرفته' );
$es_eyebrow    = (string) apply_filters( 'es_hero_eyebrow', 'المان‌های نوری شهری · استاندارد IP68' );
$es_tagline    = (string) apply_filters( 'es_hero_tagline', 'عرفان صنعت انتخاب توئه...' );

// Main video (P05-T005): per-page override, else global HomeTab option.
$es_video_id = (int) ( $es_fields['video_id'] ?? 0 );
if ( $es_video_id <= 0 ) {
	$es_video_id = HomeTab::hero_video_id();
}
$es_video_url = $es_video_id > 0 ? (string) wp_get_attachment_url( $es_video_id ) : '';
$es_poster    = HomeTab::hero_video_poster_url();
$es_preload   = HomeTab::hero_video_preload();
$es_fallback  = HomeTab::hero_video_fallback_url();
$es_has_video = '' !== $es_video_url;

// Project slides (P05-T006): per-page override, else global HomeTab option.
$es_slides_raw = isset( $es_fields['slides'] ) && is_array( $es_fields['slides'] ) ? $es_fields['slides'] : array();
if ( empty( $es_slides_raw ) ) {
	$es_slides_raw = HomeTab::hero_slides();
}
$es_slides = HomeTab::resolve_slides( $es_slides_raw );
$es_offset = $es_has_video ? 1 : 0;

// Scene 0 identity (video scene when a video is set, else the first project).
$es_about_url   = (string) apply_filters( 'es_hero_about_url', home_url( '/about/' ) );
$es_video_scene = (array) apply_filters(
	'es_hero_video_scene',
	array(
		'title' => 'تیزر رسمی خط تولید و پروژه‌های عرفان صنعت اصفهان',
		'cta'   => 'درباره کارخانه',
		'href'  => $es_about_url,
		'label' => 'معرفی شرکت',
	),
	$es_section
);
if ( $es_has_video ) {
	$es_scene0 = $es_video_scene;
} elseif ( ! empty( $es_slides ) ) {
	$es_scene0 = array(
		'title' => $es_slides[0]['title'],
		'cta'   => 'مشاهده پروژه',
		'href'  => $es_slides[0]['link'],
		'label' => $es_slides[0]['title'],
	);
} else {
	$es_scene0 = array( 'title' => $es_heading, 'cta' => '', 'href' => '', 'label' => '' );
}

// Category cards + overlay quick links (product_cat sourced).
$es_categories  = HomeTab::hero_categories();
$es_quick_links = HomeTab::hero_quick_links();
$es_quick_rows  = array_chunk( $es_quick_links, 4 );

// Category icon SVGs (static lucide markup keyed by name).
$es_icon_svgs = array(
	'cpu'       => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cpu h-6 w-6 stroke-[1.75]" aria-hidden="true"><path d="M12 20v2"></path><path d="M12 2v2"></path><path d="M17 20v2"></path><path d="M17 2v2"></path><path d="M2 12h2"></path><path d="M2 17h2"></path><path d="M2 7h2"></path><path d="M20 12h2"></path><path d="M20 17h2"></path><path d="M20 7h2"></path><path d="M7 20v2"></path><path d="M7 2v2"></path><rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="8" y="8" width="8" height="8" rx="1"></rect></svg>',
	'sparkles'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-6 w-6 stroke-[1.75]" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>',
	'trees'     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trees h-6 w-6 stroke-[1.75]" aria-hidden="true"><path d="M10 10v.2A3 3 0 0 1 8.9 16H5a3 3 0 0 1-1-5.8V10a3 3 0 0 1 6 0Z"></path><path d="M7 16v6"></path><path d="M13 19v3"></path><path d="M12 19h8.3a1 1 0 0 0 .7-1.7L18 14h.3a1 1 0 0 0 .7-1.7L16 9h.2a1 1 0 0 0 .8-1.7L13 3l-1.4 1.5"></path></svg>',
	'lightbulb' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lightbulb h-6 w-6 stroke-[1.75]" aria-hidden="true"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"></path><path d="M9 18h6"></path><path d="M10 22h4"></path></svg>',
	'waves'     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-waves h-6 w-6 stroke-[1.75]" aria-hidden="true"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path></svg>',
	'sun'       => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun h-6 w-6 stroke-[1.75]" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>',
	'flame'     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-flame h-6 w-6 stroke-[1.75]" aria-hidden="true"><path d="M12 3q1 4 4 6.5t3 5.5a1 1 0 0 1-14 0 5 5 0 0 1 1-3 1 1 0 0 0 5 0c0-2-1.5-3-1.5-5q0-2 2.5-4"></path></svg>',
);
?>
<section id="home" aria-label="بخش آغازین - طرح خانه ۱ (نوتچ معماری و دسته‌بندی‌ها)" class="es-section es-section--hero relative bg-[#f6f8f7] pt-28 pb-10 sm:pt-32 sm:pb-12 lg:pt-36 lg:pb-14" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
		<div data-home1-hero-frame="true" class="relative rounded-[2.25rem] bg-slate-950 sm:rounded-[2.75rem]">
			<div class="relative h-[470px] w-full rounded-[2.25rem] sm:h-[540px] sm:rounded-[2.75rem] lg:h-[590px]">
				<div class="pointer-events-none absolute inset-0 overflow-hidden rounded-[2.25rem] sm:rounded-[2.75rem]">
					<?php if ( $es_has_video ) : ?>
						<img src="<?php echo esc_url( $es_poster ); ?>" alt="<?php echo esc_attr( $es_video_scene['title'] ); ?>" data-home1-scene-img="0" class="pointer-events-none absolute inset-0 h-full w-full select-none object-cover object-center transition-all duration-700 ease-out z-10 scale-105 opacity-100"/>
					<?php endif; ?>
					<?php foreach ( $es_slides as $es_i => $es_slide ) : ?>
						<?php $es_idx = $es_i + $es_offset; ?>
						<img src="<?php echo esc_url( $es_slide['image'] ); ?>" alt="<?php echo esc_attr( $es_slide['title'] ); ?>" data-home1-scene-img="<?php echo (int) $es_idx; ?>" class="pointer-events-none absolute inset-0 h-full w-full select-none object-cover object-center transition-all duration-700 ease-out <?php echo 0 === $es_idx ? 'z-10 scale-105 opacity-100' : 'z-0 scale-100 opacity-0'; ?>"/>
					<?php endforeach; ?>
					<?php if ( ! $es_has_video && empty( $es_slides ) ) : ?>
						<img src="<?php echo esc_url( $es_fallback ); ?>" alt="<?php echo esc_attr( $es_heading ); ?>" data-home1-scene-img="0" class="pointer-events-none absolute inset-0 h-full w-full select-none object-cover object-center transition-all duration-700 ease-out z-10 scale-105 opacity-100"/>
					<?php endif; ?>
					<?php if ( $es_has_video ) : ?>
						<video src="<?php echo esc_url( $es_video_url ); ?>" poster="<?php echo esc_url( $es_poster ); ?>" autoplay muted loop playsinline preload="<?php echo esc_attr( $es_preload ); ?>" data-home1-inline-video="true" class="absolute inset-0 h-full w-full object-cover object-center transition-opacity duration-500 ease-out z-10 opacity-100"></video>
					<?php endif; ?>
				</div>
				<div data-home1-atmosphere="true" class="pointer-events-none absolute inset-0 overflow-hidden rounded-[2.25rem] transition-opacity duration-500 sm:rounded-[2.75rem] opacity-100"><div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-slate-950/45"></div><div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_52%,rgba(16,185,129,0.22),transparent_62%)]"></div></div>
				<div data-home1-video-controls="true" class="absolute top-5 left-5 z-30 flex items-center gap-2 transition-all duration-500 pointer-events-none -translate-y-3 opacity-0"><button type="button" data-home1-video-close="true" class="inline-flex items-center gap-2 rounded-2xl border border-white/30 bg-slate-950/75 px-4 py-2.5 text-xs font-black text-white shadow-xl backdrop-blur-xl transition-all hover:border-emerald-400 hover:bg-slate-900 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pause h-3.5 w-3.5 text-emerald-400" aria-hidden="true"><rect x="14" y="3" width="5" height="18" rx="1"></rect><rect x="5" y="3" width="5" height="18" rx="1"></rect></svg><span>توقف ویدیو و بازگشت به نمای طرح</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-4 w-4 text-slate-300" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button><button type="button" data-home1-video-mute="true" aria-label="قطع یا وصل صدای ویدیو" class="grid h-10 w-10 place-items-center rounded-2xl border border-white/30 bg-slate-950/75 text-white shadow-xl backdrop-blur-xl transition-all hover:border-emerald-400 hover:bg-slate-900 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-volume2 lucide-volume-2 h-4 w-4 text-emerald-400" aria-hidden="true"><path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"></path><path d="M16 9a5 5 0 0 1 0 6"></path><path d="M19.364 18.364a9 9 0 0 0 0-12.728"></path></svg></button></div>
				<div data-home1-overlay-item="true" class="absolute -top-px -right-px z-20 transition-all duration-500 translate-x-0 translate-y-0 opacity-100">
					<div class="relative w-[240px] max-w-[74vw] rounded-bl-[1.75rem] bg-[#f6f8f7] px-3.5 pt-2.5 pb-3.5 sm:w-[295px] sm:rounded-bl-[2.15rem] sm:px-5 sm:pt-3 sm:pb-4 lg:w-[335px] lg:rounded-bl-[2.35rem] lg:px-6 lg:pt-3.5 lg:pb-4.5">
						<div class="mb-1 flex items-center gap-1.5 text-[10px] font-extrabold text-emerald-700 sm:text-[11px]">
							<span class="inline-block h-3 w-1 shrink-0 rounded-full bg-emerald-600"></span>
							<span class="truncate"><?php echo esc_html( $es_eyebrow ); ?></span>
						</div>
						<h1 class="text-right text-[13.5px] leading-snug font-black tracking-tight text-slate-900 sm:text-[17px] lg:text-[19.5px]"><?php echo esc_html( $es_heading ); ?></h1>
						<div class="mt-1.5 flex items-center justify-start sm:mt-2">
							<div class="relative inline-flex w-full items-center justify-between gap-2 overflow-hidden rounded-xl border border-emerald-500/25 bg-white px-3 py-1.5 text-right shadow-2xs sm:px-3.5 sm:py-2">
								<span aria-hidden="true" class="absolute inset-y-1.5 right-1.5 w-1 rounded-full bg-emerald-600"></span>
								<span class="pr-2 text-[12.5px] font-black tracking-tight text-emerald-950 sm:text-[15.5px] lg:text-[17.5px]"><?php echo esc_html( $es_tagline ); ?></span>
								<span class="hidden rounded-lg bg-emerald-700 px-2 py-0.5 font-mono text-[9px] font-black tracking-wider text-white sm:inline-block">ESE</span>
							</div>
						</div>
						<svg viewBox="0 0 36 36" aria-hidden="true" class="pointer-events-none absolute -left-[35px] top-0 h-9 w-9 text-[#f6f8f7]"><path d="M36 0H0C19.882 0 36 16.118 36 36V0Z" fill="currentColor"></path></svg>
						<svg viewBox="0 0 36 36" aria-hidden="true" class="pointer-events-none absolute -bottom-[35px] right-0 h-9 w-9 text-[#f6f8f7]"><path d="M36 0V36C36 16.118 19.882 0 0 0H36Z" fill="currentColor"></path></svg>
					</div>
				</div>
				<div data-home1-overlay-item="true" class="absolute left-4 top-28 z-20 hidden max-w-[540px] flex-col gap-2 transition-all duration-500 md:top-6 md:left-6 md:flex lg:left-7 lg:top-7 translate-x-0 translate-y-0 opacity-100">
					<?php foreach ( $es_quick_rows as $es_row ) : ?>
						<div class="flex flex-wrap items-center justify-end gap-1.5">
							<?php foreach ( $es_row as $es_ql ) : ?>
								<a href="<?php echo esc_url( $es_ql['href'] ); ?>" class="rounded-xl border border-white/30 bg-slate-950/35 px-3.5 py-1.5 text-[11.5px] font-bold text-white backdrop-blur-md transition-all duration-200 hover:border-white hover:bg-white hover:text-slate-950"><?php echo esc_html( $es_ql['label'] ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div data-home1-overlay-item="true" class="absolute inset-0 z-10 flex items-center justify-center transition-all duration-500 scale-100 opacity-100"><div class="relative flex items-center justify-center"><div dir="ltr" class="pointer-events-none absolute bottom-[62%] left-1/2 -translate-x-1/2 select-none ps-[0.16em] font-mono text-[68px] xs:text-[84px] leading-none font-black tracking-[0.16em] text-white/95 drop-shadow-[0_18px_42px_rgba(0,0,0,0.7)] sm:text-[124px] lg:text-[148px]">ESE</div><button type="button" data-home1-play-trigger="true" aria-label="پخش کامل تیزر ویدیویی شرکت در همین بخش" class="group relative z-10 flex h-24 w-24 items-center justify-center rounded-full border border-white/45 bg-slate-950/60 text-white shadow-[0_16px_45px_rgba(0,0,0,0.55),0_0_50px_rgba(16,185,129,0.45)] backdrop-blur-xl transition-transform duration-300 hover:scale-105 sm:h-28 sm:w-28 cursor-pointer"><svg viewBox="0 0 100 100" class="pointer-events-none absolute inset-0 m-auto h-[calc(100%-12px)] w-[calc(100%-12px)] animate-[spin_36s_linear_infinite]"><defs><path id="eseCircleTextPath" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0"></path></defs><text class="fill-white/95 font-mono text-[8.2px] font-bold tracking-[2.1px] uppercase"><textPath href="#eseCircleTextPath" startOffset="0%">ERFAN SANAT ESFAHAN • PLAY VIDEO •</textPath></text></svg><span class="grid h-11 w-11 place-items-center rounded-full border border-white/45 bg-emerald-500 text-slate-950 shadow-[0_0_25px_rgba(16,185,129,0.75)] transition-transform duration-300 group-hover:scale-110 sm:h-12 sm:w-12"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-play h-5 w-5 translate-x-[1px] fill-current" aria-hidden="true"><path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"></path></svg></span></button></div></div>
				<div data-home1-overlay-item="true" class="absolute right-5 bottom-16 z-20 hidden items-center gap-2 transition-all duration-500 lg:flex translate-y-0 opacity-100">
					<a href="<?php echo esc_url( $es_scene0['href'] ); ?>" data-home1-scene-link="true" class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-slate-950/65 px-3.5 py-2 text-xs font-extrabold text-white backdrop-blur-xl transition-colors hover:border-emerald-400 hover:bg-slate-950/85">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-3.5 w-3.5 text-emerald-400" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
						<span data-home1-scene-title="true"><?php echo esc_html( $es_scene0['title'] ); ?></span>
						<span class="text-white/40">·</span>
						<span data-home1-scene-cta="true" class="text-emerald-300"><?php echo esc_html( $es_scene0['cta'] ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3.5 w-3.5 text-emerald-300" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg>
					</a>
				</div>
				<div data-home1-overlay-item="true" class="absolute inset-x-2 bottom-12 z-20 flex items-center justify-start sm:justify-center gap-1 overflow-x-auto no-scrollbar rounded-2xl border border-white/25 bg-slate-950/70 p-1.5 backdrop-blur-xl transition-all duration-500 lg:inset-x-auto lg:bottom-16 lg:left-5 lg:justify-start lg:gap-1.5 translate-y-0 opacity-100" role="tablist" aria-label="انتخاب تیزر معرفی شرکت یا پروژه‌های استانی">
					<?php if ( $es_has_video ) : ?>
						<button type="button" data-home1-scene-btn="0" data-scene-title="<?php echo esc_attr( $es_video_scene['title'] ); ?>" data-scene-href="<?php echo esc_attr( $es_video_scene['href'] ); ?>" data-scene-cta="<?php echo esc_attr( $es_video_scene['cta'] ); ?>" data-scene-is-video="true" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-2.5 py-1.5 text-[11px] font-extrabold transition-all cursor-pointer whitespace-nowrap bg-emerald-500 text-slate-950 shadow-xs">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-play h-3 w-3 fill-current" aria-hidden="true"><path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"></path></svg>
							<span><?php echo esc_html( $es_video_scene['label'] ); ?></span>
						</button>
					<?php endif; ?>
					<?php foreach ( $es_slides as $es_i => $es_slide ) : ?>
						<?php
						$es_idx        = $es_i + $es_offset;
						$es_city_terms = get_the_terms( $es_slide['project_id'], 'city' );
						$es_label      = ( $es_city_terms && ! is_wp_error( $es_city_terms ) ) ? $es_city_terms[0]->name : $es_slide['title'];
						?>
						<button type="button" data-home1-scene-btn="<?php echo (int) $es_idx; ?>" data-scene-title="<?php echo esc_attr( $es_slide['title'] ); ?>" data-scene-href="<?php echo esc_attr( $es_slide['link'] ); ?>" data-scene-cta="مشاهده پروژه" data-scene-is-video="false" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-2.5 py-1.5 text-[11px] font-extrabold transition-all cursor-pointer whitespace-nowrap <?php echo 0 === $es_idx ? 'bg-emerald-500 text-slate-950 shadow-xs' : 'text-white/85 hover:bg-white/10 hover:text-white'; ?>">
							<span><?php echo esc_html( $es_label ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
				<div class="absolute inset-x-0 -bottom-px z-30 flex justify-center">
					<div data-home1-bottom-tab="true" class="relative rounded-t-[1.85rem] bg-[#f6f8f7] px-8 pt-3.5 pb-1.5 text-center transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] sm:rounded-t-[2.25rem] sm:px-14 sm:pt-4 sm:pb-2 translate-y-0"><svg viewBox="0 0 32 32" aria-hidden="true" data-home1-bottom-fillet="true" class="pointer-events-none absolute -left-[31px] bottom-0 h-8 w-8 text-[#f6f8f7] transition-opacity duration-500 opacity-100"><path d="M32 32H0C17.673 32 32 17.673 32 0V32Z" fill="currentColor"></path></svg><svg viewBox="0 0 32 32" aria-hidden="true" data-home1-bottom-fillet="true" class="pointer-events-none absolute -right-[31px] bottom-0 h-8 w-8 text-[#f6f8f7] transition-opacity duration-500 opacity-100"><path d="M0 32H32C14.327 32 0 17.673 0 0V32Z" fill="currentColor"></path></svg><div class="relative inline-block px-3"><span aria-hidden="true" class="absolute inset-x-1 bottom-1 h-3 rounded-md bg-emerald-200/75"></span><h2 class="relative text-[15px] font-black text-slate-900 sm:text-lg lg:text-[20px]">پربازدیدترین دسته‌بندی‌ها</h2></div></div>
				</div>
			</div>
		</div>
		<div id="home1-categories" data-home1-categories-grid="true" class="relative z-20 -mt-px grid grid-cols-2 gap-3 bg-[#f6f8f7] pt-3 transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] sm:grid-cols-4 lg:grid-cols-7 lg:gap-3.5 translate-y-0 pb-0">
			<?php foreach ( $es_categories as $es_cat ) : ?>
				<a href="<?php echo esc_url( $es_cat['href'] ); ?>" class="card-light group relative flex flex-col items-center justify-between rounded-2xl px-3 py-5 text-center col-span-1 last:col-span-2 sm:last:col-span-1">
					<div class="grid h-13 w-13 place-items-center rounded-2xl border border-emerald-100 bg-emerald-50/60 text-emerald-700 transition-colors duration-300 group-hover:bg-emerald-600 group-hover:text-white">
						<?php echo $es_icon_svgs[ $es_cat['icon'] ] ?? $es_icon_svgs['lightbulb']; // Static lucide SVG markup. ?>
					</div>
					<div class="mt-3.5">
						<div class="text-[12.5px] font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( $es_cat['title'] ); ?></div>
						<div class="mt-1 text-[10.5px] font-medium text-slate-500"><?php echo esc_html( $es_cat['subtitle'] ); ?></div>
					</div>
					<div class="mt-3 inline-flex items-center gap-1 border-t border-slate-100 pt-2 text-[10.5px] font-bold text-emerald-700 tabular-nums">
						<span><?php echo esc_html( $es_cat['count'] ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3 w-3" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
