#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۱۱ — سه کارت استاندارد (product/project/post) برش بایت‌دقیق از میرور آرشیوها.
اجرا: python3 temp/gen_cards5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
MIRROR = os.path.join(ROOT, 'uploads/static-mirror')

def nowdoc(name, body, tag):
    return f"${name} = <<<'{tag}'\n{body}\n{tag};\n"

def rd(name):
    return open(os.path.join(MIRROR, name), encoding='utf-8').read()

def tok(s, old, new, count=1):
    assert old in s, f'missing: {old[:60]}'
    return s.replace(old, new, count)

# ═══════════ کارت محصول (shop.html) ═══════════
h = rd('shop.html')
cards = re.findall(r'<div data-product-item="true"[^>]*>.*?</a></div>', h, re.S)
C_ON, C_OFF = cards[0], cards[8]

# پوش اتریبیوت‌ها
WRAP_ON = C_ON[:C_ON.index('class="block">') + len('class="block">')]
W = WRAP_ON
W = re.sub(r'data-product-category="[^"]*"', 'data-product-category="{ES_CHIP}"', W)
W = re.sub(r'data-product-categories="[^"]*"', 'data-product-categories="{ES_CATS}"', W)
W = re.sub(r'data-product-purchasable="[^"]*"', 'data-product-purchasable="{ES_PUR}"', W)
W = re.sub(r'data-product-price="[^"]*"', 'data-product-price="{ES_PRICE_RAW}"', W)
W = re.sub(r'data-product-ip="[^"]*"', 'data-product-ip="{ES_IP}"', W)
W = re.sub(r'data-product-voltage="[^"]*"', 'data-product-voltage="{ES_VOLT}"', W)
W = re.sub(r'data-product-views="[^"]*"', 'data-product-views="{ES_VIEWS_RAW}"', W)
W = re.sub(r'data-product-rating="[^"]*"', 'data-product-rating="{ES_RATING_RAW}"', W)
W = re.sub(r'data-product-search-text="[^"]*"', 'data-product-search-text="{ES_SEARCH}"', W)
W = W + '</div>'  # بستن wrapper کارت

# بدنه (بین class="block"> و </a>)
BODY_ON = C_ON[C_ON.index('class="block">') + len('class="block">'):]
BODY_ON = BODY_ON[:BODY_ON.rfind('</a>') + len('</a>')]
B_OFF = C_OFF[C_OFF.index('class="block">') + len('class="block">'):]
B_OFF = B_OFF[:B_OFF.rfind('</a>') + len('</a>')]
B = BODY_ON
def norm0(s):
    s = re.sub(r'<svg.*?</svg>', 'SVG', s, flags=re.S)
    s = re.sub(r'(custom|href|src|alt|data-product-[\w-]+)="[^"]*"', r'\1="X"', s)
    s = re.sub(r'<span class="absolute left-3\.5 top-3\.5 rounded-full (?:bg-emerald-600|bg-slate-900/75)[^>]*>[^<]*</span>', 'BADGE', s)
    s = re.sub(r'<div class="mt-auto border-t border-slate-100 pt-3\.5">.*?</div></div></a>', 'FOOT', s, flags=re.S)
    s = re.sub(r'>([^<>]+)</', '>X</', s)
    s = re.sub(r'\d', 'N', s)
    return s
