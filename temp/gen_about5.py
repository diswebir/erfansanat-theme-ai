#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۴ — برش بایت‌دقیق سکشن about از ref و تولید:
   - template-parts/sections/about.php        (رپر)
   - template-parts/home/section-about.php    (مارک‌آپ + رپیترها)
   - افزودن آیکون‌های circle-check/award به inc/Frontend/Icons.php
   اجرا: python3 temp/gen_about5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/about.html'), encoding='utf-8').read()
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

# ۱) هدر + اینترو تا لیست
F_HEAD = cut('<section id="about"', '<li class="flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white px-4 py-3 text-xs sm:text-[13.5px] font-bold text-slate-800">')
F_HEAD = F_HEAD.replace('>درباره شرکت دانش‌بنیان عرفان صنعت اصفهان</span>', '>{ES_KICKER}</span>')
F_HEAD = F_HEAD.replace('بیش از <span class="text-emerald-700">۲۵ سال</span>', 'بیش از <span class="text-emerald-700">{ES_HL}</span>')
F_HEAD = re.sub(r'(<p class="text-sm leading-7 font-medium text-slate-600 sm:text-\[15px\] sm:leading-8">)[^<]*(</p>)', r'\1{ES_EXCERPT}\2', F_HEAD)

# ۲) آیتم لیست (الگو)
LI = cut('<li class="flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white px-4 py-3 text-xs sm:text-[13.5px] font-bold text-slate-800">', '</li>', include_end=True)
LI_TPL = LI.replace('>طراحی، تولید و اجرای صفر تا صد پروژه‌های روشنایی شهری و میادین</span>', '>{ES_TEXT}</span>')
# دو آیتم دیگر حذف
for _ in range(2):
    cut('<li class="flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white px-4 py-3 text-xs sm:text-[13.5px] font-bold text-slate-800">', '</li>', include_end=True)

# ۳) بستن لیست + ردیف CTA تا لینک
F_LIST_CLOSE = cut('</ul>', '<a href="about.html"')  # شامل باز کردن wrapper ردیف CTA (custom=0.22)

# ۴) CTA + چیپ ISO تا بلوک تصویر
F_CTA = cut('<a href="about.html"', '<div class="lg:col-span-6">')
F_CTA = F_CTA.replace('href="about.html"', 'href="{ES_URL}"')
F_CTA = F_CTA.replace('<span>آشنایی با خط تولید کارخانه</span>', '<span>{ES_TEXT}</span>')

# ۵) بلوک تصویر
F_IMAGE = cut('<div class="lg:col-span-6">', '<div class="mt-4 grid grid-cols-2 gap-3.5 sm:gap-4">')
F_IMAGE = F_IMAGE.replace('src="images/factory.jpg"', 'src="{ES_IMG}"')
F_IMAGE = re.sub(r'alt="[^"]*"', 'alt="{ES_ALT}"', F_IMAGE, count=1)
F_IMAGE = F_IMAGE.replace('>خط تولید صنعتی · شهرک صنعتی جی اصفهان</div>', '>{ES_CAPTION}</div>')
F_IMAGE = F_IMAGE.replace('>تولید تخصصی المان‌های نوری و ریسه‌های تزریق مستقیم</div>', '>{ES_TITLE}</div>')

# ۶) گرید آمار — کارت اول به‌عنوان الگو
GRID_OPEN = '<div class="mt-4 grid grid-cols-2 gap-3.5 sm:gap-4">'
assert h.startswith(GRID_OPEN), h[:80]
h = h[len(GRID_OPEN):]
STAT_OPEN = '<div custom="0.12">'
i1 = h.find(STAT_OPEN)
assert i1 >= 0, h[:80]
# کارت اول الگو تا آغاز کارت دوم
M2 = '<div custom="0.16999999999999998">'
i2 = h.find(M2, i1)
assert i2 > 0
STAT_FULL = h[i1:i2]
# حذف کارت‌های ۲ تا ۴ (در رپیتر رندر می‌شوند)
for marker, nxt in [(M2, '<div custom="0.22">'), ('<div custom="0.22">', '<div custom="0.27">'), ('<div custom="0.27">', '</div></div></div></section')]:
    a = h.find(marker)
    assert a >= 0, marker
    b = h.find(nxt, a + len(marker))
    assert b > 0, nxt
    h = h[:a] + h[b:]
h = h[:i1] + h[i1:]  # no-op نگه‌داشتن ساختار
h = h.replace(STAT_FULL, '', 1)
STAT_TPL = STAT_FULL
STAT_TPL = STAT_TPL.replace('data-counter-target="850" data-counter-suffix="+" class="text-2xl sm:text-3xl font-black tracking-tight text-emerald-700 tabular-nums lg:text-4xl">۸۵۰+<', 'data-counter-target="{ES_TARGET}" data-counter-suffix="{ES_SUFFIX}" class="text-2xl sm:text-3xl font-black tracking-tight text-emerald-700 tabular-nums lg:text-4xl">{ES_NUM}{ES_SUFFIX}<')
STAT_TPL = STAT_TPL.replace('>پروژه تکمیل‌شده</div>', '>{ES_LABEL}</div>')
STAT_TPL = STAT_TPL.replace('>در کلان‌شهرهای ایران و عراق</div>', '>{ES_SUB}</div>')
STAT_OPEN_TPL = '<div custom="{ES_DELAY}">'
STAT_REST = STAT_TPL[len(STAT_OPEN):]

# ۷) دنباله
F_TAIL = h + '>'
print('F_TAIL:', repr(F_TAIL))

