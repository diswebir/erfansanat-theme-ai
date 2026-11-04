<?php
/**
 * سکشن مزیت‌های رقابتی و فرآیند اجرا (quality) — برش بایت‌دقیق میرور
 * دو رپیتر: ویژگی‌ها (۴ کارت) و گام‌ها (۵ کارت)؛ آیکون‌های این سکشن stroke=1.75.
 * فرگمنت‌ها با temp/gen_quality5.py از refs/quality.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Frontend\Icons;

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];
$es_feats = is_array($es_f['features'] ?? null) ? array_values($es_f['features']) : [];
$es_steps = is_array($es_f['steps'] ?? null) ? array_values($es_f['steps']) : [];
if ($es_feats === [] && $es_steps === []) {
    return; // بدون محتوا → سکشن مخفی
}

/** فیلتر es_quality_args — توسعه‌پذیری سکشن مزیت‌ها */
$es_args = apply_filters('es_quality_args', ['fields' => $es_f, 'features' => $es_feats, 'steps' => $es_steps, 'section_id' => (string) ($args['section_id'] ?? 'quality')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_feats = is_array($es_args['features'] ?? null) ? array_values($es_args['features']) : $es_feats;
    $es_steps = is_array($es_args['steps'] ?? null) ? array_values($es_args['steps']) : $es_steps;
}

/** تأخیرهای نمایش میرور (عیناً حفظ شده؛ شامل اعشار بلند کارت ۴) */
$es_delays = ['0.05', '0.1', '0.15000000000000002', '0.2'];

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/quality.html
 */
$F_HEAD = <<<'ESQ11'
<section class="relative z-10 py-12 sm:py-16 lg:py-20"><div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8"><div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>{ES_KICKER}</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">{ES_TITLE_PRE}<span class="text-emerald-700">{ES_HL}</span></h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">{ES_EXCERPT}</p></div></div><div class="mt-8 sm:mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
ESQ11;

$CARD_TPL = <<<'ESQ11'
<div custom="{ES_CUSTOM}"><div class="card-light flex h-full flex-col justify-between rounded-2xl p-5 sm:p-6"><div><div class="flex items-center justify-between gap-2"><span class="grid h-11 w-11 place-items-center rounded-xl border border-emerald-100 bg-emerald-50/70 text-emerald-700">{ES_ICON}</span><span class="font-mono text-xs font-extrabold text-emerald-700 tabular-nums">{ES_BADGE}</span></div><h3 class="mt-4 text-base font-black text-slate-900">{ES_TITLE}</h3><p class="mt-2 text-xs sm:text-[13px] font-medium leading-6 text-slate-600">{ES_DESC}</p></div></div></div>
ESQ11;

$BAND_HEAD = <<<'ESQ11'
<div class="mt-8 sm:mt-10" custom="0.14"><div class="rounded-3xl border border-slate-200/85 bg-white p-6 sm:p-8 lg:p-10"><div class="flex flex-col items-center justify-between gap-2 border-b border-slate-100 pb-6 sm:flex-row"><h3 class="text-base sm:text-lg font-black text-slate-900">{ES_PROC_PRE} <span class="text-emerald-700">{ES_PROC_HL}</span></h3><span class="text-xs font-bold text-slate-500">{ES_PROC_LABEL}</span></div><div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
ESQ11;

$STEP_TPL = <<<'ESQ11'
<div class="flex items-center gap-3.5 rounded-2xl border border-slate-100 bg-[#f6f8f7]/80 p-3.5 lg:flex-col lg:items-start lg:p-4"><div class="flex w-full items-center justify-between"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-emerald-700 shadow-2xs">{ES_ICON}</span><span class="text-xs font-black text-emerald-700/70 tabular-nums">{ES_STEP}</span></div><div><div class="text-xs sm:text-[13px] font-extrabold text-slate-900">{ES_TITLE}</div><div class="mt-0.5 text-[11px] font-medium text-slate-500">{ES_SUB}</div></div></div>
ESQ11;


echo strtr($F_HEAD, [
    '{ES_KICKER}'  => (string) ($es_f['kicker'] ?? ''),
    '{ES_TITLE_PRE}' => (string) ($es_f['title_pre'] ?? ''),
    '{ES_HL}'      => (string) ($es_f['title_hl'] ?? ''),
    '{ES_EXCERPT}' => (string) ($es_f['excerpt'] ?? ''),
]);

// کارت‌های ویژگی
foreach ($es_feats as $es_i => $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    $es_icn = (string) ($es_row['icon'] ?? 'badge-check');
    echo strtr($CARD_TPL, [
        '{ES_CUSTOM}' => (string) ($es_delays[$es_i] ?? end($es_delays)),
        '{ES_ICON}'   => Icons::svg($es_icn, 'lucide lucide-' . $es_icn . ' h-5.5 w-5.5', '1.75'),
        '{ES_BADGE}'  => (string) ($es_row['badge'] ?? ''),
        '{ES_TITLE}'  => (string) ($es_row['title'] ?? ''),
        '{ES_DESC}'   => (string) ($es_row['desc'] ?? ''),
    ]);
}
echo '</div>';

// باند فرآیند + گام‌ها
echo strtr($BAND_HEAD, [
    '{ES_PROC_PRE}'   => (string) ($es_f['process_pre'] ?? ''),
    '{ES_PROC_HL}'    => (string) ($es_f['process_hl'] ?? ''),
    '{ES_PROC_LABEL}' => (string) ($es_f['process_label'] ?? ''),
]);
foreach ($es_steps as $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    $es_icn = (string) ($es_row['icon'] ?? 'search');
    echo strtr($STEP_TPL, [
        '{ES_ICON}'  => Icons::svg($es_icn, 'lucide lucide-' . $es_icn . ' h-5 w-5', '1.75'),
        '{ES_STEP}'  => (string) ($es_row['step'] ?? ''),
        '{ES_TITLE}' => (string) ($es_row['title'] ?? ''),
        '{ES_SUB}'   => (string) ($es_row['sub'] ?? ''),
    ]);
}
echo '</div></div></div></div></section>';
