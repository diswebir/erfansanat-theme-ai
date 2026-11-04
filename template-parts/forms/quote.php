<?php
/** Product quote / consultation request. */
$productId = isset($args['product_id']) ? absint($args['product_id']) : 0;
$productTitle = $productId ? get_the_title($productId) : '';
$submissionUrl = admin_url('admin-ajax.php');
?>
<section id="product-quote" class="mx-auto max-w-6xl px-5 py-10 lg:px-8" aria-labelledby="product-quote-title">
    <div class="rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm sm:p-8">
        <h2 id="product-quote-title" class="text-xl font-black text-slate-900 sm:text-2xl"><?php echo esc_html__('استعلام قیمت و مشاوره خرید', 'erfansanat-theme'); ?></h2>
        <p class="mt-2 text-sm leading-7 text-slate-600"><?php echo esc_html__('مشخصات تماس را ثبت کنید تا کارشناس فروش درباره قیمت و شرایط سفارش با شما هماهنگ کند.', 'erfansanat-theme'); ?></p>
        <?php if ($productTitle) : ?><p class="mt-3 inline-flex rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-900"><?php echo esc_html(sprintf(__('محصول موردنظر: %s', 'erfansanat-theme'), $productTitle)); ?></p><?php endif; ?>
        <form action="<?php echo esc_url($submissionUrl); ?>" method="post" class="mt-6 grid gap-4 sm:grid-cols-2" data-ese-submission-form="true" data-form-type="quote">
            <input type="hidden" name="action" value="ese_submit_form" />
            <input type="hidden" name="form_type" value="quote" />
            <input type="hidden" name="form_nonce" value="<?php echo esc_attr(wp_create_nonce(\ErfanSanat\Theme\Forms\Submission::NONCE_ACTION)); ?>" />
            <?php if ($productId) : ?><input type="hidden" name="product_id" value="<?php echo esc_attr((string) $productId); ?>" /><?php endif; ?>
            <div class="absolute -left-[10000px] h-px w-px overflow-hidden" aria-hidden="true"><label for="ese-quote-website"><?php echo esc_html__('وب‌سایت', 'erfansanat-theme'); ?></label><input id="ese-quote-website" type="text" name="website" value="" tabindex="-1" autocomplete="off" /></div>
            <div><label for="ese-quote-name" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('نام و نام خانوادگی *', 'erfansanat-theme'); ?></label><input id="ese-quote-name" name="name" type="text" required maxlength="100" autocomplete="name" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /></div>
            <div><label for="ese-quote-phone" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('شماره تماس *', 'erfansanat-theme'); ?></label><input id="ese-quote-phone" name="phone" type="tel" required maxlength="32" autocomplete="tel" inputmode="tel" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /></div>
            <div><label for="ese-quote-email" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('ایمیل (اختیاری)', 'erfansanat-theme'); ?></label><input id="ese-quote-email" name="email" type="email" maxlength="254" autocomplete="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /></div>
            <div><label for="ese-quote-organization" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('سازمان یا شرکت (اختیاری)', 'erfansanat-theme'); ?></label><input id="ese-quote-organization" name="organization" type="text" maxlength="120" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /></div>
            <div><label for="ese-quote-city" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('شهر محل اجرا (اختیاری)', 'erfansanat-theme'); ?></label><input id="ese-quote-city" name="city" type="text" maxlength="100" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /></div>
            <?php if (!$productId) : ?><div><label for="ese-quote-product" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('محصول یا پروژهٔ موردنظر', 'erfansanat-theme'); ?></label><input id="ese-quote-product" name="product" type="text" maxlength="160" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /></div><?php endif; ?>
            <div class="sm:col-span-2"><label for="ese-quote-message" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('جزئیات درخواست *', 'erfansanat-theme'); ?></label><textarea id="ese-quote-message" name="message" required minlength="8" maxlength="4000" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-7 focus:border-emerald-500 focus:bg-white focus:outline-none"></textarea></div>
            <div class="sm:col-span-2"><button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-emerald-800 px-6 py-3 text-sm font-black text-white transition hover:bg-emerald-900 disabled:cursor-wait disabled:opacity-60"><?php echo esc_html__('درخواست پیش‌فاکتور', 'erfansanat-theme'); ?></button></div>
        </form>
        <div class="mt-5 hidden rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm font-semibold text-emerald-900" role="status" aria-live="polite" data-ese-form-success="true"><?php echo esc_html__('درخواست شما ثبت شد؛ کارشناسان فروش با شما تماس می‌گیرند.', 'erfansanat-theme'); ?><button type="button" class="mr-3 underline" data-ese-form-reset="true"><?php echo esc_html__('ارسال درخواست جدید', 'erfansanat-theme'); ?></button></div>
        <p class="mt-4 hidden rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert" data-ese-form-error="true"></p>
    </div>
</section>
