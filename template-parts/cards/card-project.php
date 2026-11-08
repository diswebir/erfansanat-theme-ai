<?php
/**
 * کارت استاندارد پروژه (کارت project-item میرور) — برش بایت‌دقیق میرور آرشیو
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

$WRAP = <<<'ESC11J'
<div data-project-item="true" data-project-type="{ES_CHIP}" data-project-types="{ES_TYPES}" data-project-location="{ES_CITY}" data-project-views="{ES_VIEWS_RAW}" data-project-rating="{ES_RATING_RAW}" data-project-search-text="{ES_SEARCH}" class="block"></div>
ESC11J;

$BODY = <<<'ESC11J'
<div custom="{ES_CUSTOM}"><a href="{ES_URL}" class="card-glow group relative block overflow-hidden rounded-[1.5rem] border border-emerald-100 bg-slate-900 shadow-sm"><div class="relative h-60 overflow-hidden"><img src="{ES_IMG}" alt="{ES_TITLE}" loading="lazy" class="h-full w-full object-cover transition-transform duration-[1.2s] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110"/><div class="image-overlay absolute inset-0"></div><span class="absolute right-4 top-4 rounded-full bg-emerald-950/75 px-3.5 py-1.5 text-[11.5px] font-bold text-emerald-100 backdrop-blur-md">{ES_CHIP}</span><span class="absolute left-4 top-4 grid h-10 w-10 place-items-center rounded-full border border-white/30 bg-emerald-950/70 backdrop-blur-md transition-all duration-500 group-hover:rotate-45 group-hover:bg-emerald-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4.5 w-4.5 text-white" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span><div class="absolute inset-x-0 bottom-0 p-5"><h2 class="text-lg font-extrabold leading-7 text-white [text-shadow:0_2px_12px_rgba(0,0,0,.8)]">{ES_TITLE}</h2></div></div><div class="flex flex-wrap items-center justify-between gap-2 bg-white px-5 py-4"><span class="flex items-center gap-1.5 text-[12.5px] font-bold text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>{ES_CITY}</span><div class="flex items-center gap-3 text-[11.5px] font-bold text-slate-500"><span class="flex items-center gap-1 text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-check2 lucide-calendar-check-2 h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><path d="M21 14V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8"></path><path d="M3 10h18"></path><path d="m16 20 2 2 4-4"></path></svg>{ES_DATE}</span><span class="flex items-center gap-1 text-amber-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>{ES_RATING}</span><span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5 text-slate-400" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle></svg>{ES_VIEWS}</span></div></div></a>
ESC11J;


// ═══ منابع داده ═══
$es_url = (string) get_permalink($es_pid);
$es_title = (string) ($es_post->post_title ?? '');
$es_img_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);
$es_img = es_media_url($es_img_id > 0 ? $es_img_id : 'assets/images/light-element.jpg');

$es_terms_t = get_the_terms($es_pid, 'project_type');
$es_names = is_array($es_terms_t) ? array_map(static fn($t) => (string) $t->name, $es_terms_t) : [];
$es_chip = $es_names[0] ?? '';
$es_types = implode('|', $es_names);
$es_terms_c = get_the_terms($es_pid, 'city');
$es_city = (is_array($es_terms_c) && isset($es_terms_c[0]->name)) ? (string) $es_terms_c[0]->name : '';
$es_date = (string) get_post_meta($es_pid, 'finish-pro-time', true);
$es_views = (int) get_post_meta($es_pid, 'number2-viewc', true);
$es_rating_raw = (string) get_post_meta($es_pid, 'es_rating', true);
$es_client = (string) get_post_meta($es_pid, 'es_client', true);
$es_search = trim($es_title . ' ' . $es_city . ' ' . $es_chip . ($es_client !== '' ? ' ' . $es_client : ''));

$es_custom = (string) ($args['custom'] ?? '0');
$es_nested = !empty($args['nested']);
$es_wrap = $es_nested ? preg_replace('~</div>$~', '', $WRAP) : $WRAP;

echo strtr($es_wrap, [
    '{ES_CHIP}'       => esc_html($es_chip),
    '{ES_TYPES}'      => esc_html($es_types),
    '{ES_CITY}'       => esc_attr($es_city),
    '{ES_VIEWS_RAW}'  => esc_attr((string) $es_views),
    '{ES_RATING_RAW}' => esc_attr($es_rating_raw),
    '{ES_SEARCH}'     => esc_attr($es_search),
]);
echo strtr($BODY, [
    '{ES_CUSTOM}' => esc_attr($es_custom),
    '{ES_URL}'    => esc_url($es_url),
    '{ES_IMG}'    => esc_url($es_img),
    '{ES_TITLE}'  => esc_attr($es_title),
    '{ES_CHIP}'   => esc_html($es_chip),
    '{ES_CITY}'   => esc_html($es_city),
    '{ES_DATE}'   => esc_html($es_date),
    '{ES_RATING}' => es_fa_num($es_rating_raw),
    '{ES_VIEWS}'  => es_fa_num((string) $es_views),
]);
if ($es_nested) {
    echo '</div>';
}

