<?php
/**
 * Light product tile: the card the shop grid and the related-products rail use.
 *
 * Structure is the reference card, so both places share one markup source. The
 * wrapper (grid item, filter attributes) stays with the caller, because the
 * shop grid needs `data-product-*` on the outer element while the related rail
 * does not.
 *
 * @package ErfanSanat
 *
 * @var \WC_Product $erfansanat_product Product to render.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\ProductData;
use ErfanSanat\Theme\Frontend\SectionParts;

if ( ! isset( $erfansanat_product ) || ! $erfansanat_product instanceof \WC_Product ) {
	return;
}

$erfansanat_id       = (int) $erfansanat_product->get_id();
$erfansanat_name     = $erfansanat_product->get_name();
$erfansanat_url      = (string) get_permalink( $erfansanat_id );
$erfansanat_image    = (string) get_the_post_thumbnail_url( $erfansanat_id, 'erfansanat-card' );
$erfansanat_cats     = ProductData::categories( $erfansanat_product );
$erfansanat_cat_name = array() !== $erfansanat_cats ? (string) $erfansanat_cats[0]->name : '';
$erfansanat_sku      = (string) $erfansanat_product->get_sku();
$erfansanat_ip       = ProductData::ip_rating( $erfansanat_product );
$erfansanat_direct   = ProductData::is_direct_sale( $erfansanat_product );
$erfansanat_rating   = ProductData::rating( $erfansanat_product );
$erfansanat_excerpt  = (string) $erfansanat_product->get_short_description();
$erfansanat_excerpt  = trim( wp_strip_all_tags( $erfansanat_excerpt ) );
?>
<a href="<?php echo esc_url( $erfansanat_url ); ?>" class="card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]">
	<div class="relative h-52 overflow-hidden">
		<?php if ( '' !== $erfansanat_image ) : ?>
			<img src="<?php echo esc_url( $erfansanat_image ); ?>" alt="<?php echo esc_attr( $erfansanat_name ); ?>" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-[1.2s] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110" />
		<?php else : ?>
			<div class="grid h-full w-full place-items-center bg-slate-100 text-[11px] font-bold text-slate-400"><?php esc_html_e( 'بدون تصویر', 'erfansanat' ); ?></div>
		<?php endif; ?>

		<?php if ( '' !== $erfansanat_cat_name ) : ?>
			<span class="absolute right-3.5 top-3.5 rounded-full bg-emerald-950/80 px-3 py-1 text-[10.5px] font-bold text-emerald-100 backdrop-blur-md"><?php echo esc_html( $erfansanat_cat_name ); ?></span>
		<?php endif; ?>

		<span class="absolute left-3.5 top-3.5 rounded-full px-3 py-1 text-[10.5px] font-bold shadow-lg backdrop-blur-md <?php echo $erfansanat_direct ? 'bg-emerald-600 text-white shadow-emerald-600/25' : 'bg-amber-500 text-white shadow-amber-500/25'; ?>">
			<?php echo esc_html( $erfansanat_direct ? __( 'خرید آنلاین', 'erfansanat' ) : __( 'استعلامی', 'erfansanat' ) ); ?>
		</span>

		<?php if ( '' !== $erfansanat_ip ) : ?>
			<span dir="ltr" class="absolute bottom-3 left-3.5 rounded-lg bg-white/95 px-2 py-0.5 font-mono text-[10.5px] font-black text-emerald-900 shadow-xs"><?php echo esc_html( $erfansanat_ip ); ?></span>
		<?php endif; ?>
	</div>

	<div class="flex flex-1 flex-col gap-2.5 p-5">
		<div class="flex items-center justify-between text-[11px] font-bold text-slate-400">
			<span dir="ltr" class="font-mono text-slate-500"><?php echo esc_html( $erfansanat_sku ); ?></span>
			<span class="flex items-center gap-2.5">
				<?php if ( $erfansanat_rating['count'] > 0 ) : ?>
					<span class="flex items-center gap-1 text-amber-600">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
						<?php echo esc_html( number_format_i18n( $erfansanat_rating['average'], 2 ) ); ?>
					</span>
				<?php endif; ?>
				<?php if ( 0 !== $erfansanat_product->get_total_sales() ) : ?>
					<span class="flex items-center gap-1">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
						<?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_product->get_total_sales() ) ); ?>
					</span>
				<?php endif; ?>
			</span>
		</div>

		<div class="flex items-start justify-between gap-2">
			<h3 class="text-[16px] font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( $erfansanat_name ); ?></h3>
			<span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700 transition-all duration-400 group-hover:rotate-45 group-hover:bg-emerald-600 group-hover:text-white">
				<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			</span>
		</div>

		<?php if ( '' !== $erfansanat_excerpt ) : ?>
			<p class="line-clamp-2 text-[12.5px] leading-6 text-slate-600"><?php echo esc_html( $erfansanat_excerpt ); ?></p>
		<?php endif; ?>

		<div class="mt-auto border-t border-slate-100 pt-3.5">
			<div class="flex items-center justify-between gap-2">
				<div class="text-[14px] font-black text-emerald-700">
					<?php
					$erfansanat_price_html = $erfansanat_product->get_price_html();
					echo '' !== $erfansanat_price_html
						? wp_kses_post( $erfansanat_price_html )
						: esc_html__( 'استعلام قیمت', 'erfansanat' );
					?>
				</div>
				<?php if ( $erfansanat_product->is_in_stock() ) : ?>
					<span class="rounded-lg bg-slate-100 px-2 py-1 text-[10.5px] font-bold text-slate-600"><?php esc_html_e( 'موجود در انبار', 'erfansanat' ); ?></span>
				<?php else : ?>
					<span class="rounded-lg bg-rose-50 px-2 py-1 text-[10.5px] font-bold text-rose-700"><?php esc_html_e( 'ناموجود', 'erfansanat' ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</div>
</a>
