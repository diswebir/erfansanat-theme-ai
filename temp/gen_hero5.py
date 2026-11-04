#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۲ — برش بایت‌دقیق سکشن هیرو از ref و تولید:
   - inc/Frontend/Icons.php   (آیکون‌های lucide — inner بایت‌دقیق)
   - template-parts/sections/hero-home1.php  (رپر)
   - template-parts/home/section-hero.php    (مارک‌آپ کامل + حلقه‌های داینامیک)
   اجرا: python3 temp/gen_hero5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
REF_PATH = os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/hero-home1.html')
h = open(REF_PATH, encoding='utf-8').read()
REF = h
print('ref bytes:', len(h.encode('utf-8')))

def cut(start_marker, end_marker, include_end=False, discard=False):
    """برش از ابتدای start_marker تا ابتدای end_marker از h جاری"""
    global h
    i = h.find(start_marker)
    assert i >= 0, f'start not found: {start_marker[:60]!r}'
    j = h.find(end_marker, i + len(start_marker))
    assert j >= 0, f'end not found: {end_marker[:60]!r}'
    end = j + (len(end_marker) if include_end else 0)
    frag = h[i:end]
    h = h[:i] + h[end:]
    return frag

def discard_between(start_marker, end_marker):
    """حذف بازه بین دو نشانگر (بدون نگه‌داشتن)"""
    cut(start_marker, end_marker, include_end=False, discard=True)

# ═══════════ برش ترتیبی (ترتیب دقیق مارک‌آپ) ═══════════
_i = h.find('<img src="images/hero.jpg"')
assert _i > 0
F_HEAD = h[:_i]
h = h[_i:]

# ۵ تگ img صحنه‌ها
SCENE_IMGS = []
while h.find('<img src="') != -1 and h.find('<img src="') < h.find('<video '):
    m = re.match(r'<img src="[^"]*" alt="[^"]*" data-home1-scene-img="\d+" class="[^"]*"/>', h)
    assert m, h[:120]
    SCENE_IMGS.append(m.group(0))
    h = h[m.end():]
assert len(SCENE_IMGS) == 5, len(SCENE_IMGS)

F_VIDEO = cut('<video ', '</div>')
F_VIDEO_CLOSE = h[:6]
assert F_VIDEO_CLOSE == '</div>'
h = h[6:]

F_ATMOS = cut('<div data-home1-atmosphere="true"', '<div data-home1-video-controls="true"')
F_CTRL = cut('<div data-home1-video-controls="true"', '<div data-home1-overlay-item="true" class="absolute -top-px -right-px')
F_NOTCH = cut('<div data-home1-overlay-item="true" class="absolute -top-px -right-px', '<div data-home1-overlay-item="true" class="absolute left-4 top-28')
F_NOTCH = F_NOTCH.replace('المان‌های نوری شهری · استاندارد IP68', '{ES_KICKER}')
F_NOTCH = F_NOTCH.replace('وقتی کیفیت و ماندگاری مهمه', '{ES_TITLE}')
F_NOTCH = F_NOTCH.replace('عرفان صنعت انتخاب توئه...', '{ES_BRAND}')

# چیپ‌ها: ۱ قطعه الگو + حذف ۷ نمونه + باز/میانه/بسته
F_CHIPS_OPEN = cut('<div data-home1-overlay-item="true" class="absolute left-4 top-28', '<a href="projects.html"')
CHIP = cut('<a href="projects.html"', '</a>', include_end=True)
CHIP = CHIP.replace('href="projects.html"', 'href="{ES_URL}"')
CHIP = re.sub(r'>نورپردازی شهری</a>', '>{ES_LABEL}</a>', CHIP)
# ۳ چیپ باقی ردیف اول
for _ in range(3):
    cut('<a href="', '</a>', include_end=True)
F_CHIPS_MID = cut('</div>', '<a href="product_cat/needle-thread.html"')  # بستن ردیف اول + باز کردن ردیف دوم
# ۴ چیپ ردیف دوم — حذف (الگو همان CHIP است)
chip2 = cut('<a href="product_cat/needle-thread.html"', '</a>', include_end=True)
norm2 = chip2.replace('href="product_cat/needle-thread.html"', 'href="{ES_URL}"')
norm2 = re.sub(r'>ریسه ضدآب IP68</a>', '>{ES_LABEL}</a>', norm2)
assert norm2 == CHIP, 'chip template mismatch'
for _ in range(3):
    cut('<a href="', '</a>', include_end=True)
