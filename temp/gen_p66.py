#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۶.۶ — پروژه‌های مرتبط، محصولات مرتبط و دیدگاه‌ها (برش بایت‌دقیق wall-washer).
اجرا: python3 temp/gen_p66.py
"""
import os

ROOT = '/home/user/erfansanat-theme-ai'
SRC = os.path.join(ROOT, 'uploads/static-mirror/product/wall-washer.html')

def nowdoc(name, body, tag):
    return "$" + name + " = <<<'{t}'\n{b}\n{t};\n".format(t=tag, b=body)

s = open(SRC, encoding='utf-8').read()

# ═══════════════ SEC-A — پروژه‌های مرتبط (195272→201906) ═══════════════
a0 = s.find('<section class="mx-auto max-w-6xl px-5 pb-14 lg:px-8"><div class="mb-7 flex flex-wrap items-end justify-between gap-4"><div><div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700"><svg', 195200)
assert a0 == 195281, a0
a1 = s.find('</section>', a0)
A_RAW = s[a0:a1]
assert len(A_RAW) == 6625, len(A_RAW)

A = A_RAW
def tok1(R, old, new):
    assert R.count(old) >= 1, old[:60]
    return R.replace(old, new, 1)
A = tok1(A, '<h2 class="text-2xl font-extrabold text-slate-900">پروژه‌های اجراشده با والواشر RGB و تک‌رنگ صنعتی (IP68)</h2>',
         '<h2 class="text-2xl font-extrabold text-slate-900">پروژه‌های اجراشده با {ES_TITLE}</h2>')
A = tok1(A, 'href="../projects.html"', 'href="{ES_ARCHIVE_URL}"')
# کارت‌ها: از <div custom="0"> تا پایان گرید
c0 = A.find('<div custom="0">')
assert c0 > 0
aclose = A.rfind('</a></div>') + len('</a></div>')
assert aclose > c0
A = A[:c0] + '{ES_CARDS}' + A[aclose:]
assert '{ES_CARDS}' in A

# کارت تکرارشونده SEC-A
cards_a = []
pos = c0_raw = A_RAW.find('<div custom="0">')
seg_cards = A_RAW[pos:A_RAW.rfind('</a></div>') + len('</a></div>')]
# سه کارت: split بر مرز <div custom="
parts = seg_cards.split('<div custom="')
assert len(parts) == 4, len(parts)
CARD_A_TPL = parts[1]  # شامل «0.06"><a …» مرز بعدی؛ ما کارت اول کامل را از seg می‌سازیم
# کارت اول کامل: از '<div custom="0">' تا شروع '<div custom="0.06">'
card1_end = seg_cards.find('<div custom="0.06">')
CARD_A = seg_cards[:card1_end]
# توکنایز کارت SEC-A
def tok_card_project(c):
    c = c.replace('<div custom="0">', '<div custom="{ES_CUSTOM}">', 1)
    c = c.replace('href="../projects/ahvaz-semi-pyramid-light.html"', 'href="{ES_URL}"', 1)
    c = c.replace('src="../images/light-element.jpg"', 'src="{ES_IMG}"', 1)
    c = c.replace('alt="اجرای پروژه نیم هرم نوری اهواز"', 'alt="{ES_ALT}"', 1)
    c = c.replace('>المان‌های نورانی</span>', '>{ES_CHIP}</span>', 1)
    c = c.replace('>اجرای پروژه نیم هرم نوری اهواز</h3>', '>{ES_TITLE}</h3>', 1)
    c = c.replace('>طراحی و ساخت سازه حجمی نیم‌هرم نوری پیکسلی با مقاومت کامل در برابر اقلیم گرم و مرطوب اهواز</p>',
                  '>{ES_EXCERPT}</p>', 1)
    c = c.replace('>{ES_CITY}</span>', '>{ES_CITY}</span>', 1)
    # شهر: آخرین <span …><SVG/>اهواز</span> → svg را دست نمی‌زنیم؛ فقط متن شهر
    c = c.replace('</svg>اهواز</span>', '</svg>{ES_CITY}</span>', 1)
    for t in ('{ES_CUSTOM}', '{ES_URL}', '{ES_IMG}', '{ES_ALT}', '{ES_CHIP}', '{ES_TITLE}', '{ES_EXCERPT}', '{ES_CITY}'):
        assert t in c, t
    return c
CARD_A = tok_card_project(CARD_A)
# بازسازی: سه کارت میرور = detok کارت با مقادیر سه پروژه + customهای 0/0.06/0.12
PROJ_REF = [
    dict(custom='0', url='../projects/ahvaz-semi-pyramid-light.html', img='../images/light-element.jpg',
         alt='اجرای پروژه نیم هرم نوری اهواز', chip='المان‌های نورانی', title='اجرای پروژه نیم هرم نوری اهواز',
         exc='طراحی و ساخت سازه حجمی نیم‌هرم نوری پیکسلی با مقاومت کامل در برابر اقلیم گرم و مرطوب اهواز', city='اهواز'),
    dict(custom='0.06', url='../projects/esfahan-shohada-square.html', img='../images/light-sphere.jpg',
         alt='نورپردازی میدان شهدای جهان اسلام اصفهان', chip='نورپردازی میدان', title='نورپردازی میدان شهدای جهان اسلام اصفهان',
         exc='شاهکاری از هنر و فناوری با گوی‌های نورانی و پیکسل‌های ۴ سانتی فول‌کالر', city='اصفهان'),
    dict(custom='0.12', url='../projects/tehran-commercial-facade-lighting.html', img='../images/pexels/pexels-35318900.jpg',
         alt='نورپردازی مالتی‌مدیا نمای مجتمع تجاری و اداری — تهران', chip='نورپردازی ساختمان',
         title='نورپردازی مالتی‌مدیا نمای مجتمع تجاری و اداری — تهران',
         exc='ترکیب والواشرهای خطی RGB و لینیر لایت‌های IP68 روی نمای مدرن برج تجاری در تهران', city='تهران'),
]
def detok_card_project(c, d):
    return (c.replace('{ES_CUSTOM}', d['custom']).replace('{ES_URL}', d['url']).replace('{ES_IMG}', d['img'])
             .replace('{ES_ALT}', d['alt']).replace('{ES_CHIP}', d['chip']).replace('{ES_TITLE}', d['title'])
             .replace('{ES_EXCERPT}', d['exc']).replace('{ES_CITY}', d['city']))
rebuild_cards_a = ''.join(detok_card_project(CARD_A, d) for d in PROJ_REF)
reb_a = (A.replace('{ES_CARDS}', rebuild_cards_a)
          .replace('{ES_TITLE}', 'والواشر RGB و تک‌رنگ صنعتی (IP68)')
          .replace('{ES_ARCHIVE_URL}', '../projects.html'))
assert reb_a == A_RAW, f'SEC-A rebuild mismatch: {len(reb_a)} vs {len(A_RAW)}'

# ═══════════════ SEC-C — محصولات مرتبط (206805→213243) ═══════════════
c0 = s.find('<section class="border-t border-slate-200/80 bg-white py-14"><div class="mx-auto max-w-6xl px-5 lg:px-8">', 206700)
assert c0 == 206814, c0
c1 = s.find('</section>', c0)
C_RAW = s[c0:c1]
assert len(C_RAW) == 6429, len(C_RAW)

C = C_RAW
C = tok1(C, 'href="../shop.html"', 'href="{ES_SHOP_URL}"')
cpos = C.find('<a href="../product/point-light-pixel.html"')
assert cpos > 0
cclose = C.rfind('</a>') + len('</a>')
assert cclose > cpos
seg_c = C[cpos: cclose]
C = C[:cpos] + '{ES_CARDS}' + C[cclose:]

parts_c = seg_c.split('<a href="../product/')
assert len(parts_c) == 4, len(parts_c)
CARD_C = '<a href="../product/' + parts_c[1]
def tok_card_product(c):
    c = c.replace('href="../product/point-light-pixel.html"', 'href="{ES_URL}"', 1)
    c = c.replace('src="../images/light-tunnel.jpg"', 'src="{ES_IMG}"', 1)
    c = c.replace('alt="پوینت لایت پیکسل ۴ سانتی فول‌کالر"', 'alt="{ES_ALT}"', 1)
    c = c.replace('>پوینت لایت پیکسل LED</span>', '>{ES_CHIP}</span>', 1)
    c = c.replace('>پوینت لایت پیکسل ۴ سانتی فول‌کالر</h3>', '>{ES_TITLE}</h3>', 1)
    c = c.replace('>پیکسل فول‌کالر با گواهی IP68 برای تونل‌های نوری، گوی‌های نورانی و نمای رسانه‌ای ساختمان</p>',
                  '>{ES_EXCERPT}</p>', 1)
    for t in ('{ES_URL}', '{ES_IMG}', '{ES_ALT}', '{ES_CHIP}', '{ES_TITLE}', '{ES_EXCERPT}'):
        assert t in c, t
    return c
CARD_C = tok_card_product(CARD_C)
PROD_REF = [
    dict(url='../product/point-light-pixel.html', img='../images/light-tunnel.jpg',
         alt='پوینت لایت پیکسل ۴ سانتی فول‌کالر', chip='پوینت لایت پیکسل LED',
         title='پوینت لایت پیکسل ۴ سانتی فول‌کالر',
         exc='پیکسل فول‌کالر با گواهی IP68 برای تونل‌های نوری، گوی‌های نورانی و نمای رسانه‌ای ساختمان'),
    dict(url='../product/point-light-pixel-6cm.html', img='../images/light-sphere.jpg',
         alt='پوینت لایت پیکسل ۶ سانتی پرقدرت', chip='پوینت لایت پیکسل LED',
         title='پوینت لایت پیکسل ۶ سانتی پرقدرت',
         exc='قطر ۶ سانتی‌متر با ۶ عدد LED SMD پرقدرت، دید عالی از فواصل دور و درجه حفاظت IP68'),
    dict(url='../product/needle-string-lights.html', img='../images/string-lights.jpg',
         alt='ریسه سوزنی شهری ضد آب (IP68)', chip='ریسه ال ای دی مدل سوزنی',
         title='ریسه سوزنی شهری ضد آب (IP68)',
         exc='ریسه سوزنی شهری با گواهی IP68، مقاوم در برابر کشش و اشعه UV؛ ویژه آذین‌بندی درختان و سازه‌های نوری'),
]
def detok_card_product(c, d):
    return (c.replace('{ES_URL}', d['url']).replace('{ES_IMG}', d['img']).replace('{ES_ALT}', d['alt'])
             .replace('{ES_CHIP}', d['chip']).replace('{ES_TITLE}', d['title']).replace('{ES_EXCERPT}', d['exc']))
rebuild_cards_c = ''.join(detok_card_product(CARD_C, d) for d in PROD_REF)
reb_c = (C.replace('{ES_CARDS}', rebuild_cards_c).replace('{ES_SHOP_URL}', '../shop.html'))
assert reb_c == C_RAW, f'SEC-C rebuild mismatch: {len(reb_c)} vs {len(C_RAW)}'

# ═══════════════ REVIEWS — پنل دیدگاه‌ها (169398→195271) ═══════════════
r0 = s.find('<div data-prod-tab-panel="reviews" class="hidden">')
assert r0 == 169393, r0
R_RAW = s[r0:195271]
RV = R_RAW
# ۱) خلاصه → توکن
sum0 = RV.find('<div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 sm:px-5">')
sum1 = RV.find('<div data-reviews-list="true"', sum0)
assert sum0 > 0 and sum1 > sum0
SUMMARY_R = RV[sum0:sum1]
RV = RV[:sum0] + '{ES_SUMMARY_HTML}' + RV[sum1:]
# ۲) کارت‌های لیست → توکن {ES_ITEMS} داخل wrapper لیست
li0 = RV.find('<div data-reviews-list="true" class="space-y-3.5">')
inner0 = li0 + len('<div data-reviews-list="true" class="space-y-3.5">')
li1 = RV.find('<div class="lg:col-span-5">', inner0)
assert li1 > inner0
# داخلی لیست تا قبل از بستن wrapper: راستش تا قبل از '</div><div class="lg:col-span-5">'
seg_list = RV[inner0:li1]
assert seg_list.rstrip().endswith('</div></div>')
ITEMS = seg_list.rstrip()[:-len('</div></div>')]  # حذف بستن لیست + ستون۷
RV = RV[:inner0] + '{ES_ITEMS}' + '</div></div>' + RV[li1:]
# ۳) فرم → توکن
f0 = RV.find('<form data-review-form="true"')
f1 = RV.find('</form>', f0) + len('</form>')
FORM = RV[f0:f1]
RV = RV[:f0] + '{ES_FORM_HTML}' + RV[f1:]
FORM = FORM.replace('<form data-review-form="true"',
                    '<form method="post" action="{ES_FORM_ACTION}" data-review-form="true"', 1)
FORM = FORM.replace('data-review-form="true" class=', 'data-review-form="true">\n{ES_POST_ID_HIDDEN}<div class=', 1)
for t in ('{ES_FORM_ACTION}', '{ES_POST_ID_HIDDEN}'):
    assert t in FORM, t

# کارت دیدگاه (آیتم اول میرور)
items_parts = ITEMS.split('<div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-2xs">')
assert len(items_parts) == 4, len(items_parts)
RV_CARD = '<div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-2xs">' + items_parts[1]
RV_CARD = RV_CARD.replace('<div class="text-sm font-black text-slate-900">مهندس سعید رضایی‌پور</div>',
                          '<div class="text-sm font-black text-slate-900">{ES_NAME}</div>', 1)
RV_CARD = RV_CARD.replace('<div class="mt-0.5 text-[11px] font-bold text-emerald-700">واحد زیباسازی و تاسیسات شهری</div>',
                          '{ES_ORG_HTML}', 1)
RV_CARD = RV_CARD.replace('aria-label="امتیاز ۵ از ۵">', 'aria-label="{ES_ARIA}">', 1)
RV_CARD = RV_CARD.replace('>۱۴۰۴/۰۱/۱۸</span>', '>{ES_DATE}</span>', 1)
RV_CARD = RV_CARD.replace('>کیفیت عایق‌بندی IP68 و شدت نوردهی این محصول در پروژه اجرایی ما بسیار عالی بود. بعد از بارندگی‌های اخیر بدون کوچک‌ترین نفوذ رطوبت یا افت نور کار می‌کند.</p>',
                          '>{ES_TEXT}</p>', 1)
# پنج ستاره کارت → توکن {ES_STARS_SM}
import re as _re
RV_CARD = _re.sub(r'(<div class="flex items-center gap-0\.5" aria-label="\{ES_ARIA\}">)(.*?)(</div><span class="rounded-lg bg-slate-100)',
                  r'\1{ES_STARS_SM}\3', RV_CARD, count=1, flags=_re.S)
for t in ('{ES_NAME}', '{ES_ORG_HTML}', '{ES_ARIA}', '{ES_STARS_SM}', '{ES_DATE}', '{ES_TEXT}'):
    assert t in RV_CARD, t

# بازسازی reviews
ORG_FULL = '<div class="mt-0.5 text-[11px] font-bold text-emerald-700">واحد زیباسازی و تاسیسات شهری</div>'
SVG_FULL = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>'
SVG_EMPTY = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 text-slate-200" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>'
FA = {'۰': 0, '۱': 1, '۲': 2, '۳': 3, '۴': 4, '۵': 5}
def detok_rv_card(c, name, org, rate, date, text):
    n = FA[rate]
    return (c.replace('{ES_NAME}', name).replace('{ES_ORG_HTML}', org)
             .replace('{ES_ARIA}', f'امتیاز {rate} از ۵')
             .replace('{ES_STARS_SM}', SVG_FULL * n + SVG_EMPTY * (5 - n))
             .replace('{ES_DATE}', date).replace('{ES_TEXT}', text))
ORG2 = '<div class="mt-0.5 text-[11px] font-bold text-emerald-700">پیمانکار پروژه‌های روشنایی معابر</div>'
ORG3 = '<div class="mt-0.5 text-[11px] font-bold text-emerald-700">کارشناس مسئول برق و نورپردازی</div>'
RV_REF = [
    dict(name='مهندس سعید رضایی‌پور', org=ORG_FULL, rate='۵', date='۱۴۰۴/۰۱/۱۸',
         text='کیفیت عایق‌بندی IP68 و شدت نوردهی این محصول در پروژه اجرایی ما بسیار عالی بود. بعد از بارندگی‌های اخیر بدون کوچک‌ترین نفوذ رطوبت یا افت نور کار می‌کند.'),
    dict(name='مهندس محمدحسین کاظمی', org=ORG2, rate='۵', date='۱۴۰۳/۱۲/۲۲',
         text='خرید مستقیم از کارخانه عرفان صنعت اصفهان همراه با پیش‌فاکتور رسمی سامانه ستاد و بسته‌بندی ایمن، سرعت تحویل پروژه ما را دو برابر کرد.'),
    dict(name='مهندس علی‌اکبر شریفی', org=ORG3, rate='۴', date='۱۴۰۳/۱۱/۰۹',
         text='کیفیت ساخت بدنه و کابل‌ها کاملاً صنعتی و بادوام است و پشتیبانی فنی کارخانه برای راه‌اندازی بسیار همراه و پاسخگو بودند.'),
]
SUMMARY_DETOK = SUMMARY_R
reb_items = ''.join(detok_rv_card(RV_CARD, **d) for d in RV_REF)
FORM_MIRROR = FORM.replace(
    '<form method="post" action="{ES_FORM_ACTION}" data-review-form="true">\n{ES_POST_ID_HIDDEN}<div class=',
    '<form data-review-form="true" class=', 1)
assert '{ES_FORM_ACTION}' not in FORM_MIRROR and '{ES_POST_ID_HIDDEN}' not in FORM_MIRROR
reb_rv = (RV.replace('{ES_SUMMARY_HTML}', SUMMARY_DETOK)
            .replace('{ES_ITEMS}', reb_items)
            .replace('{ES_FORM_HTML}', FORM_MIRROR))
assert reb_rv == R_RAW, f'REVIEWS rebuild mismatch: {len(reb_rv)} vs {len(R_RAW)}'

# ═══════════════ نوشتن فایل‌ها ═══════════════
FRAGS_A = nowdoc('ES_CARD', CARD_A, 'ESP66A') + nowdoc('ES_REGION', A, 'ESP66B')
FRAGS_C = nowdoc('ES_CARD_C', CARD_C, 'ESP66C') + nowdoc('ES_REGION_C', C, 'ESP66D')
FRAGS_RV = nowdoc('ES_RV_CARD', RV_CARD, 'ESP66E') + nowdoc('ES_RV_FORM', FORM, 'ESP66F') + nowdoc('ES_RV_REGION', RV, 'ESP66G')

RELATED_PROJECTS = (
"<?php\n"
"/**\n"
" * سکشن پروژه‌های اجراشده با محصول — برش بایت‌دقیق میرور (SEC-A)؛ دیتا از متای project-ids\n"
" * فرگمنت با temp/gen_p66.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n"
"\n"
+ FRAGS_A +
"$es_ids_raw = trim((string) get_post_meta($es_pid, 'project-ids', true));\n"
"$es_ids = array_values(array_filter(array_map('intval', explode(',', $es_ids_raw)), static fn(int $i): bool => $i > 0));\n"
"if ($es_ids === []) {\n"
"    return; // سکشن خالی مخفی\n"
"}\n"
"$es_projects = get_posts([\n"
"    'post_type' => 'projects',\n"
"    'include'   => $es_ids,\n"
"    'orderby'   => 'post__in',\n"
"]);\n"
"$es_projects = array_values(array_filter(is_array($es_projects) ? $es_projects : [], static fn($p): bool => is_object($p) && (int) ($p->ID ?? 0) > 0));\n"
"if ($es_projects === []) {\n"
"    return;\n"
"}\n\n"
"$es_title = get_the_title($es_pid);\n"
"$es_cards = '';\n"
"foreach ($es_projects as $es_i => $es_p) {\n"
"    $es_p_id = (int) $es_p->ID;\n"
"    $es_p_title = (string) get_the_title($es_p_id);\n"
"    $es_custom = (string) round($es_i * 0.06, 2);\n"
"    $es_thumb = (int) get_post_meta($es_p_id, '_thumbnail_id', true);\n"
"    $es_img = $es_thumb > 0 ? es_media_url($es_thumb) : es_media_url('assets/images/light-element.jpg');\n"
"    $es_terms_t = get_the_terms($es_p_id, 'project_type');\n"
"    $es_chip = (is_array($es_terms_t) && isset($es_terms_t[0]->name)) ? (string) $es_terms_t[0]->name : '';\n"
"    $es_terms_c = get_the_terms($es_p_id, 'city');\n"
"    $es_city = (is_array($es_terms_c) && isset($es_terms_c[0]->name)) ? (string) $es_terms_c[0]->name : '';\n"
"    $es_exc = trim((string) ($es_p->post_excerpt ?? ''));\n"
"    if ($es_exc === '') {\n"
"        $es_exc = wp_trim_words(strip_tags((string) ($es_p->post_content ?? '')), 20);\n"
"    }\n"
"    $es_cards .= strtr($ES_CARD, [\n"
"        '{ES_CUSTOM}'  => $es_custom,\n"
"        '{ES_URL}'     => esc_url((string) get_permalink($es_p_id)),\n"
"        '{ES_IMG}'     => esc_url($es_img),\n"
"        '{ES_ALT}'     => esc_attr($es_p_title),\n"
"        '{ES_CHIP}'    => esc_html($es_chip),\n"
"        '{ES_TITLE}'   => esc_html($es_p_title),\n"
"        '{ES_EXCERPT}' => esc_html($es_exc),\n"
"        '{ES_CITY}'    => esc_html($es_city),\n"
"    ]);\n"
"}\n"
"echo strtr($ES_REGION, [\n"
"    '{ES_TITLE}'       => esc_html($es_title),\n"
"    '{ES_ARCHIVE_URL}' => esc_url(es_page_url('projects')),\n"
"    '{ES_CARDS}'       => $es_cards,\n"
"]);\n")

RELATED_PRODUCTS = (
"<?php\n"
"/**\n"
" * سکشن محصولات مشابه و مکمل — برش بایت‌دقیق میرور (SEC-C)؛ همان product_cat به‌جز خود محصول\n"
" * فرگمنت با temp/gen_p66.py تولید شده است.\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n\n"
+ FRAGS_C +
"$es_terms = get_the_terms($es_pid, 'product_cat');\n"
"$es_cat_id = (is_array($es_terms) && isset($es_terms[0]->term_id)) ? (int) $es_terms[0]->term_id : 0;\n"
"if ($es_cat_id < 1) {\n"
"    return; // بدون دسته → سکشن مخفی\n"
"}\n\n"
"$es_q = [\n"
"    'post_type'      => 'product',\n"
"    'posts_per_page' => 3,\n"
"    'post__not_in'   => [$es_pid],\n"
"    'tax_query'      => [[\n"
"        'taxonomy' => 'product_cat',\n"
"        'field'    => 'term_id',\n"
"        'terms'    => $es_cat_id,\n"
"    ]],\n"
"];\n"
"$es_related = get_posts(apply_filters('es_product_related_args', $es_q, $es_pid));\n"
"$es_related = array_values(array_filter(is_array($es_related) ? $es_related : [], static fn($p): bool => is_object($p) && (int) ($p->ID ?? 0) > 0));\n"
"if ($es_related === []) {\n"
"    return;\n"
"}\n\n"
"$es_cards = '';\n"
"foreach ($es_related as $es_p) {\n"
"    $es_p_id = (int) $es_p->ID;\n"
"    $es_p_title = (string) get_the_title($es_p_id);\n"
"    $es_thumb = (int) get_post_meta($es_p_id, '_thumbnail_id', true);\n"
"    $es_img = $es_thumb > 0 ? es_media_url($es_thumb) : es_media_url('assets/images/light-tree.jpg');\n"
"    $es_t = get_the_terms($es_p_id, 'product_cat');\n"
"    $es_chip = '';\n"
"    if (is_array($es_t) && $es_t !== []) {\n"
"        foreach ($es_t as $es_tt) {\n"
"            if ((int) ($es_tt->term_id ?? 0) !== $es_cat_id) {\n"
"                $es_chip = (string) ($es_tt->name ?? '');\n"
"                break;\n"
"            }\n"
"        }\n"
"        if ($es_chip === '') {\n"
"            $es_chip = (string) ($es_t[0]->name ?? '');\n"
"        }\n"
"    }\n"
"    $es_exc = trim((string) ($es_p->post_excerpt ?? ''));\n"
"    if ($es_exc === '') {\n"
"        $es_exc = wp_trim_words(strip_tags((string) ($es_p->post_content ?? '')), 20);\n"
"    }\n"
"    $es_cards .= strtr($ES_CARD_C, [\n"
"        '{ES_URL}'     => esc_url((string) get_permalink($es_p_id)),\n"
"        '{ES_IMG}'     => esc_url($es_img),\n"
"        '{ES_ALT}'     => esc_attr($es_p_title),\n"
"        '{ES_CHIP}'    => esc_html($es_chip),\n"
"        '{ES_TITLE}'   => esc_html($es_p_title),\n"
"        '{ES_EXCERPT}' => esc_html($es_exc),\n"
"    ]);\n"
"}\n"
"echo strtr($ES_REGION_C, [\n"
"    '{ES_SHOP_URL}' => esc_url(es_page_url('shop')),\n"
"    '{ES_CARDS}'    => $es_cards,\n"
"]);\n")

REVIEWS = (
"<?php\n"
"/**\n"
" * پنل دیدگاه‌های محصول — برش بایت‌دقیق میرور (پنل reviews تب‌ها): خلاصه امتیاز + لیست + فرم ثبت\n"
" * فرگمنت با temp/gen_p66.py تولید شده است. نقش/امتیاز از comment_meta (es_review_org / rating).\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
"$es_pid = (int) ($args['pid'] ?? 0);\n\n"
+ FRAGS_RV +
"\n"
"/** ستاره‌های امتیاز — سایز sm (کارت) یا md (خلاصه)؛ پر تا n، خالی تا ۵ */\n"
"$es_stars = static function (int $n, string $cls): string {\n"
"    $full = '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"lucide lucide-star ' . $cls . ' fill-amber-400 text-amber-400\" aria-hidden=\"true\"><path d=\"M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z\"></path></svg>';\n"
"    $empty = '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"lucide lucide-star ' . $cls . ' text-slate-200\" aria-hidden=\"true\"><path d=\"M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z\"></path></svg>';\n"
"    return str_repeat($full, max(0, min(5, $n))) . str_repeat($empty, max(0, 5 - max(0, min(5, $n))));\n"
"};\n\n"
"// خلاصه امتیاز\n"
"$es_title = get_the_title($es_pid);\n"
"$es_avg_raw = trim((string) get_post_meta($es_pid, '_wc_average_rating', true));\n"
"$es_avg = $es_avg_raw !== '' ? rtrim(rtrim($es_avg_raw, '0'), '.') : '0';\n"
"$es_avg_int = (int) round((float) $es_avg);\n"
"$es_count = (int) get_comments_number($es_pid);\n"
"$es_summary = '<div class=\"flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 sm:px-5\"><div class=\"flex items-center gap-3.5\"><div class=\"grid h-13 w-13 place-items-center rounded-2xl bg-emerald-700 text-lg font-black text-white tabular-nums\">' . es_fa_num($es_avg) . '</div><div><div class=\"flex items-center gap-1\">' . $es_stars($es_avg_int, 'h-4 w-4') . '</div><div class=\"mt-1 text-xs font-bold text-slate-600\">میانگین امتیاز خریداران برای ' . esc_html($es_title) . '</div></div></div><span data-reviews-count-badge=\"true\" class=\"rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs font-extrabold text-emerald-800 tabular-nums\">' . es_fa_num((string) $es_count) . ' دیدگاه ثبت‌شده</span></div>';\n\n"
"// لیست دیدگاه‌ها (تأییدشده)\n"
"$es_comments = get_comments(['post_id' => $es_pid, 'status' => 'approve', 'type' => 'comment']);\n"
"$es_items = '';\n"
"foreach ($es_comments as $es_c) {\n"
"    $es_cid = (int) ($es_c->comment_ID ?? 0);\n"
"    $es_rate = (int) get_comment_meta($es_cid, 'rating', true);\n"
"    $es_org = trim((string) get_comment_meta($es_cid, 'es_review_org', true));\n"
"    $es_org_html = $es_org !== '' ? '<div class=\"mt-0.5 text-[11px] font-bold text-emerald-700\">' . esc_html($es_org) . '</div>' : '';\n"
"    $es_items .= strtr($ES_RV_CARD, [\n"
"        '{ES_NAME}'      => esc_html((string) ($es_c->comment_author ?? '')),\n"
"        '{ES_ORG_HTML}'  => $es_org_html,\n"
"        '{ES_ARIA}'      => esc_attr('امتیاز ' . es_fa_num((string) $es_rate) . ' از ۵'),\n"
"        '{ES_STARS_SM}'  => $es_stars($es_rate, 'h-3.5 w-3.5'),\n"
"        '{ES_DATE}'      => esc_html(es_fa_num((string) get_comment_date('Y/m/d', $es_c))),\n"
"        '{ES_TEXT}'      => esc_html((string) ($es_c->comment_content ?? '')),\n"
"    ]);\n"
"}\n\n"
"// فرم ثبت — مسیر استاندارد wp-comments-post.php + متای rating/es_review_org در Woo\\Reviews\n"
"$es_form = strtr($ES_RV_FORM, [\n"
"    '{ES_FORM_ACTION}'   => esc_url(home_url('/wp-comments-post.php')),\n"
"    '{ES_POST_ID_HIDDEN}' => '<input type=\"hidden\" name=\"comment_post_ID\" value=\"' . esc_attr((string) $es_pid) . '\"/>',\n"
"]);\n\n"
"echo strtr($ES_RV_REGION, [\n"
"    '{ES_SUMMARY_HTML}' => $es_summary,\n"
"    '{ES_ITEMS}'        => $es_items,\n"
"    '{ES_FORM_HTML}'    => $es_form,\n"
"]);\n")

REVIEWS_CLASS = '''<?php
/**
 * ذخیره‌سازی دیدگاه محصول — امتیاز (rating) و سازمان (es_review_org) در comment_meta
 * فرم میرور فیلد email ندارد؛ برای مهمان‌ها ایمیل placeholder تولید می‌شود تا هسته رد نکند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\\Theme\\Woo;

final class Reviews {

    public static function init(): void {
        if (function_exists('is_admin') && is_admin()) {
            return;
        }
        add_filter('preprocess_comment', [self::class, 'guest_email_fallback']);
        add_action('comment_post', [self::class, 'save_meta'], 10, 3);
    }

    /** مهمان بدون ایمیل → placeholder (فرم میرور فیلد ایمیل ندارد) */
    public static function guest_email_fallback(array $comment): array {
        $email = trim((string) ($comment['comment_author_email'] ?? ''));
        if ($email === '' && trim((string) ($comment['comment_author'] ?? '')) !== '') {
            $comment['comment_author_email'] = 'no-reply@' . (string) (wp_parse_url(home_url(), PHP_URL_HOST) ?: 'erfansanat.local');
        }
        return $comment;
    }

    /** ذخیره امتیاز و سازمان به‌عنوان متای دیدگاه */
    public static function save_meta(int $comment_id, int|string $approved, array $comment): void {
        unset($approved, $comment);
        $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
        if ($rating >= 1 && $rating <= 5) {
            add_comment_meta($comment_id, 'rating', $rating, true);
        }
        $org = isset($_POST['es_review_org']) ? sanitize_text_field((string) wp_unslash((string) $_POST['es_review_org'])) : '';
        if ($org !== '') {
            add_comment_meta($comment_id, 'es_review_org', $org, true);
        }
    }
}
'''
open(os.path.join(ROOT, 'template-parts/single-product/related-projects.php'), 'w', encoding='utf-8').write(RELATED_PROJECTS)
open(os.path.join(ROOT, 'template-parts/single-product/related-products.php'), 'w', encoding='utf-8').write(RELATED_PRODUCTS)
open(os.path.join(ROOT, 'template-parts/single-product/reviews.php'), 'w', encoding='utf-8').write(REVIEWS)
open(os.path.join(ROOT, 'inc/Woo/Reviews.php'), 'w', encoding='utf-8').write(REVIEWS_CLASS)

for f in ('related-projects.php', 'related-products.php', 'reviews.php'):
    print(f, os.path.getsize(os.path.join(ROOT, 'template-parts/single-product', f)))
print('Reviews.php:', os.path.getsize(os.path.join(ROOT, 'inc/Woo/Reviews.php')))
