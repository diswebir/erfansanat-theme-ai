#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۵.۱۲الف — دو سکشن جاافتاده: projects-gallery + about-intro (برش بایت‌دقیق).
اجرا: python3 temp/gen_gi5.py
"""
import os, re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
REFS = os.path.join(ROOT, 'temp/phase-05-homepage-and-sections/refs')

def nowdoc(name, body, tag):
    return f"${name} = <<<'{tag}'\n{body}\n{tag};\n"

# ═══════════ about-intro ═══════════
h = open(os.path.join(REFS, 'about-intro.html'), encoding='utf-8').read()
assert h.endswith('</section')
F = h + '>'
F = F.replace('<span>اصالت و هویت صنعتی کارخانه</span>', '<span>{ES_BADGE}</span>')
F = re.sub(r'(<img src=")images/hero\.jpg(" alt=")[^"]*(")', r'\1{ES_BG}\2{ES_BG_ALT}\3', F)
F = F.replace('<span dir="ltr" class="font-mono text-lg font-black tracking-[0.14em] text-white sm:text-xl">ESE</span>', '<span dir="ltr" class="font-mono text-lg font-black tracking-[0.14em] text-white sm:text-xl">{ES_MONO}</span>')
F = F.replace('<span class="text-xs font-extrabold text-emerald-100 sm:text-sm">تاسیس کارخانه از سال ۱۳۸۰ خورشیدی</span>', '<span class="text-xs font-extrabold text-emerald-100 sm:text-sm">{ES_SINCE}</span>')
F = re.sub(r'(<h3 class="mt-4 text-xl font-black leading-snug text-white sm:text-2xl \[text-wrap:balance\]">)[^<]*(</h3>)', r'\1{ES_TITLE}\2', F)
F = re.sub(r'(<p class="mt-3 text-right text-xs leading-7 font-medium text-emerald-50/90 sm:text-\[13\.5px\] sm:leading-7">)[^<]*(</p>)', r'\1{ES_DESC}\2', F)
# سه کارت آمار
for n, t in [(1, 'ISO 9001:2015'), (2, 'درجه حفاظت IP68'), (3, 'ایران\u200cکد GS1')]:
    F = F.replace('<div class="text-[11.5px] xs:text-xs font-black text-white tabular-nums">' + t + '</div>', '<div class="text-[11.5px] xs:text-xs font-black text-white tabular-nums">{ES_S' + str(n) + 'T}</div>')
F = F.replace('<div class="mt-0.5 text-[10px] xs:text-[10.5px] font-semibold text-emerald-100/85">مدیریت کیفیت</div>', '<div class="mt-0.5 text-[10px] xs:text-[10.5px] font-semibold text-emerald-100/85">{ES_S1D}</div>')
F = F.replace('<div class="mt-0.5 text-[10px] xs:text-[10.5px] font-semibold text-emerald-100/85">تزریق مستقیم رزین</div>', '<div class="mt-0.5 text-[10px] xs:text-[10.5px] font-semibold text-emerald-100/85">{ES_S2D}</div>')
F = F.replace('<div class="mt-0.5 text-[10px] xs:text-[10.5px] font-semibold text-emerald-100/85">ثبت رسمی سامانه ستاد</div>', '<div class="mt-0.5 text-[10px] xs:text-[10.5px] font-semibold text-emerald-100/85">{ES_S3D}</div>')
# دو کارت گالری
for n, (img, chip, ttl) in [(1, ('light-tunnel.jpg', 'سازه و المان شهری', 'تونل\u200cهای نوری پیکسلی و کمانی')), (2, ('light-tree.jpg', 'تولید انحصاری', 'درختان نوری هوشمند NRF'))]:
    F = F.replace('<img src="images/' + img + '" alt="', '<img src="{ES_G' + str(n) + '}" alt="{ES_G' + str(n) + '_ALT}"', 1)
    F = F.replace('<div class="text-[10.5px] font-bold text-emerald-300">' + chip + '</div>', '<div class="text-[10.5px] font-bold text-emerald-300">{ES_G' + str(n) + '_CHIP}</div>')
    F = F.replace('<div class="text-xs font-extrabold text-white">' + ttl + '</div>', '<div class="text-xs font-extrabold text-white">{ES_G' + str(n) + '_TITLE}</div>')
F = F.replace('<a href="contact.html" class="inline-flex items-center gap-2 text-xs font-black text-emerald-700 transition-colors hover:text-emerald-900 sm:text-sm"><span>ارتباط مستقیم با کارخانه</span>', '<a href="{ES_CTA_URL}" class="inline-flex items-center gap-2 text-xs font-black text-emerald-700 transition-colors hover:text-emerald-900 sm:text-sm"><span>{ES_CTA}</span>')
need = ['{ES_BADGE}', '{ES_BG}', '{ES_BG_ALT}', '{ES_MONO}', '{ES_SINCE}', '{ES_TITLE}', '{ES_DESC}',
        '{ES_S1T}', '{ES_S1D}', '{ES_S2T}', '{ES_S2D}', '{ES_S3T}', '{ES_S3D}',
        '{ES_G1}', '{ES_G1_ALT}', '{ES_G1_CHIP}', '{ES_G1_TITLE}', '{ES_G2}', '{ES_G2_ALT}', '{ES_G2_CHIP}', '{ES_G2_TITLE}', '{ES_CTA}', '{ES_CTA_URL}']
assert all(t in F for t in need), [t for t in need if t not in F]

open(os.path.join(ROOT, 'template-parts/sections/about-intro.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن about-intro — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-about-intro.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-about-intro.php', get_defined_vars());
""")

