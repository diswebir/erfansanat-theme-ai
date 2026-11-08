<?php
/**
 * سکشن سوالات متداول — برش بایت‌دقیق میرور faq.html (تسک ۷.۸)
 * آکاردئون دسته‌بندی‌شده (Repeater با data-faq-*) + سایدبار
 * رندر: SectionManager → رپر template-parts/sections/ → این فایل
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array $args {section_id: string, fields: array<string, mixed>, post_id: int} */

$es_f = $args['fields'] ?? [];

$ESFLC = <<<'ESFL7C'
<div class="flex items-center gap-2.5 rounded-xl bg-slate-50 px-3.5 py-2.5 text-[12.5px] font-bold text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layers h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"></path><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"></path><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"></path></svg><span>{ES_FL_CAT_R}</span></div>
ESFL7C;

$ESFLI = <<<'ESFL7I'
<div custom="{ES_FL_C}"><div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white transition-all hover:border-emerald-300"><button type="button" data-faq-toggle="{ES_FL_I}" class="flex w-full items-center justify-between gap-4 px-6 py-5 text-right cursor-pointer"><div class="space-y-1"><span class="inline-block rounded-md bg-emerald-50 px-2.5 py-0.5 text-[11px] font-extrabold text-emerald-800">{ES_FL_CAT}</span><h3 class="text-[15px] font-black leading-7 text-slate-900 sm:text-[16px]">{ES_FL_Q}</h3></div><span data-faq-icon="{ES_FL_I}" class="grid h-9 w-9 shrink-0 place-items-center rounded-xl transition-transform duration-300 {ES_FL_SI}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-4.5 w-4.5" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg></span></button><div data-faq-body="{ES_FL_I}" class="{ES_FL_SB} border-t border-slate-100 bg-slate-50/50 px-6 py-5 text-[14px] leading-8 text-slate-600">{ES_FL_A}</div></div></div>
ESFL7I;

$esfl_7 = <<<'ESFL7'
<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8 lg:py-20"><div class="grid items-start gap-10 lg:grid-cols-12"><aside class="lg:col-span-4"><div class="sticky top-28 space-y-5 rounded-[1.75rem] border border-slate-200/90 bg-white p-6 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.10)]"><div class="flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-question-mark h-5.5 w-5.5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><path d="M12 17h.01"></path></svg></span><div><h2 class="text-[15.5px] font-black text-slate-900">{ES_FL_AT}</h2><p class="text-[12px] font-semibold text-slate-500">{ES_FL_AC}</p></div></div><div class="space-y-2 border-t border-slate-100 pt-4">{ES_FL_CATS}</div><div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5"><div class="text-[13.5px] font-black text-emerald-950">{ES_FL_HT}</div><p class="mt-1.5 text-[12.5px] leading-6 text-slate-600">{ES_FL_HD}</p><a href="{ES_FL_PU}" class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-800 px-4 py-3 text-[13px] font-black text-white transition-colors hover:bg-emerald-900 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span>{ES_FL_PHONE}</span></a><a href="{ES_FL_RU}" class="mt-2.5 flex w-full items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-[12px] font-bold text-emerald-800 transition-colors hover:bg-emerald-100 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg><span>{ES_FL_RT}</span></a></div></div></aside><div class="lg:col-span-8" data-faq-list="true"><div class="space-y-4">{ES_FL_ITEMS}</div></div></div></section>
ESFL7;

// دسته‌بندی‌های سایدبار
$es_cats_out = '';
foreach (array_values(is_array($es_f['cats'] ?? null) ? $es_f['cats'] : []) as $es_row) {
    $es_cats_out .= strtr($ESFLC, ['{ES_FL_CAT_R}' => (string) ($es_row['title'] ?? '')]);
}

// آیتم‌های آکاردئون (custom دوره‌ای + ایندکس data-faq-*)
$es_items_out = '';
$es_cyc = ['0', '0.03', '0.06', '0.09', '0.12', '0.15'];
foreach (array_values(is_array($es_f['items'] ?? null) ? $es_f['items'] : []) as $es_i => $es_row) {
    $es_open = (int) ($es_row['open'] ?? 0) === 1;
    $es_items_out .= strtr($ESFLI, [
        '{ES_FL_C}' => $es_cyc[$es_i % 6],
        '{ES_FL_I}' => (string) $es_i,
        '{ES_FL_SI}' => $es_open ? 'rotate-45 bg-emerald-800 text-white' : 'bg-emerald-100 text-emerald-800',
        '{ES_FL_SB}' => $es_open ? 'block' : 'hidden',
        '{ES_FL_CAT}' => (string) ($es_row['cat'] ?? ''),
        '{ES_FL_Q}' => (string) ($es_row['question'] ?? ''),
        '{ES_FL_A}' => (string) ($es_row['answer'] ?? ''),
    ]);
}

echo strtr($esfl_7, [
    '{ES_FL_CATS}' => $es_cats_out,
    '{ES_FL_ITEMS}' => $es_items_out,
    '{ES_FL_AT}' => (string) ($es_f['aside_title'] ?? ''),
    '{ES_FL_AC}' => (string) ($es_f['aside_count'] ?? ''),
    '{ES_FL_HT}' => (string) ($es_f['help_title'] ?? ''),
    '{ES_FL_HD}' => (string) ($es_f['help_desc'] ?? ''),
    '{ES_FL_PHONE}' => (string) ($es_f['phone'] ?? ''),
    '{ES_FL_PU}' => es_link_url((string) ($es_f['phone_url'] ?? '')),
    '{ES_FL_RT}' => (string) ($es_f['refund_text'] ?? ''),
    '{ES_FL_RU}' => es_link_url((string) ($es_f['refund_url'] ?? '')),
]);
