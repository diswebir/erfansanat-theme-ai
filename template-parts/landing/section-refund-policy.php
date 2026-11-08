<?php
/**
 * سکشن شرایط گارانتی — برش بایت‌دقیق میرور refund_returns.html (تسک ۷.۸)
 * کارت‌ها + پوشش/شمول گارانتی + مراحل پشتیبانی (همه Repeater)
 * رندر: SectionManager → رپر template-parts/sections/ → این فایل
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array $args {section_id: string, fields: array<string, mixed>, post_id: int} */

$es_f = $args['fields'] ?? [];

$ESRPC = <<<'ESRP7C'
<div custom="{ES_RP_CC}"><div class="card-light flex h-full flex-col rounded-[1.75rem] border border-emerald-100 bg-white p-6"><span class="mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-emerald-700 text-white shadow-sm"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-6 w-6" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><h2 class="text-[17px] font-black text-slate-900">{ES_RP_CT}</h2><p class="mt-3 text-[13.5px] leading-7 text-slate-600">{ES_RP_CD}</p></div></div>
ESRP7C;

$ESRPL = <<<'ESRP7L'
<li class="flex items-start gap-3 text-[13.5px] font-medium leading-7 text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check mt-1 h-4.5 w-4.5 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg><span>{ES_RP_CI}</span></li>
ESRP7L;

$ESRPX = <<<'ESRP7X'
<li class="flex items-start gap-3 text-[13.5px] font-medium leading-7 text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert mt-1 h-4.5 w-4.5 shrink-0 text-amber-600" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg><span>{ES_RP_XI}</span></li>
ESRP7X;

$ESRPS = <<<'ESRP7S'
<div class="rounded-2xl border border-slate-200/90 bg-white p-6"><div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-700 text-[15px] font-black text-white">{ES_RP_SN}</div><h3 class="text-[15.5px] font-black text-slate-900">{ES_RP_ST}</h3><p class="mt-2 text-[13px] leading-7 text-slate-600">{ES_RP_SD}</p></div>
ESRP7S;

$esrp_7 = <<<'ESRP7'
<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8 lg:py-20"><div class="grid gap-6 md:grid-cols-3">{ES_RP_CARDS}</div><div class="mt-12 grid gap-8 lg:grid-cols-2"><div custom="0.08"><div class="h-full rounded-[2rem] border border-emerald-200/90 bg-white p-7 shadow-xs sm:p-8"><div class="mb-5 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-100 text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-5.5 w-5.5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg></span><div><h2 class="text-lg font-black text-slate-900">{ES_RP_CT}</h2><p class="text-[12px] font-semibold text-emerald-700">{ES_RP_CS}</p></div></div><ul class="space-y-4">{ES_RP_CL}</ul></div></div><div custom="0.12"><div class="h-full rounded-[2rem] border border-amber-200/90 bg-white p-7 shadow-xs sm:p-8"><div class="mb-5 flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-100 text-amber-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert h-5.5 w-5.5" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg></span><div><h2 class="text-lg font-black text-slate-900">{ES_RP_XT}</h2><p class="text-[12px] font-semibold text-amber-800">{ES_RP_XS}</p></div></div><ul class="space-y-4">{ES_RP_XL}</ul></div></div></div><div class="mt-12" custom="0.15"><div class="rounded-[2rem] border border-slate-200 bg-slate-50/80 p-7 sm:p-10"><div class="mb-8 flex flex-wrap items-center justify-between gap-4"><div><span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-3 py-1 text-[11.5px] font-extrabold text-emerald-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wrench h-3.5 w-3.5" aria-hidden="true"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"></path></svg>{ES_RP_PB}</span><h2 class="mt-2 text-xl font-black text-slate-950 sm:text-2xl">{ES_RP_PT}</h2></div><a href="{ES_RP_PU}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-800 px-5 py-3 text-[13px] font-black text-white transition-colors hover:bg-emerald-900 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span>{ES_RP_PH}</span></a></div><div class="grid gap-6 md:grid-cols-3">{ES_RP_SG}</div><div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 pt-6 text-[13px] font-bold text-slate-600"><span>{ES_RP_ADD}</span><a href="{ES_RP_CU}" class="text-emerald-700 underline-offset-4 hover:underline">{ES_RP_CTT}</a></div></div></div></section>
ESRP7;

// کارت‌های بالا (custom دوره‌ای ۰/۰.۰۶/۰.۱۲)
$es_cards_out = '';
foreach (array_values(is_array($es_f['cards'] ?? null) ? $es_f['cards'] : []) as $es_i => $es_row) {
    $es_cards_out .= strtr($ESRPC, [
        '{ES_RP_CC}' => ['0', '0.06', '0.12'][$es_i % 3],
        '{ES_RP_CT}' => (string) ($es_row['title'] ?? ''),
        '{ES_RP_CD}' => (string) ($es_row['desc'] ?? ''),
    ]);
}

// تحت پوشش / خارج از شمول
$es_cov_out = '';
foreach (array_values(is_array($es_f['covered'] ?? null) ? $es_f['covered'] : []) as $es_row) {
    $es_cov_out .= strtr($ESRPL, ['{ES_RP_CI}' => (string) ($es_row['item'] ?? '')]);
}

$es_exc_out = '';
foreach (array_values(is_array($es_f['excluded'] ?? null) ? $es_f['excluded'] : []) as $es_row) {
    $es_exc_out .= strtr($ESRPX, ['{ES_RP_XI}' => (string) ($es_row['item'] ?? '')]);
}

// گام‌ها (شماره فارسی از ایندکس)
$es_steps_out = '';
foreach (array_values(is_array($es_f['steps'] ?? null) ? $es_f['steps'] : []) as $es_i => $es_row) {
    $es_steps_out .= strtr($ESRPS, [
        '{ES_RP_SN}' => es_fa_num($es_i + 1),
        '{ES_RP_ST}' => (string) ($es_row['title'] ?? ''),
        '{ES_RP_SD}' => (string) ($es_row['desc'] ?? ''),
    ]);
}

echo strtr($esrp_7, [
    '{ES_RP_CARDS}' => $es_cards_out,
    '{ES_RP_CL}' => $es_cov_out,
    '{ES_RP_XL}' => $es_exc_out,
    '{ES_RP_SG}' => $es_steps_out,
    '{ES_RP_CT}' => (string) ($es_f['c_title'] ?? ''),
    '{ES_RP_CS}' => (string) ($es_f['c_sub'] ?? ''),
    '{ES_RP_XT}' => (string) ($es_f['x_title'] ?? ''),
    '{ES_RP_XS}' => (string) ($es_f['x_sub'] ?? ''),
    '{ES_RP_PB}' => (string) ($es_f['p_badge'] ?? ''),
    '{ES_RP_PT}' => (string) ($es_f['p_title'] ?? ''),
    '{ES_RP_PH}' => (string) ($es_f['phone_text'] ?? ''),
    '{ES_RP_PU}' => es_link_url((string) ($es_f['phone_url'] ?? '')),
    '{ES_RP_ADD}' => (string) ($es_f['address'] ?? ''),
    '{ES_RP_CTT}' => (string) ($es_f['cta_text'] ?? ''),
    '{ES_RP_CU}' => es_link_url((string) ($es_f['cta_url'] ?? '')),
]);
