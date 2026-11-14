# -*- coding: utf-8 -*-
import json
svg=json.load(open('temp/phase-05-homepage-and-sections/ref-hero-svg.json',encoding='utf-8'))
blocks=json.load(open('temp/phase-05-homepage-and-sections/ref-hero-blocks.json',encoding='utf-8'))
play=json.load(open('temp/phase-05-homepage-and-sections/ref-hero-play.json',encoding='utf-8'))['play_overlay']
icons=svg['icons']; var=svg['variants']; notch=svg['notch']

tpl=r'''<?php
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
	'cpu'       => '__ICON_CPU__',
	'sparkles'  => '__ICON_SPARKLES__',
	'trees'     => '__ICON_TREES__',
	'lightbulb' => '__ICON_LIGHTBULB__',
	'waves'     => '__ICON_WAVES__',
	'sun'       => '__ICON_SUN__',
	'flame'     => '__ICON_FLAME__',
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
				__ATMOSPHERE__
				__VIDEO_CONTROLS__
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
						__NOTCH0__
						__NOTCH1__
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
				__PLAY_OVERLAY__
				<div data-home1-overlay-item="true" class="absolute right-5 bottom-16 z-20 hidden items-center gap-2 transition-all duration-500 lg:flex translate-y-0 opacity-100">
					<a href="<?php echo esc_url( $es_scene0['href'] ); ?>" data-home1-scene-link="true" class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-slate-950/65 px-3.5 py-2 text-xs font-extrabold text-white backdrop-blur-xl transition-colors hover:border-emerald-400 hover:bg-slate-950/85">
						__MAP_PIN__
						<span data-home1-scene-title="true"><?php echo esc_html( $es_scene0['title'] ); ?></span>
						<span class="text-white/40">·</span>
						<span data-home1-scene-cta="true" class="text-emerald-300"><?php echo esc_html( $es_scene0['cta'] ); ?></span>
						__ARROW_UL_35__
					</a>
				</div>
				<div data-home1-overlay-item="true" class="absolute inset-x-2 bottom-12 z-20 flex items-center justify-start sm:justify-center gap-1 overflow-x-auto no-scrollbar rounded-2xl border border-white/25 bg-slate-950/70 p-1.5 backdrop-blur-xl transition-all duration-500 lg:inset-x-auto lg:bottom-16 lg:left-5 lg:justify-start lg:gap-1.5 translate-y-0 opacity-100" role="tablist" aria-label="انتخاب تیزر معرفی شرکت یا پروژه‌های استانی">
					<?php if ( $es_has_video ) : ?>
						<button type="button" data-home1-scene-btn="0" data-scene-title="<?php echo esc_attr( $es_video_scene['title'] ); ?>" data-scene-href="<?php echo esc_attr( $es_video_scene['href'] ); ?>" data-scene-cta="<?php echo esc_attr( $es_video_scene['cta'] ); ?>" data-scene-is-video="true" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-2.5 py-1.5 text-[11px] font-extrabold transition-all cursor-pointer whitespace-nowrap bg-emerald-500 text-slate-950 shadow-xs">
							__PLAY_3__
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
					__BOTTOM_TAB__
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
						__ARROW_UL_3__
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
'''
repl={
 '__ATMOSPHERE__':blocks['atmosphere'],
 '__VIDEO_CONTROLS__':blocks['video_controls'],
 '__PLAY_OVERLAY__':play,
 '__BOTTOM_TAB__':blocks['bottom_tab'],
 '__NOTCH0__':notch[0],
 '__NOTCH1__':notch[1],
 '__MAP_PIN__':var['map_pin'],
 '__ARROW_UL_35__':var['arrow_ul_35'],
 '__ARROW_UL_3__':var['arrow_ul_3'],
 '__PLAY_3__':var['play_3'],
 '__ICON_CPU__':icons['cpu'],
 '__ICON_SPARKLES__':icons['sparkles'],
 '__ICON_TREES__':icons['trees'],
 '__ICON_LIGHTBULB__':icons['lightbulb'],
 '__ICON_WAVES__':icons['waves'],
 '__ICON_SUN__':icons['sun'],
 '__ICON_FLAME__':icons['flame'],
}
# escape single quotes in SVG for PHP single-quoted strings
for k,v in repl.items():
    if k.startswith('__ICON_'):
        v=v.replace("\\","\\\\").replace("'","\\'")
    tpl=tpl.replace(k,v)
open('erfansanat/template-parts/sections/home/section-hero.php','w',encoding='utf-8').write(tpl)
print("written section-hero.php",len(tpl),"bytes")
# verify no leftover tokens
import re
left=re.findall(r'__[A-Z0-9_]+__',tpl)
print("leftover tokens:",left)
