#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۶.۳ — تب‌های محصول + جدول مشخصات pa_* + شناسه‌های قانونی (برش بایت‌دقیق wall-washer).
اجرا: python3 temp/gen_p63.py
"""
import os

ROOT = '/home/user/erfansanat-theme-ai'
SRC = os.path.join(ROOT, 'uploads/static-mirror/product/wall-washer.html')

def nowdoc(name, body, tag):
    return f"${name} = <<<'{tag}'\n{body}\n{tag};\n"

s = open(SRC, encoding='utf-8').read()

SEC_ANCHOR = '<section id="product-details" data-prod-tabs-container="true" class="mx-auto max-w-6xl scroll-mt-28 px-5 pb-14 lg:px-8">'
sec = s.find(SEC_ANCHOR)
assert sec > 0
b1 = s.find('<button type="button" data-prod-tab-btn=', sec)
assert b1 > 0
HEAD_A = s[sec:b1]

def cut_btn(start):
    end = s.find('</button>', start) + len('</button>')
    return end, s[start:end]

b1e, BTN_DESC = cut_btn(b1)
b2 = s.find('<button type="button" data-prod-tab-btn=', b1e)
b2e, BTN_SPECS = cut_btn(b2)
b3 = s.find('<button type="button" data-prod-tab-btn=', b2e)
b3e, BTN_REVIEWS = cut_btn(b3)
desc_panel = s.find('<div data-prod-tab-panel="desc"', b3e)
assert desc_panel > 0
HEAD_B = s[b3e:desc_panel]
assert HEAD_B == '</div><div class="p-6 sm:p-8 lg:p-10">', repr(HEAD_B)

# توکن‌های دکمه‌ها
BTN_DESC = BTN_DESC.replace('<span>معرفی و کاربردها</span>', '<span>{ES_LABEL}</span>', 1)
BTN_DESC = BTN_DESC.replace('class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap bg-emerald-800 text-white shadow-sm"',
                            'class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_BTN_CLS}"', 1)
BTN_DESC = BTN_DESC.replace('class="lucide lucide-sparkles h-4 w-4 text-white"', 'class="lucide lucide-sparkles h-4 w-4 {ES_ICON_CLS}"', 1)
assert '{ES_LABEL}' in BTN_DESC and '{ES_BTN_CLS}' in BTN_DESC and '{ES_ICON_CLS}' in BTN_DESC

BTN_SPECS = BTN_SPECS.replace('<span>مشخصات فنی و شناسه‌ها</span>', '<span>{ES_LABEL}</span>', 1)
BTN_SPECS = BTN_SPECS.replace('class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900"',
                              'class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_BTN_CLS}"', 1)
BTN_SPECS = BTN_SPECS.replace('class="lucide lucide-clipboard-list h-4 w-4 text-emerald-700"', 'class="lucide lucide-clipboard-list h-4 w-4 {ES_ICON_CLS}"', 1)
assert '{ES_LABEL}' in BTN_SPECS and '{ES_BTN_CLS}' in BTN_SPECS and '{ES_ICON_CLS}' in BTN_SPECS

BTN_REVIEWS = BTN_REVIEWS.replace('<span>نظرات کاربران (۳)</span>', '<span>نظرات کاربران ({ES_REV_COUNT})</span>', 1)
BTN_REVIEWS = BTN_REVIEWS.replace('class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900"',
                                  'class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_BTN_CLS}"', 1)
BTN_REVIEWS = BTN_REVIEWS.replace('class="lucide lucide-message-circle h-4 w-4 text-emerald-700"', 'class="lucide lucide-message-circle h-4 w-4 {ES_ICON_CLS}"', 1)
assert '{ES_REV_COUNT}' in BTN_REVIEWS and '{ES_BTN_CLS}' in BTN_REVIEWS

# ─── پنل specs ───
sp = s.find('<div data-prod-tab-panel="specs"')
rv = s.find('<div data-prod-tab-panel="reviews"')
assert 0 < sp < rv
SPECS = s[sp:rv]

SPECS = SPECS.replace('<h2 class="text-xl font-extrabold text-slate-900">جدول کامل مشخصات فنی و مهندسی والواشر RGB و تک‌رنگ صنعتی (IP68)</h2>',
                      '<h2 class="text-xl font-extrabold text-slate-900">جدول کامل مشخصات فنی و مهندسی {ES_TITLE}</h2>', 1)
assert '{ES_TITLE}' in SPECS

# ROW_TPL: از اولین ردیف گرید تا پایان ردیف‌ها
tbl_open = '<div class="overflow-hidden rounded-2xl border border-slate-200">'
t0 = SPECS.find(tbl_open)
assert t0 > 0
r0 = SPECS.find('<div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 bg-slate-50/70 border-b border-slate-100 last:border-b-0">', t0)
r0e = SPECS.find('</div></div>', r0) + len('</div></div>')
ROW = SPECS[r0:r0e]
ROW = ROW.replace('bg-slate-50/70 border-b border-slate-100 last:border-b-0">', '{ES_BG} border-b border-slate-100 last:border-b-0">', 1)
ROW = ROW.replace('<div class="text-xs font-bold text-slate-500">نوع LED</div>', '<div class="text-xs font-bold text-slate-500">{ES_LBL}</div>', 1)
ROW = ROW.replace('<div class="text-sm font-extrabold text-slate-900 sm:col-span-2">پیکسل RGB پرقدرت فول‌کالر / پاور LED</div>', '<div class="text-sm font-extrabold text-slate-900 sm:col-span-2">{ES_VAL}</div>', 1)
assert all(t in ROW for t in ('{ES_BG}', '{ES_LBL}', '{ES_VAL}'))
ROWS_OPEN = SPECS[t0:r0]
# انتهای جدول: بعد از آخرین ردیف
legal_anchor = '<div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-6">'
lg = SPECS.find(legal_anchor)
assert lg > 0
ROWS = SPECS[r0e:lg]
ROWS_CLOSE = '</div></div>'
assert SPECS[lg - len(ROWS_CLOSE):lg] == ROWS_CLOSE
ROWS_BODY = SPECS[r0e:lg - len(ROWS_CLOSE)]
# راستی‌آزمایی: ۹ ردیف = ۱ الگو + ۸ تکرار idle؟ ردیف‌ها با پس‌زمینه متناوب‌اند — جداگانه می‌شماریم
n_slate = ROWS_BODY.count('bg-slate-50/70 border-b border-slate-100 last:border-b-0">')
n_white = ROWS_BODY.count('bg-white border-b border-slate-100 last:border-b-0">')
assert (n_slate, n_white) == (4, 4), (n_slate, n_white)

# LEGAL CARD_TPL
c0 = SPECS.find('<div class="rounded-xl border border-emerald-100 bg-white p-3.5">', lg)
c0e = SPECS.find('</div>', c0) + len('</div>')  # کارت div تو در تو ندارد
CARD = SPECS[c0:c0e]
assert CARD.count('<div') == 1, 'card must be flat'
CARD = CARD.replace('<span class="block text-[11px] font-bold text-slate-500">شناسه فنی محصول در کارخانه</span>',
                    '<span class="block text-[11px] font-bold text-slate-500">{ES_LBL}</span>', 1)
CARD = CARD.replace('<span dir="ltr" class="mt-1 block font-mono text-sm font-black text-slate-900 tabular-nums">ESE-WW-036</span>',
                    '<span dir="ltr" class="mt-1 block font-mono text-sm font-black {ES_VAL_CLS} tabular-nums">{ES_VAL}</span>', 1)
assert '{ES_LBL}' in CARD and '{ES_VAL}' in CARD and '{ES_VAL_CLS}' in CARD
CARDS_BODY = SPECS[c0e:]
# بستن گرید کارت‌ها + باکس امرالد + space-y-8 + پنل specs
CARDS_CLOSE = '</div></div></div></div>'  # گرید کارت‌ها + باکس امرالد + space-y-8 + پنل specs
assert CARDS_BODY.endswith(CARDS_CLOSE), repr(CARDS_BODY[-60:])
CARDS_REST = CARDS_BODY[:-len(CARDS_CLOSE)]  # کارت‌های ۲..۶
n_cards = CARDS_REST.count('<div class="rounded-xl border border-emerald-100 bg-white p-3.5">')
assert n_cards == 5, n_cards  # کارت اول داخل CARD است

# ─── راستی‌آزمایی بازسازی SPECS ───
TITLE = 'والواشر RGB و تک‌رنگ صنعتی (IP68)'
ROWS_DATA = [
    ('نوع LED', 'پیکسل RGB پرقدرت فول‌کالر / پاور LED'),
    ('تنوع رنگ', 'بیش از ۱۶ میلیون رنگ و تک‌رنگ'),
    ('مصرف انرژی', '۳۶ وات در هر متر'),
    ('بدنه', 'آلومینیوم اکسترود صنعتی + شیشه سکوریت'),
    ('درجه حفاظت', 'IP68'),
    ('گارانتی', '۲۴ ماه گارانتی + ۵ سال خدمات پس از فروش'),
    ('توان مصرفی', '۳۶ وات'),
    ('ولتاژ ورودی', '۲۴V DC / ۲۲۰V AC'),
    ('جنس بدنه', 'آلومینیوم اکسترود صنعتی'),
]
CARDS_DATA = [
    ('شناسه فنی محصول در کارخانه', 'ESE-WW-036', 'text-slate-900'),
    ('کد بین‌الملل GS1 (GTIN)', '6260491807290', 'text-emerald-800'),
    ('کد ملی کالا (ایران‌کد)', '2901140800601', 'text-slate-900'),
    ('کد تعرفه هماهنگ گمرکی (HS Code)', '94054290', 'text-slate-900'),
    ('کد آیسیک صنعتی (ISIC)', '2740', 'text-slate-900'),
    ('کد طبقه‌بندی مرکزی محصول (CPC)', '46531', 'text-slate-900'),
]
reb = SPECS[:t0] + ROWS_OPEN
for i, (lbl, val) in enumerate(ROWS_DATA):
    bg = 'bg-slate-50/70' if i % 2 == 0 else 'bg-white'
    reb += ROW.replace('{ES_BG}', bg).replace('{ES_LBL}', lbl).replace('{ES_VAL}', val)
reb += ROWS_CLOSE + SPECS[lg:c0]
for lbl, val, cls in CARDS_DATA:
    reb += CARD.replace('{ES_LBL}', lbl).replace('{ES_VAL}', val).replace('{ES_VAL_CLS}', cls)
reb += CARDS_CLOSE
assert reb == SPECS, f'specs rebuild mismatch: {len(reb)} vs {len(SPECS)}'
# تطبیق کارت‌های ۲..۶
reb_rest = ''.join(CARD.replace('{ES_LBL}', l).replace('{ES_VAL}', v).replace('{ES_VAL_CLS}', c) for l, v, c in CARDS_DATA[1:])
assert reb_rest == CARDS_REST, 'cards 2..6 mismatch'

# بازسازی هدر (با دی‌توکن‌سازی به مقادیر میرور)
CLS_ACTIVE = 'bg-emerald-800 text-white shadow-sm'
CLS_IDLE = 'text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900'
det_btn_desc = BTN_DESC.replace('{ES_LABEL}', 'معرفی و کاربردها').replace('{ES_BTN_CLS}', CLS_ACTIVE).replace('{ES_ICON_CLS}', 'text-white')
det_btn_specs = BTN_SPECS.replace('{ES_LABEL}', 'مشخصات فنی و شناسه\u200cها').replace('{ES_BTN_CLS}', CLS_IDLE).replace('{ES_ICON_CLS}', 'text-emerald-700')
det_btn_reviews = BTN_REVIEWS.replace('{ES_REV_COUNT}', '۳').replace('{ES_BTN_CLS}', CLS_IDLE).replace('{ES_ICON_CLS}', 'text-emerald-700')
reb_h = HEAD_A + det_btn_desc + det_btn_specs + det_btn_reviews + HEAD_B
assert reb_h == s[sec:desc_panel], 'header rebuild mismatch'

frags = nowdoc('ES_HEAD_A', HEAD_A, 'ESP63A') + nowdoc('ES_BTN_DESC', BTN_DESC, 'ESP63B') \
      + nowdoc('ES_BTN_SPECS', BTN_SPECS, 'ESP63C') + nowdoc('ES_BTN_REVIEWS', BTN_REVIEWS, 'ESP63D') \
      + nowdoc('ES_HEAD_B', HEAD_B, 'ESP63E') + nowdoc('ES_SPECS_HEAD', SPECS[:r0], 'ESP63F') \
      + nowdoc('ES_ROW', ROW, 'ESP63G') + nowdoc('ES_MID', SPECS[lg:c0], 'ESP63H') \
      + nowdoc('ES_CARD', CARD, 'ESP63I') + nowdoc('ES_SPECS_TAIL', CARDS_CLOSE, 'ESP63J')

open(os.path.join(ROOT, 'template-parts/single-product/specs-tabs.php'), 'w', encoding='utf-8').write(
"<?php\n"
"/**\n"
" * تب‌های محصول + جدول مشخصات pa_* + شناسه‌های قانونی (برش بایت‌دقیق میرور product/wall-washer.html)\n"
" * فرگمنت با temp/gen_p63.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n"
"$es_title = get_the_title($es_pid);\n\n"
"// ردیف‌های مشخصات از اتریبیوت‌های ووکامرس (_product_attributes) — خالی‌ها حذف\n"
"$es_rows = [];\n"
"$es_attrs_raw = get_post_meta($es_pid, '_product_attributes', true);\n"
"if (is_array($es_attrs_raw)) {\n"
"    foreach ($es_attrs_raw as $es_key => $es_def) {\n"
"        if (!is_array($es_def)) {\n"
"            continue;\n"
"        }\n"
"        $es_lbl = (string) ($es_def['label'] ?? '');\n"
"        if ($es_lbl === '') {\n"
"            $es_lbl = (string) $es_key;\n"
"        }\n"
"        if (!empty($es_def['is_taxonomy'])) {\n"
"            $es_terms = get_the_terms($es_pid, (string) $es_key);\n"
"            $es_vals = is_array($es_terms) ? array_map(static fn($t): string => (string) ($t->name ?? ''), $es_terms) : [];\n"
"            $es_vals = array_values(array_filter($es_vals, static fn(string $v): bool => $v !== ''));\n"
"            $es_val = $es_vals === [] ? '' : implode('، ', $es_vals);\n"
"        } else {\n"
"            $es_val = trim((string) ($es_def['value'] ?? ''));\n"
"        }\n"
"        if ($es_val !== '') {\n"
"            $es_rows[] = ['label' => $es_lbl, 'value' => $es_val];\n"
"        }\n"
"    }\n"
"}\n\n"
"// شناسه‌های قانونی (فقط مقدار-دارها به ترتیب میرور)\n"
"$es_legal = [];\n"
"foreach ([\n"
"    ['product_id', 'شناسه فنی محصول در کارخانه', 'text-slate-900'],\n"
"    ['gs1_id', 'کد بین‌الملل GS1 (GTIN)', 'text-emerald-800'],\n"
"    ['national_code', 'کد ملی کالا (ایران‌کد)', 'text-slate-900'],\n"
"    ['hs_id', 'کد تعرفه هماهنگ گمرکی (HS Code)', 'text-slate-900'],\n"
"    ['isic_id', 'کد آیسیک صنعتی (ISIC)', 'text-slate-900'],\n"
"    ['cpc_id', 'کد طبقه‌بندی مرکزی محصول (CPC)', 'text-slate-900'],\n"
"] as $es_lg) {\n"
"    $es_lv = trim((string) get_post_meta($es_pid, $es_lg[0], true));\n"
"    if ($es_lv !== '') {\n"
"        $es_legal[] = ['label' => $es_lg[1], 'value' => $es_lv, 'cls' => $es_lg[2]];\n"
"    }\n"
"}\n\n"
"$es_rev_count = (int) get_comments_number($es_pid);\n"
"$es_data = apply_filters('es_product_specs_args', [\n"
"    'rows'        => $es_rows,\n"
"    'legal'       => $es_legal,\n"
"    'has_desc'    => false,\n"
"    'has_reviews' => false,\n"
"    'rev_count'   => $es_rev_count,\n"
"], $es_pid);\n"
"$es_rows   = (array) ($es_data['rows'] ?? []);\n"
"$es_legal  = (array) ($es_data['legal'] ?? []);\n"
"$es_has_desc = (bool) ($es_data['has_desc'] ?? false);\n"
"$es_has_reviews = (bool) ($es_data['has_reviews'] ?? false);\n"
"$es_rev_count = (int) ($es_data['rev_count'] ?? 0);\n\n"
"// فقط تب‌های دارای محتوا؛ فعال = اولین تب رندرشده\n"
"$es_tabs = [];\n"
"if ($es_has_desc) {\n"
"    $es_tabs[] = 'desc';\n"
"}\n"
"if ($es_rows !== [] || $es_legal !== []) {\n"
"    $es_tabs[] = 'specs';\n"
"}\n"
"if ($es_has_reviews && $es_rev_count > 0) {\n"
"    $es_tabs[] = 'reviews';\n"
"}\n"
"if ($es_tabs === []) {\n"
"    return;\n"
"}\n"
"$es_cls_active = 'bg-emerald-800 text-white shadow-sm';\n"
"$es_cls_idle = 'text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900';\n"
"$es_icon_active = 'text-white';\n"
"$es_icon_idle = 'text-emerald-700';\n\n"
+ frags +
"\necho $ES_HEAD_A;\n"
"$es_first_done = false;\n"
"foreach ($es_tabs as $es_tab) {\n"
"    $es_active = !$es_first_done;\n"
"    $es_first_done = true;\n"
"    $es_bcls = $es_active ? $es_cls_active : $es_cls_idle;\n"
"    $es_icls = $es_active ? $es_icon_active : $es_icon_idle;\n"
"    if ($es_tab === 'desc') {\n"
"        echo strtr($ES_BTN_DESC, ['{ES_LABEL}' => 'معرفی و کاربردها', '{ES_BTN_CLS}' => $es_bcls, '{ES_ICON_CLS}' => $es_icls]);\n"
"    } elseif ($es_tab === 'specs') {\n"
"        echo strtr($ES_BTN_SPECS, ['{ES_LABEL}' => 'مشخصات فنی و شناسه\\u200cها', '{ES_BTN_CLS}' => $es_bcls, '{ES_ICON_CLS}' => $es_icls]);\n"
"    } else {\n"
"        echo strtr($ES_BTN_REVIEWS, ['{ES_REV_COUNT}' => es_fa_num((string) $es_rev_count), '{ES_BTN_CLS}' => $es_bcls, '{ES_ICON_CLS}' => $es_icls]);\n"
"    }\n"
"}\n"
"echo $ES_HEAD_B;\n"
"if (in_array('specs', $es_tabs, true)) {\n"
"    echo strtr($ES_SPECS_HEAD, ['{ES_TITLE}' => esc_html($es_title)]);\n"
"    foreach ($es_rows as $es_ri => $es_row) {\n"
"        echo strtr($ES_ROW, ['{ES_BG}' => $es_ri % 2 === 0 ? 'bg-slate-50/70' : 'bg-white', '{ES_LBL}' => esc_html((string) $es_row['label']), '{ES_VAL}' => esc_html((string) $es_row['value'])]);\n"
"    }\n"
"    echo '</div></div>';\n"
"    echo $ES_MID;\n"
"    foreach ($es_legal as $es_lgc) {\n"
"        echo strtr($ES_CARD, ['{ES_LBL}' => esc_html((string) $es_lgc['label']), '{ES_VAL}' => esc_html((string) $es_lgc['value']), '{ES_VAL_CLS}' => (string) $es_lgc['cls']]);\n"
"    }\n"
"    echo $ES_SPECS_TAIL;\n"
"}\n"
"echo '</div></div></section>';\n")

print('specs-tabs.php:', os.path.getsize(os.path.join(ROOT, 'template-parts/single-product/specs-tabs.php')))
