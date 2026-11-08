#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
ژنراتور ۷.۲ — archive-projects (آرشیو پروژه‌ها)
برش بایت‌دقیق از uploads/static-mirror/projects.html
خروجی‌ها: template-parts/archive/project-filters.php، template-parts/archive/pagination.php (بازنویسی مشترک)،
archive-projects.php، inc/Frontend/ProjectArchive.php
استراتژی: بدنه/آیکون‌ها static عین میرور؛ مقادیر داینامیک {ES_*} توکن می‌شوند.
"""
import os
import re

ROOT = '/home/user/erfansanat-theme-ai'
s = open(os.path.join(ROOT, 'uploads/static-mirror/projects.html'), encoding='utf-8').read()
shop = open(os.path.join(ROOT, 'uploads/static-mirror/shop.html'), encoding='utf-8').read()


def nowdoc(name, body, tag):
    return "$" + name + " = <<<'{t}'\n{b}\n{t};\n".format(t=tag, b=body)


def tok(R, old, new):
    assert R.count(old) >= 1, old[:70]
    return R.replace(old, new, 1)


# ═══════════ برش رگیون‌ها ═══════════
m0 = s.index('<main>') + len('<main>')
hero0 = m0
sec0 = s.index('<section id="archive"', m0)
aside0 = s.index('<aside aria-label="فیلتر و آرشیو پروژه\u200cها"', sec0)
aside1 = s.index('</aside>', aside0) + len('</aside>')
grid0 = s.index('<div data-project-grid="true"', aside1)
items0 = s.index('<div data-project-item="true"', grid0)
nav0 = s.index('<nav aria-label="صفحه\u200cبندی پروژه\u200cها', items0)
grid1 = s.rindex('</div>', items0, nav0)
assert s[grid1 + 6:nav0] == '', 'grid close drift'
nav1 = s.index('</nav>', nav0) + len('</nav>')
empty0 = s.index('<div data-project-empty="true"', nav1)
sec1 = s.index('</section>', empty0) + len('</section>')
cta0 = s.index('<section id="contact"', sec1)
assert s[sec1:cta0].strip() == '', 'gap between archive and CTA'
m1 = s.index('</main>', cta0)

HERO_RAW = s[hero0:sec0]
SEC_OPEN_RAW = s[sec0:aside0]
ASIDE_RAW = s[aside0:aside1]
TOPBAR_RAW = s[aside1:grid0]
GRID_OPEN_RAW = s[grid0:items0]
ITEMS_RAW = s[items0:grid1]
NAV_RAW = s[nav0:nav1]
EMPTY_RAW = s[nav1:sec1]
CTA_RAW = s[cta0:m1]

raw_sum = (HERO_RAW + SEC_OPEN_RAW + ASIDE_RAW + TOPBAR_RAW + GRID_OPEN_RAW + ITEMS_RAW
           + '</div>' + NAV_RAW + EMPTY_RAW + CTA_RAW)
assert raw_sum == s[m0:m1], 'boundaries drift'
print('slices OK: hero', len(HERO_RAW), 'sec_open', len(SEC_OPEN_RAW), 'aside', len(ASIDE_RAW),
      'topbar', len(TOPBAR_RAW), 'grid_open', len(GRID_OPEN_RAW), 'items', len(ITEMS_RAW),
      'nav', len(NAV_RAW), 'empty', len(EMPTY_RAW), 'cta', len(CTA_RAW))

# ═══════════ HERO ═══════════
HERO = tok(HERO_RAW, '<a href="index.html"', '<a href="{ES_HOME_URL}"')
HERO = tok(HERO, 'lg:leading-[1.22]">پروژه\u200cهای <br/>', 'lg:leading-[1.22]">{ES_H1_A}<br/>')
HERO = tok(HERO, '>نورپردازی شهری</span>', '>{ES_H1_B}</span>')
HERO = tok(HERO, 'lg:text-[16px]">روایتی از هنر، مهندسی و نور در سراسر ایران و عراق؛ از تونل\u200cهای نوری، نیم\u200cهرم نوری اهواز و میادین اصلی تا نورپردازی پل\u200cها و ساختمان\u200cهای شاخص</p>',
           'lg:text-[16px]">{ES_HERO_DESC}</p>')
HERO = tok(HERO, 'src="images/light-tunnel.jpg"', 'src="{ES_IMG_TUNNEL}"')
HERO = tok(HERO, 'src="images/light-sphere.jpg"', 'src="{ES_IMG_SPHERE}"')
HERO = tok(HERO, 'src="images/light-element.jpg"', 'src="{ES_IMG_ELEMENT}"')
assert 'images/' not in HERO, 'hero img leftovers'

H1_A = 'پروژه\u200cهای '
H1_B = 'نورپردازی شهری'
H1_DESC = 'روایتی از هنر، مهندسی و نور در سراسر ایران و عراق؛ از تونل\u200cهای نوری، نیم\u200cهرم نوری اهواز و میادین اصلی تا نورپردازی پل\u200cها و ساختمان\u200cهای شاخص'
IMG_T = 'assets/images/light-tunnel.jpg'
IMG_S = 'assets/images/light-sphere.jpg'
IMG_E = 'assets/images/light-element.jpg'

hero_rebuild = (HERO.replace('{ES_HOME_URL}', 'index.html')
                .replace('{ES_H1_A}', H1_A).replace('{ES_H1_B}', H1_B)
                .replace('{ES_HERO_DESC}', H1_DESC)
                .replace('{ES_IMG_TUNNEL}', 'images/light-tunnel.jpg')
                .replace('{ES_IMG_SPHERE}', 'images/light-sphere.jpg')
                .replace('{ES_IMG_ELEMENT}', 'images/light-element.jpg'))
assert hero_rebuild == HERO_RAW, 'hero rebuild'

# ═══════════ ASIDE → project-filters ═══════════
ASIDE = tok(ASIDE_RAW, '<span data-project-count="true">۱۲</span> پروژه در لیست',
            '<span data-project-count="true">{ES_COUNT}</span> پروژه در لیست')
tnav0 = '<nav aria-label="آرشیو نوع پروژه" class="space-y-1.5">'
t0 = ASIDE.index(tnav0) + len(tnav0)
t1 = ASIDE.index('</nav>', t0)
TYPE_ROWS_RAW = ASIDE[t0:t1]
ASIDE = ASIDE[:t0] + '{ES_TYPE_ROWS}' + ASIDE[t1:]
cnav0 = '<nav aria-label="آرشیو شهر اجرای پروژه" class="grid grid-cols-2 gap-1.5">'
c0 = ASIDE.index(cnav0) + len(cnav0)
c1 = ASIDE.index('</nav>', c0)
CITY_ROWS_RAW = ASIDE[c0:c1]
ASIDE = ASIDE[:c0] + '{ES_CITY_ROWS}' + ASIDE[c1:]

_on0 = TYPE_ROWS_RAW.index('<a href="projects.html"')
_on1 = TYPE_ROWS_RAW.index('</a>', _on0) + len('</a>')
ROW_ON = tok(TYPE_ROWS_RAW[_on0:_on1], '<a href="projects.html"', '<a href="{ES_URL}"')
ROW_ON = tok(ROW_ON, '<span>همه انواع پروژه\u200cها</span>', '<span>{ES_LABEL}</span>')
ROW_ON = tok(ROW_ON, 'text-white">۱۲</span>', 'text-white">{ES_COUNT}</span>')
_off0 = TYPE_ROWS_RAW.index('<a href="project_type/square-lighting.html"')
_off1 = TYPE_ROWS_RAW.index('</a>', _off0) + len('</a>')
ROW_OFF = tok(TYPE_ROWS_RAW[_off0:_off1], '<a href="project_type/square-lighting.html"', '<a href="{ES_URL}"')
ROW_OFF = tok(ROW_OFF, '<span>نورپردازی میدان</span>', '<span>{ES_LABEL}</span>')
ROW_OFF = tok(ROW_OFF, 'text-slate-500">۳</span>', 'text-slate-500">{ES_COUNT}</span>')
_c0 = CITY_ROWS_RAW.index('<a href="city/esfahan.html"')
_c1 = CITY_ROWS_RAW.index('</a>', _c0) + len('</a>')
ROW_CITY = tok(CITY_ROWS_RAW[_c0:_c1], '<a href="city/esfahan.html"', '<a href="{ES_URL}"')
ROW_CITY = tok(ROW_CITY, '<span class="truncate">اصفهان</span>', '<span class="truncate">{ES_NAME}</span>')
ROW_CITY = tok(ROW_CITY, 'text-slate-500">۲</span>', 'text-slate-500">{ES_COUNT}</span>')

type_rows = re.findall(r'<a href="project_type/([^"]+)" class="flex items-center justify-between rounded-xl px-3\.5 py-2\.5 text-\[12\.5px\] font-bold transition-all text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800"><span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-500/60"></span><span>([^<]+)</span></span><span class="rounded-md px-2 py-0\.5 text-\[11px\] font-extrabold bg-slate-100 text-slate-500">([^<]+)</span></a>', TYPE_ROWS_RAW)
assert len(type_rows) == 8, type_rows
city_rows = re.findall(r'<a href="city/([^"]+)" class="flex items-center justify-between rounded-xl border px-2\.5 py-2 text-\[11\.5px\] font-bold transition-all border-slate-200/80 bg-slate-50/70 text-slate-700 hover:border-emerald-300 hover:bg-emerald-50/50"><span class="truncate">([^<]+)</span><span class="rounded px-1\.5 py-0\.5 text-\[10px\] font-extrabold bg-white text-slate-500">([^<]+)</span></a>', CITY_ROWS_RAW)
assert len(city_rows) == 10, city_rows


def row_on(url, label, count):
    return ROW_ON.replace('{ES_URL}', url).replace('{ES_LABEL}', label).replace('{ES_COUNT}', count)


def row_off(url, label, count):
    return ROW_OFF.replace('{ES_URL}', url).replace('{ES_LABEL}', label).replace('{ES_COUNT}', count)


def row_city(url, label, count):
    return ROW_CITY.replace('{ES_URL}', url).replace('{ES_NAME}', label).replace('{ES_COUNT}', count)


types_rebuild = row_on('projects.html', 'همه انواع پروژه\u200cها', '۱۲') + ''.join(
    row_off('project_type/%s.html' % slug[:-5], name, cnt) for slug, name, cnt in type_rows)
cities_rebuild = ''.join(row_city('city/%s.html' % slug[:-5], name, cnt) for slug, name, cnt in city_rows)
assert types_rebuild == TYPE_ROWS_RAW, 'types rebuild'
assert cities_rebuild == CITY_ROWS_RAW, 'cities rebuild'
aside_rebuild = (ASIDE.replace('{ES_COUNT}', '۱۲')
                 .replace('{ES_TYPE_ROWS}', types_rebuild).replace('{ES_CITY_ROWS}', cities_rebuild))
assert aside_rebuild == ASIDE_RAW, 'aside rebuild'

# ═══════════ TOPBAR ═══════════
TOPBAR = tok(TOPBAR_RAW, 'همه پروژه\u200cهای نورپردازی شهری', '{ES_TOP_TITLE}')
TOPBAR = tok(TOPBAR, '<span data-project-count="true">۱۲</span> پروژه</span>', '<span data-project-count="true">{ES_COUNT}</span> پروژه</span>')
TOPBAR = tok(TOPBAR, '<option value="default" selected="">', '<option value="default"{ES_SEL_DEFAULT}>')
TOPBAR = tok(TOPBAR, '<option value="popular">', '<option value="popular"{ES_SEL_POPULAR}>')
TOPBAR = tok(TOPBAR, '<option value="rating">', '<option value="rating"{ES_SEL_RATING}>')
topbar_rebuild = (TOPBAR.replace('{ES_TOP_TITLE}', 'همه پروژه\u200cهای نورپردازی شهری')
                  .replace('{ES_COUNT}', '۱۲')
                  .replace('{ES_SEL_DEFAULT}', ' selected=""').replace('{ES_SEL_POPULAR}', '').replace('{ES_SEL_RATING}', ''))
assert topbar_rebuild == TOPBAR_RAW, 'topbar rebuild'

# ═══════════ NAV → pagination مشترک ═══════════
NAV = tok(NAV_RAW, 'aria-label="صفحه\u200cبندی پروژه\u200cها"', 'aria-label="{ES_PAGI_LABEL}"')
NAV = tok(NAV, 'data-pagination-root="projects"', 'data-pagination-root="{ES_ROOT}"')
NAV = tok(NAV, '<span data-pagination-current="true" class="font-black text-emerald-800">۱</span>',
          '<span data-pagination-current="true" class="font-black text-emerald-800">{ES_CUR}</span>')
NAV = tok(NAV, '<span data-pagination-total="true" class="font-black text-slate-900">۲</span>',
          '<span data-pagination-total="true" class="font-black text-slate-900">{ES_TOTAL_PAGES}</span>')
NAV = tok(NAV, '<span data-project-count="true">۱۲</span>', '<span {ES_COUNT_ATTR}>{ES_TOTAL_PRODUCTS}</span>')
NAV = tok(NAV, ' پروژه)</div>', ' {ES_UNIT})</div>')
NAV = tok(NAV, 'data-page-prev="true" disabled="" class=', 'data-page-prev="true"{ES_PREV_ATTR} class=')
p0 = NAV.index('<button type="button" data-page-num="1"')
p1 = NAV.index('</button>', NAV.index('data-page-num="2"')) + len('</button>')
NAV = NAV[:p0] + '{ES_PAGES_HTML}' + NAV[p1:]
NAV = tok(NAV, 'cursor-pointer"><svg xmlns', 'cursor-pointer"{ES_PREV_URL}><svg xmlns')
NAV = tok(NAV, 'cursor-pointer"><span>بعدی</span>', 'cursor-pointer"{ES_NEXT_URL}><span>بعدی</span>')


def nav_norm(x):
    x = re.sub(r'aria-label="[^"]*"', 'aria-label="L"', x)
    x = re.sub(r'data-pagination-root="[^"]*"', 'data-pagination-root="R"', x)
    x = re.sub(r'data-(?:project|product)-count="true"', 'data-count="true"', x)
    x = re.sub(r'\(مجموعاً <span data-count="true">[^<]*</span> [^)]*\)', '(مجموعاً <span data-count="true">N</span> U)', x)
    x = re.sub(r'<button type="button" data-page-num="1".*?(?=<button type="button" data-page-next)', 'PAGES', x, flags=re.S)
    x = x.replace(' disabled=""', ' DIS')
    x = re.sub(r'>[۰-۹]+</span>', '>N</span>', x)
    return x


SHOP_NAV_RAW = shop[shop.index('<nav aria-label="صفحه\u200cبندی محصولات"'):shop.index('</nav>', shop.index('<nav aria-label="صفحه\u200cبندی محصولات"')) + 6]
assert nav_norm(NAV_RAW) == nav_norm(SHOP_NAV_RAW), 'nav template mismatch (projects vs products)'

# ═══════════ EMPTY ═══════════
EMPTY = tok(EMPTY_RAW, 'class="hidden rounded-2xl', 'class="{ES_EMPTY_CLS} rounded-2xl')
EMPTY = tok(EMPTY, 'href="projects.html"', 'href="{ES_PROJECTS_URL}"')
empty_rebuild = EMPTY.replace('{ES_EMPTY_CLS}', 'hidden').replace('{ES_PROJECTS_URL}', 'projects.html')
assert empty_rebuild == EMPTY_RAW, 'empty rebuild'

# ═══════════ نوشتن فایل‌ها ═══════════
os.makedirs(os.path.join(ROOT, 'template-parts/archive'), exist_ok=True)
os.makedirs(os.path.join(ROOT, 'inc/Frontend'), exist_ok=True)


def write(path, php):
    open(os.path.join(ROOT, path), 'w', encoding='utf-8').write(php)
    print(path, 'written:', len(php))


# ── ۱) project-filters.php ──
FILTERS = (
    "<?php\n"
    "/**\n"
    " * سایدبار فیلتر و آرشیو پروژه‌ها — برش بایت‌دقیق میرور projects.html\n"
    " * فرگمنت با temp/gen_p72.py تولید شده است.\n"
    " *\n"
    " * @package ErfanSanat\n"
    " */\n"
    "declare(strict_types=1);\n\n"
    + nowdoc('ES_ASIDE', ASIDE, 'ESP72A')
    + "\n"
    + nowdoc('ES_ROW_ON', ROW_ON, 'ESP72RON')
    + "\n"
    + nowdoc('ES_ROW_OFF', ROW_OFF, 'ESP72ROFF')
    + "\n"
    + nowdoc('ES_ROW_CITY', ROW_CITY, 'ESP72RC')
    + """$es_args = $args ?? [];