F_CHIPS_CLOSE = cut('</div>', '<div data-home1-overlay-item="true" class="absolute inset-0 z-10 flex items-center justify-center')
assert F_CHIPS_CLOSE == '</div></div>', repr(F_CHIPS_CLOSE)

F_PLAY = cut('<div data-home1-overlay-item="true" class="absolute inset-0 z-10 flex items-center justify-center', '<div data-home1-overlay-item="true" class="absolute right-5 bottom-16')

F_PILL = cut('<div data-home1-overlay-item="true" class="absolute right-5 bottom-16', '<div data-home1-overlay-item="true" class="absolute inset-x-2 bottom-12')
F_PILL = F_PILL.replace('href="about.html" data-home1-scene-link="true"', 'href="{ES_URL}" data-home1-scene-link="true"')
F_PILL = F_PILL.replace('<span data-home1-scene-title="true">تیزر رسمی خط تولید و پروژه‌های عرفان صنعت اصفهان</span>', '<span data-home1-scene-title="true">{ES_TITLE}</span>')
F_PILL = F_PILL.replace('<span data-home1-scene-cta="true" class="text-emerald-300">درباره کارخانه</span>', '<span data-home1-scene-cta="true" class="text-emerald-300">{ES_CTA}</span>')

F_TABS_OPEN = cut('<div data-home1-overlay-item="true" class="absolute inset-x-2 bottom-12', '<button type="button" data-home1-scene-btn="0"')
TAB_ACTIVE = cut('<button type="button" data-home1-scene-btn="0"', '</button>', include_end=True)
TAB_IDLE = cut('<button type="button" data-home1-scene-btn="1"', '</button>', include_end=True)
# ۳ تب باقی‌مانده حذف
for _ in range(3):
    cut('<button type="button" data-home1-scene-btn="', '</button>', include_end=True)
F_TABS_CLOSE = cut('</div>', '<div class="absolute inset-x-0 -bottom-px z-30 flex justify-center">')
assert F_TABS_CLOSE == '</div>', repr(F_TABS_CLOSE)

F_BOTTOM = cut('<div class="absolute inset-x-0 -bottom-px z-30 flex justify-center">', '<div id="home1-categories"')
F_STRIP_OPEN = cut('<div id="home1-categories"', '<a href="product_cat/point-light.html"')
CARD = cut('<a href="product_cat/point-light.html"', '</a>', include_end=True)
CARD = CARD.replace('href="product_cat/point-light.html"', 'href="{ES_URL}"')
# ۶ کارت باقی‌مانده حذف
for _ in range(6):
    cut('<a href="product_cat/', '</a>', include_end=True)
F_MID = h + '>'  # ref بدون «>» پایانی بریده شده (مرز برش بعدی)
h = ''
print('F_MID (بسته‌ها):', repr(F_MID))

# ═══════════ استخراج آیکون‌ها ═══════════
def icon_inner(full_svg):
    m = re.match(r'<svg [^>]*>(.*)</svg>$', full_svg, re.S)
    assert m, full_svg[:80]
    return m.group(1)

icons = {}
for svg_full in re.findall(r'<svg xmlns="http://www\.w3\.org/2000/svg"[^>]*>.*?</svg>', REF, re.S):
    cm = re.search(r'lucide lucide-([a-z0-9-]+)', svg_full)
    if cm and cm.group(1) not in icons:
        icons[cm.group(1)] = icon_inner(svg_full)
for n in ['cpu', 'sparkles', 'trees', 'lightbulb', 'waves', 'sun', 'flame', 'map-pin', 'arrow-up-left', 'play', 'pause', 'x', 'volume2']:
    assert n in icons, n
print('icons:', sorted(icons))

icon_rows = []
for name in sorted(icons):
    inner = icons[name]
    assert '{' not in inner and '$' not in inner
    icon_rows.append(f"        '{name}' => <<<'ESIX'\n{inner}\nESIX,")
icons_php = f"""<?php
/**
 * آیکون‌های lucide قالب — inner بایت‌دقیق از میرور (تسک ۵.۲)
 * Icons::svg('cpu', 'lucide lucide-cpu h-6 w-6 stroke-[1.75]')
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\\Theme\\Frontend;

final class Icons {{

    /** inner تگ svg (بدون تگ بیرونی) */
    private const INNER = [
{chr(10).join(icon_rows)}
    ];

    /** ساخت تگ svg کامل — رشته کلاس عیناً پاس می‌شود (بایت‌دقیق میرور) */
    public static function svg(string $name, string $classes): string {{
        $inner = self::INNER[$name] ?? self::INNER['cpu'];
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $classes . '" aria-hidden="true">' . $inner . '</svg>';
    }}
}}
"""
open(os.path.join(ROOT, 'inc/Frontend/Icons.php'), 'w', encoding='utf-8').write(icons_php)

