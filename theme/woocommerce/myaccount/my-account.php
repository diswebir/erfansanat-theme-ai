<?php
/** Classic WooCommerce account shell preserving endpoint actions. */
/** @version 3.5.0 */

defined( 'ABSPATH' ) || exit;
?>
<section class="relative overflow-hidden border-b border-emerald-100 bg-slate-50 pb-10 pt-28 lg:pb-12"><div class="mx-auto max-w-[1300px] px-5 lg:px-8"><h1 class="text-3xl font-black text-slate-950 sm:text-4xl"><?php esc_html_e( 'حساب کاربری من', 'erfansanat-theme' ); ?></h1><p class="mt-2 text-sm text-slate-600">سفارش‌ها، نشانی‌ها و اطلاعات حساب خود را مدیریت کنید.</p></div></section>
<section class="woocommerce-account mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16"><div class="grid items-start gap-6 lg:grid-cols-3"><div class=""><?php do_action( 'woocommerce_account_navigation' ); ?></div><div class="woocommerce-MyAccount-content lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7"><?php do_action( 'woocommerce_account_content' ); ?></div></div></section>
