<?php
/**
 * FAQ دسته محصول — آکاردئون میرور (قالب cat) از متای ترم (es_get_category_faq)؛ مخفی اگر خالی
 * فرگمنت با temp/gen_p68.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_CAT_FAQ_WRAPPER = <<<'ESP68W'
<div class="mt-10 border-t border-slate-100 pt-8" data-faq-list="true"><div class="mb-5 flex items-center gap-2.5"><span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-question-mark h-4.5 w-4.5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><path d="M12 17h.01"></path></svg></span><h3 class="text-lg font-black text-slate-950">سوالات متداول درباره {ES_TERM_LABEL}</h3></div><div class="grid gap-3 md:grid-cols-2">{ES_ROWS}</div></div>
ESP68W;
$ES_CAT_FAQ = <<<'ESP68F'
<div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-slate-50/40"><button type="button" data-faq-toggle="{ES_I}" class="flex w-full items-center justify-between gap-3 px-5 py-4 text-right cursor-pointer"><span class="text-[13.5px] font-extrabold leading-6 text-slate-900">{ES_Q}</span><span data-faq-icon="{ES_I}" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-transform {ES_ICON_CLS}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-3.5 w-3.5" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg></span></button><div data-faq-body="{ES_I}" class="{ES_BODY_CLS} border-t border-slate-200/60 bg-white px-5 py-4 text-[13px] leading-7 text-slate-600">{ES_A}</div></div>
ESP68F;
$es_args = $args ?? [];
$es_label = (string) ($es_args['label'] ?? '');
$es_rows = array_values((array) ($es_args['rows'] ?? []));
if ($es_label === '' || $es_rows === []) {
    return; // FAQ فقط اگر متا پر
}
$es_rows_html = '';
foreach ($es_rows as $es_i => $es_row) {
    $es_first = $es_i === 0;
    $es_rows_html .= strtr($ES_CAT_FAQ, [
        '{ES_I}'        => (string) $es_i,
        '{ES_Q}'        => esc_html((string) ($es_row['name_question'] ?? '')),
        '{ES_A}'        => esc_html((string) ($es_row['answer_question'] ?? '')),
        '{ES_ICON_CLS}' => $es_first ? 'rotate-45 bg-emerald-800 text-white' : 'bg-emerald-100 text-emerald-800',
        '{ES_BODY_CLS}' => $es_first ? 'block' : 'hidden',
    ]);
}
echo strtr($ES_CAT_FAQ_WRAPPER, ['{ES_ROWS}' => $es_rows_html, '{ES_TERM_LABEL}' => esc_html($es_label)]);
