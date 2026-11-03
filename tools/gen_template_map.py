#!/usr/bin/env python3
"""Generate temp/STATIC_TEMPLATE_MAP.md (mapping tables) from the scan JSON."""
import json, os, re, sys
from collections import defaultdict

SCAN = 'temp/_scan'
pages = json.load(open(f'{SCAN}/pages.json', encoding='utf-8'))
secs  = json.load(open(f'{SCAN}/sections.json', encoding='utf-8'))
dd    = json.load(open(f'{SCAN}/dedupe.json', encoding='utf-8'))

dup_of = {}
for g in dd['dups']:
    primary = sorted(g, key=lambda x: (len(x), x))[0]
    for f in g:
        if f != primary:
            dup_of[f] = primary

def family(f):
    if f in ('index.html',): return ('front-page.php', 'صفحه اصلی (۱۵ سکشن)')
    if f in ('shop.html', 'products.html'): return ('woocommerce/archive-product.php', 'آرشیو فروشگاه /shop/')
    if f in ('projects.html',): return ('archive-projects.php', 'آرشیو پروژه‌ها')
    if f in ('blog.html',): return ('home.php', 'آرشیو وبلاگ (برگه blog)')
    if f in ('cart.html',): return ('woocommerce/cart/cart.php', 'سبد خرید')
    if f in ('checkout.html',): return ('woocommerce/checkout/form-checkout.php', 'تسویه حساب')
    if f == 'about.html': return ('page-templates/template-about.php', 'برگه درباره ما')
    if f == 'contact.html': return ('page-templates/template-contact.php', 'برگه تماس با ما')
    if f == 'faq.html': return ('page-templates/template-faq.php', 'برگه سوالات متداول')
    if f == 'licenses-and-honors.html': return ('page-templates/template-licenses.php', 'برگه مجوزها و افتخارات')
    if f == 'recruitment.html': return ('page-templates/template-recruitment.php', 'برگه فرصت‌های شغلی')
    if f == 'refund_returns.html': return ('page-templates/template-refund.php', 'برگه شرایط گارانتی')
    if f == 'services.html': return ('page-templates/template-services.php', 'برگه فهرست خدمات')
    if f.startswith('services/'): return ('single-service.php (یا قالب برگه فرزند)', 'جزئیات خدمت')
    if re.match(r'^product/[^/]+\.html$', f): return ('woocommerce/single-product.php', 'صفحه محصول')
    if re.match(r'^products/(category-.*)\.html$', f): return ('woocommerce/taxonomy-product_cat.php', 'آرشیو دسته محصول (نام قدیمی)')
    if re.match(r'^product_cat/[^/]+\.html$', f): return ('woocommerce/taxonomy-product_cat.php', 'آرشیو دسته محصول')
    if re.match(r'^projects/(city-.*)\.html$', f): return ('taxonomy-city.php', 'آرشیو شهر (نام قدیمی)')
    if re.match(r'^city/[^/]+\.html$', f): return ('taxonomy-city.php', 'آرشیو شهر')
    if re.match(r'^projects/type-.*\.html$', f): return ('taxonomy-project_type.php', 'آرشیو نوع پروژه (نام قدیمی)')
    if re.match(r'^project_type/[^/]+\.html$', f): return ('taxonomy-project_type.php', 'آرشیو نوع پروژه')
    if re.match(r'^projects/[^/]+\.html$', f): return ('single-projects.php', 'صفحه پروژه')
    if re.match(r'^blog/[^/]+\.html$', f): return ('single.php', 'تک‌نوشته')
    if re.match(r'^category/[^/]+\.html$', f): return ('category.php', 'آرشیو دسته نوشته')
    if re.match(r'^products/[^/]+\.html$', f): return ('woocommerce/single-product.php', 'صفحه محصول (نام قدیمی)')
    return ('(نامشخص)', '?')

lines = []
A = lines.append
A('# STATIC_TEMPLATE_MAP — نقشه نگاشت قالب استاتیک به سلسله‌مراتب وردپرس\n')
A('> تولیدشده به‌صورت خودکار توسط `tools/gen_template_map.py` از اسکن `reference/static-mirror`.\n')
A('> برای بازتولید: `python3 tools/scan_static.py reference/static-mirror temp/_scan && python3 tools/dedupe_pages.py && python3 tools/section_inventory.py && python3 tools/gen_template_map.py`\n')

A('\n## ۱. خلاصه اسکن\n')
A(f'- تعداد فایل HTML: **{len(pages)}**')
A(f'- بدنه یکتا (پس از حذف تکراری‌های نسبی): **{len(pages)-len(dup_of)}**')
A(f'- گروه‌های تکراری: **{len(dd["dups"])}**')
A(f'- خانواده چیدمان (`<main>`): **{len(dd["families"])}**')
A(f'- مجموع سکشن‌ها: **{sum(v["n_sections"] for v in pages.values())}**')
A(f'- CSS: `assets/css/styles.css` — یک فایل کامپایل‌شده Tailwind + لایه‌های سفارشی (`card-light`, ...) با ۳ `@font-face` محلی Vazirmatn')
A(f'- JS: `assets/js/main.js` — ۲۸۱۳ خط Vanilla JS، {16} ماژول `init*` (ناوبری، مگامنو+جستجو، اسلایدر هیرو، شمارنده، گالری/لایت‌باکس، فیلتر آرشیو، تب محصول، سبد خرید، فرم، اشتراک‌گذاری)')
A(f'- تصاویر: ۲۲ فایل (۱۰ تصویر اختصاصی + ۱۱ Pexels + لوگو SVG) + ۱ ویدیو `hero-video.mp4` (۶.۸MB)')
A('- هدر: **یک** قالب مشترک در هر ۱۲۸ صفحه (تفاوت‌ها فقط `../` و کلاس حالت فعال)')
A('- فوتر: **یک** قالب مشترک در هر ۱۲۸ صفحه (تفاوت‌ها فقط پیشوند مسیر نسبی)\n')

