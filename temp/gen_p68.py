#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۶.۸ — آرشیو دسته/تگ محصول (برش بایت‌دقیق product_cat/wall-washer.html).
اجرا: python3 temp/gen_p68.py
"""
import os

ROOT = '/home/user/erfansanat-theme-ai'
SRC = os.path.join(ROOT, 'uploads/static-mirror/product_cat/wall-washer.html')

def nowdoc(name, body, tag):
    return "$" + name + " = <<<'{t}'\n{b}\n{t};\n".format(t=tag, b=body)

def tok(R, old, new):
    assert R.count(old) >= 1, old[:70]
    return R.replace(old, new, 1)

s = open(SRC, encoding='utf-8').read()

# ═══════════ HERO دسته (128900→139004) ═══════════
h0 = s.find('<section class="relative overflow-hidden bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-36">')
assert h0 == 128900
# برش تا archive-open — سکشن id="archive" توسط شل چاپ می‌شود (دوبله‌سکشن ممنوع)
ha = s.find('<section id="archive"', h0)
assert ha > h0
HERO_RAW = s[h0:ha]
HERO = tok(HERO_RAW, '<h1 class="text-[2.3rem] font-black leading-[1.28] tracking-[-0.015em] text-slate-950 sm:text-[3rem] lg:text-[3.4rem] lg:leading-[1.22]">تولید و قیمت کارخانه <br/><span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent">لینیرلایت و والواشر LED نمای ساختمان</span></h1>',
           '<h1 class="text-[2.3rem] font-black leading-[1.28] tracking-[-0.015em] text-slate-950 sm:text-[3rem] lg:text-[3.4rem] lg:leading-[1.22]">{ES_H1_A}<br/><span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent">{ES_H1_B}</span></h1>')
HERO = tok(HERO, '<p class="mt-6 max-w-[36rem] text-[15.5px] font-medium leading-9 text-slate-600 lg:text-[16px]">والواشرهای خطی پرقدرت و لینیر لایت‌های معماری با بدنه آلومینیوم اکسترود صنعتی، درجه حفاظت IP68، نمایش ۱۶ میلیون رنگ و لنزهای اپتیکی دقیق.</p>',
           '<p class="mt-6 max-w-[36rem] text-[15.5px] font-medium leading-9 text-slate-600 lg:text-[16px]">{ES_HERO_DESC}</p>')
HERO = tok(HERO, 'src="../images/light-element.jpg"', 'src="{ES_HERO_IMG}"')
HERO = tok(HERO, '<a href="../index.html"', '<a href="{ES_HOME_URL}"')
HERO = tok(HERO, '<a href="../shop.html"', '<a href="{ES_SHOP_URL}"')
HERO = tok(HERO, 'font-extrabold bg-emerald-50 text-emerald-800">لینیرلایت و والواشر</span></span></nav>',
           'font-extrabold bg-emerald-50 text-emerald-800">{ES_TERM_NAME}</span></span></nav>')
reb = (HERO.replace('{ES_H1_A}', 'تولید و قیمت کارخانه ').replace('{ES_H1_B}', 'لینیرلایت و والواشر LED نمای ساختمان')
           .replace('{ES_HERO_DESC}', 'والواشرهای خطی پرقدرت و لینیر لایت‌های معماری با بدنه آلومینیوم اکسترود صنعتی، درجه حفاظت IP68، نمایش ۱۶ میلیون رنگ و لنزهای اپتیکی دقیق.')
           .replace('{ES_HERO_IMG}', '../images/light-element.jpg')
           .replace('{ES_HOME_URL}', '../index.html').replace('{ES_SHOP_URL}', '../shop.html')
           .replace('{ES_TERM_NAME}', 'لینیرلایت و والواشر'))
assert reb == HERO_RAW, f'HERO rebuild {len(reb)} vs {len(HERO_RAW)}'

# ═══════════ FAQ دسته — WRAPPER + ROW قالبی ═══════════
w0 = s.rfind('<div class="mt-10 border-t border-slate-100 pt-8" data-faq-list="true">')
assert w0 > 0
j = s.find('</article>', w0)
assert j > w0
FAQ_BLOCK_RAW = s[w0:j]
CARD = '<div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-slate-50/40">'
pre = FAQ_BLOCK_RAW[:FAQ_BLOCK_RAW.find(CARD)]
tail = FAQ_BLOCK_RAW[FAQ_BLOCK_RAW.find(CARD):]
parts = tail.split(CARD)
assert len(parts) == 5, len(parts)
ROW_RAW = CARD + parts[1]          # ردیف اول کامل (خودبسته)
last_card = CARD + parts[4]
POST = last_card[-12:]  # دو بستن آخر = گرید+رپر (بدنه+کارت داخل الگوی ردیف‌اند)
assert POST == '</div></div>', repr(POST)

ROW = ROW_RAW
ROW = tok(ROW, 'data-faq-toggle="0"', 'data-faq-toggle="{ES_I}"')
ROW = tok(ROW, '>نحوه استعلام قیمت و دریافت پیش‌فاکتور رسمی برای شهرداری‌ها چگونه است؟</span>', '>{ES_Q}</span>')
ROW = tok(ROW, 'data-faq-icon="0" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-transform rotate-45 bg-emerald-800 text-white"',
          'data-faq-icon="{ES_I}" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-transform {ES_ICON_CLS}"')
ROW = tok(ROW, 'data-faq-body="0" class="block border-t border-slate-200/60 bg-white px-5 py-4 text-[13px] leading-7 text-slate-600"',
          'data-faq-body="{ES_I}" class="{ES_BODY_CLS} border-t border-slate-200/60 bg-white px-5 py-4 text-[13px] leading-7 text-slate-600"')
A1 = 'شهرداری‌ها، دهیاری‌ها، سازمان‌های زیباسازی و پیمانکاران محترم می‌توانند از طریق تماس با شماره ۰۳۱-۹۱۰۹۱۰۱۱ (۱۰ خط ویژه) یا ارسال مشخصات پروژه، در کمتر از ۲ ساعت کاری پیش‌فاکتور رسمی ممهور به همراه کدهای کالا و کاتالوگ فنی دریافت نمایند. همچنین امکان ثبت معامله در سامانه تدارکات الکترونیکی دولت (ستاد) فراهم است.'
ROW = tok(ROW, A1, '{ES_A}')
assert all(t in ROW for t in ('{ES_I}', '{ES_Q}', '{ES_A}', '{ES_ICON_CLS}', '{ES_BODY_CLS}'))

def detok_row(d):
    return (ROW.replace('{ES_I}', d['i']).replace('{ES_Q}', d['q']).replace('{ES_A}', d['a'])
               .replace('{ES_ICON_CLS}', d['icon']).replace('{ES_BODY_CLS}', d['body']))

def row_answer(r):
    m0 = r.find('text-slate-600">')
    assert m0 > 0
    m1 = r.find('</div>', m0)
    return r[m0 + len('text-slate-600">'):m1]

QS = ['نحوه استعلام قیمت و دریافت پیش‌فاکتور رسمی برای شهرداری‌ها چگونه است؟',
      'تفاوت محصولات «قابل خرید آنلاین» با محصولات «نیازمند استعلام» در فروشگاه چیست؟',
      'چرا ریسه‌ها و پوینت لایت‌های عرفان صنعت در بارندگی دچار اتصالی نمی‌شوند؟',
      'برای پارک‌ها و فضاهایی که مردم به المان دسترسی دارند چه ولتاژی پیشنهاد می‌شود؟']
ANS = [row_answer(p) for p in parts[1:]]
ICONS = ['rotate-45 bg-emerald-800 text-white', 'bg-emerald-100 text-emerald-800', 'bg-emerald-100 text-emerald-800', 'bg-emerald-100 text-emerald-800']
BODIES = ['block', 'hidden', 'hidden', 'hidden']
reb_rows = ''.join(detok_row(dict(i=str(n), q=QS[n], a=ANS[n], icon=ICONS[n], body=BODIES[n])) for n in range(4))
reb_full = pre + reb_rows + POST
assert reb_full == FAQ_BLOCK_RAW, f'FAQ rebuild {len(reb_full)} vs {len(FAQ_BLOCK_RAW)}'

# WRAPPER: pre + {ES_ROWS} + POST — با توکن عنوان
WRAP = tok(pre, '<h3 class="text-lg font-black text-slate-950">سوالات متداول درباره لینیرلایت و والواشر</h3>',
           '<h3 class="text-lg font-black text-slate-950">سوالات متداول درباره {ES_TERM_LABEL}</h3>')
WRAP = WRAP + '{ES_ROWS}' + POST
reb_w = WRAP.replace('{ES_ROWS}', reb_rows).replace('{ES_TERM_LABEL}', 'لینیرلایت و والواشر')
assert reb_w == FAQ_BLOCK_RAW, 'WRAPPER rebuild'
print('HERO + FAQ slice OK; faq block:', len(FAQ_BLOCK_RAW), 'rows:', len(ROW_RAW))

open(os.path.join(ROOT, 'template-parts/archive/category-faq.php'), 'w', encoding='utf-8').write(
"<?php\n"
"/**\n"
" * FAQ دسته محصول — آکاردئون میرور (قالب cat) از متای ترم (es_get_category_faq)؛ مخفی اگر خالی\n"
" * فرگمنت با temp/gen_p68.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
+ nowdoc('ES_CAT_FAQ_WRAPPER', WRAP, 'ESP68W') + nowdoc('ES_CAT_FAQ', ROW, 'ESP68F') +
"$es_args = $args ?? [];\n"
"$es_label = (string) ($es_args['label'] ?? '');\n"
"$es_rows = array_values((array) ($es_args['rows'] ?? []));\n"
"if ($es_label === '' || $es_rows === []) {\n"
"    return; // FAQ فقط اگر متا پر\n"
"}\n"
"$es_rows_html = '';\n"
"foreach ($es_rows as $es_i => $es_row) {\n"
"    $es_first = $es_i === 0;\n"
"    $es_rows_html .= strtr($ES_CAT_FAQ, [\n"
"        '{ES_I}'        => (string) $es_i,\n"
"        '{ES_Q}'        => esc_html((string) ($es_row['name_question'] ?? '')),\n"
"        '{ES_A}'        => esc_html((string) ($es_row['answer_question'] ?? '')),\n"
"        '{ES_ICON_CLS}' => $es_first ? 'rotate-45 bg-emerald-800 text-white' : 'bg-emerald-100 text-emerald-800',\n"
"        '{ES_BODY_CLS}' => $es_first ? 'block' : 'hidden',\n"
"    ]);\n"
"}\n"
"echo strtr($ES_CAT_FAQ_WRAPPER, ['{ES_ROWS}' => $es_rows_html, '{ES_TERM_LABEL}' => esc_html($es_label)]);\n")

# ═══════════ پارتیال hero-cat.php ═══════════
open(os.path.join(ROOT, 'template-parts/archive/hero-cat.php'), 'w', encoding='utf-8').write(
"<?php\n"
"/**\n"
" * هیرو آرشیو دسته محصول — برش بایت‌دقیق میرور product_cat/wall-washer.html\n"
" * فرگمنت با temp/gen_p68.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
+ nowdoc('ES_HERO', HERO, 'ESP68H') +
"$es_args = $args ?? [];\n"
"$es_label = (string) ($es_args['label'] ?? '');\n"
"echo strtr($ES_HERO, [\n"
"    '{ES_H1_A}'      => esc_html((string) ($es_args['h1_a'] ?? '')),\n"
"    '{ES_H1_B}'      => esc_html((string) ($es_args['h1_b'] ?? '')),\n"
"    '{ES_HERO_DESC}' => esc_html((string) ($es_args['desc'] ?? '')),\n"
"    '{ES_HERO_IMG}'  => esc_url((string) ($es_args['img'] ?? es_media_url('assets/images/light-element.jpg'))),\n"
"    '{ES_HOME_URL}'  => esc_url(home_url('/')),\n"
"    '{ES_SHOP_URL}'  => esc_url(es_page_url('shop')),\n"
"    '{ES_TERM_NAME}' => esc_html($es_label),\n"
"]);\n")

# ═══════════ taxonomy-product_cat.php ═══════════
SHELL = (
"<?php\n"
"/**\n"
" * آرشیو دسته محصول — هیرو/گرید/صفحه‌بندی از شل shop (۶.۷) + FAQ دسته از متای ترم (۳.۱۳)\n"
" * برش هیرو: میرور product_cat/wall-washer.html با temp/gen_p68.py.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"$es_term = get_queried_object();\n"
"if (!is_object($es_term) || ($es_term->term_id ?? 0) < 1) {\n"
"    get_template_part('archive-product');\n"
"    return;\n"
"}\n"
"$es_tid = (int) $es_term->term_id;\n"
"$es_label = (string) ($es_term->name ?? '');\n"
"$es_desc = trim(term_description($es_tid));\n"
"$es_q = $GLOBALS['wp_query'] ?? null;\n"
"$es_total = (int) ($GLOBALS['__es_archive_total'] ?? ($es_q->found_posts ?? 0));\n"
"$es_paged = max(1, (int) get_query_var('paged'));\n"
"$es_per = (int) es_option('archives.products_per_page', '20');\n"
"$es_pages = max(1, (int) ceil($es_total / max(1, $es_per)));\n"
"$es_is_empty = $es_total < 1;\n\n"
"$es_get = static fn(string $k): string => isset($_GET[$k]) && is_string($_GET[$k]) ? (string) $_GET[$k] : '';\n\n"
"$es_opt = static function (string $tax, string $sel) use ($es_get): string {\n"
"    $out = '';\n"
"    foreach ((array) get_terms(['taxonomy' => $tax, 'hide_empty' => false]) as $t) {\n"
"        if (is_wp_error($t) || !is_object($t)) {\n"
"            continue;\n"
"        }\n"
"        $out .= '<option value=\"' . esc_attr((string) $t->slug) . '\"' . ($es_get($sel) === (string) $t->slug ? ' selected=\"\"' : '') . '>' . esc_html((string) $t->name) . '</option>';\n"
"    }\n"
"    return $out;\n"
"};\n\n"
"// درخت دسته‌ها (ترم فعلی = فعال میرور)\n"
"$es_cat_tree = '';\n"
"foreach ((array) get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]) as $t) {\n"
"    if (is_wp_error($t) || !is_object($t) || (int) $t->term_id === $es_tid) {\n"
"        continue;\n"
"    }\n"
"    $es_cat_tree .= '<div class=\"space-y-1\"><a href=\"' . esc_url(get_term_link($t)) . '\" class=\"flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800\"><span class=\"flex items-center gap-2\"><span class=\"h-2 w-2 shrink-0 rounded-full bg-emerald-500/70\"></span><span class=\"leading-5\">' . esc_html((string) $t->name) . '</span></span><span class=\"rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-slate-100 text-slate-500\">' . es_fa_num((string) ($t->count ?? 0)) . '</span></a></div>';\n"
"}\n\n"
"echo '<main class=\"bg-[#f6f8f7]\">';\n"
"ob_start();\n"
"es_get_template('template-parts/archive/hero-cat.php', [\n"
"    'h1_a' => 'تولید و قیمت کارخانه ',\n"
"    'h1_b' => $es_label,\n"
"    'label' => $es_label,\n"
"    'desc' => $es_desc !== '' ? $es_desc : (string) es_option('archives.cat_desc_fallback', 'والواشرهای خطی پرقدرت و لینیر لایت‌های معماری با بدنه آلومینیوم اکسترود صنعتی، درجه حفاظت IP68، نمایش ۱۶ میلیون رنگ و لنزهای اپتیکی دقیق.'),\n"
"    'img'  => es_get_category_thumb($es_tid),\n"
"]);\n"
"echo (string) ob_get_clean();\n\n"
"echo '<section id=\"archive\" class=\"mx-auto max-w-7xl scroll-mt-24 px-5 py-12 lg:px-8 lg:py-16\" data-products-archive=\"true\" data-initial-category=\"' . esc_attr($es_label) . '\" data-items-per-page=\"9\"><div class=\"grid items-start gap-8 lg:grid-cols-12\">';\n\n"
"ob_start();\n"
"es_get_template('template-parts/archive/product-filters.php', [\n"
"    'total' => $es_total, 'search' => $es_get('s'), 'mode' => $es_get('purchase') ?: 'all',\n"
"    'price_label' => $es_get('price') !== '' ? esc_html('سقف: ' . es_fa_num(number_format((float) $es_get('price'))) . ' تومان') : 'همه قیمت‌ها',\n"
"    'price_value' => $es_get('price') ?: '15000000',\n"
"    'ip_options' => $es_opt('pa_ip-rating', 'ip'), 'volt_options' => $es_opt('pa_input-voltage', 'voltage'),\n"
"    'cat_tree' => $es_cat_tree,\n"
"]);\n"
"echo (string) ob_get_clean();\n\n"
"echo '<div class=\"lg:col-span-8 xl:col-span-9\">';\n"
"$es_topbar_label = $es_label;\n"
"ob_start();\n"
"es_get_template('template-parts/archive/topbar.php', ['total' => $es_total, 'label' => $es_topbar_label, 'sort' => $es_get('sort')]);\n"
"echo (string) ob_get_clean();\n\n"
"echo '<div data-product-grid=\"true\" class=\"grid gap-6 sm:grid-cols-2 xl:grid-cols-3\">';\n"
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
"        'base_url' => (string) get_term_link($es_term),\n"
"    ]);\n"
"    echo (string) ob_get_clean();\n"
"}\n\n"
"es_get_template('template-parts/archive/product-empty.php', ['show' => $es_is_empty]);\n"
"\n"
"// FAQ دسته (متای ترم) — مخفی اگر خالی\n"
"ob_start();\n"
"echo '<div class=\"mt-16 lg:mt-20\">';\n"
"es_get_template('template-parts/archive/category-faq.php', [\n"
"    'label' => $es_label,\n"
"    'rows'  => es_get_category_faq($es_tid),\n"
"]);\n"
"echo (string) ob_get_clean();\n"
"echo '</div>';\n"
"echo '</section>';\n"
"echo '</main>';\n"
"get_footer();\n")

open(os.path.join(ROOT, 'taxonomy-product_cat.php'), 'w', encoding='utf-8').write(SHELL)

# ═══════════ taxonomy-product_tag.php — گرید ساده + صفحه‌بندی ═══════════
TAG = (
"<?php\n"
"/**\n"
" * آرشیو برچسب محصول — گرید ساده + صفحه‌بندی (نوار فیلتر ندارد)\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"get_header();\n"
"$es_term = get_queried_object();\n"
"$es_label = is_object($es_term) ? (string) ($es_term->name ?? '') : '';\n"
"$es_q = $GLOBALS['wp_query'] ?? null;\n"
"$es_total = (int) ($GLOBALS['__es_archive_total'] ?? ($es_q->found_posts ?? 0));\n"
"$es_paged = max(1, (int) get_query_var('paged'));\n"
"$es_per = (int) es_option('archives.products_per_page', '20');\n"
"$es_pages = max(1, (int) ceil($es_total / max(1, $es_per)));\n"
"echo '<main class=\"bg-[#f6f8f7]\">';\n"
"echo '<section class=\"relative overflow-hidden pb-16 lg:pb-24\"><div class=\"mx-auto max-w-7xl px-5 lg:px-8\">';\n"
"echo '<div class=\"mb-7 flex flex-wrap items-end justify-between gap-4\"><div><div class=\"mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700\"><span>برچسب محصول</span></div><h1 class=\"text-2xl font-extrabold text-slate-900\">' . esc_html($es_label) . '</h1></div><span class=\"rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-extrabold text-emerald-800\">' . es_fa_num((string) $es_total) . ' محصول</span></div>';\n"
"echo '<div data-product-grid=\"true\" class=\"grid gap-6 sm:grid-cols-2 xl:grid-cols-3\">';\n"
"if ($es_total > 0) {\n"
"    $es_i = 0;\n"
"    foreach (($es_q->posts ?? []) as $es_post) {\n"
"        ob_start();\n"
"        es_get_template('template-parts/cards/card-product.php', ['post' => $es_post, 'custom' => (string) round(($es_i % 3) * 0.06, 2)]);\n"
"        echo (string) ob_get_clean();\n"
"        $es_i++;\n"
"    }\n"
"}\n"
"echo '</div>';\n"
"if ($es_total > 0 && $es_pages > 1) {\n"
"    ob_start();\n"
"    es_get_template('template-parts/archive/pagination.php', [\n"
"        'current' => $es_paged, 'total_pages' => $es_pages, 'total_products' => $es_total,\n"
"        'base_url' => (string) get_term_link($es_term),\n"
"    ]);\n"
"    echo (string) ob_get_clean();\n"
"}\n"
"echo '</div></section>';\n"
"echo '</main>';\n"
"get_footer();\n")
open(os.path.join(ROOT, 'taxonomy-product_tag.php'), 'w', encoding='utf-8').write(TAG)

print('taxonomy shells written')

# ═══════════ پارتیال‌های مشترک: topbar + empty (از shop.html) ═══════════
SHOP = os.path.join(ROOT, 'uploads/static-mirror/shop.html')
sh = open(SHOP, encoding='utf-8').read()
tb0 = sh.find('<div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3.5 shadow-2xs sm:px-5">')
tb1 = sh.find('<div data-product-grid="true"', tb0)
T_RAW = sh[tb0:tb1]
T = tok(T_RAW, '<span data-product-count="true">۲۰</span> محصول</span>',
        '<span data-product-count="true">{ES_COUNT}</span> محصول</span>')
T = tok(T, '<span class="text-[13.5px] font-black text-slate-900">همه محصولات تولیدی عرفان صنعت</span>',
        '<span class="text-[13.5px] font-black text-slate-900">{ES_LABEL}</span>')
T = tok(T, '<option value="default" selected="">پیش‌فرض کارخانه</option><option value="popular">پربازدیدترین</option><option value="rating">بالاترین امتیاز</option><option value="price-asc">ارزان‌ترین</option><option value="price-desc">گران‌ترین</option></select>',
        '<option value="default" selected="">پیش‌فرض کارخانه</option>{ES_SORT_OPTIONS}</select>')
SORT_OPT = '<option value="popular">پربازدیدترین</option><option value="rating">بالاترین امتیاز</option><option value="price-asc">ارزان‌ترین</option><option value="price-desc">گران‌ترین</option>'
reb_t = (T.replace('{ES_COUNT}', '۲۰').replace('{ES_LABEL}', 'همه محصولات تولیدی عرفان صنعت')
          .replace('{ES_SORT_OPTIONS}', SORT_OPT))
assert reb_t == T_RAW, 'TOPBAR rebuild'

e0 = sh.find('<div data-product-empty="true"')
assert e0 == 241934, e0
e1 = sh.find('<div class="fixed bottom-5 left-1/2 z-40 -translate-x-1/2 lg:hidden">', e0)
EMPTY_RAW = sh[e0:e1]
EMPTY = tok(EMPTY_RAW, '<div data-product-empty="true" class="hidden rounded-2xl', '<div data-product-empty="true" class="{ES_EMPTY_CLS} rounded-2xl')
EMPTY = tok(EMPTY, 'href="shop.html"', 'href="{ES_SHOP_URL}"')
reb_e = (EMPTY.replace('{ES_EMPTY_CLS}', 'hidden').replace('{ES_SHOP_URL}', 'shop.html'))
assert reb_e == EMPTY_RAW, 'EMPTY rebuild'

open(os.path.join(ROOT, 'template-parts/archive/topbar.php'), 'w', encoding='utf-8').write(
"<?php\n"
"/**\n"
" * نوار بالای گرید آرشیو (شمارنده + مرتب‌سازی) — برش بایت‌دقیق میرور shop.html\n"
" * فرگمنت با temp/gen_p68.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
+ nowdoc('ES_TOPBAR', T, 'ESP68T') +
"$es_args = $args ?? [];\n"
"$es_sort = (string) ($es_args['sort'] ?? '');\n"
"$es_opts = '<option value=\"popular\">پربازدیدترین</option><option value=\"rating\">بالاترین امتیاز</option><option value=\"price-asc\">ارزان‌ترین</option><option value=\"price-desc\">گران‌ترین</option>';\n"
"if ($es_sort !== '' && str_contains($es_opts, 'value=\"' . $es_sort . '\"')) {\n"
"    $es_opts = str_replace('value=\"' . $es_sort . '\"', 'value=\"' . $es_sort . '\" selected=\"\"', $es_opts);\n"
"}\n"
"echo strtr($ES_TOPBAR, [\n"
"    '{ES_COUNT}'        => es_fa_num((string) ($es_args['total'] ?? 0)),\n"
"    '{ES_LABEL}'        => esc_html((string) ($es_args['label'] ?? 'همه محصولات تولیدی عرفان صنعت')),\n"
"    '{ES_SORT_OPTIONS}' => $es_opts,\n"
"]);\n")

open(os.path.join(ROOT, 'template-parts/archive/product-empty.php'), 'w', encoding='utf-8').write(
"<?php\n"
"/**\n"
" * جعبه حالت خالی آرشیو — برش بایت‌دقیق میرور shop.html\n"
" * فرگمنت با temp/gen_p68.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
+ nowdoc('ES_EMPTY', EMPTY, 'ESP68E') +
"$es_args = $args ?? [];\n"
"echo strtr($ES_EMPTY, [\n"
"    '{ES_EMPTY_CLS}' => !empty($es_args['show']) ? 'block' : 'hidden',\n"
"    '{ES_SHOP_URL}'  => esc_url((string) ($es_args['shop_url'] ?? es_page_url('shop'))),\n"
"]);\n")
print('topbar + empty partials written')
