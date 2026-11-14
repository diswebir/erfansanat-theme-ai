<?php
/**
 * Cart page (P06-T022). Overrides WooCommerce's cart template with the reference
 * layout while keeping the standard cart hooks (AC-01) and the data-cart-*
 * contract main.js enhances (AC-02). Rows, subtotal, tax, coupon discount and
 * grand total come from the real WC()->cart (AC-03). Quantity/remove controls
 * post to Woo; main.js provides the instant client-side recalculation.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\ProductQuantity;

get_header();

$es_cart = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart : null;

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};
$es_money = static function ( $amount ) use ( $es_fa ) {
	return $es_fa( number_format( (float) $amount, 0, '.', ',' ) ) . ' تومان';
};

$es_items    = $es_cart ? $es_cart->get_cart() : array();
$es_subtotal = $es_cart ? (float) $es_cart->get_cart_contents_total() : 0;
$es_tax      = ( $es_cart && method_exists( $es_cart, 'get_cart_contents_taxes' ) ) ? (float) $es_cart->get_cart_contents_taxes() : $es_subtotal * 0.10;
$es_discount = ( $es_cart && method_exists( $es_cart, 'get_discount_total' ) ) ? (float) $es_cart->get_discount_total() : 0;
$es_grand    = ( $es_cart && method_exists( $es_cart, 'get_total' ) ) ? (float) $es_cart->get_total() : ( $es_subtotal - $es_discount + $es_tax );
$es_count    = $es_cart ? (int) $es_cart->get_cart_contents_count() : 0;
$es_coupons  = ( $es_cart && method_exists( $es_cart, 'get_applied_coupons' ) ) ? $es_cart->get_applied_coupons() : array();

$es_checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
$es_shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

// Coupon handling (real Woo apply_coupon; shim mirrors the same API).
$es_coupon_error = '';
if ( isset( $_POST['es_apply_coupon'], $_POST['es_coupon_code'] ) && $es_cart ) {
	check_admin_referer( 'es_cart_coupon', 'es_cart_coupon_nonce' );
	$es_code = sanitize_text_field( wp_unslash( $_POST['es_coupon_code'] ) );
	if ( method_exists( $es_cart, 'apply_coupon' ) && $es_cart->apply_coupon( $es_code ) ) {
		$es_coupons  = $es_cart->get_applied_coupons();
		$es_discount = (float) $es_cart->get_discount_total();
		$es_grand    = (float) $es_cart->get_total();
	} else {
		$es_coupon_error = sprintf( 'کد تخفیف «%s» معتبر نیست.', $es_code );
	}
}
?>

<main class="bg-slate-50/50" data-cart-page="true">
	<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur-md">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a>
				<span class="flex min-w-0 items-center gap-1.5">
					<a href="<?php echo esc_url( $es_shop_url ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php esc_html_e( 'فروشگاه محصولات', 'erfansanat' ); ?></a>
				</span>
				<span class="max-w-[16rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php esc_html_e( 'سبد خرید', 'erfansanat' ); ?></span>
			</nav>
			<h1 class="mt-5 text-[2rem] font-black leading-[1.3] text-slate-950 sm:text-[2.6rem]"><?php esc_html_e( 'سبد خرید و لیست پیش‌فاکتور', 'erfansanat' ); ?></h1>
			<p class="mt-3 max-w-[40rem] text-[14px] font-medium leading-8 text-slate-600"><?php esc_html_e( 'اقلام انتخابی خود را بازبینی کنید؛ امکان دریافت پیش‌فاکتور رسمی یا پرداخت آنلاین فراهم است.', 'erfansanat' ); ?></p>
		</div>
	</section>

	<section class="mx-auto max-w-[1300px] px-5 py-12 lg:px-8 lg:py-16">
		<?php do_action( 'woocommerce_before_cart' ); ?>

		<div class="grid items-start gap-8 lg:grid-cols-12">
			<div class="space-y-4 lg:col-span-8">
				<div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-5 py-4 shadow-2xs">
					<div class="flex items-center gap-2 text-[13.5px] font-black text-slate-900">
						<span><?php esc_html_e( 'اقلام سبد شما', 'erfansanat' ); ?></span>
						<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-extrabold text-emerald-800"><span data-cart-count="true"><?php echo esc_html( $es_fa( $es_count ) ); ?></span> <?php esc_html_e( 'قلم', 'erfansanat' ); ?></span>
					</div>
					<button type="button" data-cart-clear="true" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[11.5px] font-extrabold text-slate-600 transition hover:border-red-200 hover:text-red-600">
						<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
						<span><?php esc_html_e( 'خالی کردن سبد', 'erfansanat' ); ?></span>
					</button>
				</div>

				<?php do_action( 'woocommerce_before_cart_table' ); ?>

				<div data-cart-items-list="true" class="space-y-4 <?php echo $es_items ? '' : 'hidden'; ?>">
					<?php foreach ( $es_items as $es_item_key => $es_item ) : ?>
						<?php
						$es_product = isset( $es_item['data'] ) && is_object( $es_item['data'] ) ? $es_item['data'] : wc_get_product( $es_item['product_id'] );
						if ( ! $es_product ) {
							continue;
						}
						$es_pid   = (int) $es_item['product_id'];
						$es_qty   = (int) $es_item['quantity'];
						$es_price = (float) $es_product->get_price();
						$es_rules = ProductQuantity::rules( $es_product );
						$es_min   = (int) $es_rules['min'];
						$es_step  = (int) $es_rules['step'];
						$es_img   = $es_product->get_image_id() ? (string) wp_get_attachment_image_url( $es_product->get_image_id(), 'thumbnail' ) : ES_ASSETS_URI . 'images/light-tunnel.jpg';
						?>
						<div data-cart-item-row="<?php echo esc_attr( $es_item_key ); ?>" data-unit-price="<?php echo esc_attr( (string) $es_price ); ?>" data-min-qty="<?php echo (int) $es_min; ?>" data-step-qty="<?php echo (int) $es_step; ?>" data-current-qty="<?php echo (int) $es_qty; ?>" class="flex flex-wrap items-center gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs sm:flex-nowrap">
							<img src="<?php echo esc_url( $es_img ); ?>" alt="<?php echo esc_attr( $es_product->get_name() ); ?>" class="h-20 w-20 shrink-0 rounded-xl object-cover" loading="lazy" />
							<div class="min-w-0 flex-1">
								<a href="<?php echo esc_url( $es_product->get_permalink() ); ?>" class="block truncate text-[14px] font-extrabold text-slate-900 transition hover:text-emerald-700"><?php echo esc_html( $es_product->get_name() ); ?></a>
								<div class="mt-1 text-[11.5px] font-bold text-slate-500"><?php echo esc_html( $es_money( $es_price ) ); ?> <span class="text-slate-400">/ <?php esc_html_e( 'واحد', 'erfansanat' ); ?></span></div>
							</div>
							<div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 p-1">
								<button type="button" data-cart-qty-dec="true" aria-label="<?php esc_attr_e( 'کاهش تعداد', 'erfansanat' ); ?>" class="grid h-8 w-8 place-items-center rounded-lg text-slate-600 transition hover:bg-white hover:text-emerald-700">
									<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>
								</button>
								<span data-cart-qty-val="true" class="w-8 text-center text-[13px] font-black text-slate-900 tabular-nums"><?php echo esc_html( $es_fa( $es_qty ) ); ?></span>
								<button type="button" data-cart-qty-inc="true" aria-label="<?php esc_attr_e( 'افزایش تعداد', 'erfansanat' ); ?>" class="grid h-8 w-8 place-items-center rounded-lg text-slate-600 transition hover:bg-white hover:text-emerald-700">
									<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
								</button>
							</div>
							<div class="w-28 text-left text-[13.5px] font-black text-emerald-800 tabular-nums" data-cart-row-total><?php echo esc_html( $es_money( $es_price * $es_qty ) ); ?></div>
							<button type="button" data-cart-remove="true" aria-label="<?php esc_attr_e( 'حذف قلم', 'erfansanat' ); ?>" class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-slate-200 text-slate-400 transition hover:border-red-200 hover:text-red-600">
								<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
							</button>
						</div>
					<?php endforeach; ?>
				</div>

				<div data-cart-empty="true" class="<?php echo $es_items ? 'hidden' : 'flex'; ?> flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center">
					<svg class="h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
					<p class="mt-4 text-[14.5px] font-extrabold text-slate-700"><?php esc_html_e( 'سبد خرید شما خالی است.', 'erfansanat' ); ?></p>
					<p class="mt-1.5 text-[12.5px] text-slate-500"><?php esc_html_e( 'برای مشاهده محصولات به فروشگاه بروید.', 'erfansanat' ); ?></p>
					<a href="<?php echo esc_url( $es_shop_url ); ?>" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'مشاهده فروشگاه', 'erfansanat' ); ?></a>
				</div>

				<?php do_action( 'woocommerce_after_cart_table' ); ?>
			</div>

			<div class="lg:col-span-4">
				<div class="sticky top-28 overflow-hidden rounded-[1.5rem] border border-slate-200/90 bg-white shadow-[0_18px_45px_-28px_rgba(15,23,42,0.10)]">
					<div class="border-b border-slate-100 bg-emerald-50/60 px-6 py-4">
						<h2 class="text-sm font-black text-emerald-950"><?php esc_html_e( 'خلاصه صورت‌حساب و شرایط ارسال', 'erfansanat' ); ?></h2>
					</div>
					<div class="space-y-4 p-6">
						<div class="space-y-2.5 text-xs">
							<div class="flex items-center justify-between text-slate-600">
								<span><?php esc_html_e( 'جمع کل اقلام قیمت‌دار:', 'erfansanat' ); ?></span>
								<span data-cart-subtotal="true" class="font-extrabold text-slate-900 tabular-nums"><?php echo esc_html( $es_money( $es_subtotal ) ); ?></span>
							</div>
							<?php if ( $es_discount > 0 ) : ?>
								<div class="flex items-center justify-between text-emerald-700">
									<span><?php esc_html_e( 'تخفیف کوپن:', 'erfansanat' ); ?></span>
									<span data-cart-discount="true" class="font-extrabold tabular-nums"><?php echo esc_html( $es_money( $es_discount ) ); ?>-</span>
								</div>
							<?php endif; ?>
							<div class="flex items-center justify-between text-slate-600">
								<span><?php esc_html_e( 'مالیات و عوارض (۱۰٪):', 'erfansanat' ); ?></span>
								<span data-cart-tax="true" class="font-bold text-slate-800 tabular-nums"><?php echo esc_html( $es_money( $es_tax ) ); ?></span>
							</div>
							<div class="flex items-center justify-between text-slate-600">
								<span><?php esc_html_e( 'بسته‌بندی صنعتی و بیمه بار:', 'erfansanat' ); ?></span>
								<span class="font-bold text-emerald-700"><?php esc_html_e( 'رایگان (کارخانه)', 'erfansanat' ); ?></span>
							</div>
						</div>

						<form method="post" class="space-y-2 border-t border-slate-100 pt-4">
							<?php wp_nonce_field( 'es_cart_coupon', 'es_cart_coupon_nonce' ); ?>
							<label class="block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'کد تخفیف / کوپن', 'erfansanat' ); ?></label>
							<div class="flex gap-2">
								<input type="text" name="es_coupon_code" placeholder="<?php esc_attr_e( 'مثلاً WELCOME10', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-800 outline-none focus:border-emerald-500" />
								<button type="submit" name="es_apply_coupon" value="1" class="shrink-0 rounded-xl bg-emerald-700 px-4 py-2 text-[12px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'اعمال', 'erfansanat' ); ?></button>
							</div>
							<?php if ( $es_coupon_error ) : ?>
								<p class="text-[11.5px] font-bold text-red-600" data-coupon-error><?php echo esc_html( $es_coupon_error ); ?></p>
							<?php endif; ?>
							<?php if ( $es_coupons ) : ?>
								<p class="text-[11.5px] font-bold text-emerald-700" data-coupon-applied><?php echo esc_html( sprintf( 'کوپن اعمال‌شده: %s', implode( '، ', $es_coupons ) ) ); ?></p>
							<?php endif; ?>
						</form>

						<div class="border-t border-slate-100 pt-4">
							<div class="flex items-baseline justify-between">
								<span class="text-xs font-extrabold text-slate-800"><?php esc_html_e( 'مبلغ قابل پرداخت:', 'erfansanat' ); ?></span>
								<span data-cart-grandtotal="true" class="text-xl font-black text-emerald-800 tabular-nums"><?php echo esc_html( $es_money( $es_grand ) ); ?></span>
							</div>
							<p class="mt-1.5 text-[11px] leading-5 text-slate-500"><?php esc_html_e( 'امکان پرداخت آنلاین با کلیه کارت‌های عضو شتاب یا دریافت پیش‌فاکتور رسمی با مهر و امضای شرکت.', 'erfansanat' ); ?></p>
						</div>

						<a href="<?php echo esc_url( $es_checkout_url ); ?>" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-500 px-5 py-3.5 text-sm font-extrabold text-white shadow-md transition hover:shadow-lg <?php echo $es_items ? '' : 'pointer-events-none opacity-50'; ?>" data-cart-checkout>
							<span><?php esc_html_e( 'ادامه و تسویه حساب', 'erfansanat' ); ?></span>
							<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
						</a>
					</div>
				</div>
			</div>
		</div>

		<?php do_action( 'woocommerce_after_cart' ); ?>
	</section>
</main>

<?php
get_footer();