assert norm0(B) == norm0(B_OFF), 'product card skeleton differs'
B = re.sub(r'(<div custom=")[^"]*(">)', r'\1{ES_CUSTOM}\2', B)
B = re.sub(r'(<a href=")[^"]*(")', r'\1{ES_URL}\2', B)
B = re.sub(r'(<img src=")images/[^"]*(" alt=")[^"]*(")', r'\1{ES_IMG}\2{ES_TITLE}\3', B)
B = re.sub(r'(<span class="absolute right-3\.5 top-3\.5 rounded-full bg-emerald-950/80 px-3 py-1 text-\[10\.5px\] font-bold text-emerald-100 backdrop-blur-md">)[^<]*(</span>)', r'\1{ES_CHIP}\2', B)
B = re.sub(r'<span class="absolute left-3\.5 top-3\.5 rounded-full (?:bg-emerald-600 px-3 py-1 text-\[10\.5px\] font-bold text-white shadow-lg shadow-emerald-600/25 backdrop-blur-md|bg-slate-900/75 px-3 py-1 text-\[10\.5px\] font-bold text-amber-200 backdrop-blur-md)">[^<]*</span>', '{ES_BADGE}', B)
B = re.sub(r'(<span dir="ltr" class="absolute bottom-3 left-3\.5 rounded-lg bg-white/95 px-2 py-0\.5 font-mono text-\[10\.5px\] font-black text-emerald-900 shadow-xs">)[^<]*(</span>)', r'\1{ES_IP}\2', B)
B = re.sub(r'(<span dir="ltr" class="font-mono text-slate-500">)[^<]*(</span>)', r'\1{ES_SKU}\2', B)
B = re.sub(r'(lucide-star[^>]*>.*?</svg>)[^<]*(</span>)', r'\1{ES_RATING}\2', B, count=1, flags=re.S)
B = re.sub(r'(lucide-eye[^>]*>.*?</svg>)[^<]*(</span>)', r'\1{ES_VIEWS}\2', B, count=1, flags=re.S)
B = re.sub(r'(<h2 class="text-\[16px\] font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700">)[^<]*(</h2>)', r'\1{ES_TITLE}\2', B)
B = re.sub(r'(<p class="line-clamp-2 text-\[12\.5px\] leading-6 text-slate-600">)[^<]*(</p>)', r'\1{ES_EXCERPT}\2', B)
# فوتر: قیمت‌دار یا استعلامی
FOOT_ON = re.search(r'<div class="mt-auto border-t border-slate-100 pt-3\.5">.*?</div></div></a>', B, re.S)
assert FOOT_ON
FOOT_ON = FOOT_ON.group(0)
FOOT_OFF = re.search(r'<div class="mt-auto border-t border-slate-100 pt-3\.5">.*?</div></div></a>', C_OFF[C_OFF.index('class="block">'):], re.S).group(0)
B = B.replace(FOOT_ON, '{ES_FOOTER}')
BADGE_ON = re.search(r'<span class="absolute left-3\.5 top-3\.5 rounded-full bg-emerald-600[^>]*>[^<]*</span>', BODY_ON).group(0)
BADGE_OFF = re.search(r'<span class="absolute left-3\.5 top-3\.5 rounded-full bg-slate-900/75[^>]*>[^<]*</span>', C_OFF[C_OFF.index('class="block">'):]).group(0)
assert all(t in B for t in ['{ES_CUSTOM}', '{ES_URL}', '{ES_IMG}', '{ES_TITLE}', '{ES_CHIP}', '{ES_BADGE}', '{ES_IP}', '{ES_SKU}', '{ES_RATING}', '{ES_VIEWS}', '{ES_EXCERPT}', '{ES_FOOTER}', '{ES_SEARCH}'[:0]])

