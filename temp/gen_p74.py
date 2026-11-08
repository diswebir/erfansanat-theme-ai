#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
ژنراتور ۷.۴ — home.php / archive.php / category.php + template-parts/archive/blog-shell.php
برش بایت‌دقیق میرور blog.html + category/magazine.html با assert بازسازی کامل.
اجرا: python3 temp/gen_p74.py
"""
import json, pathlib

ROOT = pathlib.Path(__file__).resolve().parents[1]
MIR_B = (ROOT / 'uploads/static-mirror/blog.html').read_text(encoding='utf-8')
MIR_C = (ROOT / 'uploads/static-mirror/category/magazine.html').read_text(encoding='utf-8')

def main_inner(html: str) -> str:
    m0 = html.index('<main class="min-h-screen bg-[#f8faf9]">') + len('<main class="min-h-screen bg-[#f8faf9]">')
    return html[m0:html.index('</main>', m0)]

B = main_inner(MIR_B)
C = main_inner(MIR_C)

# ── برش ر-ionها از blog.html ─────────────────────────────────────────────
HERO_OPEN = '<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36">'
SEC_OPEN = '<section id="articles" class="bg-white py-14 lg:py-16" data-blog-archive="true" data-items-per-page="4">'
GRID_MARK = '<div data-blog-grid="true" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2">'

hero = B[B.index(HERO_OPEN):B.index('</section>', B.index(HERO_OPEN)) + len('</section>')]
sec = B[B.index(SEC_OPEN):B.index(GRID_MARK)]
cards_region_b = B[B.index(GRID_MARK) + len(GRID_MARK):B.index('<nav aria-label="صفحه‌بندی')]
tail_b = B[B.index('<nav aria-label="صفحه‌بندی'):]

# Featured card (ستون راست هیرو)
F_OPEN = '<div class="relative mx-auto w-full max-w-[30rem]">'
f0 = hero.index(F_OPEN)
f1 = hero.index('</a></div>', f0) + len('</a></div>')
FEATURED = hero[f0:f1]

# دکمه‌های دسته هیرو (نوار JS) — فقط ردیف دکمه‌ها (کانتینر div در فرگمنت می‌ماند)
BR0 = hero.index('<div class="mt-5 flex flex-wrap gap-2">') + len('<div class="mt-5 flex flex-wrap gap-2">')
BTN_ROWS_B = hero[BR0:]
BTN_ROWS_B = BTN_ROWS_B[:BTN_ROWS_B.index('اخبار و رویداد ها</button>') + len('اخبار و رویداد ها</button>')]

# ردیف لینک‌های دسته در سکشن
LINK_ROWS_B = sec[sec.index('<a href="blog.html"'):]
LINK_ROWS_B = LINK_ROWS_B[:LINK_ROWS_B.index('<button type="button" data-blog-reset')]

# توکن‌گذاری هیرو
H = hero
H = H.replace('href="index.html"', 'href="{ES_HOME_URL}"', 1)
# عنصر سوم کرامب (وبلاگ پیل فعال در بلاگ) → توکن‌ها
pill = '<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">وبلاگ</span></span>'
assert pill in H, 'pill crumb3 بلاگ'
H = H.replace(pill, '{ES_CRUMB3_SPAN}', 1)
H = H.replace('<span class="text-[13px] font-extrabold text-emerald-800">مجله فنی و آموزشی عرفان صنعت · ۷ مقاله</span>', '<span class="text-[13px] font-extrabold text-emerald-800">{ES_BADGE}</span>', 1)
H = H.replace('>اخبار و مقالات<br/>', '>{ES_TITLE_A}<br/>', 1)
H = H.replace('>نورپردازی شهری</span></h1>', '>{ES_TITLE_B}</span></h1>', 1)
H = H.replace('مقالات تخصصی، راهنماهای خرید، اخبار و رویدادها و دانش فنی صنعت روشنایی شهری و صنعتی؛ نوشته‌شده توسط کارشناسان فنی شرکت دانش‌بنیان عرفان صنعت اصفهان', '{ES_DESC}', 1)
H = H.replace(' value=""/>', ' value="{ES_SEARCH_VALUE}"/>', 1)
H = H.replace(BTN_ROWS_B, '{ES_BTN_ROWS}', 1)
H = H.replace(FEATURED, '{ES_FEATURED}', 1)
HERO_T = H
assert '{ES_CRUMB3_SPAN}' in HERO_T and '{ES_FEATURED}' in HERO_T and '{ES_BTN_ROWS}' in HERO_T

# توکن‌گذاری featured
FT = FEATURED
FT = FT.replace('href="blog/types-of-light-tunnel.html"', 'href="{ES_F_URL}"', 1)
FT = FT.replace('src="images/light-tunnel.jpg"', 'src="{ES_F_IMG}"', 1)
FT = FT.replace('<span>مجله علمی</span>', '<span>{ES_F_CHIP}</span>', 1)
FT = FT.replace('>۹ دقیقه مطالعه</span>', '>{ES_F_MIN} دقیقه مطالعه</span>', 1)
FT = FT.replace('<h3 class="line-clamp-2 text-[19px] font-black leading-8 text-white [text-shadow:0_2px_14px_rgba(0,0,0,0.6)]">آشنایی جامع با انواع تونل نوری و راهنمای انتخاب بهترین تونل نوری شهری</h3>', '<h3 class="line-clamp-2 text-[19px] font-black leading-8 text-white [text-shadow:0_2px_14px_rgba(0,0,0,0.6)]">{ES_F_TITLE}</h3>', 1)
alt = 'alt="آشنایی جامع با انواع تونل نوری و راهنمای انتخاب بهترین تونل نوری شهری"'
assert FT.count(alt) == 1
FT = FT.replace(alt, 'alt="{ES_F_TITLE}"', 1)
FEATURED_T = FT

# توکن‌گذاری سکشن
S = sec
S = S.replace('<div custom="0">', '<div custom="0">', 1)
S = S.replace('<h2 data-blog-heading="true" class="text-2xl font-extrabold text-slate-900">آخرین مقالات و اخبار</h2>', '<h2 data-blog-heading="true" class="text-2xl font-extrabold text-slate-900">{ES_HEADING}</h2>', 1)
S = S.replace('<span data-blog-count="true">۷</span> مقاله</p>', '<span data-blog-count="true">{ES_COUNT}</span>{ES_SUM_SUFFIX}</p>', 1)
S = S.replace(LINK_ROWS_B, '{ES_LINK_ROWS}', 1)
S = S.replace('data-blog-reset="true" class="hidden rounded-xl', 'data-blog-reset="true" class="{ES_RESET_CLS} rounded-xl', 1)
SEC_T = S
assert '{ES_LINK_ROWS}' in SEC_T and '{ES_RESET_CLS}' in SEC_T and '{ES_SUM_SUFFIX}' in SEC_T

# قالب‌های دکمه/لینک (از میرور)
BTN_ON = '<button type="button" data-blog-cat-btn="{NAME}" class="rounded-xl px-4 py-2.5 text-[12.5px] font-bold transition-all cursor-pointer bg-emerald-700 text-white shadow-md shadow-emerald-700/25">{NAME}</button>'
BTN_OFF = '<button type="button" data-blog-cat-btn="{NAME}" class="rounded-xl px-4 py-2.5 text-[12.5px] font-bold transition-all cursor-pointer border border-slate-200 bg-white text-slate-600 hover:border-emerald-400 hover:text-emerald-700">{NAME}</button>'
LINK_ON = '<a href="{URL}" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors bg-emerald-700 text-white">{LABEL}</a>'
LINK_OFF = '<a href="{URL}" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300">{LABEL}</a>'
for tpl, src in ((BTN_ON, '<button type="button" data-blog-cat-btn="همه" class="rounded-xl px-4 py-2.5 text-[12.5px] font-bold transition-all cursor-pointer bg-emerald-700 text-white shadow-md shadow-emerald-700/25">همه</button>'),
                 (LINK_OFF, '<a href="category/news-and-events.html" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300">اخبار و رویداد ها</a>')):
    assert src in B, src[:60]

# شناسه توکن‌ها برای هر حالت (بازسازی پایتونی)
def crumb3_span(text: str) -> str:
    return pill.replace('>وبلاگ</span>', '>' + text + '</span>')

def crumb_extra(blog_url: str) -> str:
    return ('<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>'
            + '<a href="' + blog_url + '" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">وبلاگ</a></span>')

# بررسی: crumb_extra باید دقیقاً همان عنصر وبلاگِ link در magazine.html باشد
ci = C
i0 = ci.index('<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="../blog.html"')
i1 = ci.index('</a></span>', i0) + len('</a></span>')
CE_C = ci[i0:i1].replace('../blog.html', '{BLOG}')
assert crumb_extra('{BLOG}') == CE_C, 'crumb_extra با میرور کت یکی نیست'

HOME_URL = 'https://test.example/'
BLOG_URL = 'https://test.example/blog/'
MAG_URL = 'https://test.example/category/magazine/'
NEWS_URL = 'https://test.example/category/news-and-events/'

DESC_B = 'مقالات تخصصی، راهنماهای خرید، اخبار و رویدادها و دانش فنی صنعت روشنایی شهری و صنعتی؛ نوشته‌شده توسط کارشناسان فنی شرکت دانش‌بنیان عرفان صنعت اصفهان'
DESC_C = 'مقالات تخصصی، راهنماهای خرید، بررسی استانداردهای IP67/IP68 و دانش فنی تجهیزات نورپردازی شهری و صنعتی.'

def btn_rows(names, active, esc=lambda x: x):
    out = []
    for n in names:
        tpl = BTN_ON if n == active else BTN_OFF
        out.append(tpl.replace('{NAME}', esc(n)))
    return ''.join(out)

def link_rows(rows, esc=lambda x: x):
    # rows: [(label, url, active)]
    out = []
    for label, url, act in rows:
        tpl = LINK_ON if act else LINK_OFF
        out.append(tpl.replace('{URL}', esc(url)).replace('{LABEL}', esc(label)))
    return ''.join(out)

NAMES = ['همه', 'مجله علمی', 'اخبار و رویداد ها']

def reconstruct(mode: str) -> str:
    """بازسازی پایتونی main اینر برای هر حالت — cards_region از خود میرور برش می‌خورد."""
    if mode == 'home':
        ref = B
        ref = ref.replace('href="index.html"', 'href="' + HOME_URL + '"')  # لینک‌سازی محیطی
        vals = {
            '{ES_HOME_URL}': HOME_URL, '{ES_CRUMB3_SPAN}': crumb3_span('وبلاگ'),
            '{ES_BADGE}': 'مجله فنی و آموزشی عرفان صنعت · ۷ مقاله',
            '{ES_TITLE_A}': 'اخبار و مقالات', '{ES_TITLE_B}': 'نورپردازی شهری',
            '{ES_DESC}': DESC_B, '{ES_SEARCH_VALUE}': '',
            '{ES_BTN_ROWS}': btn_rows(NAMES, 'همه'),
            '{ES_FEATURED}': FEATURED,
            '{ES_HEADING}': 'آخرین مقالات و اخبار', '{ES_COUNT}': '۷', '{ES_SUM_SUFFIX}': ' مقاله',
            '{ES_LINK_ROWS}': link_rows([('همه مطالب', 'blog.html', True), ('مجله علمی', 'category/magazine.html', False), ('اخبار و رویداد ها', 'category/news-and-events.html', False)]),
            '{ES_RESET_CLS}': 'hidden',
        }
        cards = cards_region_b
    else:
        ref = C
        ref = ref.replace('href="../index.html"', 'href="' + HOME_URL + '"')  # لینک‌سازی محیطی
        vals = {
            '{ES_HOME_URL}': HOME_URL, '{ES_CRUMB3_SPAN}': crumb_extra('../blog.html') + crumb3_span('مجله علمی'),
            '{ES_BADGE}': 'آرشیو دسته‌بندی: مجله علمی · ۷ مقاله',
            '{ES_TITLE_A}': 'مجله علمی', '{ES_TITLE_B}': 'نورپردازی شهری',
            '{ES_DESC}': DESC_C, '{ES_SEARCH_VALUE}': '',
            '{ES_BTN_ROWS}': btn_rows(NAMES, 'مجله علمی'),
            '{ES_FEATURED}': FEATURED.replace('href="blog/', 'href="../blog/').replace('src="images/', 'src="../images/'),
            '{ES_HEADING}': 'آرشیو مطالب «مجله علمی»', '{ES_COUNT}': '۵', '{ES_SUM_SUFFIX}': ' مقاله در دسته «مجله علمی»',
            '{ES_LINK_ROWS}': link_rows([('همه مطالب', '../blog.html', False), ('مجله علمی', '../category/magazine.html', True), ('اخبار و رویداد ها', '../category/news-and-events.html', False)]),
            '{ES_RESET_CLS}': 'inline-flex',
        }
        cards = C[C.index(GRID_MARK) + len(GRID_MARK):C.index('<nav aria-label="صفحه‌بندی')]
    out = HERO_T + SEC_T + GRID_MARK + cards + tail_b
    # featured در هر دو هیرو یکسان است (توکن‌هایش در ref میرور عیناً موجود است)
    for k, v in vals.items():
        out = out.replace(k, v)
    assert out == ref, 'بازسازی ' + mode + ' مطابق میرور نیست'
    return out

reconstruct('home')
reconstruct('category')

# ── تولید PHP ────────────────────────────────────────────────────────────
def heredoc(name: str, body: str) -> str:
    mark = 'ESP74' + name
    return '$' + name + " = <<<'" + mark + "'\n" + body + '\n' + mark + ';\n'

frag = {
    'ES74_HERO': HERO_T, 'ES74_FEATURED': FEATURED_T, 'ES74_SEC': SEC_T, 'ES74_GRID': GRID_MARK,
    'ES74_BTN_ON': BTN_ON, 'ES74_BTN_OFF': BTN_OFF, 'ES74_LINK_ON': LINK_ON, 'ES74_LINK_OFF': LINK_OFF,
    'ES74_MAIN_OPEN': '<main class="min-h-screen bg-[#f8faf9]">',
    'ES74_CRUMB_LINK': crumb_extra('{ES_BLOG_URL}'),
    'ES74_EMPTY': '<div data-blog-empty="true" class="{ES_EMPTY_CLS} rounded-2xl border border-dashed border-slate-300 bg-slate-50/60 py-16 text-center"><p class="text-[14px] font-bold text-slate-500">مقاله‌ای با این جستجو یافت نشد.</p></div>',
    'ES74_CLOSE': '</div></section></main>',
}

php = ['<?php', '/**', ' * شل آرشیو نوشته‌ها (وبلاگ) — برش بایت‌دقیق میرور blog.html + category/magazine.html', ' * فرگمنت‌ها با temp/gen_p74.py تولید شده‌اند (assert بازسازی دو حالت home/category).', ' * mode=home → hero برگه وبلاگ؛ mode=category → hero ترم. هوم فقط برای صفحه نوشته‌ها (page_for_posts).', ' *', ' * @package ErfanSanat', ' */', 'declare(strict_types=1);', '', '/** @var array {mode: string, term?: object|null} $args */', '']
for k, v in frag.items():
    php.append(heredoc(k, v))

php.append(r'''
// ═══ داده‌ها ═══
$es_mode = (string) ($args['mode'] ?? 'home');
$es_term = $args['term'] ?? null;
$es_is_cat = $es_mode === 'category' && is_object($es_term) && isset($es_term->term_id);
$es_name = $es_is_cat ? (string) $es_term->name : '';
$es_tid = $es_is_cat ? (int) $es_term->term_id : 0;
$es_tmeta = static fn (string $k, string $def): string => (string) (get_term_meta($es_tid, $k, true) ?: $def);

$es_page_id = (int) get_option('page_for_posts'); // home فقط برای صفحه نوشته‌ها
$es_pmeta = static fn (string $k, string $def): string => (string) (get_post_meta($es_page_id, $k, true) ?: $def);

$es_total_all = (int) (wp_count_posts('post')->publish ?? 0);
$es_per = max(1, (int) es_option('archives.posts_per_page', '4'));
$es_posts = (array) ($GLOBALS['wp_query']->posts ?? []);
$es_total = (int) ($GLOBALS['__es_archive_total'] ?? count($es_posts));
$es_paged = max(1, (int) get_query_var('paged'));
$es_pages = max(1, (int) ceil($es_total / $es_per));
$es_sticky = array_map('intval', (array) get_option('sticky_posts', []));
$es_terms = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);
if (!is_array($es_terms)) {
    $es_terms = [];
}
$es_blog_url = es_page_url('blog');
$es_base_url = $es_is_cat ? (string) get_term_link($es_term) : $es_blog_url;
$es_is_empty = $es_total < 1;

// متغیرها برای فاز ۸ (هوک‌های توسعه)
$es_blog_vars = (array) apply_filters('es_blog_archive_vars', [
    'mode' => $es_mode, 'term' => $es_is_cat ? $es_term : null, 'total' => $es_total, 'per_page' => $es_per,
]);

// کارت ویژه هیرو: نخستین نوشته sticky، وگرنه نخستین مقاله
$es_fpost = null;
foreach ($es_posts as $es_p) {
    if (in_array((int) ($es_p->ID ?? 0), $es_sticky, true)) {
        $es_fpost = $es_p;
        break;
    }
}
if ($es_fpost === null) {
    $es_fpost = $es_posts[0] ?? null;
}
$es_featured_html = '';
if (is_object($es_fpost)) {
    $es_fid = (int) $es_fpost->ID;
    $es_ftitle = (string) ($es_fpost->post_title ?? '');
    $es_fterms = get_the_terms($es_fid, 'category');
    $es_fchip = (is_array($es_fterms) && isset($es_fterms[0]->name)) ? (string) $es_fterms[0]->name : '';
    $es_fimg_id = (int) get_post_meta($es_fid, '_thumbnail_id', true);
    $es_fimg = es_media_url($es_fimg_id > 0 ? $es_fimg_id : 'assets/images/light-tunnel.jpg');
    $es_fmin = (int) get_post_meta($es_fid, 'es_read_time', true);
    if ($es_fmin < 1) {
        $es_fwords = count(preg_split('/\s+/u', trim(strip_tags((string) ($es_fpost->post_content ?? '')))) ?: []);
        $es_fmin = max(1, (int) ceil($es_fwords / 225));
    }
    $es_featured_html = strtr($ES74_FEATURED, [
        '{ES_F_URL}'   => esc_url((string) get_permalink($es_fid)),
        '{ES_F_IMG}'   => esc_url($es_fimg),
        '{ES_F_TITLE}' => esc_attr($es_ftitle),
        '{ES_F_CHIP}'  => esc_html($es_fchip),
        '{ES_F_MIN}'   => es_fa_num((string) $es_fmin),
    ]);
}

// دکمه‌های دسته هیرو (JS) + لینک‌های دسته سکشن
$es_btn_rows = '';
$es_link_rows = '';
$es_btn_on = $es_is_cat ? $es_name : 'همه';
$es_btn_rows .= strtr($es_btn_on === 'همه' ? $ES74_BTN_ON : $ES74_BTN_OFF, ['{NAME}' => esc_html('همه')]);
if (!$es_is_cat) {
    $es_link_rows .= strtr($ES74_LINK_ON, ['{URL}' => esc_url($es_blog_url), '{LABEL}' => esc_html('همه مطالب')]);
} else {
    $es_link_rows .= strtr($ES74_LINK_OFF, ['{URL}' => esc_url($es_blog_url), '{LABEL}' => esc_html('همه مطالب')]);
}
foreach ($es_terms as $es_t) {
    $es_tname = (string) $es_t->name;
    $es_active = $es_is_cat && $es_tname === $es_name;
    $es_btn_rows .= strtr($es_active ? $ES74_BTN_ON : $ES74_BTN_OFF, ['{NAME}' => esc_html($es_tname)]);
    $es_tlink = (string) get_term_link($es_t);
    $es_link_rows .= strtr($es_active ? $ES74_LINK_ON : $ES74_LINK_OFF, ['{URL}' => esc_url($es_tlink), '{LABEL}' => esc_html($es_tname)]);
}

// متن‌های هیرو و سکشن — متا با fallback میرور
if ($es_is_cat) {
    $es_crumb_extra = strtr($ES74_CRUMB_LINK, ['{ES_BLOG_URL}' => esc_url($es_blog_url)]);
    $es_crumb3 = esc_html($es_tmeta('es_blog_crumb', $es_name));
    $es_badge = esc_html($es_tmeta('es_blog_badge', 'آرشیو دسته‌بندی: ' . $es_name . ' · ' . es_fa_num((string) $es_total_all) . ' مقاله'));
    $es_title_a = esc_html($es_tmeta('es_blog_title_a', $es_name));
    $es_title_b = esc_html($es_tmeta('es_blog_title_b', 'نورپردازی شهری'));
    $es_desc_def = (string) ($es_term->description ?? '');
    $es_desc = esc_html(trim($es_desc_def) !== '' ? $es_desc_def : $es_tmeta('es_blog_desc', 'مقالات تخصصی، راهنماهای خرید، اخبار و رویدادها و دانش فنی صنعت روشنایی شهری و صنعتی؛ نوشته‌شده توسط کارشناسان فنی شرکت دانش‌بنیان عرفان صنعت اصفهان'));
    $es_heading = esc_html($es_tmeta('es_blog_heading', 'آرشیو مطالب «' . $es_name . '»'));
    $es_sum_suffix = ' مقاله در دسته «' . esc_html($es_name) . '»';
    $es_reset_cls = 'inline-flex';
    $es_search_value = (string) get_query_var('s');
} else {
    $es_crumb_extra = '';
    $es_crumb3 = esc_html($es_pmeta('es_blog_crumb', 'وبلاگ'));
    $es_badge = esc_html($es_pmeta('es_blog_badge', 'مجله فنی و آموزشی عرفان صنعت · ' . es_fa_num((string) $es_total_all) . ' مقاله'));
    $es_title_a = esc_html($es_pmeta('es_blog_title_a', 'اخبار و مقالات'));
    $es_title_b = esc_html($es_pmeta('es_blog_title_b', 'نورپردازی شهری'));
    $es_desc = esc_html($es_pmeta('es_blog_desc', 'مقالات تخصصی، راهنماهای خرید، اخبار و رویدادها و دانش فنی صنعت روشنایی شهری و صنعتی؛ نوشته‌شده توسط کارشناسان فنی شرکت دانش‌بنیان عرفان صنعت اصفهان'));
    $es_heading = esc_html($es_pmeta('es_blog_heading', 'آخرین مقالات و اخبار'));
    $es_sum_suffix = ' مقاله';
    $es_reset_cls = 'hidden';
    $es_search_value = isset($_GET['s']) && is_string($_GET['s']) ? sanitize_text_field((string) wp_unslash((string) $_GET['s'])) : '';
}

// ═══ رندر ═══
echo $ES74_MAIN_OPEN;
echo strtr($ES74_HERO, [
    '{ES_HOME_URL}'      => esc_url(home_url('/')),
    '{ES_CRUMB3_SPAN}'   => $es_crumb_extra . '<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">' . $es_crumb3 . '</span></span>',
    '{ES_BADGE}'         => $es_badge,
    '{ES_TITLE_A}'       => $es_title_a,
    '{ES_TITLE_B}'       => $es_title_b,
    '{ES_DESC}'          => $es_desc,
    '{ES_SEARCH_VALUE}'  => esc_attr($es_search_value),
    '{ES_BTN_ROWS}'      => $es_btn_rows,
    '{ES_FEATURED}'      => $es_featured_html,
]);
echo strtr($ES74_SEC, [
    '{ES_HEADING}'     => $es_heading,
    '{ES_COUNT}'       => esc_html(es_fa_num((string) $es_total)),
    '{ES_SUM_SUFFIX}'  => $es_sum_suffix,
    '{ES_LINK_ROWS}'   => $es_link_rows,
    '{ES_RESET_CLS}'   => $es_reset_cls,
]);
echo $ES74_GRID;
if (!$es_is_empty) {
    $es_i = 0;
    foreach ($es_posts as $es_post) {
        ob_start();
        es_get_template('template-parts/cards/card-post.php', [
            'post'        => $es_post,
            'nested'      => true,
            'visible'     => $es_i < $es_per,
            'featured'    => in_array((int) ($es_post->ID ?? 0), $es_sticky, true),
            'search_chip' => false,
            'custom'      => ['0.05', '0.12000000000000001', '0.19'][$es_i % 3],
        ]);
        echo (string) ob_get_clean();
        $es_i++;
    }
}
echo '</div>';
ob_start();
es_get_template('template-parts/archive/pagination.php', [
    'current'     => $es_paged,
    'total_pages' => $es_pages,
    'base_url'    => $es_base_url,
    'root'        => 'blog',
    'label'       => 'صفحه‌بندی مقالات',
    'nav_cls'     => $es_pages > 1 ? 'flex' : 'hidden',
    'cur_label'   => 'صفحه',
    'count_seg'   => '',
    'box_cls'     => 'bg-slate-50/70 px-5 py-4',
    'navbtn_cls'  => 'bg-white px-3 py-2',
    'page_btn'    => true,
    'next_href'   => false,
]);
echo (string) ob_get_clean();
echo strtr($ES74_EMPTY, ['{ES_EMPTY_CLS}' => $es_is_empty ? 'block' : 'hidden']);
echo $ES74_CLOSE;
''')

shell = '\n'.join(php) + '\n'
(ROOT / 'template-parts/archive/blog-shell.php').write_text(shell, encoding='utf-8')

thin = """<?php
/**
 * {TITLE}
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
es_get_template('template-parts/archive/blog-shell.php', {ARGS});
get_footer();
"""
(ROOT / 'home.php').write_text(thin.format(TITLE='صفحه نوشته‌ها (وبلاگ) — مطابق میرور blog.html؛ فقط برای page_for_posts', ARGS="['mode' => 'home']"), encoding='utf-8')
(ROOT / 'archive.php').write_text(thin.format(TITLE='آرشیو عمومی نوشته‌ها (تاریخ/نویسنده) — همان شل بلاگ', ARGS="['mode' => 'home']"), encoding='utf-8')
(ROOT / 'category.php').write_text(thin.format(TITLE='آرشیو دستهٔ نوشته‌ها — مطابق میرور category/*.html', ARGS="['mode' => 'category', 'term' => get_queried_object()]"), encoding='utf-8')

(ROOT / 'temp/p74_fragments.json').write_text(json.dumps(frag, ensure_ascii=False, indent=1), encoding='utf-8')
print('OK — blog-shell.php + home/archive/category تولید شد؛ بازسازی home/category میرور assert شد.')
