#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
ژنراتور ۷.۱ — single-projects (سینگل پروژه)
برش بایت‌دقیق از uploads/static-mirror/projects/esfahan-shohada-square.html
استراتژی: بدنه/آیکون‌ها/ردیف‌ها static عین میرور؛ فقط مقادیر داینامیک {ES_*} توکن می‌شوند.
"""
import os

ROOT = '/home/user/erfansanat-theme-ai'
s = open(os.path.join(ROOT, 'uploads/static-mirror/projects/esfahan-shohada-square.html'), encoding='utf-8').read()


def nowdoc(name, body, tag):
    return "$" + name + " = <<<'{t}'\n{b}\n{t};\n".format(t=tag, b=body)


def tok(R, old, new):
    assert R.count(old) >= 1, old[:70]
    return R.replace(old, new, 1)


m0 = s.index('<main class="bg-slate-50/50">') + len('<main class="bg-slate-50/50">')
gal0 = s.index('<section class="mx-auto max-w-6xl px-4 py-8', m0)
tabs0 = s.index('<section id="project-details"', m0)
prod0 = s.rindex('<section', m0, s.index('محصولات و تجهیزات استفاده', m0))
rel0 = s.rindex('<section', m0, s.index('سایر پروژه‌های شاخص', m0))
m1 = s.index('</main>', m0)

HERO_RAW = s[m0:gal0]
GAL_RAW = s[gal0:tabs0]
TABS_RAW = s[tabs0:prod0]
PROD_RAW = s[prod0:rel0]
REL_RAW = s[rel0:m1]

# ═══════════ HERO ═══════════
HERO = tok(HERO_RAW, '<a href="../index.html"', '<a href="{ES_HOME_URL}"')
HERO = tok(HERO, '<a href="../projects.html"', '<a href="{ES_PROJECTS_URL}"')
HERO = tok(HERO, '<a href="../project_type/square-lighting.html"', '<a href="{ES_TYPE_URL}"')
HERO = tok(HERO, 'hover:text-emerald-700">نورپردازی میدان</a>', 'hover:text-emerald-700">{ES_TYPE_NAME}</a>')
HERO = tok(HERO, 'text-emerald-800">نورپردازی میدان شهدای جهان اسلام اصفهان</span></span></nav>', 'text-emerald-800">{ES_TITLE}</span></span></nav>')
HERO = tok(HERO, '<span>نورپردازی میدان · اصفهان</span>', '<span>{ES_BADGE}</span>')
HERO = tok(HERO, 'lg:leading-[1.24]">نورپردازی میدان شهدای جهان اسلام اصفهان </h1>', 'lg:leading-[1.24]">{ES_TITLE} </h1>')
HERO = tok(HERO, 'pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]">شاهکاری از هنر و فناوری با گوی‌های نورانی و پیکسل‌های ۴ سانتی فول‌کالر</p>', 'pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]">{ES_DESC}</p>')

# ═══════════ GALLERY + SIDEBAR ═══════════
IMGS = ['../images/light-sphere.jpg', '../images/pexels/pexels-14720175.jpg', '../images/pexels/pexels-17789372.jpg', '../images/pexels/pexels-35318900.jpg', '../images/pexels/pexels-29570545.jpg']
GAL = GAL_RAW
GAL = tok(GAL, '<img src="../images/light-sphere.jpg" alt="نورپردازی میدان شهدای جهان اسلام اصفهان" data-gallery-main-img="true"', '<img src="{ES_G0}" alt="{ES_TITLE}" data-gallery-main-img="true"')
# alt ترسیمی thumbها: «{ES_TITLE} - تصویر N» — N لاتین؛ جایگزینی src عیناً بعد از data-src (altها static نمی‌مانند چون عنوان داینامیک است)
import re as _re_gal
_gal_alts = _re_gal.findall(r'<img src="([^"]+)" alt="([^"]*? - تصویر \d)"', GAL)
assert len(_gal_alts) == 5, _gal_alts
_FAD = '۰۱۲۳۴۵۶۷۸۹'
for n, (src, _alt) in enumerate(_gal_alts):
    _fa_n = ''.join(_FAD[int(d)] for d in str(n + 1))
    GAL = tok(GAL, f'<img src="{src}" alt="{_alt}"', f'<img src="{{ES_G{n}}}" alt="{{ES_TITLE}} - تصویر {_fa_n}"')
# thumbs: پنج data-gallery-src
for n in range(5):
    GAL = tok(GAL, 'data-gallery-src="../images/light-sphere.jpg"' if n == 0 else f'data-gallery-src="../images/pexels/pexels-{["14720175","17789372","35318900","29570545"][n-1]}.jpg"',
              f'data-gallery-src="{{ES_G{n}}}"')

GAL = tok(GAL, 'backdrop-blur-md">نورپردازی میدان</span>', 'backdrop-blur-md">{ES_CHIP}</span>')
GAL = tok(GAL, 'backdrop-blur-md">۱ / ۵</span>', 'backdrop-blur-md">{ES_G_CUR} / {ES_G_TOTAL}</span>')
# sweep: هر تصویر باقی‌مانده گالری (lightbox و ...) → {ES_Gn} بر اساس نام فایل
_GMAP = {'../images/light-sphere.jpg': 0, '../images/pexels/pexels-14720175.jpg': 1, '../images/pexels/pexels-17789372.jpg': 2, '../images/pexels/pexels-35318900.jpg': 3, '../images/pexels/pexels-29570545.jpg': 4}
while '../images/' in GAL:
    _i = GAL.index('../images/')
    _j = GAL.index('"', _i)
    _path = GAL[_i:_j]
    assert _path in _GMAP, _path
    GAL = tok(GAL, _path, '{ES_G%d}' % _GMAP[_path])
assert '../images/' not in GAL
# سایدبار: نوع + شهر لینک‌دار
GAL = tok(GAL, '<a href="../project_type/square-lighting.html" class="text-emerald-700 transition-colors hover:text-emerald-900 hover:underline">نورپردازی میدان</a>', '<a href="{ES_TYPE_URL}" class="text-emerald-700 transition-colors hover:text-emerald-900 hover:underline">{ES_TYPE_NAME}</a>')
GAL = tok(GAL, '<a href="../city/esfahan.html"', '<a href="{ES_CITY_URL}"')
assert GAL.count('>اصفهان</span></a>') >= 1
GAL = tok(GAL, '>اصفهان</span></a>', '>{ES_CITY_NAME}</span></a>')
GAL = tok(GAL, 'text-slate-900">نورپردازی میدان شهدای جهان اسلام اصفهان</h2>', 'text-slate-900">{ES_TITLE}</h2>')
GAL = tok(GAL, '<div class="mt-0.5 truncate text-[11px] sm:text-[11.5px] font-extrabold text-slate-800">شهرداری اصفهان</div>', '<div class="mt-0.5 truncate text-[11px] sm:text-[11.5px] font-extrabold text-slate-800">{ES_CLIENT}</div>')
GAL = tok(GAL, '<div class="mt-0.5 truncate text-[11px] sm:text-[11.5px] font-extrabold text-slate-800">اصفهان</div>', '<div class="mt-0.5 truncate text-[11px] sm:text-[11.5px] font-extrabold text-slate-800">{ES_CITY_NAME}</div>')
GAL = tok(GAL, '<div class="mt-0.5 truncate text-[11px] sm:text-[11.5px] font-extrabold text-slate-800">۱۰ روز (۲۰ تا ۳۰ دی‌ماه ۱۴۰۱)</div>', '<div class="mt-0.5 truncate text-[11px] sm:text-[11.5px] font-extrabold text-slate-800">{ES_DURATION}</div>')
# جستجوی لینک محصولات؟ (نه — سایدبار گالری است)

# ═══════════ TABS ═══════════
TABS = TABS_RAW
TABS = tok(TABS, 'sm:text-2xl">درباره نورپردازی میدان شهدای جهان اسلام اصفهان</h2>', 'sm:text-2xl">درباره {ES_TITLE}</h2>')
# بدنه desc = فقط ردیف پاراگراف‌ها (کول راست گرید)؛ تا شروع کول چپ (جعبه ویژگی static)
COL5 = '<div class="lg:col-span-5">'
d0 = TABS.index('درباره {ES_TITLE}</h2>')
# مختصات RAW:
d0r = TABS_RAW.index('درباره نورپردازی میدان شهدای جهان اسلام اصفهان</h2>')
body_r = d0r + len('درباره نورپردازی میدان شهدای جهان اسلام اصفهان</h2>')
c0r = TABS_RAW.index(COL5, d0r)
DESC_BLOCK = TABS_RAW[body_r:c0r]
# مختصات TABS (توکنیزه‌شده — طول متفاوت):
body_t = d0 + len('درباره {ES_TITLE}</h2>')
c0t = TABS.index(COL5, d0)
assert c0t - body_t == c0r - body_r, 'desc span shift'
assert DESC_BLOCK.endswith('</div></div>'), 'desc block tail'
DESC_BLOCK = DESC_BLOCK[:-len('</div>')]  # بستن کول گرید static می‌ماند
TABS = TABS[:body_t] + '{ES_CONTENT}</div>' + TABS[c0t:]
# specs: مقادیر
TABS = tok(TABS, 'text-slate-900">شناسنامه کامل اجرایی نورپردازی میدان شهدای جهان اسلام اصفهان</h2>', 'text-slate-900">شناسنامه کامل اجرایی {ES_TITLE}</h2>')
TABS = tok(TABS, 'sm:col-span-2">شهرداری اصفهان</div>', 'sm:col-span-2">{ES_CLIENT}</div>')
TABS = tok(TABS, '<a href="../city/esfahan.html" class="inline-flex items-center gap-1.5 text-emerald-800 hover:underline"><span>اصفهان</span>', '<a href="{ES_CITY_URL}" class="inline-flex items-center gap-1.5 text-emerald-800 hover:underline"><span>{ES_CITY_NAME}</span>')
TABS = tok(TABS, '<a href="../project_type/square-lighting.html" class="inline-flex items-center gap-1.5 text-emerald-800 hover:underline"><span>نورپردازی میدان</span>', '<a href="{ES_TYPE_URL}" class="inline-flex items-center gap-1.5 text-emerald-800 hover:underline"><span>{ES_TYPE_NAME}</span>')
TABS = tok(TABS, 'sm:col-span-2">۱۰ روز (۲۰ تا ۳۰ دی‌ماه ۱۴۰۱)</div>', 'sm:col-span-2">{ES_DURATION}</div>')
TABS = tok(TABS, 'sm:col-span-2">۳۰ دی ۱۴۰۱</div>', 'sm:col-span-2">{ES_COMPLETED}</div>')
TABS = tok(TABS, 'sm:col-span-2">عایق‌بندی استاندارد صنعتی IP67 / IP68 · ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش</div>', 'sm:col-span-2">{ES_STANDARD}</div>')

# ═══════════ PRODUCTS ═══════════
CLS = 'mt-1.5 line-clamp-2 text-xs leading-6 text-slate-500'
PDESC = 'کره درخشان با پیکسل\u200cهای فول\u200cکالر و باکس کنترل مرکزی حرفه\u200cای'
PROD = tok(PROD_RAW, '<a href="../shop.html" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900">', '<a href="{ES_SHOP_URL}" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900">')
# کارت اول را توکن کن
c0 = PROD.index('<div custom="0">')
c1 = PROD.index('<div custom="0.1">') if '<div custom="0.1">' in PROD else PROD.index('<div custom="')
marks = []
i = c0
while True:
    j = PROD.find('<div custom="', i)
    if j < 0:
        break
    marks.append(j)
    i = j + 10
# مرز کارت اول تا دوم:
CARD_RAW = PROD[marks[0]:marks[1]]
CARD = tok(CARD_RAW, '<a href="../product/lighting-ball.html"', '<a href="{ES_URL}"')
CARD = tok(CARD, '<div custom="0">', '<div custom="{ES_CUSTOM}">')
CARD = tok(CARD, '<img src="../images/light-sphere.jpg" alt="گوی نورانی هوشمند ۳۶۰ درجه"', '<img src="{ES_IMG}" alt="{ES_TITLE}"')
CARD = tok(CARD, 'backdrop-blur-md">المان های نورپردازی شهری</span>', 'backdrop-blur-md">{ES_CHIP}</span>')
CARD = tok(CARD, 'group-hover:text-emerald-700">گوی نورانی هوشمند ۳۶۰ درجه</h3>', 'group-hover:text-emerald-700">{ES_TITLE}</h3>')
# desc کارت
import re as _re
_md = _re.search(r'<p class="' + CLS + r'">([^<]+)</p>', CARD)
assert _md, 'card desc'
CARD = tok(CARD, _md.group(0), '<p class="' + CLS + '">{ES_DESC}</p>')
e4 = PROD.index('</a>', marks[3]) + len('</a></div>')
C234 = PROD[marks[1]:e4]  # کارت‌های دوم تا چهارم (فقط برای assert بازسازی)
PROD = PROD[:marks[0]] + '{ES_PRODUCT_CARDS}' + PROD[e4:]

# ═══════════ RELATED (کارت لایت پروژه) ═══════════
REL = tok(REL_RAW, '<a href="../shop.html"' if False else '<a href="../projects.html" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900"', '<a href="{ES_PROJECTS_URL}" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900"')
r0 = REL.index('<a href="../projects/ahvaz-semi-pyramid-light.html"')
r1 = REL.index('<a href="../projects/half-arch-tunnel-alborz.html"')
r3 = REL.index('<a href="../projects/tunnel-90-alborz.html"')
e3 = REL.index('</a>', r3) + len('</a>')
C23 = REL[r1:e3]  # کارت‌های دوم و سوم (فقط برای assert بازسازی)
RCARD_RAW = REL[r0:r1]
RC = tok(RCARD_RAW, '<a href="../projects/ahvaz-semi-pyramid-light.html"', '<a href="{ES_URL}"')
RC = tok(RC, '<img src="../images/light-element.jpg" alt="اجرای پروژه نیم هرم نوری اهواز"', '<img src="{ES_IMG}" alt="{ES_TITLE}"')
RC = tok(RC, 'backdrop-blur-md">المان‌های نورانی</span>', 'backdrop-blur-md">{ES_CHIP}</span>')
RC = tok(RC, 'group-hover:text-emerald-700">اجرای پروژه نیم هرم نوری اهواز</h3>', 'group-hover:text-emerald-700">{ES_TITLE}</h3>')
RC = tok(RC, 'line-clamp-2 text-xs leading-6 text-slate-500">طراحی و ساخت سازه حجمی نیم‌هرم نوری پیکسلی با مقاومت کامل در برابر اقلیم گرم و مرطوب اهواز</p>', 'line-clamp-2 text-xs leading-6 text-slate-500">{ES_DESC}</p>')
RC = tok(RC, 'w-3.5 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>اهواز</span>', 'w-3.5 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>{ES_CITY}</span>')
REL = REL[:r0] + '{ES_RELATED_CARDS}' + REL[e3:]

# ═══════════ asserts ═══════════
IMGS = ['../images/light-sphere.jpg', '../images/pexels/pexels-14720175.jpg', '../images/pexels/pexels-17789372.jpg', '../images/pexels/pexels-35318900.jpg', '../images/pexels/pexels-29570545.jpg']
def hero_r():
    return (HERO.replace('{ES_HOME_URL}', '../index.html').replace('{ES_PROJECTS_URL}', '../projects.html')
            .replace('{ES_TYPE_URL}', '../project_type/square-lighting.html').replace('{ES_TYPE_NAME}', 'نورپردازی میدان')
            .replace('{ES_TITLE}', 'نورپردازی میدان شهدای جهان اسلام اصفهان')
            .replace('{ES_BADGE}', 'نورپردازی میدان · اصفهان')
            .replace('{ES_DESC}', 'شاهکاری از هنر و فناوری با گوی‌های نورانی و پیکسل‌های ۴ سانتی فول‌کالر'))
assert hero_r() == HERO_RAW, 'HERO rebuild'
def gal_r():
    out = GAL
    for n, img in enumerate(IMGS):
        out = out.replace('{ES_G%d}' % n, img)
    return (out.replace('{ES_TITLE}', 'نورپردازی میدان شهدای جهان اسلام اصفهان')
            .replace('{ES_CHIP}', 'نورپردازی میدان').replace('{ES_G_CUR}', '۱').replace('{ES_G_TOTAL}', '۵')
            .replace('{ES_TYPE_URL}', '../project_type/square-lighting.html').replace('{ES_TYPE_NAME}', 'نورپردازی میدان')
            .replace('{ES_CITY_URL}', '../city/esfahan.html').replace('{ES_CITY_NAME}', 'اصفهان')
            .replace('{ES_CLIENT}', 'شهرداری اصفهان').replace('{ES_DURATION}', '۱۰ روز (۲۰ تا ۳۰ دی‌ماه ۱۴۰۱)'))
assert gal_r() == GAL_RAW, 'GAL rebuild'
def tabs_r():
    return (TABS.replace('{ES_TITLE}', 'نورپردازی میدان شهدای جهان اسلام اصفهان')
            .replace('{ES_CONTENT}', DESC_BLOCK)
            .replace('{ES_CLIENT}', 'شهرداری اصفهان')
            .replace('{ES_CITY_URL}', '../city/esfahan.html').replace('{ES_CITY_NAME}', 'اصفهان')
            .replace('{ES_TYPE_URL}', '../project_type/square-lighting.html').replace('{ES_TYPE_NAME}', 'نورپردازی میدان')
            .replace('{ES_DURATION}', '۱۰ روز (۲۰ تا ۳۰ دی‌ماه ۱۴۰۱)').replace('{ES_COMPLETED}', '۳۰ دی ۱۴۰۱')
            .replace('{ES_STANDARD}', 'عایق‌بندی استاندارد صنعتی IP67 / IP68 · ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش'))
assert tabs_r() == TABS_RAW, 'TABS rebuild'
def card_r():
    return (CARD.replace('{ES_CUSTOM}', '0').replace('{ES_URL}', '../product/lighting-ball.html')
            .replace('{ES_IMG}', '../images/light-sphere.jpg').replace('{ES_TITLE}', 'گوی نورانی هوشمند ۳۶۰ درجه')
            .replace('{ES_CHIP}', 'المان های نورپردازی شهری')
            .replace('{ES_DESC}', PDESC))
assert card_r() == CARD_RAW, 'CARD rebuild'
def rc_r():
    return (RC.replace('{ES_URL}', '../projects/ahvaz-semi-pyramid-light.html')
            .replace('{ES_IMG}', '../images/light-element.jpg').replace('{ES_TITLE}', 'اجرای پروژه نیم هرم نوری اهواز')
            .replace('{ES_CHIP}', 'المان‌های نورانی')
            .replace('{ES_DESC}', 'طراحی و ساخت سازه حجمی نیم‌هرم نوری پیکسلی با مقاومت کامل در برابر اقلیم گرم و مرطوب اهواز')
            .replace('{ES_CITY}', 'اهواز'))
assert rc_r() == RCARD_RAW, 'RCARD rebuild'
prod_r = PROD.replace('{ES_PRODUCT_CARDS}', CARD_RAW + C234).replace('{ES_SHOP_URL}', '../shop.html')
assert prod_r == PROD_RAW, 'PROD rebuild'
rel_r = REL.replace('{ES_RELATED_CARDS}', RCARD_RAW + C23).replace('{ES_PROJECTS_URL}', '../projects.html')
assert rel_r == REL_RAW, 'REL rebuild'
raw_sum = HERO_RAW + GAL_RAW + TABS_RAW + PROD_RAW + REL_RAW
assert raw_sum == s[m0:m1], 'boundaries drift: %d vs %d' % (len(raw_sum), len(m1 - m0) if False else len(s[m0:m1]))
print('slices OK; hero:', len(HERO_RAW), 'gal:', len(GAL_RAW), 'tabs:', len(TABS_RAW), 'prod:', len(PROD_RAW), 'rel:', len(REL_RAW), 'desc:', len(DESC_BLOCK))

# ═══════════ نوشتن پارشیال‌ها و شل ═══════════
os.makedirs(os.path.join(ROOT, 'template-parts/single-project'), exist_ok=True)

def write(path, php):
    open(os.path.join(ROOT, path), 'w', encoding='utf-8').write(php)
    print(path, 'written:', len(php))

write('template-parts/single-project/hero.php',
"<?php\n/**\n * هیرو سینگل پروژه — برش بایت‌دقیق میرور projects/esfahan-shohada-square.html\n * فرگمنت با temp/gen_p71.py تولید شده است.\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\n"
+ nowdoc('ES_H', HERO, 'ESP71H')
+ "$es_args = $args ?? [];\n"
"echo strtr($ES_H, [\n"
"    '{ES_HOME_URL}'      => esc_url(home_url('/')),\n"
"    '{ES_PROJECTS_URL}'  => esc_url(es_page_url('projects')),\n"
"    '{ES_TYPE_URL}'      => esc_url((string) ($es_args['type_url'] ?? '')),\n"
"    '{ES_TYPE_NAME}'     => esc_html((string) ($es_args['type_name'] ?? '')),\n"
"    '{ES_TITLE}'         => esc_html((string) ($es_args['title'] ?? '')),\n"
"    '{ES_BADGE}'         => esc_html((string) ($es_args['badge'] ?? '')),\n"
"    '{ES_DESC}'          => esc_html((string) ($es_args['desc'] ?? '')),\n"
"]);\n")

write('template-parts/single-project/gallery.php',
"<?php\n/**\n * گالری + سایدبار سینگل پروژه — برش بایت‌دقیق میرور (lightbox)\n * فرگمنت با temp/gen_p71.py تولید شده است.\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\n"
+ nowdoc('ES_G', GAL, 'ESP71G')
+ "$es_args = $args ?? [];\n"
"$es_imgs = (array) ($es_args['imgs'] ?? []);\n"
"$es_tr = [\n"
"    '{ES_TITLE}'   => esc_html((string) ($es_args['title'] ?? '')),\n"
"    '{ES_CHIP}'    => esc_html((string) ($es_args['chip'] ?? '')),\n"
"    '{ES_G_CUR}'   => esc_html('۱'),\n"
"    '{ES_G_TOTAL}' => esc_html(es_fa_num((string) max(1, count($es_imgs)))),\n"
"    '{ES_TYPE_URL}'  => esc_url((string) ($es_args['type_url'] ?? '')),\n"
"    '{ES_TYPE_NAME}' => esc_html((string) ($es_args['type_name'] ?? '')),\n"
"    '{ES_CITY_URL}'  => esc_url((string) ($es_args['city_url'] ?? '')),\n"
"    '{ES_CITY_NAME}' => esc_html((string) ($es_args['city_name'] ?? '')),\n"
"    '{ES_CLIENT}'    => esc_html((string) ($es_args['client'] ?? '')),\n"
"    '{ES_DURATION}'  => esc_html((string) ($es_args['duration'] ?? '')),\n"
"];\n"
"foreach ($es_imgs as $es_i => $es_src) {\n"
"    $es_tr['{ES_G' . $es_i . '}'] = esc_url((string) $es_src);\n"
"}\n"
"echo strtr($ES_G, $es_tr);\n")

write('template-parts/single-project/tabs.php',
"<?php\n/**\n * تب‌های سینگل پروژه (درباره/شناسنامه/فرآیند) — برش بایت‌دقیق میرور\n * فرگمنت با temp/gen_p71.py تولید شده است.\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\n"
+ nowdoc('ES_T', TABS, 'ESP71T')
+ "$es_args = $args ?? [];\n"
"echo strtr($ES_T, [\n"
"    '{ES_TITLE}'     => esc_html((string) ($es_args['title'] ?? '')),\n"
"    '{ES_CONTENT}'   => (string) ($es_args['content'] ?? ''), // the_content — بدون escape\n"
"    '{ES_CLIENT}'    => esc_html((string) ($es_args['client'] ?? '')),\n"
"    '{ES_CITY_URL}'  => esc_url((string) ($es_args['city_url'] ?? '')),\n"
"    '{ES_CITY_NAME}' => esc_html((string) ($es_args['city_name'] ?? '')),\n"
"    '{ES_TYPE_URL}'  => esc_url((string) ($es_args['type_url'] ?? '')),\n"
"    '{ES_TYPE_NAME}' => esc_html((string) ($es_args['type_name'] ?? '')),\n"
"    '{ES_DURATION}'  => esc_html((string) ($es_args['duration'] ?? '')),\n"
"    '{ES_COMPLETED}' => esc_html((string) ($es_args['completed'] ?? '')),\n"
"    '{ES_STANDARD}'  => esc_html((string) ($es_args['standard'] ?? '')),\n"
"]);\n")

write('template-parts/single-project/product-card.php',
"<?php\n/**\n * کارت محصول سینگل پروژه (کارت لایت میرور) — برش بایت‌دقیق\n * فرگمنت با temp/gen_p71.py تولید شده است.\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\n"
+ nowdoc('ES_C', CARD, 'ESP71C')
+ "$es_args = $args ?? [];\n"
"echo strtr($ES_C, [\n"
"    '{ES_CUSTOM}' => esc_attr((string) ($es_args['custom'] ?? '0')),\n"
"    '{ES_URL}'   => esc_url((string) ($es_args['url'] ?? '')),\n"
"    '{ES_IMG}'   => esc_url((string) ($es_args['img'] ?? '')),\n"
"    '{ES_TITLE}' => esc_html((string) ($es_args['title'] ?? '')),\n"
"    '{ES_CHIP}'  => esc_html((string) ($es_args['chip'] ?? '')),\n"
"    '{ES_DESC}'  => esc_html((string) ($es_args['desc'] ?? '')),\n"
"]);\n")

write('template-parts/single-project/related-card.php',
"<?php\n/**\n * کارت لایت پروژه مرتبط — برش بایت‌دقیق میرور\n * فرگمنت با temp/gen_p71.py تولید شده است.\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\n"
+ nowdoc('ES_R', RC, 'ESP71R')
+ "$es_args = $args ?? [];\n"
"echo strtr($ES_R, [\n"
"    '{ES_URL}'   => esc_url((string) ($es_args['url'] ?? '')),\n"
"    '{ES_IMG}'   => esc_url((string) ($es_args['img'] ?? '')),\n"
"    '{ES_TITLE}' => esc_html((string) ($es_args['title'] ?? '')),\n"
"    '{ES_CHIP}'  => esc_html((string) ($es_args['chip'] ?? '')),\n"
"    '{ES_DESC}'  => esc_html((string) ($es_args['desc'] ?? '')),\n"
"    '{ES_CITY}'  => esc_html((string) ($es_args['city'] ?? '')),\n"
"]);\n")

write('template-parts/single-project/products.php',
"<?php\n/**\n * سکشن محصولات پروژه (mortabet_mahsole_2) — برش بایت‌دقیق میرور؛ مخفی اگر خالی\n * فرگمنت با temp/gen_p71.py تولید شده است.\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\n"
+ nowdoc('ES_P', PROD, 'ESP71P')
+ "$es_args = $args ?? [];\n"
"$es_cards = (array) ($es_args['cards'] ?? []);\n"
"if ($es_cards === []) {\n"
"    return; // سکشن خالی مخفی\n"
"}\n"
"echo strtr($ES_P, [\n"
"    '{ES_SHOP_URL}'       => esc_url(es_page_url('shop')),\n"
"    '{ES_PRODUCT_CARDS}'  => (string) $es_args['cards_html'],\n"
"]);\n")

write('template-parts/single-project/related.php',
"<?php\n/**\n * سکشن پروژه‌های مرتبط — برش بایت‌دقیق میرور؛ مخفی اگر خالی\n * فرگمنت با temp/gen_p71.py تولید شده است.\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\n"
+ nowdoc('ES_RP', REL, 'ESP71RP')
+ "$es_args = $args ?? [];\n"
"if ((string) ($es_args['cards_html'] ?? '') === '') {\n"
"    return; // سکشن خالی مخفی\n"
"}\n"
"echo strtr($ES_RP, [\n"
"    '{ES_PROJECTS_URL}'   => esc_url(es_page_url('projects')),\n"
"    '{ES_RELATED_CARDS}'  => (string) $es_args['cards_html'],\n"
"]);\n")

SHELL = r'''<?php
/**
 * سینگل پروژه (single-projects) — برش بایت‌دقیق میرور projects/*.html
 * فرگمنت‌ها با temp/gen_p71.py تولید شده‌اند.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();

$es_id    = (int) get_the_ID();
$es_title = (string) get_the_title($es_id);

// خلاصه: چکیده → برش محتوا (قرارداد کارت‌ها)
$es_excerpt = (string) get_post_field('post_excerpt', $es_id);
if ($es_excerpt === '') {
    $es_excerpt = wp_trim_words(strip_tags((string) get_post_field('post_content', $es_id)), 24);
}

// ═══ ترم‌های city / project_type ═══
$es_terms_c  = get_the_terms($es_id, 'city');
$es_city     = (is_array($es_terms_c) && isset($es_terms_c[0]) && is_object($es_terms_c[0])) ? $es_terms_c[0] : null;
$es_city_slug = $es_city !== null ? (string) $es_city->slug : '';
$es_terms_t  = get_the_terms($es_id, 'project_type');
$es_type     = (is_array($es_terms_t) && isset($es_terms_t[0]) && is_object($es_terms_t[0])) ? $es_terms_t[0] : null;
$es_type_name = $es_type !== null ? (string) $es_type->name : '';
$es_city_name = $es_city !== null ? (string) $es_city->name : '';

$es_term_link = static function (?object $term): string {
    if ($term === null) {
        return '';
    }
    $es_link = get_term_link($term);
    return is_wp_error($es_link) ? '' : (string) $es_link;
};

$es_args_common = [
    'title'     => $es_title,
    'type_url'  => $es_term_link($es_type),
    'type_name' => $es_type_name,
    'city_url'  => $es_term_link($es_city),
    'city_name' => $es_city_name,
];

echo '<main class="bg-slate-50/50">';

// ═══ هیرو ═══
ob_start();
es_get_template('template-parts/single-project/hero.php', array_merge($es_args_common, [
    'badge' => trim($es_type_name . ' · ' . $es_city_name, ' ·'),
    'desc'  => $es_excerpt,
]));
echo (string) ob_get_clean();

// ═══ گالری شش‌تایی (3.12) + سایدبار ═══
$es_gal_ids = class_exists(\ErfanSanat\Theme\Frontend\ProjectGallery::class)
    ? \ErfanSanat\Theme\Frontend\ProjectGallery::get_gallery($es_id) : [];
$es_gal_fallback = es_media_url('assets/images/light-sphere.jpg');
$es_imgs = [];
for ($es_i = 0; $es_i < 5; $es_i++) {
    $es_imgs[] = isset($es_gal_ids[$es_i]) && (int) $es_gal_ids[$es_i] > 0
        ? es_media_url((int) $es_gal_ids[$es_i]) : $es_gal_fallback;
}
ob_start();
es_get_template('template-parts/single-project/gallery.php', array_merge($es_args_common, [
    'imgs'     => $es_imgs,
    'chip'     => $es_type_name,
    'client'   => (string) get_post_meta($es_id, 'es_client', true),
    'duration' => (string) get_post_meta($es_id, 'es_duration', true),
]));
echo (string) ob_get_clean();

// ═══ تب‌ها: درباره (the_content) + شناسنامه + فرآیند ═══
ob_start();
es_get_template('template-parts/single-project/tabs.php', array_merge($es_args_common, [
    'content'   => (string) apply_filters('the_content', (string) get_post_field('post_content', $es_id)),
    'client'    => (string) get_post_meta($es_id, 'es_client', true),
    'duration'  => (string) get_post_meta($es_id, 'es_duration', true),
    'completed' => (string) get_post_meta($es_id, 'finish-pro-time', true),
    'standard'  => (string) get_post_meta($es_id, 'es_standard', true),
]));
echo (string) ob_get_clean();

// ═══ محصولات پروژه (mortabet_mahsole_2 → 3.12) ═══
$es_prod_ids = class_exists(\ErfanSanat\Theme\Frontend\ProjectGallery::class)
    ? \ErfanSanat\Theme\Frontend\ProjectGallery::get_products($es_id) : [];
$es_customs = ['0', '0.06', '0.12', '0.18']; // اتریبیوت design میرور
$es_cards = '';
foreach ($es_prod_ids as $es_k => $es_pid) {
    $es_pid  = (int) $es_pid;
    $es_pexc = (string) get_post_field('post_excerpt', $es_pid);
    if ($es_pexc === '') {
        $es_pexc = wp_trim_words(strip_tags((string) get_post_field('post_content', $es_pid)), 22);
    }
    // تصویر: شاخص → گالری محصول → placeholder میرور (قرارداد card-product)
    $es_thumb = (int) get_post_meta($es_pid, '_thumbnail_id', true);
    if ($es_thumb < 1) {
        $es_gal = (string) get_post_meta($es_pid, '_product_image_gallery', true);
        if ($es_gal === '') {
            $es_gal = (string) get_post_meta($es_pid, 'galerry_produ', true);
        }
        $es_first  = trim(explode(',', $es_gal)[0] ?? '');
        $es_thumb  = ctype_digit($es_first) && (int) $es_first > 0 ? (int) $es_first : 0;
    }
    $es_pterms = get_the_terms($es_pid, 'product_cat');
    ob_start();
    es_get_template('template-parts/single-project/product-card.php', [
        'custom' => $es_customs[$es_k] ?? (string) $es_k,
        'url'   => (string) get_permalink($es_pid),
        'img'   => es_media_url($es_thumb > 0 ? $es_thumb : 'assets/images/light-tunnel.jpg'),
        'title' => (string) get_the_title($es_pid),
        'chip'  => (is_array($es_pterms) && isset($es_pterms[0]) && is_object($es_pterms[0])) ? (string) $es_pterms[0]->name : '',
        'desc'  => $es_pexc,
    ]);
    $es_cards .= (string) ob_get_clean();
}
ob_start();
es_get_template('template-parts/single-project/products.php', ['cards' => $es_prod_ids, 'cards_html' => $es_cards]);
echo (string) ob_get_clean();

// ═══ پروژه‌های مرتبط: هم‌city یا هم‌project_type (کوئری سبک) ═══
$es_tax = [];
if ($es_city_slug !== '') {
    $es_tax[] = ['taxonomy' => 'city', 'field' => 'slug', 'terms' => [$es_city_slug]];
}
if ($es_type !== null) {
    $es_tax[] = ['taxonomy' => 'project_type', 'field' => 'term_id', 'terms' => [(int) $es_type->term_id]];
}
$es_rel = $es_tax === [] ? [] : (array) get_posts([
    'post_type'      => 'projects',
    'posts_per_page' => 3,
    'post__not_in'   => [$es_id],
    'tax_query'      => array_merge(['relation' => 'OR'], $es_tax),
]);
$es_rel_html = '';
foreach ($es_rel as $es_rp) {
    $es_rid  = (int) ($es_rp->ID ?? 0);
    $es_rexc = (string) get_post_field('post_excerpt', $es_rid);
    if ($es_rexc === '') {
        $es_rexc = wp_trim_words(strip_tags((string) get_post_field('post_content', $es_rid)), 20);
    }
    $es_rterms_t = get_the_terms($es_rid, 'project_type');
    $es_rterms_c = get_the_terms($es_rid, 'city');
    ob_start();
    es_get_template('template-parts/single-project/related-card.php', [
        'url'   => (string) get_permalink($es_rid),
        'img'   => es_media_url((int) get_post_meta($es_rid, '_thumbnail_id', true) > 0
            ? (int) get_post_meta($es_rid, '_thumbnail_id', true) : 'assets/images/light-element.jpg'),
        'title' => (string) get_the_title($es_rid),
        'chip'  => (is_array($es_rterms_t) && isset($es_rterms_t[0]) && is_object($es_rterms_t[0])) ? (string) $es_rterms_t[0]->name : '',
        'desc'  => $es_rexc,
        'city'  => (is_array($es_rterms_c) && isset($es_rterms_c[0]) && is_object($es_rterms_c[0])) ? (string) $es_rterms_c[0]->name : '',
    ]);
    $es_rel_html .= (string) ob_get_clean();
}
ob_start();
es_get_template('template-parts/single-project/related.php', ['cards_html' => $es_rel_html]);
echo (string) ob_get_clean();

echo '</main>';
get_footer();
'''
write('single-projects.php', SHELL)
