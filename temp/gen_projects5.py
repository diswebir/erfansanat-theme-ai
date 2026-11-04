#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۳ — برش بایت‌دقیق سکشن projects از ref و تولید:
   - template-parts/sections/projects.php        (رپر)
   - template-parts/cards/card-project.php       (کارت استاندارد — دو واریانت hero/stack)
   - template-parts/home/section-projects.php    (مارک‌آپ سکشن + حلقه get_posts)
   اجرا: python3 temp/gen_projects5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
h = open(os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs/projects.html'), encoding='utf-8').read()
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

# ═══ برش ترتیبی ═══
# ۱) هدر سکشن + اینترو تا اولین کارت
F_HEAD = cut('<section id="projects"', '<div class="flex h-full flex-col lg:col-span-3" custom="0.05">')

# ۲) کارت بزرگ (hero) — کامل با wrapper
F_HERO_WRAP = cut('<div class="flex h-full flex-col lg:col-span-3" custom="0.05">', '<div class="flex h-full flex-col gap-4 sm:gap-5 lg:col-span-2">')

# ۳) باز کردن پشته + wrapper کارت دوم
F_STACK_OPEN = cut('<div class="flex h-full flex-col gap-4 sm:gap-5 lg:col-span-2">', '<div class="flex flex-1 flex-col" custom="0.1">')
STACK_ITEM_OPEN1 = cut('<div class="flex flex-1 flex-col" custom="0.1">', '<a href="projects/esfahan-shohada-square.html"')
CARD_STACK = cut('<a href="projects/esfahan-shohada-square.html"', '</a>', include_end=True)
cut('</div>', '<div class="flex flex-1 flex-col" custom="0.15">')  # بستن آیتم دوم
ITEM_OPEN2 = cut('<div class="flex flex-1 flex-col" custom="0.15">', '<a href="projects/artam-tree-midan.html"')
card3 = cut('<a href="projects/artam-tree-midan.html"', '</a>', include_end=True)
norm3 = card3.replace('href="projects/artam-tree-midan.html"', 'href="{ES_URL}"')
# الگوی کارت پشته‌ای از کارت دوم
norm2 = CARD_STACK.replace('href="projects/esfahan-shohada-square.html"', 'href="{ES_URL}"')
for a, b in [('گوی و المان حجمی نورانی', 'X'), ('درخت و المان نوری هوشمند', 'X'),
             ('کره‌ها و حجم‌های نوری درخشان در نقطه کانونی میادین و فضاهای باز شهری', 'Y'),
             ('طراحی و ساخت المان‌های نوری منحصربه‌فرد با هویت بصری ماندگار برای شهرداری‌ها', 'Y'),
             ('المان حجمی سه‌بعدی · فول‌کالر', 'Z'), ('سازه‌های نوری اختصاصی · استاندارد IP68', 'Z'),
             ('images/light-sphere.jpg', 'I'), ('images/light-element.jpg', 'I')]:
    a2 = a.replace(a, 'X' if b in ('X', 'I') else b)  # noqa
norm2c = re.sub(r'alt="[^"]*"', 'alt="A"', norm2)
norm3c = re.sub(r'alt="[^"]*"', 'alt="A"', norm3)
norm2c = norm2c.replace('>گوی و المان حجمی نورانی</h3>', '>T</h3>').replace('>درخت و المان نوری هوشمند</h3>', '>T</h3>')
norm3c = norm3c.replace('>گوی و المان حجمی نورانی</h3>', '>T</h3>').replace('>درخت و المان نوری هوشمند</h3>', '>T</h3>')
norm2c = re.sub(r'src="images/[^"]*"', 'src="I"', norm2c)
norm3c = re.sub(r'src="images/[^"]*"', 'src="I"', norm3c)
norm2c = re.sub(r'<span class="text-xs font-bold text-emerald-300">[^<]*</span>', '<span class="text-xs font-bold text-emerald-300">S</span>', norm2c)
norm3c = re.sub(r'<span class="text-xs font-bold text-emerald-300">[^<]*</span>', '<span class="text-xs font-bold text-emerald-300">S</span>', norm3c)
norm2c = re.sub(r'<p class=" text-xs[^>]*">[^<]*</p>', '<p class="P">EX</p>', norm2c)
norm3c = re.sub(r'<p class=" text-xs[^>]*">[^<]*</p>', '<p class="P">EX</p>', norm3c)
norm2c = re.sub(r'>۰۲</span>', '>N</span>', norm2c)
norm3c = re.sub(r'>۰۳</span>', '>N</span>', norm3c)
assert norm2c == norm3c, 'دو کارت پشته‌ای هم‌الگو نیستند\n' + norm2c[:400] + '\n---\n' + norm3c[:400]
# الگوی واقعی کارت پشته‌ای: توکن‌های ES روی نمونه دوم
CARD_STACK_TPL = norm2.replace('href="{ES_URL}"', 'href="{ES_URL}"')
CARD_STACK_TPL = re.sub(r'src="images/[^"]*"', 'src="{ES_IMG}"', CARD_STACK_TPL)
CARD_STACK_TPL = re.sub(r'alt="[^"]*"', 'alt="{ES_ALT}"', CARD_STACK_TPL, count=1)
CARD_STACK_TPL = re.sub(r'<span class="text-xs font-bold text-emerald-300">[^<]*</span>', '<span class="text-xs font-bold text-emerald-300">{ES_TYPES}</span>', CARD_STACK_TPL)
CARD_STACK_TPL = re.sub(r'<h3 class="font-black tracking-tight text-white text-lg sm:text-xl">[^<]*</h3>', '<h3 class="font-black tracking-tight text-white text-lg sm:text-xl">{ES_TITLE}</h3>', CARD_STACK_TPL)
CARD_STACK_TPL = re.sub(r'<p class=" text-xs sm:text-\[13px\] font-medium leading-6 text-slate-200/90">[^<]*</p>', '<p class=" text-xs sm:text-[13px] font-medium leading-6 text-slate-200/90">{ES_EXCERPT}</p>', CARD_STACK_TPL)
CARD_STACK_TPL = re.sub(r'>۰۲</span>', '>{ES_NUM}</span>', CARD_STACK_TPL)
# custom attr آیتم دوم
ITEM_OPEN_TPL = '<div class="flex flex-1 flex-col" custom="{ES_DELAY}">'

# ۴) دنباله بسته‌ها — بستن اول، مال آیتم پشته است که حلقه می‌بندد
assert h.startswith('</div></div></div></section')
F_TAIL = h[6:] + '>'
print('F_TAIL:', repr(F_TAIL))

# ═══ الگوی کارت hero از F_HERO_WRAP ═══
hero_card_m = re.search(r'<a href="[^"]*".*?</a>', F_HERO_WRAP, re.S)
HERO_CARD = hero_card_m.group(0)
HERO_WRAP_OPEN = F_HERO_WRAP[:hero_card_m.start()]
HERO_WRAP_CLOSE = F_HERO_WRAP[hero_card_m.end():]
HERO_TPL = HERO_CARD.replace(f'href="projects/half-arch-tunnel-alborz.html"', 'href="{ES_URL}"')
HERO_TPL = re.sub(r'alt="[^"]*"', 'alt="{ES_ALT}"', HERO_TPL, count=1)
HERO_TPL = re.sub(r'<span class="text-xs font-bold text-emerald-300">[^<]*</span>', '<span class="text-xs font-bold text-emerald-300">{ES_TYPES}</span>', HERO_TPL)
HERO_TPL = re.sub(r'<h3 class="font-black tracking-tight text-white text-xl sm:text-2xl lg:text-3xl">[^<]*</h3>', '<h3 class="font-black tracking-tight text-white text-xl sm:text-2xl lg:text-3xl">{ES_TITLE}</h3>', HERO_TPL)
HERO_TPL = re.sub(r'<p class="max-w-lg text-xs sm:text-\[13px\] font-medium leading-6 text-slate-200/90">[^<]*</p>', '<p class="max-w-lg text-xs sm:text-[13px] font-medium leading-6 text-slate-200/90">{ES_EXCERPT}</p>', HERO_TPL)
# شماره ۰۱
HERO_TPL = HERO_TPL.replace('>۰۱</span>', '>{ES_NUM}</span>')
HERO_TPL = HERO_TPL.replace('src="images/light-tunnel.jpg"', 'src="{ES_IMG}"')
CARD_STACK_TPL = CARD_STACK_TPL.replace('>۰۲</span>', '>{ES_NUM}</span>')
CARD_STACK_TPL = CARD_STACK_TPL.replace('src="images/light-sphere.jpg"', 'src="{ES_IMG}"')

# اینترو: توکن‌ها
F_HEAD = F_HEAD.replace('>نمونه‌کارهای شاخص</span>', '>{ES_KICKER}</span>')
F_HEAD = F_HEAD.replace('پروژه‌های <span class="text-emerald-700">نورپردازی شهری</span>', 'پروژه‌های <span class="text-emerald-700">{ES_HL}</span>')
F_HEAD = re.sub(r'(<p class="text-sm leading-7 font-medium text-slate-600 sm:text-\[15px\] sm:leading-8">)[^<]*(</p>)', r'\1{ES_EXCERPT}\2', F_HEAD)

os.makedirs(os.path.join(ROOT, 'template-parts/home'), exist_ok=True)
os.makedirs(os.path.join(ROOT, 'template-parts/cards'), exist_ok=True)
os.makedirs(os.path.join(ROOT, 'template-parts/sections'), exist_ok=True)

# ═══ رپر ═══
open(os.path.join(ROOT, 'template-parts/sections/projects.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن projects — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-projects.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-projects.php', get_defined_vars());
""")

def nowdoc(name, body):
    return f"${name} = <<<'ESP3'\n{body}\nESP3;\n"

frags = ''.join([
    nowdoc('F_HEAD', F_HEAD),
    nowdoc('HERO_WRAP_OPEN', HERO_WRAP_OPEN),
    nowdoc('HERO_TPL', HERO_TPL),
    nowdoc('HERO_WRAP_CLOSE', HERO_WRAP_CLOSE),
    nowdoc('F_STACK_OPEN', F_STACK_OPEN),
    nowdoc('ITEM_OPEN_TPL', ITEM_OPEN_TPL),
    nowdoc('CARD_STACK_TPL', CARD_STACK_TPL),
    nowdoc('F_TAIL', F_TAIL),
])

PHP = """<?php
/**
 * سکشن پروژه‌های شاخص (projects) — برش بایت‌دقیق میرور + WP-Query داینامیک
 * فرگمنت‌ها با temp/gen_projects5.py از refs/projects.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];
$es_kicker  = (string) ($es_f['kicker'] ?? '');
$es_hl      = (string) ($es_f['title_highlight'] ?? '');
$es_excerpt = (string) ($es_f['excerpt'] ?? '');
$es_count   = max(1, (int) ($es_f['count'] ?? 3));
$es_picked  = array_values(array_filter(array_map('intval', preg_split('/[\\s,،]+/u', (string) ($es_f['handpicked'] ?? '')))));

/** آرگومان‌های کوئری پروژه‌ها — سبک (بدون شمارش کل) */
$es_query_args = [
    'post_type'        => 'projects',
    'posts_per_page'   => $es_count,
    'post_status'      => 'publish',
    'no_found_rows'    => true,
    'suppress_filters' => false,
];
if ($es_picked !== []) {
    $es_query_args['post__in'] = $es_picked;
    $es_query_args['orderby']  = 'post__in';
}

/**
 * فیلتر es_projects_query_args — توسعه‌پذیری کوئری سکشن پروژه‌ها
 */
$es_query_args = (array) apply_filters('es_projects_query_args', $es_query_args, $args);

$es_items = [];
foreach (get_posts($es_query_args) as $es_post) {
    $es_thumb = (int) get_post_meta((int) $es_post->ID, '_thumbnail_id', true);
    $es_terms = get_the_terms((int) $es_post->ID, 'project_type');
    $es_type_names = is_array($es_terms) ? implode(' · ', array_map(static fn($t): string => (string) $t->name, $es_terms)) : '';
    $es_items[] = [
        'url'      => (string) get_permalink($es_post),
        'img'      => es_media_url($es_thumb),
        'alt'      => (string) ($es_post->post_title ?? ''),
        'num'      => es_fa_num(sprintf('%02d', count($es_items) + 1)),
        'types'    => $es_type_names,
        'title'    => (string) ($es_post->post_title ?? ''),
        'excerpt'  => (string) ($es_post->post_excerpt ?? ''),
        'delay'    => rtrim(rtrim(sprintf('%.2f', 0.05 * (count($es_items) + 1)), '0'), '.'),
    ];
}

/** فیلتر es_projects_items — توسعه‌پذیری آیتم‌های رندر */
$es_items = (array) apply_filters('es_projects_items', $es_items, $args);
if ($es_items === []) {
    return; // سکشن خالی → رندر نمی‌شود
}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/projects.html
 */
FRAGBLOCK

// ═══ رندر ═══
echo strtr($F_HEAD, [
    '{ES_KICKER}'  => $es_kicker,
    '{ES_HL}'      => $es_hl,
    '{ES_EXCERPT}' => $es_excerpt,
]);

// کارت نخست: بزرگ (col-span-3)
$es_first = array_shift($es_items);
echo $HERO_WRAP_OPEN;
echo strtr($HERO_TPL, [
    '{ES_URL}'      => (string) $es_first['url'],
    '{ES_IMG}'      => (string) $es_first['img'],
    '{ES_ALT}'      => (string) $es_first['alt'],
    '{ES_NUM}'      => (string) $es_first['num'],
    '{ES_TYPES}'    => (string) $es_first['types'],
    '{ES_TITLE}'    => (string) $es_first['title'],
    '{ES_EXCERPT}'  => (string) $es_first['excerpt'],
]);
echo $HERO_WRAP_CLOSE;

// باقی کارت‌ها: پشته (col-span-2)
if ($es_items !== []) {
    echo $F_STACK_OPEN;
    foreach ($es_items as $es_row) {
        echo strtr($ITEM_OPEN_TPL, ['{ES_DELAY}' => (string) $es_row['delay']]);
        echo strtr($CARD_STACK_TPL, [
            '{ES_URL}'      => (string) $es_row['url'],
            '{ES_IMG}'      => (string) $es_row['img'],
            '{ES_ALT}'      => (string) $es_row['alt'],
            '{ES_NUM}'      => (string) $es_row['num'],
            '{ES_TYPES}'    => (string) $es_row['types'],
            '{ES_TITLE}'    => (string) $es_row['title'],
            '{ES_EXCERPT}'  => (string) $es_row['excerpt'],
        ]);
        echo '</div>';
    }
}

echo $F_TAIL;
"""
PHP = PHP.replace('FRAGBLOCK', frags)
open(os.path.join(ROOT, 'template-parts/home/section-projects.php'), 'w', encoding='utf-8').write(PHP)
print('section-projects.php bytes:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-projects.php')))
