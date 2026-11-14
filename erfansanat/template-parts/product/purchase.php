<?php
/**
 * Product purchase / summary card (P06-T006). Renders the sticky summary card:
 * header (sale-mode badge + rating + SKU), price block, and the mode-dependent
 * CTA. true_sale=true + in-stock + priced -> online add-to-cart (AC-01, SC-02);
 * true_sale=false -> inquiry / proforma CTA (AC-02); out-of-stock or unpriced ->
 * correct notice (AC-03). Inner hooks let later tasks enrich the card:
 * es_product_summary_mid (specs T009, swatches/qty T007) and
 * es_product_summary_after (notices/catalog T008). The add-to-cart button keeps
 * every data-product-* attribute main.js reads (immutable JS).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\ProductQuantity;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

$es_product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : false;
if ( ! $es_product ) {
	return;
}

$es_id        = $es_product->get_id();
$es_title     = get_the_title( $es_id );
$es_true_sale = (string) get_post_meta( $es_id, 'true_sale', true );
$es_is_online = ( 'true' === $es_true_sale );
$es_in_stock  = $es_product->is_in_stock();
$es_price     = $es_product->get_price();
$es_regular   = $es_product->get_regular_price();
$es_on_sale   = $es_product->is_on_sale();
$es_sku       = $es_product->get_sku();
$es_rating    = (float) $es_product->get_average_rating();
$es_qty_rules = ProductQuantity::rules( $es_product );
$es_min       = $es_qty_rules['min'];
$es_step      = $es_qty_rules['step'];
$es_max       = $es_qty_rules['max'];

// Real Woo variable products use the canonical variation form (Variation
// Swatches + WPC Quantity own it). The static stepper/swatches below are the
// QA / no-Woo fallback only.
$es_real_woo    = defined( 'WC_VERSION' );
$es_is_variable = method_exists( $es_product, 'is_type' ) && $es_product->is_type( 'variable' );
$es_use_native_variation = $es_real_woo && $es_is_variable;
$es_unit      = (string) apply_filters( 'es_product_purchase_unit', (string) get_post_meta( $es_id, '_unit', true ), $es_id );
if ( '' === $es_unit ) {
	$es_unit = 'عدد';
}

// Online purchase is possible only when online + in stock + a positive price.
$es_can_buy = $es_is_online && $es_in_stock && is_numeric( $es_price ) && (float) $es_price > 0;

$es_fa_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_fa        = static function ( $v ) use ( $es_fa_digits ) { return strtr( (string) $v, $es_fa_digits ); };
$es_toman     = static function ( $amount ) use ( $es_fa ) { return $es_fa( number_format( (float) $amount, 0, '.', ',' ) ) . ' تومان'; };

$es_cats      = get_the_terms( $es_id, 'product_cat' );
$es_cat_name  = ( $es_cats && ! is_wp_error( $es_cats ) ) ? $es_cats[0]->name : '';
$es_img       = get_the_post_thumbnail_url( $es_id, 'large' );
if ( ! $es_img ) {
	$es_img = ES_ASSETS_URI . 'images/hero.jpg';
}

$es_contact = FooterTab::contact();
$es_phone   = '' !== $es_contact['phone'] ? $es_contact['phone'] : '031-91091011';
$es_phone_fa = 'مشاوره: ' . $es_fa( str_replace( '-', ' - ', $es_phone ) );
$es_cart_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'cart' ) : home_url( '/cart/' );
$es_checkout_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'checkout' ) : home_url( '/checkout/' );

$es_badge   = $es_is_online ? 'آماده ثبت سفارش آنلاین' : 'تولید سفارشی کارخانه';
$es_rating_fa = $es_fa( number_format( $es_rating, 2 ) );

$es_star   = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>';
$es_file   = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>';
$es_phoneI = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>';
$es_cartI  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>';
$es_cartW  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>';

// ── P06-T007: swatches (Variation Swatches) + WPC quantity stepper ─────────.
$es_color_terms = get_the_terms( $es_id, 'pa_color' );
$es_size_terms  = get_the_terms( $es_id, 'pa_size' );
$es_color_terms = ( $es_color_terms && ! is_wp_error( $es_color_terms ) ) ? array_values( $es_color_terms ) : array();
$es_size_terms  = ( $es_size_terms && ! is_wp_error( $es_size_terms ) ) ? array_values( $es_size_terms ) : array();

// Term name -> swatch dot class (filterable; defaults mirror the reference).
$es_color_map = apply_filters(
	'es_product_color_swatch_map',
	array(
		'فول‌کالر (هفت رنگ خودکار)' => 'bg-gradient-to-r from-red-500 via-emerald-500 to-blue-500',
		'فول کالر'                  => 'bg-gradient-to-r from-red-500 via-emerald-500 to-blue-500',
		'سبز'                       => 'bg-emerald-500',
		'قرمز'                      => 'bg-red-500',
		'سفید'                      => 'bg-white border border-slate-200',
		'آبی'                       => 'bg-blue-500',
		'زرد'                       => 'bg-amber-400',
		'بنفش'                      => 'bg-purple-500',
		'نارنجی'                    => 'bg-orange-500',
		'صورتی'                     => 'bg-pink-500',
		'مشکی'                      => 'bg-slate-900',
		'آفتابی'                    => 'bg-amber-300',
		'مهتابی'                    => 'bg-slate-100 border border-slate-200',
	),
	$es_id
);

$es_minus = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-minus h-3.5 w-3.5" aria-hidden="true"><path d="M5 12h14"></path></svg>';
$es_plus  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-3.5 w-3.5" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>';
$es_qty_total = $es_toman( (float) $es_price * $es_min );
?>
<div class="overflow-hidden rounded-3xl border border-emerald-200/80 bg-white shadow-[0_20px_50px_-24px_rgba(5,150,105,0.16)]">
	<div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-emerald-50/60 px-5 py-3.5 sm:px-6">
		<div class="flex items-center gap-2">
			<span class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20"></span>
			<span class="text-xs font-extrabold text-emerald-950"><?php echo esc_html( $es_badge ); ?></span>
		</div>
		<div class="flex items-center gap-2 text-[11.5px] font-bold text-slate-600">
			<?php if ( $es_rating > 0 ) : ?>
				<span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-0.5 text-amber-600 ring-1 ring-amber-200"><?php echo $es_star; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $es_rating_fa ); ?></span></span>
			<?php endif; ?>
			<?php if ( '' !== $es_sku ) : ?>
				<span dir="ltr" class="rounded-md bg-white px-2 py-0.5 font-mono text-[11px] font-bold text-slate-600 ring-1 ring-slate-200/80"><?php echo esc_html( $es_sku ); ?></span>
			<?php endif; ?>
		</div>
	</div>
	<div class="space-y-5 p-5 sm:p-6">
		<div>
			<?php if ( $es_can_buy ) : ?>
				<?php if ( $es_on_sale ) : ?>
					<div class="mb-1 flex items-center gap-2">
						<span class="text-xs font-bold text-slate-400 line-through"><?php echo esc_html( $es_toman( $es_regular ) ); ?></span>
						<span class="rounded-md bg-rose-50 px-2 py-0.5 text-[10px] font-extrabold text-rose-700">قیمت مصوب کارخانه</span>
					</div>
				<?php endif; ?>
				<div class="flex flex-wrap items-baseline justify-between gap-2">
					<span class="text-2xl font-black text-emerald-800"><?php echo esc_html( $es_toman( $es_price ) ); ?></span>
					<?php if ( $es_min > 1 ) : ?>
						<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-bold text-emerald-800">حداقل سفارش: <?php echo esc_html( $es_fa( $es_min ) . ' ' . $es_unit ); ?></span>
					<?php endif; ?>
				</div>
			<?php elseif ( ! $es_in_stock ) : ?>
				<div class="flex flex-wrap items-baseline justify-between gap-2">
					<span class="text-2xl font-black text-slate-400">ناموجود</span>
				</div>
			<?php else : ?>
				<div class="flex flex-wrap items-baseline justify-between gap-2">
					<span class="text-2xl font-black text-emerald-800">استعلام قیمت روز</span>
				</div>
			<?php endif; ?>
			<p class="mt-1 text-[11.5px] leading-5 text-slate-500">فروش مستقیم از کارخانه عرفان صنعت اصفهان بدون واسطه (همراه با فاکتور رسمی)</p>
		</div>
		<?php
		/** Hook: es_product_summary_mid — key specs (T009), swatches/quantity (T007). */
		do_action( 'es_product_summary_mid', $es_product );
		?>
		<?php if ( ! $es_use_native_variation && $es_color_terms ) : ?>
			<div data-opt-group="رنگ نور" class="space-y-2 border-t border-slate-100 pt-4">
				<div class="flex items-center justify-between text-xs">
					<span class="font-bold text-slate-700">رنگ نور:</span>
					<span data-opt-selected-label="true" class="font-extrabold text-emerald-700"><?php echo esc_html( $es_color_terms[0]->name ); ?></span>
				</div>
				<div class="flex flex-wrap gap-1.5">
					<?php foreach ( $es_color_terms as $es_ci => $es_cterm ) :
						$es_sel    = ( 0 === $es_ci );
						$es_dot    = $es_color_map[ $es_cterm->name ] ?? 'bg-slate-300';
						$es_ccls   = 'flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11.5px] font-bold transition-all cursor-pointer ' . ( $es_sel ? 'bg-emerald-900 text-white ring-2 ring-emerald-600 ring-offset-1' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' );
						?>
						<button type="button" data-opt-btn="true" data-opt-is-color="true" data-opt-value-label="<?php echo esc_attr( $es_cterm->name ); ?>" title="<?php echo esc_attr( $es_cterm->name ); ?>" class="<?php echo esc_attr( $es_ccls ); ?>"><span class="h-3 w-3 rounded-full <?php echo esc_attr( $es_dot ); ?>"></span><span><?php echo esc_html( $es_cterm->name ); ?></span></button>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( ! $es_use_native_variation && $es_size_terms ) : ?>
			<div data-opt-group="طول و تراکم ریسه" class="space-y-2 border-t border-slate-100 pt-4">
				<div class="flex items-center justify-between text-xs">
					<span class="font-bold text-slate-700">طول و تراکم ریسه:</span>
					<span data-opt-selected-label="true" class="font-extrabold text-emerald-700"><?php echo esc_html( $es_size_terms[0]->name ); ?></span>
				</div>
				<div class="flex flex-wrap gap-1.5">
					<?php foreach ( $es_size_terms as $es_si => $es_sterm ) :
						$es_sel  = ( 0 === $es_si );
						$es_scls = 'rounded-xl border px-3 py-1.5 text-[11.5px] font-bold transition-all cursor-pointer ' . ( $es_sel ? 'border-emerald-700 bg-emerald-700 text-white shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-400 hover:bg-white' );
						?>
						<button type="button" data-opt-btn="true" data-opt-is-color="false" data-opt-value-label="<?php echo esc_attr( $es_sterm->name ); ?>" class="<?php echo esc_attr( $es_scls ); ?>"><?php echo esc_html( $es_sterm->name ); ?></button>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		<div class="space-y-2.5 pt-1">
			<?php if ( $es_use_native_variation ) : ?>
				<?php global $product; $product = $es_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
				<?php get_template_part( 'woocommerce/single-product/variable' ); ?>
			<?php elseif ( $es_can_buy ) : ?>
				<div data-pdp-qty-root="true" data-unit-price="<?php echo esc_attr( $es_price ); ?>" data-min-qty="<?php echo esc_attr( (string) $es_min ); ?>" data-step-qty="<?php echo esc_attr( (string) $es_step ); ?>" data-max-qty="<?php echo esc_attr( (string) $es_max ); ?>" class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5">
					<span class="text-xs font-bold text-slate-700">تعداد سفارش:</span>
					<div class="flex items-center gap-3">
						<div class="inline-flex items-center rounded-xl border border-slate-200 bg-white p-0.5 shadow-2xs">
							<button type="button" data-pdp-qty-dec="true" aria-label="کاهش تعداد" class="grid h-7 w-7 place-items-center rounded-lg text-slate-600 transition-colors hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer"><?php echo $es_minus; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
							<span data-pdp-qty-value="true" class="min-w-9 px-1.5 text-center font-mono text-xs font-black text-slate-900 tabular-nums"><?php echo esc_html( $es_fa( $es_min ) ); ?></span>
							<button type="button" data-pdp-qty-inc="true" aria-label="افزایش تعداد" class="grid h-7 w-7 place-items-center rounded-lg text-slate-600 transition-colors hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer"><?php echo $es_plus; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						</div>
						<span data-pdp-total-value="true" class="text-xs font-black text-emerald-800 tabular-nums"><?php echo esc_html( $es_qty_total ); ?></span>
					</div>
				</div>
				<button type="button" data-pdp-add-to-cart="true" data-product-slug="<?php echo esc_attr( $es_product->get_slug() ); ?>" data-product-title="<?php echo esc_attr( $es_title ); ?>" data-product-sku="<?php echo esc_attr( $es_sku ); ?>" data-product-price="<?php echo esc_attr( $es_price ); ?>" data-product-unit="<?php echo esc_attr( $es_unit ); ?>" data-product-img="<?php echo esc_url( $es_img ); ?>" data-product-image="<?php echo esc_url( $es_img ); ?>" data-product-category="<?php echo esc_attr( $es_cat_name ); ?>" data-product-min="<?php echo esc_attr( (string) $es_min ); ?>" data-product-step="<?php echo esc_attr( (string) $es_step ); ?>" data-product-max="<?php echo esc_attr( (string) $es_max ); ?>" class="group relative inline-flex w-full items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-700 px-5 py-4 text-sm font-black text-white shadow-[0_12px_28px_-8px_rgba(0,154,56,0.48)] ring-1 ring-emerald-500/40 transition-all duration-300 hover:-translate-y-0.5 hover:from-emerald-800 hover:via-emerald-700 hover:to-emerald-800 hover:shadow-[0_16px_34px_-8px_rgba(0,154,56,0.62)] active:translate-y-0 cursor-pointer whitespace-nowrap"><?php echo $es_cartW; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span data-pdp-add-label="true">افزودن به سبد خرید و پیش‌فاکتور</span></button>
				<div data-pdp-added-banner="true" data-pdp-cart-toast="true" class="hidden items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-xs">
					<span class="font-extrabold text-emerald-900">✓ به سبد خرید اضافه شد</span>
					<a href="<?php echo esc_url( $es_cart_url ); ?>" class="rounded-lg bg-emerald-700 px-3 py-1.5 font-black text-white transition-colors hover:bg-emerald-800">مشاهده سبد خرید ←</a>
				</div>
			<?php else : ?>
				<button type="button" data-quote-open="product" data-quote-id="<?php echo esc_attr( (string) $es_product->get_id() ); ?>" data-quote-title="<?php echo esc_attr( $es_title ); ?>" class="group relative inline-flex w-full items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-700 px-5 py-4 text-sm font-black text-white shadow-[0_12px_28px_-8px_rgba(0,154,56,0.48)] ring-1 ring-emerald-500/40 transition-all duration-300 hover:-translate-y-0.5 hover:from-emerald-800 hover:via-emerald-700 hover:to-emerald-800 hover:shadow-[0_16px_34px_-8px_rgba(0,154,56,0.62)] active:translate-y-0 cursor-pointer whitespace-nowrap"><?php echo $es_file; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>درخواست پیش‌فاکتور رسمی و استعلام قیمت</span></button>
			<?php endif; ?>
			<?php if ( ! $es_is_online ) : ?>
				<?php get_template_part( 'template-parts/forms/quote' ); // P08-T014: quote popup (dependency recorded). ?>
			<?php endif; ?>
			<div class="grid grid-cols-2 gap-2.5">
				<a href="<?php echo esc_url( 'tel:' . $es_phone ); ?>" class="flex items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50/80 px-3 py-2.5 text-xs font-extrabold text-emerald-900 transition-all hover:border-emerald-400 hover:bg-emerald-100/80 whitespace-nowrap"><?php echo $es_phoneI; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $es_phone_fa ); ?></span></a>
				<a href="<?php echo esc_url( $es_cart_url ); ?>" class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-extrabold text-slate-700 transition-all hover:border-emerald-300 hover:bg-emerald-50 whitespace-nowrap"><?php echo $es_cartI; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>مشاهده سبد خرید</span></a>
			</div>
		</div>
		<?php
		/** Hook: es_product_summary_after — notices / catalog (T008). */
		do_action( 'es_product_summary_after', $es_product );
		?>
	</div>
</div>
