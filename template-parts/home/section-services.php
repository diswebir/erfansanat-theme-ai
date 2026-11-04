<?php
/**
 * سکشن خدمات تب‌دار (services) — برش بایت‌دقیق میرور + رپیتر ردیف‌ها
 * فرگمنت‌ها با temp/gen_cat_services5.py از refs/services.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Frontend\Icons;

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];
$es_rows = is_array($es_f['rows'] ?? null) ? array_values($es_f['rows']) : [];
if ($es_rows === []) {
    return; // بدون خدمت → رندر نمی‌شود
}
$es_total = count($es_rows);

/** فیلتر es_services_args — توسعه‌پذیری سکشن خدمات */
$es_args = apply_filters('es_services_args', ['fields' => $es_f, 'rows' => $es_rows, 'section_id' => (string) ($args['section_id'] ?? 'services')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_rows = is_array($es_args['rows'] ?? null) ? array_values($es_args['rows']) : $es_rows;
    $es_total = count($es_rows);
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/services.html
 */
$F_HEAD = <<<'ESS7'
<section id="services" class="relative z-10 scroll-mt-20 py-12 sm:py-16 lg:py-20" data-services-section="true"><div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8"><div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>{ES_KICKER}</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">خدمات تخصصی <span class="text-emerald-700">{ES_HL}</span></h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">{ES_EXCERPT}</p></div></div><div class="mt-8 sm:mt-10 grid items-stretch gap-6 lg:grid-cols-12 lg:gap-8"><div class="flex flex-col justify-between gap-2.5 lg:col-span-6" custom="0.05">
ESS7;
$ROW_TPL = <<<'ESS7'
<button type="button" data-service-row="{ES_I}" data-service-title="{ES_TITLE}" data-service-desc-text="{ES_DESC}" data-service-img="{ES_IMG}" data-service-counter="{ES_COUNTER}" class="group relative flex items-start gap-4 rounded-2xl border p-4 text-right transition-colors duration-200 sm:p-5 cursor-pointer {ES_ROW_CLS}"><span data-service-indicator="{ES_I}" class="absolute right-0 top-1/2 w-1 -translate-y-1/2 rounded-l-full bg-emerald-600 transition-all duration-200 {ES_IND_CLS}"></span><span data-service-num="{ES_I}" class="mt-0.5 text-xs font-black tabular-nums transition-colors {ES_NUM_CLS}">{ES_NUM}</span><div class="flex flex-1 flex-col gap-1"><span class="flex items-center justify-between gap-3"><span data-service-heading="{ES_I}" class="text-sm sm:text-base font-extrabold transition-colors {ES_HEAD_CLS}">{ES_TITLE}</span>{ES_ICON}</span><p data-service-desc="{ES_I}" class="text-xs sm:text-[13px] font-medium leading-6 text-slate-600">{ES_DESC}</p><div data-service-mobile-img="{ES_I}" class="{ES_MOB_CLS} overflow-hidden lg:hidden"><img src="{ES_IMG}" alt="{ES_TITLE}" loading="lazy" class="mt-3 h-40 w-full rounded-xl border border-slate-200 object-cover"/></div></div></button>
ESS7;
$PREVIEW_TPL = <<<'ESS7'
<div class="hidden lg:col-span-6 lg:block" custom="0.1"><div class="relative h-full min-h-[520px] overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 shadow-sm"><img data-service-preview-img="true" src="{ES_IMG}" alt="{ES_TITLE}" class="absolute inset-0 h-full w-full object-cover transition-opacity duration-300"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/25 to-transparent"></div><span data-service-preview-counter="true" class="absolute left-6 top-6 rounded-xl border border-white/20 bg-slate-950/65 px-3.5 py-1.5 text-xs font-extrabold text-emerald-300 tabular-nums backdrop-blur-md">{ES_COUNTER}</span><div class="absolute inset-x-0 bottom-0 flex items-center gap-4 p-7"><span class="grid h-13 w-13 shrink-0 place-items-center rounded-2xl border border-emerald-400/40 bg-emerald-950/80 backdrop-blur-md">{ES_ICON0}{ES_ICON1}{ES_ICON2}{ES_ICON3}{ES_ICON4}</span><div class="flex flex-col gap-1"><span data-service-preview-title="true" class="text-xl font-black text-white">{ES_TITLE}</span><span data-service-preview-desc="true" class="text-xs sm:text-[13px] font-medium leading-6 text-slate-200">{ES_DESC}</span></div></div></div></div></div></div></section
ESS7;
$F_CLOSE = <<<'ESS7'
>
ESS7;


// ═══ رندر ═══
echo strtr($F_HEAD, [
    '{ES_KICKER}'  => (string) ($es_f['kicker'] ?? ''),
    '{ES_HL}'      => (string) ($es_f['title_highlight'] ?? ''),
    '{ES_EXCERPT}' => (string) ($es_f['excerpt'] ?? ''),
]);

// ردیف‌های خدمت
foreach ($es_rows as $es_i => $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    // کلاس آیکون: آیکون‌های پسوند عددی (building2) در میرور نام دوبل دارند
    $es_icn = (string) ($es_row['icon'] ?? 'landmark');
    $es_icn_cls = 'lucide lucide-' . $es_icn . (preg_match('/\d$/', $es_icn) === 1 ? ' lucide-' . preg_replace('/(\d)$/', '-$1', $es_icn) : '');
    echo strtr($ROW_TPL, [
        '{ES_I}'        => (string) $es_i,
        '{ES_TITLE}'    => (string) ($es_row['title'] ?? ''),
        '{ES_DESC}'     => (string) ($es_row['desc'] ?? ''),
        '{ES_IMG}'      => es_media_url((string) ($es_row['image'] ?? '')),
        '{ES_COUNTER}'  => es_fa_num((string) ($es_i + 1)) . ' / ' . es_fa_num((string) $es_total),
        '{ES_NUM}'      => es_fa_num(str_pad((string) ($es_i + 1), 2, '0', STR_PAD_LEFT)),
        '{ES_ROW_CLS}'  => $es_i === 0 ? 'border-emerald-500/40 bg-white shadow-sm' : 'border-slate-200/80 bg-white/60 hover:border-emerald-200 hover:bg-white',
        '{ES_IND_CLS}'  => $es_i === 0 ? 'h-10' : 'h-0',
        '{ES_NUM_CLS}'  => $es_i === 0 ? 'text-emerald-700' : 'text-slate-400',
        '{ES_HEAD_CLS}' => $es_i === 0 ? 'text-emerald-950' : 'text-slate-800',
        '{ES_MOB_CLS}'  => $es_i === 0 ? 'block' : 'hidden',
        '{ES_ICON}'     => Icons::svg($es_icn, $es_icn_cls . ($es_i === 0 ? ' h-5 w-5 shrink-0 transition-colors text-emerald-600' : ' h-5 w-5 shrink-0 transition-colors text-slate-400 group-hover:text-emerald-600'), '1.7', ' data-service-row-icon="' . (string) $es_i . '"'),
    ]);
}
echo '</div>';

// پنل پیش‌نمایش (ردیف فعال = ردیف اول)
$es_row0 = is_array($es_rows[0] ?? null) ? $es_rows[0] : [];
echo strtr($PREVIEW_TPL, [
    '{ES_IMG}'     => es_media_url((string) ($es_row0['image'] ?? '')),
    '{ES_TITLE}'   => (string) ($es_row0['title'] ?? ''),
    '{ES_DESC}'    => (string) ($es_row0['desc'] ?? ''),
    '{ES_COUNTER}' => es_fa_num('1') . ' / ' . es_fa_num((string) $es_total),
        '{ES_ICON0}' => isset($es_rows[0]) ? strtr('<span data-service-preview-icon="{ES_I}" class="{ES_ICON_CLS}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-landmark h-6 w-6 text-emerald-300" aria-hidden="true"><path d="M10 18v-7"></path><path d="M11.12 2.198a2 2 0 0 1 1.76.006l7.866 3.847c.476.233.31.949-.22.949H3.474c-.53 0-.695-.716-.22-.949z"></path><path d="M14 18v-7"></path><path d="M18 18v-7"></path><path d="M3 22h18"></path><path d="M6 18v-7"></path></svg></span>', ['{ES_I}' => (string) 0, '{ES_ICON_CLS}' => 'block']) : '',
        '{ES_ICON1}' => isset($es_rows[1]) ? strtr('<span data-service-preview-icon="{ES_I}" class="hidden"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-milestone h-6 w-6 text-emerald-300" aria-hidden="true"><path d="M12 13v8"></path><path d="M12 3v3"></path><path d="M4 6a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h13a2 2 0 0 0 1.152-.365l3.424-2.317a1 1 0 0 0 0-1.635l-3.424-2.318A2 2 0 0 0 17 6z"></path></svg></span>', ['{ES_I}' => (string) 1, '{ES_ICON_CLS}' => 'hidden']) : '',
        '{ES_ICON2}' => isset($es_rows[2]) ? strtr('<span data-service-preview-icon="{ES_I}" class="hidden"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-waypoints h-6 w-6 text-emerald-300" aria-hidden="true"><circle cx="12" cy="4.5" r="2.5"></circle><path d="m10.2 6.3-3.9 3.9"></path><circle cx="4.5" cy="12" r="2.5"></circle><path d="M7 12h10"></path><circle cx="19.5" cy="12" r="2.5"></circle><path d="m13.8 17.7 3.9-3.9"></path><circle cx="12" cy="19.5" r="2.5"></circle></svg></span>', ['{ES_I}' => (string) 2, '{ES_ICON_CLS}' => 'hidden']) : '',
        '{ES_ICON3}' => isset($es_rows[3]) ? strtr('<span data-service-preview-icon="{ES_I}" class="hidden"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-6 w-6 text-emerald-300" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg></span>', ['{ES_I}' => (string) 3, '{ES_ICON_CLS}' => 'hidden']) : '',
        '{ES_ICON4}' => isset($es_rows[4]) ? strtr('<span data-service-preview-icon="{ES_I}" class="hidden"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 lucide-building-2 h-6 w-6 text-emerald-300" aria-hidden="true"><path d="M10 12h4"></path><path d="M10 8h4"></path><path d="M14 21v-3a2 2 0 0 0-4 0v3"></path><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"></path><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"></path></svg></span>', ['{ES_I}' => (string) 4, '{ES_ICON_CLS}' => 'hidden']) : '',
]);
echo $F_CLOSE;
