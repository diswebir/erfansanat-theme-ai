#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۶.۴ — پنل desc (features/feature2/notice/usages) + FAQ + کاتالوگ (برش بایت‌دقیق wall-washer/baloti).
اجرا: python3 temp/gen_p64.py
"""
import os

ROOT = '/home/user/erfansanat-theme-ai'
WW = os.path.join(ROOT, 'uploads/static-mirror/product/wall-washer.html')
BL = os.path.join(ROOT, 'uploads/static-mirror/product/baloti-string.html')

def nowdoc(name, body, tag):
    return f"${name} = <<<'{tag}'\n{body}\n{tag};\n"

s = open(WW, encoding='utf-8').read()
dp = s.find('<div data-prod-tab-panel="desc"')
sp = s.find('<div data-prod-tab-panel="specs"')
DESC = s[dp:sp]

TITLE = 'والواشر RGB و تک‌رنگ صنعتی (IP68)'

# ── DESC_HEAD: تا «مزایا و ویژگی‌های برجسته» ──
f_anchor = '<div class="mb-4 flex items-center gap-2 text-sm font-extrabold text-slate-900">'
f0 = DESC.find(f_anchor)
assert f0 > 0
DESC_HEAD = DESC[:f0]
DESC_HEAD = DESC_HEAD.replace('>معرفی تخصصی ' + TITLE + '</h2>', '>معرفی تخصصی {ES_TITLE}</h2>', 1)
DESC_HEAD = DESC_HEAD.replace('<span dir="ltr" class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-500">Industrial RGB &amp; Mono LED Wall Washer IP68</span>', '{ES_EN_HTML}', 1)
c0 = DESC_HEAD.find('<div class="mt-4 space-y-4 text-[14.5px] leading-9 text-slate-600">')
c1 = DESC_HEAD.find('</div>', DESC_HEAD.find('</p>', c0)) + len('</div>')
CONTENT_RAW = DESC_HEAD[c0:c1]
DESC_HEAD = DESC_HEAD[:c0] + '{ES_CONTENT}' + DESC_HEAD[c1:]
assert '{ES_TITLE}' in DESC_HEAD and '{ES_EN_HTML}' in DESC_HEAD and '{ES_CONTENT}' in DESC_HEAD

# ── FEAT_HEAD + FEAT_ROW ──
grid_anchor = '<div class="grid gap-3 sm:grid-cols-2">'
g0 = DESC.find(grid_anchor, f0)
FEAT_HEAD = DESC[f0:g0 + len(grid_anchor)]
r0 = DESC.find('<div class="flex items-start gap-2.5 rounded-xl border border-slate-200/60 bg-white px-3.5 py-2.5 text-[13px] font-semibold leading-6 text-slate-700">', g0)
r1 = DESC.find('</div>', DESC.find('</span>', r0)) + len('</div>')
FEAT_ROW = DESC[r0:r1]
FEAT_ROW = FEAT_ROW.replace('<span>ابعاد سفارشی بر اساس پروژه</span>', '<span>{ES_TEXT}</span>', 1)
assert FEAT_ROW.count('<div') == 1
# بعد از آخرین feature: بستن گرید + بستن کارت + آمبر NOTICE
n0 = DESC.find('<div class="rounded-2xl border border-amber-200/90 bg-amber-50/70 p-5">', r0)
mid_raw = DESC[r1:n0]
n_rest = mid_raw.count('<div class="flex items-start gap-2.5 rounded-xl border border-slate-200/60')
assert n_rest == 3, n_rest
assert mid_raw.endswith('</div></div>'), repr(mid_raw[-30:])
FEAT_MID = '</div></div>'  # بستن گرید + بستن کارت (سه ردیف باقیمانده با ROW×۳ بازسازی می‌شوند)
# NOTICE (آمبر) تا پایان
n_end = DESC.find('</div>', DESC.find('text-amber-900/90', n0))
n_end = DESC.find('</div>', n_end) + len('</div>')
NOTICE = DESC[n0:n_end]
NOTICE = NOTICE.replace('زاویه تابش لنز (۱۵، ۳۰، ۴۵ یا ۶۰ درجه) را متناسب با ارتفاع ستون یا نمای ساختمان در هنگام سفارش مشخص نمایید.', '{ES_TEXT}', 1)
assert '{ES_TEXT}' in NOTICE
DESC_MID = DESC[n_end:n_end + len('</div><div class="space-y-6 lg:col-span-5">')]
assert DESC_MID == '</div><div class="space-y-6 lg:col-span-5">', repr(DESC_MID)

# ── USE_HEAD (chips card) ──
ch0 = DESC.find('<div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-6">', n_end)
cg0 = DESC.find('<div class="flex flex-wrap gap-2">', ch0)
USE_HEAD = DESC[ch0:cg0 + len('<div class="flex flex-wrap gap-2">')]
chip0 = DESC.find('<span class="rounded-xl border border-emerald-200/80 bg-white px-3.5 py-2 text-xs font-bold text-emerald-950 shadow-2xs">', cg0)
chipe = DESC.find('</span>', chip0) + len('</span>')
USE_CHIP = DESC[chip0:chipe]
USE_CHIP = USE_CHIP.replace('>نمای ساختمان‌های مدرن و تاریخی</span>', '>{ES_TEXT}</span>', 1)
assert USE_CHIP.count('<span') == 1
chip1 = DESC.find('<span class="rounded-xl border border-emerald-200/80', chipe)
# ── USE_MID: بقیه چیپ‌ها + بستن + لینک کارت ──
lg_anchor = '<div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-6">'
lg0 = DESC.find(lg_anchor, chipe)
USE_MID = DESC[chipe:lg0]
n_chips_rest = USE_MID.count('<span class="rounded-xl border border-emerald-200/80')
assert n_chips_rest == 2, n_chips_rest
import re as _re
USE_MID = _re.sub(r'<span class="rounded-xl border border-emerald-200/80 bg-white px-3\.5 py-2 text-xs font-bold text-emerald-950 shadow-2xs">[^<]*</span>', '', USE_MID)
assert '<span class="rounded-xl' not in USE_MID
lggrid = DESC.find('<div class="flex flex-wrap gap-2">', lg0)
USE_MID += DESC[lg0:lggrid + len('<div class="flex flex-wrap gap-2">')]
# ── USE_LINK ──
a0 = DESC.find('<a href="../project_type/lighting-building.html"', lggrid)
a1 = DESC.find('</a>', a0) + len('</a>')
USE_LINK = DESC[a0:a1]
USE_LINK = USE_LINK.replace('href="../project_type/lighting-building.html"', 'href="{ES_URL}"', 1)
USE_LINK = USE_LINK.replace('<span>پروژه‌های نورپردازی ساختمان</span>', '<span>{ES_NAME}</span>', 1)
assert '{ES_URL}' in USE_LINK and '{ES_NAME}' in USE_LINK
a2 = DESC.find('<a href="../project_type/', a1)
# ── USE_TAIL: لینک‌های باقیمانده + ۵ بستن ──
USE_TAIL = DESC[a1:]
n_links_rest = USE_TAIL.count('<a href="../project_type/')
assert n_links_rest == 2, n_links_rest
import re as _re2
USE_TAIL = _re2.sub(r'<a href="\.\./project_type/[^"]*"[^>]*>.*?</a>', '', USE_TAIL, flags=_re2.S)
assert '<a href="../project_type/' not in USE_TAIL
assert USE_TAIL == '</div></div></div></div></div>', repr(USE_TAIL)

# ── بازسازی DESC ──
EN_BADGE = '<span dir="ltr" class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-500">Industrial RGB &amp; Mono LED Wall Washer IP68</span>'
CONTENT = '<div class="mt-4 space-y-4 text-[14.5px] leading-9 text-slate-600"><p>والواشر RGB عرفان صنعت با تولید ۱۶ میلیون رنگ، امکان ایجاد جلوه‌های نوری پویا و انیمیشن‌های خلاقانه را فراهم می‌کند. در پروژه‌های عرفان صنعت از والواشر برای نورپردازی ستون‌ها، پنجره‌ها، پایه‌های پل و لبه‌های ساختمان استفاده شده است.</p><p>با وجود روشنایی استثنایی، این محصول تنها ۳۶ وات انرژی مصرف می‌کند و با بدنه آلومینیومی بادوام و گواهی IP68، ضدآب، ضدغبار و ضدضربه است.</p></div>'
reb = (DESC_HEAD.replace('{ES_TITLE}', TITLE).replace('{ES_EN_HTML}', EN_BADGE).replace('{ES_CONTENT}', CONTENT)
       + FEAT_HEAD)
for t in ['ابعاد سفارشی بر اساس پروژه', 'پخش نور یکنواخت با لنز اپتیکی', 'مصرف بهینه انرژی', 'پوشش محافظ پیشرفته IP68']:
    reb += FEAT_ROW.replace('{ES_TEXT}', t)
reb += FEAT_MID + NOTICE.replace('{ES_TEXT}', 'زاویه تابش لنز (۱۵، ۳۰، ۴۵ یا ۶۰ درجه) را متناسب با ارتفاع ستون یا نمای ساختمان در هنگام سفارش مشخص نمایید.')
reb += DESC_MID + USE_HEAD
for t in ['نمای ساختمان‌های مدرن و تاریخی', 'پایه‌ها و عرشه پل', 'ستون‌ها و لبه‌های معماری']:
    reb += USE_CHIP.replace('{ES_TEXT}', t)
reb += USE_MID
for url, name in [('../project_type/lighting-building.html', 'پروژه‌های نورپردازی ساختمان'),
                  ('../project_type/bridge-lighting.html', 'پروژه‌های نورپردازی پل'),
                  ('../project_type/square-lighting.html', 'پروژه‌های نورپردازی میدان')]:
    reb += USE_LINK.replace('{ES_URL}', url).replace('{ES_NAME}', name)
reb += USE_TAIL
assert reb == DESC, f'desc rebuild mismatch: {len(reb)} vs {len(DESC)}'

# ── FAQ ──
fs = s.find('<section class="mx-auto max-w-4xl px-5 pb-14 lg:px-8">')
fe = s.find('</section>', fs) + len('</section>')
FAQ = s[fs:fe]
FAQ = FAQ.replace('<h2 class="text-lg font-extrabold text-slate-900">سوالات متداول درباره ' + TITLE + '</h2>',
                  '<h2 class="text-lg font-extrabold text-slate-900">سوالات متداول درباره {ES_TITLE}</h2>', 1)
q0 = FAQ.find('<div class="py-3.5 first:pt-0 last:pb-0">')
q0e = FAQ.find('</div>', FAQ.find('</p>', q0)) + len('</div></div>')  # بستن body + بستن ردیف
FAQ_ROW_OPEN = FAQ[q0:q0e]
FAQ_ROW_OPEN = FAQ_ROW_OPEN.replace('data-faq-toggle="0"', 'data-faq-toggle="{ES_I}"', 1)
FAQ_ROW_OPEN = FAQ_ROW_OPEN.replace('<span class="text-sm font-extrabold text-slate-800">شرایط گارانتی و خدمات پس از فروش این محصول چگونه است؟</span>',
                                    '<span class="text-sm font-extrabold text-slate-800">{ES_Q}</span>', 1)
FAQ_ROW_OPEN = FAQ_ROW_OPEN.replace('data-faq-icon="0"', 'data-faq-icon="{ES_I}"', 1)
FAQ_ROW_OPEN = FAQ_ROW_OPEN.replace('data-faq-body="0"', 'data-faq-body="{ES_I}"', 1)
FAQ_ROW_OPEN = FAQ_ROW_OPEN.replace('<p class="text-[13.5px] leading-7 text-slate-600">تمامی محصولات عرفان صنعت دارای ۲۴ ماه گارانتی تعویض بی‌قید و شرط و ۵ سال خدمات پس از فروش و تامین قطعات اصلی در سراسر کشور هستند.</p>',
                                    '<p class="text-[13.5px] leading-7 text-slate-600">{ES_A}</p>', 1)
assert all(t in FAQ_ROW_OPEN for t in ('{ES_I}', '{ES_Q}', '{ES_A}'))
q1 = FAQ.find('<div class="py-3.5 first:pt-0 last:pb-0">', q0e)
q1e = FAQ.find('</div>', FAQ.find('</p>', q1)) + len('</div></div>')
FAQ_ROW_IDLE = FAQ[q1:q1e]
FAQ_ROW_IDLE = FAQ_ROW_IDLE.replace('data-faq-toggle="1"', 'data-faq-toggle="{ES_I}"', 1)
FAQ_ROW_IDLE = FAQ_ROW_IDLE.replace('<span class="text-sm font-extrabold text-slate-800">آیا امکان تولید در ابعاد، متراژ یا رنگ‌بندی سفارشی وجود دارد؟</span>',
                                    '<span class="text-sm font-extrabold text-slate-800">{ES_Q}</span>', 1)
FAQ_ROW_IDLE = FAQ_ROW_IDLE.replace('data-faq-icon="1"', 'data-faq-icon="{ES_I}"', 1)
FAQ_ROW_IDLE = FAQ_ROW_IDLE.replace('data-faq-body="1"', 'data-faq-body="{ES_I}"', 1)
FAQ_ROW_IDLE = FAQ_ROW_IDLE.replace('<p class="text-[13.5px] leading-7 text-slate-600">بله؛ از آنجا که عرفان صنعت تولیدکننده مستقیم است، امکان سفارشی‌سازی کامل متراژ، تعداد شعله، ارتفاع سازه و رنگ نور بر اساس نیاز پروژه شما فراهم است.</p>',
                                    '<p class="text-[13.5px] leading-7 text-slate-600">{ES_A}</p>', 1)
assert all(t in FAQ_ROW_IDLE for t in ('{ES_I}', '{ES_Q}', '{ES_A}'))
FAQ_HEAD = FAQ[:q0]
FAQ_TAIL = FAQ[q1e:]
n_idle_rest = FAQ_TAIL.count('<div class="py-3.5 first:pt-0 last:pb-0">')
assert n_idle_rest == 2, n_idle_rest
import re as _re3
FAQ_TAIL = _re3.sub(r'<div class="py-3\.5 first:pt-0 last:pb-0">.*?</p></div></div>', '', FAQ_TAIL, flags=_re3.S)
assert FAQ_TAIL == '</div></div></section>', repr(FAQ_TAIL)  # بستن faq-list + کارت + سکشن (آیتم‌ها کامل در ROW هستند)
# بازسازی FAQ با ۴ سوال میرور
QA = [('شرایط گارانتی و خدمات پس از فروش این محصول چگونه است؟', 'تمامی محصولات عرفان صنعت دارای ۲۴ ماه گارانتی تعویض بی‌قید و شرط و ۵ سال خدمات پس از فروش و تامین قطعات اصلی در سراسر کشور هستند.'),
      ('آیا امکان تولید در ابعاد، متراژ یا رنگ‌بندی سفارشی وجود دارد؟', 'بله؛ از آنجا که عرفان صنعت تولیدکننده مستقیم است، امکان سفارشی‌سازی کامل متراژ، تعداد شعله، ارتفاع سازه و رنگ نور بر اساس نیاز پروژه شما فراهم است.'),
      ('نحوه ارسال به سایر شهرها و زمان تحویل چقدر است؟', 'ارسال به سراسر ایران از طریق باربری تخصصی و بیمه کامل بار انجام می‌شود. محصولات موجود در انبار ظرف ۲۴ تا ۴۸ ساعت و سفارش‌های اختصاصی طبق زمان‌بندی قرارداد ارسال می‌گردند.'),
      ('آیا نصب محصول نیاز به تیم تخصصی دارد؟', 'محصولاتی مانند ریسه‌ها به سادگی و با راهنمای فارسی قابل نصب هستند. برای المان‌های بزرگ شهری و پروژه‌های پیکسلی، تیم اجرایی عرفان صنعت آماده نصب و راه‌اندازی در محل پروژه شماست.')]
reb_f = FAQ_HEAD
for i, (qq, aa) in enumerate(QA):
    tpl = FAQ_ROW_OPEN if i == 0 else FAQ_ROW_IDLE
    reb_f += tpl.replace('{ES_I}', str(i)).replace('{ES_Q}', qq).replace('{ES_A}', aa)
reb_f += FAQ_TAIL
assert reb_f == FAQ, f'faq rebuild mismatch: {len(reb_f)} vs {len(FAQ)}'

# ── نوشتن فایل‌ها ──
F_FRAGS = nowdoc('ES_DESC_HEAD', DESC_HEAD, 'ESP64A') + nowdoc('ES_FEAT_HEAD', FEAT_HEAD, 'ESP64B') \
        + nowdoc('ES_FEAT_ROW', FEAT_ROW, 'ESP64C') + nowdoc('ES_FEAT_MID', FEAT_MID, 'ESP64D') \
        + nowdoc('ES_DESC_MID', DESC_MID, 'ESP64E')
U_FRAGS = nowdoc('ES_USE_HEAD', USE_HEAD, 'ESP64F') + nowdoc('ES_USE_CHIP', USE_CHIP, 'ESP64G') \
        + nowdoc('ES_USE_MID', USE_MID, 'ESP64H') + nowdoc('ES_USE_LINK', USE_LINK, 'ESP64I') \
        + nowdoc('ES_USE_TAIL', USE_TAIL, 'ESP64J')
N_FRAGS = nowdoc('ES_NOTICE', NOTICE, 'ESP64K')
Q_FRAGS = nowdoc('ES_FAQ_HEAD', FAQ_HEAD, 'ESP64L') + nowdoc('ES_FAQ_OPEN', FAQ_ROW_OPEN, 'ESP64M') \
        + nowdoc('ES_FAQ_IDLE', FAQ_ROW_IDLE, 'ESP64N') + nowdoc('ES_FAQ_TAIL', FAQ_TAIL, 'ESP64O')
W = lambda rel, txt: open(os.path.join(ROOT, rel), 'w', encoding='utf-8').write(txt)

W('template-parts/single-product/usages.php',
"<?php\n"
"/**\n"
" * کاربردهای محصول — چیپ‌های usage_product + لینک آرشیو ترم‌های usage_countr (ادغام dedup؛ برش بایت‌دقیق میرور)\n"
" * فرگمنت با temp/gen_p64.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"use ErfanSanat\\Theme\\Data\\RepeaterEngine;\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n\n"
"$es_terms = [];\n"
"foreach (RepeaterEngine::read($es_pid, 'usage_countr')['rows'] as $es_row) {\n"
"    $es_tid = (int) ($es_row['id'] ?? 0);\n"
"    if ($es_tid < 1) {\n"
"        continue;\n"
"    }\n"
"    $es_term = get_term($es_tid, 'project_type');\n"
"    if (is_wp_error($es_term)) {\n"
"        continue;\n"
"    }\n"
"    $es_name = (string) ($es_term->name ?? '');\n"
"    if ($es_name === '') {\n"
"        continue;\n"
"    }\n"
"    $es_link = function_exists('get_term_link') ? get_term_link($es_term) : '';\n"
"    $es_terms[] = ['name' => $es_name, 'url' => is_string($es_link) ? $es_link : ''];\n"
"}\n"
"$es_term_names = array_column($es_terms, 'name');\n\n"
"$es_chips = [];\n"
"foreach (RepeaterEngine::read($es_pid, 'usage_product')['rows'] as $es_row) {\n"
"    $es_text = trim((string) ($es_row['add-new-usage'] ?? ''));\n"
"    if ($es_text === '' || in_array($es_text, $es_term_names, true)) {\n"
"        continue; // ادغام: ترم‌های همنام فقط یک‌بار به‌صورت لینک‌دار\n"
"    }\n"
"    $es_chips[] = $es_text;\n"
"}\n"
"if ($es_chips === [] && $es_terms === []) {\n"
"    return;\n"
"}\n\n"
+ U_FRAGS +
"\necho $ES_USE_HEAD;\n"
"foreach ($es_chips as $es_text) {\n"
"    echo strtr($ES_USE_CHIP, ['{ES_TEXT}' => esc_html($es_text)]);\n"
"}\n"
"echo $ES_USE_MID;\n"
"foreach ($es_terms as $es_t) {\n"
"    echo strtr($ES_USE_LINK, ['{ES_URL}' => esc_url((string) $es_t['url']), '{ES_NAME}' => esc_html((string) $es_t['name'])]);\n"
"}\n"
"echo $ES_USE_TAIL;\n")

W('template-parts/single-product/notice.php',
"<?php\n"
"/**\n"
" * نوار اطلاعیه قبل از خرید (before-sale-c) — جعبه آمبر میرور؛ مخفی اگر خالی\n"
" * فرگمنت با temp/gen_p64.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n"
"$es_notice = trim((string) ($args['text'] ?? get_post_meta($es_pid, 'before-sale-c', true)));\n"
"if ($es_notice === '') {\n"
"    return;\n"
"}\n\n"
+ N_FRAGS +
"\necho strtr($ES_NOTICE, ['{ES_TEXT}' => esc_html($es_notice)]);\n")

W('template-parts/single-product/features.php',
"<?php\n"
"/**\n"
" * پنل desc محصول — معرفی + ویژگی‌ها (product-feature) + ویژگی دو بخشی (product-feature2_copy)\n"
" * + اطلاعیه before-sale-c + کاربردها؛ برش بایت‌دقیق میرور product/wall-washer.html\n"
" * فرگمنت با temp/gen_p64.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"use ErfanSanat\\Theme\\Data\\RepeaterEngine;\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n"
"$es_title = get_the_title($es_pid);\n"
"$es_content = (string) get_post_field('post_content', $es_pid);\n"
"$es_en = (string) get_post_meta($es_pid, 'english_titl', true);\n"
"$es_en_html = $es_en !== '' ? '<span dir=\"ltr\" class=\"rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-500\">' . esc_html($es_en) . '</span>' : '';\n\n"
"$es_features = [];\n"
"foreach (RepeaterEngine::read($es_pid, 'product-feature')['rows'] as $es_row) {\n"
"    $es_text = trim((string) ($es_row['add-new-feature'] ?? ''));\n"
"    if ($es_text !== '') {\n"
"        $es_features[] = $es_text;\n"
"    }\n"
"}\n"
"$es_f2 = [];\n"
"foreach (RepeaterEngine::read($es_pid, 'product-feature2_copy')['rows'] as $es_row) {\n"
"    $es_ft = trim((string) ($es_row['add-new-feature'] ?? ''));\n"
"    $es_fv = trim((string) ($es_row['vaule_of_variable'] ?? ''));\n"
"    if ($es_ft !== '' && $es_fv !== '') {\n"
"        $es_f2[] = ['t' => $es_ft, 'v' => $es_fv];\n"
"    }\n"
"}\n"
"$es_notice = trim((string) get_post_meta($es_pid, 'before-sale-c', true));\n\n"
"$es_data = apply_filters('es_product_features_args', [\n"
"    'content'   => $es_content,\n"
"    'features'  => $es_features,\n"
"    'feature2'  => $es_f2,\n"
"    'notice'    => $es_notice,\n"
"], $es_pid);\n"
"$es_content  = (string) ($es_data['content'] ?? '');\n"
"$es_features = (array) ($es_data['features'] ?? []);\n"
"$es_f2       = (array) ($es_data['feature2'] ?? []);\n"
"$es_notice   = (string) ($es_data['notice'] ?? '');\n"
"if ($es_content === '' && $es_features === [] && $es_f2 === [] && $es_notice === '') {\n"
"    return;\n"
"}\n\n"
+ F_FRAGS +
"\necho strtr($ES_DESC_HEAD, ['{ES_TITLE}' => esc_html($es_title), '{ES_EN_HTML}' => $es_en_html, '{ES_CONTENT}' => $es_content]);\n"
"if ($es_features !== []) {\n"
"    echo $ES_FEAT_HEAD;\n"
"    foreach ($es_features as $es_text) {\n"
"        echo strtr($ES_FEAT_ROW, ['{ES_TEXT}' => esc_html($es_text)]);\n"
"    }\n"
"    echo $ES_FEAT_MID;\n"
"}\n"
"if ($es_f2 !== []) {\n"
"    // ویژگی کلیدی دو بخشی — رخداد میرور خالی است؛ الگو از کارت‌های خواهر میرور (کارت چکیده خرید) طراحی شد\n"
"    echo '<div class=\"rounded-2xl border border-slate-200/80 bg-slate-50/60 p-5 sm:p-6\"><div class=\"mb-4 flex items-center gap-2 text-sm font-extrabold text-slate-900\"><span>مشخصات کلیدی در یک نگاه</span></div><div class=\"grid gap-2.5 sm:grid-cols-2\">';\n"
"    foreach ($es_f2 as $es_pair) {\n"
"        echo '<div class=\"rounded-xl border border-slate-200/60 bg-white px-3.5 py-2.5\"><div class=\"text-[11px] font-bold text-slate-500\">' . esc_html((string) $es_pair['t']) . '</div><div class=\"mt-0.5 text-[13px] font-extrabold text-slate-800\">' . esc_html((string) $es_pair['v']) . '</div></div>';\n"
"    }\n"
"    echo '</div></div>';\n"
"}\n"
"es_get_template('template-parts/single-product/notice.php', ['pid' => $es_pid, 'text' => $es_notice]);\n"
"echo $ES_DESC_MID;\n"
"es_get_template('template-parts/single-product/usages.php', ['pid' => $es_pid]);\n")

W('template-parts/single-product/faq.php',
"<?php\n"
"/**\n"
" * سوالات متداول محصول — آکاردئون data-faq-* میرور (برش بایت‌دقیق product/wall-washer.html)\n"
" * فرگمنت با temp/gen_p64.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"use ErfanSanat\\Theme\\Data\\RepeaterEngine;\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n"
"$es_title = get_the_title($es_pid);\n"
"$es_rows = [];\n"
"foreach (RepeaterEngine::read($es_pid, 'faq')['rows'] as $es_row) {\n"
"    $es_q = trim((string) ($es_row['name_question'] ?? ''));\n"
"    $es_a = trim((string) ($es_row['answer_question'] ?? ''));\n"
"    if ($es_q !== '' && $es_a !== '') {\n"
"        $es_rows[] = ['q' => $es_q, 'a' => $es_a];\n"
"    }\n"
"}\n"
"$es_rows = apply_filters('es_product_faq_rows', $es_rows, $es_pid);\n"
"if ($es_rows === []) {\n"
"    return;\n"
"}\n\n"
+ Q_FRAGS +
"\necho strtr($ES_FAQ_HEAD, ['{ES_TITLE}' => esc_html($es_title)]);\n"
"foreach ($es_rows as $es_i => $es_row) {\n"
"    $es_tpl = $es_i === 0 ? $ES_FAQ_OPEN : $ES_FAQ_IDLE;\n"
"    echo strtr($es_tpl, ['{ES_I}' => (string) $es_i, '{ES_Q}' => esc_html((string) $es_row['q']), '{ES_A}' => esc_html((string) $es_row['a'])]);\n"
"}\n"
"echo $ES_FAQ_TAIL;\n")

print('features.php:', os.path.getsize(os.path.join(ROOT, 'template-parts/single-product/features.php')))
print('usages.php:', os.path.getsize(os.path.join(ROOT, 'template-parts/single-product/usages.php')))
print('faq.php:', os.path.getsize(os.path.join(ROOT, 'template-parts/single-product/faq.php')))
print('notice.php:', os.path.getsize(os.path.join(ROOT, 'template-parts/single-product/notice.php')))