ai_php = f"""<?php
/**
 * سکشن معرفی تاسیس شرکت (about-intro) — برش بایت‌دقیق میرور
 * فرگمنت با temp/gen_gi5.py از refs/about-intro.html برش خورده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {{section_id: string, fields: array<string, mixed>, post_id: int}} $args */

$es_f = $args['fields'] ?? [];

/** فیلتر es_about_intro_args — توسعه‌پذیری سکشن معرفی */
$es_args = apply_filters('es_about_intro_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'about-intro')]);
if (is_array($es_args)) {{
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}}

{nowdoc('ES_ABOUT_INTRO', F, 'ESI12')}

echo strtr($ES_ABOUT_INTRO, [
    '{{ES_BADGE}}'     => (string) ($es_f['badge'] ?? ''),
    '{{ES_BG}}'        => es_media_url((string) ($es_f['bg_image'] ?? 'assets/images/hero.jpg')),
    '{{ES_BG_ALT}}'    => (string) ($es_f['bg_alt'] ?? ''),
    '{{ES_MONO}}'      => (string) ($es_f['mono'] ?? ''),
    '{{ES_SINCE}}'     => (string) ($es_f['since'] ?? ''),
    '{{ES_TITLE}}'     => (string) ($es_f['title'] ?? ''),
    '{{ES_DESC}}'      => (string) ($es_f['desc'] ?? ''),
    '{{ES_S1T}}'       => (string) ($es_f['s1_title'] ?? ''),
    '{{ES_S1D}}'       => (string) ($es_f['s1_desc'] ?? ''),
    '{{ES_S2T}}'       => (string) ($es_f['s2_title'] ?? ''),
    '{{ES_S2D}}'       => (string) ($es_f['s2_desc'] ?? ''),
    '{{ES_S3T}}'       => (string) ($es_f['s3_title'] ?? ''),
    '{{ES_S3D}}'       => (string) ($es_f['s3_desc'] ?? ''),
    '{{ES_G1}}'        => es_media_url((string) ($es_f['g1_image'] ?? 'assets/images/light-tunnel.jpg')),
    '{{ES_G1_ALT}}'    => (string) ($es_f['g1_alt'] ?? ''),
    '{{ES_G1_CHIP}}'   => (string) ($es_f['g1_chip'] ?? ''),
    '{{ES_G1_TITLE}}'  => (string) ($es_f['g1_title'] ?? ''),
    '{{ES_G2}}'        => es_media_url((string) ($es_f['g2_image'] ?? 'assets/images/light-tree.jpg')),
    '{{ES_G2_ALT}}'    => (string) ($es_f['g2_alt'] ?? ''),
    '{{ES_G2_CHIP}}'   => (string) ($es_f['g2_chip'] ?? ''),
    '{{ES_G2_TITLE}}'  => (string) ($es_f['g2_title'] ?? ''),
    '{{ES_CTA}}'       => (string) ($es_f['cta_text'] ?? ''),
    '{{ES_CTA_URL}}'   => es_link_url((string) ($es_f['cta_url'] ?? '')),
]);
"""
open(os.path.join(ROOT, 'template-parts/home/section-about-intro.php'), 'w', encoding='utf-8').write(ai_php)
print('about-intro done:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-about-intro.php')))

# ═══════════ projects-gallery ═══════════
h = open(os.path.join(REFS, 'projects-gallery.html'), encoding='utf-8').read()
assert h.endswith('</section')
p1 = h.find('<div data-home2-port-panel="all"')
p2 = h.find('<div data-home2-port-panel="tunnel"')
p3 = h.find('<div data-home2-port-panel="urban"')
tail_i = h.find('<div class="mt-6 flex justify-center">')

