<?php
/**
 * سکشن تایم‌لاین تاریخچه (history) — برش بایت‌دقیق میرور + رپیتر مراحل
 * فرگمنت‌ها با temp/gen_history5.py از refs/history.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Frontend\Icons;

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];
$es_stages = is_array($es_f['stages'] ?? null) ? array_values($es_f['stages']) : [];
if ($es_stages === []) {
    return; // بدون مرحله → سکشن رندر نمی‌شود
}
$es_total = count($es_stages);
$es_total_fa = es_fa_num((string) $es_total);

/** فیلتر es_history_args — توسعه‌پذیری تایم‌لاین */
$es_args = apply_filters('es_history_args', ['fields' => $es_f, 'stages' => $es_stages, 'section_id' => (string) ($args['section_id'] ?? 'history')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_stages = is_array($es_args['stages'] ?? null) ? array_values($es_args['stages']) : $es_stages;
    $es_total = count($es_stages);
    $es_total_fa = es_fa_num((string) $es_total);
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/history.html
 */
$F_HEAD = <<<'ESH5'
<section data-history-scroll-track="true" data-history-total="{ES_TOTAL}" aria-label="تاریخچه شرکت دانش‌بنیان عرفان صنعت اصفهان" class="relative mx-auto max-w-[1300px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8"><div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>{ES_KICKER}</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">تاریخچه و دستاوردهای <span class="text-emerald-700">{ES_HL}</span></h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">{ES_EXCERPT}</p></div></div><div data-history-stage="true" class="mt-8"><div class="rounded-3xl border border-slate-200/85 bg-white p-3.5 shadow-2xs sm:p-5"><div class="mb-3.5 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3"><div class="flex items-center gap-2 text-xs font-extrabold text-slate-700"><span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span><span>مسیر تکامل صنعتی شرکت</span><span class="text-slate-300">·</span><span data-history-step-label="true" class="font-black text-emerald-700 tabular-nums">گام {ES_N1} از {ES_TOTAL_FA} (سال {ES_YEAR1})</span></div><div class="flex items-center gap-3"><span class="hidden text-[11px] font-bold text-slate-400 sm:inline">با اسکرول یا کلیک بین سال‌ها حرکت کنید</span><div class="h-2 w-32 overflow-hidden rounded-full bg-slate-100 sm:w-44"><div data-history-progress-bar="true" style="width:{ES_WIDTH}%" class="h-full rounded-full bg-gradient-to-l from-emerald-500 to-emerald-700 transition-all duration-300"></div></div></div></div><div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6" role="tablist" aria-label="سال‌های شاخص تاریخچه شرکت">
ESH5;
$TAB_ACTIVE_T = <<<'ESH5'
<button type="button" role="tab" aria-selected="{ES_SEL}" data-home2-history-btn="{ES_I}" class="{ES_CLS}"><div class="flex w-full items-center justify-between gap-1.5"><span class="text-sm sm:text-base font-black tabular-nums">{ES_YEAR}</span><span class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-black tabular-nums {ES_NUMCLS}">{ES_NUM}</span></div><span class="mt-1 line-clamp-1 text-[10.5px] font-bold {ES_NAMECLS}">{ES_NAME}</span></button>
ESH5;
$F_TABS_CLOSE = <<<'ESH5'
</div></div>
ESH5;
$PANEL_T = <<<'ESH5'
<div data-home2-history-panel="{ES_I}" class="{ES_CLS} card-light mt-5 items-center gap-6 rounded-3xl p-6 sm:p-8 lg:grid-cols-12 lg:gap-10"><div class="text-right lg:col-span-6"><div class="flex flex-wrap items-center gap-2"><span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-800 tabular-nums">{ES_ICON_SVG}<span>{ES_YEAR_CH}</span></span><span class="rounded-xl border border-emerald-200/80 bg-white px-3 py-1 text-[11px] font-extrabold text-emerald-700">{ES_BADGE}</span></div><h3 class="mt-3.5 text-xl font-black text-slate-900 sm:text-2xl">{ES_TITLE}</h3><div class="mt-1.5 text-xs font-extrabold text-emerald-700 sm:text-[13px]">{ES_SUBL}</div><p class="mt-3.5 text-right text-xs leading-7 font-medium text-slate-600 sm:text-[13.5px] sm:leading-7">{ES_BODY}</p><div class="mt-5 grid grid-cols-2 gap-3"><div class="rounded-2xl border border-slate-200/80 bg-[#f6f8f7] px-4 py-3"><div class="text-[11px] font-semibold text-slate-500">{ES_S1L}</div><div class="mt-0.5 text-xs font-black text-slate-900 sm:text-sm">{ES_S1V}</div></div><div class="rounded-2xl border border-slate-200/80 bg-[#f6f8f7] px-4 py-3"><div class="text-[11px] font-semibold text-slate-500">{ES_S2L}</div><div class="mt-0.5 text-xs font-black text-slate-900 sm:text-sm">{ES_S2V}</div></div></div><div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4"><span class="text-xs font-extrabold text-slate-500 tabular-nums">{ES_STEP}<span class="text-emerald-700">{ES_HINT}</span></span><div class="flex items-center gap-2"><button type="button" data-home2-history-prev="true" aria-label="سال قبل" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-extrabold text-slate-700 transition-colors hover:border-emerald-400 hover:text-emerald-700 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right h-3.5 w-3.5" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg><span>{ES_PREV}</span></button><button type="button" data-home2-history-next="true" aria-label="سال بعد" class="inline-flex items-center gap-1 rounded-xl bg-emerald-700 px-3 py-1.5 text-xs font-extrabold text-white transition-colors hover:bg-emerald-800 cursor-pointer"><span>{ES_NEXT}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></button></div></div></div><div class="lg:col-span-6"><div class="grid grid-cols-12 gap-3.5"><div class="relative col-span-7 h-60 overflow-hidden rounded-2xl bg-slate-900 sm:h-72"><img src="{ES_IMG}" alt="{ES_ALT}" class="h-full w-full object-cover"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div><div class="absolute right-3.5 bottom-3 text-[11px] font-bold text-white">{ES_CAP}</div></div><div class="col-span-5 flex flex-col gap-3.5"><div class="h-[113px] overflow-hidden rounded-2xl bg-slate-900 sm:h-[137px]"><img src="{ES_IMG2}" alt="{ES_ALT}" class="h-full w-full object-cover"/></div><div class="h-[113px] overflow-hidden rounded-2xl bg-slate-900 sm:h-[137px]"><img src="{ES_IMG3}" alt="{ES_ALT}" class="h-full w-full object-cover"/></div></div></div></div></div>
ESH5;
$F_TAIL = <<<'ESH5'
</div></section>
ESH5;


// ═══ رندر ═══
$es_first_year = (string) ($es_stages[0]['year'] ?? '');
echo strtr($F_HEAD, [
    '{ES_TOTAL}'  => (string) $es_total,
    '{ES_TOTAL_FA}' => $es_total_fa,
    '{ES_KICKER}' => (string) ($es_f['kicker'] ?? ''),
    '{ES_HL}'     => (string) ($es_f['title_highlight'] ?? ''),
    '{ES_EXCERPT}' => (string) ($es_f['excerpt'] ?? ''),
    '{ES_N1}'     => es_fa_num('1'),
    '{ES_YEAR1}'  => $es_first_year,
    '{ES_WIDTH}'  => (string) (int) round(100 / max(1, $es_total)),
]);

// دکمه‌های تب
foreach ($es_stages as $es_i => $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($TAB_ACTIVE_T, [
        '{ES_I}'       => (string) $es_i,
        '{ES_SEL}'     => $es_i === 0 ? 'true' : 'false',
        '{ES_YEAR}'    => (string) ($es_row['year'] ?? ''),
        '{ES_NUM}'     => es_fa_num((string) ($es_i + 1)),
        '{ES_CLS}'     => $es_i === 0 ? 'group flex flex-col items-start justify-between rounded-2xl border px-3.5 py-2.5 text-right transition-all cursor-pointer border-emerald-700 bg-emerald-700 text-white shadow-sm' : 'group flex flex-col items-start justify-between rounded-2xl border px-3.5 py-2.5 text-right transition-all cursor-pointer border-slate-200/75 bg-[#f6f8f7] text-slate-700 hover:border-emerald-200 hover:bg-white',
        '{ES_NUMCLS}'  => $es_i === 0 ? 'bg-white/20 text-white' : 'bg-slate-200/80 text-slate-600',
        '{ES_NAMECLS}' => $es_i === 0 ? 'text-emerald-100' : 'text-slate-500',
        '{ES_NAME}'    => (string) ($es_row['name'] ?? ''),
    ]);
}
echo $F_TABS_CLOSE;

// پنل‌های مراحل
foreach ($es_stages as $es_i => $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($PANEL_T, [
        '{ES_I}'       => (string) $es_i,
        '{ES_CLS}'     => $es_i === 0 ? 'grid' : 'hidden',
        '{ES_YEAR_CH}' => 'سال ' . (string) ($es_row['year'] ?? '') . ' خورشیدی',
        '{ES_BADGE}'   => (string) ($es_row['badge'] ?? ''),
        '{ES_TITLE}'   => (string) ($es_row['title'] ?? ''),
        '{ES_SUBL}'    => (string) ($es_row['subline'] ?? ''),
        '{ES_BODY}'    => (string) ($es_row['body'] ?? ''),
        '{ES_S1L}'     => (string) ($es_row['s1_label'] ?? ''),
        '{ES_S1V}'     => (string) ($es_row['s1_value'] ?? ''),
        '{ES_S2L}'     => (string) ($es_row['s2_label'] ?? ''),
        '{ES_S2V}'     => (string) ($es_row['s2_value'] ?? ''),
        '{ES_STEP}'    => 'مرحله ' . es_fa_num((string) ($es_i + 1)) . ' از ' . $es_total_fa . ' · ',
        '{ES_HINT}'    => (string) ($es_row['hint'] ?? ''),
        '{ES_PREV}'    => 'قبلی',
        '{ES_NEXT}'    => 'بعدی',
        '{ES_CAP}'     => (string) ($es_row['caption'] ?? ''),
        '{ES_IMG}'     => es_media_url((string) ($es_row['image'] ?? '')),
        '{ES_IMG2}'    => es_media_url((string) ($es_row['image2'] ?? '')),
        '{ES_IMG3}'    => es_media_url((string) ($es_row['image3'] ?? '')),
        '{ES_ALT}'     => (string) ($es_row['image_alt'] ?? ''),
        '{ES_ICON_SVG}'   => \ErfanSanat\Theme\Frontend\Icons::svg((string) ($es_row['icon'] ?? 'award'), 'lucide lucide-' . (string) ($es_row['icon'] ?? 'award') . ' h-4 w-4 ' . ((string) ($es_row['icon'] ?? 'award') === 'sun' ? 'text-amber-500' : 'text-emerald-600')),
    ]);
}
echo $F_TAIL;
