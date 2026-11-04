<?php
/**
 * سکشن مشتریان و سازمان‌های همکار (customers) — برش بایت‌دقیق میرور + رپیتر کارت‌ها
 * فرگمنت‌ها با temp/gen_ac5.py از refs/customers.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Frontend\Icons;

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];
$es_rows = is_array($es_f['rows'] ?? null) ? array_values($es_f['rows']) : [];
if ($es_rows === []) {
    return; // بدون مشتری → سکشن مخفی
}

/** فیلتر es_customers_args — توسعه‌پذیری سکشن مشتریان */
$es_args = apply_filters('es_customers_args', ['fields' => $es_f, 'rows' => $es_rows, 'section_id' => (string) ($args['section_id'] ?? 'customers')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_rows = is_array($es_args['rows'] ?? null) ? array_values($es_args['rows']) : $es_rows;
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/customers.html
 */
$F_HEAD = <<<'ESC8'
<section aria-label="مشتریان و سازمان‌های همکار عرفان صنعت" class="mx-auto max-w-[1300px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8"><div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>{ES_KICKER}</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">مشتریان و سازمان‌های <span class="text-emerald-700">{ES_HL}</span></h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">{ES_EXCERPT}</p></div></div><div class="mt-8 sm:mt-10 grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-6">
ESC8;

$CARD_TPL = <<<'ESC8'
<div class="card-light flex flex-col items-start justify-between rounded-2xl p-4 text-right"><div class="flex w-full items-center justify-between"><span class="grid h-9 w-9 place-items-center rounded-xl border border-emerald-100 bg-emerald-50/70 text-emerald-700">{ES_ICON}</span><span class="font-mono text-[11px] font-bold text-slate-400 tabular-nums">{ES_NUM}</span></div><div class="mt-3"><div class="text-xs sm:text-[13px] font-extrabold text-slate-900">{ES_TITLE}</div><div class="mt-0.5 line-clamp-1 text-[10.5px] font-medium text-slate-500">{ES_SUB}</div></div></div>
ESC8;


echo strtr($F_HEAD, [
    '{ES_KICKER}'  => (string) ($es_f['kicker'] ?? ''),
    '{ES_HL}'      => (string) ($es_f['title_highlight'] ?? ''),
    '{ES_EXCERPT}' => (string) ($es_f['excerpt'] ?? ''),
]);

foreach ($es_rows as $es_i => $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($CARD_TPL, [
        '{ES_ICON}'  => Icons::svg((string) ($es_row['icon'] ?? 'landmark'), 'lucide lucide-' . (string) ($es_row['icon'] ?? 'landmark') . ' h-4.5 w-4.5'),
        '{ES_NUM}'   => es_fa_num(str_pad((string) ($es_i + 1), 2, '0', STR_PAD_LEFT)),
        '{ES_TITLE}' => (string) ($es_row['title'] ?? ''),
        '{ES_SUB}'   => (string) ($es_row['sub'] ?? ''),
    ]);
}
echo '</div></section>';
