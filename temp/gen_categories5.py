#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۶ — برش بایت‌دقیق سکشن دسته‌بندی‌ها (#products) از ref و تولید:
   - template-parts/sections/categories.php      (رپر)
   - template-parts/home/section-categories.php  (مارک‌آپ + رپیتر کارت‌ها)
   - افزودن آیکون wifi به Icons
   اجرا: python3 temp/gen_categories5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/categories.html'), encoding='utf-8').read()
REF = h
print('ref bytes:', len(h.encode('utf-8')))

def cut(start_marker, end_marker, include_end=False):
    global h
    i = h.find(start_marker)
    assert i >= 0, f'start not found: {start_marker[:60]!r}'
    j = h.find(end_marker, i + len(start_marker))
    assert j >= 0, f'end not found: {end_marker[:60]!r}'
    end = j + (len(end_marker) if include_end else 0)
    frag = h[i:end]
    h = h[:i] + h[end:]
    return frag

# ═══ ۱) هدر + چیپ wifi + باز شدن گرید ═══
F_HEAD = cut('<section id="products"', '<div class="sm:col-span-2" custom="0.04">')
F_HEAD = F_HEAD.replace('>تولیدات تخصصی کارخانه</span>', '>{ES_KICKER}</span>')
F_HEAD = F_HEAD.replace('منتخب محصولات <span class="text-emerald-700">عرفان صنعت اصفهان</span>', 'منتخب محصولات <span class="text-emerald-700">{ES_HL}</span>')
F_HEAD = re.sub(r'(<p class="text-sm leading-7 font-medium text-slate-600 sm:text-\[15px\] sm:leading-8">)[^<]*(</p>)', r'\1{ES_EXCERPT}\2', F_HEAD)
F_HEAD = F_HEAD.replace('>کنترل هوشمند یکپارچه</div>', '>{ES_F1}</div>')
F_HEAD = F_HEAD.replace('>پشتیبانی از پروتکل بی‌سیم NRF و WiFi</div>', '>{ES_F2}</div>')

# ═══ ۲) شش کارت + کارت CTA ═══
marks = [m.start() for m in re.finditer(r'<a href="product/', h)]
_cl = h.find('<div class="card-light flex h-full min-h-[260px]')
assert _cl > marks[5]
cta_i = h.rfind('<div custom="0.18">', 0, _cl)
assert cta_i > marks[5]
F_CARDS = h[:cta_i]          # ۶ کارت (با wrapperهای خود)
CTA_CARD = h[cta_i:]
h = ''
print('F_CARDS bytes:', len(F_CARDS.encode()), 'CTA bytes:', len(CTA_CARD.encode()))

# کارت CTA → توکن
CTA_CARD = CTA_CARD.replace('<h3 class="text-lg font-black leading-snug text-slate-900 sm:text-xl">طراحی و ساخت المان<span class="block text-emerald-700">مطابق طرح اختصاصی شما</span>', '<h3 class="text-lg font-black leading-snug text-slate-900 sm:text-xl">{ES_CTA_T1}<span class="block text-emerald-700">{ES_CTA_T2}</span>')
CTA_CARD = re.sub(r'(<p class="mt-2 text-xs leading-6 font-medium text-slate-600">)[^<]*(</p>)', r'\1{ES_CTA_DESC}\2', CTA_CARD)
CTA_CARD = CTA_CARD.replace('href="contact.html"', 'href="{ES_CTA_URL}"')
CTA_CARD = CTA_CARD.replace('<span>ثبت سفارش اختصاصی</span>', '<span>{ES_CTA_BTN}</span>')
CTA_CARD = CTA_CARD.replace('>ساخت سفارشی</span>', '>{ES_CTA_BADGE}</span>')

