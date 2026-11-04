#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۶.۲ — single-product: هیرو + گالری لایت‌باکس (برش بایت‌دقیق از product/wall-washer.html).
اجرا: python3 temp/gen_p62.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SRC = os.path.join(ROOT, 'uploads/static-mirror/product/wall-washer.html')

def nowdoc(name, body, tag):
    return f"${name} = <<<'{tag}'\n{body}\n{tag};\n"

s = open(SRC, encoding='utf-8').read()

# ─── برش هیرو ───
H_ANCHOR = '<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">'
h0 = s.find(H_ANCHOR)
assert h0 > 0
h_end = s.find('</section>', h0)
assert h_end > 0
HERO = s[h0:h_end + len('</section>')]
# توکن‌های هیرو
HERO = HERO.replace('href="../index.html"', 'href="{ES_HOME_URL}"', 1)
HERO = HERO.replace('href="../shop.html"', 'href="{ES_SHOP_URL}"', 1)
HERO = HERO.replace('href="../product_cat/wall-washer.html"', 'href="{ES_CAT_URL}"', 1)
assert HERO.count('لینیرلایت و والواشر') == 2
HERO = HERO.replace('<a href="{ES_CAT_URL}" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">لینیرلایت و والواشر</a>',
                    '<a href="{ES_CAT_URL}" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">{ES_CAT}</a>', 1)
HERO = HERO.replace('<span>لینیرلایت و والواشر</span>', '<span>{ES_CAT}</span>', 1)
HERO = HERO.replace('<span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">والواشر RGB و تک‌رنگ صنعتی (IP68)</span>',
                    '<span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">{ES_TITLE}</span>', 1)
HERO = HERO.replace('والواشر RGB و تک‌رنگ صنعتی (IP68) </h1>', '{ES_TITLE} </h1>', 1)
HERO = HERO.replace('</h1><p class="mt-4', '</h1>{ES_EN_HTML}<p class="mt-4', 1)
HERO = HERO.replace('بدنه آلومینیوم اکسترود سنگین با شیشه سکوریت، ۱۶ میلیون رنگ و توان ۳۶ وات</p>', '{ES_ZIREONVAN}</p>', 1)
need_h = ['{ES_HOME_URL}', '{ES_SHOP_URL}', '{ES_CAT_URL}', '{ES_CAT}', '{ES_TITLE}', '{ES_EN_HTML}', '{ES_ZIREONVAN}']
assert all(t in HERO for t in need_h), [t for t in need_h if t not in HERO]

# ─── برش گالری ───
G_ANCHOR = '<div custom="0"><div data-gallery-root="true" class="space-y-3.5">'
g0 = s.find(G_ANCHOR)
assert g0 > 0
g_stop = s.find('<div custom="0.06">', g0)
assert g_stop > 0
GAL_RAW = s[g0:g_stop]
GAL = GAL_RAW
# main img + chip + counter
GAL = GAL.replace('<img src="../images/light-element.jpg" alt="والواشر RGB و تک‌رنگ صنعتی (IP68)" data-gallery-main-img="true"',
                  '<img src="{ES_IMG_0}" alt="{ES_TITLE}" data-gallery-main-img="true"', 1)
GAL = GAL.replace('<span class="absolute right-3.5 top-3.5 sm:right-4 sm:top-4 rounded-xl border border-white/25 bg-emerald-950/75 px-3 py-1 sm:px-3.5 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-extrabold text-emerald-100 backdrop-blur-md">لینیرلایت و والواشر</span>',
                  '<span class="absolute right-3.5 top-3.5 sm:right-4 sm:top-4 rounded-xl border border-white/25 bg-emerald-950/75 px-3 py-1 sm:px-3.5 sm:py-1.5 text-[10.5px] sm:text-[11.5px] font-extrabold text-emerald-100 backdrop-blur-md">{ES_CAT}</span>', 1)
GAL = GAL.replace('>۱ / ۴</span>', '>{ES_COUNTER}</span>', 1)
assert '{ES_COUNTER}' in GAL

# ─── فرگمنت‌های تب‌نما ───
T_ACT_ANCHOR = '<button type="button" data-gallery-thumb="0"'
t0 = GAL.find(T_ACT_ANCHOR)
assert t0 > 0
t1 = GAL.find('</button>', t0) + len('</button>')
THUMB_ACT = GAL[t0:t1]
THUMB_ACT = THUMB_ACT.replace('data-gallery-thumb="0"', 'data-gallery-thumb="{ES_I}"', 1)
THUMB_ACT = THUMB_ACT.replace('data-gallery-src="../images/light-element.jpg"', 'data-gallery-src="{ES_SRC}"', 1)
THUMB_ACT = THUMB_ACT.replace('<img src="../images/light-element.jpg" alt="والواشر RGB و تک‌رنگ صنعتی (IP68) - تصویر ۱" loading="lazy"',
                              '<img src="{ES_SRC}" alt="{ES_ALT}" loading="lazy"', 1)