$es_count = max(0, (int) ($es_args['count'] ?? 0));
$es_active_type = (string) ($es_args['active_type'] ?? '');
$es_active_city = (string) ($es_args['active_city'] ?? '');
$es_projects_url = function_exists('es_page_url') ? es_page_url('projects') : home_url('/projects/');

$es_row = static function (string $tpl, string $url, string $label, string $count): string {
    return strtr($tpl, [
        '{ES_URL}'   => esc_url($url),
        '{ES_LABEL}' => esc_html($label),
        '{ES_NAME}'  => esc_html($label),
        '{ES_COUNT}' => esc_html($count),
    ]);
};

$es_types_html = $es_active_type === ''
    ? $es_row($ES_ROW_ON, $es_projects_url, 'همه انواع پروژه\u200cها', es_fa_num((string) $es_count))
    : $es_row($ES_ROW_OFF, $es_projects_url, 'همه انواع پروژه\u200cها', es_fa_num((string) $es_count));
foreach (get_terms(['taxonomy' => 'project_type', 'hide_empty' => false]) as $es_t) {
    if (is_wp_error($es_t) || !is_object($es_t)) {
        continue;
    }
    $es_on = (string) $es_t->slug === $es_active_type;
    $es_types_html .= $es_row($es_on ? $ES_ROW_ON : $ES_ROW_OFF, (string) get_term_link($es_t), (string) $es_t->name, es_fa_num((string) ($es_t->count ?? 0)));
}