HEAD = h[:p1]
HEAD = HEAD.replace('<span>آرشیو تفکیکی پروژه\u200cهای اجرایی</span>', '<span>{ES_KICKER}</span>')
HEAD = HEAD.replace('<h3 class="mt-1 text-lg font-black text-slate-900 sm:text-xl">نمونه‌کارهای اجراشده در <span class="text-emerald-700">کلان‌شهرهای کشور</span></h3>', '<h3 class="mt-1 text-lg font-black text-slate-900 sm:text-xl">{ES_TITLE_PRE}<span class="text-emerald-700">{ES_HL}</span></h3>')
# تب‌ها: سه دکمه → قالب
TAB_BOX_START = HEAD.find('<div role="tablist"')
assert TAB_BOX_START > 0
HEAD_PRE = HEAD[:TAB_BOX_START]
TAB_BOX = HEAD[TAB_BOX_START:]
TAB_OPEN = TAB_BOX[:TAB_BOX.find('<button')]
TAB_CLOSE = TAB_BOX[TAB_BOX.rfind('</button>') + len('</button>'):]
assert TAB_CLOSE == '</div></div>', repr(TAB_CLOSE)  # بستن باکس تب‌ها + ستون هدر
tabs = re.findall(r'<button type="button" role="tab"[^>]*data-home2-port-tab="[^"]*"[^>]*>.*?</button>', TAB_BOX, re.S)
assert len(tabs) == 3
TAB1 = tabs[0]
T = TAB1
T = re.sub(r'data-home2-port-tab="[^"]*"', 'data-home2-port-tab="{ES_KEY}"', T)
T = re.sub(r'aria-selected="[^"]*"', 'aria-selected="{ES_SELECTED}"', T)
T = re.sub(r'class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap [^"]*"',
           'class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_TAB_CLS}"', T)
T = re.sub(r'(<span>)[^<]*(</span>)', r'\1{ES_LABEL}\2', T, count=1)
T = re.sub(r'(<span class="rounded-md px-1\.5 py-0\.5 text-\[10px\] font-black tabular-nums bg-white/20 text-white">)[^<]*(</span>)', r'\1{ES_COUNT}\2', T)
assert all(t in T for t in ['{ES_KEY}', '{ES_SELECTED}', '{ES_TAB_CLS}', '{ES_LABEL}', '{ES_COUNT}'])

# کارت پنل (کارت اول پنل all)
cards_all = re.findall(r'<a href="projects/[^"]*" class="card-light group flex h-full flex-col justify-between rounded-2xl p-3">.*?</a>', h[p1:p2], re.S)
assert len(cards_all) == 5, len(cards_all)
CARD = cards_all[0]
C = CARD
C = re.sub(r'(<a href=")[^"]*(")', r'\1{ES_URL}\2', C)
C = re.sub(r'(<img src=")images/[^"]*(" alt=")[^"]*(")', r'\1{ES_IMG}\2{ES_TITLE}\3', C)
C = re.sub(r'(<span class="absolute bottom-2\.5 right-2\.5 rounded-md bg-slate-950/55 px-2 py-0\.5 text-\[10\.5px\] font-extrabold text-white backdrop-blur-xs">)[^<]*(</span>)', r'\1{ES_CITY}\2', C)
C = re.sub(r'(<div class="text-\[11px\] font-bold text-emerald-700 tabular-nums">)[^<]*(</div>)', r'\1{ES_META}\2', C)
C = re.sub(r'(<h4 class="mt-1 line-clamp-2 min-h-\[2\.5rem\] text-right text-xs sm:text-\[13px\] leading-5 font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700">)[^<]*(</h4>)', r'\1{ES_TITLE}\2', C)
assert all(t in C for t in ['{ES_URL}', '{ES_IMG}', '{ES_CITY}', '{ES_META}', '{ES_TITLE}'])

# اسکلت کارت‌ها هم‌الگو
def norm_card(s):
    s = re.sub(r'<svg.*?</svg>', 'SVG', s, flags=re.S)
    s = re.sub(r'(href|src|alt)="[^"]*"', r'\1="X"', s)
    s = re.sub(r'>([^<>]+)</', '>X</', s)
    s = re.sub(r'\d', 'N', s)
    return s
for coll, name in [(cards_all, 'all')]:
    n0 = norm_card(CARD)
    for k, c in enumerate(coll):
        assert norm_card(c) == n0, f'{name} card{k} differs'

