<?php
/**
 * Home hero section — main video layer (P05-T005).
 *
 * The main hero video is a global theme option (HomeTab), independent from
 * the per-page project slides (AC-01). Poster + preload are configurable
 * (AC-03). When no video is selected the hero falls back to an image so
 * removing the video never breaks the hero (AC-02 / SC-02). The full
 * multi-scene markup + JS is completed in P05-T006 / P05-T007.
 *
 * Receives $es_section (id/type/fields) and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\HomeTab;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'hero' );
$es_type = (string) ( $es_section['type'] ?? 'hero' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'نورپردازی شهری و المان‌های نوری' );
$es_subheading = (string) ( $es_fields['subheading'] ?? 'طراح و تولیدکننده تجهیزات نورپردازی پیشرفته' );

// Main video: per-page section override, else the global HomeTab option.
$es_video_id = (int) ( $es_fields['video_id'] ?? 0 );
if ( $es_video_id <= 0 ) {
	$es_video_id = HomeTab::hero_video_id();
}
$es_video_url = $es_video_id > 0 ? (string) wp_get_attachment_url( $es_video_id ) : '';

$es_poster   = HomeTab::hero_video_poster_url();
$es_preload  = HomeTab::hero_video_preload();
$es_fallback = HomeTab::hero_video_fallback_url();

// Project slides (P05-T006): per-page section override, else global HomeTab.
// Only published projects resolve; order is preserved (AC-01/02/03/04).
$es_slides_raw = isset( $es_fields['slides'] ) && is_array( $es_fields['slides'] ) ? $es_fields['slides'] : array();
if ( empty( $es_slides_raw ) ) {
	$es_slides_raw = HomeTab::hero_slides();
}
$es_slides = HomeTab::resolve_slides( $es_slides_raw );
?>
<section id="home" aria-label="بخش آغازین" class="es-section es-section--hero relative bg-[#f6f8f7] pt-28 pb-10 sm:pt-32 sm:pb-12 lg:pt-36 lg:pb-14" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<div class="relative overflow-hidden rounded-[2.25rem] sm:rounded-[2.75rem]">
			<div class="pointer-events-none absolute inset-0 overflow-hidden rounded-[2.25rem] sm:rounded-[2.75rem]">
				<?php if ( ! empty( $es_slides ) ) : ?>
					<?php foreach ( $es_slides as $es_i => $es_slide ) : ?>
						<img src="<?php echo esc_url( $es_slide['image'] ); ?>" alt="<?php echo esc_attr( $es_slide['title'] ); ?>" data-home1-scene-img="<?php echo (int) $es_i; ?>" class="pointer-events-none absolute inset-0 h-full w-full select-none object-cover object-center transition-all duration-700 ease-out <?php echo 0 === $es_i ? 'z-10 scale-105 opacity-100' : 'z-0 scale-100 opacity-0'; ?>"/>
					<?php endforeach; ?>
				<?php else : ?>
					<img src="<?php echo esc_url( $es_fallback ); ?>" alt="<?php echo esc_attr( $es_heading ); ?>" data-home1-scene-img="0" class="pointer-events-none absolute inset-0 h-full w-full select-none object-cover object-center transition-all duration-700 ease-out z-10 scale-105 opacity-100"/>
				<?php endif; ?>
				<?php if ( '' !== $es_video_url ) : ?>
					<video src="<?php echo esc_url( $es_video_url ); ?>" poster="<?php echo esc_url( $es_poster ); ?>" autoplay muted loop playsinline preload="<?php echo esc_attr( $es_preload ); ?>" data-home1-inline-video="true" class="absolute inset-0 h-full w-full object-cover object-center transition-opacity duration-500 ease-out z-10 opacity-100"></video>
				<?php endif; ?>
			</div>
			<div class="relative flex min-h-[26rem] flex-col justify-end gap-4 p-8 sm:p-12 lg:p-16">
				<h1 class="text-right text-[13.5px] leading-snug font-black tracking-tight text-slate-900 sm:text-[17px] lg:text-[19.5px]"><?php echo esc_html( $es_heading ); ?></h1>
				<?php if ( '' !== trim( $es_subheading ) ) : ?>
					<p class="text-right text-[13px] leading-7 text-slate-600 sm:text-[14px]"><?php echo esc_html( $es_subheading ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