need_ta = ['{ES_I}', '{ES_SRC}', '{ES_ALT}']
assert all(t in THUMB_ACT for t in need_ta), [t for t in need_ta if t not in THUMB_ACT]

# نمونه idle: دومین thumb
t2s = GAL.find('<button type="button" data-gallery-thumb="1"', t1)
assert t2s > 0
t2e = GAL.find('</button>', t2s) + len('</button>')
THUMB_IDLE = GAL[t2s:t2e]
THUMB_IDLE = THUMB_IDLE.replace('data-gallery-thumb="1"', 'data-gallery-thumb="{ES_I}"', 1)
THUMB_IDLE = THUMB_IDLE.replace('data-gallery-src="../images/pexels/pexels-35318900.jpg"', 'data-gallery-src="{ES_SRC}"', 1)
THUMB_IDLE = THUMB_IDLE.replace('<img src="../images/pexels/pexels-35318900.jpg" alt="والواشر RGB و تک‌رنگ صنعتی (IP68) - تصویر ۲" loading="lazy"',
                                '<img src="{ES_SRC}" alt="{ES_ALT}" loading="lazy"', 1)
assert '{ES_I}' in THUMB_IDLE and '{ES_SRC}' in THUMB_IDLE and '{ES_ALT}' in THUMB_IDLE

# HEAD: از ابتدای گالری تا اولین thumb
GAL_HEAD = GAL[:t0]
# MID: از پایان آخرین thumb تا دات‌ها (شامل بستن گرید + لایت‌باکس + کلوز/پرو/نکست + img لایت‌باکس)
last_tb = GAL.rfind('</button>', 0, GAL.find('data-gallery-lightbox="true"'))
# آخرین بستن مربوط به آخرین thumb: پیدا کردن انتهای گرید تصاویر کوچک
grid_close = GAL.find('</div><div data-gallery-lightbox="true"')
assert grid_close > 0
mid_end = GAL.find('<button type="button" data-gallery-lightbox-dot="0"')
assert mid_end > 0
GAL_MID = GAL[grid_close:mid_end]
GAL_MID = GAL_MID.replace('<img src="../images/light-element.jpg" alt="والواشر RGB و تک‌رنگ صنعتی (IP68)" data-gallery-lightbox-img="true"',
                          '<img src="{ES_IMG_0}" alt="{ES_TITLE}" data-gallery-lightbox-img="true"', 1)
assert '{ES_IMG_0}' in GAL_MID
# TAIL: از دات صفرم تا پایان گالری
DOT_ACT_ANCHOR = '<button type="button" data-gallery-lightbox-dot="0"'
d0 = GAL.find(DOT_ACT_ANCHOR)
assert d0 > 0
d0e = GAL.find('</button>', d0) + len('</button>')
DOT_ACT = GAL[d0:d0e]
DOT_ACT = DOT_ACT.replace('data-gallery-lightbox-dot="0"', 'data-gallery-lightbox-dot="{ES_I}"', 1)
DOT_ACT = DOT_ACT.replace('aria-label="تصویر 1"', 'aria-label="{ES_ARIA}"', 1)
DOT_ACT = DOT_ACT.replace('class="h-2 rounded-full transition-all w-7 bg-emerald-400"', 'class="{ES_CLS}"', 1)
assert '{ES_I}' in DOT_ACT and '{ES_ARIA}' in DOT_ACT and '{ES_CLS}' in DOT_ACT
d1s = GAL.find('<button type="button" data-gallery-lightbox-dot="1"', d0e)
assert d1s > 0
d1e = GAL.find('</button>', d1s) + len('</button>')
DOT_IDLE = GAL[d1s:d1e]
DOT_IDLE = DOT_IDLE.replace('data-gallery-lightbox-dot="1"', 'data-gallery-lightbox-dot="{ES_I}"', 1)
DOT_IDLE = DOT_IDLE.replace('aria-label="تصویر 2"', 'aria-label="{ES_ARIA}"', 1)
DOT_IDLE = DOT_IDLE.replace('class="h-2 rounded-full transition-all w-2 bg-white/40 hover:bg-white/70"', 'class="{ES_CLS}"', 1)
assert '{ES_I}' in DOT_IDLE and '{ES_ARIA}' in DOT_IDLE and '{ES_CLS}' in DOT_IDLE
d3 = GAL.find('<button type="button" data-gallery-lightbox-dot="3"', d1e)
assert d3 > 0
d3e = GAL.find('</button>', d3) + len('</button>')
GAL_TAIL = GAL[d3e:]
assert GAL_TAIL == '</div></div></div></div>', repr(GAL_TAIL)

