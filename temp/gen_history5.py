#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۵ — برش بایت‌دقیق سکشن history از ref و تولید:
   - template-parts/sections/history.php      (رپر)
   - template-parts/home/section-history.php  (مارک‌آپ + رپیتر مراحل)
   اجرا: python3 temp/gen_history5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/history.html'), encoding='utf-8').read()
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

# ═══ ۱) هدر سکشن + هدر استیج + پیشرفت + باز شدن تبع‌لیست ═══
F_HEAD = cut('<section data-history-scroll-track="true"', '<button type="button" role="tab"')
# توکن‌ها
F_HEAD = F_HEAD.replace('data-history-total="6"', 'data-history-total="{ES_TOTAL}"')
F_HEAD = F_HEAD.replace('>مسیر رشد و تکامل صنعتی</span>', '>{ES_KICKER}</span>')
F_HEAD = F_HEAD.replace('تاریخچه و دستاوردهای <span class="text-emerald-700">عرفان صنعت اصفهان (۱۳۸۰ تا ۱۴۰۵)</span>', 'تاریخچه و دستاوردهای <span class="text-emerald-700">{ES_HL}</span>')
F_HEAD = re.sub(r'(<p class="text-sm leading-7 font-medium text-slate-600 sm:text-\[15px\] sm:leading-8">)[^<]*(</p>)', r'\1{ES_EXCERPT}\2', F_HEAD)
F_HEAD = F_HEAD.replace('>گام ۱ از ۶ (سال ۱۳۸۰)</span>', '>گام {ES_N1} از {ES_TOTAL} (سال {ES_YEAR1})</span>')
F_HEAD = F_HEAD.replace('style="width:17%"', 'style="width:{ES_WIDTH}%"')

# ═══ ۲) دکمه‌های تب ═══
TAB_ACTIVE = cut('<button type="button" role="tab"', '</button>', include_end=True)
TAB_IDLE = cut('<button type="button" role="tab"', '</button>', include_end=True)
for _ in range(4):
    cut('<button type="button" role="tab"', '</button>', include_end=True)
# نرمال‌سازی دو الگو برای مقایسه
def norm_tab(s):
    s = re.sub(r'data-home2-history-btn="\d+"', 'IDX', s)
    s = re.sub(r'aria-selected="[^"]*"', 'SEL', s)
    s = re.sub(r'tabular-nums">[^<]*</span>', 'Y</span>', s)
    s = re.sub(r'(rounded-full[^>]*>)[^<]*(</span>)', r'\1N\2', s)
    s = re.sub(r'class="group flex[^"]*"', 'CLS', s)
    s = re.sub(r'bg-(?:white/20|slate-200/80) text-(?:white|slate-600)', 'NUMCLS', s)
    s = re.sub(r'<span class="mt-1 line-clamp-1 text-\[10\.5px\] font-bold [^"]*">[^<]*</span>', 'NAME', s)
    return s
assert norm_tab(TAB_ACTIVE) == norm_tab(TAB_IDLE), 'الگوی دکمه‌ها متفاوت'
CLS_ACTIVE = re.search(r'class="(group flex[^"]*)"', TAB_ACTIVE).group(1)
CLS_IDLE = re.search(r'class="(group flex[^"]*)"', TAB_IDLE).group(1)
NUMCLS_ACTIVE = re.search(r'tabular-nums (bg-white/20 text-white)"', TAB_ACTIVE).group(1)
NUMCLS_IDLE = re.search(r'tabular-nums (bg-slate-200/80 text-slate-600)"', TAB_IDLE).group(1)
NAME_ACTIVE = re.search(r'<span class="mt-1 line-clamp-1 text-\[10\.5px\] font-bold ([^"]*)">([^<]*)</span>', TAB_ACTIVE, re.S)
NAME_CLS_A, NAME_CLS_I = NAME_ACTIVE.group(1), re.search(r'<span class="mt-1 line-clamp-1 text-\[10\.5px\] font-bold ([^"]*)">', TAB_IDLE).group(1)
# توکن‌گذاری دکمه فعال
TAB_ACTIVE_T = re.sub(r'data-home2-history-btn="\d+"', 'data-home2-history-btn="{ES_I}"', TAB_ACTIVE)
TAB_ACTIVE_T = TAB_ACTIVE_T.replace('aria-selected="true"', 'aria-selected="{ES_SEL}"')
TAB_ACTIVE_T = re.sub(r'tabular-nums">[^<]*</span>', 'tabular-nums">{ES_YEAR}</span>', TAB_ACTIVE_T)
TAB_ACTIVE_T = re.sub(r'(rounded-full[^>]*>)[^<]*(</span>)', r'\1{ES_NUM}\2', TAB_ACTIVE_T)
TAB_ACTIVE_T = TAB_ACTIVE_T.replace(f'class="{CLS_ACTIVE}"', 'class="{ES_CLS}"')
TAB_ACTIVE_T = TAB_ACTIVE_T.replace(NUMCLS_ACTIVE, '{ES_NUMCLS}')
TAB_ACTIVE_T = re.sub(r'(class="mt-1 line-clamp-1 text-\[10\.5px\] font-bold )[^"]*(">)[^<]*(</span>)', r'\1{ES_NAMECLS}\2{ES_NAME}\3', TAB_ACTIVE_T)
F_TABS_CLOSE = cut('</div>', '<div data-home2-history-panel="0"')

