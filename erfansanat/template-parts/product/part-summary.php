<?php
/**
 * Product summary card: identity, price, spec chips and the purchase form.
 *
 * The reference button wrote straight to browser storage and faked a cart. This
 * version prints WooCommerce's own purchase form instead, so the button posts
 * to the real add-to-cart handler and a product with `true_sale = false` gets a
 * quote CTA rather than a dead "add to cart".
 *
 * @package ErfanSanat
 *
 * @var \WC_Product $erfansanat_product Product being viewed.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\ProductData;
use ErfanSanat\Theme\Frontend\SectionParts;

if ( ! isset( $erfansanat_product ) || ! $erfansanat_product instanceof \WC_Product ) {
	return;
}

$erfansanat_id      = (int) $erfansanat_product->get_id();
$erfansanat_rating  = ProductData::rating( $erfansanat_product );
$erfansanat_sku     = (string) $erfansanat_product->get_sku();
$erfansanat_direct  = ProductData::is_direct_sale( $erfansanat_product );
$erfansanat_summary = ProductData::summary_specs( $erfansanat_product, 3 );
$erfansanat_notice  = \ErfanSanat\Theme\Data\LegacyMeta::text( $erfansanat_id, 'before-sale-c' );
$erfansanat_min_qty = max( 1, (int) $erfansanat_product->get_min_purchase_quantity() );
$erfansanat_catalog = ProductData::catalog_url( $erfansanat_id );

// Three states, not two. A product flagged for online sale but currently out
// of stock must say so; offering a quote CTA there would read as "sold
// elsewhere" instead of "temporarily unavailable".
$erfansanat_in_stock = $erfansanat_product->is_in_stock();
$erfansanat_direct   = $erfansanat_direct && $erfansanat_in_stock;
?>
<div class="overflow-hidden rounded-3xl border border-emerald-200/80 bg-white shadow-[0_20px_50px_-24px_rgba(5,150,105,0.16)]">
	<div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-emerald-50/60 px-5 py-3.5 sm:px-6">
		<div class="flex items-center gap-2">
			<span class="h-2.5 w-2.5 rounded-full <?php echo $erfansanat_direct ? 'bg-emerald-500 ring-4 ring-emerald-500/20' : 'bg-amber-500 ring-4 ring-amber-500/20'; ?>"></span>
			<span class="text-xs font-extrabold text-emerald-950">
				<?php
				echo esc_html(
					$erfansanat_direct
						? __( 'آماده ثبت سفارش آنلاین', 'erfansanat' )
						: ( $erfansanat_in_stock
							? __( 'فروش استعلامی — تماس با واحد فروش', 'erfansanat' )
							: __( 'موقتاً ناموجود — استعلام موجودی', 'erfansanat' ) )
				);
				?>
			</span>
		</div>
		<div class="flex items-center gap-2 text-[11.5px] font-bold text-slate-600">
			<?php if ( $erfansanat_rating['count'] > 0 ) : ?>
				<span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-0.5 text-amber-600 ring-1 ring-amber-200">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" class="h-3 w-3" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
					<span class="tabular-nums"><?php echo esc_html( number_format_i18n( $erfansanat_rating['average'], 2 ) ); ?></span>
					<span class="sr-only"><?php esc_html_e( 'از ۵', 'erfansanat' ); ?></span>
				</span>
			<?php endif; ?>
			<?php if ( '' !== $erfansanat_sku ) : ?>
				<span dir="ltr" class="rounded-md bg-white px-2 py-0.5 font-mono text-[11px] font-bold text-slate-600 ring-1 ring-slate-200/80"><?php echo esc_html( $erfansanat_sku ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<div class="space-y-5 p-5 sm:p-6">
		<div>
			<?php
			// Real WooCommerce price HTML: regular price, sale price and the
			// currency symbol all come from the store's own settings.
			$erfansanat_price_html = $erfansanat_product->get_price_html();
			?>
			<?php if ( '' !== $erfansanat_price_html ) : ?>
				<div class="flex flex-wrap items-baseline justify-between gap-2">
					<span class="text-2xl font-black text-emerald-800"><?php echo wp_kses_post( $erfansanat_price_html ); ?></span>
					<?php if ( $erfansanat_direct ) : ?>
						<span class="w-full text-[11px] font-bold text-slate-400"><?php esc_html_e( 'قیمت مصوب کارخانه — با احتساب مالیات بر ارزش افزوده', 'erfansanat' ); ?></span>
					<?php endif; ?>
					<?php if ( $erfansanat_product->is_on_sale() ) : ?>
						<span class="rounded-md bg-rose-50 px-2 py-0.5 text-[10px] font-extrabold text-rose-700"><?php esc_html_e( 'قیمت با تخفیف', 'erfansanat' ); ?></span>
					<?php endif; ?>
					<?php if ( $erfansanat_direct ) : ?>
						<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-bold text-emerald-800">
							<?php
							printf(
								/* translators: %s: minimum order quantity. */
								esc_html__( 'حداقل سفارش: %s', 'erfansanat' ),
								esc_html( SectionParts::fa_digits( (string) $erfansanat_min_qty ) )
							);
							?>
						</span>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<p class="text-sm font-extrabold text-slate-700"><?php esc_html_e( 'قیمت این محصول پس از استعلام اعلام می‌شود.', 'erfansanat' ); ?></p>
			<?php endif; ?>

			<?php
			echo wc_get_stock_html( $erfansanat_product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce builds and escapes its own stock markup.
			?>

			<?php if ( '' !== $erfansanat_notice ) : ?>
				<p class="mt-2 rounded-xl border border-amber-200/90 bg-amber-50/70 px-3 py-2 text-[12px] font-bold leading-6 text-amber-900">
					<?php echo esc_html( $erfansanat_notice ); ?>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( array() !== $erfansanat_summary ) : ?>
			<div class="border-t border-slate-100 pt-4">
				<div class="mb-2.5 flex items-center justify-between">
					<span class="text-xs font-extrabold text-slate-800"><?php esc_html_e( 'چکیده مشخصات', 'erfansanat' ); ?></span>
					<a href="#product-details" data-scroll-to-specs="true" class="text-[11px] font-bold text-emerald-700 hover:underline"><?php esc_html_e( 'مشخصات کامل ↓', 'erfansanat' ); ?></a>
				</div>
				<div class="grid grid-cols-3 gap-2">
					<?php foreach ( $erfansanat_summary as $erfansanat_row ) : ?>
						<div class="rounded-xl border border-slate-100 bg-slate-50/80 px-2.5 py-2 text-center">
							<div class="truncate text-[10px] font-semibold text-slate-400"><?php echo esc_html( $erfansanat_row['label'] ); ?></div>
							<div class="mt-0.5 truncate text-[11.5px] font-extrabold text-slate-800"><?php echo esc_html( $erfansanat_row['value'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<div id="buy" class="scroll-mt-28 space-y-2.5 pt-1">
			<?php
			if ( $erfansanat_direct ) {
				/*
				 * WooCommerce's own dispatcher, not a hand-built template path.
				 * It resolves the per-type template, passes the arguments the
				 * variable template requires and enqueues the variation
				 * script; calling `wc_get_template()` directly skips all three
				 * and a variable product then dies on a missing attribute map.
				 */
				// WooCommerce's add-to-cart templates read the product from the unprefixed global.
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
				$GLOBALS['product'] = $erfansanat_product;
				woocommerce_template_single_add_to_cart();
			} else {
				?>
				<a href="<?php echo esc_url( SectionParts::phone_href() ); ?>" class="group relative inline-flex w-full items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-700 px-5 py-4 text-sm font-black text-white shadow-[0_12px_28px_-8px_rgba(0,154,56,0.48)] ring-1 ring-emerald-500/40 transition-all duration-300 hover:-translate-y-0.5 hover:from-emerald-800 hover:via-emerald-700 hover:to-emerald-800">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
					<span>
						<?php
						echo esc_html(
							$erfansanat_in_stock
								? __( 'استعلام قیمت و ثبت پیش‌فاکتور', 'erfansanat' )
								: __( 'استعلام موجودی و زمان تحویل', 'erfansanat' )
						);
						?>
					</span>
				</a>
				<?php
			}
			?>

			<div class="grid grid-cols-2 gap-2.5">
				<a href="<?php echo esc_url( SectionParts::phone_href() ); ?>" class="flex items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50/80 px-3 py-2.5 text-xs font-extrabold text-emerald-900 transition-all hover:border-emerald-400 hover:bg-emerald-100/80 whitespace-nowrap">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
					<span><?php echo esc_html( SectionParts::phone() ); ?></span>
				</a>
				<a href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '' ); ?>" class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-extrabold text-slate-700 transition-all hover:border-emerald-300 hover:bg-white hover:text-emerald-800 whitespace-nowrap">
					<span><?php esc_html_e( 'مشاهده سبد خرید', 'erfansanat' ); ?></span>
				</a>
			</div>

			<?php if ( '' !== $erfansanat_catalog ) : ?>
				<a href="<?php echo esc_url( $erfansanat_catalog ); ?>" class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-extrabold text-slate-700 transition-all hover:border-emerald-300 hover:text-emerald-800" download>
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
					<span><?php esc_html_e( 'دانلود کاتالوگ محصول', 'erfansanat' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
