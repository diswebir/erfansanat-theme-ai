<?php
/** Product image gallery wired to the protected main.js data-gallery contract. */

defined( 'ABSPATH' ) || exit;

$product = \ErfanSanat\Theme\Woo\ProductTemplate::product( $args['product'] ?? null );
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $product );
if ( ! $product || ! $product_id ) { return; }
$ids = \ErfanSanat\Theme\Woo\ProductGallery::images( $product_id );
if ( ! $ids ) { return; }
$title = method_exists( $product, 'get_name' ) ? (string) $product->get_name() : (string) get_the_title( $product_id );
$terms = function_exists( 'get_the_terms' ) ? get_the_terms( $product_id, 'product_cat' ) : array();
$category = is_array( $terms ) && isset( $terms[0]->name ) ? (string) $terms[0]->name : '';
$image_urls = array();
foreach ( $ids as $id ) {
	$url = function_exists( 'wp_get_attachment_image_url' ) ? wp_get_attachment_image_url( $id, 'large' ) : '';
	if ( ! is_string( $url ) || '' === $url ) { $url = function_exists( 'wp_get_attachment_url' ) ? wp_get_attachment_url( $id ) : ''; }
	if ( is_string( $url ) && '' !== $url ) { $image_urls[] = array( 'id' => $id, 'url' => esc_url( $url ) ); }
}
if ( ! $image_urls ) { return; }
$count = count( $image_urls );
$first = $image_urls[0];
$first_alt = $title . ' — ' . esc_html__( 'تصویر', 'erfansanat-theme' ) . ' ۱';
?>
<div data-gallery-root="true" class="space-y-3.5">
	<div class="group relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-900 shadow-sm">
		<img src="<?php echo esc_url( $first['url'] ); ?>" data-gallery-src="<?php echo esc_url( $first['url'] ); ?>" alt="<?php echo esc_attr( $first_alt ); ?>" data-gallery-main-img="true" fetchpriority="high" decoding="async" class="h-[16rem] h-72 sm:h-[24rem] lg:h-[27rem] w-full cursor-zoom-in object-cover transition-transform duration-700">
		<div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/55 via-transparent to-transparent"></div>
		<?php if ( '' !== $category ) : ?><span class="absolute right-3.5 top-3.5 rounded-xl border border-white/25 bg-emerald-950/75 px-3 py-1 text-[10.5px] font-extrabold text-emerald-100 backdrop-blur-md"><?php echo esc_html( $category ); ?></span><?php endif; ?>
		<div class="absolute bottom-3 left-3 right-3 flex items-center justify-between sm:bottom-4 sm:left-4 sm:right-4">
			<div class="flex items-center gap-1.5 sm:gap-2"><button type="button" data-gallery-prev="true" aria-label="تصویر قبلی" class="grid h-8 w-8 place-items-center rounded-xl border border-white/25 bg-slate-900/75 text-white backdrop-blur-md transition-colors hover:bg-emerald-700">‹</button><button type="button" data-gallery-next="true" aria-label="تصویر بعدی" class="grid h-8 w-8 place-items-center rounded-xl border border-white/25 bg-slate-900/75 text-white backdrop-blur-md transition-colors hover:bg-emerald-700">›</button><span data-gallery-counter="true" aria-live="polite" class="rounded-xl border border-white/20 bg-slate-900/75 px-2.5 py-1 text-[10.5px] font-bold text-white backdrop-blur-md"><?php echo esc_html( number_format_i18n( 1 ) . ' / ' . number_format_i18n( $count ) ); ?></span></div>
			<button type="button" data-gallery-expand="true" class="rounded-xl border border-white/25 bg-white/90 px-3 py-1.5 text-[11px] font-extrabold text-slate-800">نمایش بزرگ</button>
		</div>
	</div>
	<?php if ( $count > 1 ) : ?><div class="grid grid-cols-4 gap-1.5 sm:grid-cols-5 sm:gap-2.5" aria-label="تصاویر محصول"><?php foreach ( $image_urls as $index => $image ) : $alt = $title . ' — ' . esc_html__( 'تصویر', 'erfansanat-theme' ) . ' ' . number_format_i18n( $index + 1 ); $thumb = function_exists( 'wp_get_attachment_image_url' ) ? wp_get_attachment_image_url( $image['id'], 'thumbnail' ) : $image['url']; $active = 0 === $index; ?><button type="button" data-gallery-thumb="<?php echo esc_attr( (string) $index ); ?>" data-gallery-src="<?php echo esc_url( $image['url'] ); ?>" aria-label="<?php echo esc_attr( $alt ); ?>" class="relative h-16 sm:h-20 overflow-hidden rounded-xl sm:rounded-2xl border-2 transition-all cursor-pointer <?php echo $active ? 'border-emerald-500 shadow-[0_4px_16px_-4px_rgba(5,150,105,.45)]' : 'border-transparent opacity-65 hover:opacity-100'; ?>"><img src="<?php echo esc_url( is_string( $thumb ) ? $thumb : $image['url'] ); ?>" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover"><span data-gallery-thumb-ring="true" class="pointer-events-none absolute inset-0 rounded-lg sm:rounded-xl ring-2 ring-inset ring-emerald-500 <?php echo $active ? 'block' : 'hidden'; ?>"></span></button><?php endforeach; ?></div><?php endif; ?>
	<div data-gallery-lightbox="true" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/90 p-4 backdrop-blur-md" role="dialog" aria-modal="true" aria-label="نمایش تصاویر محصول"><button type="button" data-gallery-lightbox-close="true" aria-label="بستن گالری" class="absolute right-5 top-5 grid h-11 w-11 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-white/25">×</button><button type="button" data-gallery-lightbox-prev="true" aria-label="قبلی" class="absolute right-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-emerald-600">‹</button><button type="button" data-gallery-lightbox-next="true" aria-label="بعدی" class="absolute left-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-emerald-600">›</button><img src="<?php echo esc_url( $first['url'] ); ?>" data-gallery-src="<?php echo esc_url( $first['url'] ); ?>" alt="<?php echo esc_attr( $first_alt ); ?>" data-gallery-lightbox-img="true" class="max-h-[84vh] max-w-[90vw] rounded-2xl object-contain shadow-2xl"><div class="absolute bottom-6 left-1/2 flex -translate-x-1/2 items-center gap-2"><?php foreach ( $image_urls as $index => $image ) : $active = 0 === $index; ?><button type="button" data-gallery-lightbox-dot="<?php echo esc_attr( (string) $index ); ?>" aria-label="تصویر <?php echo esc_attr( number_format_i18n( $index + 1 ) ); ?>" class="h-2 rounded-full transition-all <?php echo $active ? 'w-7 bg-emerald-400' : 'w-2 bg-white/40 hover:bg-white/70'; ?>"></button><?php endforeach; ?></div></div>
</div>
