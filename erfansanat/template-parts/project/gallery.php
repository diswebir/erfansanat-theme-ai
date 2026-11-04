<?php
/**
 * Single project gallery + lightbox (P07-T002). Renders the featured image plus
 * the five legacy gallery images (second-image … six-image, including the exact
 * `foure-image` key) in stored order (AC-01/AC-02). Empty or deleted attachments
 * are skipped (AC-03). The data-gallery-* contract mirrors the reference and is
 * driven by the immutable main.js (SC-02 lightbox).
 *
 * @package ErfanSanat
 *
 * @var array $args { project_id: int }
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Admin\ProjectMetaboxes;

$es_pid = isset( $args['project_id'] ) ? (int) $args['project_id'] : (int) get_the_ID();
if ( ! $es_pid || 'projects' !== get_post_type( $es_pid ) ) {
	return;
}

$es_title = get_the_title( $es_pid );

// Featured image first, then the legacy gallery in stored order.
$es_ids = array();
$es_featured = (int) get_post_thumbnail_id( $es_pid );
if ( $es_featured > 0 ) {
	$es_ids[] = $es_featured;
}
foreach ( ProjectMetaboxes::get_gallery( $es_pid ) as $es_gid ) {
	$es_gid = (int) $es_gid;
	if ( $es_gid > 0 && ! in_array( $es_gid, $es_ids, true ) ) {
		$es_ids[] = $es_gid;
	}
}

// Resolve to real, existing attachment URLs (AC-03: skip empty/deleted).
$es_images = array();
foreach ( $es_ids as $es_id ) {
	$es_attachment = get_post( $es_id );
	if ( ! $es_attachment || 'attachment' !== $es_attachment->post_type ) {
		continue;
	}
	$es_url = wp_get_attachment_image_url( $es_id, 'large' );
	if ( ! $es_url ) {
		$es_url = wp_get_attachment_url( $es_id );
	}
	if ( $es_url ) {
		$es_images[] = $es_url;
	}
}

if ( empty( $es_images ) ) {
	return; // Nothing to render.
}

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};
$es_count = count( $es_images );
$es_chevron_left  = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>';
$es_chevron_right = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>';
$es_expand_icon   = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6"/><path d="M9 21H3v-6"/><path d="M21 3l-7 7"/><path d="M3 21l7-7"/></svg>';
$es_close_icon    = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>';
?>

<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
	<div data-gallery-root="true" class="space-y-3.5">
		<div class="group relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-900 shadow-sm">
			<img src="<?php echo esc_url( $es_images[0] ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" data-gallery-main-img="true" class="h-[16rem] w-full cursor-zoom-in object-cover xs:h-[19rem] sm:h-[24rem] lg:h-[30rem]" />
			<div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/55 via-transparent to-transparent"></div>
			<span class="absolute right-3.5 top-3.5 rounded-xl border border-white/25 bg-emerald-950/75 px-3 py-1 text-[10.5px] font-extrabold text-white sm:right-4 sm:top-4 sm:px-3.5 sm:py-1.5 sm:text-[11.5px]"><?php echo esc_html( sprintf( 'گالری پروژه · %s تصویر', $es_fa( $es_count ) ) ); ?></span>
			<div class="absolute bottom-3 left-3 right-3 flex items-center justify-between sm:bottom-4 sm:left-4 sm:right-4">
				<div class="flex items-center gap-1.5 sm:gap-2">
					<button type="button" data-gallery-prev="true" aria-label="<?php esc_attr_e( 'تصویر قبلی', 'erfansanat' ); ?>" class="grid h-8 w-8 place-items-center rounded-xl border border-white/25 bg-white/90 text-slate-800 transition hover:bg-white sm:h-9 sm:w-9"><?php echo $es_chevron_left; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					<button type="button" data-gallery-next="true" aria-label="<?php esc_attr_e( 'تصویر بعدی', 'erfansanat' ); ?>" class="grid h-8 w-8 place-items-center rounded-xl border border-white/25 bg-white/90 text-slate-800 transition hover:bg-white sm:h-9 sm:w-9"><?php echo $es_chevron_right; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					<span data-gallery-counter="true" class="rounded-xl border border-white/20 bg-slate-900/75 px-2.5 py-1 text-[10.5px] font-bold text-white tabular-nums sm:px-3 sm:py-1.5 sm:text-[11.5px]"><?php echo esc_html( $es_fa( 1 ) . ' / ' . $es_fa( $es_count ) ); ?></span>
				</div>
				<button type="button" data-gallery-expand="true" class="inline-flex items-center gap-1 rounded-xl border border-white/25 bg-white/90 px-3 py-1 text-[10.5px] font-extrabold text-slate-800 transition hover:bg-white sm:px-3.5 sm:py-1.5 sm:text-[11.5px]"><?php echo $es_expand_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'بزرگ‌نمایی', 'erfansanat' ); ?></span></button>
			</div>
		</div>

		<div class="grid grid-cols-4 gap-1.5 sm:grid-cols-5 sm:gap-2.5">
			<?php foreach ( $es_images as $es_i => $es_url ) : ?>
				<button type="button" data-gallery-thumb="<?php echo (int) $es_i; ?>" data-gallery-src="<?php echo esc_url( $es_url ); ?>" class="relative h-16 overflow-hidden rounded-xl border-2 <?php echo 0 === $es_i ? 'border-emerald-500 shadow-[0_4px_16px_-4px_rgba(5,150,105,.45)]' : 'border-transparent opacity-65 hover:opacity-100'; ?> transition sm:h-20 sm:rounded-2xl">
					<img src="<?php echo esc_url( $es_url ); ?>" alt="<?php echo esc_attr( sprintf( '%s - تصویر %s', $es_title, $es_fa( $es_i + 1 ) ) ); ?>" loading="lazy" class="h-full w-full object-cover" />
					<span data-gallery-thumb-ring="true" class="pointer-events-none absolute inset-0 rounded-lg ring-2 ring-inset ring-emerald-500 <?php echo 0 === $es_i ? 'block' : 'hidden'; ?> sm:rounded-xl"></span>
				</button>
			<?php endforeach; ?>
		</div>

		<div data-gallery-lightbox="true" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/90 p-4 backdrop-blur-md">
			<button type="button" data-gallery-lightbox-close="true" aria-label="<?php esc_attr_e( 'بستن گالری', 'erfansanat' ); ?>" class="absolute right-5 top-5 grid h-11 w-11 place-items-center rounded-full border border-white/25 bg-white/10 text-white transition hover:bg-white/20"><?php echo $es_close_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<button type="button" data-gallery-lightbox-prev="true" aria-label="<?php esc_attr_e( 'قبلی', 'erfansanat' ); ?>" class="absolute right-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/25 bg-white/10 text-white transition hover:bg-white/20"><?php echo $es_chevron_left; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<img data-gallery-lightbox-img="true" src="<?php echo esc_url( $es_images[0] ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" class="max-h-[85vh] max-w-[90vw] rounded-2xl object-contain" />
			<button type="button" data-gallery-lightbox-next="true" aria-label="<?php esc_attr_e( 'بعدی', 'erfansanat' ); ?>" class="absolute left-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/25 bg-white/10 text-white transition hover:bg-white/20"><?php echo $es_chevron_right; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<div class="absolute bottom-6 left-1/2 flex -translate-x-1/2 items-center gap-1.5">
				<?php foreach ( $es_images as $es_i => $es_url ) : ?>
					<button type="button" data-gallery-lightbox-dot="<?php echo (int) $es_i; ?>" aria-label="<?php echo esc_attr( sprintf( 'تصویر %s', $es_fa( $es_i + 1 ) ) ); ?>" class="h-2 rounded-full transition-all <?php echo 0 === $es_i ? 'w-7 bg-emerald-400' : 'w-2 bg-white/40 hover:bg-white/70'; ?>"></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