# ═══════════ کارت پروژه (projects.html) ═══════════
h = rd('projects.html')
pcards = re.findall(r'<div data-project-item="true"[^>]*>.*?</a></div>', h, re.S)
P = pcards[0]
WRAP_P = P[:P.index('class="block">') + len('class="block">')]
W2 = WRAP_P
W2 = re.sub(r'data-project-type="[^"]*"', 'data-project-type="{ES_CHIP}"', W2)
W2 = re.sub(r'data-project-types="[^"]*"', 'data-project-types="{ES_TYPES}"', W2)
W2 = re.sub(r'data-project-location="[^"]*"', 'data-project-location="{ES_CITY}"', W2)
W2 = re.sub(r'data-project-views="[^"]*"', 'data-project-views="{ES_VIEWS_RAW}"', W2)
W2 = re.sub(r'data-project-rating="[^"]*"', 'data-project-rating="{ES_RATING_RAW}"', W2)
W2 = re.sub(r'data-project-search-text="[^"]*"', 'data-project-search-text="{ES_SEARCH}"', W2)
W2 = W2 + '</div>'
BODY_P = P[P.index('class="block">') + len('class="block">'):]
BODY_P = BODY_P[:BODY_P.rfind('</a>') + len('</a>')]
B2 = BODY_P
B2 = re.sub(r'(<div custom=")[^"]*(">)', r'\1{ES_CUSTOM}\2', B2)
B2 = re.sub(r'(<a href=")[^"]*(")', r'\1{ES_URL}\2', B2)
B2 = re.sub(r'(<img src=")images/[^"]*(" alt=")[^"]*(")', r'\1{ES_IMG}\2{ES_TITLE}\3', B2)
B2 = re.sub(r'(<span class="absolute right-4 top-4 rounded-full bg-emerald-950/75 px-3\.5 py-1\.5 text-\[11\.5px\] font-bold text-emerald-100 backdrop-blur-md">)[^<]*(</span>)', r'\1{ES_CHIP}\2', B2)
B2 = re.sub(r'(<h2 class="text-lg font-extrabold leading-7 text-white \[text-shadow:0_2px_12px_rgba\(0,0,0,\.8\)\]">)[^<]*(</h2>)', r'\1{ES_TITLE}\2', B2)
B2 = re.sub(r'(lucide-map-pin h-4 w-4 text-emerald-600" aria-hidden="true">.*?</svg>)([^<]*)(</span>)', r'\1{ES_CITY}\3', B2, count=1, flags=re.S)
B2 = re.sub(r'(lucide-calendar-check-2 h-3\.5 w-3\.5 text-emerald-600" aria-hidden="true">.*?</svg>)[^<]*(</span>)', r'\1{ES_DATE}\2', B2, count=1, flags=re.S)
B2 = re.sub(r'(lucide-star h-3\.5 w-3\.5 fill-amber-400 text-amber-400" aria-hidden="true">.*?</svg>)[^<]*(</span>)', r'\1{ES_RATING}\2', B2, count=1, flags=re.S)
B2 = re.sub(r'(lucide-eye h-3\.5 w-3\.5 text-slate-400" aria-hidden="true">.*?</svg>)[^<]*(</span>)', r'\1{ES_VIEWS}\2', B2, count=1, flags=re.S)
assert all(t in B2 for t in ['{ES_CUSTOM}', '{ES_URL}', '{ES_IMG}', '{ES_TITLE}', '{ES_CHIP}', '{ES_CITY}', '{ES_DATE}', '{ES_RATING}', '{ES_VIEWS}'])
# اسکلت کارت دوم هم‌الگو
B2b = pcards[1][pcards[1].index('class="block">') + len('class="block">'):]
B2b = B2b[:B2b.rfind('</a>') + len('</a>')]
def norm2(s):
    s = re.sub(r'<svg.*?</svg>', 'SVG', s, flags=re.S)
    s = re.sub(r'(custom|href|src|alt|data-project-[\w-]+)="[^"]*"', r'\1="X"', s)
    s = re.sub(r'>([^<>]+)</', '>X</', s)
    s = re.sub(r'\d', 'N', s)
    return s
assert norm2(B2) == norm2(B2b), 'project card skeleton differs'

