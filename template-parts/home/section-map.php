<?php
/**
 * سکشن نقشه کارخانه (map) — برش بایت‌دقیق میرور + فیلدهای SectionManager
 * بستر نقشه: بدون embed → SVG تزئینی میرور؛ با embed → iframe فقط از فیلد ادمین (هرگز ورودی کاربر).
 * فرگمنت با temp/gen_cm5.py از refs/map.html برش خورده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Admin\OptionsOutput;

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

/** فیلتر es_map_args — توسعه‌پذیری سکشن نقشه */
$es_args = apply_filters('es_map_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'map')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/map.html
 */
$ES_MAP_SVG = <<<'ESM10'
<svg viewBox="0 0 1000 400" aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full object-cover"><rect width="1000" height="400" fill="#eef2f0"></rect><rect x="40" y="30" width="180" height="110" rx="14" fill="#e2e8e5"></rect><rect x="260" y="40" width="210" height="95" rx="14" fill="#dfe7e3"></rect><rect x="520" y="25" width="190" height="120" rx="14" fill="#d5ebe0"></rect><rect x="80" y="210" width="220" height="150" rx="16" fill="#e2e8e5"></rect><rect x="350" y="220" width="240" height="140" rx="16" fill="#d5ebe0"></rect><path d="M0,350 C260,310 520,380 1000,300" stroke="#bae6fd" stroke-width="22" fill="none"></path><path d="M0,165 L1000,165 M320,0 L320,400 M620,0 L560,400 M0,75 L1000,260" stroke="#ffffff" stroke-width="12" fill="none"></path><path d="M0,165 L1000,165 M320,0 L320,400 M620,0 L560,400 M0,75 L1000,260" stroke="#cbd5e1" stroke-width="1.5" stroke-dasharray="8 8" fill="none"></path></svg>
ESM10;

$ES_MAP = <<<'ESM10'
<section aria-label="موقعیت کارخانه روی نقشه و پشتیبانی فنی عرفان صنعت" class="mx-auto max-w-[1300px] px-4 pt-2 pb-16 sm:px-6 sm:pb-20 lg:px-8"><div class="relative overflow-hidden rounded-3xl border border-slate-200/85 bg-[#eef2f0] p-5 sm:p-8">{ES_MAP_BG}<div class="relative z-10 grid items-center gap-6 lg:grid-cols-12"><div class="rounded-2xl border border-emerald-900/20 bg-gradient-to-b from-[#06130d] to-[#0a1e14] p-6 text-white shadow-xl lg:col-span-5"><div class="text-xs font-extrabold text-emerald-300">{ES_KICKER}</div><h3 class="mt-1.5 text-lg font-black text-white">{ES_TITLE}</h3><p class="mt-2.5 text-xs leading-6 font-medium text-slate-300">{ES_ADDRESS}</p><div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-white/10 pt-3.5 text-xs"><span class="font-bold text-slate-300 tabular-nums">{ES_POSTAL}</span><a href="{ES_DIR_URL}" class="inline-flex items-center gap-1.5 font-extrabold text-emerald-300 hover:text-white"><span>{ES_DIR_TEXT}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a></div></div><div class="flex min-h-[160px] items-center justify-center lg:col-span-7"><a href="{ES_PIN_URL}" class="group flex items-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3 shadow-md transition-transform hover:-translate-y-0.5"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-emerald-600 text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-5 w-5" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg></span><div class="text-right"><div class="text-xs font-black text-slate-900">{ES_PIN_TITLE}</div><div class="mt-0.5 text-[11px] font-bold text-emerald-700">{ES_PIN_SUB}</div></div></a></div></div></div></section>
ESM10;


$es_c = OptionsOutput::contact_args();
$es_address = trim((string) ($es_f['address'] ?? ''));
$es_address = $es_address !== '' ? $es_address : (string) $es_c['address'];
$es_postal = trim((string) ($es_f['postal'] ?? ''));
$es_postal = $es_postal !== '' ? $es_postal : 'کدپستی: ' . (string) $es_c['postal_code'];

// مخفی‌سازی: بدون نشانی و عنوان
if ($es_address === '' && (string) ($es_f['title'] ?? '') === '') {
    return;
}

// بستر نقشه: iframe ادمین با wp_kses گسترده (iframe مجاز فقط از آپشن)؛ وگرنه SVG میرور
$es_embed = trim((string) ($es_f['embed'] ?? ''));
if ($es_embed !== '') {
    $es_bg = wp_kses($es_embed, [
        'iframe' => ['src' => true, 'width' => true, 'height' => true, 'frameborder' => true, 'style' => true, 'allow' => true, 'allowfullscreen' => true, 'loading' => true, 'title' => true, 'referrerpolicy' => true],
    ]);
} else {
    $es_bg = $ES_MAP_SVG;
}

echo strtr($ES_MAP, [
    '{ES_MAP_BG}'   => $es_bg,
    '{ES_KICKER}'   => (string) ($es_f['kicker'] ?? ''),
    '{ES_TITLE}'    => (string) ($es_f['title'] ?? ''),
    '{ES_ADDRESS}'  => $es_address,
    '{ES_POSTAL}'   => $es_postal,
    '{ES_DIR_URL}'  => es_link_url((string) ($es_f['directions_url'] ?? '')),
    '{ES_DIR_TEXT}' => (string) ($es_f['directions_text'] ?? ''),
    '{ES_PIN_URL}'  => es_link_url((string) ($es_f['pin_url'] ?? '')),
    '{ES_PIN_TITLE}' => (string) ($es_f['pin_title'] ?? ''),
    '{ES_PIN_SUB}'  => (string) ($es_f['pin_sub'] ?? ''),
]);
