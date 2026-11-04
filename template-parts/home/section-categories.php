<?php
/**
 * سکشن دسته‌بندی تولیدات (categories) — برش بایت‌دقیق میرور + رپیتر کارت‌ها
 * فرگمنت‌ها با temp/gen_categories5.py از refs/categories.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];
$es_cards = is_array($es_f['cards'] ?? null) ? array_values($es_f['cards']) : [];

/** فیلتر es_categories_args — توسعه‌پذیری سکشن دسته‌ها */
$es_args = apply_filters('es_categories_args', ['fields' => $es_f, 'cards' => $es_cards, 'section_id' => (string) ($args['section_id'] ?? 'categories')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_cards = is_array($es_args['cards'] ?? null) ? array_values($es_args['cards']) : $es_cards;
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/categories.html
 */
$F_HEAD = <<<'ESC6'
<section id="products" class="relative z-10 py-12 sm:py-16 lg:py-20" style="scroll-margin-top:5rem"><div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8"><div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between"><div class="flex max-w-2xl flex-col gap-3 items-start text-right"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>{ES_KICKER}</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">منتخب محصولات <span class="text-emerald-700">{ES_HL}</span></h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">{ES_EXCERPT}</p></div></div><div class="shrink-0" custom="0.12"><div class="inline-flex items-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3 shadow-2xs"><span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wifi h-5 w-5" aria-hidden="true"><path d="M12 20h.01"></path><path d="M2 8.82a15 15 0 0 1 20 0"></path><path d="M5 12.859a10 10 0 0 1 14 0"></path><path d="M8.5 16.429a5 5 0 0 1 7 0"></path></svg></span><div class="leading-tight"><div class="text-xs font-extrabold text-slate-900">{ES_F1}</div><div class="mt-0.5 text-[11px] font-medium text-slate-500">{ES_F2}</div></div></div></div></div><div class="mt-8 sm:mt-10 grid gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4">
ESC6;
$CARD_TPL = <<<'ESC6'
<div{ES_SPAN} custom="{ES_DELAY}"><a href="{ES_URL}" class="card-glow group relative flex h-full min-h-[260px] sm:min-h-[295px] flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 p-5 sm:p-6{ES_SIZE}"><img src="{ES_IMG}" alt="{ES_ALT}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/45 to-slate-950/20"></div><div class="relative z-10 flex items-center justify-between gap-2"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-white/25 bg-slate-950/60 text-white backdrop-blur-md transition-colors duration-300 group-hover:border-emerald-400 group-hover:bg-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span><div class="flex items-center gap-2 text-[11px] font-bold text-white/90">{ES_FLAG}<span class="rounded-lg border border-white/20 bg-slate-950/60 px-2 py-0.5 font-mono text-[10.5px] font-bold text-emerald-300 backdrop-blur-xs">{ES_CHIP}</span></div></div><div class="relative z-10 mt-auto pt-12"><div class="text-[11.5px] font-bold text-emerald-300">{ES_SUB}</div><h3 class="mt-1 text-lg font-black tracking-tight text-white sm:text-xl">{ES_TITLE}</h3><div class="mt-2.5 flex items-center justify-between gap-2 border-t border-white/15 pt-2.5 text-[11.5px] font-medium text-slate-300"><span class="truncate">{ES_DESC}</span>{ES_PRICE}</div></div></a></div>
ESC6;
$CTA_CARD = <<<'ESC6'
<div custom="0.18"><div class="card-light flex h-full min-h-[260px] sm:min-h-[295px] flex-col justify-between rounded-3xl p-6"><div class="flex items-center justify-between"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-5 w-5" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg></span><span class="text-xs font-extrabold text-emerald-700">{ES_CTA_BADGE}</span></div><div class="my-4"><h3 class="text-lg font-black leading-snug text-slate-900 sm:text-xl">{ES_CTA_T1}<span class="block text-emerald-700">{ES_CTA_T2}</span></h3><p class="mt-2 text-xs leading-6 font-medium text-slate-600">{ES_CTA_DESC}</p></div><a href="{ES_CTA_URL}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-xs font-black text-white transition-colors hover:bg-emerald-800"><span>{ES_CTA_BTN}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div></div></div></div></section
ESC6;
$FLAG_HTML = <<<'ESC6'
<span class="rounded-lg bg-emerald-600/90 px-2.5 py-1 text-[10.5px] font-extrabold text-white backdrop-blur-xs">{ES_FLAG_TEXT}</span>
ESC6;
$PRICE_HTML = <<<'ESC6'
<span class="shrink-0 font-extrabold text-emerald-300 tabular-nums">{ES_PRICE_TEXT}</span>
ESC6;


// ═══ رندر ═══
echo strtr($F_HEAD, [
    '{ES_KICKER}'  => (string) ($es_f['kicker'] ?? ''),
    '{ES_HL}'      => (string) ($es_f['title_highlight'] ?? ''),
    '{ES_EXCERPT}' => (string) ($es_f['excerpt'] ?? ''),
    '{ES_F1}'      => (string) ($es_f['feature_title'] ?? ''),
    '{ES_F2}'      => (string) ($es_f['feature_sub'] ?? ''),
]);

// شش کارت تولیدات
    $es_delays = ['0.04', '0.08', '0.12', '0.06', '0.1', '0.14'];
foreach ($es_cards as $es_i => $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    $es_flag = trim((string) ($es_row['flag'] ?? ''));
    $es_price = trim((string) ($es_row['price'] ?? ''));
    echo strtr($CARD_TPL, [
        '{ES_SPAN}'  => $es_i === 0 ? ' class="sm:col-span-2"' : '',
        '{ES_DELAY}' => (string) ($es_row['delay'] ?? ($es_delays[$es_i] ?? '0.04')),
        '{ES_URL}'   => es_link_url((string) ($es_row['url'] ?? '')),
        '{ES_IMG}'   => es_media_url((string) ($es_row['image'] ?? '')),
        '{ES_ALT}'   => (string) ($es_row['alt'] ?? ''),
        '{ES_SIZE}'  => $es_i === 0 ? ' min-h-[320px] sm:min-h-[380px]' : ' ',
        '{ES_FLAG}'  => $es_flag === '' ? '' : strtr($FLAG_HTML, ['{ES_FLAG_TEXT}' => $es_flag]),
        '{ES_CHIP}'  => (string) ($es_row['chip'] ?? ''),
        '{ES_SUB}'   => (string) ($es_row['sub'] ?? ''),
        '{ES_TITLE}' => (string) ($es_row['title'] ?? ''),
        '{ES_DESC}'  => (string) ($es_row['desc'] ?? ''),
        '{ES_PRICE}' => $es_price === '' ? '' : strtr($PRICE_HTML, ['{ES_PRICE_TEXT}' => $es_price]),
    ]);
}

// کارت CTA سفارشی‌سازی
echo strtr($CTA_CARD, [
    '{ES_CTA_BADGE}' => (string) ($es_f['cta_badge'] ?? ''),
    '{ES_CTA_T1}'    => (string) ($es_f['cta_title'] ?? ''),
    '{ES_CTA_T2}'    => (string) ($es_f['cta_title2'] ?? ''),
    '{ES_CTA_DESC}'  => (string) ($es_f['cta_desc'] ?? ''),
    '{ES_CTA_URL}'   => es_link_url((string) ($es_f['cta_url'] ?? '')),
    '{ES_CTA_BTN}'   => (string) ($es_f['cta_button'] ?? ''),
]);
echo '>'; // بسته‌های انتهایی داخل CTA_CARD هستند