# ═══════════ نرمال‌سازی الگوهای حلقه‌ای ═══════════
IMG_TPL = '<img src="{ES_SRC}" alt="{ES_ALT}" data-home1-scene-img="{ES_I}" class="{ES_CLS}"/>'
CLS_IMG_ACTIVE = re.match(r'<img src="[^"]*" alt="[^"]*" data-home1-scene-img="\d+" class="([^"]*)"/>', SCENE_IMGS[0]).group(1)
CLS_IMG_IDLE = re.match(r'<img src="[^"]*" alt="[^"]*" data-home1-scene-img="\d+" class="([^"]*)"/>', SCENE_IMGS[1]).group(1)

vid_m = re.match(r'<video src="([^"]*)" poster="([^"]*)"(.*?)</video>', F_VIDEO, re.S)
assert vid_m, F_VIDEO[:80]
VIDEO_TPL = '<video src="{ES_SRC}" poster="{ES_POSTER}"' + vid_m.group(3) + '</video>'

TAB_PREFIX = 'inline-flex shrink-0 items-center gap-1.5 rounded-xl px-2.5 py-1.5 text-[11px] font-extrabold transition-all cursor-pointer whitespace-nowrap '
t_a = re.match(r'<button type="button" data-home1-scene-btn="\d+" data-scene-title="[^"]*" data-scene-href="[^"]*" data-scene-cta="[^"]*" data-scene-is-video="[^"]*" class="([^"]*)">(.*?)</button>', TAB_ACTIVE, re.S)
t_i = re.match(r'<button type="button" data-home1-scene-btn="\d+" data-scene-title="[^"]*" data-scene-href="[^"]*" data-scene-cta="[^"]*" data-scene-is-video="[^"]*" class="([^"]*)">(.*?)</button>', TAB_IDLE, re.S)
assert t_a and t_i
TAB_CLS_ACTIVE = t_a.group(1).replace(TAB_PREFIX, '')
TAB_CLS_IDLE = t_i.group(1).replace(TAB_PREFIX, '')
TAB_ICON = t_a.group(2)[:t_a.group(2).find('<span>')]  # فقط svg (span نام در الگو هست)
TAB_TPL = ('<button type="button" data-home1-scene-btn="{ES_I}" data-scene-title="{ES_TITLE}" data-scene-href="{ES_URL}" data-scene-cta="{ES_CTA}" data-scene-is-video="{ES_IV}" class="' + TAB_PREFIX + '{ES_CLS}">{ES_ICON}<span>{ES_NAME}</span></button>')

c_icon = re.search(r'(<div class="grid h-13 w-13 place-items-center[^>]*>)(<svg .*?</svg>)(</div>)', CARD, re.S)
c_name = re.search(r'(<div class="mt-3\.5"><div class="text-\[12\.5px\] font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700">)([^<]*)(</div><div class="mt-1 text-\[10\.5px\] font-medium text-slate-500">)([^<]*)(</div></div>)', CARD)
c_count = re.search(r'(<div class="mt-3 inline-flex items-center gap-1 border-t border-slate-100 pt-2 text-\[10\.5px\] font-bold text-emerald-700 tabular-nums"><span>)([^<]*)(</span>)', CARD)
assert c_icon and c_name and c_count
CARD_TPL = CARD.replace(c_icon.group(0), '{ES_ICON_DIV}')
CARD_TPL = CARD_TPL.replace(c_name.group(0), '<div class="mt-3.5"><div class="text-[12.5px] font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700">{ES_NAME}</div><div class="mt-1 text-[10.5px] font-medium text-slate-500">{ES_SUB}</div></div>')
CARD_TPL = CARD_TPL.replace(c_count.group(0), c_count.group(1) + '{ES_COUNT}' + c_count.group(3))
ICON_DIV_TPL = c_icon.group(1) + '{ES_ICON}' + c_icon.group(3)
CARD_ICON_CLS_FULL = re.search(r'class="([^"]*)"', c_icon.group(2)).group(1)
CARD_ICON_CLS = CARD_ICON_CLS_FULL.replace('lucide lucide-cpu ', '')  # دنباله ثابت کلاس (نام آیکون در رندر ساخته می‌شود)

