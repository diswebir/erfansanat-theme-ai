#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۶.۷ — آرشیو فروشگاه (shop) — هیرو/فیلترها/گرید/صفحه‌بندی (برش بایت‌دقیق shop.html).
اجرا: python3 temp/gen_p67.py
"""
import os

ROOT = '/home/user/erfansanat-theme-ai'
SRC = os.path.join(ROOT, 'uploads/static-mirror/shop.html')

def nowdoc(name, body, tag):
    return "$" + name + " = <<<'{t}'\n{b}\n{t};\n".format(t=tag, b=body)

def tok(R, old, new):
    assert R.count(old) >= 1, old[:70]
    return R.replace(old, new, 1)

s = open(SRC, encoding='utf-8').read()

# ═══════════ HERO (129900→139453) ═══════════
h0 = s.find('<section class="relative overflow-hidden bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-36">')
assert h0 == 129900
HERO_RAW = s[h0:139453]
HERO = tok(HERO_RAW, '<h1 class="text-[2.3rem] font-black leading-[1.28] tracking-[-0.015em] text-slate-950 sm:text-[3rem] lg:text-[3.4rem] lg:leading-[1.22]">فروشگاه محصولات <br/><span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent">نورپردازی شهری و صنعتی</span></h1>',
           '<h1 class="text-[2.3rem] font-black leading-[1.28] tracking-[-0.015em] text-slate-950 sm:text-[3rem] lg:text-[3.4rem] lg:leading-[1.22]">{ES_H1_A}<br/><span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent">{ES_H1_B}</span></h1>')
HERO = tok(HERO, '<p class="mt-6 max-w-[36rem] text-[15.5px] font-medium leading-9 text-slate-600 lg:text-[16px]">مجموعه‌ای کامل از تجهیزات نورپردازی پیشرفته؛ از پوینت لایت پیکسل، ریسه‌های بلوطی، فندقی و سوزنی IP68 تا درختان نوری هوشمند، خورشیدنماها و باکس کنترل مرکزی</p>',
           '<p class="mt-6 max-w-[36rem] text-[15.5px] font-medium leading-9 text-slate-600 lg:text-[16px]">{ES_HERO_DESC}</p>')
reb = HERO.replace('{ES_H1_A}', 'فروشگاه محصولات ').replace('{ES_H1_B}', 'نورپردازی شهری و صنعتی').replace('{ES_HERO_DESC}', 'مجموعه‌ای کامل از تجهیزات نورپردازی پیشرفته؛ از پوینت لایت پیکسل، ریسه‌های بلوطی، فندقی و سوزنی IP68 تا درختان نوری هوشمند، خورشیدنماها و باکس کنترل مرکزی')
assert reb == HERO_RAW

# ═══════════ ASIDE فیلترها (139453→154673) ═══════════
A_RAW = s[139453:154673]
A = A_RAW
A = tok(A, '<span data-product-count="true">۲۰</span> محصول منطبق</p>',
        '<span data-product-count="true">{ES_COUNT}</span> محصول منطبق</p>')
A = tok(A, 'value=""', 'value="{ES_SEARCH_VALUE}"')
A = tok(A, 'data-product-purchase-mode="all" class="rounded-lg py-2 text-[11.5px] font-extrabold transition-all cursor-pointer bg-emerald-700 text-white shadow-2xs">همه</button>',
        'data-product-purchase-mode="all" class="rounded-lg py-2 text-[11.5px] font-extrabold transition-all cursor-pointer {ES_MODE_ALL_CLS}">همه</button>')
A = tok(A, 'data-product-purchase-mode="online" class="rounded-lg py-2 text-[11.5px] font-extrabold transition-all cursor-pointer text-slate-600 hover:text-slate-900">خرید آنلاین</button>',
        'data-product-purchase-mode="online" class="rounded-lg py-2 text-[11.5px] font-extrabold transition-all cursor-pointer {ES_MODE_ONLINE_CLS}">خرید آنلاین</button>')
A = tok(A, 'data-product-purchase-mode="inquiry" class="rounded-lg py-2 text-[11.5px] font-extrabold transition-all cursor-pointer text-slate-600 hover:text-slate-900">استعلامی</button>',
        'data-product-purchase-mode="inquiry" class="rounded-lg py-2 text-[11.5px] font-extrabold transition-all cursor-pointer {ES_MODE_INQUIRY_CLS}">استعلامی</button>')
A = tok(A, '<span class="text-[11.5px] font-bold text-emerald-700">همه قیمت‌ها</span>',
        '<span class="text-[11.5px] font-bold text-emerald-700">{ES_PRICE_LABEL}</span>')
A = tok(A, 'class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-emerald-100 accent-emerald-700" value="15000000"/>',
        'class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-emerald-100 accent-emerald-700" value="{ES_PRICE_VALUE}"/>')
# optionsهای IP و ولتاژ (بعد از option همه)
A = tok(A, '<option value="all" selected="">همه استانداردها</option><option value="IP68">IP68 (ضد غوطه‌وری)</option><option value="IP67">IP67 (ضدآب کامل)</option><option value="IP65">IP65 (فضای باز)</option></select>',
        '<option value="all" selected="">همه استانداردها</option>{ES_IP_OPTIONS}</select>')
A = tok(A, '<option value="all" selected="">همه ولتاژها</option><option value="24V">۲۴ ولت DC (ایمن)</option><option value="220V">۲۲۰ ولت شهری</option></select>',
        '<option value="all" selected="">همه ولتاژها</option>{ES_VOLT_OPTIONS}</select>')
# درخت دسته‌ها: از بعد از لینک «همه محصولات» تا پایان nav
ct0 = A.find('<div class="space-y-1"><a href="product_cat/point-light.html"')
assert ct0 > 0
ct1 = A.rfind('</nav>')
assert ct1 > ct0
CAT_TREE_RAW = A[ct0:ct1]
A = A[:ct0] + '{ES_CAT_TREE}' + A[ct1:]
# بازسازی
reb_a = (A.replace('{ES_COUNT}', '۲۰').replace('{ES_SEARCH_VALUE}', '')
          .replace('{ES_MODE_ALL_CLS}', 'bg-emerald-700 text-white shadow-2xs')
          .replace('{ES_MODE_ONLINE_CLS}', 'text-slate-600 hover:text-slate-900')
          .replace('{ES_MODE_INQUIRY_CLS}', 'text-slate-600 hover:text-slate-900')
          .replace('{ES_PRICE_LABEL}', 'همه قیمت‌ها').replace('{ES_PRICE_VALUE}', '15000000')
          .replace('{ES_IP_OPTIONS}', '<option value="IP68">IP68 (ضد غوطه‌وری)</option><option value="IP67">IP67 (ضدآب کامل)</option><option value="IP65">IP65 (فضای باز)</option>')
          .replace('{ES_VOLT_OPTIONS}', '<option value="24V">۲۴ ولت DC (ایمن)</option><option value="220V">۲۲۰ ولت شهری</option>')
          .replace('{ES_CAT_TREE}', CAT_TREE_RAW))
assert reb_a == A_RAW, f'ASIDE rebuild {len(reb_a)} vs {len(A_RAW)}'

# ═══════════ TOPBAR (154673→156981) ═══════════
tb0 = s.find('<div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3.5 shadow-2xs sm:px-5">')
tb1 = s.find('<div data-product-grid="true"', tb0)
assert tb0 > 0 and tb1 > tb0
T_RAW = s[tb0:tb1]
T = T_RAW
T = tok(T, '<span data-product-count="true">۲۰</span> محصول</span>',
        '<span data-product-count="true">{ES_COUNT}</span> محصول</span>')
T = tok(T, '<option value="default" selected="">پیش‌فرض کارخانه</option><option value="popular">پربازدیدترین</option><option value="rating">بالاترین امتیاز</option><option value="price-asc">ارزان‌ترین</option><option value="price-desc">گران‌ترین</option></select>',
        '<option value="default" selected="">پیش‌فرض کارخانه</option>{ES_SORT_EXTRA}</select>')
reb_t = T.replace('{ES_COUNT}', '۲۰').replace('{ES_SORT_EXTRA}', '<option value="popular">پربازدیدترین</option><option value="rating">بالاترین امتیاز</option><option value="price-asc">ارزان‌ترین</option><option value="price-desc">گران‌ترین</option>')
assert reb_t == T_RAW, 'TOPBAR rebuild'

# ═══════════ GRID (156981→239474) — فقط قالب باز و بسته؛ کارت‌ها از 5.11 ═══════════
G_RAW = s[156981:239474]
g_open = '<div data-product-grid="true" class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">'
assert G_RAW.startswith(g_open)
assert G_RAW.rstrip().endswith('</div>')
# قالب: باز + {ES_CARDS} + بستن
G = g_open + '{ES_CARDS}' + '</div>'

# ═══════════ PAGINATION (239474→241939) ═══════════
P_RAW = s[239474:s.find('<div data-product-empty="true"')]
P = P_RAW
P = tok(P, 'نمایش صفحه <span data-pagination-current="true" class="font-black text-emerald-800">۱</span> از <span data-pagination-total="true" class="font-black text-slate-900">۳</span> (مجموعاً <span data-product-count="true">۲۰</span> محصول)',
        'نمایش صفحه <span data-pagination-current="true" class="font-black text-emerald-800">{ES_CUR}</span> از <span data-pagination-total="true" class="font-black text-slate-900">{ES_TOTAL_PAGES}</span> (مجموعاً <span data-product-count="true">{ES_TOTAL_PRODUCTS}</span> محصول)')
P = tok(P, '<button type="button" data-page-prev="true" disabled="" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 disabled:opacity-40 cursor-pointer">',
        '<button type="button" data-page-prev="true"{ES_PREV_ATTR} class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 disabled:opacity-40 cursor-pointer"{ES_PREV_URL}>')
p0 = P.find('<button type="button" data-page-num="1"')
assert p0 > 0
n0 = P.find('<button type="button" data-page-next="true"')
assert n0 > p0
PAGES_RAW = P[p0:n0]
P = P[:p0] + '{ES_PAGES_HTML}' + P[n0:]
P = tok(P, '<button type="button" data-page-next="true" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 disabled:opacity-40 cursor-pointer">',
        '<button type="button" data-page-next="true"{ES_NEXT_ATTR} class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 disabled:opacity-40 cursor-pointer"{ES_NEXT_URL}>')
reb_p = (P.replace('{ES_CUR}', '۱').replace('{ES_TOTAL_PAGES}', '۳').replace('{ES_TOTAL_PRODUCTS}', '۲۰')
          .replace('{ES_PREV_ATTR}', ' disabled=""').replace('{ES_PREV_URL}', '')
          .replace('{ES_PAGES_HTML}', PAGES_RAW)
          .replace('{ES_NEXT_ATTR}', '').replace('{ES_NEXT_URL}', ''))
assert reb_p == P_RAW, 'PAGI rebuild'
print('HERO/ASIDE/TOPBAR/PAGI slice OK; cat-tree chars:', len(CAT_TREE_RAW), 'pages:', len(PAGES_RAW))

open(os.path.join(ROOT, 'template-parts/archive/product-filters.php'), 'w', encoding='utf-8').write(
"<?php\n"
"/**\n"
" * سایدبار فیلترهای آرشیو فروشگاه — برش بایت‌دقیق میرور shop.html (aside)\n"
" * فرگمنت با temp/gen_p67.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
+ nowdoc('ES_ASIDE', A, 'ESP67A') +
"$es_args = $args ?? [];\n"
"$es_mode = (string) ($es_args['mode'] ?? 'all');\n"
"$es_active = 'bg-emerald-700 text-white shadow-2xs';\n"
"$es_idle = 'text-slate-600 hover:text-slate-900';\n"
"$es_base = (string) ($es_args['base_url'] ?? '');\n"
"echo strtr($ES_ASIDE, [\n"
"    '{ES_COUNT}'             => es_fa_num((string) ($es_args['total'] ?? 0)),\n"
"    '{ES_SEARCH_VALUE}'      => esc_attr((string) ($es_args['search'] ?? '')),\n"
"    '{ES_MODE_ALL_CLS}'      => $es_mode === 'all' ? $es_active : $es_idle,\n"
"    '{ES_MODE_ONLINE_CLS}'   => $es_mode === 'online' ? $es_active : $es_idle,\n"
"    '{ES_MODE_INQUIRY_CLS}'  => $es_mode === 'inquiry' ? $es_active : $es_idle,\n"
"    '{ES_PRICE_LABEL}'       => esc_html((string) ($es_args['price_label'] ?? 'همه قیمت‌ها')),\n"
"    '{ES_PRICE_VALUE}'       => esc_attr((string) ($es_args['price_value'] ?? '15000000')),\n"
"    '{ES_IP_OPTIONS}'        => (string) ($es_args['ip_options'] ?? ''),\n"
"    '{ES_VOLT_OPTIONS}'      => (string) ($es_args['volt_options'] ?? ''),\n"
"    '{ES_CAT_TREE}'          => (string) ($es_args['cat_tree'] ?? ''),\n"
"]);\n")

open(os.path.join(ROOT, 'template-parts/archive/pagination.php'), 'w', encoding='utf-8').write(
"<?php\n"
"/**\n"
" * صفحه‌بندی آرشیو فروشگاه — برش بایت‌دقیق میرور shop.html (nav) با دکمه‌های میرور\n"
" * فرگمنت با temp/gen_p67.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
+ nowdoc('ES_PAGI', P, 'ESP67B') +
"$es_args = $args ?? [];\n"
"$es_current = max(1, (int) ($es_args['current'] ?? 1));\n"
"$es_total_pages = max(1, (int) ($es_args['total_pages'] ?? 1));\n"
"$es_base = (string) ($es_args['base_url'] ?? '');\n"
"$es_url = static function (int $n) use ($es_base): string {\n"
"    return $n <= 1 ? $es_base : $es_base . (str_contains($es_base, '?') ? '&' : '?') . 'paged=' . $n;\n"
"};\n\n"
"if ($es_total_pages <= 1) {\n"
"    return; // تک‌صفحه‌ای → بدون نوار\n"
"}\n"
"$es_pages_html = '';\n"
"foreach (range(1, $es_total_pages) as $es_n) {\n"
"    if ($es_n === $es_current) {\n"
"        $es_pages_html .= '<button type=\"button\" data-page-num=\"' . $es_n . '\" class=\"grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer bg-emerald-700 text-white shadow-sm shadow-emerald-700/25\">' . es_fa_num((string) $es_n) . '</button>';\n"
"    } else {\n"
"        $es_pages_html .= '<a href=\"' . esc_url($es_url($es_n)) . '\" data-page-num=\"' . $es_n . '\" class=\"grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer border border-slate-200 bg-white text-slate-700 hover:border-emerald-300 hover:text-emerald-800\">' . es_fa_num((string) $es_n) . '</a>';\n"
"    }\n"
"}\n"
"echo strtr($ES_PAGI, [\n"
"    '{ES_CUR}'            => es_fa_num((string) $es_current),\n"
"    '{ES_TOTAL_PAGES}'    => es_fa_num((string) $es_total_pages),\n"
"    '{ES_TOTAL_PRODUCTS}' => es_fa_num((string) ($es_args['total_products'] ?? 0)),\n"
"    '{ES_PREV_ATTR}'      => $es_current <= 1 ? ' disabled=\"\"' : '',\n"
"    '{ES_PREV_URL}'       => $es_current > 1 ? ' data-href=\"' . esc_url($es_url($es_current - 1)) . '\"' : '',\n"
"    '{ES_PAGES_HTML}'     => $es_pages_html,\n"
"    '{ES_NEXT_ATTR}'      => $es_current >= $es_total_pages ? ' disabled=\"\"' : '',\n"
"    '{ES_NEXT_URL}'       => $es_current < $es_total_pages ? ' data-href=\"' . esc_url($es_url($es_current + 1)) . '\"' : '',\n"
"]);\n")
print('partials written')

# ═══════════ EMPTY (241939→drawer) ═══════════
e0 = s.find('<div data-product-empty="true"')
assert e0 == 241934, e0
e1 = s.find('<div class="fixed bottom-5 left-1/2 z-40 -translate-x-1/2 lg:hidden">', e0)
assert e1 > e0
EMPTY_RAW = s[e0:e1]
EMPTY = tok(EMPTY_RAW, '<div data-product-empty="true" class="hidden rounded-2xl', '<div data-product-empty="true" class="{ES_EMPTY_CLS} rounded-2xl')
reb_e = EMPTY.replace('{ES_EMPTY_CLS}', 'hidden')
assert reb_e == EMPTY_RAW

# ═══════════ نوشتن شل archive-product.php ═══════════
REGION = (
    nowdoc('ES_HERO', HERO, 'ESP67H') + nowdoc('ES_TOPBAR', T, 'ESP67T')
    + nowdoc('ES_GRID_OPEN', g_open, 'ESP67G') + nowdoc('ES_EMPTY', EMPTY, 'ESP67E')
)
SHELL = (
"<?php\n"
"/**\n"
" * آرشیو محصولات (shop) — برش بایت‌دقیق میرور shop.html؛ فیلترها/گرید/صفحه‌بندی داینامیک\n"
" * فرگمنت‌ها با temp/gen_p67.py تولید شده‌اند.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
+ REGION +
"get_header();\n\n"
"$es_q = $GLOBALS['wp_query'] ?? null;\n"
"$es_total = (int) ($GLOBALS['__es_archive_total'] ?? ($es_q->found_posts ?? 0));\n"
"$es_paged = max(1, (int) get_query_var('paged'));\n"
"$es_per = (int) es_option('archives.products_per_page', '20');\n"
"$es_pages = max(1, (int) ceil($es_total / max(1, $es_per)));\n"
"$es_is_empty = $es_total < 1;\n\n"
"$es_get = static fn(string $k): string => isset($_GET[$k]) && is_string($_GET[$k]) ? (string) $_GET[$k] : '';\n\n"
"// گزینه‌های selectها از termها\n"
"$es_opt = static function (string $tax, string $all_label, string $sel) use ($es_get): string {\n"
"    $out = '';\n"
"    foreach ((array) get_terms(['taxonomy' => $tax, 'hide_empty' => false]) as $t) {\n"
"        if (is_wp_error($t) || !is_object($t)) {\n"
"            continue;\n"
"        }\n"
"        $v = (string) $t->slug;\n"
"        $lbl = (string) $t->name . ($t->description !== '' ? ' (' . $t->description . ')' : '');\n"
"        $out .= '<option value=\"' . esc_attr($v) . '\"' . ($es_get($sel) === $v ? ' selected=\"\"' : '') . '>' . esc_html($lbl) . '</option>';\n"
"    }\n"
"    return $out;\n"
"};\n\n"
"// درخت دسته‌ها (قالب میرور)\n"
"$es_cat_tree = '';\n"
"$es_terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);\n"
"foreach ((array) $es_terms as $t) {\n"
"    if (is_wp_error($t) || !is_object($t)) {\n"
"        continue;\n"
"    }\n"
"    $es_n = (int) ($t->count ?? 0);\n"
"    $es_cat_tree .= '<div class=\"space-y-1\"><a href=\"' . esc_url(get_term_link($t)) . '\" class=\"flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800\"><span class=\"flex items-center gap-2\"><span class=\"h-2 w-2 shrink-0 rounded-full bg-emerald-500/70\"></span><span class=\"leading-5\">' . esc_html((string) $t->name) . '</span></span><span class=\"rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-slate-100 text-slate-500\">' . es_fa_num((string) $es_n) . '</span></a></div>';\n"
"}\n\n"
"echo '<main class=\"bg-[#f6f8f7]\">';\n"
"echo strtr($ES_HERO, [\n"
"    '{ES_H1_A}'     => esc_html('فروشگاه محصولات '),\n"
"    '{ES_H1_B}'     => esc_html('نورپردازی شهری و صنعتی'),\n"
"    '{ES_HERO_DESC}' => esc_html((string) es_option('archives.shop_desc', 'مجموعه‌ای کامل از تجهیزات نورپردازی پیشرفته؛ از پوینت لایت پیکسل، ریسه‌های بلوطی، فندقی و سوزنی IP68 تا درختان نوری هوشمند، خورشیدنماها و باکس کنترل مرکزی')),\n"
"]);\n\n"
"// سکشن آرشیو: archive-open میرور داخل فرگمنت هیرو است (id=\"archive\")؛ اینجا ستون‌ها\n"
"ob_start();\n"
"es_get_template('template-parts/archive/product-filters.php', [\n"
"    'total'       => $es_total,\n"
"    'search'      => $es_get('s'),\n"
"    'mode'        => $es_get('purchase') ?: 'all',\n"
"    'price_label' => $es_get('price') !== '' ? esc_html('سقف: ' . es_fa_num(number_format((float) $es_get('price'))) . ' تومان') : 'همه قیمت‌ها',\n"
"    'price_value' => $es_get('price') ?: '15000000',\n"
"    'ip_options'  => $es_opt('pa_ip-rating', 'همه استانداردها', 'ip'),\n"
"    'volt_options' => $es_opt('pa_input-voltage', 'همه ولتاژها', 'voltage'),\n"
"    'cat_tree'    => $es_cat_tree,\n"
"]);\n"
"echo (string) ob_get_clean();\n\n"
"echo '<div class=\"lg:col-span-8 xl:col-span-9\">';\n\n"
"// نوار بالای گرید\n"
"echo strtr($ES_TOPBAR, [\n"
"    '{ES_COUNT}'       => es_fa_num((string) $es_total),\n"
"    '{ES_SORT_EXTRA}'  => $es_get('sort') === 'popular' ? '<option value=\"popular\" selected=\"\">پربازدیدترین</option>' : '',\n"
"]);\n\n"
"echo strtr($ES_GRID_OPEN, []);\n"
"if (!$es_is_empty) {\n"
"    $es_i = 0;\n"
"    foreach (($es_q->posts ?? []) as $es_post) {\n"
"        ob_start();\n"
"        es_get_template('template-parts/cards/card-product.php', ['post' => $es_post, 'custom' => (string) round(($es_i % 3) * 0.06, 2)]);\n"
"        echo (string) ob_get_clean();\n"
"        $es_i++;\n"
"    }\n"
"}\n"
"echo '</div>';\n\n"
"if (!$es_is_empty && $es_pages > 1) {\n"
"    ob_start();\n"
"    es_get_template('template-parts/archive/pagination.php', [\n"
"        'current' => $es_paged, 'total_pages' => $es_pages, 'total_products' => $es_total,\n"
"        'base_url' => get_post_type_archive_link('product'),\n"
"    ]);\n"
"    echo (string) ob_get_clean();\n"
"}\n\n"
"echo strtr($ES_EMPTY, ['{ES_EMPTY_CLS}' => $es_is_empty ? 'block' : 'hidden']);\n"
"echo '</section>';\n"
"echo '</main>';\n"
"get_footer();\n")
open(os.path.join(ROOT, 'archive-product.php'), 'w', encoding='utf-8').write(SHELL)
print('archive-product.php written:', len(SHELL))