$es_cities_html = '';
foreach (get_terms(['taxonomy' => 'city', 'hide_empty' => false]) as $es_t) {
    if (is_wp_error($es_t) || !is_object($es_t)) {
        continue;
    }
    $es_cities_html .= $es_row($ES_ROW_CITY, (string) get_term_link($es_t), (string) $es_t->name, es_fa_num((string) ($es_t->count ?? 0)));
}

echo strtr($ES_ASIDE, [
    '{ES_COUNT}'     => esc_html(es_fa_num((string) $es_count)),
    '{ES_TYPE_ROWS}' => $es_types_html,
    '{ES_CITY_ROWS}' => $es_cities_html,
]);
"""
)
write('template-parts/archive/project-filters.php', FILTERS)

# ── ۲) pagination.php (بازنویسی مشترک — پیش‌فرض‌ها = محصولات برای سازگاری ۶.۷) ──
PAGI = (
    "<?php\n"
    "/**\n"
    " * صفحه‌بندی مشترک آرشیوها (محصولات/پروژه‌ها) — برش بایت‌دقیق میرور\n"
    " * فرگمنت با temp/gen_p72.py از ناو projects.html تولید شده (سازگار با shop.html).\n"
    " * پیش‌فرض args = محصولات (۶.۷)؛ برای پروژه‌ها: root/count_attr/unit/label پاس داده شود.\n"
    " *\n"
    " * @package ErfanSanat\n"
    " */\n"
    "declare(strict_types=1);\n\n"
    + nowdoc('ES_PAGI', NAV, 'ESP72P')
    + """$es_args = $args ?? [];