# ═══════════ کارت مقاله (blog.html) ═══════════
h = rd('blog.html')
bcards = re.findall(r'<div data-blog-item="true"[^>]*>.*?</a></div>', h, re.S)
BP = bcards[0]
WRAP_BP = BP[:BP.index('class="block">') + len('class="block">')]
W3 = WRAP_BP
W3 = re.sub(r'data-blog-category="[^"]*"', 'data-blog-category="{ES_CHIP}"', W3)
W3 = re.sub(r'data-blog-search-text="[^"]*"', 'data-blog-search-text="{ES_SEARCH}"', W3)
W3 = W3 + '</div>'
BODY_BP = BP[BP.index('class="block">') + len('class="block">'):]
BODY_BP = BODY_BP[:BODY_BP.rfind('</a>') + len('</a>')]
B3 = BODY_BP
def norm3raw(s):
    s = re.sub(r'<svg.*?</svg>', 'SVG', s, flags=re.S)
    s = re.sub(r'(custom|href|src|alt|data-blog-[\w-]+)="[^"]*"', r'\1="X"', s)
    s = re.sub(r'<span class="absolute left-4 top-4 rounded-full bg-emerald-600[^>]*>★ ویژه</span>', '', s)
    s = re.sub(r'>([^<>]+)</', '>X</', s)
    s = re.sub(r'\d', 'N', s)
    return s
B3 = re.sub(r'(<div custom=")[^"]*(">)', r'\1{ES_CUSTOM}\2', B3)
B3 = re.sub(r'(<a href=")[^"]*(")', r'\1{ES_URL}\2', B3)
B3 = re.sub(r'(<img src=")images/[^"]*(" alt=")[^"]*(")', r'\1{ES_IMG}\2{ES_TITLE}\3', B3)
B3 = re.sub(r'(<span class="absolute right-4 top-4 rounded-full bg-emerald-950/80 px-3\.5 py-1\.5 text-\[11px\] font-bold text-emerald-100 backdrop-blur-md">)[^<]*(</span>)', r'\1{ES_CHIP}\2', B3)
FEATURED = re.search(r'<span class="absolute left-4 top-4 rounded-full bg-emerald-600 px-3 py-1\.5 text-\[10\.5px\] font-bold text-white shadow-lg">★ ویژه</span>', B3)
assert FEATURED
B3 = B3.replace(FEATURED.group(0), '{ES_FEATURED}')
B3 = re.sub(r'(lucide-calendar-days h-3\.5 w-3\.5 text-emerald-600" aria-hidden="true">.*?</svg>)[^<]*(</span>)', r'\1{ES_DATE}\2', B3, count=1, flags=re.S)
B3 = re.sub(r'(lucide-clock-3 h-3\.5 w-3\.5 text-emerald-600" aria-hidden="true">.*?</svg>)[^<]*(</span>)', r'\1{ES_MIN} دقیقه مطالعه\2', B3, count=1, flags=re.S)
B3 = re.sub(r'(<h3 class="text-\[16\.5px\] font-extrabold leading-8 text-slate-900 transition-colors group-hover:text-emerald-700">)[^<]*(</h3>)', r'\1{ES_TITLE}\2', B3)
B3 = re.sub(r'(<p class="line-clamp-3 text-\[13px\] leading-7 text-slate-600">)[^<]*(</p>)', r'\1{ES_EXCERPT}\2', B3)
assert all(t in B3 for t in ['{ES_CUSTOM}', '{ES_URL}', '{ES_IMG}', '{ES_TITLE}', '{ES_CHIP}', '{ES_FEATURED}', '{ES_DATE}', '{ES_MIN}', '{ES_EXCERPT}'])
B3b = bcards[3][bcards[3].index('class="block">') + len('class="block">'):]
B3b = B3b[:B3b.rfind('</a>') + len('</a>')]
assert norm3raw(BODY_BP) == norm3raw(B3b), 'post card skeleton differs'

# ═══════════ تولید فایل‌های PHP ═══════════
os.makedirs(os.path.join(ROOT, 'template-parts/cards'), exist_ok=True)

