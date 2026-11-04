<?php
/**
 * سکشن محصولات مشابه و مکمل — برش بایت‌دقیق میرور (SEC-C)؛ همان product_cat به‌جز خود محصول
 * فرگمنت با temp/gen_p66.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$es_pid = (int) ($args['pid'] ?? 0);

$ES_CARD_C = <<<'ESP66C'
<a href="{ES_URL}" class="group flex flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-50/50 transition-all duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:bg-white hover:shadow-md"><div class="relative h-48 overflow-hidden"><img src="{ES_IMG}" alt="{ES_ALT}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"/><span class="absolute right-3.5 top-3.5 rounded-full bg-slate-950/75 px-3 py-1 text-[10.5px] font-bold text-white backdrop-blur-md">{ES_CHIP}</span></div><div class="flex flex-1 flex-col justify-between p-5"><div><h3 class="text-base font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h3><p class="mt-1.5 line-clamp-2 text-xs leading-6 text-slate-500">{ES_EXCERPT}</p></div><div class="mt-4 flex items-center justify-between border-t border-slate-200/60 pt-3 text-xs font-extrabold text-emerald-700"><span>مشاهده مشخصات</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></div></div></a>
ESP66C;
$ES_REGION_C = <<<'ESP66D'
<section class="border-t border-slate-200/80 bg-white py-14"><div class="mx-auto max-w-6xl px-5 lg:px-8"><div class="mb-8 flex flex-wrap items-end justify-between gap-4"><div><div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-boxes h-4 w-4" aria-hidden="true"><path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"></path><path d="m7 16.5-4.74-2.85"></path><path d="m7 16.5 5-3"></path><path d="M7 16.5v5.17"></path><path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"></path><path d="m17 16.5-5-3"></path><path d="m17 16.5 4.74-2.85"></path><path d="M17 16.5v5.17"></path><path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"></path><path d="M12 8 7.26 5.15"></path><path d="m12 8 4.74-2.85"></path><path d="M12 13.5V8"></path></svg><span>سایر تولیدات عرفان صنعت</span></div><h2 class="text-2xl font-extrabold text-slate-900">محصولات مشابه و مکمل</h2></div><a href="{ES_SHOP_URL}" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900"><span>مشاهده همه محصولات</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div><div class="grid gap-6 md:grid-cols-3">{ES_CARDS}</div></div>
ESP66D;
$es_terms = get_the_terms($es_pid, 'product_cat');
$es_cat_id = (is_array($es_terms) && isset($es_terms[0]->term_id)) ? (int) $es_terms[0]->term_id : 0;
if ($es_cat_id < 1) {
    return; // بدون دسته → سکشن مخفی
}

$es_q = [
    'post_type'      => 'product',
    'posts_per_page' => 3,
    'post__not_in'   => [$es_pid],
    'tax_query'      => [[
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => $es_cat_id,
    ]],
];
$es_related = get_posts(apply_filters('es_product_related_args', $es_q, $es_pid));
$es_related = array_values(array_filter(is_array($es_related) ? $es_related : [], static fn($p): bool => is_object($p) && (int) ($p->ID ?? 0) > 0));
if ($es_related === []) {
    return;
}

$es_cards = '';
foreach ($es_related as $es_p) {
    $es_p_id = (int) $es_p->ID;
    $es_p_title = (string) get_the_title($es_p_id);
    $es_thumb = (int) get_post_meta($es_p_id, '_thumbnail_id', true);
    $es_img = $es_thumb > 0 ? es_media_url($es_thumb) : es_media_url('assets/images/light-tree.jpg');
    $es_t = get_the_terms($es_p_id, 'product_cat');
    $es_chip = '';
    if (is_array($es_t) && $es_t !== []) {
        foreach ($es_t as $es_tt) {
            if ((int) ($es_tt->term_id ?? 0) !== $es_cat_id) {
                $es_chip = (string) ($es_tt->name ?? '');
                break;
            }
        }
        if ($es_chip === '') {
            $es_chip = (string) ($es_t[0]->name ?? '');
        }
    }
    $es_exc = trim((string) ($es_p->post_excerpt ?? ''));
    if ($es_exc === '') {
        $es_exc = wp_trim_words(strip_tags((string) ($es_p->post_content ?? '')), 20);
    }
    $es_cards .= strtr($ES_CARD_C, [
        '{ES_URL}'     => esc_url((string) get_permalink($es_p_id)),
        '{ES_IMG}'     => esc_url($es_img),
        '{ES_ALT}'     => esc_attr($es_p_title),
        '{ES_CHIP}'    => esc_html($es_chip),
        '{ES_TITLE}'   => esc_html($es_p_title),
        '{ES_EXCERPT}' => esc_html($es_exc),
    ]);
}
echo strtr($ES_REGION_C, [
    '{ES_SHOP_URL}' => esc_url(es_page_url('shop')),
    '{ES_CARDS}'    => $es_cards,
]);