$es_current = max(1, (int) ($es_args['current'] ?? 1));
$es_total_pages = max(1, (int) ($es_args['total_pages'] ?? 1));
$es_base = (string) ($es_args['base_url'] ?? '');
$es_root = (string) ($es_args['root'] ?? 'products');
$es_count_attr = (string) ($es_args['count_attr'] ?? 'data-product-count="true"');
$es_unit = (string) ($es_args['unit'] ?? 'محصول');
$es_label = (string) ($es_args['label'] ?? 'صفحه\u200cبندی محصولات');

$es_url = static function (int $n) use ($es_base): string {
    return $n <= 1 ? $es_base : $es_base . (str_contains($es_base, '?') ? '&' : '?') . 'paged=' . $n;
};

if ($es_total_pages <= 1) {
    return; // تک‌صفحه‌ای → بدون نوار
}
$es_pages_html = '';
foreach (range(1, $es_total_pages) as $es_n) {
    if ($es_n === $es_current) {
        $es_pages_html .= '<button type="button" data-page-num="' . $es_n . '" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer bg-emerald-700 text-white shadow-sm shadow-emerald-700/25">' . es_fa_num((string) $es_n) . '</button>';
    } else {
        $es_pages_html .= '<a href="' . esc_url($es_url($es_n)) . '" data-page-num="' . $es_n . '" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer border border-slate-200 bg-white text-slate-700 hover:border-emerald-300 hover:text-emerald-800">' . es_fa_num((string) $es_n) . '</a>';
    }
}
echo strtr($ES_PAGI, [
    '{ES_PAGI_LABEL}'     => esc_html($es_label),
    '{ES_ROOT}'           => esc_attr($es_root),
    '{ES_COUNT_ATTR}'     => esc_attr($es_count_attr),
    '{ES_UNIT}'           => esc_html($es_unit),
    '{ES_CUR}'            => es_fa_num((string) $es_current),
    '{ES_TOTAL_PAGES}'    => es_fa_num((string) $es_total_pages),
    '{ES_TOTAL_PRODUCTS}' => es_fa_num((string) ($es_args['total_products'] ?? 0)),
    '{ES_PREV_ATTR}'      => $es_current <= 1 ? ' disabled=""' : '',
    '{ES_PREV_URL}'       => $es_current > 1 ? ' data-href="' . esc_url($es_url($es_current - 1)) . '"' : '',
    '{ES_PAGES_HTML}'     => $es_pages_html,
    '{ES_NEXT_ATTR}'      => $es_current >= $es_total_pages ? ' disabled=""' : '',
    '{ES_NEXT_URL}'       => $es_current < $es_total_pages ? ' data-href="' . esc_url($es_url($es_current + 1)) . '"' : '',
]);
"""
)
write('template-parts/archive/pagination.php', PAGI)

# ── ۳) archive-projects.php ──
SHELL = (
    "<?php\n"
    "/**\n"
    " * آرشیو پروژه‌ها — برش بایت‌دقیق میرور projects.html؛ فیلترها/گرید/صفحه‌بندی داینامیک\n"
    " * فرگمنت‌ها با temp/gen_p72.py تولید شده‌اند.\n"
    " *\n"
    " * @package ErfanSanat\n"
    " */\n"
    "declare(strict_types=1);\n\n"
    + nowdoc('ES_HERO', HERO, 'ESP72H')
    + "\n"
    + nowdoc('ES_SEC_OPEN', SEC_OPEN_RAW, 'ESP72S')
    + "\n"
    + nowdoc('ES_TOPBAR', TOPBAR, 'ESP72T')
    + "\n"
    + nowdoc('ES_GRID_OPEN', GRID_OPEN_RAW, 'ESP72G')
    + "\n"
    + nowdoc('ES_EMPTY', EMPTY, 'ESP72E')
    + "\n"
    + nowdoc('ES_CTA', CTA_RAW, 'ESP72C')
    + """get_header();

