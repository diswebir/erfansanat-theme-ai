<?php
/**
 * Project gallery with thumbnails and the reference's lightbox hooks.
 *
 * `main.js` drives `data-gallery-*`: the arrows step through the images, the
 * thumbnails jump, and the expand button opens the lightbox. The markup keeps
 * every image in the DOM with a real `src`, so a visitor without JavaScript
 * still sees the hero image and a thumbnail strip.
 *
 * The title is printed inside an `<h1>` by the hero, so the caption here is a
 * `<p>`: a single page must not carry two `h1`s.
 *
 * @package ErfanSanat
 *
 * @var array<int, array{id: int, url: string, alt: string}> $erfansanat_gallery Images in display order.
 * @var string                                              $erfansanat_badge   Project type pill.
 * @var string                                              $erfansanat_title   Project title, for alt text.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_gallery = isset( $erfansanat_gallery ) && is_array( $erfansanat_gallery ) ? $erfansanat_gallery : array();
$erfansanat_badge   = isset( $erfansanat_badge ) ? (string) $erfansanat_badge : '';
$erfansanat_title   = isset( $erfansanat_title ) ? (string) $erfansanat_title : '';

if ( array() === $erfansanat_gallery ) {
	return;
}

$erfansanat_main  = $erfansanat_gallery[0];
$erfansanat_total = count( $erfansanat_gallery );
?>
<div data-gallery-root="true" class="space-y-3.5">
	<div class="group relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-900 shadow-sm">
		<img
			src="<?php echo esc_url( (string) $erfansanat_main['url'] ); ?>"
			alt="<?php echo esc_attr( '' !== $erfansanat_main['alt'] ? (string) $erfansanat_main['alt'] : $erfansanat_title ); ?>"
			data-gallery-main-img="true"
			class="h-[16rem] w-full cursor-zoom-in object-cover transition-transform duration-700 xs:h-[19rem] sm:h-[24rem] lg:h-[27rem]"
		/>
		<div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/55 via-transparent to-transparent"></div>
		<?php if ( '' !== $erfansanat_badge ) : ?>
			<span class="absolute right-3.5 top-3.5 rounded-xl border border-white/25 bg-emerald-950/75 px-3 py-1 text-[10.5px] font-extrabold text-emerald-100 backdrop-blur-md sm:right-4 sm:top-4 sm:px-3.5 sm:py-1.5 sm:text-[11.5px]"><?php echo esc_html( $erfansanat_badge ); ?></span>
		<?php endif; ?>
		<div class="absolute bottom-3 left-3 right-3 flex items-center justify-between sm:bottom-4 sm:left-4 sm:right-4">
			<div class="flex items-center gap-1.5 sm:gap-2">
				<button type="button" data-gallery-prev="true" aria-label="<?php esc_attr_e( 'تصویر قبلی', 'erfansanat' ); ?>" class="grid h-8 w-8 cursor-pointer place-items-center rounded-xl border border-white/25 bg-slate-900/75 text-white backdrop-blur-md transition-colors hover:bg-emerald-700 sm:h-9 sm:w-9">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
				</button>
				<button type="button" data-gallery-next="true" aria-label="<?php esc_attr_e( 'تصویر بعدی', 'erfansanat' ); ?>" class="grid h-8 w-8 cursor-pointer place-items-center rounded-xl border border-white/25 bg-slate-900/75 text-white backdrop-blur-md transition-colors hover:bg-emerald-700 sm:h-9 sm:w-9">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
				</button>
				<span data-gallery-counter="true" class="rounded-xl border border-white/20 bg-slate-900/75 px-2.5 py-1 text-[10.5px] font-bold text-white backdrop-blur-md sm:px-3 sm:py-1.5 sm:text-[11.5px]">
					<?php
					printf(
						/* translators: 1: current image, 2: total images. */
						esc_html__( '%1$s / %2$s', 'erfansanat' ),
						esc_html( SectionParts::fa_digits( '1' ) ),
						esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) )
					);
					?>
				</span>
			</div>
			<button type="button" data-gallery-expand="true" class="inline-flex cursor-pointer items-center gap-1 rounded-xl border border-white/25 bg-white/90 px-3 py-1 text-[10.5px] font-extrabold text-slate-900 shadow-sm backdrop-blur-md transition-colors hover:bg-emerald-700 hover:text-white sm:px-3.5 sm:py-1.5 sm:text-[11.5px]">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="m15 15 6 6"></path><path d="m15 9 6-6"></path><path d="M21 16v5h-5"></path><path d="M21 8V3h-5"></path><path d="M3 16v5h5"></path><path d="m3 21 6-6"></path><path d="M3 8V3h5"></path><path d="M9 9 3 3"></path></svg>
				<span><?php esc_html_e( 'بزرگ‌نمایی', 'erfansanat' ); ?></span>
			</button>
		</div>
	</div>

	<?php if ( $erfansanat_total > 1 ) : ?>
		<div class="grid grid-cols-4 gap-1.5 sm:grid-cols-5 sm:gap-2.5">
			<?php foreach ( $erfansanat_gallery as $erfansanat_index => $erfansanat_image ) : ?>
				<button
					type="button"
					data-gallery-thumb="<?php echo esc_attr( (string) $erfansanat_index ); ?>" data-gallery-thumb-item="true"
					data-gallery-src="<?php echo esc_url( (string) $erfansanat_image['url'] ); ?>"
					aria-label="<?php echo esc_attr( sprintf( /* translators: %s: image number. */ __( 'نمایش تصویر %s', 'erfansanat' ), SectionParts::fa_digits( (string) ( $erfansanat_index + 1 ) ) ) ); ?>"
					class="relative h-16 cursor-pointer overflow-hidden rounded-xl border-2 transition-all sm:h-20 sm:rounded-2xl <?php echo 0 === $erfansanat_index ? 'border-emerald-500 ring-2 ring-emerald-200' : 'border-slate-200 hover:border-emerald-400'; ?>"
					data-gallery-thumb-ring
				>
					<img src="<?php echo esc_url( (string) $erfansanat_image['url'] ); ?>" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover" />
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
