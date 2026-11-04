<?php
/** Native customer feedback form using the theme's existing emerald/slate component style. */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
?>
<section class="mx-auto max-w-[1300px] px-5 pt-10 lg:px-8" aria-labelledby="client-feedback-heading">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <header class="mb-6">
            <p class="text-xs font-bold text-emerald-700">صدای مشتریان</p>
            <h2 id="client-feedback-heading" class="mt-2 text-2xl font-black text-slate-900">بازخورد خود را با ما در میان بگذارید</h2>
            <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">دیدگاه شما درباره تجربه همکاری با عرفان صنعت به بهبود خدمات و پروژه‌های آینده کمک می‌کند.</p>
        </header>
        <form data-client-feedback-form="true" method="post" class="grid gap-4 md:grid-cols-2">
            <label class="block text-sm font-bold text-slate-700">نام و نام خانوادگی<input name="name" type="text" autocomplete="name" required minlength="2" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm" placeholder="نام شما"></label>
            <label class="block text-sm font-bold text-slate-700">سازمان (اختیاری)<input name="organization" type="text" autocomplete="organization" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm" placeholder="نام سازمان یا مجموعه"></label>
            <fieldset class="es-form-full-row">
                <legend class="mb-2 text-sm font-bold text-slate-700">امتیاز شما <span class="text-slate-500">(از ۱ تا ۵)</span></legend>
                <div class="flex flex-wrap gap-2">
                    <?php for ($rating = 5; $rating >= 1; $rating--) : ?><label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700"><input type="radio" name="rating" value="<?php echo esc_attr((string) $rating); ?>" required class="accent-emerald-700"><span><?php echo esc_html(number_format_i18n($rating)); ?> از ۵</span></label><?php endfor; ?>
                </div>
            </fieldset>
            <label class="block text-sm font-bold text-slate-700 es-form-full-row">متن بازخورد<textarea name="message" rows="5" maxlength="3000" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm" placeholder="تجربه خود را درباره همکاری با ما بنویسید"></textarea></label>
            <label class="hidden" aria-hidden="true">وب‌سایت<input type="text" name="website" tabindex="-1" autocomplete="off" value=""></label>
            <p data-client-feedback-status="true" class="text-sm text-slate-600 es-form-full-row" role="status" aria-live="polite"></p>
            <div class="es-form-full-row"><button type="submit" class="inline-flex rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white">ثبت بازخورد</button></div>
        </form>
    </div>
</section>
