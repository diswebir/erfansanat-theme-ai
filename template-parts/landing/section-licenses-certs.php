<?php
/**
 * سکشن گواهینامه‌ها و افتخارات — برش بایت‌دقیق میرور licenses-and-honors.html (تسک ۷.۸)
 * بج‌ها + گرید گواهینامه‌ها (Repeater) + باکس ستاد و دریافت مستندات
 * رندر: SectionManager → رپر template-parts/sections/ → این فایل
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array $args {section_id: string, fields: array<string, mixed>, post_id: int} */

$es_f = $args['fields'] ?? [];

$ESLCB = <<<'ESLC7B'
<div class="flex items-start gap-3.5 rounded-2xl border border-slate-100 bg-slate-50/70 p-4"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-emerald-700 text-white shadow-xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-5 w-5" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg></span><div><div class="text-[14px] font-black text-slate-900">{ES_LC_BT}</div><p class="mt-1 text-[12px] leading-6 text-slate-600">{ES_LC_BD}</p></div></div>
ESLC7B;

$ESLCC = <<<'ESLC7C'
<div custom="{ES_LC_CC}"><article class="card-light flex h-full flex-col justify-between rounded-[1.75rem] border border-slate-200/80 bg-white p-6 transition-all hover:border-emerald-300"><div><div class="mb-4 flex items-center justify-between gap-2"><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11.5px] font-extrabold text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg>{ES_LC_CAT}</span><span dir="ltr" class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-600">{ES_LC_CODE}</span></div><h2 class="text-[16.5px] font-black leading-8 text-slate-900">{ES_LC_TITLE}</h2><p class="mt-3 text-[13.5px] leading-7 text-slate-600">{ES_LC_DESC}</p></div><div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4 text-[12px] font-bold text-slate-500"><span class="flex items-center gap-1.5 text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check2 lucide-file-check-2 h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="m3 15 2 2 4-4"></path></svg>{ES_LC_ORG}</span><span class="text-emerald-700">{ES_LC_PERIOD}</span></div></article></div>
ESLC7C;

$ESLCS = <<<'ESLC7S'
<div class="flex items-center gap-2 text-[13px] font-bold text-slate-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg><span>{ES_LC_SI}</span></div>
ESLC7S;

$eslc_7 = <<<'ESLC7'
<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8 lg:py-20"><div custom="0"><div class="mb-12 grid gap-4 rounded-[2rem] border border-emerald-100 bg-white p-6 shadow-[0_20px_50px_-28px_rgba(5,150,105,0.12)] sm:grid-cols-2 lg:grid-cols-4 lg:p-8">{ES_LC_BADGES}</div></div><div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">{ES_LC_TILES}</div><div class="mt-14" custom="0.12"><div class="rounded-[2rem] border border-emerald-200/80 bg-gradient-to-br from-emerald-50/90 via-white to-emerald-50/40 p-7 sm:p-10"><div class="grid items-center gap-8 lg:grid-cols-12"><div class="space-y-4 lg:col-span-8"><div class="inline-flex items-center gap-2 rounded-xl bg-emerald-800 px-3.5 py-1.5 text-[12px] font-extrabold text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-4 w-4" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg><span>{ES_LC_SB}</span></div><h2 class="text-xl font-black text-slate-950 sm:text-2xl">{ES_LC_SH}</h2><p class="text-[14.5px] leading-8 text-slate-600">{ES_LC_SD}</p><div class="grid gap-2.5 pt-2 sm:grid-cols-2">{ES_LC_SIS}</div></div><div class="flex flex-col gap-3 rounded-2xl border border-emerald-200 bg-white p-6 shadow-sm lg:col-span-4"><div class="flex items-center gap-2.5 text-[14px] font-black text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-5 w-5 text-emerald-700" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg><span>{ES_LC_DT}</span></div><p class="text-[12.5px] leading-6 text-slate-600">{ES_LC_DD}</p><a href="{ES_LC_PU}" class="mt-2 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-800 px-5 py-3.5 text-[13.5px] font-black text-white transition-colors hover:bg-emerald-900 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span>{ES_LC_PHONE}</span></a><a href="{ES_LC_SHU}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-5 py-3 text-[13px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-900 whitespace-nowrap">{ES_LC_PT}</a></div></div></div></div></section>
ESLC7;

// بج‌های بالا
$es_badges_out = '';
foreach (array_values(is_array($es_f['badges'] ?? null) ? $es_f['badges'] : []) as $es_row) {
    $es_badges_out .= strtr($ESLCB, [
        '{ES_LC_BT}' => (string) ($es_row['title'] ?? ''),
        '{ES_LC_BD}' => (string) ($es_row['desc'] ?? ''),
    ]);
}

// کاشی‌های گواهینامه (custom دوره‌ای ۰.۰۴/۰.۱/۰.۱۶)
$es_certs_out = '';
foreach (array_values(is_array($es_f['certs'] ?? null) ? $es_f['certs'] : []) as $es_i => $es_row) {
    $es_certs_out .= strtr($ESLCC, [
        '{ES_LC_CC}' => ['0.04', '0.1', '0.16'][$es_i % 3],
        '{ES_LC_CAT}' => (string) ($es_row['cat'] ?? ''),
        '{ES_LC_CODE}' => (string) ($es_row['code'] ?? ''),
        '{ES_LC_TITLE}' => (string) ($es_row['title'] ?? ''),
        '{ES_LC_DESC}' => (string) ($es_row['desc'] ?? ''),
        '{ES_LC_ORG}' => (string) ($es_row['org'] ?? ''),
        '{ES_LC_PERIOD}' => (string) ($es_row['period'] ?? ''),
    ]);
}

// فهرست باکس ستاد
$es_sis_out = '';
foreach (array_values(is_array($es_f['s_items'] ?? null) ? $es_f['s_items'] : []) as $es_row) {
    $es_sis_out .= strtr($ESLCS, ['{ES_LC_SI}' => (string) ($es_row['item'] ?? '')]);
}

echo strtr($eslc_7, [
    '{ES_LC_BADGES}' => $es_badges_out,
    '{ES_LC_TILES}' => $es_certs_out,
    '{ES_LC_SIS}' => $es_sis_out,
    '{ES_LC_SB}' => (string) ($es_f['s_badge'] ?? ''),
    '{ES_LC_SH}' => (string) ($es_f['s_h2'] ?? ''),
    '{ES_LC_SD}' => (string) ($es_f['s_desc'] ?? ''),
    '{ES_LC_DT}' => (string) ($es_f['docs_title'] ?? ''),
    '{ES_LC_DD}' => (string) ($es_f['docs_desc'] ?? ''),
    '{ES_LC_PHONE}' => (string) ($es_f['phone'] ?? ''),
    '{ES_LC_PT}' => (string) ($es_f['products_text'] ?? ''),
    '{ES_LC_PU}' => es_link_url((string) ($es_f['phone_url'] ?? '')),
    '{ES_LC_SHU}' => es_link_url((string) ($es_f['products_url'] ?? '')),
]);
