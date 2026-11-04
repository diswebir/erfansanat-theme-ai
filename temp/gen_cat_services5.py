#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۷ — برش بایت‌دقیق catalog + services از ref و تولید قالب‌ها.
اجرا: python3 temp/gen_cat_services5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

def nowdoc(name, body, tag):
    return f"${name} = <<<'{tag}'\n{body}\n{tag};\n"

# ═══════════════ کاتالوگ ═══════════════
h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/catalog.html'), encoding='utf-8').read()
REF_CAT = h
assert h.endswith('</section')
F = h + '>'

F = F.replace('>مرجع مشخصات فنی · کدهای سامانه ستاد · کد ملی کالا GS1</span>', '>{ES_KICKER}</span>')
F = F.replace('<h3 class="mt-2.5 text-xl font-black text-white sm:text-2xl">کاتالوگ جامع محصولات عرفان صنعت اصفهان (ویرایش ۱۴۰۴)</h3>', '<h3 class="mt-2.5 text-xl font-black text-white sm:text-2xl">{ES_TITLE}</h3>')
F = re.sub(r'(<p class="mt-2\.5 max-w-2xl text-right text-xs leading-7 font-medium text-emerald-50/90 sm:text-\[13\.5px\] sm:leading-7">)[^<]*(</p>)', r'\1{ES_DESC}\2', F)
F = F.replace('href="shop.html" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-xs font-black text-emerald-900 transition-colors hover:bg-emerald-50 sm:text-sm"', 'href="{ES_URL}" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-xs font-black text-emerald-900 transition-colors hover:bg-emerald-50 sm:text-sm"')
F = F.replace('<span>مشاهده و دریافت کاتالوگ محصولات</span>', '<span>{ES_BTN}</span>')
F = F.replace('>شامل +۱۲۵ محصول تخصصی · آماده ارائه به شهرداری‌ها</span>', '>{ES_NOTE}</span>')
F = F.replace('<a href="shop.html" class="group relative block h-44 w-64', '<a href="{ES_URL}" class="group relative block h-44 w-64')
F = re.sub(r'(<img src=")images/light-tunnel\.jpg(" alt=")[^"]*(")', r'\1{ES_BG}\2{ES_BG_ALT}\3', F)
F = F.replace('src="images/light-tree.jpg" alt="جلد کاتالوگ محصولات ESE"', 'src="{ES_COVER}" alt="{ES_COVER_ALT}"')
F = F.replace('>نسخه ۱۴۰۴</span>', '>{ES_VERSION}</span>')
F = F.replace('>کاتالوگ فنی و اجرایی</div>', '>{ES_COVER_KICKER}</div>')
F = F.replace('<span>محصولات نورپردازی شهری</span>', '<span>{ES_COVER_TITLE}</span>')

# آیکون download
ip_path = os.path.join(ROOT, 'inc/Frontend/Icons.php')
ip = open(ip_path, encoding='utf-8').read()
for svg_full in re.findall(r'<svg xmlns="http://www\.w3\.org/2000/svg"[^>]*>.*?</svg>', REF_CAT, re.S):
    cm = re.search(r'lucide lucide-download', svg_full)
    if cm and "'download' =>" not in ip:
        inner = re.match(r'<svg [^>]*>(.*)</svg>$', svg_full, re.S).group(1)
        idx = ip.rfind('    ];')
        ip = ip[:idx] + f"        'download' => <<<'ESIX'\n{inner}\nESIX,\n" + ip[idx:]
open(ip_path, 'w', encoding='utf-8').write(ip)

