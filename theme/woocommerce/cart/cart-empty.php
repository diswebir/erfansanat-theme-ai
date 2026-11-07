<?php
/** Branded, accessible empty cart with the standard WooCommerce hooks. */
/** @version 7.0.1 */

defined( 'ABSPATH' ) || exit;
do_action( 'woocommerce_before_cart' );
?>
<section class="relative overflow-hidden border-b border-emerald-100 bg-slate-50 pb-10 pt-28 lg:pb-12"><div class="mx-auto max-w-[1300px] px-5 lg:px-8"><h1 class="text-3xl font-black text-slate-950 sm:text-4xl">سبد خرید</h1></div></section><section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16"><div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"><div aria-hidden="true" class="mx-auto mb-5 grid h-16 w-16 place-items-center rounded-2xl bg-emerald-50 text-3xl text-emerald-700">🛒</div><h1 class="text-2xl font-black text-slate-900"><?php esc_html_e( 'سبد خرید شما خالی است', 'erfansanat-theme' ); ?></h1><p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-600">برای دیدن محصولات و دریافت اطلاعات بیشتر به فروشگاه محصولات عرفان صنعت بروید.</p><p class="return-to-shop mt-7"><a class="button wc-backward inline-flex items-center justify-center rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white transition-colors hover:bg-emerald-800" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'بازگشت به فروشگاه', 'woocommerce' ) ) ); ?></a></p></div></section>
<?php do_action( 'woocommerce_after_cart' ); ?>