# پنل‌ها: بستن panel div + پنل‌های tunnel/urban
TAIL = h[tail_i:]
TAIL = TAIL.replace('<a href="projects.html" class="group inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200/90 bg-white px-6 py-3 text-xs sm:text-sm font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800"><span>مشاهده آرشیو کامل پروژه\u200cها</span>', '<a href="{ES_ARCHIVE_URL}" class="group inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200/90 bg-white px-6 py-3 text-xs sm:text-sm font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800"><span>{ES_ARCHIVE}</span>')
assert TAIL.endswith('</section')
TAIL = TAIL[:-len('</section')]  # + '>' در PHP

# بستن‌های بین پنل‌ها: هر پنل با </div> بسته می‌شود
BETWEEN = h[p1:p2]
close_all = BETWEEN[BETWEEN.rfind(cards_all[4]) + len(cards_all[4]):]
assert close_all == '</div>', repr(close_all)

open(os.path.join(ROOT, 'template-parts/sections/projects-gallery.php'), 'w', encoding='utf-8').write(
"""<?php
/**
 * سکشن projects-gallery — مسیر رندر SectionManager؛ مارک‌آپ در template-parts/home/section-projects-gallery.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

es_get_template('template-parts/home/section-projects-gallery.php', get_defined_vars());
""")

frags = ''.join([
    nowdoc('F_HEAD', HEAD_PRE, 'ESG12'),
    nowdoc('TAB_OPEN', TAB_OPEN, 'ESG12'),
    nowdoc('TAB_TPL', T, 'ESG12'),
    nowdoc('TAB_CLOSE', TAB_CLOSE, 'ESG12'),
    nowdoc('CARD_TPL', C, 'ESG12'),
    nowdoc('F_TAIL', TAIL, 'ESG12'),
])

