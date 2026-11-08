<?php
/**
 * سکشن ارزش‌ها و نقشه ایران — برش بایت‌دقیق میرور about.html (تسک ۷.۷)
 * اصول بنیادین — چهار کارت ارزش، نقشه ایران و دکمه پروژه‌ها
 * رندر: SectionManager → رپر template-parts/sections/ → این فایل
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

$esabv_7 = <<<'ESABV7'
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-20"><div custom="0"><h2 class="mb-12 text-center text-2xl font-extrabold text-slate-900 lg:text-3xl">{ES_AV_H2_PRE}<span class="text-emerald-700">{ES_AV_H2_HL}</span></h2></div><div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4"><div custom="0.05"><div class="card-light flex h-full flex-col gap-4 rounded-3xl p-7"><span class="grid h-12 w-12 place-items-center rounded-2xl border border-emerald-200 bg-emerald-50"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-6 w-6 text-emerald-700" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg></span><h3 class="text-lg font-extrabold text-slate-900">{ES_AV_C1_T}</h3><p class="text-[13.5px] leading-7 text-slate-600">{ES_AV_C1_D}</p></div></div><div custom="0.12000000000000001"><div class="card-light flex h-full flex-col gap-4 rounded-3xl p-7"><span class="grid h-12 w-12 place-items-center rounded-2xl border border-emerald-200 bg-emerald-50"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-target h-6 w-6 text-emerald-700" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg></span><h3 class="text-lg font-extrabold text-slate-900">{ES_AV_C2_T}</h3><p class="text-[13.5px] leading-7 text-slate-600">{ES_AV_C2_D}</p></div></div><div custom="0.19"><div class="card-light flex h-full flex-col gap-4 rounded-3xl p-7"><span class="grid h-12 w-12 place-items-center rounded-2xl border border-emerald-200 bg-emerald-50"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-leaf h-6 w-6 text-emerald-700" aria-hidden="true"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path></svg></span><h3 class="text-lg font-extrabold text-slate-900">{ES_AV_C3_T}</h3><p class="text-[13.5px] leading-7 text-slate-600">{ES_AV_C3_D}</p></div></div><div custom="0.26"><div class="card-light flex h-full flex-col gap-4 rounded-3xl p-7"><span class="grid h-12 w-12 place-items-center rounded-2xl border border-emerald-200 bg-emerald-50"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart-handshake h-6 w-6 text-emerald-700" aria-hidden="true"><path d="M19.414 14.414C21 12.828 22 11.5 22 9.5a5.5 5.5 0 0 0-9.591-3.676.6.6 0 0 1-.818.001A5.5 5.5 0 0 0 2 9.5c0 2.3 1.5 4 3 5.5l5.535 5.362a2 2 0 0 0 2.879.052 2.12 2.12 0 0 0-.004-3 2.124 2.124 0 1 0 3-3 2.124 2.124 0 0 0 3.004 0 2 2 0 0 0 0-2.828l-1.881-1.882a2.41 2.41 0 0 0-3.409 0l-1.71 1.71a2 2 0 0 1-2.828 0 2 2 0 0 1 0-2.828l2.823-2.762"></path></svg></span><h3 class="text-lg font-extrabold text-slate-900">{ES_AV_C4_T}</h3><p class="text-[13.5px] leading-7 text-slate-600">{ES_AV_C4_D}</p></div></div></div><div custom="0.15"><div class="mt-16 grid items-center gap-8 rounded-[2rem] border border-emerald-100 bg-emerald-50/60 p-8 lg:grid-cols-[auto_1fr_auto] lg:p-12"><span class="mx-auto grid h-20 w-20 place-items-center rounded-3xl bg-white shadow-md"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-earth h-10 w-10 text-emerald-600" aria-hidden="true"><path d="M21.54 15H17a2 2 0 0 0-2 2v4.54"></path><path d="M7 3.34V5a3 3 0 0 0 3 3a2 2 0 0 1 2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h3.17"></path><path d="M11 21.95V18a2 2 0 0 0-2-2a2 2 0 0 1-2-2v-1a2 2 0 0 0-2-2H2.05"></path><circle cx="12" cy="12" r="10"></circle></svg></span><div><h3 class="text-xl font-extrabold text-slate-900">{ES_AV_MAP_T}</h3><p class="mt-3 max-w-3xl text-[14.5px] leading-8 text-slate-600">{ES_AV_MAP_D}</p></div><a href="{ES_AV_CTA_URL}" class="group flex shrink-0 items-center gap-2.5 self-center rounded-full bg-emerald-600 px-7 py-3.5 text-[14px] font-extrabold text-white transition-all hover:bg-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-round h-4.5 w-4.5" aria-hidden="true"><path d="M18 21a8 8 0 0 0-16 0"></path><circle cx="10" cy="8" r="5"></circle><path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"></path></svg>{ES_AV_CTA}<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div></div></section>
ESABV7;

echo strtr($esabv_7, [
    '{ES_AV_H2_PRE}'  => (string) ($es_f['h2_pre'] ?? ''),
    '{ES_AV_H2_HL}'   => (string) ($es_f['h2_hl'] ?? ''),
    '{ES_AV_C1_T}'    => (string) ($es_f['c1_t'] ?? ''),
    '{ES_AV_C1_D}'    => (string) ($es_f['c1_d'] ?? ''),
    '{ES_AV_C2_T}'    => (string) ($es_f['c2_t'] ?? ''),
    '{ES_AV_C2_D}'    => (string) ($es_f['c2_d'] ?? ''),
    '{ES_AV_C3_T}'    => (string) ($es_f['c3_t'] ?? ''),
    '{ES_AV_C3_D}'    => (string) ($es_f['c3_d'] ?? ''),
    '{ES_AV_C4_T}'    => (string) ($es_f['c4_t'] ?? ''),
    '{ES_AV_C4_D}'    => (string) ($es_f['c4_d'] ?? ''),
    '{ES_AV_MAP_T}'   => (string) ($es_f['map_t'] ?? ''),
    '{ES_AV_MAP_D}'   => (string) ($es_f['map_d'] ?? ''),
    '{ES_AV_CTA_URL}' => es_link_url((string) ($es_f['cta_url'] ?? '')),
    '{ES_AV_CTA}'     => (string) ($es_f['cta'] ?? ''),
]);
