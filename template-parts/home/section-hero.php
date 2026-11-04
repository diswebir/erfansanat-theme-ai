<?php
/**
 * سکشن هیرو طرح ۱ (hero-home1) — برش بایت‌دقیق میرور + داده پویای SectionManager
 * فرگمنت‌ها با temp/gen_hero5.py از refs/hero-home1.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Frontend\Icons;

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

// ── فیلدهای ساده ──
$es_kicker = (string) ($es_f['kicker'] ?? '');
$es_title  = (string) ($es_f['title'] ?? '');
$es_brand  = (string) ($es_f['brand_line'] ?? '');
$es_video  = es_media_url((string) ($es_f['video'] ?? ''));
$es_vs     = max(0, (int) ($es_f['video_scene'] ?? 0));

// ── صحنه‌ها ──
$es_scenes = is_array($es_f['scenes'] ?? null) ? $es_f['scenes'] : [];
$es_scene0 = is_array($es_scenes[$es_vs] ?? null) ? $es_scenes[$es_vs] : [];

// ── لینک‌های سریع چیپ ابری (حداکثر ۸) ──
$es_ql = is_array($es_f['quick_links'] ?? null) ? array_slice($es_f['quick_links'], 0, 8) : [];

// ── نوار پربازدیدترین دسته‌بندی‌ها (رپیتر strip_items) ──
$es_strip = is_array($es_f['strip_items'] ?? null) ? $es_f['strip_items'] : [];

/** فیلتر es_hero_args — توسعه‌پذیری داده‌های هیرو */
$es_args = apply_filters('es_hero_args', ['fields' => $es_f, 'strip' => $es_strip, 'section_id' => (string) ($args['section_id'] ?? 'hero-home1')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_strip = is_array($es_args['strip'] ?? null) ? $es_args['strip'] : $es_strip;
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/hero-home1.html
 */
$F_HEAD = <<<'ESH5'
<section id="home" aria-label="بخش آغازین - طرح خانه ۱ (نوتچ معماری و دسته‌بندی‌ها)" class="relative bg-[#f6f8f7] pt-28 pb-10 sm:pt-32 sm:pb-12 lg:pt-36 lg:pb-14"><div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8"><div data-home1-hero-frame="true" class="relative rounded-[2.25rem] bg-slate-950 sm:rounded-[2.75rem]"><div class="relative h-[470px] w-full rounded-[2.25rem] sm:h-[540px] sm:rounded-[2.75rem] lg:h-[590px]"><div class="pointer-events-none absolute inset-0 overflow-hidden rounded-[2.25rem] sm:rounded-[2.75rem]">
ESH5;
$F_VIDEO_CLOSE = <<<'ESH5'
</div>
ESH5;
$F_ATMOS = <<<'ESH5'
<div data-home1-atmosphere="true" class="pointer-events-none absolute inset-0 overflow-hidden rounded-[2.25rem] transition-opacity duration-500 sm:rounded-[2.75rem] opacity-100"><div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-slate-950/45"></div><div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_52%,rgba(16,185,129,0.22),transparent_62%)]"></div></div>
ESH5;
$F_CTRL = <<<'ESH5'
<div data-home1-video-controls="true" class="absolute top-5 left-5 z-30 flex items-center gap-2 transition-all duration-500 pointer-events-none -translate-y-3 opacity-0"><button type="button" data-home1-video-close="true" class="inline-flex items-center gap-2 rounded-2xl border border-white/30 bg-slate-950/75 px-4 py-2.5 text-xs font-black text-white shadow-xl backdrop-blur-xl transition-all hover:border-emerald-400 hover:bg-slate-900 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pause h-3.5 w-3.5 text-emerald-400" aria-hidden="true"><rect x="14" y="3" width="5" height="18" rx="1"></rect><rect x="5" y="3" width="5" height="18" rx="1"></rect></svg><span>توقف ویدیو و بازگشت به نمای طرح</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-4 w-4 text-slate-300" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button><button type="button" data-home1-video-mute="true" aria-label="قطع یا وصل صدای ویدیو" class="grid h-10 w-10 place-items-center rounded-2xl border border-white/30 bg-slate-950/75 text-white shadow-xl backdrop-blur-xl transition-all hover:border-emerald-400 hover:bg-slate-900 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-volume2 lucide-volume-2 h-4 w-4 text-emerald-400" aria-hidden="true"><path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"></path><path d="M16 9a5 5 0 0 1 0 6"></path><path d="M19.364 18.364a9 9 0 0 0 0-12.728"></path></svg></button></div>
ESH5;
$F_NOTCH = <<<'ESH5'
<div data-home1-overlay-item="true" class="absolute -top-px -right-px z-20 transition-all duration-500 translate-x-0 translate-y-0 opacity-100"><div class="relative w-[240px] max-w-[74vw] rounded-bl-[1.75rem] bg-[#f6f8f7] px-3.5 pt-2.5 pb-3.5 sm:w-[295px] sm:rounded-bl-[2.15rem] sm:px-5 sm:pt-3 sm:pb-4 lg:w-[335px] lg:rounded-bl-[2.35rem] lg:px-6 lg:pt-3.5 lg:pb-4.5"><div class="mb-1 flex items-center gap-1.5 text-[10px] font-extrabold text-emerald-700 sm:text-[11px]"><span class="inline-block h-3 w-1 shrink-0 rounded-full bg-emerald-600"></span><span class="truncate">{ES_KICKER}</span></div><h1 class="text-right text-[13.5px] leading-snug font-black tracking-tight text-slate-900 sm:text-[17px] lg:text-[19.5px]">{ES_TITLE}</h1><div class="mt-1.5 flex items-center justify-start sm:mt-2"><div class="relative inline-flex w-full items-center justify-between gap-2 overflow-hidden rounded-xl border border-emerald-500/25 bg-white px-3 py-1.5 text-right shadow-2xs sm:px-3.5 sm:py-2"><span aria-hidden="true" class="absolute inset-y-1.5 right-1.5 w-1 rounded-full bg-emerald-600"></span><span class="pr-2 text-[12.5px] font-black tracking-tight text-emerald-950 sm:text-[15.5px] lg:text-[17.5px]">{ES_BRAND}</span><span class="hidden rounded-lg bg-emerald-700 px-2 py-0.5 font-mono text-[9px] font-black tracking-wider text-white sm:inline-block">ESE</span></div></div><svg viewBox="0 0 36 36" aria-hidden="true" class="pointer-events-none absolute -left-[35px] top-0 h-9 w-9 text-[#f6f8f7]"><path d="M36 0H0C19.882 0 36 16.118 36 36V0Z" fill="currentColor"></path></svg><svg viewBox="0 0 36 36" aria-hidden="true" class="pointer-events-none absolute -bottom-[35px] right-0 h-9 w-9 text-[#f6f8f7]"><path d="M36 0V36C36 16.118 19.882 0 0 0H36Z" fill="currentColor"></path></svg></div></div>
ESH5;
$F_CHIPS_OPEN = <<<'ESH5'
<div data-home1-overlay-item="true" class="absolute left-4 top-28 z-20 hidden max-w-[540px] flex-col gap-2 transition-all duration-500 md:top-6 md:left-6 md:flex lg:left-7 lg:top-7 translate-x-0 translate-y-0 opacity-100"><div class="flex flex-wrap items-center justify-end gap-1.5">
ESH5;
$F_CHIPS_MID = <<<'ESH5'
</div><div class="flex flex-wrap items-center justify-end gap-1.5">
ESH5;
$F_CHIPS_CLOSE = <<<'ESH5'
</div></div>
ESH5;
$F_PLAY = <<<'ESH5'
<div data-home1-overlay-item="true" class="absolute inset-0 z-10 flex items-center justify-center transition-all duration-500 scale-100 opacity-100"><div class="relative flex items-center justify-center"><div dir="ltr" class="pointer-events-none absolute bottom-[62%] left-1/2 -translate-x-1/2 select-none ps-[0.16em] font-mono text-[68px] xs:text-[84px] leading-none font-black tracking-[0.16em] text-white/95 drop-shadow-[0_18px_42px_rgba(0,0,0,0.7)] sm:text-[124px] lg:text-[148px]">ESE</div><button type="button" data-home1-play-trigger="true" aria-label="پخش کامل تیزر ویدیویی شرکت در همین بخش" class="group relative z-10 flex h-24 w-24 items-center justify-center rounded-full border border-white/45 bg-slate-950/60 text-white shadow-[0_16px_45px_rgba(0,0,0,0.55),0_0_50px_rgba(16,185,129,0.45)] backdrop-blur-xl transition-transform duration-300 hover:scale-105 sm:h-28 sm:w-28 cursor-pointer"><svg viewBox="0 0 100 100" class="pointer-events-none absolute inset-0 m-auto h-[calc(100%-12px)] w-[calc(100%-12px)] animate-[spin_36s_linear_infinite]"><defs><path id="eseCircleTextPath" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0"></path></defs><text class="fill-white/95 font-mono text-[8.2px] font-bold tracking-[2.1px] uppercase"><textPath href="#eseCircleTextPath" startOffset="0%">ERFAN SANAT ESFAHAN • PLAY VIDEO •</textPath></text></svg><span class="grid h-11 w-11 place-items-center rounded-full border border-white/45 bg-emerald-500 text-slate-950 shadow-[0_0_25px_rgba(16,185,129,0.75)] transition-transform duration-300 group-hover:scale-110 sm:h-12 sm:w-12"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-play h-5 w-5 translate-x-[1px] fill-current" aria-hidden="true"><path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"></path></svg></span></button></div></div>
ESH5;
$F_PILL = <<<'ESH5'
<div data-home1-overlay-item="true" class="absolute right-5 bottom-16 z-20 hidden items-center gap-2 transition-all duration-500 lg:flex translate-y-0 opacity-100"><a href="{ES_URL}" data-home1-scene-link="true" class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-slate-950/65 px-3.5 py-2 text-xs font-extrabold text-white backdrop-blur-xl transition-colors hover:border-emerald-400 hover:bg-slate-950/85"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-3.5 w-3.5 text-emerald-400" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg><span data-home1-scene-title="true">{ES_TITLE}</span><span class="text-white/40">·</span><span data-home1-scene-cta="true" class="text-emerald-300">{ES_CTA}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3.5 w-3.5 text-emerald-300" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div>
ESH5;
$F_TABS_OPEN = <<<'ESH5'
<div data-home1-overlay-item="true" class="absolute inset-x-2 bottom-12 z-20 flex items-center justify-start sm:justify-center gap-1 overflow-x-auto no-scrollbar rounded-2xl border border-white/25 bg-slate-950/70 p-1.5 backdrop-blur-xl transition-all duration-500 lg:inset-x-auto lg:bottom-16 lg:left-5 lg:justify-start lg:gap-1.5 translate-y-0 opacity-100" role="tablist" aria-label="انتخاب تیزر معرفی شرکت یا پروژه‌های استانی">
ESH5;
$F_TABS_CLOSE = <<<'ESH5'
</div>
ESH5;
$F_BOTTOM = <<<'ESH5'
<div class="absolute inset-x-0 -bottom-px z-30 flex justify-center"><div data-home1-bottom-tab="true" class="relative rounded-t-[1.85rem] bg-[#f6f8f7] px-8 pt-3.5 pb-1.5 text-center transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] sm:rounded-t-[2.25rem] sm:px-14 sm:pt-4 sm:pb-2 translate-y-0"><svg viewBox="0 0 32 32" aria-hidden="true" data-home1-bottom-fillet="true" class="pointer-events-none absolute -left-[31px] bottom-0 h-8 w-8 text-[#f6f8f7] transition-opacity duration-500 opacity-100"><path d="M32 32H0C17.673 32 32 17.673 32 0V32Z" fill="currentColor"></path></svg><svg viewBox="0 0 32 32" aria-hidden="true" data-home1-bottom-fillet="true" class="pointer-events-none absolute -right-[31px] bottom-0 h-8 w-8 text-[#f6f8f7] transition-opacity duration-500 opacity-100"><path d="M0 32H32C14.327 32 0 17.673 0 0V32Z" fill="currentColor"></path></svg><div class="relative inline-block px-3"><span aria-hidden="true" class="absolute inset-x-1 bottom-1 h-3 rounded-md bg-emerald-200/75"></span><h2 class="relative text-[15px] font-black text-slate-900 sm:text-lg lg:text-[20px]">پربازدیدترین دسته‌بندی‌ها</h2></div></div></div></div></div>
ESH5;
$F_STRIP_OPEN = <<<'ESH5'
<div id="home1-categories" data-home1-categories-grid="true" class="relative z-20 -mt-px grid grid-cols-2 gap-3 bg-[#f6f8f7] pt-3 transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] sm:grid-cols-4 lg:grid-cols-7 lg:gap-3.5 translate-y-0 pb-0">
ESH5;
$F_MID = <<<'ESH5'
</div></div></section>
ESH5;
$IMG_TPL = <<<'ESH5'
<img src="{ES_SRC}" alt="{ES_ALT}" data-home1-scene-img="{ES_I}" class="{ES_CLS}"/>
ESH5;
$VIDEO_TPL = <<<'ESH5'
<video src="{ES_SRC}" poster="{ES_POSTER}" autoPlay="" muted="" loop="" playsInline="" preload="metadata" data-home1-inline-video="true" class="absolute inset-0 h-full w-full object-cover object-center transition-opacity duration-500 ease-out z-10 opacity-100"></video>
ESH5;
$CHIP_TPL = <<<'ESH5'
<a href="{ES_URL}" class="rounded-xl border border-white/30 bg-slate-950/35 px-3.5 py-1.5 text-[11.5px] font-bold text-white backdrop-blur-md transition-all duration-200 hover:border-white hover:bg-white hover:text-slate-950">{ES_LABEL}</a>
ESH5;
$TAB_TPL = <<<'ESH5'
<button type="button" data-home1-scene-btn="{ES_I}" data-scene-title="{ES_TITLE}" data-scene-href="{ES_URL}" data-scene-cta="{ES_CTA}" data-scene-is-video="{ES_IV}" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-2.5 py-1.5 text-[11px] font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_CLS}">{ES_ICON}<span>{ES_NAME}</span></button>
ESH5;
$CARD_TPL = <<<'ESH5'
<a href="{ES_URL}" class="card-light group relative flex flex-col items-center justify-between rounded-2xl px-3 py-5 text-center col-span-1 last:col-span-2 sm:last:col-span-1">{ES_ICON_DIV}<div class="mt-3.5"><div class="text-[12.5px] font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700">{ES_NAME}</div><div class="mt-1 text-[10.5px] font-medium text-slate-500">{ES_SUB}</div></div><div class="mt-3 inline-flex items-center gap-1 border-t border-slate-100 pt-2 text-[10.5px] font-bold text-emerald-700 tabular-nums"><span>{ES_COUNT}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3 w-3" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></div></a>
ESH5;
$ICON_DIV_TPL = <<<'ESH5'
<div class="grid h-13 w-13 place-items-center rounded-2xl border border-emerald-100 bg-emerald-50/60 text-emerald-700 transition-colors duration-300 group-hover:bg-emerald-600 group-hover:text-white">{ES_ICON}</div>
ESH5;


// ═══ رندر ═══
// ۱) هدر سکشن
echo $F_HEAD;

// ۲) تصاویر صحنه‌ها
$es_n = count($es_scenes);
for ($es_i = 0; $es_i < $es_n; $es_i++) {
    $es_row = $es_scenes[$es_i];
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($IMG_TPL, [
        '{ES_SRC}' => es_media_url((string) ($es_row['image'] ?? '')),
        '{ES_ALT}' => (string) ($es_row['title'] ?? ''),
        '{ES_I}'   => (string) $es_i,
        '{ES_CLS}' => $es_i === $es_vs ? 'pointer-events-none absolute inset-0 h-full w-full select-none object-cover object-center transition-all duration-700 ease-out z-10 scale-105 opacity-100' : 'pointer-events-none absolute inset-0 h-full w-full select-none object-cover object-center transition-all duration-700 ease-out z-0 scale-100 opacity-0',
    ]);
}

// ۳) ویدیو درون‌خطی + پوستر صحنه فعال
echo strtr($VIDEO_TPL, [
    '{ES_SRC}'    => $es_video,
    '{ES_POSTER}' => es_media_url((string) ($es_scene0['image'] ?? '')),
]);
echo $F_VIDEO_CLOSE;

// ۴) اتمسفر + کنترل‌های ویدیو
echo $F_ATMOS;
echo $F_CTRL;

// ۵) نوتچ
echo strtr($F_NOTCH, [
    '{ES_KICKER}' => $es_kicker,
    '{ES_TITLE}'  => $es_title,
    '{ES_BRAND}'  => $es_brand,
]);

// ۶) چیپ‌های ابری — دو ردیف چهارتایی
echo $F_CHIPS_OPEN;
foreach (array_slice($es_ql, 0, 4) as $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($CHIP_TPL, [
        '{ES_URL}'   => es_link_url((string) ($es_row['url'] ?? '')),
        '{ES_LABEL}' => (string) ($es_row['label'] ?? ''),
    ]);
}
echo $F_CHIPS_MID;
foreach (array_slice($es_ql, 4, 4) as $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($CHIP_TPL, [
        '{ES_URL}'   => es_link_url((string) ($es_row['url'] ?? '')),
        '{ES_LABEL}' => (string) ($es_row['label'] ?? ''),
    ]);
}
echo $F_CHIPS_CLOSE;

// ۷) پلی‌ترِیگر مرکزی
echo $F_PLAY;

// ۸) پیل scene-link (صحنه فعال)
echo strtr($F_PILL, [
    '{ES_URL}'   => es_link_url((string) ($es_scene0['cta_url'] ?? '')),
    '{ES_TITLE}' => (string) ($es_scene0['title'] ?? ''),
    '{ES_CTA}'   => (string) ($es_scene0['cta_text'] ?? ''),
]);

// ۹) تب‌لیست صحنه‌ها
echo $F_TABS_OPEN;
$es_n = count($es_scenes);
for ($es_i = 0; $es_i < $es_n; $es_i++) {
    $es_row = $es_scenes[$es_i];
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($TAB_TPL, [
        '{ES_I}'     => (string) $es_i,
        '{ES_TITLE}' => (string) ($es_row['title'] ?? ''),
        '{ES_URL}'   => es_link_url((string) ($es_row['cta_url'] ?? '')),
        '{ES_CTA}'   => (string) ($es_row['cta_text'] ?? ''),
        '{ES_IV}'    => $es_i === $es_vs ? 'true' : 'false',
        '{ES_CLS}'   => $es_i === $es_vs ? 'bg-emerald-500 text-slate-950 shadow-xs' : 'text-white/85 hover:bg-white/10 hover:text-white',
        '{ES_ICON}'  => $es_i === $es_vs ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-play h-3 w-3 fill-current" aria-hidden="true"><path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"></path></svg>' : '',
        '{ES_NAME}'  => (string) ($es_row['name'] ?? ''),
    ]);
}
echo $F_TABS_CLOSE;

// ۱۰) تب پایینی + فیلت‌ها
echo $F_BOTTOM;

// ۱۱) گرید دسته‌بندی‌ها
echo $F_STRIP_OPEN;
foreach ($es_strip as $es_row) {
    echo strtr($CARD_TPL, [
        '{ES_URL}'      => es_link_url((string) ($es_row['url'] ?? '')),
        '{ES_ICON_DIV}' => strtr($ICON_DIV_TPL, [
            '{ES_ICON}' => Icons::svg((string) $es_row['icon'], 'lucide lucide-' . (string) $es_row['icon'] . ' h-6 w-6 stroke-[1.75]'),
        ]),
        '{ES_NAME}'  => (string) $es_row['name'],
        '{ES_SUB}'   => (string) $es_row['sub'],
        '{ES_COUNT}' => (string) ($es_row['count_text'] ?? ''),
    ]);
}
echo $F_MID;
