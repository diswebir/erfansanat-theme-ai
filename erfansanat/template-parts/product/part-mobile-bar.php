<?php
/**
 * Sticky purchase bar shown under the `lg` breakpoint.
 *
 * Purely additive to the real purchase form: it links to the cart and to the
 * on-page form instead of duplicating the add-to-cart POST, so there is exactly
 * one purchase path on the page.
 *
 * @package ErfanSanat
 *
 * @var \WC_Product $erfansanat_product Product being viewed.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\ProductData;

if ( ! isset( $erfansanat_product ) || ! $erfansanat_product instanceof \WC_Product ) {
	return;
}

$erfansanat_price_html = $erfansanat_product->get_price_html();
$erfansanat_direct     = ProductData::is_direct_sale( $erfansanat_product );
?>
<div class="fixed inset-x-0 bottom-0 z-40 flex items-center justify-between gap-3 border-t border-emerald-100 bg-white/95 px-4 py-3 shadow-[0_-8px_30px_-15px_rgba(15,23,42,0.2)] backdrop-blur-xl lg:hidden">
	<div>
		<div class="text-[10.5px] font-bold text-slate-400"><?php esc_html_e( 'قیمت روز کارخانه', 'erfansanat' ); ?></div>
		<div class="text-sm font-black text-emerald-800">
			<?php
			echo '' !== $erfansanat_price_html
				? wp_kses_post( $erfansanat_price_html )
				: esc_html__( 'استعلام قیمت', 'erfansanat' );
			?>
		</div>
	</div>
	<a href="#buy" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-700 px-5 py-2.5 text-xs font-black text-white shadow-md transition-all hover:from-emerald-800 hover:to-emerald-800 whitespace-nowrap">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
		<span><?php echo esc_html( $erfansanat_direct ? __( 'ثبت سفارش', 'erfansanat' ) : __( 'استعلام و تماس', 'erfansanat' ) ); ?></span>
	</a>
</div>
