<?php
/**
 * سکشن پروژه‌های شاخص (projects) — برش بایت‌دقیق میرور + WP-Query داینامیک
 * فرگمنت‌ها با temp/gen_projects5.py از refs/projects.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];
$es_kicker  = (string) ($es_f['kicker'] ?? '');
$es_hl      = (string) ($es_f['title_highlight'] ?? '');
$es_excerpt = (string) ($es_f['excerpt'] ?? '');
$es_count   = max(1, (int) ($es_f['count'] ?? 3));
$es_picked  = array_values(array_filter(array_map('intval', preg_split('/[\s,،]+/u', (string) ($es_f['handpicked'] ?? '')))));

/** آرگومان‌های کوئری پروژه‌ها — سبک (بدون شمارش کل) */
$es_query_args = [
    'post_type'        => 'projects',
    'posts_per_page'   => $es_count,
    'post_status'      => 'publish',
    'no_found_rows'    => true,
    'suppress_filters' => false,
];
if ($es_picked !== []) {
    $es_query_args['post__in'] = $es_picked;
    $es_query_args['orderby']  = 'post__in';
}

/**
 * فیلتر es_projects_query_args — توسعه‌پذیری کوئری سکشن پروژه‌ها
 */
$es_query_args = (array) apply_filters('es_projects_query_args', $es_query_args, $args);

$es_items = [];
foreach (get_posts($es_query_args) as $es_post) {
    $es_thumb = (int) get_post_meta((int) $es_post->ID, '_thumbnail_id', true);
    $es_terms = get_the_terms((int) $es_post->ID, 'project_type');
    $es_type_names = is_array($es_terms) ? implode(' · ', array_map(static fn($t): string => (string) $t->name, $es_terms)) : '';
    $es_items[] = [
        'url'      => (string) get_permalink($es_post),
        'img'      => es_media_url($es_thumb),
        'alt'      => (string) ($es_post->post_title ?? ''),
        'num'      => es_fa_num(sprintf('%02d', count($es_items) + 1)),
        'types'    => $es_type_names,
        'title'    => (string) ($es_post->post_title ?? ''),
        'excerpt'  => (string) ($es_post->post_excerpt ?? ''),
        'delay'    => rtrim(rtrim(sprintf('%.2f', 0.05 * (count($es_items) + 1)), '0'), '.'),
    ];
}