open(os.path.join(ROOT, 'template-parts/sections/catalog.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن catalog — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-catalog.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-catalog.php', get_defined_vars());
""")

cat_php = f"""<?php
/**
 * سکشن دانلود کاتالوگ جامع (catalog) — برش بایت‌دقیق میرور + فیلدهای SectionManager
 * فرگمنت با temp/gen_cat_services5.py از refs/catalog.html برش خورده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {{section_id: string, fields: array<string, mixed>, post_id: int}} $args */

$es_f = $args['fields'] ?? [];

/** فیلتر es_catalog_args — توسعه‌پذیری سکشن کاتالوگ */
$es_args = apply_filters('es_catalog_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'catalog')]);
if (is_array($es_args)) {{
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}}

{nowdoc('ES_CATALOG', F, 'ESC7')}

// آدرس دکمه: فایل PDF در صورت تنظیم؛ وگرنه مسیر فروشگاه
$es_url = (string) ($es_f['file'] ?? '');
$es_url = $es_url !== '' ? es_media_url($es_url) : es_link_url((string) ($es_f['button_url'] ?? ''));

echo strtr($ES_CATALOG, [
    '{{ES_KICKER}}'      => (string) ($es_f['kicker'] ?? ''),
    '{{ES_TITLE}}'       => (string) ($es_f['title'] ?? ''),
    '{{ES_DESC}}'        => (string) ($es_f['desc'] ?? ''),
    '{{ES_URL}}'         => $es_url,
    '{{ES_BTN}}'         => (string) ($es_f['button_text'] ?? ''),
    '{{ES_NOTE}}'        => (string) ($es_f['note'] ?? ''),
    '{{ES_COVER}}'       => es_media_url((string) ($es_f['cover'] ?? '')),
    '{{ES_COVER_ALT}}'   => (string) ($es_f['cover_alt'] ?? ''),
    '{{ES_BG}}'          => es_media_url((string) ($es_f['bg_image'] ?? 'assets/images/light-tunnel.jpg')),
    '{{ES_BG_ALT}}'      => (string) ($es_f['bg_alt'] ?? 'کاتالوگ جامع محصولات عرفان صنعت'),
    '{{ES_VERSION}}'     => (string) ($es_f['version'] ?? ''),
    '{{ES_COVER_KICKER}}' => (string) ($es_f['cover_kicker'] ?? ''),
    '{{ES_COVER_TITLE}}' => (string) ($es_f['cover_title'] ?? ''),
]);
"""
open(os.path.join(ROOT, 'template-parts/home/section-catalog.php'), 'w', encoding='utf-8').write(cat_php)
print('catalog done:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-catalog.php')))

# ═══════════════ خدمات ═══════════════
h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/services.html'), encoding='utf-8').read()
REF_SRV = h
assert h.endswith('</section')

# هدر تا اولین ردیف
i = h.find('<button type="button" data-service-row="0"')
F_HEAD = h[:i]
F_HEAD = F_HEAD.replace('>خدمات مهندسی و اجرایی</span>', '>{ES_KICKER}</span>')
F_HEAD = F_HEAD.replace('خدمات تخصصی <span class="text-emerald-700">نورپردازی شهری</span>', 'خدمات تخصصی <span class="text-emerald-700">{ES_HL}</span>')
F_HEAD = re.sub(r'(<p class="text-sm leading-7 font-medium text-slate-600 sm:text-\[15px\] sm:leading-8">)[^<]*(</p>)', r'\1{ES_EXCERPT}\2', F_HEAD)

# پنل پیش‌نمایش + بدنه
j = h.find('<div class="hidden lg:col-span-6 lg:block" custom="0.1">')
PREVIEW = h[j:]
BODY = h[i:j]

# پنج دکمه ردیف
row_marks = [m.start() for m in re.finditer(r'<button type="button" data-service-row="\d+"', BODY)]
row_marks.append(len(BODY))
rows = [BODY[row_marks[k]:row_marks[k+1]] for k in range(5)]

# نرمال‌سازی ردیف ۰ (active) → قالب توکن‌دار
R = rows[0]
for attr in ['row', 'indicator', 'num', 'heading', 'desc', 'mobile-img', 'row-icon']:
    R = re.sub('data-service-' + attr + '="\\d+"', 'data-service-' + attr + '="{ES_I}"', R)
R = re.sub(r'data-service-title="[^"]*"', 'data-service-title="{ES_TITLE}"', R)
R = re.sub(r'data-service-desc-text="[^"]*"', 'data-service-desc-text="{ES_DESC}"', R)
R = re.sub(r'data-service-img="[^"]*"', 'data-service-img="{ES_IMG}"', R)
R = re.sub(r'data-service-counter="[^"]*"', 'data-service-counter="{ES_COUNTER}"', R)
R = re.sub(r'(data-service-num="\{ES_I\}" class="mt-0\.5 text-xs font-black tabular-nums transition-colors )[^"]*(">)[^<]*(</span>)', r'\1{ES_NUM_CLS}\2{ES_NUM}\3', R)
R = re.sub(r'(data-service-heading="\{ES_I\}" class="text-sm sm:text-base font-extrabold transition-colors )[^"]*(">)', r'\1{ES_HEAD_CLS}\2', R)
R = re.sub(r'(data-service-row="\{ES_I\}" data-service-title="\{ES_TITLE\}" data-service-desc-text="\{ES_DESC\}" data-service-img="\{ES_IMG\}" data-service-counter="\{ES_COUNTER\}" class="group relative flex items-start gap-4 rounded-2xl border p-4 text-right transition-colors duration-200 sm:p-5 cursor-pointer )[^"]*(")', r'\1{ES_ROW_CLS}\2', R)
R = re.sub(r'(data-service-indicator="\{ES_I\}" class="absolute right-0 top-1/2 w-1 -translate-y-1/2 rounded-l-full bg-emerald-600 transition-all duration-200 )[^"]*(")', r'\1{ES_IND_CLS}\2', R)
R = re.sub(r'(data-service-mobile-img="\{ES_I\}" class=")(?:block|hidden)( overflow-hidden lg:hidden")', r'\1{ES_MOB_CLS}\2', R)
R = re.sub(r'(data-service-heading="\{ES_I\}"[^>]*>)[^<]*(</span>)', r'\1{ES_TITLE}\2', R)
R = re.sub(r'(data-service-desc="\{ES_I\}" class="text-xs sm:text-\[13px\] font-medium leading-6 text-slate-600">)[^<]*(</p>)', r'\1{ES_DESC}\2', R)
R = re.sub(r'<img src="images/[^"]*" alt="[^"]*"', '<img src="{ES_IMG}" alt="{ES_TITLE}"', R)
R = re.sub(r'<svg xmlns="http://www\.w3\.org/2000/svg"[^>]*aria-hidden="true" data-service-row-icon="\{ES_I\}">.*?</svg>', '{ES_ICON}', R, flags=re.S)
assert all(t in R for t in ['{ES_ICON}', '{ES_ROW_CLS}', '{ES_IND_CLS}', '{ES_NUM_CLS}', '{ES_HEAD_CLS}', '{ES_MOB_CLS}', '{ES_NUM}', '{ES_TITLE}', '{ES_DESC}', '{ES_IMG}', '{ES_COUNTER}', '{ES_I}'])

# اطمینان: اسکلت ردیف‌ها هم‌الگو
def norm_row(s):
    s = re.sub(r'data-service-[\w-]+="[^"]*"', 'A', s)
    s = re.sub(r'class="[^"]*"', 'C', s)
    s = re.sub(r'src="images/[^"]*"', 'S', s)
    s = re.sub(r'alt="[^"]*"', 'T', s)
    s = re.sub(r'>[^<>]+</', '>X</', s)
    s = re.sub(r'<svg.*?</svg>', 'SVG', s, flags=re.S)
    s = re.sub(r'\d', 'N', s)
    return s
n0 = norm_row(rows[0])
for k in range(1, 5):
    rk = rows[k]
    if k == 4 and rk.endswith('</div>'):
        rk = rk[:-6]  # بستن ستون دکمه‌ها جدا رندر می‌شود
    assert norm_row(rk) == n0, f'row{k} differs'

# پنل پیش‌نمایش → توکن‌ها
P = PREVIEW
P = re.sub(r'data-service-preview-icon="\d+"', 'data-service-preview-icon="{ES_I}"', P)
P = re.sub(r'(data-service-preview-img="true" src=")images/[^"]*(" alt=")[^"]*(")', r'\1{ES_IMG}\2{ES_TITLE}\3', P)
P = re.sub(r'(data-service-preview-counter="true"[^>]*>)[^<]*(</span>)', r'\1{ES_COUNTER}\2', P)
P = re.sub(r'(data-service-preview-title="true"[^>]*>)[^<]*(</span>)', r'\1{ES_TITLE}\2', P)
P = re.sub(r'(data-service-preview-desc="true"[^>]*>)[^<]*(</span>)', r'\1{ES_DESC}\2', P)
ICON_BLOCKS = re.findall(r'(<span data-service-preview-icon="\{ES_I\}" class="(?:block|hidden)">.*?</span>)', P, re.S)
assert len(ICON_BLOCKS) == 5, len(ICON_BLOCKS)
for k, blk in enumerate(ICON_BLOCKS):
    P = P.replace(blk, '{ES_ICON' + str(k) + '}', 1)

# کلاس‌های فعال/غیرفعال
CLS_ROW_ACTIVE = 'border-emerald-500/40 bg-white shadow-sm'
CLS_ROW_IDLE = 'border-slate-200/80 bg-white/60 hover:border-emerald-200 hover:bg-white'
CLS_IND_ACTIVE = 'h-10'
CLS_IND_IDLE = 'h-0'
CLS_NUM_ACTIVE = 'text-emerald-700'
CLS_NUM_IDLE = 'text-slate-400'
CLS_HEAD_ACTIVE = 'text-emerald-950'
CLS_HEAD_IDLE = 'text-slate-800'
CLS_MOB_ACTIVE = 'block'
CLS_MOB_IDLE = 'hidden'

# آیکون‌های جدید در Icons.php
ip = open(ip_path, encoding='utf-8').read()
for svg_full in re.findall(r'<svg xmlns="http://www\.w3\.org/2000/svg"[^>]*>.*?</svg>', REF_SRV, re.S):
    cm = re.search(r'lucide lucide-(landmark|milestone|waypoints) ', svg_full)
    if cm and f"'{cm.group(1)}' =>" not in ip:
        inner = re.match(r'<svg [^>]*>(.*)</svg>$', svg_full, re.S).group(1)
        idx = ip.rfind('    ];')
        ip = ip[:idx] + f"        '{cm.group(1)}' => <<<'ESIX'\n{inner}\nESIX,\n" + ip[idx:]
open(ip_path, 'w', encoding='utf-8').write(ip)

open(os.path.join(ROOT, 'template-parts/sections/services.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن services — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-services.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-services.php', get_defined_vars());
""")

frags = ''.join([
    nowdoc('F_HEAD', F_HEAD, 'ESS7'),
    nowdoc('ROW_TPL', R, 'ESS7'),
    nowdoc('PREVIEW_TPL', P, 'ESS7'),
    nowdoc('F_CLOSE', '>', 'ESS7'),
])

# توکن‌های آیکون پیش‌نمایش — svg کامل هر بلوک میرور حفظ می‌شود
icon_lines = []
for k in range(5):
    blk = ICON_BLOCKS[k].replace('class="block"', 'class="{ES_ICON_CLS}"')
    icon_lines.append(
        "        '{ES_ICON" + str(k) + "}' => isset($es_rows[" + str(k) + "]) ? strtr('" + blk + "', ['{ES_I}' => (string) " + str(k) + ", '{ES_ICON_CLS}' => " + ("'block'" if k == 0 else "'hidden'") + "]) : '',"
    )
icon_tokens_php = '\n'.join(icon_lines)

srv_php = open(os.path.join(ROOT, 'temp/services-template.base.txt'), encoding='utf-8').read()
for name in ['CLS_ROW_ACTIVE', 'CLS_ROW_IDLE', 'CLS_IND_ACTIVE', 'CLS_IND_IDLE', 'CLS_NUM_ACTIVE', 'CLS_NUM_IDLE', 'CLS_HEAD_ACTIVE', 'CLS_HEAD_IDLE', 'CLS_MOB_ACTIVE', 'CLS_MOB_IDLE']:
    srv_php = srv_php.replace('%' + name + '%', eval(name))
srv_php = srv_php.replace('{frags}', frags)
srv_php = srv_php.replace('{ICON_TOKENS}', icon_tokens_php)
open(os.path.join(ROOT, 'template-parts/home/section-services.php'), 'w', encoding='utf-8').write(srv_php)
print('services done:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-services.php')))
