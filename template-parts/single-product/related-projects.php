<?php
/**
 * سکشن پروژه‌های اجراشده با محصول — برش بایت‌دقیق میرور (SEC-A)؛ دیتا از متای project-ids
 * فرگمنت با temp/gen_p66.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$es_pid = (int) ($args['pid'] ?? 0);

$ES_CARD = <<<'ESP66A'
<div custom="{ES_CUSTOM}"><a href="{ES_URL}" class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg"><div class="relative h-52 overflow-hidden"><img src="{ES_IMG}" alt="{ES_ALT}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"/><span class="absolute right-3.5 top-3.5 rounded-full bg-slate-950/75 px-3 py-1 text-[11px] font-bold text-white backdrop-blur-md">{ES_CHIP}</span></div><div class="flex flex-1 flex-col justify-between p-5"><div><h3 class="text-base font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h3><p class="mt-1.5 line-clamp-2 text-xs leading-6 text-slate-500">{ES_EXCERPT}</p></div><div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-xs font-bold"><span class="flex items-center gap-1 text-slate-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>{ES_CITY}</span><span class="text-emerald-700">مشاهده پروژه ←</span></div></div></a></div>
ESP66A;
$ES_REGION = <<<'ESP66B'
<section class="mx-auto max-w-6xl px-5 pb-14 lg:px-8"><div class="mb-7 flex flex-wrap items-end justify-between gap-4"><div><div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-3.5 w-3.5" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg><span>نمونه‌کارهای واقعی اجراشده با این محصول</span></div><h2 class="text-2xl font-extrabold text-slate-900">پروژه‌های اجراشده با {ES_TITLE}</h2></div><a href="{ES_ARCHIVE_URL}" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900"><span>مشاهده همه پروژه‌ها</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div><div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">{ES_CARDS}</div>
ESP66B;
$es_ids_raw = trim((string) get_post_meta($es_pid, 'project-ids', true));
$es_ids = array_values(array_filter(array_map('intval', explode(',', $es_ids_raw)), static fn(int $i): bool => $i > 0));
if ($es_ids === []) {
    return; // سکشن خالی مخفی
}
$es_projects = get_posts([
    'post_type' => 'projects',
    'include'   => $es_ids,
    'orderby'   => 'post__in',
]);
$es_projects = array_values(array_filter(is_array($es_projects) ? $es_projects : [], static fn($p): bool => is_object($p) && (int) ($p->ID ?? 0) > 0));
if ($es_projects === []) {
    return;
}

$es_title = get_the_title($es_pid);
$es_cards = '';
foreach ($es_projects as $es_i => $es_p) {
    $es_p_id = (int) $es_p->ID;
    $es_p_title = (string) get_the_title($es_p_id);
    $es_custom = (string) round($es_i * 0.06, 2);
    $es_thumb = (int) get_post_meta($es_p_id, '_thumbnail_id', true);
    $es_img = $es_thumb > 0 ? es_media_url($es_thumb) : es_media_url('assets/images/light-element.jpg');
    $es_terms_t = get_the_terms($es_p_id, 'project_type');
    $es_chip = (is_array($es_terms_t) && isset($es_terms_t[0]->name)) ? (string) $es_terms_t[0]->name : '';
    $es_terms_c = get_the_terms($es_p_id, 'city');
    $es_city = (is_array($es_terms_c) && isset($es_terms_c[0]->name)) ? (string) $es_terms_c[0]->name : '';
    $es_exc = trim((string) ($es_p->post_excerpt ?? ''));
    if ($es_exc === '') {
        $es_exc = wp_trim_words(strip_tags((string) ($es_p->post_content ?? '')), 20);
    }
    $es_cards .= strtr($ES_CARD, [
        '{ES_CUSTOM}'  => $es_custom,
        '{ES_URL}'     => esc_url((string) get_permalink($es_p_id)),
        '{ES_IMG}'     => esc_url($es_img),
        '{ES_ALT}'     => esc_attr($es_p_title),
        '{ES_CHIP}'    => esc_html($es_chip),
        '{ES_TITLE}'   => esc_html($es_p_title),
        '{ES_EXCERPT}' => esc_html($es_exc),
        '{ES_CITY}'    => esc_html($es_city),
    ]);
}
echo strtr($ES_REGION, [
    '{ES_TITLE}'       => esc_html($es_title),
    '{ES_ARCHIVE_URL}' => esc_url(es_page_url('projects')),
    '{ES_CARDS}'       => $es_cards,
]);
