<?php
/**
 * سکشن درباره ما + آمار شمارنده (about) — برش بایت‌دقیق میرور + رپیترهای SectionManager
 * فرگمنت‌ها با temp/gen_about5.py از refs/about.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Frontend\Icons;

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

$es_points = is_array($es_f['points'] ?? null) ? $es_f['points'] : [];
$es_stats  = is_array($es_f['stats'] ?? null) ? $es_f['stats'] : [];

/** فیلتر es_about_args — توسعه‌پذیری داده‌های سکشن درباره */
$es_args = apply_filters('es_about_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'about')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/about.html
 */
$F_HEAD = <<<'ESA4'
<section id="about" class="relative z-10 py-12 sm:py-16 lg:py-20" style="scroll-margin-top:5rem"><div class="mx-auto grid max-w-[1300px] items-center gap-10 px-4 sm:px-6 lg:grid-cols-12 lg:gap-12 lg:px-8"><div class="flex flex-col items-start gap-5 lg:col-span-6"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>{ES_KICKER}</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.35] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">بیش از <span class="text-emerald-700">{ES_HL}</span> تجربه مهندسی در تولید سازه‌های نوری شهری</h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">{ES_EXCERPT}</p></div><div class="w-full" custom="0.16"><ul class="grid gap-2.5">
ESA4;
$LI_TPL = <<<'ESA4'
<li class="flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white px-4 py-3 text-xs sm:text-[13.5px] font-bold text-slate-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-4.5 w-4.5 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg><span>{ES_TEXT}</span></li>
ESA4;
$F_LIST_CLOSE = <<<'ESA4'
</ul></div><div class="flex w-full flex-col gap-3 pt-1 sm:w-auto sm:flex-row sm:items-center" custom="0.22">
ESA4;
$F_CTA = <<<'ESA4'
<a href="{ES_URL}" class="group inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3.5 text-xs sm:text-sm font-black text-white transition-colors hover:bg-emerald-800"><span>{ES_TEXT}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-y-0.5 group-hover:-translate-x-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a><div class="inline-flex items-center gap-2.5 rounded-xl border border-slate-200/90 bg-white px-4 py-2.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award h-6 w-6 shrink-0 text-emerald-600" aria-hidden="true"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg><div class="leading-tight"><div class="text-xs font-extrabold text-slate-900">ISO 9001:2015</div><div class="text-[11px] font-medium text-slate-500">تضمین کیفیت بین‌المللی</div></div></div></div></div>
ESA4;
$F_IMAGE = <<<'ESA4'
<div class="lg:col-span-6"><div custom="0.1"><div class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950"><img src="{ES_IMG}" alt="{ES_ALT}" loading="lazy" class="h-56 w-full object-cover sm:h-64"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent"></div><div class="absolute inset-x-0 bottom-0 flex items-center justify-between p-5"><div><div class="text-xs font-bold text-emerald-300">{ES_CAPTION}</div><div class="mt-0.5 text-sm sm:text-base font-black text-white">{ES_TITLE}</div></div><span class="hidden rounded-lg bg-white/15 px-2.5 py-1 font-mono text-xs font-black text-white backdrop-blur-md sm:inline-block">ESE FACTORY</span></div></div></div>
ESA4;
$STAT_OPEN_TPL = <<<'ESA4'
<div custom="{ES_DELAY}">
ESA4;
$STAT_REST = <<<'ESA4'
<div class="card-light flex flex-col justify-between rounded-2xl p-4 sm:p-5"><span data-counter-target="{ES_TARGET}" data-counter-suffix="{ES_SUFFIX}" class="text-2xl sm:text-3xl font-black tracking-tight text-emerald-700 tabular-nums lg:text-4xl">{ES_NUM}{ES_SUFFIX}</span><div class="mt-2"><div class="text-xs sm:text-[13.5px] font-extrabold text-slate-900">{ES_LABEL}</div><div class="mt-0.5 text-[11px] font-medium text-slate-500">{ES_SUB}</div></div></div></div>
ESA4;
$F_TAIL = <<<'ESA4'
</div></div></div></section>
ESA4;


// ═══ رندر ═══
echo strtr($F_HEAD, [
    '{ES_KICKER}' => (string) ($es_f['kicker'] ?? ''),
    '{ES_HL}'     => (string) ($es_f['title_highlight'] ?? ''),
    '{ES_EXCERPT}' => (string) ($es_f['excerpt'] ?? ''),
]);

// چک‌لیست
foreach ($es_points as $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($LI_TPL, ['{ES_TEXT}' => (string) ($es_row['text'] ?? '')]);
}
echo $F_LIST_CLOSE;

// CTA + چیپ ISO
echo strtr($F_CTA, [
    '{ES_URL}'  => es_link_url((string) ($es_f['cta_url'] ?? '')),
    '{ES_TEXT}' => (string) ($es_f['cta_text'] ?? ''),
]);

// بلوک تصویر
echo strtr($F_IMAGE, [
    '{ES_IMG}'     => es_media_url((string) ($es_f['image'] ?? '')),
    '{ES_ALT}'     => (string) ($es_f['image_alt'] ?? ''),
    '{ES_CAPTION}' => (string) ($es_f['image_caption'] ?? ''),
    '{ES_TITLE}'   => (string) ($es_f['image_title'] ?? ''),
]);

// آمار شمارنده
echo '<div class="mt-4 grid grid-cols-2 gap-3.5 sm:gap-4">';
$es_stat_delays = ['0.12', '0.16999999999999998', '0.22', '0.27'];
foreach ($es_stats as $es_i => $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    $es_delay = (string) ($es_row['delay'] ?? ($es_stat_delays[$es_i] ?? '0.' . str_pad((string) (12 + 5 * $es_i), 2, '0')));
    $es_target = (string) ($es_row['target'] ?? '');
    $es_suffix = (string) ($es_row['suffix'] ?? '+');
    echo strtr($STAT_OPEN_TPL, ['{ES_DELAY}' => $es_delay]);
    echo strtr($STAT_REST, [
        '{ES_TARGET}' => $es_target,
        '{ES_SUFFIX}' => $es_suffix,
        '{ES_NUM}'    => es_fa_num($es_target),
        '{ES_LABEL}'  => (string) ($es_row['label'] ?? ''),
        '{ES_SUB}'    => (string) ($es_row['sub'] ?? ''),
    ]);
}
echo $F_TAIL;
