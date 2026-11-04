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
$es_min       = max( 1, (int) get_post_meta( $es_id, '_min_qty', true ) );
$es_step      = max( 1, (int) apply_filters( 'es_product_purchase_step', (int) get_post_meta( $es_id, '_step_qty', true ), $es_id ) );
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
		<div class="space-y-2.5 pt-1">
			<?php if ( $es_can_buy ) : ?>
				<button type="button" data-pdp-add-to-cart="true" data-product-slug="<?php echo esc_attr( $es_product->get_slug() ); ?>" data-product-title="<?php echo esc_attr( $es_title ); ?>" data-product-sku="<?php echo esc_attr( $es_sku ); ?>" data-product-price="<?php echo esc_attr( $es_price ); ?>" data-product-unit="<?php echo esc_attr( $es_unit ); ?>" data-product-img="<?php echo esc_url( $es_img ); ?>" data-product-image="<?php echo esc_url( $es_img ); ?>" data-product-category="<?php echo esc_attr( $es_cat_name ); ?>" data-product-min="<?php echo esc_attr( (string) $es_min ); ?>" data-product-step="<?php echo esc_attr( (string) $es_step ); ?>" class="group relative inline-flex w-full items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-700 px-5 py-4 text-sm font-black text-white shadow-[0_12px_28px_-8px_rgba(0,154,56,0.48)] ring-1 ring-emerald-500/40 transition-all duration-300 hover:-translate-y-0.5 hover:from-emerald-800 hover:via-emerald-700 hover:to-emerald-800 hover:shadow-[0_16px_34px_-8px_rgba(0,154,56,0.62)] active:translate-y-0 cursor-pointer whitespace-nowrap"><?php echo $es_cartW; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span data-pdp-add-label="true">افزودن به سبد خرید و پیش‌فاکتور</span></button>
				<div data-pdp-added-banner="true" data-pdp-cart-toast="true" class="hidden items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-xs">
					<span class="font-extrabold text-emerald-900">✓ به سبد خرید اضافه شد</span>
					<a href="<?php echo esc_url( $es_cart_url ); ?>" class="rounded-lg bg-emerald-700 px-3 py-1.5 font-black text-white transition-colors hover:bg-emerald-800">مشاهده سبد خرید ←</a>
				</div>
			<?php else : ?>
				<a href="<?php echo esc_url( $es_checkout_url ); ?>" class="group relative inline-flex w-full items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-700 px-5 py-4 text-sm font-black text-white shadow-[0_12px_28px_-8px_rgba(0,154,56,0.48)] ring-1 ring-emerald-500/40 transition-all duration-300 hover:-translate-y-0.5 hover:from-emerald-800 hover:via-emerald-700 hover:to-emerald-800 hover:shadow-[0_16px_34px_-8px_rgba(0,154,56,0.62)] active:translate-y-0 whitespace-nowrap"><?php echo $es_file; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>درخواست پیش‌فاکتور رسمی و استعلام قیمت</span></a>
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
