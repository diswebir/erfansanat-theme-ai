<?php
/**
 * سکشن دانلود کاتالوگ جامع (catalog) — برش بایت‌دقیق میرور + فیلدهای SectionManager
 * فرگمنت با temp/gen_cat_services5.py از refs/catalog.html برش خورده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

/** فیلتر es_catalog_args — توسعه‌پذیری سکشن کاتالوگ */
$es_args = apply_filters('es_catalog_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'catalog')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}

$ES_CATALOG = <<<'ESC7'
<section aria-label="دانلود کاتالوگ جامع محصولات عرفان صنعت" class="mx-auto max-w-[1300px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8"><div class="relative overflow-hidden rounded-3xl border-0 bg-gradient-to-l from-[#064e3b] via-[#065f46] to-[#047857] text-white shadow-[0_20px_50px_-22px_rgba(4,120,87,0.32)]"><img src="{ES_BG}" alt="{ES_BG_ALT}" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-15"/><div class="pointer-events-none absolute inset-0 bg-gradient-to-l from-[#064e3b]/95 via-[#065f46]/90 to-[#047857]/85"></div><div class="relative z-10 grid items-center gap-6 px-6 py-8 sm:px-10 sm:py-10 lg:grid-cols-12 lg:px-12"><div class="text-right lg:col-span-8"><div class="inline-flex items-center gap-2 text-xs font-extrabold text-emerald-200"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-4 w-4 text-emerald-300" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg><span>{ES_KICKER}</span></div><h3 class="mt-2.5 text-xl font-black text-white sm:text-2xl">{ES_TITLE}</h3><p class="mt-2.5 max-w-2xl text-right text-xs leading-7 font-medium text-emerald-50/90 sm:text-[13.5px] sm:leading-7">{ES_DESC}</p><div class="mt-6 flex flex-wrap items-center gap-4"><a href="{ES_URL}" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-xs font-black text-emerald-900 transition-colors hover:bg-emerald-50 sm:text-sm"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M12 15V3"></path><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5"></path></svg><span>{ES_BTN}</span></a><span class="text-xs font-bold text-emerald-100 tabular-nums">{ES_NOTE}</span></div></div><div class="flex justify-center lg:col-span-4 lg:justify-end"><a href="{ES_URL}" class="group relative block h-44 w-64 overflow-hidden rounded-2xl bg-slate-900 p-5 shadow-xl transition-transform duration-300 hover:-translate-y-1"><img src="{ES_COVER}" alt="{ES_COVER_ALT}" class="absolute inset-0 h-full w-full object-cover opacity-55 transition-transform duration-500 group-hover:scale-105"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/55 to-transparent"></div><div class="relative z-10 flex h-full flex-col justify-between text-right"><div class="flex items-center justify-between"><span class="rounded-lg bg-emerald-500 px-2.5 py-0.5 text-[10px] font-black text-slate-950 tabular-nums">{ES_VERSION}</span><span class="font-mono text-xs font-black text-white/80">ESE</span></div><div><div class="text-[11px] font-bold text-emerald-300">{ES_COVER_KICKER}</div><div class="mt-0.5 flex items-center justify-between text-sm font-black text-white"><span>{ES_COVER_TITLE}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 text-emerald-300" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></div></div></div></a></div></div></div></section>
ESC7;


// آدرس دکمه: فایل PDF در صورت تنظیم؛ وگرنه مسیر فروشگاه
$es_url = (string) ($es_f['file'] ?? '');
$es_url = $es_url !== '' ? es_media_url($es_url) : es_link_url((string) ($es_f['button_url'] ?? ''));

echo strtr($ES_CATALOG, [
    '{ES_KICKER}'      => (string) ($es_f['kicker'] ?? ''),
    '{ES_TITLE}'       => (string) ($es_f['title'] ?? ''),
    '{ES_DESC}'        => (string) ($es_f['desc'] ?? ''),
    '{ES_URL}'         => $es_url,
    '{ES_BTN}'         => (string) ($es_f['button_text'] ?? ''),
    '{ES_NOTE}'        => (string) ($es_f['note'] ?? ''),
    '{ES_COVER}'       => es_media_url((string) ($es_f['cover'] ?? '')),
    '{ES_COVER_ALT}'   => (string) ($es_f['cover_alt'] ?? ''),
    '{ES_BG}'          => es_media_url((string) ($es_f['bg_image'] ?? 'assets/images/light-tunnel.jpg')),
    '{ES_BG_ALT}'      => (string) ($es_f['bg_alt'] ?? 'کاتالوگ جامع محصولات عرفان صنعت'),
    '{ES_VERSION}'     => (string) ($es_f['version'] ?? ''),
    '{ES_COVER_KICKER}' => (string) ($es_f['cover_kicker'] ?? ''),
    '{ES_COVER_TITLE}' => (string) ($es_f['cover_title'] ?? ''),
]);
