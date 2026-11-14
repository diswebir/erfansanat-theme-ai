<?php
/**
 * Product gallery (P06-T003). DOM parity with static-mirror including the
 * lightbox. Images come from the featured image + the legacy gallery with
 * fallback order galerry_produ → _product_image_gallery → gallery-filed →
 * image-2..6 (AC-02, via ProductMetaboxes::get_gallery). Order is preserved
 * (AC-01); deleted/invalid attachments are skipped (AC-03). All initGalleries
 * hooks are emitted so main.js drives thumbnails/prev/next/lightbox (SC-02).
 *
 * Hooked into woocommerce_before_single_product_summary (replaces the T002
 * shell fallback). Receives the global product.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Admin\ProductMetaboxes;

$es_product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : false;
if ( ! $es_product ) {
	return;
}

$es_id    = $es_product->get_id();
$es_title = get_the_title( $es_id );

// Featured image first, then the legacy gallery (deduplicated, in order).
$es_ids   = array();
$es_thumb = (int) get_post_thumbnail_id( $es_id );
if ( $es_thumb > 0 ) {
	$es_ids[] = $es_thumb;
}
foreach ( ProductMetaboxes::get_gallery( $es_id ) as $es_gid ) {
	$es_gid = (int) $es_gid;
	if ( $es_gid > 0 && ! in_array( $es_gid, $es_ids, true ) ) {
		$es_ids[] = $es_gid;
	}
}

// AC-03: keep only attachments that still resolve to a URL (deleted -> skip).
$es_images = array();
foreach ( $es_ids as $es_aid ) {
	$es_url = wp_get_attachment_image_url( $es_aid, 'large' );
	if ( $es_url ) {
		$es_images[] = $es_url;
	}
}
if ( array() === $es_images ) {
	$es_images[] = ES_ASSETS_URI . 'images/hero.jpg';
}
$es_count = count( $es_images );
$es_first = $es_images[0];

$es_cats    = get_the_terms( $es_id, 'product_cat' );
$es_badge   = ( $es_cats && ! is_wp_error( $es_cats ) ) ? $es_cats[0]->name : '';
$es_fa      = static function ( $v ) { return strtr( (string) $v, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) ); };
$es_counter = $es_fa( 1 ) . ' / ' . $es_fa( $es_count );

$es_chev_r = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-4 w-4 sm:h-4.5 sm:w-4.5" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>';
$es_chev_l = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-4 w-4 sm:h-4.5 sm:w-4.5" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>';
$es_expand = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-expand h-3.5 w-3.5" aria-hidden="true"><path d="m15 15 6 6"></path><path d="m15 9 6-6"></path><path d="M21 16v5h-5"></path><path d="M21 8V3h-5"></path><path d="M3 16v5h5"></path><path d="m3 21 6-6"></path><path d="M3 8V3h5"></path><path d="M9 9 3 3"></path></svg>';
$es_x      = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-5 w-5" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>';
?>
<div data-gallery-root="true" class="space-y-3.5">
	<div class="group relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-900 shadow-sm">
		<img src="<?php echo esc_url( $es_first ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" data-gallery-main-img="true" class="h-[16rem] xs:h-[19rem] sm:h-[24rem] lg:h-[27rem] w-full cursor-zoom-in object-cover transition-transform duration-700"/>
		<div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/55 via-transparent to-transparent"></div>
		<?php if ( '' !== $es_badge ) : ?>
			<span class="absolute right-3.5 top-3.5 sm:right-4 sm:top-4 rounded-xl border border-white/25 bg-emerald-950/75 px-3 py-1 sm:px-3.5 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-extrabold text-emerald-100 backdrop-blur-md"><?php echo esc_html( $es_badge ); ?></span>
		<?php endif; ?>
		<div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 flex items-center justify-between">
			<div class="flex items-center gap-1.5 sm:gap-2">
				<button type="button" data-gallery-prev="true" aria-label="تصویر قبلی" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-xl border border-white/25 bg-slate-900/75 text-white backdrop-blur-md transition-colors hover:bg-emerald-700 cursor-pointer"><?php echo $es_chev_r; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<button type="button" data-gallery-next="true" aria-label="تصویر بعدی" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-xl border border-white/25 bg-slate-900/75 text-white backdrop-blur-md transition-colors hover:bg-emerald-700 cursor-pointer"><?php echo $es_chev_l; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<span data-gallery-counter="true" class="rounded-xl border border-white/20 bg-slate-900/75 px-2.5 py-1 sm:px-3 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-bold text-white backdrop-blur-md"><?php echo esc_html( $es_counter ); ?></span>
			</div>
			<button type="button" data-gallery-expand="true" class="inline-flex items-center gap-1 rounded-xl border border-white/25 bg-white/90 px-3 py-1 sm:px-3.5 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-extrabold text-slate-900 shadow-sm backdrop-blur-md transition-colors hover:bg-emerald-700 hover:text-white cursor-pointer"><?php echo $es_expand; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>بزرگ‌نمایی</span></button>
		</div>
	</div>
	<div class="grid grid-cols-4 gap-1.5 sm:grid-cols-5 sm:gap-2.5">
		<?php foreach ( $es_images as $es_i => $es_url ) :
			$es_active = ( 0 === $es_i );
			$es_tcls   = 'relative h-16 sm:h-20 overflow-hidden rounded-xl sm:rounded-2xl border-2 transition-all cursor-pointer ' . ( $es_active ? 'border-emerald-500 shadow-[0_4px_16px_-4px_rgba(5,150,105,.45)]' : 'border-transparent opacity-65 hover:opacity-100' );
			?>
			<button type="button" data-gallery-thumb="<?php echo esc_attr( (string) $es_i ); ?>" data-gallery-src="<?php echo esc_url( $es_url ); ?>" class="<?php echo esc_attr( $es_tcls ); ?>">
				<img src="<?php echo esc_url( $es_url ); ?>" alt="<?php echo esc_attr( $es_title . ' - تصویر ' . $es_fa( $es_i + 1 ) ); ?>" loading="lazy" class="h-full w-full object-cover"/>
				<span data-gallery-thumb-ring="true" class="pointer-events-none absolute inset-0 rounded-lg sm:rounded-xl ring-2 ring-inset ring-emerald-500 <?php echo $es_active ? 'block' : 'hidden'; ?>"></span>
			</button>
		<?php endforeach; ?>
	</div>
	<div data-gallery-lightbox="true" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/90 p-4 backdrop-blur-md">
		<button type="button" data-gallery-lightbox-close="true" aria-label="بستن گالری" class="absolute right-5 top-5 grid h-11 w-11 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-white/25"><?php echo $es_x; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		<button type="button" data-gallery-lightbox-prev="true" aria-label="قبلی" class="absolute right-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-emerald-600"><?php echo $es_chev_r; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		<button type="button" data-gallery-lightbox-next="true" aria-label="بعدی" class="absolute left-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-emerald-600"><?php echo $es_chev_l; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		<img src="<?php echo esc_url( $es_first ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" data-gallery-lightbox-img="true" class="max-h-[84vh] max-w-[90vw] rounded-2xl object-contain shadow-2xl"/>
		<div class="absolute bottom-6 left-1/2 flex -translate-x-1/2 items-center gap-2">
			<?php foreach ( $es_images as $es_i => $es_url ) :
				$es_active = ( 0 === $es_i );
				$es_dcls   = 'h-2 rounded-full transition-all ' . ( $es_active ? 'w-7 bg-emerald-400' : 'w-2 bg-white/40 hover:bg-white/70' );
				?>
				<button type="button" data-gallery-lightbox-dot="<?php echo esc_attr( (string) $es_i ); ?>" aria-label="<?php echo esc_attr( 'تصویر ' . ( $es_i + 1 ) ); ?>" class="<?php echo esc_attr( $es_dcls ); ?>"></button>
			<?php endforeach; ?>
		</div>
	</div>
</div>