# ─── راستی‌آزمایی بازسازی بایت‌دقیق ───
IMGS = ['../images/light-element.jpg', '../images/pexels/pexels-35318900.jpg', '../images/pexels/pexels-36052966.jpg', '../images/pexels/pexels-29570545.jpg']
FA = ['۱', '۲', '۳', '۴']
LA = ['1', '2', '3', '4']
TITLE = 'والواشر RGB و تک‌رنگ صنعتی (IP68)'
CAT = 'لینیرلایت و والواشر'
reb = (GAL_HEAD.replace('{ES_IMG_0}', IMGS[0]).replace('{ES_TITLE}', TITLE)
               .replace('{ES_CAT}', CAT).replace('{ES_COUNTER}', '۱ / ۴'))
for i in range(4):
    tpl = THUMB_ACT if i == 0 else THUMB_IDLE
    reb += (tpl.replace('{ES_I}', str(i))
               .replace('{ES_SRC}', IMGS[i])
               .replace('{ES_ALT}', f'{TITLE} - تصویر {FA[i]}'))
reb += GAL_MID.replace('{ES_IMG_0}', IMGS[0]).replace('{ES_TITLE}', TITLE)
for i in range(4):
    tpl = DOT_ACT if i == 0 else DOT_IDLE
    reb += (tpl.replace('{ES_I}', str(i))
               .replace('{ES_ARIA}', f'تصویر {LA[i]}')
               .replace('{ES_CLS}', 'h-2 rounded-full transition-all w-7 bg-emerald-400' if i == 0 else 'h-2 rounded-full transition-all w-2 bg-white/40 hover:bg-white/70'))
reb += GAL_TAIL
assert reb == GAL_RAW, f'gallery rebuild mismatch: {len(reb)} vs {len(GAL_RAW)}'
assert HERO.count('{') == HERO.count('}') + 0  # توکن‌ها جفت هستند

# ─── نوشتن فایل‌ها ───
frags = nowdoc('ES_HERO', HERO, 'ESP62H') + nowdoc('ES_GAL_HEAD', GAL_HEAD, 'ESP62A') \
      + nowdoc('ES_THUMB_ACT', THUMB_ACT, 'ESP62B') + nowdoc('ES_THUMB_IDLE', THUMB_IDLE, 'ESP62C') \
      + nowdoc('ES_GAL_MID', GAL_MID, 'ESP62D') + nowdoc('ES_DOT_ACT', DOT_ACT, 'ESP62E') \
      + nowdoc('ES_DOT_IDLE', DOT_IDLE, 'ESP62F') + nowdoc('ES_GAL_TAIL', GAL_TAIL, 'ESP62G')

open(os.path.join(ROOT, 'template-parts/single-product/hero.php'), 'w', encoding='utf-8').write(
"<?php\n"
"/**\n"
" * هیرو محصول تکی — breadcrumb/H1/زیرعنوان/عنوان انگلیسی (برش بایت‌دقیق میرور product/wall-washer.html)\n"
" * فرگمنت با temp/gen_p62.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n"
"$es_title = get_the_title($es_pid);\n"
"$es_cats = get_the_terms($es_pid, 'product_cat');\n"
"$es_cat = is_array($es_cats) && $es_cats !== [] ? (string) ($es_cats[0]->name ?? '') : '';\n"
"$es_cat_url = '';\n"
"if (is_array($es_cats) && $es_cats !== [] && function_exists('get_term_link')) {\n"
"    $es_link = get_term_link($es_cats[0]);\n"
"    $es_cat_url = is_string($es_link) ? $es_link : '';\n"
"}\n"
"$es_zireonvan = (string) get_post_meta($es_pid, 'zireonvan', true);\n"
"$es_en = (string) get_post_meta($es_pid, 'english_titl', true);\n"
"$es_en_html = $es_en !== '' ? '<p dir=\"ltr\" class=\"mt-2 max-w-3xl font-mono text-[12.5px] font-bold tracking-wide text-slate-400\">' . esc_html($es_en) . '</p>' : '';\n\n"
+ frags +
"\necho strtr($ES_HERO, [\n"
"    '{ES_HOME_URL}'  => esc_url(home_url('/')),\n"
"    '{ES_SHOP_URL}'  => esc_url(es_page_url('shop')),\n"
"    '{ES_CAT_URL}'   => esc_url($es_cat_url),\n"
"    '{ES_CAT}'       => esc_html($es_cat),\n"
"    '{ES_TITLE}'     => esc_html($es_title),\n"
"    '{ES_EN_HTML}'   => $es_en_html,\n"
"    '{ES_ZIREONVAN}' => esc_html($es_zireonvan),\n"
"]);\n")