# ═══ ۳) کارت‌ها → حلقه PHP (الگو شامل wrapper) ═══
wraps = re.findall(r'<div(?: class="sm:col-span-2")? custom="[^"]+">', F_CARDS)
links = re.findall(r'<a href="product/[^"]*".*?</a></div>', F_CARDS, re.S)
assert len(wraps) == 6 and len(links) == 6, (len(wraps), len(links))
delays_ref = re.findall(r'<div(?: class="sm:col-span-2")? custom="([^"]+)">', F_CARDS)
FLAG_HTML = '<span class="rounded-lg bg-emerald-600/90 px-2.5 py-1 text-[10.5px] font-extrabold text-white backdrop-blur-xs">{ES_FLAG_TEXT}</span>'
PRICE_HTML = '<span class="shrink-0 font-extrabold text-emerald-300 tabular-nums">{ES_PRICE_TEXT}</span>'
T = wraps[0] + links[0]
T = re.sub(r'<div( class="sm:col-span-2")? custom="[^"]+">', '<div{ES_SPAN} custom="{ES_DELAY}">', T, count=1)
T = re.sub(r'href="product/[^"]*"', 'href="{ES_URL}"', T, count=1)
T = re.sub(r'src="images/[^"]*"', 'src="{ES_IMG}"', T, count=1)
T = re.sub(r'alt="[^"]*"', 'alt="{ES_ALT}"', T, count=1)
T = T.replace('p-5 sm:p-6 min-h-[320px] sm:min-h-[380px]', 'p-5 sm:p-6{ES_SIZE}')
T = T.replace('<div class="flex items-center gap-2 text-[11px] font-bold text-white/90"><span class="rounded-lg border', '<div class="flex items-center gap-2 text-[11px] font-bold text-white/90">{ES_FLAG}<span class="rounded-lg border')
T = re.sub(r'(rounded-lg border border-white/20 bg-slate-950/60 px-2 py-0\.5 font-mono text-\[10\.5px\] font-bold text-emerald-300 backdrop-blur-xs">)[^<]*(</span>)', r'\1{ES_CHIP}\2', T)
T = re.sub(r'<div class="text-\[11\.5px\] font-bold text-emerald-300">[^<]*</div>', '<div class="text-[11.5px] font-bold text-emerald-300">{ES_SUB}</div>', T)
T = re.sub(r'(<h3 class="mt-1 text-lg font-black tracking-tight text-white sm:text-xl">)[^<]*(</h3>)', r'\1{ES_TITLE}\2', T)
T = re.sub(r'(<span class="truncate">)[^<]*(</span>)(</div>)', r'\1{ES_DESC}\2{ES_PRICE}\3', T)
assert '{ES_TITLE}' in T and '{ES_PRICE}' in T and '{ES_FLAG}' in T and '{ES_SUB}' in T and '{ES_CHIP}' in T and '{ES_SIZE}' in T and '{ES_SPAN}' in T, 'tokens missing'

# ═══ آیکون‌های جدید (wifi) ═══
new_icons = {}
for svg_full in re.findall(r'<svg xmlns="http://www\.w3\.org/2000/svg"[^>]*>.*?</svg>', REF, re.S):
    cm = re.search(r'lucide lucide-wifi', svg_full)
    if cm:
        new_icons['wifi'] = re.match(r'<svg [^>]*>(.*)</svg>$', svg_full, re.S).group(1)
icons_path = os.path.join(ROOT, 'inc/Frontend/Icons.php')
ip = open(icons_path, encoding='utf-8').read()
for name, inner in new_icons.items():
    assert '{' not in inner and '$' not in inner
    if f"'{name}' =>" not in ip:
        idx = ip.rfind('    ];')
        ip = ip[:idx] + f"        '{name}' => <<<'ESIX'\n{inner}\nESIX,\n" + ip[idx:]
open(icons_path, 'w', encoding='utf-8').write(ip)
print('icons added:', sorted(new_icons))

# ═══ رپر ═══
open(os.path.join(ROOT, 'template-parts/sections/categories.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن categories — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-categories.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-categories.php', get_defined_vars());
""")

def nowdoc(name, body):
    return f"${name} = <<<'ESC6'\n{body}\nESC6;\n"

frags = ''.join([
    nowdoc('F_HEAD', F_HEAD),
    nowdoc('CARD_TPL', T),
    nowdoc('CTA_CARD', CTA_CARD),
    nowdoc('FLAG_HTML', FLAG_HTML),
    nowdoc('PRICE_HTML', PRICE_HTML),
])

PHP = """<?php
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
FRAGBLOCK

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
"""
PHP = PHP.replace('DELAYS_REF', "['" + "', '".join(delays_ref) + "']")
PHP = PHP.replace('FRAGBLOCK', frags)
open(os.path.join(ROOT, 'template-parts/home/section-categories.php'), 'w', encoding='utf-8').write(PHP)
print('delays:', delays_ref)
print('section-categories.php bytes:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-categories.php')))
