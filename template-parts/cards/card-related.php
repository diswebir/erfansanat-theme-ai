<?php
/**
 * کارت فشرده مقاله مرتبط — برش بایت‌دقیق کارت‌های «مقالات مرتبط» میرور blog/*.html
 * فرگمنت با temp/gen_p75.py برش خورده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES75_CARD = <<<'ESP75ES75_CARD'
<div custom="{ES_CUSTOM}"><a href="{ES_URL}" class="card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]"><div class="relative h-44 overflow-hidden"><img src="{ES_IMG}" alt="{ES_TITLE}" loading="lazy" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-108"/><span class="absolute right-4 top-4 rounded-full bg-emerald-950/75 px-3 py-1 text-[11px] font-bold text-emerald-100 backdrop-blur-md">{ES_CHIP}</span></div><div class="flex flex-1 flex-col gap-2.5 p-5"><span class="text-[11.5px] font-semibold text-slate-500">{ES_DATE}</span><h4 class="text-[15.5px] font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h4><p class="line-clamp-2 text-[12.5px] leading-6 text-slate-500">{ES_EXCERPT}</p><span class="mt-auto flex items-center gap-1.5 pt-2 text-[12.5px] font-bold text-emerald-700">ادامه مطلب<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform duration-400 group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span></div></a></div>
ESP75ES75_CARD;


$es_post = $args['post'] ?? null;
if (!is_object($es_post)) {
    return;
}
$es_pid = (int) ($es_post->ID ?? 0);
$es_terms = get_the_terms($es_pid, 'category');
$es_chip = (is_array($es_terms) && isset($es_terms[0]->name)) ? (string) $es_terms[0]->name : '';
$es_img_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);
$es_img = es_media_url($es_img_id > 0 ? $es_img_id : 'assets/images/light-tunnel.jpg');
echo strtr($ES75_CARD, [
    '{ES_CUSTOM}'  => esc_attr((string) ($args['custom'] ?? '0')),
    '{ES_URL}'     => esc_url((string) get_permalink($es_pid)),
    '{ES_IMG}'     => esc_url($es_img),
    '{ES_CHIP}'    => esc_html($es_chip),
    '{ES_DATE}'    => esc_html(es_fa_num(get_the_date('j F Y', $es_post))),
    '{ES_TITLE}'   => esc_html((string) ($es_post->post_title ?? '')),
    '{ES_EXCERPT}' => esc_html((string) ($es_post->post_excerpt ?? '')),
]);