open(os.path.join(ROOT, 'template-parts/single-product/gallery.php'), 'w', encoding='utf-8').write(
"<?php\n"
"/**\n"
" * گالری محصول تکی — main/thumbs/lightbox با هوک‌های data-gallery-* میرور (برش بایت‌دقیق)\n"
" * فرگمنت با temp/gen_p62.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"use ErfanSanat\\Theme\\Data\\ProductGallery;\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n"
"$es_title = get_the_title($es_pid);\n"
"$es_cats = get_the_terms($es_pid, 'product_cat');\n"
"$es_cat = is_array($es_cats) && $es_cats !== [] ? (string) ($es_cats[0]->name ?? '') : '';\n\n"
"$es_ids = [];\n"
"$es_thumb_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);\n"
"if ($es_thumb_id > 0) {\n"
"    $es_ids[] = $es_thumb_id;\n"
"}\n"
"foreach (ProductGallery::get_ids($es_pid) as $es_gid) {\n"
"    if (!in_array($es_gid, $es_ids, true)) {\n"
"        $es_ids[] = $es_gid;\n"
"    }\n"
"}\n"
"$es_imgs = array_map('es_media_url', $es_ids);\n"
"if ($es_imgs === []) {\n"
"    $es_imgs = [es_media_url('assets/images/light-tree.jpg')];\n"
"}\n"
"$es_total = count($es_imgs);\n"
"$es_fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];\n\n"
+ frags +
"\necho strtr($ES_GAL_HEAD, ['{ES_IMG_0}' => esc_url((string) $es_imgs[0]), '{ES_TITLE}' => esc_html($es_title), '{ES_CAT}' => esc_html($es_cat), '{ES_COUNTER}' => es_fa_num(1) . ' / ' . es_fa_num((string) $es_total)]);\n"
"foreach ($es_imgs as $es_i => $es_src) {\n"
"    $es_alt = $es_title . ' - تصویر ' . ($es_fa[$es_i + 1] ?? (string) ($es_i + 1));\n"
"    if ($es_i === 0) {\n"
"        echo strtr($ES_THUMB_ACT, ['{ES_I}' => (string) $es_i, '{ES_SRC}' => esc_url((string) $es_src), '{ES_ALT}' => esc_attr($es_alt)]);\n"
"    } else {\n"
"        echo strtr($ES_THUMB_IDLE, ['{ES_I}' => (string) $es_i, '{ES_SRC}' => esc_url((string) $es_src), '{ES_ALT}' => esc_attr($es_alt)]);\n"
"    }\n"
"}\n"
"echo strtr($ES_GAL_MID, ['{ES_IMG_0}' => esc_url((string) $es_imgs[0]), '{ES_TITLE}' => esc_html($es_title)]);\n"
"foreach ($es_imgs as $es_i => $es_src) {\n"
"    $es_dot_cls = $es_i === 0 ? 'h-2 rounded-full transition-all w-7 bg-emerald-400' : 'h-2 rounded-full transition-all w-2 bg-white/40 hover:bg-white/70';\n"
"    $es_aria = 'تصویر ' . ($es_i + 1);\n"
"    $es_tpl = $es_i === 0 ? $ES_DOT_ACT : $ES_DOT_IDLE;\n"
"    echo strtr($es_tpl, ['{ES_I}' => (string) $es_i, '{ES_ARIA}' => esc_attr($es_aria), '{ES_CLS}' => $es_dot_cls]);\n"
"}\n"
"echo $ES_GAL_TAIL;\n")

# اسکلت single-product.php (تسک‌های ۶.۳ به بعد بین gallery و بسته‌شدن گسترش می‌یابند)
open(os.path.join(ROOT, 'single-product.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * قالب محصول تکی ووکامرس — شل میرور: هیرو + گرید ۱۲ستونه (گالری چپ/خرید راست)
 * فرگمنت‌ها: template-parts/single-product/{hero,gallery,…}.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

$es_pid = (int) get_the_ID();
echo '<main class="bg-slate-50/50 pb-28 lg:pb-0">';
es_get_template('template-parts/single-product/hero.php', ['pid' => $es_pid]);
echo '<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">';
echo '<div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-10">';
echo '<div class="space-y-4 lg:col-span-7">';
es_get_template('template-parts/single-product/gallery.php', ['pid' => $es_pid]);
echo '</div>';
echo '<div class="lg:col-span-5 lg:sticky lg:top-28">';
// ناحیه خرید (تسک ۶.۵) و ادامه محتوا (۶.۳/۶.۴/۶.۶) اینجا تزریق می‌شود
echo '</div>';
echo '</div>';
echo '</section>';
echo '</main>';
get_footer();
""")

print('hero.php:', os.path.getsize(os.path.join(ROOT, 'template-parts/single-product/hero.php')))
print('gallery.php:', os.path.getsize(os.path.join(ROOT, 'template-parts/single-product/gallery.php')))
print('single-product.php:', os.path.getsize(os.path.join(ROOT, 'single-product.php')))