$es_q = $GLOBALS['wp_query'] ?? null;
$es_total = (int) ($GLOBALS['__es_archive_total'] ?? ($es_q->found_posts ?? 0));
$es_paged = max(1, (int) get_query_var('paged'));
$es_per = (int) es_option('archives.projects_per_page', '6');
$es_pages = max(1, (int) ceil($es_total / max(1, $es_per)));
$es_is_empty = $es_total < 1;

$es_get = static fn(string $k): string => isset($_GET[$k]) && is_string($_GET[$k]) ? (string) $_GET[$k] : '';
$es_h1_b = (string) es_option('archives.projects_h1', 'نورپردازی شهری');
$es_h1_desc = (string) es_option('archives.projects_desc', 'روایتی از هنر، مهندسی و نور در سراسر ایران و عراق؛ از تونل\u200cهای نوری، نیم\u200cهرم نوری اهواز و میادین اصلی تا نورپردازی پل\u200cها و ساختمان\u200cهای شاخص');

echo '<main>';
echo strtr($ES_HERO, [
    '{ES_HOME_URL}'   => esc_url(home_url('/')),
    '{ES_H1_A}'       => esc_html('پروژه\u200cهای '),
    '{ES_H1_B}'       => esc_html($es_h1_b),
    '{ES_HERO_DESC}'  => esc_html($es_h1_desc),
    '{ES_IMG_TUNNEL}' => esc_url(es_media_url('assets/images/light-tunnel.jpg')),
    '{ES_IMG_SPHERE}' => esc_url(es_media_url('assets/images/light-sphere.jpg')),
    '{ES_IMG_ELEMENT}' => esc_url(es_media_url('assets/images/light-element.jpg')),
]);
echo strtr($ES_SEC_OPEN, []);

