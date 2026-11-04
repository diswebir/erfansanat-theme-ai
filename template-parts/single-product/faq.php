<?php
/**
 * سوالات متداول محصول — آکاردئون data-faq-* میرور (برش بایت‌دقیق product/wall-washer.html)
 * فرگمنت با temp/gen_p64.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Data\RepeaterEngine;

$es_pid = (int) ($args['pid'] ?? 0);
$es_title = get_the_title($es_pid);
$es_rows = [];
foreach (RepeaterEngine::read($es_pid, 'faq')['rows'] as $es_row) {
    $es_q = trim((string) ($es_row['name_question'] ?? ''));
    $es_a = trim((string) ($es_row['answer_question'] ?? ''));
    if ($es_q !== '' && $es_a !== '') {
        $es_rows[] = ['q' => $es_q, 'a' => $es_a];
    }
}
$es_rows = apply_filters('es_product_faq_rows', $es_rows, $es_pid);
if ($es_rows === []) {
    return;
}

$ES_FAQ_HEAD = <<<'ESP64L'
<section class="mx-auto max-w-4xl px-5 pb-14 lg:px-8"><div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs sm:p-8"><div class="mb-6 flex items-center gap-2.5"><span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-question-mark h-5 w-5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><path d="M12 17h.01"></path></svg></span><div><h2 class="text-lg font-extrabold text-slate-900">سوالات متداول درباره {ES_TITLE}</h2><p class="text-xs text-slate-500">پاسخ به پرتکرارترین سوالات خریداران و کارفرمایان</p></div></div><div data-faq-list="true" class="divide-y divide-slate-100">
ESP64L;
$ES_FAQ_OPEN = <<<'ESP64M'
<div class="py-3.5 first:pt-0 last:pb-0"><button type="button" data-faq-toggle="{ES_I}" class="flex w-full items-center justify-between gap-4 py-2 text-right cursor-pointer"><span class="text-sm font-extrabold text-slate-800">{ES_Q}</span><span data-faq-icon="{ES_I}" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-all bg-emerald-800 text-white rotate-45"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-4 w-4" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg></span></button><div data-faq-body="{ES_I}" class="block pt-2 pb-1"><p class="text-[13.5px] leading-7 text-slate-600">{ES_A}</p></div></div>
ESP64M;
$ES_FAQ_IDLE = <<<'ESP64N'
<div class="py-3.5 first:pt-0 last:pb-0"><button type="button" data-faq-toggle="{ES_I}" class="flex w-full items-center justify-between gap-4 py-2 text-right cursor-pointer"><span class="text-sm font-extrabold text-slate-800">{ES_Q}</span><span data-faq-icon="{ES_I}" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-all bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-4 w-4" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg></span></button><div data-faq-body="{ES_I}" class="hidden pt-2 pb-1"><p class="text-[13.5px] leading-7 text-slate-600">{ES_A}</p></div></div>
ESP64N;
$ES_FAQ_TAIL = <<<'ESP64O'
</div></div></section>
ESP64O;

echo strtr($ES_FAQ_HEAD, ['{ES_TITLE}' => esc_html($es_title)]);
foreach ($es_rows as $es_i => $es_row) {
    $es_tpl = $es_i === 0 ? $ES_FAQ_OPEN : $ES_FAQ_IDLE;
    echo strtr($es_tpl, ['{ES_I}' => (string) $es_i, '{ES_Q}' => esc_html((string) $es_row['q']), '{ES_A}' => esc_html((string) $es_row['a'])]);
}
echo $ES_FAQ_TAIL;