pg_php = f"""<?php
/**
 * سکشن گالری پروژه‌های صفحه اصلی (projects-gallery) — برش بایت‌دقیق میرور + سه پنل داینامیک
 * پنل‌ها: تب all = آخرین پروژه‌ها؛ تب‌های tunnel/urban = فیلتر بر اساس نام types هر تب.
 * فرگمنت‌ها با temp/gen_gi5.py از refs/projects-gallery.html برش خورده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array {{section_id: string, fields: array<string, mixed>, post_id: int}} $args */

$es_f = $args['fields'] ?? [];
$es_tabs = is_array($es_f['tabs'] ?? null) ? array_values($es_f['tabs']) : [];
if ($es_tabs === []) {{
    return; // بدون تب → سکشن مخفی
}}

/** فیلتر es_projects_gallery_args — توسعه‌پذیری سکشن گالری پروژه‌ها */
$es_args = apply_filters('es_projects_gallery_args', ['fields' => $es_f, 'tabs' => $es_tabs, 'section_id' => (string) ($args['section_id'] ?? 'projects-gallery')]);
if (is_array($es_args)) {{
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
    $es_tabs = is_array($es_args['tabs'] ?? null) ? array_values($es_args['tabs']) : $es_tabs;
}}

/** تعداد کارت هر پنل (میرور: ۵) */
$es_per_tab = max(1, (int) ($es_f['per_tab'] ?? 5));

/** منبع: آخرین پروژه‌ها (فیلتر es_projects_gallery_query_args) */
$es_qargs = apply_filters('es_projects_gallery_query_args', [
    'post_type'      => 'projects',
    'post_status'    => 'publish',
    'numberposts'    => 30,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);
$es_pool = get_posts(is_array($es_qargs) ? $es_qargs : []);

/** گروه‌بندی بر اساس types هر تب (نام‌های تاکسونومی با «|» جدا شده‌اند؛ تب all همه را می‌گیرد) */
$es_grouped = [];
foreach ($es_tabs as $es_tab) {{
    if (!is_array($es_tab)) {{
        continue;
    }}
    $es_key = (string) ($es_tab['key'] ?? '');
    if ($es_key === '') {{
        continue;
    }}
    if ($es_key === 'all') {{
        $es_grouped[$es_key] = array_slice($es_pool, 0, $es_per_tab);
        continue;
    }}
    $es_want = array_filter(array_map('trim', explode('|', (string) ($es_tab['types'] ?? ''))));
    $es_rows = [];
    foreach ($es_pool as $es_p) {{
        $es_terms = get_the_terms((int) ($es_p->ID ?? 0), 'project_type');
        if (!is_array($es_terms)) {{
            continue;
        }}
        foreach ($es_terms as $es_t) {{
            if (in_array((string) $es_t->name, $es_want, true)) {{
                $es_rows[] = $es_p;
                break;
            }}
        }}
        if (count($es_rows) >= $es_per_tab) {{
            break;
        }}
    }}
    $es_grouped[$es_key] = $es_rows;
}}

$es_any = false;
foreach ($es_grouped as $es_rows) {{
    if ($es_rows !== []) {{
        $es_any = true;
        break;
    }}
}}
if (!$es_any) {{
    return; // بدون پروژه → سکشن مخفی
}}

/**
 * فرگمنت‌های ثابت — برش بایت‌دقیق از refs/projects-gallery.html
 */
{frags}

echo strtr($F_HEAD, [
    '{{ES_KICKER}}'    => (string) ($es_f['kicker'] ?? ''),
    '{{ES_TITLE_PRE}}' => (string) ($es_f['title_pre'] ?? ''),
    '{{ES_HL}}'        => (string) ($es_f['title_hl'] ?? ''),
]);

// جعبه تب‌ها
echo $TAB_OPEN;
foreach ($es_tabs as $es_ti => $es_tab) {{
    if (!is_array($es_tab)) {{
        continue;
    }}
    $es_key = (string) ($es_tab['key'] ?? '');
    $es_count = count($es_grouped[$es_key] ?? []);
    if ($es_ti === 0) {{
        echo strtr($TAB_TPL, ['{{ES_KEY}}' => esc_attr($es_key), '{{ES_SELECTED}}' => 'true', '{{ES_TAB_CLS}}' => 'bg-emerald-700 text-white shadow-xs', '{{ES_LABEL}}' => esc_html((string) ($es_tab['label'] ?? '')), '{{ES_COUNT}}' => es_fa_num((string) $es_count)]);
    }} else {{
        echo strtr($TAB_TPL, ['{{ES_KEY}}' => esc_attr($es_key), '{{ES_SELECTED}}' => 'false', '{{ES_TAB_CLS}}' => 'bg-white text-slate-600 hover:bg-emerald-50 hover:text-emerald-800', '{{ES_LABEL}}' => esc_html((string) ($es_tab['label'] ?? '')), '{{ES_COUNT}}' => es_fa_num((string) $es_count)]);
    }}
}}
echo $TAB_CLOSE;

// پنل‌ها
$es_first = true;
foreach ($es_tabs as $es_tab) {{
    if (!is_array($es_tab)) {{
        continue;
    }}
    $es_key = (string) ($es_tab['key'] ?? '');
    $es_rows = $es_grouped[$es_key] ?? [];
    echo '<div data-home2-port-panel="' . esc_attr($es_key) . '" class="grid mt-5 grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">';
    foreach ($es_rows as $es_p) {{
        $es_pid = (int) ($es_p->ID ?? 0);
        $es_terms = get_the_terms($es_pid, 'project_type');
        $es_type = (is_array($es_terms) && isset($es_terms[0]->name)) ? (string) $es_terms[0]->name : '';
        $es_terms_c = get_the_terms($es_pid, 'city');
        $es_city = (is_array($es_terms_c) && isset($es_terms_c[0]->name)) ? (string) $es_terms_c[0]->name : '';
        // سال: متا es_project_year؛ وگرنه استخراج از finish-pro-time
        $es_year = (string) get_post_meta($es_pid, 'es_project_year', true);
        if ($es_year === '') {{
            if (preg_match('/([\\x{{06F0}}-\\x{{06F9}}]{{4}})/u', (string) get_post_meta($es_pid, 'finish-pro-time', true), $es_m) === 1) {{
                $es_year = $es_m[1];
            }}
        }}
        $es_img_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);
        echo strtr($CARD_TPL, [
            '{{ES_URL}}'   => esc_url((string) get_permalink($es_pid)),
            '{{ES_IMG}}'   => es_media_url($es_img_id > 0 ? $es_img_id : 'assets/images/light-element.jpg'),
            '{{ES_TITLE}}' => esc_attr((string) ($es_p->post_title ?? '')),
            '{{ES_CITY}}'  => esc_html($es_city),
            '{{ES_META}}'  => esc_html(trim($es_type . ' · سال ' . $es_year, ' ·')),
        ]);
    }}
    echo '</div>';
    $es_first = false;
}}

echo strtr($F_TAIL, [
    '{{ES_ARCHIVE_URL}}' => es_page_url('projects'),
    '{{ES_ARCHIVE}}'     => (string) ($es_f['archive_text'] ?? ''),
]);
echo '>';
"""
open(os.path.join(ROOT, 'template-parts/home/section-projects-gallery.php'), 'w', encoding='utf-8').write(pg_php)
print('projects-gallery done:', os.path.getsize(os.path.join(ROOT, 'template-parts/home/section-projects-gallery.php')))