ob_start();
es_get_template('template-parts/archive/project-filters.php', [
    'count'       => $es_total,
    'active_type' => $es_get('type'),
    'active_city' => $es_get('location'),
]);
echo (string) ob_get_clean();

echo strtr($ES_TOPBAR, [
    '{ES_TOP_TITLE}'    => esc_html('همه پروژه\u200cهای ' . $es_h1_b),
    '{ES_COUNT}'        => es_fa_num((string) $es_total),
    '{ES_SEL_DEFAULT}'  => $es_get('sort') === '' || $es_get('sort') === 'default' ? ' selected=""' : '',
    '{ES_SEL_POPULAR}'  => $es_get('sort') === 'popular' ? ' selected=""' : '',
    '{ES_SEL_RATING}'   => $es_get('sort') === 'rating' ? ' selected=""' : '',
]);

echo strtr($ES_GRID_OPEN, []);
if (!$es_is_empty) {
    $es_i = 0;
    foreach (($es_q->posts ?? []) as $es_post) {
        ob_start();
        es_get_template('template-parts/cards/card-project.php', ['post' => $es_post, 'custom' => $es_i % 2 === 0 ? '0.04' : '0.1']);
        echo (string) ob_get_clean();
        $es_i++;
    }
}
echo '</div>';

if (!$es_is_empty && $es_pages > 1) {
    ob_start();
    es_get_template('template-parts/archive/pagination.php', [
        'current'        => $es_paged,
        'total_pages'    => $es_pages,
        'total_products' => $es_total,
        'base_url'       => get_post_type_archive_link('projects'),
        'root'           => 'projects',
        'count_attr'     => 'data-project-count="true"',
        'unit'           => 'پروژه',
        'label'          => 'صفحه\u200cبندی پروژه\u200cها',
    ]);
    echo (string) ob_get_clean();
}