# ۷) دنباله
F_TAIL = h + '>'
print('F_TAIL:', repr(F_TAIL))

# ═══ آیکون‌های جدید برای Icons.php ═══
new_icons = {}
for svg_full in re.findall(r'<svg xmlns="http://www\.w3\.org/2000/svg"[^>]*>.*?</svg>', REF, re.S):
    cm = re.search(r'lucide lucide-([a-z0-9-]+)', svg_full)
    if cm and cm.group(1) not in ('circle-check', 'award'):
        continue
    if cm:
        inner = re.match(r'<svg [^>]*>(.*)</svg>$', svg_full, re.S).group(1)
        new_icons[cm.group(1)] = inner
assert 'circle-check' in new_icons and 'award' in new_icons, sorted(new_icons)

icons_path = os.path.join(ROOT, 'inc/Frontend/Icons.php')
ip = open(icons_path, encoding='utf-8').read()
for name, inner in new_icons.items():
    assert '{' not in inner and '$' not in inner
    row = f"        '{name}' => <<<'ESIX'\n{inner}\nESIX,"
    if f"'{name}' =>" not in ip:
        # درج قبل از بسته شدن آرایه INNER (آخرین «    ];»)
        idx = ip.rfind('    ];')
        ip = ip[:idx] + row + '\n' + ip[idx:]
open(icons_path, 'w', encoding='utf-8').write(ip)
print('icons added:', sorted(new_icons))

# ═══ رپر ═══
open(os.path.join(ROOT, 'template-parts/sections/about.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن about — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-about.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-about.php', get_defined_vars());
""")

def nowdoc(name, body):
    return f"${name} = <<<'ESA4'\n{body}\nESA4;\n"

frags = ''.join([
    nowdoc('F_HEAD', F_HEAD),
    nowdoc('LI_TPL', LI_TPL),
    nowdoc('F_LIST_CLOSE', F_LIST_CLOSE),
    nowdoc('F_CTA', F_CTA),
    nowdoc('F_IMAGE', F_IMAGE),
    nowdoc('STAT_OPEN_TPL', STAT_OPEN_TPL),
    nowdoc('STAT_REST', STAT_REST),
    nowdoc('F_TAIL', F_TAIL),
])

# delayهای پیش‌فرض میرور (بایت‌دقیق)
delays_php = "['0.12', '0.16999999999999998', '0.22', '0.27']"

PHP = """<?php
/**
 * سکشن درباره ما + آمار شمارنده (about) — برش بایت‌دقیق میرور + رپیترهای SectionManager
 * فرگمنت‌ها با temp/gen_about5.py از refs/about.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\\Theme\\Frontend\\Icons;

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

$es_points = is_array($es_f['points'] ?? null) ? $es_f['points'] : [];
$es_stats  = is_array($es_f['stats'] ?? null) ? $es_f['stats'] : [];

/** فیلتر es_about_args — توسعه‌پذیری داده‌های سکشن درباره */
$es_args = apply_filters('es_about_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'about')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/about.html
 */
FRAGBLOCK

// ═══ رندر ═══
echo strtr($F_HEAD, [
    '{ES_KICKER}' => (string) ($es_f['kicker'] ?? ''),
    '{ES_HL}'     => (string) ($es_f['title_highlight'] ?? ''),
    '{ES_EXCERPT}' => (string) ($es_f['excerpt'] ?? ''),
]);

// چک‌لیست
foreach ($es_points as $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($LI_TPL, ['{ES_TEXT}' => (string) ($es_row['text'] ?? '')]);
}
echo $F_LIST_CLOSE;

// CTA + چیپ ISO
echo strtr($F_CTA, [
    '{ES_URL}'  => es_link_url((string) ($es_f['cta_url'] ?? '')),
    '{ES_TEXT}' => (string) ($es_f['cta_text'] ?? ''),
]);

// بلوک تصویر
echo strtr($F_IMAGE, [
    '{ES_IMG}'     => es_media_url((string) ($es_f['image'] ?? '')),
    '{ES_ALT}'     => (string) ($es_f['image_alt'] ?? ''),
    '{ES_CAPTION}' => (string) ($es_f['image_caption'] ?? ''),
    '{ES_TITLE}'   => (string) ($es_f['image_title'] ?? ''),
]);

// آمار شمارنده
echo '<div class="mt-4 grid grid-cols-2 gap-3.5 sm:gap-4">';
$es_stat_delays = DELAYS;
foreach ($es_stats as $es_i => $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    $es_delay = (string) ($es_row['delay'] ?? ($es_stat_delays[$es_i] ?? '0.' . str_pad((string) (12 + 5 * $es_i), 2, '0')));
    $es_target = (string) ($es_row['target'] ?? '');
    $es_suffix = (string) ($es_row['suffix'] ?? '+');
    echo strtr($STAT_OPEN_TPL, ['{ES_DELAY}' => $es_delay]);
    echo strtr($STAT_REST, [
        '{ES_TARGET}' => $es_target,
        '{ES_SUFFIX}' => $es_suffix,
        '{ES_NUM}'    => es_fa_num($es_target),
        '{ES_LABEL}'  => (string) ($es_row['label'] ?? ''),
        '{ES_SUB}'    => (string) ($es_row['sub'] ?? ''),
    ]);
}
echo $F_TAIL;
"""
PHP = PHP.replace('FRAGBLOCK', frags).replace('DELAYS', delays_php)
open(os.path.join(ROOT, 'template-parts/home/section-about.php'), 'w', encoding='utf-8').write(PHP)
print('section-about.php bytes:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-about.php')))
