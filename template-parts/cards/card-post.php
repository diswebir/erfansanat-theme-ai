<?php
/**
 * کارت استاندارد مقاله (کارت blog-item میرور) — برش بایت‌دقیق میرور آرشیو
 * فرگمنت‌ها با temp/gen_cards5.py برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {post: object|null} $args */

$es_post = $args['post'] ?? null;
if (!is_object($es_post)) {
    return;
}
$es_pid = (int) ($es_post->ID ?? 0);

/** فیلتر es_card_args — توسعه‌پذیری کارت */
$es_args = apply_filters('es_card_args', $args, $es_post);
if (is_array($es_args)) {
    $es_post = is_object($es_args['post'] ?? null) ? $es_args['post'] : $es_post;
}

$WRAP = <<<'ESC11B'
<div data-blog-item="true" data-blog-category="{ES_CHIP}" data-blog-search-text="{ES_SEARCH}" class="block"></div>
ESC11B;

$BODY = <<<'ESC11B'
<div custom="{ES_CUSTOM}"><a href="{ES_URL}" class="card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]"><div class="relative h-52 overflow-hidden"><img src="{ES_IMG}" alt="{ES_TITLE}" loading="lazy" class="h-full w-full object-cover transition-transform duration-[1.1s] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110"/><span class="absolute right-4 top-4 rounded-full bg-emerald-950/80 px-3.5 py-1.5 text-[11px] font-bold text-emerald-100 backdrop-blur-md">{ES_CHIP}</span>{ES_FEATURED}<div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-emerald-950/70 to-transparent"></div></div><div class="flex flex-1 flex-col gap-3.5 p-6"><div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[11.5px] font-semibold text-slate-500"><span class="flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path><path d="M8 14h.01"></path><path d="M12 14h.01"></path><path d="M16 14h.01"></path><path d="M8 18h.01"></path><path d="M12 18h.01"></path><path d="M16 18h.01"></path></svg>{ES_DATE}</span><span class="flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock3 lucide-clock-3 h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M12 6v6h4"></path><circle cx="12" cy="12" r="10"></circle></svg>{ES_MIN} دقیقه مطالعه</span></div><h3 class="text-[16.5px] font-extrabold leading-8 text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h3><p class="line-clamp-3 text-[13px] leading-7 text-slate-600">{ES_EXCERPT}</p><div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4"><span class="flex items-center gap-1.5 text-[11.5px] font-semibold text-slate-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg>کارشناسان فنی عرفان صنعت</span><span class="flex items-center gap-1.5 text-[12.5px] font-bold text-emerald-700">ادامه مطلب<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform duration-400 group-hover:-translate-y-0.5 group-hover:-translate-x-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span></div></div></a>
ESC11B;

$FEATURED = <<<'ESC11B'
<span class="absolute left-4 top-4 rounded-full bg-emerald-600 px-3 py-1.5 text-[10.5px] font-bold text-white shadow-lg">★ ویژه</span>
ESC11B;


// ═══ منابع داده ═══
$es_url = (string) get_permalink($es_pid);
$es_title = (string) ($es_post->post_title ?? '');
$es_excerpt = (string) ($es_post->post_excerpt !== '' ? $es_post->post_excerpt : wp_trim_words(strip_tags((string) ($es_post->post_content ?? '')), 34));
$es_img_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);
$es_img = es_media_url($es_img_id > 0 ? $es_img_id : 'assets/images/light-tunnel.jpg');
$es_terms = get_the_terms($es_pid, 'category');
$es_chip = (is_array($es_terms) && isset($es_terms[0]->name)) ? (string) $es_terms[0]->name : '';

// زمان مطالعه: متا es_read_time → برآورد ۲۲۵ واژه در دقیقه
$es_min = (int) get_post_meta($es_pid, 'es_read_time', true);
if ($es_min < 1) {
    $es_words = count(preg_split('/\s+/u', trim(strip_tags((string) ($es_post->post_content ?? '')))) ?: []);
    $es_min = max(1, (int) ceil($es_words / 225));
}
$es_date = es_fa_num(get_the_date('j F Y', $es_post));
$es_featured = !empty($args['featured']);
$es_custom = (string) ($args['custom'] ?? '0');
$es_search = trim($es_title . ' ' . $es_excerpt . ' ' . $es_chip);

echo strtr($WRAP, [
    '{ES_CHIP}'   => esc_html($es_chip),
    '{ES_SEARCH}' => esc_attr($es_search),
]);
echo strtr($BODY, [
    '{ES_CUSTOM}'   => esc_attr($es_custom),
    '{ES_URL}'      => esc_url($es_url),
    '{ES_IMG}'      => esc_url($es_img),
    '{ES_TITLE}'    => esc_attr($es_title),
    '{ES_CHIP}'     => esc_html($es_chip),
    '{ES_FEATURED}' => $es_featured ? $FEATURED : '',
    '{ES_DATE}'     => esc_html($es_date),
    '{ES_MIN}'      => es_fa_num((string) $es_min),
    '{ES_EXCERPT}'  => esc_html($es_excerpt),
]);