echo strtr($ES_EMPTY, [
    '{ES_EMPTY_CLS}'    => $es_is_empty ? 'block' : 'hidden',
    '{ES_PROJECTS_URL}' => esc_url(es_page_url('projects')),
]);
echo strtr($ES_CTA, []);
echo '</main>';
get_footer();
"""
)
write('archive-projects.php', SHELL)

# ── ۴) inc/Frontend/ProjectArchive.php ──
PROJECT_ARCHIVE = """<?php
/**
 * فیلترهای GET آرشیو پروژه‌ها — اعمال امن روی pre_get_posts
 *
 * پارامترها: type (اسلاگ project_type)، location (اسلاگ city)، sort (default|popular|rating)
 * شناسه فایل: inc/Frontend/ProjectArchive.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\\Theme\\Frontend;

final class ProjectArchive {

    public static function init(): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        add_action('pre_get_posts', [self::class, 'apply'], 20);
    }

    /** دریافت امن پارامتر GET */
    private static function g(string $key): string {
        if (!isset($_GET[$key]) || !is_string($_GET[$key])) {
            return '';
        }
        return sanitize_text_field((string) wp_unslash((string) $_GET[$key]));
    }

    /** args کوئری بر اساس GET — عمومی برای تست */
    public static function query_args(): array {
        $args = [];
        $tax_query = [];
        $type = self::g('type');
        if ($type !== '' && $type !== 'all') {
            $tax_query[] = ['taxonomy' => 'project_type', 'field' => 'slug', 'terms' => $type];
        }
        $loc = self::g('location');
        if ($loc !== '' && $loc !== 'all') {
            $tax_query[] = ['taxonomy' => 'city', 'field' => 'slug', 'terms' => $loc];
        }
        if ($tax_query !== []) {
            $args['tax_query'] = $tax_query;
        }
        $sort = self::g('sort');
        if ($sort === 'popular') {
            $args['meta_key'] = 'number2-viewc';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        } elseif ($sort === 'rating') {
            $args['meta_key'] = 'es_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        }
        return $args;
    }

    /** اعمال روی کوئری اصلی آرشیو پروژه‌ها */
    public static function apply(object $query): void {
        if (!method_exists($query, 'is_main_query') || !$query->is_main_query()) {
            return;
        }
        if (!(function_exists('is_post_type_archive') && is_post_type_archive('projects'))) {
            return;
        }
        $per = (int) es_option('archives.projects_per_page', '6');
        $query->set('posts_per_page', max(1, min(100, $per)));
        foreach (self::query_args() as $es_k => $es_v) {
            $query->set($es_k, $es_v);
        }
    }
}
"""
write('inc/Frontend/ProjectArchive.php', PROJECT_ARCHIVE)

print('gen_p72 done')