/** فیلتر es_projects_items — توسعه‌پذیری آیتم‌های رندر */
$es_items = (array) apply_filters('es_projects_items', $es_items, $args);
if ($es_items === []) {
    return; // سکشن خالی → رندر نمی‌شود
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/projects.html
 */
$F_HEAD = <<<'ESP3'
<section id="projects" class="relative z-10 mx-auto max-w-[1300px] scroll-mt-24 px-4 pt-12 pb-8 sm:px-6 sm:pt-16 sm:pb-10 lg:px-8"><div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>{ES_KICKER}</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">پروژه‌های <span class="text-emerald-700">{ES_HL}</span></h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">{ES_EXCERPT}</p></div></div><div class="mt-8 sm:mt-10 grid gap-4 sm:gap-5 lg:grid-cols-5 lg:items-stretch">
ESP3;
$HERO_WRAP_OPEN = <<<'ESP3'
<div class="flex h-full flex-col lg:col-span-3" custom="0.05">
ESP3;
$HERO_TPL = <<<'ESP3'
<a href="{ES_URL}" class="card-glow group relative block h-full w-full overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 min-h-[340px] sm:min-h-[420px] lg:min-h-[540px]"><img src="{ES_IMG}" alt="{ES_ALT}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/35 to-slate-950/15"></div><div class="absolute inset-x-0 top-0 flex items-center justify-between p-5 sm:p-6"><span class="grid h-10 w-10 place-items-center rounded-2xl border border-white/25 bg-slate-950/60 text-white backdrop-blur-md transition-colors duration-300 group-hover:border-emerald-400 group-hover:bg-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4.5 w-4.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span><span class="text-sm font-black tracking-wider text-white/75 tabular-nums">{ES_NUM}</span></div><div class="absolute inset-x-0 bottom-0 flex flex-col gap-1.5 p-5 sm:p-7"><span class="text-xs font-bold text-emerald-300">{ES_TYPES}</span><h3 class="font-black tracking-tight text-white text-xl sm:text-2xl lg:text-3xl">{ES_TITLE}</h3><p class="max-w-lg text-xs sm:text-[13px] font-medium leading-6 text-slate-200/90">{ES_EXCERPT}</p></div></a>
ESP3;
$HERO_WRAP_CLOSE = <<<'ESP3'
</div>
ESP3;
$F_STACK_OPEN = <<<'ESP3'
<div class="flex h-full flex-col gap-4 sm:gap-5 lg:col-span-2">
ESP3;
$ITEM_OPEN_TPL = <<<'ESP3'
<div class="flex flex-1 flex-col" custom="{ES_DELAY}">
ESP3;
$CARD_STACK_TPL = <<<'ESP3'
<a href="{ES_URL}" class="card-glow group relative block h-full w-full overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 min-h-[235px] sm:min-h-[258px]"><img src="{ES_IMG}" alt="{ES_ALT}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/35 to-slate-950/15"></div><div class="absolute inset-x-0 top-0 flex items-center justify-between p-5 sm:p-6"><span class="grid h-10 w-10 place-items-center rounded-2xl border border-white/25 bg-slate-950/60 text-white backdrop-blur-md transition-colors duration-300 group-hover:border-emerald-400 group-hover:bg-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4.5 w-4.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span><span class="text-sm font-black tracking-wider text-white/75 tabular-nums">{ES_NUM}</span></div><div class="absolute inset-x-0 bottom-0 flex flex-col gap-1.5 p-5 sm:p-7"><span class="text-xs font-bold text-emerald-300">{ES_TYPES}</span><h3 class="font-black tracking-tight text-white text-lg sm:text-xl">{ES_TITLE}</h3><p class=" text-xs sm:text-[13px] font-medium leading-6 text-slate-200/90">{ES_EXCERPT}</p></div></a>
ESP3;
$F_TAIL = <<<'ESP3'
</div></div></section>
ESP3;


// ═══ رندر ═══
echo strtr($F_HEAD, [
    '{ES_KICKER}'  => $es_kicker,
    '{ES_HL}'      => $es_hl,
    '{ES_EXCERPT}' => $es_excerpt,
]);

// کارت نخست: بزرگ (col-span-3)
$es_first = array_shift($es_items);
echo $HERO_WRAP_OPEN;
echo strtr($HERO_TPL, [
    '{ES_URL}'      => (string) $es_first['url'],
    '{ES_IMG}'      => (string) $es_first['img'],
    '{ES_ALT}'      => (string) $es_first['alt'],
    '{ES_NUM}'      => (string) $es_first['num'],
    '{ES_TYPES}'    => (string) $es_first['types'],
    '{ES_TITLE}'    => (string) $es_first['title'],
    '{ES_EXCERPT}'  => (string) $es_first['excerpt'],
]);
echo $HERO_WRAP_CLOSE;

// باقی کارت‌ها: پشته (col-span-2)
if ($es_items !== []) {
    echo $F_STACK_OPEN;
    foreach ($es_items as $es_row) {
        echo strtr($ITEM_OPEN_TPL, ['{ES_DELAY}' => (string) $es_row['delay']]);
        echo strtr($CARD_STACK_TPL, [
            '{ES_URL}'      => (string) $es_row['url'],
            '{ES_IMG}'      => (string) $es_row['img'],
            '{ES_ALT}'      => (string) $es_row['alt'],
            '{ES_NUM}'      => (string) $es_row['num'],
            '{ES_TYPES}'    => (string) $es_row['types'],
            '{ES_TITLE}'    => (string) $es_row['title'],
            '{ES_EXCERPT}'  => (string) $es_row['excerpt'],
        ]);
        echo '</div>';
    }
}

echo $F_TAIL;
