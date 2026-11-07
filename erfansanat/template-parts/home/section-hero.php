<?php
/**
 * Home section S01: hero with scene switcher, inline video and category grid.
 *
 * Markup follows `static-mirror/index.html` S01 including every `data-home1-*`
 * hook that the transferred main.js binds. The only structural change is the
 * documented duplicate-id decision: `id="home"` lives here, on the hero, and
 * the page wrapper in header.php no longer claims it.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;
use ErfanSanat\Theme\Navigation\MenuTree;

$erfansanat_hero_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();

$erfansanat_hero_title    = (string) SectionParts::field( $erfansanat_hero_fields, 'title', 'وقتی کیفیت و ماندگاری مهمه' );
$erfansanat_hero_subtitle = (string) SectionParts::field( $erfansanat_hero_fields, 'subtitle', '' );
$erfansanat_hero_eyebrow  = (string) SectionParts::field( $erfansanat_hero_fields, 'eyebrow', '' );
$erfansanat_hero_media    = (int) SectionParts::field( $erfansanat_hero_fields, 'media_id', 0 );
$erfansanat_hero_scenes   = (array) SectionParts::field( $erfansanat_hero_fields, 'scenes', array() );
$erfansanat_hero_cta      = (array) SectionParts::field( $erfansanat_hero_fields, 'cta', array() );
$erfansanat_hero_video    = SectionParts::hero_video();

// Hero backdrop: the chosen media wins, then the first scene image, then the
// reference poster so the section is never an empty black box.
$erfansanat_hero_poster = '';
if ( $erfansanat_hero_media > 0 ) {
	$erfansanat_hero_poster = (string) wp_get_attachment_image_url( $erfansanat_hero_media, 'erfansanat-hero' );
}
if ( '' === $erfansanat_hero_poster ) {
	$erfansanat_hero_poster = SectionParts::asset( 'images/hero.jpg' );
}

$erfansanat_hero_scene_count = count( $erfansanat_hero_scenes );
?>
<section id="home" aria-label="<?php esc_attr_e( 'بخش آغازین - هیرو صفحه اصلی', 'erfansanat' ); ?>" class="relative bg-[#f6f8f7] pt-28 pb-10 sm:pt-32 sm:pb-12 lg:pt-36 lg:pb-14">
	<div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
		<div data-home1-hero-frame="true" class="relative rounded-[2.25rem] bg-slate-950 sm:rounded-[2.75rem]">
			<div class="relative h-[470px] w-full rounded-[2.25rem] sm:h-[540px] sm:rounded-[2.75rem] lg:h-[590px]">

				<div class="pointer-events-none absolute inset-0 overflow-hidden rounded-[2.25rem] sm:rounded-[2.75rem]">
					<?php
					foreach ( $erfansanat_hero_scenes as $erfansanat_index => $erfansanat_scene ) :
						$erfansanat_scene = is_array( $erfansanat_scene ) ? $erfansanat_scene : array();
						$erfansanat_image = SectionParts::asset( (string) ( $erfansanat_scene['image'] ?? '' ) );
						if ( '' === $erfansanat_image ) {
							continue;
						}
						$erfansanat_hidden = 0 === (int) $erfansanat_index ? 'opacity-100' : 'opacity-0';
						?>
						<?php
						// The first scene is the largest thing on the page and the
						// one the visitor is looking at, so it loads eagerly and at
						// high priority; every later scene is off-screen until the
						// rotation reaches it, so it waits.
						$erfansanat_first = 0 === (int) $erfansanat_index;
						?>
						<img src="<?php echo esc_url( $erfansanat_image ); ?>"
							alt="<?php echo esc_attr( (string) ( $erfansanat_scene['title'] ?? '' ) ); ?>"
							data-home1-scene-img="<?php echo esc_attr( (string) $erfansanat_index ); ?>"
							<?php echo $erfansanat_first ? 'fetchpriority="high" decoding="sync"' : 'loading="lazy" decoding="async"'; ?>
							class="pointer-events-none absolute inset-0 h-full w-full select-none object-cover <?php echo esc_attr( $erfansanat_hidden ); ?>" />
					<?php endforeach; ?>

					<?php if ( '' !== $erfansanat_hero_video ) : ?>
						<video src="<?php echo esc_url( $erfansanat_hero_video ); ?>"
							poster="<?php echo esc_url( $erfansanat_hero_poster ); ?>"
							autoplay muted loop playsinline preload="metadata"
							data-home1-inline-video="true"
							class="absolute inset-0 h-full w-full object-cover opacity-0"></video>
					<?php endif; ?>
				</div>

				<div data-home1-atmosphere="true" class="pointer-events-none absolute inset-0 overflow-hidden rounded-[2.25rem] transition-opacity duration-500 sm:rounded-[2.75rem] opacity-100">
					<div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-slate-950/45"></div>
					<div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_52%,rgba(16,185,129,0.22),transparent_62%)]"></div>
				</div>

				<div data-home1-video-controls="true" class="absolute top-5 left-5 z-30 flex items-center gap-2 transition-all duration-500 pointer-events-none -translate-y-3 opacity-0">
					<button type="button" data-home1-video-close="true" class="inline-flex items-center gap-2 rounded-2xl border border-white/30 bg-slate-950/75 px-4 py-2.5 text-xs font-black text-white shadow-lg backdrop-blur-md transition-colors hover:bg-slate-900">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><rect x="14" y="3" width="5" height="18" rx="1"></rect><rect x="5" y="3" width="5" height="18" rx="1"></rect></svg>
						<span><?php esc_html_e( 'توقف ویدیو و بازگشت به نمای طرح', 'erfansanat' ); ?></span>
					</button>
					<button type="button" data-home1-video-mute="true" aria-label="<?php esc_attr_e( 'قطع یا وصل صدای ویدیو', 'erfansanat' ); ?>" class="grid h-10 w-10 place-items-center rounded-2xl border border-white/30 bg-slate-950/75 text-white shadow-lg backdrop-blur-md transition-colors hover:bg-slate-900">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"></path><path d="M16 9a5 5 0 0 1 0 6"></path><path d="M19.364 18.364a9 9 0 0 0 0-12.728"></path></svg>
					</button>
				</div>

				<?php // Top-right notch carrying the reference h1. ?>
				<div data-home1-overlay-item="true" class="absolute -top-px -right-px z-20 transition-all duration-500 translate-x-0 translate-y-0 opacity-100">
					<div class="relative w-[240px] max-w-[74vw] rounded-bl-[1.75rem] bg-[#f6f8f7] px-3.5 pt-2.5 pb-3.5 sm:w-[295px] sm:rounded-bl-[2.15rem] sm:px-5 sm:pt-3 sm:pb-4 lg:w-[335px] lg:rounded-bl-[2.5rem]">
						<?php if ( '' !== $erfansanat_hero_eyebrow ) : ?>
							<div class="mb-1 flex items-center gap-1.5 text-[10px] font-extrabold text-emerald-700 sm:text-[11px]">
								<span class="inline-block h-3 w-1 shrink-0 rounded-full bg-emerald-600"></span>
								<span class="truncate"><?php echo esc_html( $erfansanat_hero_eyebrow ); ?></span>
							</div>
						<?php endif; ?>
						<h1 class="text-right text-[13.5px] leading-snug font-black tracking-tight text-slate-900 sm:text-[17px] lg:text-[19.5px]"><?php echo esc_html( $erfansanat_hero_title ); ?></h1>
						<?php if ( '' !== $erfansanat_hero_subtitle ) : ?>
							<div class="mt-1.5 flex items-center justify-start sm:mt-2">
								<div class="relative inline-flex w-full items-center justify-between gap-2 overflow-hidden rounded-xl border border-emerald-500/25 bg-white px-3 py-1.5 text-right shadow-2xs sm:px-3.5 sm:py-2">
									<span aria-hidden="true" class="absolute inset-y-1.5 right-1.5 w-1 rounded-full bg-emerald-600"></span>
									<span class="pr-2 text-[12.5px] font-black tracking-tight text-emerald-950 sm:text-[15.5px] lg:text-[17.5px]"><?php echo esc_html( $erfansanat_hero_subtitle ); ?></span>
									<span class="hidden rounded-lg bg-emerald-700 px-2 py-0.5 font-mono text-[9px] font-black tracking-wider text-white sm:inline-block"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_mark', 'ESE' ) ); ?></span>
								</div>
							</div>
						<?php endif; ?>
						<svg viewBox="0 0 36 36" aria-hidden="true" class="pointer-events-none absolute -left-[35px] top-0 h-9 w-9 text-[#f6f8f7]"><path d="M36 0H0C19.882 0 36 16.118 36 36V0Z" fill="currentColor"></path></svg>
						<svg viewBox="0 0 36 36" aria-hidden="true" class="pointer-events-none absolute -bottom-[35px] right-0 h-9 w-9 text-[#f6f8f7]"><path d="M36 0V36C36 16.118 19.882 0 0 0H36Z" fill="currentColor"></path></svg>
					</div>
				</div>

				<div data-home1-overlay-item="true" class="absolute left-4 top-28 z-20 hidden max-w-[540px] flex-col gap-2 transition-all duration-500 md:top-6 md:left-6 md:flex lg:left-7 lg:top-7 translate-y-0 opacity-100">
					<?php
					// The reference chips are real taxonomy links; they are built from
					// the live categories rather than the mirror's file names.
					$erfansanat_chip_terms = MenuTree::product_tabs( 2 );
					$erfansanat_chip_links = array();
					foreach ( $erfansanat_chip_terms as $erfansanat_tab ) {
						if ( '' !== (string) $erfansanat_tab['url'] ) {
							$erfansanat_chip_links[ (string) $erfansanat_tab['label'] ] = (string) $erfansanat_tab['url'];
						}
					}
					$erfansanat_chip_chunks = array_chunk( $erfansanat_chip_links, 4, true );
					foreach ( $erfansanat_chip_chunks as $erfansanat_chunk ) :
						?>
						<div class="flex flex-wrap items-center justify-end gap-1.5">
							<?php foreach ( $erfansanat_chunk as $erfansanat_label => $erfansanat_url ) : ?>
								<a href="<?php echo esc_url( $erfansanat_url ); ?>" class="rounded-xl border border-white/30 bg-slate-950/35 px-3.5 py-1.5 text-[11.5px] font-bold text-white backdrop-blur-md transition-all duration-200 hover:border-white/60 hover:bg-slate-950/55"><?php echo esc_html( $erfansanat_label ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>

				<div data-home1-overlay-item="true" class="absolute inset-0 z-10 flex items-center justify-center transition-all duration-500 scale-100 opacity-100">
					<div class="relative flex items-center justify-center">
						<?php if ( '' !== $erfansanat_hero_video ) : ?>
							<button type="button" data-home1-play-trigger="true" aria-label="<?php esc_attr_e( 'پخش کامل تیزر ویدیویی شرکت در همین بخش', 'erfansanat' ); ?>" class="group relative z-10 flex h-24 w-24 items-center justify-center rounded-full border border-white/40 bg-slate-950/35 backdrop-blur-md transition-transform duration-300 hover:scale-105">
								<svg viewBox="0 0 100 100" class="pointer-events-none absolute inset-0 m-auto h-[calc(100%-12px)] w-[calc(100%-12px)] animate-[spin_36s_linear_infinite]" aria-hidden="true">
									<defs>
										<path id="erfansanatCircleTextPath" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0"></path>
									</defs>
									<text class="fill-white/95 font-mono text-[8.2px] font-bold tracking-[2.1px] uppercase">
										<textPath href="#erfansanatCircleTextPath" startOffset="0%">ERFAN SANAT ESFAHAN • PLAY VIDEO •</textPath>
									</text>
								</svg>
								<span class="grid h-11 w-11 place-items-center rounded-full border border-white/45 bg-emerald-500 text-slate-950 shadow-[0_0_25px_rgba(16,185,129,0.75)] transition-transform duration-300 group-hover:scale-110">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"></path></svg>
								</span>
							</button>
						<?php endif; ?>
					</div>
				</div>

				<?php
				$erfansanat_first_scene = is_array( $erfansanat_hero_scenes[0] ?? null ) ? $erfansanat_hero_scenes[0] : array();
				?>
				<div data-home1-overlay-item="true" class="absolute right-5 bottom-16 z-20 hidden items-center gap-2 transition-all duration-500 lg:flex translate-y-0 opacity-100">
					<a href="<?php echo esc_url( (string) ( $erfansanat_first_scene['url'] ?? home_url( '/' ) ) ); ?>" data-home1-scene-link="true" class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-slate-950/65 px-3.5 py-2 text-xs font-extrabold text-white backdrop-blur-md transition-colors hover:bg-slate-950/85">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
						<span data-home1-scene-title="true"><?php echo esc_html( (string) ( $erfansanat_first_scene['title'] ?? '' ) ); ?></span>
						<span class="text-white/40">·</span>
						<span data-home1-scene-cta="true" class="text-emerald-300"><?php echo esc_html( (string) ( $erfansanat_first_scene['cta'] ?? '' ) ); ?></span>
					</a>
				</div>

				<div data-home1-overlay-item="true" class="absolute inset-x-2 bottom-12 z-20 flex items-center justify-start sm:justify-center gap-1 overflow-x-auto no-scrollbar rounded-2xl border border-white/15 bg-slate-950/45 p-1 backdrop-blur-md">
					<?php foreach ( $erfansanat_hero_scenes as $erfansanat_index => $erfansanat_scene ) : ?>
						<?php $erfansanat_scene = is_array( $erfansanat_scene ) ? $erfansanat_scene : array(); ?>
						<button type="button"
							data-home1-scene-btn="<?php echo esc_attr( (string) $erfansanat_index ); ?>"
							data-scene-title="<?php echo esc_attr( (string) ( $erfansanat_scene['title'] ?? '' ) ); ?>"
							data-scene-href="<?php echo esc_url( (string) ( $erfansanat_scene['url'] ?? '' ) ); ?>"
							data-scene-cta="<?php echo esc_attr( (string) ( $erfansanat_scene['cta'] ?? '' ) ); ?>"
							class="shrink-0 rounded-xl px-3 py-1.5 text-[11px] font-extrabold whitespace-nowrap transition-colors <?php echo 0 === (int) $erfansanat_index ? 'bg-white text-emerald-900' : 'text-white/80 hover:bg-white/10 hover:text-white'; ?>">
							<?php echo esc_html( (string) ( $erfansanat_scene['tab'] ?? '' ) ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="absolute inset-x-0 -bottom-px z-30 flex justify-center">
					<div data-home1-bottom-tab="true" class="relative rounded-t-[1.85rem] bg-[#f6f8f7] px-8 pt-3.5 pb-1.5 text-center transition-all duration-700">
						<svg viewBox="0 0 32 32" aria-hidden="true" data-home1-bottom-fillet="true" class="pointer-events-none absolute -left-[31px] bottom-0 h-8 w-8 text-[#f6f8f7]"><path d="M32 32H0C17.673 32 32 17.673 32 0V32Z" fill="currentColor"></path></svg>
						<svg viewBox="0 0 32 32" aria-hidden="true" data-home1-bottom-fillet="true" class="pointer-events-none absolute -right-[31px] bottom-0 h-8 w-8 text-[#f6f8f7]"><path d="M0 32H32C14.327 32 0 17.673 0 0V32Z" fill="currentColor"></path></svg>
						<div class="relative inline-block px-3">
							<span aria-hidden="true" class="absolute inset-x-1 bottom-1 h-3 rounded-md bg-emerald-200/75"></span>
							<h2 class="relative text-[15px] font-black text-slate-900 sm:text-lg lg:text-[20px]"><?php esc_html_e( 'پربازدیدترین دسته‌بندی‌ها', 'erfansanat' ); ?></h2>
						</div>
					</div>
				</div>
			</div>

			<div id="home1-categories" data-home1-categories-grid="true" class="relative z-20 -mt-px grid grid-cols-2 gap-3 bg-[#f6f8f7] pt-3 transition-all duration-700 sm:grid-cols-3 lg:grid-cols-9">
				<?php
				$erfansanat_cats = MenuTree::product_tabs( 1 );
				foreach ( $erfansanat_cats as $erfansanat_cat ) :
					$erfansanat_cat_icon = 'lamp';
					?>
					<a href="<?php echo esc_url( (string) $erfansanat_cat['url'] ); ?>" class="card-light group relative flex flex-col items-center justify-between rounded-2xl px-3 py-5 text-center">
						<div class="grid h-13 w-13 place-items-center rounded-2xl border border-emerald-100 bg-emerald-50/60 text-emerald-700 transition-colors duration-300 group-hover:bg-emerald-600 group-hover:text-white">
							<?php SectionParts::icon( $erfansanat_cat_icon, 'h-6 w-6' ); ?>
						</div>
						<div class="mt-3.5">
							<div class="text-[12.5px] font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( (string) $erfansanat_cat['label'] ); ?></div>
						</div>
						<div class="mt-3 inline-flex items-center gap-1 border-t border-slate-100 pt-2 text-[10.5px] font-bold text-emerald-700 tabular-nums">
							<span><?php echo esc_html( (string) count( (array) $erfansanat_cat['items'] ) ); ?> <?php esc_html_e( 'مدل', 'erfansanat' ); ?></span>
							<?php SectionParts::arrow( 'h-3 w-3' ); ?>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
