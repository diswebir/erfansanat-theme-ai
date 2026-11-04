<?php
/**
 * کارت استاندارد محصول (کارت product-item میرور) — برش بایت‌دقیق میرور آرشیو
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

$WRAP = <<<'ESC11P'
<div data-product-item="true" data-product-category="{ES_CHIP}" data-product-categories="{ES_CATS}" data-product-purchasable="{ES_PUR}" data-product-price="{ES_PRICE_RAW}" data-product-ip="{ES_IP}" data-product-voltage="{ES_VOLT}" data-product-views="{ES_VIEWS_RAW}" data-product-rating="{ES_RATING_RAW}" data-product-search-text="{ES_SEARCH}" class="block"></div>
ESC11P;

$BODY = <<<'ESC11P'
<div custom="{ES_CUSTOM}"><a href="{ES_URL}" class="card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]"><div class="relative h-52 overflow-hidden"><img src="{ES_IMG}" alt="{ES_TITLE}" loading="lazy" class="h-full w-full object-cover transition-transform duration-[1.2s] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110"/><span class="absolute right-3.5 top-3.5 rounded-full bg-emerald-950/80 px-3 py-1 text-[10.5px] font-bold text-emerald-100 backdrop-blur-md">{ES_CHIP}</span>{ES_BADGE}<span dir="ltr" class="absolute bottom-3 left-3.5 rounded-lg bg-white/95 px-2 py-0.5 font-mono text-[10.5px] font-black text-emerald-900 shadow-xs">{ES_IP}</span></div><div class="flex flex-1 flex-col gap-2.5 p-5"><div class="flex items-center justify-between text-[11px] font-bold text-slate-400"><span dir="ltr" class="font-mono text-slate-500">{ES_SKU}</span><span class="flex items-center gap-2.5"><span class="flex items-center gap-1 text-amber-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>{ES_RATING}</span><span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5 text-slate-400" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle></svg>{ES_VIEWS}</span></span></div><div class="flex items-start justify-between gap-2"><h2 class="text-[16px] font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h2><span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700 transition-all duration-400 group-hover:rotate-45 group-hover:bg-emerald-600 group-hover:text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span></div><p class="line-clamp-2 text-[12.5px] leading-6 text-slate-600">{ES_EXCERPT}</p>{ES_FOOTER}
ESC11P;

$FOOT_ON = <<<'ESC11P'
<div class="mt-auto border-t border-slate-100 pt-3.5"><div class="flex items-center justify-between"><div>{ES_OLD}<div class="text-[14px] font-black text-emerald-700">{ES_PRICE} تومان</div></div>{ES_MIN}</div></div></div></a>
ESC11P;

$FOOT_OFF = <<<'ESC11P'
<div class="mt-auto border-t border-slate-100 pt-3.5"><div class="flex items-center justify-between"><span class="text-[12.5px] font-extrabold text-emerald-800">تولید سفارشی / استعلام قیمت</span><span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-800">مشاوره فنی</span></div></div></div></a>
ESC11P;

$es_badge_on = '<span class="absolute left-3.5 top-3.5 rounded-full bg-emerald-600 px-3 py-1 text-[10.5px] font-bold text-white shadow-lg shadow-emerald-600/25 backdrop-blur-md">🛒 خرید آنلاین</span>';
$es_badge_off = '<span class="absolute left-3.5 top-3.5 rounded-full bg-slate-900/75 px-3 py-1 text-[10.5px] font-bold text-amber-200 backdrop-blur-md">استعلام پروژه</span>';

// ═══ منابع داده ═══
$es_url = (string) get_permalink($es_pid);
$es_title = (string) ($es_post->post_title ?? '');
$es_excerpt = (string) ($es_post->post_excerpt !== '' ? $es_post->post_excerpt : wp_trim_words(strip_tags((string) ($es_post->post_content ?? '')), 22));

// تصویر: شاخص → اولین گالری → placeholder میرور
$es_thumb_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);
if ($es_thumb_id < 1) {
    $es_gal = (string) get_post_meta($es_pid, '_product_image_gallery', true);
    if ($es_gal === '') {
        $es_gal = (string) get_post_meta($es_pid, 'galerry_produ', true);
    }
    $es_first = trim(explode(',', $es_gal)[0] ?? '');
    $es_thumb_id = ctype_digit($es_first) && (int) $es_first > 0 ? (int) $es_first : 0;
}
$es_img = es_media_url($es_thumb_id > 0 ? $es_thumb_id : 'assets/images/light-tree.jpg');

// دسته/اتریبیوت‌ها
$es_terms = get_the_terms($es_pid, 'product_cat');
$es_chip = (is_array($es_terms) && isset($es_terms[0]->name)) ? (string) $es_terms[0]->name : '';
$es_cats = implode('|', array_map(static fn($t) => (string) $t->name, is_array($es_terms) ? $es_terms : []));
$es_terms_ip = get_the_terms($es_pid, 'pa_ip');
$es_ip = (is_array($es_terms_ip) && isset($es_terms_ip[0]->name)) ? (string) $es_terms_ip[0]->name : '';
$es_terms_v = get_the_terms($es_pid, 'pa_voltage');
$es_volt = (is_array($es_terms_v) && isset($es_terms_v[0]->name)) ? (string) $es_terms_v[0]->name : '';

// خرید: true_sale !== 'false' → آنلاین (قرارداد فاز ۳)
$es_pur = (string) get_post_meta($es_pid, 'true_sale', true) !== 'false';
$es_price_raw = (string) get_post_meta($es_pid, '_price', true);
$es_price_raw = $es_price_raw === '' ? '0' : $es_price_raw;
$es_regular = (string) get_post_meta($es_pid, '_regular_price', true);
$es_sale = (string) get_post_meta($es_pid, '_sale_price', true);
$es_sku = (string) get_post_meta($es_pid, '_sku', true);
$es_min = (string) get_post_meta($es_pid, 'es_min_qty', true);
$es_views = (int) get_post_meta($es_pid, 'number2-viewc', true);
$es_rating_raw = (string) get_post_meta($es_pid, '_wc_average_rating', true);
$es_price = es_fa_num(number_format((float) ($es_price_raw !== '' ? $es_price_raw : '0')));
$es_old = $es_sale !== '' && $es_regular !== '' && (float) $es_regular > (float) $es_sale ? es_fa_num(number_format((float) $es_regular)) : '';

// فوتر قیمت‌دار (خط قبلی فقط اگر تخفیف) یا استعلامی
if ($es_pur) {
    $es_old_html = $es_old !== '' ? '<div class="text-[11px] font-semibold text-slate-400 line-through">' . $es_old . ' تومان</div>' : '';
    $es_min_html = $es_min !== '' ? '<span class="rounded-lg bg-slate-100 px-2 py-1 text-[10.5px] font-bold text-slate-600">حداقل: ' . es_fa_num($es_min) . '</span>' : '';
    $es_footer = strtr($FOOT_ON, ['{ES_PRICE}' => $es_price, '{ES_OLD}' => $es_old_html, '{ES_MIN}' => $es_min_html]);
    $es_badge = $es_badge_on;
} else {
    $es_footer = strtr($FOOT_OFF, []);
    $es_badge = $es_badge_off;
}

// فاصله‌گذاری ورود (تماس‌دهنده می‌تواند custom بدهد)
$es_custom = (string) ($args['custom'] ?? '0');

$es_search = trim($es_title . ' ' . $es_excerpt . ' ' . $es_chip . ' ' . $es_sku);

echo strtr($WRAP, [
    '{ES_CHIP}'      => esc_html($es_chip),
    '{ES_CATS}'      => esc_html($es_cats),
    '{ES_PUR}'       => $es_pur ? 'true' : 'false',
    '{ES_PRICE_RAW}' => esc_attr($es_price_raw),
    '{ES_IP}'        => esc_attr($es_ip),
    '{ES_VOLT}'      => esc_attr($es_volt),
    '{ES_VIEWS_RAW}' => esc_attr((string) $es_views),
    '{ES_RATING_RAW}' => esc_attr($es_rating_raw),
    '{ES_SEARCH}'    => esc_attr($es_search),
]);
echo strtr($BODY, [
    '{ES_CUSTOM}' => esc_attr($es_custom),
    '{ES_URL}'    => esc_url($es_url),
    '{ES_IMG}'    => esc_url($es_img),
    '{ES_TITLE}'  => esc_attr($es_title),
    '{ES_CHIP}'   => esc_html($es_chip),
    '{ES_BADGE}'  => $es_badge,
    '{ES_IP}'     => esc_html($es_ip),
    '{ES_SKU}'    => esc_html($es_sku),
    '{ES_RATING}' => es_fa_num($es_rating_raw),
    '{ES_VIEWS}'  => es_fa_num((string) $es_views),
    '{ES_EXCERPT}' => esc_html($es_excerpt),
    '{ES_FOOTER}' => $es_footer,
]);

