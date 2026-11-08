#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
ژنراتور ۷.۶ — search.php / 404.php / searchform.php + template-parts/search/search-shell.php
صفحه جستجو/۴۰۴ در میرور مرجع اختصاصی ندارد — از کامپوننت‌های بایت‌دقیق میرور ساخته می‌شود:
backdrop و کرامب و اینپوت جستجو و حالت خالی از blog.html؛ گرید از archive-product (۶.۷)؛
تب‌ها/لینک‌های پیشنهادی با قرص‌های لینک blog.html (۷.۴)؛ ناو = صفحه‌بندی مشترک (۶.۷).
اجرا: python3 temp/gen_p76.py
"""
import json, pathlib, re

ROOT = pathlib.Path(__file__).resolve().parents[1]
BLOG = (ROOT / 'uploads/static-mirror/blog.html').read_text(encoding='utf-8')
M0 = BLOG.index('<main class="min-h-screen bg-[#f8faf9]">') + len('<main class="min-h-screen bg-[#f8faf9]">')
B = BLOG[M0:BLOG.index('</main>', M0)]

# ── استخراج فرگمنت‌های بایت‌دقیق از میرور ────────────────────────────────
# ۱) backdrop هیرو بلاگ
i0 = B.index('<div aria-hidden="true" class="pointer-events-none absolute inset-0">')
BACKDROP = B[i0:B.index('</div></div>', i0) + len('</div></div>')]
assert 'radial-gradient(1000px 520px' in BACKDROP and 'light-dot-grid' in BACKDROP

# ۲) کلاس‌های اینپوت جستجو (بایت‌دقیق)
INP = re.search(r'<input type="text" data-blog-search="true"[^>]*>', B).group(0)
INPUT_CLS = re.search(r'class="([^"]+)"', INP).group(1)
INPUT_CLS = INPUT_CLS.replace('{ES_SEARCH_VALUE}', '').strip() if '{' in INPUT_CLS else INPUT_CLS
assert 'pr-13' in INPUT_CLS and 'rounded-2xl' in INPUT_CLS

# آیکن جستجوی بلاگ (قبل از اینپوت)
si0 = B.rindex('<svg xmlns="http://www.w3.org/2000/svg"', 0, B.index('<input type="text" data-blog-search'))
SEARCH_ICON = B[si0:B.index('</svg>', si0) + len('</svg>')]
assert 'lucide-search' in SEARCH_ICON

# ۳) حالت خالی بلاگ (کلاس‌ها بایت‌دقیق)
EMPTY = re.search(r'<div data-blog-empty="true"[^>]*>.*?</div></div>', B, re.S)
EMPTY_BLOCK = re.search(r'<div data-blog-empty="true" class="hidden rounded-2xl border border-dashed border-slate-300 bg-slate-50/60 py-16 text-center"><p class="text-\[14px\] font-bold text-slate-500">[^<]*</p></div>', B).group(0)
EMPTY_CLS = 'rounded-2xl border border-dashed border-slate-300 bg-slate-50/60 py-16 text-center'
EMPTY_P_CLS = 'text-[14px] font-bold text-slate-500'

# ۴) گرید محصولات (۶.۷)
ARCH = (ROOT / 'archive-product.php').read_text(encoding='utf-8')
GRID_CLS = re.search(r'<div data-product-grid="true" class="(grid gap-6 sm:grid-cols-2 xl:grid-cols-3)">', ARCH).group(1)
assert GRID_CLS == 'grid gap-6 sm:grid-cols-2 xl:grid-cols-3'

# ۵) قرص‌های لینک بلاگ (۷.۴) — از ردیف لینک‌های سکشن
LINK_ON = '<a href="{URL}" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors bg-emerald-700 text-white">{LABEL}</a>'
LINK_OFF = '<a href="{URL}" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300">{LABEL}</a>'
row = re.search(r'<a href="blog\.html" class="rounded-xl px-3\.5 py-2 text-\[12px\] font-bold transition-colors bg-emerald-700 text-white">همه مطالب</a>', B)
assert row
row2 = re.search(r'<a href="category/magazine\.html" class="rounded-xl px-3\.5 py-2 text-\[12px\] font-bold transition-colors border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300">مجله علمی</a>', B)
assert row2

# ۶) کرامب (۷.۵)
CRUMB_PILL = '<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">{X}</span></span>'
CRUMB_LINK = '<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="{URL}" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">{X}</a></span>'
pill_ref = re.search(r'<span class="flex min-w-0 items-center gap-1\.5"><svg xmlns="http://www\.w3\.org/2000/svg"[^>]*><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-\[16rem\] truncate rounded-lg px-2 py-0\.5 font-extrabold bg-emerald-50 text-emerald-800">وبلاگ</span></span>', B)
assert pill_ref and pill_ref.group(0) == CRUMB_PILL.replace('{X}', 'وبلاگ')
MAG = (ROOT / 'uploads/static-mirror/category/magazine.html').read_text(encoding='utf-8')
link_ref = re.search(r'<span class="flex min-w-0 items-center gap-1\.5"><svg xmlns="http://www\.w3\.org/2000/svg"[^>]*><path d="m15 18-6-6 6-6"></path></svg><a href="[^"]*blog\.html" class="truncate rounded-lg px-1\.5 py-0\.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">وبلاگ</a></span>', MAG)
assert link_ref and link_ref.group(0) == CRUMB_LINK.replace('{URL}', '../blog.html').replace('{X}', 'وبلاگ')

# ۷) H1 سبک سینگل (۷.۵) — الگوی مرکزچین
H1_CLS = 'mx-auto mt-6 max-w-4xl text-center text-[1.9rem] font-black leading-[1.5] tracking-[-0.012em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.4]'
LED = (ROOT / 'uploads/static-mirror/blog/led-wall-washer-guide.html').read_text(encoding='utf-8')
h1_ref = re.search(r'<h1 class="mx-auto mt-6 max-w-4xl text-center text-\[1\.9rem\] font-black leading-\[1\.5\] tracking-\[-0\.012em\] text-slate-950 sm:text-\[2\.5rem\] lg:text-\[2\.9rem\] lg:leading-\[1\.4\]">[^<]*</h1>', LED)
assert h1_ref

# ۸) بج هیرو (پیل سبز) — سبک بلاگ ۷.۴
BADGE_PILL = '<div class="mb-6 inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white py-1.5 pl-5 pr-1.5 shadow-sm"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-700 text-white shadow-[0_0_20px_rgba(0,125,45,0.35)]"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{ICON_CLS}" aria-hidden="true">{ICON_PATHS}</svg></span><span class="text-[13px] font-extrabold text-emerald-800">{BADGE}</span></div>'
SEARCH_ICON_CLS = 'lucide lucide-search h-4 w-4'

# ── قالب‌های صفحات ───────────────────────────────────────────────────────
SEARCH_HERO = ('<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36">' + BACKDROP
    + '<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">'
    + '<nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]">'
    + '<a href="{ES_HOME_URL}" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a>'
    + '{ES_CRUMB}</nav></div>'
    + '<div class="relative z-10 mx-auto max-w-3xl px-5 pb-16 pt-10 text-center lg:pb-20 lg:pt-12">'
    + BADGE_PILL.replace('{ICON_CLS}', SEARCH_ICON_CLS).replace('{ICON_PATHS}', '<path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle>').replace('{BADGE}', '{ES_BADGE}')
    + '<h1 class="' + H1_CLS + '">{ES_TITLE}</h1>'
    + '<p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600">{ES_DESC}</p>'
    + '<div class="mt-8">{ES_FORM}</div>'
    + '<div class="mt-6 flex flex-wrap items-center justify-center gap-2">{ES_TABS}</div></div>'
    + '<div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div></section>')

SEC_HEAD = ('<section id="results" class="bg-white py-14 lg:py-16" data-search-archive="true"><div class="mx-auto max-w-7xl scroll-mt-24 px-5 lg:px-8">'
    + '<div class="mb-8 flex flex-wrap items-end justify-between gap-4"><div>'
    + '<h2 data-search-heading="true" class="text-2xl font-extrabold text-slate-900">{ES_HEADING}</h2>'
    + '<p data-search-summary="true" class="mt-2 text-[13.5px] font-medium text-slate-500"><span data-search-count="true">{ES_COUNT}</span> {ES_UNIT}{ES_SUM_SUFFIX}</p></div>'
    + '<div class="flex flex-wrap items-center gap-2">{ES_TABS_LINKS}</div></div>'
    + '<div data-search-grid="true" class="' + GRID_CLS + '">')

NOTFOUND404 = ('<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36">' + BACKDROP
    + '<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">'
    + '<nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]">'
    + '<a href="{ES_HOME_URL}" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a>'
    + CRUMB_PILL.replace('{X}', '۴۰۴') + '</nav></div>'
    + '<div class="relative z-10 mx-auto max-w-3xl px-5 pb-16 pt-10 text-center lg:pb-20 lg:pt-12">'
    + '<div class="text-[5.5rem] font-black leading-none tracking-tight text-emerald-800/15 sm:text-[7.5rem]">۴۰۴</div>'
    + '<h1 class="' + H1_CLS + ' mt-2">این صفحه در دسترس نیست</h1>'
    + '<p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600">آدرس واردشده وجود ندارد یا جابه‌جا شده است. از جستجو استفاده کنید یا از دسترسی سریع زیر کمک بگیرید.</p>'
    + '<div class="mt-8">{ES_FORM}</div>'
    + '<div class="mt-6 flex flex-wrap items-center justify-center gap-2">{ES_QUICK}</div></div>'
    + '<div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div></section>')

SEARCHFORM = ('<form role="search" method="get" class="es-searchform" action="{ES_ACTION}">'
    + '<div class="relative mx-auto max-w-[30rem]">' + SEARCH_ICON
    + '<input type="text" name="s" placeholder="جستجو در سایت…" class="' + INPUT_CLS + '" value="{ES_S}"/></div></form>')

assert '{ES_BADGE}' in SEARCH_HERO and '{ES_FORM}' in SEARCH_HERO and '{ES_TABS}' in SEARCH_HERO
assert '{ES_S}' in SEARCHFORM and INPUT_CLS in SEARCHFORM and 'lucide-search' in SEARCHFORM
assert '۴۰۴' in NOTFOUND404 and '{ES_QUICK}' in NOTFOUND404

# ── تولید PHP ────────────────────────────────────────────────────────────
def heredoc(name: str, body: str) -> str:
    mark = 'ESP76' + name
    return '$' + name + " = <<<'" + mark + "'\n" + body + '\n' + mark + ';\n'

frag = {
    'ES76_SEARCH_HERO': SEARCH_HERO,
    'ES76_SEC_HEAD': SEC_HEAD,
    'ES76_LINK_ON': LINK_ON, 'ES76_LINK_OFF': LINK_OFF,
    'ES76_404': NOTFOUND404,
    'ES76_GRID_CLS': GRID_CLS,
    'ES76_EMPTY_CLS': EMPTY_CLS, 'ES76_EMPTY_P_CLS': EMPTY_P_CLS,
    'ES76_CRUMB_PILL': CRUMB_PILL,
}

php = ['<?php', '/**', ' * شل نتایج جستجو — ساخت از کامپوننت‌های بایت‌دقیق میرور (blog.html + گرید ۶.۷ + ناو مشترک)', ' * فرگمنت‌ها با temp/gen_p76.py استخراج/assert شده‌اند. تب‌ها با GET type (any/product/projects/post).', ' *', ' * @package ErfanSanat', ' */', 'declare(strict_types=1);', '']
for k, v in frag.items():
    php.append(heredoc(k, v))

php.append(r'''
// ═══ داده‌ها ═══
$es_s = (string) get_query_var('s');
$es_type = isset($_GET['type']) && is_string($_GET['type']) ? sanitize_key((string) wp_unslash((string) $_GET['type'])) : '';
$es_types_map = ['product' => 'محصولات', 'projects' => 'پروژه‌ها', 'post' => 'مقالات'];
$es_type = isset($es_types_map[$es_type]) ? $es_type : '';
$es_posts = (array) ($GLOBALS['wp_query']->posts ?? []);
$es_total = (int) ($GLOBALS['__es_archive_total'] ?? count($es_posts));
$es_paged = max(1, (int) get_query_var('paged'));
$es_per = (int) apply_filters('es_search_per_page', 9);
$es_per = max(1, $es_per);
$es_pages = max(1, (int) ceil($es_total / $es_per));
$es_home = home_url('/');
$es_base = $es_home . '?s=' . rawurlencode($es_s) . ($es_type !== '' ? '&type=' . $es_type : '');

/** متغیرها برای فاز ۸ (هوک‌های توسعه) */
$es_search_vars = (array) apply_filters('es_search_vars', [
    's' => $es_s, 'type' => $es_type, 'total' => $es_total, 'per_page' => $es_per,
]);

// قرص تب‌های هیرو و لینک‌های سکشن
$es_tab_url = static function (string $t) use ($es_s): string {
    return home_url('/') . '?s=' . rawurlencode($es_s) . ($t === '' ? '' : '&type=' . $t);
};
$es_tabs = '';
$es_tabs_links = '';
foreach (['' => 'همه'] + $es_types_map as $es_t => $es_label) {
    $es_active = ($es_t === $es_type) || ($es_t === '' && $es_type === '');
    $es_url = $es_tab_url($es_t);
    $es_tabs .= strtr($es_active ? $ES76_LINK_ON : $ES76_LINK_OFF, ['{URL}' => esc_url($es_url), '{LABEL}' => esc_html($es_label)]);
    $es_tabs_links .= strtr($es_active ? $ES76_LINK_ON : $ES76_LINK_OFF, ['{URL}' => esc_url($es_url), '{LABEL}' => esc_html($es_label)]);
}

// ═══ رندر ═══
echo strtr($ES76_SEARCH_HERO, [
    '{ES_HOME_URL}' => esc_url($es_home),
    '{ES_CRUMB}'    => strtr($ES76_CRUMB_PILL, ['{X}' => esc_html('جستجو')]),
    '{ES_BADGE}'    => esc_html($es_s === '' ? 'جستجو در سایت عرفان صنعت' : 'نتیجه‌های جستجو'),
    '{ES_TITLE}'    => esc_html($es_s === '' ? 'جستجو' : 'نتایج جستجو برای: ' . $es_s),
    '{ES_DESC}'     => esc_html($es_s === '' ? 'عبارت موردنظر خود را وارد کنید تا محصولات، پروژه‌ها و مقالات مرتبط را ببینید.' : 'در محصولات، پروژه‌های اجراشده و مقالات مجله فنی عرفان صنعت.'),
    '{ES_FORM}'     => get_search_form(),
    '{ES_TABS}'     => $es_s === '' ? '' : $es_tabs,
]);
echo strtr($ES76_SEC_HEAD, [
    '{ES_HEADING}'    => esc_html($es_type === '' ? 'همه نتایج' : 'نتایج ' . $es_types_map[$es_type]),
    '{ES_COUNT}'      => esc_html(es_fa_num((string) $es_total)),
    '{ES_UNIT}'       => esc_html('نتیجه'),
    '{ES_SUM_SUFFIX}' => $es_s !== '' ? ' برای «' . esc_html($es_s) . '»' : '',
    '{ES_TABS_LINKS}' => $es_s === '' ? '' : $es_tabs_links,
]);
if ($es_posts !== []) {
    $es_customs_product = ['0', '0.06', '0.12'];
    $es_customs_project = ['0.04', '0.1'];
    $es_customs_post = ['0.05', '0.12000000000000001', '0.19'];
    $es_i = 0;
    foreach ($es_posts as $es_post) {
        ob_start();
        switch ((string) ($es_post->post_type ?? 'post')) {
            case 'product':
                es_get_template('template-parts/cards/card-product.php', ['post' => $es_post, 'custom' => $es_customs_product[$es_i % 3]]);
                break;
            case 'projects':
                es_get_template('template-parts/cards/card-project.php', ['post' => $es_post, 'custom' => $es_customs_project[$es_i % 2]]);
                break;
            default:
                es_get_template('template-parts/cards/card-post.php', ['post' => $es_post, 'custom' => $es_customs_post[$es_i % 3]]);
        }
        echo (string) ob_get_clean();
        $es_i++;
    }
}
echo '</div>';
$es_empty_cls = $es_posts === [] ? '' : 'hidden ';
echo '<div data-search-empty="true" class="' . $es_empty_cls . $ES76_EMPTY_CLS . '"><p class="' . $ES76_EMPTY_P_CLS . '">';
echo esc_html($es_posts === [] && $es_s === '' ? 'عبارتی برای جستجو وارد نشده است.' : 'نتیجه‌ای برای این جستجو یافت نشد.');
echo '</p></div>';
ob_start();
es_get_template('template-parts/archive/pagination.php', [
    'current'        => $es_paged,
    'total_pages'    => $es_pages,
    'total_products' => $es_total,
    'base_url'       => $es_base,
    'root'           => 'search',
    'count_attr'     => 'data-search-count="true"',
    'unit'           => 'نتیجه',
    'label'          => 'صفحه‌بندی نتایج',
    'always_show'    => false,
    'nav_cls'        => $es_pages > 1 ? 'flex' : 'hidden',
]);
echo (string) ob_get_clean();
echo '</div></section>';
''')

shell = '\n'.join(php) + '\n'
(ROOT / 'template-parts/search').mkdir(parents=True, exist_ok=True)
(ROOT / 'template-parts/search/search-shell.php').write_text(shell, encoding='utf-8')

search = """<?php
/**
 * نتایج جستجو — کامپوننت‌های بایت‌دقیق میرور + WP_Query استاندارد
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
echo '<main class="min-h-screen bg-[#f8faf9]">';
es_get_template('template-parts/search/search-shell.php');
echo '</main>';
get_footer();
"""
(ROOT / 'search.php').write_text(search, encoding='utf-8')

(ROOT / 'searchform.php').write_text(
    "<?php\n/**\n * فرم جستجوی عمومی — کلاس‌های اینپوت بایت‌دقیق میرور blog.html\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\n$ES76_SF = <<<'ESP76SF'\n" + SEARCHFORM + "\nESP76SF;\n\necho strtr($ES76_SF, [\n    '{ES_ACTION}' => esc_url(home_url('/')),\n    '{ES_S}'      => esc_attr((string) get_query_var('s')),\n]);\n",
    encoding='utf-8')

(ROOT / '404.php').write_text(
    "<?php\n/**\n * صفحه ۴۰۴ — ساخت از کامپوننت‌های بایت‌دقیق میرور (backdrop/کرامب/فرم/قرص‌های لینک)\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\nget_header();\necho '<main class=\"min-h-screen bg-[#f8faf9]\">';\n$ES76_F = <<<'ESP76F'\n" + NOTFOUND404 + "\nESP76F;\n$ES76_LON = <<<'ESP76L'\n" + LINK_ON + "\nESP76L;\n$ES76_LOFF = <<<'ESP76L'\n" + LINK_OFF + "\nESP76L;\n\n$es_quick = strtr($ES76_LON, ['{URL}' => esc_url(home_url('/')), '{LABEL}' => esc_html('صفحه اصلی')])\n    . strtr($ES76_LOFF, ['{URL}' => esc_url(es_page_url('shop')), '{LABEL}' => esc_html('فروشگاه محصولات')])\n    . strtr($ES76_LOFF, ['{URL}' => esc_url(home_url('/projects/')), '{LABEL}' => esc_html('پروژه‌ها')])\n    . strtr($ES76_LOFF, ['{URL}' => esc_url(es_page_url('blog')), '{LABEL}' => esc_html('مقالات')]);\n\necho strtr($ES76_F, [\n    '{ES_HOME_URL}' => esc_url(home_url('/')),\n    '{ES_FORM}'     => get_search_form(),\n    '{ES_QUICK}'    => $es_quick,\n]);\necho '</main>';\nget_footer();\n",
    encoding='utf-8')

(ROOT / 'temp/p76_fragments.json').write_text(json.dumps({k: (v[:100] + '…' if len(v) > 100 else v) for k, v in frag.items()}, ensure_ascii=False, indent=1), encoding='utf-8')
print('OK — search-shell + search.php + searchform.php + 404.php (پیش‌نویس)')
