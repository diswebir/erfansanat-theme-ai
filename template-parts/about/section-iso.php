<?php
/**
 * سکشن گواهینامه ISO — برش بایت‌دقیق میرور about.html (تسک ۷.۷)
 * گواهینامه ISO 9001:2015 — فهرست ویژگی‌ها و کارت گواهینامه
 * رندر: SectionManager → رپر template-parts/sections/ → این فایل
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

$esabs_7 = <<<'ESABS7'
<section class="border-y border-emerald-100 bg-white py-16 lg:py-20"><div class="mx-auto grid max-w-7xl items-center gap-12 px-5 lg:grid-cols-2 lg:px-8"><div class="flex flex-col items-start gap-6"><div custom="0"><span class="inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[13px] font-bold text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-4 w-4" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg>{ES_AO_KICKER}</span></div><div custom="0.08"><h2 class="text-2xl font-extrabold leading-[1.4] text-slate-900 lg:text-3xl">{ES_AO_H2_PRE}<span class="text-emerald-700">{ES_AO_H2_HL}</span></h2></div><div custom="0.14"><p class="text-[15px] leading-9 text-slate-600">{ES_AO_TEXT}</p></div><div custom="0.2"><ul class="flex flex-col gap-3.5"><li class="flex items-center gap-3 text-[14.5px] font-semibold text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg>{ES_AO_B1}</li><li class="flex items-center gap-3 text-[14.5px] font-semibold text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg>{ES_AO_B2}</li><li class="flex items-center gap-3 text-[14.5px] font-semibold text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg>{ES_AO_B3}</li></ul></div></div><div custom="0.12"><div class="relative mx-auto max-w-md"><div class="absolute -inset-4 rounded-[2rem] bg-emerald-100/60 blur-2xl"></div><div class="card-light relative flex flex-col items-center gap-5 rounded-[2rem] p-10 text-center"><span class="grid h-24 w-24 place-items-center rounded-full border-4 border-emerald-200 bg-emerald-50"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-12 w-12 text-emerald-700" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg></span><span class="text-2xl font-black text-slate-900">{ES_AO_CARD_V}</span><span class="text-[13.5px] leading-7 text-slate-600">{ES_AO_CARD_L}<br/>{ES_AO_CARD_EN}</span><span class="rounded-full bg-emerald-600 px-5 py-2 text-[12px] font-bold text-white">{ES_AO_CARD_BADGE}</span></div></div></div></div></section>
ESABS7;

echo strtr($esabs_7, [
    '{ES_AO_KICKER}'     => (string) ($es_f['kicker'] ?? ''),
    '{ES_AO_H2_PRE}'     => (string) ($es_f['h2_pre'] ?? ''),
    '{ES_AO_H2_HL}'      => (string) ($es_f['h2_hl'] ?? ''),
    '{ES_AO_TEXT}'       => (string) ($es_f['text'] ?? ''),
    '{ES_AO_B1}'         => (string) ($es_f['b1'] ?? ''),
    '{ES_AO_B2}'         => (string) ($es_f['b2'] ?? ''),
    '{ES_AO_B3}'         => (string) ($es_f['b3'] ?? ''),
    '{ES_AO_CARD_V}'     => (string) ($es_f['card_v'] ?? ''),
    '{ES_AO_CARD_L}'     => (string) ($es_f['card_l'] ?? ''),
    '{ES_AO_CARD_EN}'    => (string) ($es_f['card_en'] ?? ''),
    '{ES_AO_CARD_BADGE}' => (string) ($es_f['card_badge'] ?? ''),
]);