def wrapper(section, note, fragments, body):
    frag_php = '\n'.join(fragments)
    return f"""<?php
/**
 * کارت استاندارد {note} — برش بایت‌دقیق میرور آرشیو
 * فرگمنت‌ها با temp/gen_cards5.py برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {{post: object|null}} $args */

$es_post = $args['post'] ?? null;
if (!is_object($es_post)) {{
    return;
}}
$es_pid = (int) ($es_post->ID ?? 0);

/** فیلتر es_card_args — توسعه‌پذیری کارت */
$es_args = apply_filters('es_card_args', $args, $es_post);
if (is_array($es_args)) {{
    $es_post = is_object($es_args['post'] ?? null) ? $es_args['post'] : $es_post;
}}

{frag_php}

{body}
"""

# ── card-product.php ──
product_body = """// ═══ منابع داده ═══
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
"""
open(os.path.join(ROOT, 'template-parts/cards/card-product.php'), 'w', encoding='utf-8').write(
    wrapper('product', 'محصول (کارت product-item میرور)',
        [nowdoc('WRAP', W, 'ESC11P'), nowdoc('BODY', B, 'ESC11P'),
         nowdoc('FOOT_ON', FOOT_ON, 'ESC11P'), nowdoc('FOOT_OFF', FOOT_OFF, 'ESC11P'),
         "$es_badge_on = " + repr(BADGE_ON) + ";", "$es_badge_off = " + repr(BADGE_OFF) + ";"],
        product_body).replace("$BADGE_ON", "ES_BADGE_ON").replace("$BADGE_OFF", "ES_BADGE_OFF")
)
# فوتر قیمت: توکن‌های sub
fp = open(os.path.join(ROOT, 'template-parts/cards/card-product.php'), encoding='utf-8').read()
foot_on_php = FOOT_ON
foot_on_php = foot_on_php.replace('<div class="text-[11px] font-semibold text-slate-400 line-through">۱۱۲,۰۰۰ تومان</div>', '{ES_OLD}')
foot_on_php = foot_on_php.replace('<div class="text-[14px] font-black text-emerald-700">۹۸,۰۰۰ تومان</div>', '<div class="text-[14px] font-black text-emerald-700">{ES_PRICE} تومان</div>')
foot_on_php = foot_on_php.replace('<span class="rounded-lg bg-slate-100 px-2 py-1 text-[10.5px] font-bold text-slate-600">حداقل: ۵۰ عدد</span>', '{ES_MIN}')
fp = fp.replace(nowdoc('FOOT_ON', FOOT_ON, 'ESC11P'), nowdoc('FOOT_ON', foot_on_php, 'ESC11P'))
open(os.path.join(ROOT, 'template-parts/cards/card-product.php'), 'w', encoding='utf-8').write(fp)

# ── card-project.php ──
project_body = """// ═══ منابع داده ═══
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
$es_search = trim($es_title . ' ' . $es_city . ' ' . $es_chip);

$es_custom = (string) ($args['custom'] ?? '0');

echo strtr($WRAP, [
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
"""
open(os.path.join(ROOT, 'template-parts/cards/card-project.php'), 'w', encoding='utf-8').write(
    wrapper('project', 'پروژه (کارت project-item میرور)',
        [nowdoc('WRAP', W2, 'ESC11J'), nowdoc('BODY', B2, 'ESC11J')],
        project_body)
)

# ── card-post.php ──
post_body = """// ═══ منابع داده ═══
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
    $es_words = count(preg_split('/\\s+/u', trim(strip_tags((string) ($es_post->post_content ?? '')))) ?: []);
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
"""
open(os.path.join(ROOT, 'template-parts/cards/card-post.php'), 'w', encoding='utf-8').write(
    wrapper('post', 'مقاله (کارت blog-item میرور)',
        [nowdoc('WRAP', W3, 'ESC11B'), nowdoc('BODY', B3, 'ESC11B'),
         nowdoc('FEATURED', FEATURED.group(0), 'ESC11B')],
        post_body)
)
print('cards done:',
      os.path.getsize(os.path.join(ROOT, 'template-parts/cards/card-product.php')),
      os.path.getsize(os.path.join(ROOT, 'template-parts/cards/card-project.php')),
      os.path.getsize(os.path.join(ROOT, 'template-parts/cards/card-post.php')))
