<?php
/** Moderated product-feedback request stored in the private admin inbox. */
$productId = isset($args['product_id']) ? absint($args['product_id']) : 0;
$productTitle = $productId ? get_the_title($productId) : '';
$submissionUrl = admin_url('admin-ajax.php');
if (!$productId) {
    return;
}
?>
<section id="client-feedback" class="mx-auto max-w-6xl px-5 py-10 lg:px-8" aria-labelledby="client-feedback-title">
    <div class="rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm sm:p-8">
        <h2 id="client-feedback-title" class="text-xl font-black text-slate-900 sm:text-2xl"><?php echo esc_html__('ثبت بازخورد درباره محصول', 'erfansanat-theme'); ?></h2>
        <p class="mt-2 text-sm leading-7 text-slate-600"><?php echo esc_html(sprintf(__('نظر شما دربارهٔ «%s» برای بررسی به صندوق بازخورد مشتریان ارسال می‌شود.', 'erfansanat-theme'), $productTitle)); ?></p>
        <form action="<?php echo esc_url($submissionUrl); ?>" method="post" class="mt-6 grid gap-4 sm:grid-cols-2" data-ese-submission-form="true" data-form-type="client_feedback">
            <input type="hidden" name="action" value="ese_submit_form" />
            <input type="hidden" name="form_type" value="client_feedback" />
            <input type="hidden" name="form_nonce" value="<?php echo esc_attr(wp_create_nonce(\ErfanSanat\Theme\Forms\Submission::NONCE_ACTION)); ?>" />
            <input type="hidden" name="product_id" value="<?php echo esc_attr((string) $productId); ?>" />
            <div class="absolute -left-[10000px] h-px w-px overflow-hidden" aria-hidden="true"><label for="ese-feedback-website"><?php echo esc_html__('وب‌سایت', 'erfansanat-theme'); ?></label><input id="ese-feedback-website" type="text" name="website" value="" tabindex="-1" autocomplete="off" /></div>
            <div><label for="ese-feedback-name" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('نام و نام خانوادگی *', 'erfansanat-theme'); ?></label><input id="ese-feedback-name" name="name" type="text" required maxlength="100" autocomplete="name" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /></div>
            <div><label for="ese-feedback-organization" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('سازمان یا شرکت (اختیاری)', 'erfansanat-theme'); ?></label><input id="ese-feedback-organization" name="organization" type="text" maxlength="120" autocomplete="organization" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /></div>
            <div><label for="ese-feedback-rating" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('امتیاز شما *', 'erfansanat-theme'); ?></label><select id="ese-feedback-rating" name="rating" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none"><option value="5"><?php echo esc_html__('۵ از ۵ — عالی', 'erfansanat-theme'); ?></option><option value="4"><?php echo esc_html__('۴ از ۵ — خیلی خوب', 'erfansanat-theme'); ?></option><option value="3"><?php echo esc_html__('۳ از ۵ — خوب', 'erfansanat-theme'); ?></option><option value="2"><?php echo esc_html__('۲ از ۵ — متوسط', 'erfansanat-theme'); ?></option><option value="1"><?php echo esc_html__('۱ از ۵ — نیازمند بهبود', 'erfansanat-theme'); ?></option></select></div>
            <div><label for="ese-feedback-email" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('ایمیل (اختیاری)', 'erfansanat-theme'); ?></label><input id="ese-feedback-email" name="email" type="email" maxlength="254" autocomplete="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none" /></div>
            <div class="sm:col-span-2"><label for="ese-feedback-message" class="mb-1.5 block text-xs font-bold text-slate-700"><?php echo esc_html__('نظر شما *', 'erfansanat-theme'); ?></label><textarea id="ese-feedback-message" name="message" required minlength="8" maxlength="2000" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-7 focus:border-emerald-500 focus:bg-white focus:outline-none"></textarea></div>
            <div class="sm:col-span-2"><button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-emerald-800 px-6 py-3 text-sm font-black text-white transition hover:bg-emerald-900 disabled:cursor-wait disabled:opacity-60"><?php echo esc_html__('ارسال بازخورد برای بررسی', 'erfansanat-theme'); ?></button></div>
        </form>
        <div class="mt-5 hidden rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm font-semibold text-emerald-900" role="status" aria-live="polite" data-ese-form-success="true"><?php echo esc_html__('بازخورد شما برای بررسی ثبت شد. از همراهی‌تان سپاسگزاریم.', 'erfansanat-theme'); ?><button type="button" class="mr-3 underline" data-ese-form-reset="true"><?php echo esc_html__('ارسال بازخورد دیگر', 'erfansanat-theme'); ?></button></div>
        <p class="mt-4 hidden rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert" data-ese-form-error="true"></p>
    </div>
</section>