A('\n## ۲. جدول نگاشت فایل → قالب وردپرس\n')
A('| # | فایل استاتیک | قالب وردپرس | توضیح | سکشن | هدر/فوتر |')
A('|---|---|---|---|---|---|')
for i, f in enumerate(sorted(pages), 1):
    tpl, note = family(f)
    n = pages[f]['n_sections']
    duptxt = f' <br>↳ تکرارِ `{dup_of[f]}`' if f in dup_of else ''
    A(f'| {i} | `{f}`{duptxt} | `{tpl}` | {note} | {n} | {pages[f]["header_hash"]}/{pages[f]["footer_hash"]} |')

A('\n## ۳. سکشن‌های صفحه اصلی (`index.html` → `front-page.php`)\n')
A('| # | id | aria-label | بایت | tab | form | img | داده‌هوک‌های اصلی |')
A('|---|---|---|---|---|---|---|---|')
for i, s in enumerate(secs['index.html'], 1):
    if s['tag'] != 'section': continue
    A(f'| {i} | `{s["id"] or "-"}` | {(s["aria"] or "-")[:60]} | {s["bytes"]} | {s["tabs"]} | {s["forms"]} | {s["imgs"]} | `{", ".join(s["hooks"][:6])}` |')

for page, title in [('product/delsa-light-tree.html','صفحه محصول (`single-product.php`)'),
                    ('projects/esfahan-shohada-square.html','صفحه پروژه (`single-projects.php`)'),
                    ('blog/types-of-light-tunnel.html','تک‌نوشته (`single.php`)'),
                    ('products.html','آرشیو فروشگاه (`archive-product.php`)'),
                    ('projects.html','آرشیو پروژه (`archive-projects.php`)'),
                    ('blog.html','آرشیو وبلاگ (`home.php`)')]:
    A(f'\n## ۴. سکشن‌های {title} — `{page}`\n')
    A('| # | id | aria-label | بایت | tab | form | img | هوک‌ها |')
    A('|---|---|---|---|---|---|---|---|')
    for i, s in enumerate(secs[page], 1):
        if s['tag'] in ('header','footer'): continue
        A(f'| {i} | `{s["id"] or "-"}` | {(s["aria"] or "-")[:55]} | {s["bytes"]} | {s["tabs"]} | {s["forms"]} | {s["imgs"]} | `{", ".join(s["hooks"][:6])}` |')

A('\n## ۵. فایل‌های تکراری (باید ادغام شوند، نه دوباره ساخته شوند)\n')
A('| گروه | فایل اصلی (مرجع) | فایل‌های تکراری |')
A('|---|---|---|')
for g in dd['dups']:
    primary = sorted(g, key=lambda x: (len(x), x))[0]
    A(f'| {len(g)} | `{primary}` | ' + ', '.join(f'`{x}`' for x in g if x != primary) + ' |')

A('\n## ۶. کاتالوگ دارایی‌ها (Assets)\n')
A('| فایل | حجم | مقصد در قالب |')
A('|---|---|---|')
for path, dest in [('assets/css/styles.css','assets/css/styles.css'),
                   ('assets/js/main.js','assets/js/main.js'),
                   ('assets/fonts/vazirmatn-arabic.woff2','assets/fonts/'),
                   ('assets/fonts/vazirmatn-latin.woff2','assets/fonts/'),
                   ('assets/fonts/vazirmatn-latin-ext.woff2','assets/fonts/'),
                   ('images/ese-logo.svg','assets/img/ (لوگو)'),
                   ('images/hero.jpg … (10 تصویر)','assets/img/ (اسلایدهای هیرو)'),
                   ('images/pexels/*.jpg (11)','assets/img/pexels/'),
                   ('images/hero-video.mp4','assets/video/ (۶.۸MB — نیاز به تأیید)')]:
    A(f'| `{path}` | - | `{dest}` |')

A('\n## ۷. صفحاتی که در میرور استاتیک وجود ندارند (نیازمند تصمیم)\n')
A('- `arya` — لندینگ درب بازکن هوشمند (ID 16947)')
A('- `smartelement` — لندینگ خدمات (ID 11984)')
A('- `my-account` / `auth` — حساب کاربری و ورود (WooCommerce + voorodak)')
A('- `project` — برگه پروژه‌های برگزیده (ID 8366)')
A('- `search.php` — نتایج جستجو، `404.php`، `page.php` عمومی، `single.php` برای برگه‌های ووکامرس خالی')

open('temp/STATIC_TEMPLATE_MAP.md','w',encoding='utf-8').write('\n'.join(lines)+'\n')
print('written', len(lines), 'lines')
