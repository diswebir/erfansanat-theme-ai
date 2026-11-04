<?php
/**
 * Product gallery: main image, thumbnails, counter and the lightbox.
 *
 * The markup and every `data-gallery-*` hook match the static reference, so the
 * transferred `initGalleries()` drives it unchanged. The only difference is
 * where the sources come from: real attachment ids from WooCommerce and the
 * legacy gallery keys, resolved to sized URLs here.
 *
 * @package ErfanSanat
 *
 * @var array<int, int> $erfansanat_gallery_ids Attachment ids, first = main.
 * @var string          $erfansanat_title       Product name, for alt text.
 * @var string          $erfansanat_badge       Corner chip (category name).
 * @var string          $erfansanat_ip          Protective rating chip, may be empty.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_gallery_ids = isset( $erfansanat_gallery_ids ) && is_array( $erfansanat_gallery_ids ) ? array_values( array_filter( array_map( 'absint', $erfansanat_gallery_ids ) ) ) : array();
$erfansanat_title       = isset( $erfansanat_title ) ? (string) $erfansanat_title : '';
$erfansanat_badge       = isset( $erfansanat_badge ) ? (string) $erfansanat_badge : '';
$erfansanat_ip          = isset( $erfansanat_ip ) ? (string) $erfansanat_ip : '';

// A product without a single image still renders the frame, so the summary
// column never collapses against a bare background.
$erfansanat_main     = 0 !== count( $erfansanat_gallery_ids ) ? (int) $erfansanat_gallery_ids[0] : 0;
$erfansanat_main_src = $erfansanat_main > 0 ? (string) wp_get_attachment_image_url( $erfansanat_main, 'large' ) : '';
$erfansanat_thumbs   = count( $erfansanat_gallery_ids ) > 1 ? array_slice( $erfansanat_gallery_ids, 0, 6 ) : array();
$erfansanat_counter  = max( 1, count( $erfansanat_gallery_ids ) );
?>
<div class="space-y-3.5" data-gallery-root="true">
	<div class="group relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-900 shadow-sm">
		<?php if ( '' !== $erfansanat_main_src ) : ?>
			<img src="<?php echo esc_url( $erfansanat_main_src ); ?>"
				alt="<?php echo esc_attr( $erfansanat_title ); ?>"
				data-gallery-main-img="true"
				class="h-[16rem] xs:h-[19rem] sm:h-[24rem] lg:h-[27rem] w-full cursor-zoom-in object-cover transition-transform duration-700" />
		<?php else : ?>
			<div class="grid h-[16rem] xs:h-[19rem] sm:h-[24rem] lg:h-[27rem] w-full place-items-center bg-slate-100 text-xs font-bold text-slate-400">
				<?php esc_html_e( 'تصویری برای این محصول ثبت نشده است', 'erfansanat' ); ?>
			</div>
		<?php endif; ?>
		<div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/55 via-transparent to-transparent"></div>

		<?php if ( '' !== $erfansanat_badge ) : ?>
			<span class="absolute right-3.5 top-3.5 sm:right-4 sm:top-4 rounded-xl border border-white/25 bg-emerald-950/75 px-3 py-1 sm:px-3.5 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-extrabold text-emerald-100 backdrop-blur-md"><?php echo esc_html( $erfansanat_badge ); ?></span>
		<?php endif; ?>

		<?php if ( '' !== $erfansanat_ip ) : ?>
			<span dir="ltr" class="absolute bottom-3 left-3.5 rounded-lg bg-white/95 px-2 py-0.5 font-mono text-[10.5px] font-black text-emerald-900 shadow-xs sm:bottom-4 sm:left-4"><?php echo esc_html( $erfansanat_ip ); ?></span>
		<?php endif; ?>

		<div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 flex items-center justify-between">
			<div class="flex items-center gap-1.5 sm:gap-2">
				<?php if ( $erfansanat_counter > 1 ) : ?>
					<button type="button" data-gallery-prev="true" aria-label="<?php esc_attr_e( 'تصویر قبلی', 'erfansanat' ); ?>" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-xl border border-white/25 bg-slate-900/75 text-white backdrop-blur-md transition-colors hover:bg-emerald-700 cursor-pointer">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 sm:h-4.5 sm:w-4.5" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
					</button>
					<button type="button" data-gallery-next="true" aria-label="<?php esc_attr_e( 'تصویر بعدی', 'erfansanat' ); ?>" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-xl border border-white/25 bg-slate-900/75 text-white backdrop-blur-md transition-colors hover:bg-emerald-700 cursor-pointer">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 sm:h-4.5 sm:w-4.5" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
					</button>
				<?php endif; ?>
				<span data-gallery-counter="true" class="rounded-xl border border-white/20 bg-slate-900/75 px-2.5 py-1 sm:px-3 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-bold text-white backdrop-blur-md"><?php echo esc_html( \ErfanSanat\Theme\Frontend\SectionParts::fa_digits( '1' ) . ' / ' . \ErfanSanat\Theme\Frontend\SectionParts::fa_digits( (string) $erfansanat_counter ) ); ?></span>
			</div>
			<?php if ( '' !== $erfansanat_main_src ) : ?>
				<button type="button" data-gallery-expand="true" class="inline-flex items-center gap-1 rounded-xl border border-white/25 bg-white/90 px-3 py-1 sm:px-3.5 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-extrabold text-slate-900 shadow-sm backdrop-blur-md transition-colors hover:bg-emerald-700 hover:text-white cursor-pointer">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="m15 15 6 6"></path><path d="m15 9 6-6"></path><path d="M21 16v5h-5"></path><path d="M21 8V3h-5"></path><path d="M3 16v5h5"></path><path d="m3 21 6-6"></path><path d="M3 8V3h5"></path><path d="M9 9 3 3"></path></svg>
					<span><?php esc_html_e( 'بزرگ‌نمایی', 'erfansanat' ); ?></span>
				</button>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( array() !== $erfansanat_thumbs ) : ?>
		<div class="grid grid-cols-4 gap-1.5 sm:grid-cols-5 sm:gap-2.5">
			<?php
			foreach ( $erfansanat_thumbs as $erfansanat_index => $erfansanat_thumb_id ) :
				$erfansanat_thumb_src = (string) wp_get_attachment_image_url( (int) $erfansanat_thumb_id, 'thumbnail' );
				$erfansanat_full_src  = (string) wp_get_attachment_image_url( (int) $erfansanat_thumb_id, 'large' );
				if ( '' === $erfansanat_thumb_src || '' === $erfansanat_full_src ) {
					continue;
				}
				$erfansanat_is_first = 0 === (int) $erfansanat_index;
				?>
				<button type="button"
					data-gallery-thumb="<?php echo (int) $erfansanat_index; ?>"
					data-gallery-src="<?php echo esc_url( $erfansanat_full_src ); ?>"
					class="relative h-16 sm:h-20 overflow-hidden rounded-xl sm:rounded-2xl border-2 transition-all cursor-pointer <?php echo $erfansanat_is_first ? 'border-emerald-500 shadow-[0_4px_16px_-4px_rgba(5,150,105,.45)]' : 'border-transparent opacity-65 hover:opacity-100'; ?>">
					<img src="<?php echo esc_url( $erfansanat_thumb_src ); ?>"
						alt="<?php echo esc_attr( sprintf( /* translators: 1: product name, 2: image number. */ __( '%1$s - تصویر %2$s', 'erfansanat' ), $erfansanat_title, \ErfanSanat\Theme\Frontend\SectionParts::fa_digits( (string) ( (int) $erfansanat_index + 1 ) ) ) ); ?>"
						loading="lazy" class="h-full w-full object-cover" />
					<span data-gallery-thumb-ring="true" class="pointer-events-none absolute inset-0 rounded-lg sm:rounded-xl ring-2 ring-inset ring-emerald-500 <?php echo $erfansanat_is_first ? 'block' : 'hidden'; ?>"></span>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( '' !== $erfansanat_main_src ) : ?>
		<div data-gallery-lightbox="true" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/90 p-4 backdrop-blur-md">
			<button type="button" data-gallery-lightbox-close="true" aria-label="<?php esc_attr_e( 'بستن گالری', 'erfansanat' ); ?>" class="absolute right-5 top-5 grid h-11 w-11 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-white/25">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
			</button>
			<?php if ( $erfansanat_counter > 1 ) : ?>
				<button type="button" data-gallery-lightbox-prev="true" aria-label="<?php esc_attr_e( 'قبلی', 'erfansanat' ); ?>" class="absolute right-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-emerald-600">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
				</button>
				<button type="button" data-gallery-lightbox-next="true" aria-label="<?php esc_attr_e( 'بعدی', 'erfansanat' ); ?>" class="absolute left-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-emerald-600">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
				</button>
			<?php endif; ?>
			<img src="<?php echo esc_url( $erfansanat_main_src ); ?>" alt="<?php echo esc_attr( $erfansanat_title ); ?>" data-gallery-lightbox-img="true" class="max-h-[84vh] max-w-[90vw] rounded-2xl object-contain shadow-2xl" />
			<?php if ( count( $erfansanat_gallery_ids ) > 1 ) : ?>
				<div class="absolute bottom-6 left-1/2 flex -translate-x-1/2 items-center gap-2">
					<?php foreach ( $erfansanat_gallery_ids as $erfansanat_dot_index => $erfansanat_dot_id ) : ?>
						<button type="button"
							data-gallery-lightbox-dot="<?php echo (int) $erfansanat_dot_index; ?>"
							aria-label="<?php echo esc_attr( sprintf( /* translators: %s: image number. */ __( 'تصویر %s', 'erfansanat' ), \ErfanSanat\Theme\Frontend\SectionParts::fa_digits( (string) ( (int) $erfansanat_dot_index + 1 ) ) ) ); ?>"
							class="h-2 rounded-full transition-all <?php echo 0 === (int) $erfansanat_dot_index ? 'w-7 bg-emerald-400' : 'w-2 bg-white/40 hover:bg-white/70'; ?>"></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