# ═══════════ رپر ═══════════
os.makedirs(os.path.join(ROOT, 'template-parts/sections'), exist_ok=True)
os.makedirs(os.path.join(ROOT, 'template-parts/home'), exist_ok=True)
open(os.path.join(ROOT, 'template-parts/sections/hero-home1.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن hero-home1 — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-hero.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-hero.php', get_defined_vars());
""")

# ═══════════ فرگمنت‌های PHP ═══════════
def nowdoc(name, body):
    return f"${name} = <<<'ESH5'\n{body}\nESH5;\n"

frags = [
    nowdoc('F_HEAD', F_HEAD),
    nowdoc('F_VIDEO_CLOSE', F_VIDEO_CLOSE),
    nowdoc('F_ATMOS', F_ATMOS),
    nowdoc('F_CTRL', F_CTRL),
    nowdoc('F_NOTCH', F_NOTCH),
    nowdoc('F_CHIPS_OPEN', F_CHIPS_OPEN),
    nowdoc('F_CHIPS_MID', F_CHIPS_MID),
    nowdoc('F_CHIPS_CLOSE', F_CHIPS_CLOSE),
    nowdoc('F_PLAY', F_PLAY),
    nowdoc('F_PILL', F_PILL),
    nowdoc('F_TABS_OPEN', F_TABS_OPEN),
    nowdoc('F_TABS_CLOSE', F_TABS_CLOSE),
    nowdoc('F_BOTTOM', F_BOTTOM),
    nowdoc('F_STRIP_OPEN', F_STRIP_OPEN),
    nowdoc('F_MID', F_MID),
    nowdoc('IMG_TPL', IMG_TPL),
    nowdoc('VIDEO_TPL', VIDEO_TPL),
    nowdoc('CHIP_TPL', CHIP),
    nowdoc('TAB_TPL', TAB_TPL),
    nowdoc('CARD_TPL', CARD_TPL),
    nowdoc('ICON_DIV_TPL', ICON_DIV_TPL),
]
frag_block = ''.join(frags)

PHP = """<?php
/**
 * سکشن هیرو طرح ۱ (hero-home1) — برش بایت‌دقیق میرور + داده پویای SectionManager
 * فرگمنت‌ها با temp/gen_hero5.py از refs/hero-home1.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\\Theme\\Frontend\\Icons;

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

// ── فیلدهای ساده ──
$es_kicker = (string) ($es_f['kicker'] ?? '');
$es_title  = (string) ($es_f['title'] ?? '');
$es_brand  = (string) ($es_f['brand_line'] ?? '');
$es_video  = es_media_url((string) ($es_f['video'] ?? ''));
$es_vs     = max(0, (int) ($es_f['video_scene'] ?? 0));

// ── صحنه‌ها ──
$es_scenes = is_array($es_f['scenes'] ?? null) ? $es_f['scenes'] : [];
$es_scene0 = is_array($es_scenes[$es_vs] ?? null) ? $es_scenes[$es_vs] : [];

// ── لینک‌های سریع چیپ ابری (حداکثر ۸) ──
$es_ql = is_array($es_f['quick_links'] ?? null) ? array_slice($es_f['quick_links'], 0, 8) : [];

// ── نوار پربازدیدترین دسته‌بندی‌ها (رپیتر strip_items) ──
$es_strip = is_array($es_f['strip_items'] ?? null) ? $es_f['strip_items'] : [];

/** فیلتر es_hero_args — توسعه‌پذیری داده‌های هیرو */
$es_args = apply_filters('es_hero_args', ['fields' => $es_f, 'strip' => $es_strip, 'section_id' => (string) ($args['section_id'] ?? 'hero-home1')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_strip = is_array($es_args['strip'] ?? null) ? $es_args['strip'] : $es_strip;
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/hero-home1.html
 */
FRAGBLOCK

// ═══ رندر ═══
// ۱) هدر سکشن
echo $F_HEAD;

// ۲) تصاویر صحنه‌ها
$es_n = count($es_scenes);
for ($es_i = 0; $es_i < $es_n; $es_i++) {
    $es_row = $es_scenes[$es_i];
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($IMG_TPL, [
        '{ES_SRC}' => es_media_url((string) ($es_row['image'] ?? '')),
        '{ES_ALT}' => (string) ($es_row['title'] ?? ''),
        '{ES_I}'   => (string) $es_i,
        '{ES_CLS}' => $es_i === $es_vs ? 'CLS_IMG_ACTIVE_V' : 'CLS_IMG_IDLE_V',
    ]);
}

// ۳) ویدیو درون‌خطی + پوستر صحنه فعال
echo strtr($VIDEO_TPL, [
    '{ES_SRC}'    => $es_video,
    '{ES_POSTER}' => es_media_url((string) ($es_scene0['image'] ?? '')),
]);
echo $F_VIDEO_CLOSE;

// ۴) اتمسفر + کنترل‌های ویدیو
echo $F_ATMOS;
echo $F_CTRL;

// ۵) نوتچ
echo strtr($F_NOTCH, [
    '{ES_KICKER}' => $es_kicker,
    '{ES_TITLE}'  => $es_title,
    '{ES_BRAND}'  => $es_brand,
]);

// ۶) چیپ‌های ابری — دو ردیف چهارتایی
echo $F_CHIPS_OPEN;
foreach (array_slice($es_ql, 0, 4) as $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($CHIP_TPL, [
        '{ES_URL}'   => es_link_url((string) ($es_row['url'] ?? '')),
        '{ES_LABEL}' => (string) ($es_row['label'] ?? ''),
    ]);
}
echo $F_CHIPS_MID;
foreach (array_slice($es_ql, 4, 4) as $es_row) {
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($CHIP_TPL, [
        '{ES_URL}'   => es_link_url((string) ($es_row['url'] ?? '')),
        '{ES_LABEL}' => (string) ($es_row['label'] ?? ''),
    ]);
}
echo $F_CHIPS_CLOSE;

// ۷) پلی‌ترِیگر مرکزی
echo $F_PLAY;

// ۸) پیل scene-link (صحنه فعال)
echo strtr($F_PILL, [
    '{ES_URL}'   => es_link_url((string) ($es_scene0['cta_url'] ?? '')),
    '{ES_TITLE}' => (string) ($es_scene0['title'] ?? ''),
    '{ES_CTA}'   => (string) ($es_scene0['cta_text'] ?? ''),
]);

// ۹) تب‌لیست صحنه‌ها
echo $F_TABS_OPEN;
$es_n = count($es_scenes);
for ($es_i = 0; $es_i < $es_n; $es_i++) {
    $es_row = $es_scenes[$es_i];
    if (!is_array($es_row)) {
        continue;
    }
    echo strtr($TAB_TPL, [
        '{ES_I}'     => (string) $es_i,
        '{ES_TITLE}' => (string) ($es_row['title'] ?? ''),
        '{ES_URL}'   => es_link_url((string) ($es_row['cta_url'] ?? '')),
        '{ES_CTA}'   => (string) ($es_row['cta_text'] ?? ''),
        '{ES_IV}'    => $es_i === $es_vs ? 'true' : 'false',
        '{ES_CLS}'   => $es_i === $es_vs ? 'TAB_CLS_ACTIVE_V' : 'TAB_CLS_IDLE_V',
        '{ES_ICON}'  => $es_i === $es_vs ? 'TAB_ICON_V' : '',
        '{ES_NAME}'  => (string) ($es_row['name'] ?? ''),
    ]);
}
echo $F_TABS_CLOSE;

// ۱۰) تب پایینی + فیلت‌ها
echo $F_BOTTOM;

// ۱۱) گرید دسته‌بندی‌ها
echo $F_STRIP_OPEN;
foreach ($es_strip as $es_row) {
    echo strtr($CARD_TPL, [
        '{ES_URL}'      => es_link_url((string) ($es_row['url'] ?? '')),
        '{ES_ICON_DIV}' => strtr($ICON_DIV_TPL, [
            '{ES_ICON}' => Icons::svg((string) $es_row['icon'], 'lucide lucide-' . (string) $es_row['icon'] . ' CARD_ICON_CLS_V'),
        ]),
        '{ES_NAME}'  => (string) $es_row['name'],
        '{ES_SUB}'   => (string) $es_row['sub'],
        '{ES_COUNT}' => (string) ($es_row['count_text'] ?? ''),
    ]);
}
echo $F_MID;
"""
PHP = PHP.replace('FRAGBLOCK', frag_block)
PHP = PHP.replace('CLS_IMG_ACTIVE_V', CLS_IMG_ACTIVE)
PHP = PHP.replace('CLS_IMG_IDLE_V', CLS_IMG_IDLE)
PHP = PHP.replace('TAB_CLS_ACTIVE_V', TAB_CLS_ACTIVE)
PHP = PHP.replace('TAB_CLS_IDLE_V', TAB_CLS_IDLE)
PHP = PHP.replace('TAB_ICON_V', TAB_ICON)
PHP = PHP.replace('CARD_ICON_CLS_V', CARD_ICON_CLS)
open(os.path.join(ROOT, 'template-parts/home/section-hero.php'), 'w', encoding='utf-8').write(PHP)
print('section-hero.php bytes:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-hero.php')))
print('icons.php bytes:', os.path.getsize(os.path.join(ROOT, 'inc/Frontend/Icons.php')))
print('OK')
