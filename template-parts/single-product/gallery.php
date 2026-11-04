<?php
/**
 * گالری محصول تکی — main/thumbs/lightbox با هوک‌های data-gallery-* میرور (برش بایت‌دقیق)
 * فرگمنت با temp/gen_p62.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Data\ProductGallery;

$es_pid = (int) ($args['pid'] ?? 0);
$es_title = get_the_title($es_pid);
$es_cats = get_the_terms($es_pid, 'product_cat');
$es_cat = is_array($es_cats) && $es_cats !== [] ? (string) ($es_cats[0]->name ?? '') : '';

$es_ids = [];
$es_thumb_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);
if ($es_thumb_id > 0) {
    $es_ids[] = $es_thumb_id;
}
foreach (ProductGallery::get_ids($es_pid) as $es_gid) {
    if (!in_array($es_gid, $es_ids, true)) {
        $es_ids[] = $es_gid;
    }
}
$es_imgs = array_map('es_media_url', $es_ids);
if ($es_imgs === []) {
    $es_imgs = [es_media_url('assets/images/light-tree.jpg')];
}
$es_total = count($es_imgs);
$es_fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

$ES_HERO = <<<'ESP62H'
<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34"><div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div><div class="light-dot-grid absolute inset-0 opacity-45"></div><div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div></div><div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8"><nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="{ES_HOME_URL}" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="{ES_SHOP_URL}" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">فروشگاه محصولات</a></span><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="{ES_CAT_URL}" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">{ES_CAT}</a></span><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">{ES_TITLE}</span></span></nav><div class="pt-6"><div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs"><span class="h-2 w-2 rounded-full bg-emerald-600"></span><span>{ES_CAT}</span></div><h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]">{ES_TITLE} </h1>{ES_EN_HTML}<p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]">{ES_ZIREONVAN}</p></div></div></section>
ESP62H;
$ES_GAL_HEAD = <<<'ESP62A'
<div custom="0"><div data-gallery-root="true" class="space-y-3.5"><div class="group relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-900 shadow-sm"><img src="{ES_IMG_0}" alt="{ES_TITLE}" data-gallery-main-img="true" class="h-[16rem] xs:h-[19rem] sm:h-[24rem] lg:h-[27rem] w-full cursor-zoom-in object-cover transition-transform duration-700"/><div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/55 via-transparent to-transparent"></div><span class="absolute right-3.5 top-3.5 sm:right-4 sm:top-4 rounded-xl border border-white/25 bg-emerald-950/75 px-3 py-1 sm:px-3.5 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-extrabold text-emerald-100 backdrop-blur-md">{ES_CAT}</span><div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 flex items-center justify-between"><div class="flex items-center gap-1.5 sm:gap-2"><button type="button" data-gallery-prev="true" aria-label="تصویر قبلی" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-xl border border-white/25 bg-slate-900/75 text-white backdrop-blur-md transition-colors hover:bg-emerald-700 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-4 w-4 sm:h-4.5 sm:w-4.5" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg></button><button type="button" data-gallery-next="true" aria-label="تصویر بعدی" class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-xl border border-white/25 bg-slate-900/75 text-white backdrop-blur-md transition-colors hover:bg-emerald-700 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-4 w-4 sm:h-4.5 sm:w-4.5" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg></button><span data-gallery-counter="true" class="rounded-xl border border-white/20 bg-slate-900/75 px-2.5 py-1 sm:px-3 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-bold text-white backdrop-blur-md">{ES_COUNTER}</span></div><button type="button" data-gallery-expand="true" class="inline-flex items-center gap-1 rounded-xl border border-white/25 bg-white/90 px-3 py-1 sm:px-3.5 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-extrabold text-slate-900 shadow-sm backdrop-blur-md transition-colors hover:bg-emerald-700 hover:text-white cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-expand h-3.5 w-3.5" aria-hidden="true"><path d="m15 15 6 6"></path><path d="m15 9 6-6"></path><path d="M21 16v5h-5"></path><path d="M21 8V3h-5"></path><path d="M3 16v5h5"></path><path d="m3 21 6-6"></path><path d="M3 8V3h5"></path><path d="M9 9 3 3"></path></svg><span>بزرگ‌نمایی</span></button></div></div><div class="grid grid-cols-4 gap-1.5 sm:grid-cols-5 sm:gap-2.5">
ESP62A;
$ES_THUMB_ACT = <<<'ESP62B'
<button type="button" data-gallery-thumb="{ES_I}" data-gallery-src="{ES_SRC}" class="relative h-16 sm:h-20 overflow-hidden rounded-xl sm:rounded-2xl border-2 transition-all cursor-pointer border-emerald-500 shadow-[0_4px_16px_-4px_rgba(5,150,105,.45)]"><img src="{ES_SRC}" alt="{ES_ALT}" loading="lazy" class="h-full w-full object-cover"/><span data-gallery-thumb-ring="true" class="pointer-events-none absolute inset-0 rounded-lg sm:rounded-xl ring-2 ring-inset ring-emerald-500 block"></span></button>
ESP62B;
$ES_THUMB_IDLE = <<<'ESP62C'
<button type="button" data-gallery-thumb="{ES_I}" data-gallery-src="{ES_SRC}" class="relative h-16 sm:h-20 overflow-hidden rounded-xl sm:rounded-2xl border-2 transition-all cursor-pointer border-transparent opacity-65 hover:opacity-100"><img src="{ES_SRC}" alt="{ES_ALT}" loading="lazy" class="h-full w-full object-cover"/><span data-gallery-thumb-ring="true" class="pointer-events-none absolute inset-0 rounded-lg sm:rounded-xl ring-2 ring-inset ring-emerald-500 hidden"></span></button>
ESP62C;
$ES_GAL_MID = <<<'ESP62D'
</div><div data-gallery-lightbox="true" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/90 p-4 backdrop-blur-md"><button type="button" data-gallery-lightbox-close="true" aria-label="بستن گالری" class="absolute right-5 top-5 grid h-11 w-11 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-white/25"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-5 w-5" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button><button type="button" data-gallery-lightbox-prev="true" aria-label="قبلی" class="absolute right-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-5 w-5" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg></button><button type="button" data-gallery-lightbox-next="true" aria-label="بعدی" class="absolute left-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-5 w-5" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg></button><img src="{ES_IMG_0}" alt="{ES_TITLE}" data-gallery-lightbox-img="true" class="max-h-[84vh] max-w-[90vw] rounded-2xl object-contain shadow-2xl"/><div class="absolute bottom-6 left-1/2 flex -translate-x-1/2 items-center gap-2">
ESP62D;
$ES_DOT_ACT = <<<'ESP62E'
<button type="button" data-gallery-lightbox-dot="{ES_I}" aria-label="{ES_ARIA}" class="{ES_CLS}"></button>
ESP62E;
$ES_DOT_IDLE = <<<'ESP62F'
<button type="button" data-gallery-lightbox-dot="{ES_I}" aria-label="{ES_ARIA}" class="{ES_CLS}"></button>
ESP62F;
$ES_GAL_TAIL = <<<'ESP62G'
</div></div></div></div>
ESP62G;

echo strtr($ES_GAL_HEAD, ['{ES_IMG_0}' => esc_url((string) $es_imgs[0]), '{ES_TITLE}' => esc_html($es_title), '{ES_CAT}' => esc_html($es_cat), '{ES_COUNTER}' => es_fa_num(1) . ' / ' . es_fa_num((string) $es_total)]);
foreach ($es_imgs as $es_i => $es_src) {
    $es_alt = $es_title . ' - تصویر ' . ($es_fa[$es_i + 1] ?? (string) ($es_i + 1));
    if ($es_i === 0) {
        echo strtr($ES_THUMB_ACT, ['{ES_I}' => (string) $es_i, '{ES_SRC}' => esc_url((string) $es_src), '{ES_ALT}' => esc_attr($es_alt)]);
    } else {
        echo strtr($ES_THUMB_IDLE, ['{ES_I}' => (string) $es_i, '{ES_SRC}' => esc_url((string) $es_src), '{ES_ALT}' => esc_attr($es_alt)]);
    }
}
echo strtr($ES_GAL_MID, ['{ES_IMG_0}' => esc_url((string) $es_imgs[0]), '{ES_TITLE}' => esc_html($es_title)]);
foreach ($es_imgs as $es_i => $es_src) {
    $es_dot_cls = $es_i === 0 ? 'h-2 rounded-full transition-all w-7 bg-emerald-400' : 'h-2 rounded-full transition-all w-2 bg-white/40 hover:bg-white/70';
    $es_aria = 'تصویر ' . ($es_i + 1);
    $es_tpl = $es_i === 0 ? $ES_DOT_ACT : $ES_DOT_IDLE;
    echo strtr($es_tpl, ['{ES_I}' => (string) $es_i, '{ES_ARIA}' => esc_attr($es_aria), '{ES_CLS}' => $es_dot_cls]);
}
echo $ES_GAL_TAIL;
