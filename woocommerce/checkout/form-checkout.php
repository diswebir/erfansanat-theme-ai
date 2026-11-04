<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
?>
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" data-checkout-page="true">
    <header class="mb-6"><p class="text-xs font-bold text-emerald-700">سفارش آنلاین</p><h1 class="mt-1 text-2xl font-black text-slate-900">تکمیل اطلاعات و تسویه‌حساب</h1><p class="mt-2 text-sm text-slate-600">اطلاعات ارسال و پرداخت امن از طریق درگاه‌های فعال ووکامرس انجام می‌شود.</p></header>
    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/')); ?>" enctype="multipart/form-data" data-checkout-form="true" data-wc-checkout="true">
        <?php do_action('woocommerce_checkout_before_customer_details'); ?>
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
            <div id="customer_details" class="col2-set space-y-6"><div class="col-1 rounded-3xl border border-slate-200 bg-white p-5 md:p-7"><?php do_action('woocommerce_checkout_billing'); ?></div><div class="col-2 rounded-3xl border border-slate-200 bg-white p-5 md:p-7"><?php do_action('woocommerce_checkout_shipping'); ?></div></div>
            <aside class="h-fit rounded-3xl border border-slate-200 bg-white p-5 md:p-7"><h2 class="mb-4 text-lg font-black text-slate-900">سفارش و روش پرداخت</h2><div id="order_review" class="woocommerce-checkout-review-order" data-checkout-order-review="true"><?php do_action('woocommerce_checkout_order_review'); ?></div></aside>
        </div>
        <?php do_action('woocommerce_checkout_after_customer_details'); wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
        <div data-checkout-receipt="true" class="hidden" aria-live="polite"><h2>رسید سفارش</h2><p>مشتری: <span data-receipt-customer="true"></span></p><p>تلفن: <span data-receipt-phone="true"></span></p><p>روش پرداخت: <span data-receipt-method="true"></span></p><p>شماره پیگیری: <span data-receipt-code="true"></span></p></div>
    </form>
</section>