# ═══ ۳) پنل‌ها — ۶ پنل هم‌اسکلت ═══
PANEL0 = cut('<div data-home2-history-panel="0"', '<div data-home2-history-panel="1"')
for _ in range(3):
    cut('<div data-home2-history-panel="', '<div data-home2-history-panel="')
# پنل ۵ + بسته‌ها: پنل ۵ در حلقه از الگو رندر می‌شود؛ فقط بسته‌ها نگه داشته می‌شود
TAIL = '</div></div></div></section'
assert h.endswith(TAIL), repr(h[-60:])
h = h[:-len(TAIL)]  # حذف پنل ۵ + بستهٔ خودش
F_TAIL = '</div></div></section' + '>'
h = ''
print('F_TAIL:', repr(F_TAIL))

# استخراج متون پنل ۰ به ترتیب
texts0 = [t for t in re.findall(r'>([^<>]+)<', PANEL0) if t.strip()]
assert len(texts0) == 14, len(texts0)
TOKENS = ['{ES_YEAR_CH}', '{ES_BADGE}', '{ES_TITLE}', '{ES_SUBL}', '{ES_BODY}', '{ES_S1L}', '{ES_S1V}', '{ES_S2L}', '{ES_S2V}', '{ES_STEP}', '{ES_HINT}', '{ES_PREV}', '{ES_NEXT}', '{ES_CAP}']
PANEL_T = PANEL0
for t, tok in zip(texts0, TOKENS):
    assert t in PANEL_T, t[:40]
    PANEL_T = PANEL_T.replace(f'>{t}<', f'>{tok}<', 1)
PANEL_T = re.sub(r'data-home2-history-panel="\d+"', 'data-home2-history-panel="{ES_I}"', PANEL_T, count=1)
PANEL_T = PANEL_T.replace('class="grid card-light', 'class="{ES_CLS} card-light', 1)
PANEL_T = re.sub(r'src="images/[^"]*"', 'src="{ES_IMG}"', PANEL_T, count=1)
PANEL_T = re.sub(r'alt="[^"]*"', 'alt="{ES_ALT}"', PANEL_T, count=1)
assert '{ES_' in PANEL_T

# ═══ استخراج فیلدهای ۶ مرحله → JSON برای اسکیم ═══
import json
hraw = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/history.html'), encoding='utf-8').read()
marks = [m.start() for m in re.finditer(r'<div data-home2-history-panel="\d+"', hraw)]
marks.append(hraw.find('</section>'))
imgs = [re.search(r'src="images/([^"]*)"', hraw[marks[i]:marks[i+1]]).group(1) for i in range(6)]
imgs = ['assets/images/' + x for x in imgs]
years = re.findall(r'font-black tabular-nums">([^<]*)</span>', hraw)
assert len(years) == 6, years
rows = []
for i in range(6):
    p = hraw[marks[i]:marks[i+1]]
    tx = [t for t in re.findall(r'>([^<>]+)<', p) if t.strip()]
    assert len(tx) == 14, (i, len(tx))
    rows.append({
        'year':  years[i],
        'name':  tx[1], 'badge': tx[1],
        'title': tx[2], 'subline': tx[3], 'body': tx[4],
        's1_label': tx[5], 's1_value': tx[6], 's2_label': tx[7], 's2_value': tx[8],
        'hint': tx[10], 'caption': tx[13],
        'image': imgs[i], 'image_alt': tx[2],
    })
