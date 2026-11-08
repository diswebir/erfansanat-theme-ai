<?php
/**
 * سکشن معرفی درباره ما — برش بایت‌دقیق میرور about.html (تسک ۷.۷)
 * معرفی کارخانه — کارت شناور، بج، دو پاراگراف و نوار آمار
 * رندر: SectionManager → رپر template-parts/sections/ → این فایل
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

$esabi_7 = <<<'ESABI7'
<section class="mx-auto max-w-7xl px-5 py-16 lg:px-8 lg:py-20"><div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16"><div custom="0"><div class="relative"><div class="relative overflow-hidden rounded-[1.75rem] border border-emerald-100 shadow-xl"><img src="images/factory.jpg" alt="نمای بیرونی کارخانه عرفان صنعت اصفهان" class="h-80 w-full object-cover lg:h-[26rem]"/><div class="absolute left-5 top-5 px-3.5 py-2"><img src="images/ese-logo.svg" alt="ESE ERFAN" class="h-10 w-28 object-contain"/></div></div><div class="absolute -bottom-6 right-6 flex items-center gap-3 rounded-2xl border border-emerald-100 bg-white px-5 py-4 shadow-lg"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-factory h-8 w-8 text-emerald-600" aria-hidden="true"><path d="M12 16h.01"></path><path d="M16 16h.01"></path><path d="M3 19a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.5a.5.5 0 0 0-.769-.422l-4.462 2.844A.5.5 0 0 1 15 10.5v-2a.5.5 0 0 0-.769-.422L9.77 10.922A.5.5 0 0 1 9 10.5V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"></path><path d="M8 16h.01"></path></svg><span class="flex flex-col leading-tight"><span class="text-[14px] font-extrabold text-slate-900">{ES_AI_FCARD_V}</span><span class="text-[11.5px] text-slate-500">{ES_AI_FCARD_L}</span></span></div></div></div><div class="flex flex-col items-start gap-6"><div custom="0"><span class="inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[13px] font-bold text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>{ES_AI_BADGE}</span></div><div custom="0.08"><h2 class="text-2xl font-extrabold leading-[1.4] text-slate-900 lg:text-3xl">{ES_AI_H2_PRE}<span class="text-emerald-700">{ES_AI_H2_HL}</span></h2></div><div custom="0.14"><p class="text-[15px] leading-9 text-slate-600 lg:leading-10"><strong class="text-slate-900">{ES_AI_LEAD}</strong>{ES_AI_P1}</p></div><div custom="0.2"><p class="text-[15px] leading-9 text-slate-600">{ES_AI_P2}</p></div></div></div><div class="mt-20 grid grid-cols-2 gap-5 lg:grid-cols-4"><div custom="0.05"><div class="card-light flex flex-col items-center gap-2 rounded-3xl p-7 text-center"><span class="text-4xl font-black text-emerald-700">{ES_AI_ST1_V}</span><span class="text-[13.5px] font-semibold text-slate-600">{ES_AI_ST1_L}</span></div></div><div custom="0.12000000000000001"><div class="card-light flex flex-col items-center gap-2 rounded-3xl p-7 text-center"><span class="text-4xl font-black text-emerald-700">{ES_AI_ST2_V}</span><span class="text-[13.5px] font-semibold text-slate-600">{ES_AI_ST2_L}</span></div></div><div custom="0.19"><div class="card-light flex flex-col items-center gap-2 rounded-3xl p-7 text-center"><span class="text-4xl font-black text-emerald-700">{ES_AI_ST3_V}</span><span class="text-[13.5px] font-semibold text-slate-600">{ES_AI_ST3_L}</span></div></div><div custom="0.26"><div class="card-light flex flex-col items-center gap-2 rounded-3xl p-7 text-center"><span class="text-4xl font-black text-emerald-700">{ES_AI_ST4_V}</span><span class="text-[13.5px] font-semibold text-slate-600">{ES_AI_ST4_L}</span></div></div></div></section>
ESABI7;

echo strtr($esabi_7, [
    '{ES_AI_FCARD_V}' => (string) ($es_f['fcard_v'] ?? ''),
    '{ES_AI_FCARD_L}' => (string) ($es_f['fcard_l'] ?? ''),
    '{ES_AI_BADGE}'   => (string) ($es_f['badge'] ?? ''),
    '{ES_AI_H2_PRE}'  => (string) ($es_f['h2_pre'] ?? ''),
    '{ES_AI_H2_HL}'   => (string) ($es_f['h2_hl'] ?? ''),
    '{ES_AI_LEAD}'    => (string) ($es_f['lead'] ?? ''),
    '{ES_AI_P1}'      => (string) ($es_f['p1'] ?? ''),
    '{ES_AI_P2}'      => (string) ($es_f['p2'] ?? ''),
    '{ES_AI_ST1_V}'   => (string) ($es_f['st1_v'] ?? ''),
    '{ES_AI_ST1_L}'   => (string) ($es_f['st1_l'] ?? ''),
    '{ES_AI_ST2_V}'   => (string) ($es_f['st2_v'] ?? ''),
    '{ES_AI_ST2_L}'   => (string) ($es_f['st2_l'] ?? ''),
    '{ES_AI_ST3_V}'   => (string) ($es_f['st3_v'] ?? ''),
    '{ES_AI_ST3_L}'   => (string) ($es_f['st3_l'] ?? ''),
    '{ES_AI_ST4_V}'   => (string) ($es_f['st4_v'] ?? ''),
    '{ES_AI_ST4_L}'   => (string) ($es_f['st4_l'] ?? ''),
]);
