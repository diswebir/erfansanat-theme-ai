<?php
/**
 * سکشن گالری پروژه‌های صفحه اصلی (projects-gallery) — برش بایت‌دقیق میرور + سه پنل داینامیک
 * پنل‌ها: تب all = آخرین پروژه‌ها؛ تب‌های tunnel/urban = فیلتر بر اساس نام types هر تب.
 * فرگمنت‌ها با temp/gen_gi5.py از refs/projects-gallery.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];
$es_tabs = is_array($es_f['tabs'] ?? null) ? array_values($es_f['tabs']) : [];
if ($es_tabs === []) {
    return; // بدون تب → سکشن مخفی
}

/** فیلتر es_projects_gallery_args — توسعه‌پذیری سکشن گالری پروژه‌ها */
$es_args = apply_filters('es_projects_gallery_args', ['fields' => $es_f, 'tabs' => $es_tabs, 'section_id' => (string) ($args['section_id'] ?? 'projects-gallery')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_tabs = is_array($es_args['tabs'] ?? null) ? array_values($es_args['tabs']) : $es_tabs;
}

/** تعداد کارت هر پنل (میرور: ۵) */
$es_per_tab = max(1, (int) ($es_f['per_tab'] ?? 5));

/** منبع: آخرین پروژه‌ها (فیلتر es_projects_gallery_query_args) */
$es_qargs = apply_filters('es_projects_gallery_query_args', [
    'post_type'      => 'projects',
    'post_status'    => 'publish',
    'numberposts'    => 30,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);
$es_pool = get_posts(is_array($es_qargs) ? $es_qargs : []);

/** گروه‌بندی بر اساس types هر تب (نام‌های تاکسونومی با «|» جدا شده‌اند؛ تب all همه را می‌گیرد) */
$es_grouped = [];
foreach ($es_tabs as $es_tab) {
    if (!is_array($es_tab)) {
        continue;
    }
    $es_key = (string) ($es_tab['key'] ?? '');
    if ($es_key === '') {
        continue;
    }
    if ($es_key === 'all') {
        $es_grouped[$es_key] = array_slice($es_pool, 0, $es_per_tab);
        continue;
    }
    $es_want = array_filter(array_map('trim', explode('|', (string) ($es_tab['types'] ?? ''))));
    $es_rows = [];
    foreach ($es_pool as $es_p) {
        $es_terms = get_the_terms((int) ($es_p->ID ?? 0), 'project_type');
        if (!is_array($es_terms)) {
            continue;
        }
        foreach ($es_terms as $es_t) {
            if (in_array((string) $es_t->name, $es_want, true)) {
                $es_rows[] = $es_p;
                break;
            }
        }
        if (count($es_rows) >= $es_per_tab) {
            break;
        }
    }
    $es_grouped[$es_key] = $es_rows;
}

$es_any = false;
foreach ($es_grouped as $es_rows) {
    if ($es_rows !== []) {
        $es_any = true;
        break;
    }
}
if (!$es_any) {
    return; // بدون پروژه → سکشن مخفی
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/projects-gallery.html
 */
$F_HEAD = <<<'ESG12'
<section aria-label="گالری نمونه‌کارها و پروژه‌های اجراشده عرفان صنعت" class="relative z-10 mx-auto max-w-[1300px] px-4 pt-2 pb-12 sm:px-6 sm:pb-16 lg:px-8"><div class="flex flex-col items-start justify-between gap-4 rounded-2xl border border-slate-200/85 bg-white p-5 sm:flex-row sm:items-center sm:p-6"><div><div class="inline-flex items-center gap-2 text-xs font-extrabold text-emerald-700"><span aria-hidden="true" class="h-3 w-1 rounded-full bg-emerald-600"></span><span>{ES_KICKER}</span></div><h3 class="mt-1 text-lg font-black text-slate-900 sm:text-xl">{ES_TITLE_PRE}<span class="text-emerald-700">{ES_HL}</span></h3></div>
ESG12;
$TAB_OPEN = <<<'ESG12'
<div role="tablist" aria-label="دسته‌بندی نمونه‌کارها" class="inline-flex flex-wrap items-center gap-1.5 rounded-2xl border border-slate-200/85 bg-[#f6f8f7] p-1.5">
ESG12;
$TAB_TPL = <<<'ESG12'
<button type="button" role="tab" aria-selected="{ES_SELECTED}" data-home2-port-tab="{ES_KEY}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_TAB_CLS}"><span>{ES_LABEL}</span><span class="rounded-md px-1.5 py-0.5 text-[10px] font-black tabular-nums bg-white/20 text-white">{ES_COUNT}</span></button>
ESG12;
$TAB_CLOSE = <<<'ESG12'
</div></div>
ESG12;
$CARD_TPL = <<<'ESG12'
<a href="{ES_URL}" class="card-light group flex h-full flex-col justify-between rounded-2xl p-3"><div><div class="relative aspect-[4/3] w-full overflow-hidden rounded-xl bg-slate-900"><img src="{ES_IMG}" alt="{ES_TITLE}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/65 via-transparent to-transparent"></div><span class="absolute bottom-2.5 right-2.5 rounded-md bg-slate-950/55 px-2 py-0.5 text-[10.5px] font-extrabold text-white backdrop-blur-xs">{ES_CITY}</span></div><div class="mt-3 px-1"><div class="text-[11px] font-bold text-emerald-700 tabular-nums">{ES_META}</div><h4 class="mt-1 line-clamp-2 min-h-[2.5rem] text-right text-xs sm:text-[13px] leading-5 font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h4></div></div><div class="mt-3 flex items-center justify-between border-t border-slate-100 px-1 pt-2.5 text-[11px] font-bold text-slate-500 group-hover:text-emerald-700"><span>مشاهده جزئیات پروژه</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3.5 w-3.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></div></a>
ESG12;
$F_TAIL = <<<'ESG12'
<div class="mt-6 flex justify-center"><a href="{ES_ARCHIVE_URL}" class="group inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200/90 bg-white px-6 py-3 text-xs sm:text-sm font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800"><span>{ES_ARCHIVE}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 text-emerald-700 transition-transform group-hover:-translate-y-0.5 group-hover:-translate-x-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div>
ESG12;


echo strtr($F_HEAD, [
    '{ES_KICKER}'    => (string) ($es_f['kicker'] ?? ''),
    '{ES_TITLE_PRE}' => (string) ($es_f['title_pre'] ?? ''),
    '{ES_HL}'        => (string) ($es_f['title_hl'] ?? ''),
]);

// جعبه تب‌ها
echo $TAB_OPEN;
foreach ($es_tabs as $es_ti => $es_tab) {
    if (!is_array($es_tab)) {
        continue;
    }
    $es_key = (string) ($es_tab['key'] ?? '');
    $es_count = count($es_grouped[$es_key] ?? []);
    if ($es_ti === 0) {
        echo strtr($TAB_TPL, ['{ES_KEY}' => esc_attr($es_key), '{ES_SELECTED}' => 'true', '{ES_TAB_CLS}' => 'bg-emerald-700 text-white shadow-xs', '{ES_LABEL}' => esc_html((string) ($es_tab['label'] ?? '')), '{ES_COUNT}' => es_fa_num((string) $es_count)]);
    } else {
        echo strtr($TAB_TPL, ['{ES_KEY}' => esc_attr($es_key), '{ES_SELECTED}' => 'false', '{ES_TAB_CLS}' => 'bg-white text-slate-600 hover:bg-emerald-50 hover:text-emerald-800', '{ES_LABEL}' => esc_html((string) ($es_tab['label'] ?? '')), '{ES_COUNT}' => es_fa_num((string) $es_count)]);
    }
}
echo $TAB_CLOSE;

// پنل‌ها
$es_first = true;
foreach ($es_tabs as $es_tab) {
    if (!is_array($es_tab)) {
        continue;
    }
    $es_key = (string) ($es_tab['key'] ?? '');
    $es_rows = $es_grouped[$es_key] ?? [];
    echo '<div data-home2-port-panel="' . esc_attr($es_key) . '" class="grid mt-5 grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">';
    foreach ($es_rows as $es_p) {
        $es_pid = (int) ($es_p->ID ?? 0);
        $es_terms = get_the_terms($es_pid, 'project_type');
        $es_type = (is_array($es_terms) && isset($es_terms[0]->name)) ? (string) $es_terms[0]->name : '';
        $es_terms_c = get_the_terms($es_pid, 'city');
        $es_city = (is_array($es_terms_c) && isset($es_terms_c[0]->name)) ? (string) $es_terms_c[0]->name : '';
        // سال: متا es_project_year؛ وگرنه استخراج از finish-pro-time
        $es_year = (string) get_post_meta($es_pid, 'es_project_year', true);
        if ($es_year === '') {
            if (preg_match('/([\x{06F0}-\x{06F9}]{4})/u', (string) get_post_meta($es_pid, 'finish-pro-time', true), $es_m) === 1) {
                $es_year = $es_m[1];
            }
        }
        $es_img_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);
        echo strtr($CARD_TPL, [
            '{ES_URL}'   => esc_url((string) get_permalink($es_pid)),
            '{ES_IMG}'   => es_media_url($es_img_id > 0 ? $es_img_id : 'assets/images/light-element.jpg'),
            '{ES_TITLE}' => esc_attr((string) ($es_p->post_title ?? '')),
            '{ES_CITY}'  => esc_html($es_city),
            '{ES_META}'  => esc_html(trim($es_type . ' · سال ' . $es_year, ' ·')),
        ]);
    }
    echo '</div>';
    $es_first = false;
}

echo strtr($F_TAIL, [
    '{ES_ARCHIVE_URL}' => es_page_url('projects'),
    '{ES_ARCHIVE}'     => (string) ($es_f['archive_text'] ?? ''),
]);
echo '>';