json.dump(rows, open('/tmp/es-history-rows.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
print('rows dumped:', len(rows))

os.makedirs(os.path.join(ROOT, 'template-parts/home'), exist_ok=True)
open(os.path.join(ROOT, 'template-parts/sections/history.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن history — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-history.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-history.php', get_defined_vars());
""")

def nowdoc(name, body):
    return f"${name} = <<<'ESH5'\n{body}\nESH5;\n"

frags = ''.join([
    nowdoc('F_HEAD', F_HEAD),
    nowdoc('TAB_ACTIVE_T', TAB_ACTIVE_T),
    nowdoc('F_TABS_CLOSE', F_TABS_CLOSE),
    nowdoc('PANEL_T', PANEL_T),
    nowdoc('F_TAIL', F_TAIL),
])

PHP = """<?php
/**
 * سکشن تایم‌لاین تاریخچه (history) — برش بایت‌دقیق میرور + رپیتر مراحل
 * فرگمنت‌ها با temp/gen_history5.py از refs/history.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];
$es_stages = is_array($es_f['stages'] ?? null) ? array_values($es_f['stages']) : [];
if ($es_stages === []) {
    return; // بدون مرحله → سکشن رندر نمی‌شود
}
$es_total = count($es_stages);
$es_total_fa = es_fa_num((string) $es_total);

/** فیلتر es_history_args — توسعه‌پذیری تایم‌لاین */
$es_args = apply_filters('es_history_args', ['fields' => $es_f, 'stages' => $es_stages, 'section_id' => (string) ($args['section_id'] ?? 'history')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_stages = is_array($es_args['stages'] ?? null) ? array_values($es_args['stages']) : $es_stages;
    $es_total = count($es_stages);
    $es_total_fa = es_fa_num((string) $es_total);
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/history.html
 */
FRAGBLOCK

// ═══ رندر ═══
$es_first_year = (string) ($es_stages[0]['year'] ?? '');
echo strtr($F_HEAD, [
    '{ES_TOTAL}'  => $es_total_fa,
    '{ES_KICKER}' => (string) ($es_f['kicker'] ?? ''),
    '{ES_HL}'     => (string) ($es_f['title_highlight'] ?? ''),
    '{ES_EXCERPT}' => (string) ($es_f['excerpt'] ?? ''),
    '{ES_N1}'     => es_fa_num('1'),
    '{ES_YEAR1}'  => $es_first_year,
    '{ES_WIDTH}'  => (string) (int) round(100 / max(1, $es_total)),
]);

// دکمه‌های تب
foreach ($es_stages as $es_i => $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($TAB_ACTIVE_T, [
        '{ES_I}'       => (string) $es_i,
        '{ES_SEL}'     => $es_i === 0 ? 'true' : 'false',
        '{ES_YEAR}'    => (string) ($es_row['year'] ?? ''),
        '{ES_NUM}'     => es_fa_num((string) ($es_i + 1)),
        '{ES_CLS}'     => $es_i === 0 ? 'CLS_ACTIVE' : 'CLS_IDLE',
        '{ES_NUMCLS}'  => $es_i === 0 ? 'NUMCLS_ACTIVE' : 'NUMCLS_IDLE',
        '{ES_NAMECLS}' => $es_i === 0 ? 'NAMECLS_A' : 'NAMECLS_I',
        '{ES_NAME}'    => (string) ($es_row['name'] ?? ''),
    ]);
}
echo $F_TABS_CLOSE;

// پنل‌های مراحل
foreach ($es_stages as $es_i => $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($PANEL_T, [
        '{ES_I}'       => (string) $es_i,
        '{ES_CLS}'     => $es_i === 0 ? 'grid' : 'hidden',
        '{ES_YEAR_CH}' => 'سال ' . (string) ($es_row['year'] ?? '') . ' خورشیدی',
        '{ES_BADGE}'   => (string) ($es_row['badge'] ?? ''),
        '{ES_TITLE}'   => (string) ($es_row['title'] ?? ''),
        '{ES_SUBL}'    => (string) ($es_row['subline'] ?? ''),
        '{ES_BODY}'    => (string) ($es_row['body'] ?? ''),
        '{ES_S1L}'     => (string) ($es_row['s1_label'] ?? ''),
        '{ES_S1V}'     => (string) ($es_row['s1_value'] ?? ''),
        '{ES_S2L}'     => (string) ($es_row['s2_label'] ?? ''),
        '{ES_S2V}'     => (string) ($es_row['s2_value'] ?? ''),
        '{ES_STEP}'    => 'مرحله ' . es_fa_num((string) ($es_i + 1)) . ' از ' . $es_total_fa . ' · ',
        '{ES_HINT}'    => (string) ($es_row['hint'] ?? ''),
        '{ES_PREV}'    => 'قبلی',
        '{ES_NEXT}'    => 'بعدی',
        '{ES_CAP}'     => (string) ($es_row['caption'] ?? ''),
        '{ES_IMG}'     => es_media_url((string) ($es_row['image'] ?? '')),
        '{ES_ALT}'     => (string) ($es_row['image_alt'] ?? ''),
    ]);
}
echo $F_TAIL;
"""
PHP = PHP.replace('FRAGBLOCK', frags)
PHP = PHP.replace("'CLS_ACTIVE'", "'" + CLS_ACTIVE.replace('\\', '\\\\') + "'")
PHP = PHP.replace("'CLS_IDLE'", "'" + CLS_IDLE.replace('\\', '\\\\') + "'")
open(os.path.join(ROOT, 'template-parts/home/section-history.php'), 'w', encoding='utf-8').write(PHP)
print('section-history.php bytes:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-history.php')))
