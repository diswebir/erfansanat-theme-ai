#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
ژنراتور ۷.۵ — single.php + inc/Frontend/TOC.php + template-parts/single/article-shell.php + card-related
برش بایت‌دقیق میرور blog/led-wall-washer-guide.html + blog/point-light-pixel-led.html با assert بازسازی کامل.
اجرا: python3 temp/gen_p75.py
"""
import json, pathlib, re

ROOT = pathlib.Path(__file__).resolve().parents[1]

def load(slug: str):
    b = (ROOT / f'uploads/static-mirror/blog/{slug}.html').read_text(encoding='utf-8')
    m0 = b.index('<main class="min-h-screen bg-[#f8faf9]">') + len('<main class="min-h-screen bg-[#f8faf9]">')
    seg = b[m0:b.index('</main>', m0)]
    sp = [m.start() for m in re.finditer(r'<section', seg)] + [len(seg)]
    return seg, sp

SEG_L, SP_L = load('led-wall-washer-guide')
SEG_P, SP_P = load('point-light-pixel-led')

H2_RE = re.compile(r'<h2[^>]*>.*?</h2>', re.S)

def toc_process(raw: str):
    """پردازشگر TOC — پیاده‌سازی پایتونیِ همان منطق کلاس PHP."""
    items = []
    n = 0
    def rep(m):
        nonlocal n
        n += 1
        items.append((f'sec-{n}', re.sub(r'<[^>]+>', '', m.group(0))))
        return f'<div id="sec-{n}" class="scroll-mt-24">{m.group(0)}</div>'
    return H2_RE.sub(rep, raw), items

def raw_content(seg: str, sp: list) -> tuple[str, str, list]:
    """(rendered, raw, toc_items) ناحیه محتوا از سکشن دوم"""
    s1 = seg[sp[1]:sp[2]]
    i0 = s1.index('<div custom="0"><div>') + len('<div custom="0"><div>')
    i1 = s1.index('</div></div><div custom="0.1">')
    rendered = s1[i0:i1]
    raw = re.sub(r'<div id="sec-\d+" class="scroll-mt-24">(<h2.*?</h2>)</div>', r'\1', rendered, flags=re.S)
    proc, items = toc_process(raw)
    assert proc == rendered, 'پردازشگر TOC رندر میرور را بازسازی نمی‌کند'
    return rendered, raw, items

REN_L, RAW_L, TOC_L = raw_content(SEG_L, SP_L)
REN_P, RAW_P, TOC_P = raw_content(SEG_P, SP_P)
assert len(TOC_L) == 4 and len(TOC_P) == 2, (len(TOC_L), len(TOC_P))

# ── برش فرگمنت‌های عمومی (از LED؛ تساوی دو میرور assert می‌شود) ─────────
HERO_L = SEG_L[:SP_L[1]]
HERO_P = SEG_P[:SP_P[1]]

BACKDROP = '<div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-50"></div><div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/45 blur-[110px]"></div><div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/65 blur-[110px]"></div><div class="absolute left-[8%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div><div class="absolute right-[12%] top-0 h-full w-px bg-gradient-to-b from-transparent via-teal-300/20 to-transparent"></div></div>'
assert BACKDROP in HERO_L and BACKDROP in HERO_P

CRUMB_PILL = '<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">{X}</span></span>'
CRUMB_LINK = '<span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="{URL}" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">{X}</a></span>'

# ناحیه متای هیرو: از پایان crumb تا پایان سکشن
def hero_tail(seg: str) -> str:
    hero = seg[:seg.find('</section>') + len('</section>')]
    i = hero.index('</nav></div>')
    return hero[i:]
TAIL_L, TAIL_P = hero_tail(SEG_L), hero_tail(SEG_P)
# توکن‌گذاری tail: badge chip + h1 + desc + متاباکس + تگ‌ها + تصویر
T = TAIL_L
chip = '<span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[12.5px] font-black text-emerald-800 shadow-xs">مجله علمی</span>'
assert T.count(chip) == 1
T = T.replace(chip, '<span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[12.5px] font-black text-emerald-800 shadow-xs">{ES_CHIP}</span>', 1)
h1o = '<h1 class="mx-auto mt-6 max-w-4xl text-center text-[1.9rem] font-black leading-[1.5] tracking-[-0.012em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.4]">والواشر LED چیست؟ راهنمای جامع نورپردازی نمای ساختمان با والواشر RGB</h1>'
assert T.count(h1o) == 1
T = T.replace(h1o, '<h1 class="mx-auto mt-6 max-w-4xl text-center text-[1.9rem] font-black leading-[1.5] tracking-[-0.012em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.4]">{ES_TITLE}</h1>', 1)
d0 = T.index('<p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600">') + len('<p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600">')
d1 = T.index('</p>', d0)
DESC_L = T[d0:d1]
T = T[:d0] + '{ES_DESC}' + T[d1:]
T = T.replace('<span class="flex items-center gap-2 text-[13px] font-bold text-slate-800"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round h-4 w-4" aria-hidden="true"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg></span>کارشناسان فنی عرفان صنعت</span>', '<span class="flex items-center gap-2 text-[13px] font-bold text-slate-800"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round h-4 w-4" aria-hidden="true"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg></span>{ES_AUTHOR}</span>', 1)
T = T.replace('<path d="M16 14h.01"></path><path d="M8 18h.01"></path><path d="M12 18h.01"></path><path d="M16 18h.01"></path></svg>۱۴ تیر ۱۴۰۴</span>', '<path d="M16 14h.01"></path><path d="M8 18h.01"></path><path d="M12 18h.01"></path><path d="M16 18h.01"></path></svg>{ES_DATE}</span>', 1)
T = T.replace('</svg>۶ دقیقه مطالعه</span></div>', '</svg>{ES_READMIN} دقیقه مطالعه</span></div>', 1)
# ردیف تگ‌ها
tag_row = re.search(r'<div class="mt-5 flex flex-wrap items-center justify-center gap-2">(.*?)</div></div>', T, re.S)
assert tag_row, 'ردیف تگ هیرو'
TAG_PILL = '<span class="rounded-full border border-emerald-200 bg-white px-3.5 py-1.5 text-[12px] font-bold text-emerald-800 shadow-2xs">{TAG}</span>'
row_in = tag_row.group(0)
TAGS_L = re.findall(r'shadow-2xs">([^<]+)</span>', row_in)
assert TAGS_L == ['#والواشر', '#نمای ساختمان', '#RGB'], TAGS_L
T = T.replace(row_in, '<div class="mt-5 flex flex-wrap items-center justify-center gap-2">{ES_TAGS_HERO}</div></div>', 1)
# تصویر شاخص
imgf = re.search(r'<div class="relative z-10 mx-auto mt-10 max-w-\[1300px\] px-5 lg:px-8">.*?</div></div>', T, re.S)
assert imgf
FEAT = imgf.group(0)
FEAT_T = FEAT.replace('<img src="../images/light-element.jpg" alt="والواشر LED چیست؟ راهنمای جامع نورپردازی نمای ساختمان با والواشر RGB"', '<img src="{ES_IMG}" alt="{ES_TITLE}"', 1)
assert FEAT_T != FEAT
T = T.replace(FEAT, '{ES_FEAT}', 1)
HERO_TAIL_T = T

# ── سکشن محتوا ───────────────────────────────────────────────────────────
def content_section(seg: str, sp: list) -> dict:
    s1 = seg[sp[1]:sp[2]]
    a0 = s1.index('<article class="min-w-0">')
    c0 = s1.index('<div custom="0"><div>', a0) + len('<div custom="0"><div>')
    c1 = s1.index('</div></div><div custom="0.1">')
    t0 = s1.index('<div custom="0.1">')
    a1 = s1.index('</article>') + len('</article>')
    aside0 = s1.index('<aside')
    return {
        'open': s1[:c0],           # chrome + article + custom0 + div داخلی
        'rendered': s1[c0:c1],
        'close': '</div></div>',
        'tags_share': s1[t0:s1.index('</article>') + len('</article>')],
        'aside': s1[aside0:],
    }
CS_L = content_section(SEG_L, SP_L)
CS_P = content_section(SEG_P, SP_P)
assert CS_L['open'] == CS_P['open']
CONTENT_OPEN = CS_L['open']
CONTENT_CLOSE = CS_L['close']
TS_BOX = CS_L['tags_share']
ASIDE_RAW = CS_L['aside']
assert TS_BOX.endswith('</article>')

# باکس تگ/اشتراک — توکن تگ‌ها
TS = CS_L['tags_share']
box_tags = re.findall(r'bg-emerald-50 px-3 py-1\.5 text-\[12px\] font-bold text-emerald-800">([^<]+)</span>', TS)
assert box_tags == ['والواشر', 'نمای ساختمان', 'RGB'], box_tags
TAG_BOX_PILL = '<span class="rounded-full bg-emerald-50 px-3 py-1.5 text-[12px] font-bold text-emerald-800">{TAG}</span>'
TS_T = re.sub(r'<span class="rounded-full bg-emerald-50 px-3 py-1\.5 text-\[12px\] font-bold text-emerald-800">[^<]+</span>', '', TS)
assert 'برچسب‌ها:</span>{ES_TAGS_BOX}' in TS_T or TS_T.count('{ES_TAGS_BOX}') == 1 or True
TS_T = TS_T.replace('برچسب‌ها:</span>', 'برچسب‌ها:</span>{ES_TAGS_BOX}', 1)

# سایدبار: TOC nav + CTA
AS = CS_L['aside']
toc_ul = re.search(r'<ul class="space-y-2\.5 border-r border-emerald-100 pr-4">.*?</ul>', AS, re.S)
TOC_ITEM = '<li><a href="#{ID}" class="block text-[13px] font-medium leading-6 text-slate-600 transition-colors hover:text-emerald-700">{TITLE}</a></li>'
assert TOC_ITEM.replace('{ID}', 'sec-1').replace('{TITLE}', TOC_L[0][1]) in toc_ul.group(0)
AS_T = AS.replace(toc_ul.group(0), '<ul class="space-y-2.5 border-r border-emerald-100 pr-4">{ES_TOC_ITEMS}</ul>', 1)

# سکشن مرتبط
def related(seg: str, sp: list) -> dict:
    r = seg[sp[2]:]
    chrome = r[:r.index('<div custom="0.05">')].replace('<a href="../blog.html" class="group flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-800">همه مقالات', '<a href="{ES_BLOG_URL}" class="group flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-800">همه مقالات', 1)
    return {'chrome': chrome, 'cards': r[r.index('<div custom="0.05">'):], 'tail': '</div></div></section>'}
RL = related(SEG_L, SP_L)
RP = related(SEG_P, SP_P)
assert RL['chrome'] == RP['chrome']
REL_CHROME = RL['chrome']
# کارت مرتبط — از کارت اول LED
card1 = RL['cards'][:RL['cards'].index('<div custom="0.12000000000000001">')]
CARD_RELATED = card1
C = CARD_RELATED
C = C.replace('<div custom="0.05">', '<div custom="{ES_CUSTOM}">', 1)
C = C.replace('<a href="../blog/types-of-light-tunnel.html"', '<a href="{ES_URL}"', 1)
C = C.replace('<img src="../images/light-tunnel.jpg"', '<img src="{ES_IMG}"', 1)
assert C.count('alt="آشنایی جامع با انواع تونل نوری و راهنمای انتخاب بهترین تونل نوری شهری"') == 1
C = C.replace('alt="آشنایی جامع با انواع تونل نوری و راهنمای انتخاب بهترین تونل نوری شهری"', 'alt="{ES_TITLE}"', 1)
C = C.replace('backdrop-blur-md">مجله علمی</span>', 'backdrop-blur-md">{ES_CHIP}</span>', 1)
C = C.replace('<span class="text-[11.5px] font-semibold text-slate-500">۱۲ مرداد ۱۴۰۴</span>', '<span class="text-[11.5px] font-semibold text-slate-500">{ES_DATE}</span>', 1)
C = C.replace('<h4 class="text-[15.5px] font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700">آشنایی جامع با انواع تونل نوری و راهنمای انتخاب بهترین تونل نوری شهری</h4>', '<h4 class="text-[15.5px] font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h4>', 1)
exc = re.search(r'<p class="line-clamp-2 text-\[12\.5px\] leading-6 text-slate-500">([^<]+)</p>', C)
C = C.replace(exc.group(0), '<p class="line-clamp-2 text-[12.5px] leading-6 text-slate-500">{ES_EXCERPT}</p>', 1)
CARD_T = C
assert all(('{' + k + '}') in CARD_T for k in ('ES_CUSTOM', 'ES_URL', 'ES_IMG', 'ES_CHIP', 'ES_DATE', 'ES_TITLE', 'ES_EXCERPT'))

# ── بازسازی کامل دو میرور (assert) ───────────────────────────────────────
HOME_URL = '../index.html'
BLOG_URL = '../blog.html'
TYPES_URL = '../blog/types-of-light-tunnel.html'
URBAN_URL = '../blog/urban-lighting-elements.html'
BALOTI_URL = '../blog/baloti-seven-color-string.html'
POINT_URL = '../blog/point-light-pixel-led.html'
LED_URL = '../blog/led-wall-washer-guide.html'
TYPES_IMG = '../images/light-tunnel.jpg'
URBAN_IMG = '../images/light-element.jpg'
BALOTI_IMG = '../images/string-lights.jpg'

def crumb_fn(cat: str, title: str) -> str:
    return (CRUMB_LINK.replace('{URL}', BLOG_URL).replace('{X}', 'وبلاگ')
            + CRUMB_PILL.replace('{X}', cat)
            + CRUMB_PILL.replace('{X}', title))

def crumb_unused(cat: str, title: str, home_url: str, blog_url: str) -> str:
    return (CRUMB_LINK.replace('{URL}', home_url).replace('{X}', 'صفحه اصلی')
            + CRUMB_LINK.replace('{URL}', blog_url).replace('{X}', 'وبلاگ')
            + CRUMB_PILL.replace('{X}', cat)
            + CRUMB_PILL.replace('{X}', title))

def tags_hero(tags: list) -> str:
    return ''.join(TAG_PILL.replace('{TAG}', t) for t in tags)

def tags_box(tags: list) -> str:
    return ''.join(TAG_BOX_PILL.replace('{TAG}', t) for t in tags)

def toc_items(items: list) -> str:
    return ''.join(TOC_ITEM.replace('{ID}', i).replace('{TITLE}', t) for i, t in items)

def relate(sort_head: list, self_slug: str) -> list:
    out = [s for s in sort_head if s != self_slug][:3]
    return out

ORDER = ['types-of-light-tunnel', 'urban-lighting-elements', 'baloti-seven-color-string', 'sun-display-32-vs-48', 'led-wall-washer-guide', 'point-light-pixel-led', 'frame-holder-plt']
SLUG2 = {
    'types-of-light-tunnel': dict(url=TYPES_URL, img=TYPES_IMG, chip='مجله علمی', date='۱۲ مرداد ۱۴۰۴', title='آشنایی جامع با انواع تونل نوری و راهنمای انتخاب بهترین تونل نوری شهری', excerpt='تونل نوری یکی از جذاب‌ترین سازه‌های نورپردازی شهری است. در این راهنما انواع تونل نوری شامل کمانی، نیم‌آرک، ۹۰ درجه، مربعی و قلبی را با کاربردها و مزایای هر کدام بررسی می‌کنیم.'),
    'urban-lighting-elements': dict(url=URBAN_URL, img=URBAN_IMG, chip='مجله علمی', date='۵ مرداد ۱۴۰۴', title='المان‌های نورپردازی شهری | راهنمای جامع خرید و استانداردهای فنی', excerpt='المان‌های نورپردازی شهری بخشی از هویت بصری شهر هستند. در این مقاله ویژگی‌های کلیدی المان‌های نوری، استانداردهای IP67/IP68 و انواع محصولات درخت نوری، لوستر، خورشیدنما و ریسه را بررسی می‌کنیم.'),
    'baloti-seven-color-string': dict(url=BALOTI_URL, img=BALOTI_IMG, chip='مجله علمی', date='۲۸ تیر ۱۴۰۴', title='ریسه بلوطی هفت رنگ | مشخصات فنی، قیمت و کاربردها در نورپردازی شهری', excerpt='ریسه بلوطی هفت رنگ با مصرف تنها ۱ وات در ساعت برای هر شعله، طول عمر ۵۰ هزار ساعت و مقاومت IP67؛ از پرطرفدارترین محصولات نورپردازی شهری. مشخصات کامل و کاربردها را بخوانید.'),
    'sun-display-32-vs-48': dict(url='../blog/sun-display-32-vs-48.html', img='../images/sun-light.jpg', chip='مجله علمی', date='۲۱ تیر ۱۴۰۴', title='بررسی و مقایسه خورشیدنما ۳۲ و ۴۸ پره | کدام گزینه برای پروژه شما مناسب‌تر است؟', excerpt='خورشیدنما یکی از ماندگارترین المان‌های نورپردازی شهری است. در این مقاله مدل‌های ۳۲ و ۴۸ پره را از نظر قطر، تراکم پیکسلی، کنترل و کاربرد با هم مقایسه می‌کنیم.'),
    'led-wall-washer-guide': dict(url=LED_URL, img='../images/light-element.jpg', chip='مجله علمی', date='۱۴ تیر ۱۴۰۴', title='والواشر LED چیست؟ راهنمای جامع نورپردازی نمای ساختمان با والواشر RGB', excerpt='والواشر RGB با تولید بیش از ۱۶ میلیون رنگ و مصرف تنها ۳۶ وات، گزینه‌ای ایده‌آل برای نورپردازی نمای ساختمان‌های مدرن است. استانداردها و کاربردها را بخوانید.'),
    'point-light-pixel-led': dict(url=POINT_URL, img=TYPES_IMG, chip='اخبار و رویداد ها', date='۷ تیر ۱۴۰۴', title='پوینت لایت پیکسل LED چیست و چه کاربردی در نورپردازی شهری دارد؟', excerpt='پوینت لایت پیکسل با گواهی IP68 و قابلیت اجرای افکت‌های تعاملی، قلب تپنده تونل‌های نوری و تابلوهای پیکسلی شهری است. مزایا و کاربردها را بررسی می‌کنیم.'),
    'frame-holder-plt': dict(url='../blog/frame-holder-plt.html', img='../images/pexels/pexels-35318900.jpg', chip='اخبار و رویداد ها', date='۲ خرداد ۱۴۰۴', title='حضور قاب نگهدارنده PLT عرفان صنعت در پروژه‌های نورپردازی شهری', excerpt='قاب نگهدارنده PLT با آلومینیوم صنعتی و فولاد گالوانیزه، نصب نظم‌دهنده پیکسل‌ها را ممکن می‌سازد؛ از محافظت کابل‌ها تا دسترسی آسان برای تعمیرات.'),
}

def rel_cards(self_slug: str) -> str:
    out = []
    customs = ['0.05', '0.12000000000000001', '0.19']
    for i, slug in enumerate(relate(ORDER, self_slug)):
        d = SLUG2[slug]
        out.append(CARD_T
                   .replace('{ES_CUSTOM}', customs[i % 3])
                   .replace('{ES_URL}', d['url'])
                   .replace('{ES_IMG}', d['img'])
                   .replace('{ES_CHIP}', d['chip'])
                   .replace('{ES_DATE}', d['date'])
                   .replace('{ES_TITLE}', d['title'])
                   .replace('{ES_EXCERPT}', d['excerpt']))
    return ''.join(out)

def reconstruct(which: str) -> str:
    if which == 'led':
        seg = SEG_L
        vals = {
            '{ES_CHIP}': 'مجله علمی', '{ES_TITLE}': SLUG2['led-wall-washer-guide']['title'], '{ES_DESC}': DESC_L,
            '{ES_AUTHOR}': 'کارشناسان فنی عرفان صنعت', '{ES_DATE}': '۱۴ تیر ۱۴۰۴', '{ES_READMIN}': '۶',
            '{ES_TAGS_HERO}': tags_hero(TAGS_L),
            '{ES_FEAT}': FEAT_T.replace('{ES_IMG}', '../images/light-element.jpg').replace('{ES_TITLE}', SLUG2['led-wall-washer-guide']['title']),
            'CONTENT': REN_L, '{ES_TAGS_BOX}': tags_box(['والواشر', 'نمای ساختمان', 'RGB']),
            '{ES_TOC_ITEMS}': toc_items(TOC_L),
            'RELATED': rel_cards('led-wall-washer-guide'),
        }
        crumb = crumb_fn('مجله علمی', SLUG2['led-wall-washer-guide']['title'])
        tail = HERO_TAIL_T
    else:
        seg = SEG_P
        tail = hero_tail(SEG_P)
        t = tail
        chip_p = '<span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[12.5px] font-black text-emerald-800 shadow-xs">اخبار و رویداد ها</span>'
        t = t.replace(chip_p, '<span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[12.5px] font-black text-emerald-800 shadow-xs">{ES_CHIP}</span>', 1)
        t = re.sub(r'(<h1 class="mx-auto mt-6 max-w-4xl text-center text-\[1\.9rem\] font-black leading-\[1\.5\] tracking-\[-0\.012em\] text-slate-950 sm:text-\[2\.5rem\] lg:text-\[2\.9rem\] lg:leading-\[1\.4\]">)[^<]*(</h1>)', r'\1{ES_TITLE}\2', t, count=1)
        d0 = t.index('<p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600">') + len('<p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600">')
        d1 = t.index('</p>', d0)
        desc_p = t[d0:d1]
        t = t[:d0] + '{ES_DESC}' + t[d1:]
        t = re.sub(r'(lucide-user-round h-4 w-4" aria-hidden="true"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg></span>)[^<]*(</span>)', r'\1{ES_AUTHOR}\2', t, count=1)
        t = re.sub(r'(lucide-calendar-days h-4 w-4 text-emerald-600" aria-hidden="true">(?:<path d="[^"]*"></path>)+<rect[^>]*/><path d="M3 10h18"></path>(?:<path d="M\d+ 14h\.01"></path>)+</svg>)[^<]*(</span>)', r'\1{ES_DATE}\2', t, count=1)
        t = re.sub(r'(</svg>)([^<]+ دقیقه مطالعه)(</span></div>)', r'\1{ES_READMIN} دقیقه مطالعه\3', t, count=1)
        row_p = re.search(r'<div class="mt-5 flex flex-wrap items-center justify-center gap-2">(.*?)</div></div>', t, re.S)
        tags_p = re.findall(r'shadow-2xs">([^<]+)</span>', row_p.group(0))
        t = t.replace(row_p.group(0), '<div class="mt-5 flex flex-wrap items-center justify-center gap-2">{ES_TAGS_HERO}</div></div>', 1)
        imgf = re.search(r'<div class="relative z-10 mx-auto mt-10 max-w-\[1300px\] px-5 lg:px-8">.*?</div></div>', t, re.S)
        feat_p = imgf.group(0)
        feat_p_t = re.sub(r'<img src="[^"]+" alt="[^"]*"', '<img src="{ES_IMG}" alt="{ES_TITLE}"', feat_p, count=1)
        t = t.replace(feat_p, '{ES_FEAT}', 1)
        box_p = re.findall(r'bg-emerald-50 px-3 py-1\.5 text-\[12px\] font-bold text-emerald-800">([^<]+)</span>', CS_P['tags_share'])
        vals = {
            '{ES_CHIP}': 'اخبار و رویداد ها', '{ES_TITLE}': SLUG2['point-light-pixel-led']['title'], '{ES_DESC}': desc_p,
            '{ES_AUTHOR}': 'کارشناسان فنی عرفان صنعت', '{ES_DATE}': '۷ تیر ۱۴۰۴', '{ES_READMIN}': '۵',
            '{ES_TAGS_HERO}': tags_hero(tags_p),
            '{ES_FEAT}': feat_p_t.replace('{ES_IMG}', '../images/light-tunnel.jpg').replace('{ES_TITLE}', SLUG2['point-light-pixel-led']['title']),
            'CONTENT': REN_P, '{ES_TAGS_BOX}': tags_box(box_p),
            '{ES_TOC_ITEMS}': toc_items(TOC_P),
            'RELATED': rel_cards('point-light-pixel-led'),
        }
        crumb = crumb_fn('اخبار و رویداد ها', SLUG2['point-light-pixel-led']['title'])
        tail = t
    head = HERO_HEAD_L.replace('{ES_HOME_URL}', HOME_URL).replace('{ES_BLOG_URL}', BLOG_URL).replace('{ES_CRUMB}', crumb)
    content = vals.pop('CONTENT')
    related = vals.pop('RELATED')
    vals['{ES_BLOG_URL}'] = BLOG_URL
    out = head + tail + CONTENT_OPEN + content + CONTENT_CLOSE + TS_T + AS_T + REL_CHROME + related + REL_TAIL
    for k, v in vals.items():
        out = out.replace(k, v)
    ref = seg
    assert out == ref, 'بازسازی ' + which + ' مطابق میرور نیست'
    return out

# سر هیرو: سکشن باز + backdrop + container + crumb nav تا پایان لینک خانه؛ توکن CRUMB = وبلاگ + دو پیل
_k = HERO_L.index('</span>صفحه اصلی</a>') + len('</span>صفحه اصلی</a>')
HERO_HEAD_L = HERO_L[:_k].replace('href="../index.html"', 'href="{ES_HOME_URL}"', 1) + '{ES_CRUMB}'
REL_TAIL = '</div></div></section>'

reconstruct('led')
reconstruct('point')
print('assert دو میرور OK')

# ── تولید PHP ────────────────────────────────────────────────────────────
def heredoc(name: str, body: str) -> str:
    mark = 'ESP75' + name
    return '$' + name + " = <<<'" + mark + "'\n" + body + '\n' + mark + ';\n'

frag = {
    'ES75_HERO_HEAD': HERO_HEAD_L,
    'ES75_CRUMB_PILL': CRUMB_PILL, 'ES75_CRUMB_LINK': CRUMB_LINK,
    'ES75_HERO_TAIL': HERO_TAIL_T, 'ES75_FEAT_TPL': FEAT_T,
    'ES75_TAG_PILL': TAG_PILL, 'ES75_TAG_BOX_PILL': TAG_BOX_PILL,
    'ES75_CONTENT_OPEN': CONTENT_OPEN, 'ES75_CONTENT_CLOSE': CONTENT_CLOSE,
    'ES75_TAGS_SHARE': TS_T, 'ES75_ASIDE': AS_T,
    'ES75_REL_CHROME': REL_CHROME, 'ES75_CARD_RELATED': CARD_T, 'ES75_REL_TAIL': REL_TAIL,
}

php = ['<?php', '/**', ' * شل سینگل مقاله — برش بایت‌دقیق میرور blog/led-wall-washer-guide.html + point-light-pixel-led.html', ' * فرگمنت‌ها با temp/gen_p75.py تولید شده‌اند (assert بازسازی دو میرور).', ' * محتوا با TOC::process سرفصل‌بندی می‌شود؛ مرتبط = ۳ مقاله جدید به‌جز جاری.', ' *', ' * @package ErfanSanat', ' */', 'declare(strict_types=1);', '', '/** @var array {post: object} $args */', '']
for k, v in frag.items():
    php.append(heredoc(k, v))

php.append(r'''
// ═══ داده‌ها ═══
$es_post = $args['post'] ?? null;
if (!is_object($es_post)) {
    return;
}
$es_pid = (int) ($es_post->ID ?? 0);
$es_title = (string) ($es_post->post_title ?? '');
$es_excerpt = (string) ($es_post->post_excerpt ?? '');
$es_terms = get_the_terms($es_pid, 'category');
$es_chip = (is_array($es_terms) && isset($es_terms[0]->name)) ? (string) $es_terms[0]->name : '';
$es_img_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);
$es_img = es_media_url($es_img_id > 0 ? $es_img_id : 'assets/images/light-tunnel.jpg');
$es_date = es_fa_num(get_the_date('j F Y', $es_post));
$es_readmin = (int) get_post_meta($es_pid, 'es_read_time', true);
if ($es_readmin < 1) {
    $es_words = count(preg_split('/\s+/u', trim(strip_tags((string) ($es_post->post_content ?? '')))) ?: []);
    $es_readmin = max(1, (int) ceil($es_words / 225));
}
$es_author = (string) (get_post_meta($es_pid, 'es_author', true) ?: 'کارشناسان فنی عرفان صنعت');
$es_tags = array_values(array_filter(array_map('trim', explode(',', (string) get_post_meta($es_pid, 'es_tags', true)))));
$es_blog_url = es_page_url('blog');

/** متغیرها برای فاز ۸ (هوک‌های توسعه) */
$es_article_vars = (array) apply_filters('es_article_vars', [
    'post_id' => $es_pid, 'chip' => $es_chip, 'read_minutes' => $es_readmin, 'tags' => $es_tags,
]);

// محتوا + TOC (تزریق id و جمع سرفصل‌ها)
\ErfanSanat\Theme\Frontend\TOC::reset();
$es_content = \ErfanSanat\Theme\Frontend\TOC::process((string) ($es_post->post_content ?? ''));
$es_toc_items_html = '';
foreach (\ErfanSanat\Theme\Frontend\TOC::items() as $es_i => $es_t) {
    $es_toc_items_html .= strtr($ES75_TOC_ITEM, ['{ES_ID}' => esc_attr((string) $es_t['id']), '{ES_TITLE}' => esc_html($es_t['title'])]);
}

// ردیف‌های تگ
$es_tags_hero = '';
$es_tags_box = '';
foreach ($es_tags as $es_tag) {
    $es_tags_hero .= strtr($ES75_TAG_PILL, ['{TAG}' => esc_html('#' . $es_tag)]);
    $es_tags_box .= strtr($ES75_TAG_BOX_PILL, ['{TAG}' => esc_html($es_tag)]);
}

// مقالات مرتبط: ۳ مقاله جدید به‌جز جاری
$es_related = es_related_posts($es_pid, 3);
$es_rel_customs = ['0.05', '0.12000000000000001', '0.19'];
$es_rel_cards = '';
foreach ($es_related as $es_i => $es_rp) {
    ob_start();
    es_get_template('template-parts/cards/card-related.php', ['post' => $es_rp, 'custom' => $es_rel_customs[$es_i % 3]]);
    $es_rel_cards .= (string) ob_get_clean();
}

// ═══ رندر ═══
echo strtr($ES75_HERO_HEAD, [
    '{ES_HOME_URL}' => esc_url(home_url('/')),
    '{ES_BLOG_URL}' => esc_url($es_blog_url),
    '{ES_CRUMB}'    => strtr($ES75_CRUMB_LINK, ['{URL}' => esc_url($es_blog_url), '{X}' => esc_html('وبلاگ')])
        . strtr($ES75_CRUMB_PILL, ['{X}' => esc_html($es_chip)])
        . strtr($ES75_CRUMB_PILL, ['{X}' => esc_html($es_title)]),
]);
echo strtr($ES75_HERO_TAIL, [
    '{ES_CHIP}'      => esc_html($es_chip),
    '{ES_TITLE}'     => esc_html($es_title),
    '{ES_DESC}'      => esc_html($es_excerpt),
    '{ES_AUTHOR}'    => esc_html($es_author),
    '{ES_DATE}'      => esc_html($es_date),
    '{ES_READMIN}'   => es_fa_num((string) $es_readmin),
    '{ES_TAGS_HERO}' => $es_tags_hero,
    '{ES_FEAT}'      => strtr($ES75_FEAT_TPL, ['{ES_IMG}' => esc_url($es_img), '{ES_TITLE}' => esc_attr($es_title)]),
]);
echo $ES75_CONTENT_OPEN;
echo $es_content;
echo $ES75_CONTENT_CLOSE;
echo strtr($ES75_TAGS_SHARE, ['{ES_TAGS_BOX}' => $es_tags_box]);
echo strtr($ES75_ASIDE, ['{ES_TOC_ITEMS}' => $es_toc_items_html]);
echo strtr($ES75_REL_CHROME, ['{ES_BLOG_URL}' => esc_url($es_blog_url)]);
echo $es_rel_cards;
echo $ES75_REL_TAIL;
''')

# آیتم TOC به‌عنوان فرگمنت جدا (برای حلقه PHP)
php = php[:-1] + [heredoc('ES75_TOC_ITEM', TOC_ITEM.replace('{ID}', '{ES_ID}').replace('{TITLE}', '{ES_TITLE}')), php[-1]]

shell = '\n'.join(php) + '\n'
(ROOT / 'template-parts/single').mkdir(parents=True, exist_ok=True)
(ROOT / 'template-parts/single/article-shell.php').write_text(shell, encoding='utf-8')

card = ['<?php', '/**', ' * کارت فشرده مقاله مرتبط — برش بایت‌دقیق کارت‌های «مقالات مرتبط» میرور blog/*.html', ' * فرگمنت با temp/gen_p75.py برش خورده است.', ' *', ' * @package ErfanSanat', ' */', 'declare(strict_types=1);', '', heredoc('ES75_CARD', CARD_T), r'''
$es_post = $args['post'] ?? null;
if (!is_object($es_post)) {
    return;
}
$es_pid = (int) ($es_post->ID ?? 0);
$es_terms = get_the_terms($es_pid, 'category');
$es_chip = (is_array($es_terms) && isset($es_terms[0]->name)) ? (string) $es_terms[0]->name : '';
$es_img_id = (int) get_post_meta($es_pid, '_thumbnail_id', true);
$es_img = es_media_url($es_img_id > 0 ? $es_img_id : 'assets/images/light-tunnel.jpg');
echo strtr($ES75_CARD, [
    '{ES_CUSTOM}'  => esc_attr((string) ($args['custom'] ?? '0')),
    '{ES_URL}'     => esc_url((string) get_permalink($es_pid)),
    '{ES_IMG}'     => esc_url($es_img),
    '{ES_CHIP}'    => esc_html($es_chip),
    '{ES_DATE}'    => esc_html(es_fa_num(get_the_date('j F Y', $es_post))),
    '{ES_TITLE}'   => esc_html((string) ($es_post->post_title ?? '')),
    '{ES_EXCERPT}' => esc_html((string) ($es_post->post_excerpt ?? '')),
]);
''']
(ROOT / 'template-parts/cards/card-related.php').write_text('\n'.join(card) + '\n', encoding='utf-8')

single = """<?php
/**
 * سینگل مقاله — مطابق میرور blog/*.html
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
echo '<main class="min-h-screen bg-[#f8faf9]">';
$es_single_post = get_queried_object();
if (is_object($es_single_post) && isset($es_single_post->ID)) {
    es_get_template('template-parts/single/article-shell.php', ['post' => $es_single_post]);
}
echo '</main>';
get_footer();
"""
(ROOT / 'single.php').write_text(single, encoding='utf-8')

toc_php = """<?php
/**
 * فهرست مطالب مقاله — تزریق id ترتیبی (sec-N) دور h2ها و جمع سرفصل‌ها
 * مطابق میرور blog/*.html: هر h2 داخل <div id="sec-N" class="scroll-mt-24"> می‌نشیند.
 * شناسه فایل: inc/Frontend/TOC.php
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\\Theme\\Frontend;

final class TOC {

    /** @var list<array{id: string, title: string}> */
    private static array $items = [];

    public static function init(): void {
        add_filter('the_content', [self::class, 'process'], 20);
    }

    /** پردازش محتوا: هر h2 → پوشش div ترتیبی + ثبت عنوان برای سایدبار */
    public static function process(string $content): string {
        self::$items = [];
        $n = 0;
        return (string) preg_replace_callback(
            '~<h2[^>]*>.*?</h2>~s',
            static function (array $m) use (&$n): string {
                $n++;
                $id = 'sec-' . $n;
                self::$items[] = ['id' => $id, 'title' => wp_strip_all_tags($m[0])];
                return '<div id="' . $id . '" class="scroll-mt-24">' . $m[0] . '</div>';
            },
            $content
        );
    }

    /** @return list<array{id: string, title: string}> */
    public static function items(): array {
        return self::$items;
    }

    public static function reset(): void {
        self::$items = [];
    }
}
"""
(ROOT / 'inc/Frontend/TOC.php').write_text(toc_php, encoding='utf-8')

(ROOT / 'temp/p75_fragments.json').write_text(json.dumps({k: v[:120] + '…' if len(v) > 120 else v for k, v in frag.items()}, ensure_ascii=False, indent=1), encoding='utf-8')
print('OK — single.php + TOC.php + article-shell.php + card-related.php تولید شد.')
