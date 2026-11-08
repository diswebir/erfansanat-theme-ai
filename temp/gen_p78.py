#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""تسک ۷.۸ — قالب برگه‌های licenses / faq / refund_returns / recruitment (با SectionManager)

برش بایت‌دقیق میرور + Repeater؛ خروجی:
  - page-templates/page-{licenses,faq,refund,recruitment}.php
  - inc/Sections/LandingSections.php (۴ گروه: licenses/faq/refund/recruitment)
  - template-parts/landing/section-*.php (۸ مارک‌آپ) + template-parts/sections/*.php (۸ رپر)
  - temp/p78_expect_{licenses,faq,refund,recruitment}.html
پچ‌ها: SectionManager (TEMPLATE_GROUPS ×۴) + Theme.php (بوت LandingSections)
assert بازسازی: هر سکشن با پیش‌فرض‌های میرور = برش اصلی (رشته کامل)

اجرا: python3 temp/gen_p78.py
"""
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
HOME_T = 'https://test.example/'


def main_seg(f):
    b = open(os.path.join(ROOT, 'uploads/static-mirror', f), encoding='utf-8').read()
    m0 = b.index('<main>') + 6
    return b[m0:b.index('</main>', m0)]


def mask(doc):
    out = []
    last = 0
    for m in re.finditer(r'>([^<>]+)<', doc):
        t = m.group(1)
        if t.strip():
            out.append(doc[last:m.start(1)])
            out.append('\x01')
            last = m.end(1)
    out.append(doc[last:])
    return ''.join(out)


def runs(chunk):
    return [m.group(1) for m in re.finditer(r'>([^<>]+)<', chunk) if m.group(1).strip()]


def find_run(rr, pred):
    return next(i for i, s in enumerate(rr) if pred(s))


def fill(skeleton, pairs):
    out = skeleton
    for k, v in pairs:
        assert k in out, 'fill token missing: ' + k
        out = out.replace(k, v)
    return out


class Tok:
    def __init__(self, frag):
        self.orig = frag
        self.runs = [(m.start(1), m.group(1)) for m in re.finditer(r'>([^<>]+)<', frag) if m.group(1).strip()]
        self.pos_ops = []
        self.str_ops = []

    def run(self, idx, token):
        pos, raw = self.runs[idx]
        self.pos_ops.append((pos, len(raw), token, raw))
        return self

    def attr(self, old, new, expect=1):
        n = self.orig.count(old)
        assert n == expect, f'attr anchor x{n} (expect {expect}): {old[:70]!r}'
        self.str_ops.append((old, new, n))
        return self

    def default(self, idx):
        return self.runs[idx][1]

    def result(self):
        out = self.orig
        for pos, ln, token, raw in sorted(self.pos_ops, key=lambda x: -x[0]):
            assert out[pos:pos + ln] == raw, f'pos mismatch @ {pos}'
            out = out[:pos] + token + out[pos + ln:]
        for old, new, n in self.str_ops:
            assert out.count(old) == n, f'str anchor x{out.count(old)}: {old[:70]!r}'
            out = out.replace(old, new)
        return out


# ══════════ هیروهای مشترک (۴ صفحه، ساختار یکسان) ══════════
PAGES = {
    'licenses': 'licenses-and-honors.html',
    'faq': 'faq.html',
    'refund': 'refund_returns.html',
    'recruitment': 'recruitment.html',
}
HERO_RAW = {}
for pg, fn in PAGES.items():
    seg = main_seg(fn)
    HERO_RAW[pg] = seg[:seg.index('</section>') + 10]
assert mask(HERO_RAW['licenses']) == mask(HERO_RAW['faq']) == mask(HERO_RAW['refund']) == mask(HERO_RAW['recruitment']), 'hero structure mismatch'
for pg in PAGES:
    assert len(runs(HERO_RAW[pg])) == 6, (pg, len(runs(HERO_RAW[pg])))
print('  hero struct: 4/4 masked-equal, 6 runs each')

HP = {'licenses': 'LH', 'faq': 'FH', 'refund': 'RH', 'recruitment': 'KH'}
HERO_TPL = {}
for pg, pre in HP.items():
    t = Tok(HERO_RAW[pg])
    t.attr('href="index.html"', 'href="{ES_HOME_URL}"')
    t.run(1, '{ES_' + pre + '_CRUMB}')
    t.run(2, '{ES_' + pre + '_KICKER}')
    t.run(3, '{ES_' + pre + '_H1_PRE}')
    t.run(4, '{ES_' + pre + '_H1_HL}')
    t.run(5, '{ES_' + pre + '_DESC}')
    HERO_TPL[pg] = t.result()
HERO_FIELDS = [
    ('crumb', 'text', 'برچسب مسیر — صفحه فعلی'),
    ('kicker', 'text', 'بج بالای عنوان'),
    ('h1_pre', 'text', 'عنوان — بخش اول'),
    ('h1_hl', 'text', 'عنوان — بخش سبز'),
    ('desc', 'textarea', 'متن معرفی'),
]


def hero_schema(pg):
    rr = runs(HERO_RAW[pg])
    return [(k, ft, lb, v) for (k, ft, lb), v in zip(HERO_FIELDS, rr[1:])]


# ══════════ licenses-certs ══════════
seg = main_seg('licenses-and-honors.html')
S1 = seg[2864:2864 + 21239]
GRID_LIC = '<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">'
SETAD_AT = 15719
grid_at = S1.index(GRID_LIC)
b0 = S1.index('<div class="flex items-start gap-3.5 rounded-2xl')
head_lic = S1[:b0]
badges_zone = S1[b0:grid_at]
assert badges_zone.endswith('</div></div>'), badges_zone[-60:]
badges_str = badges_zone[:-len('</div></div>')]
badge_pts = [m.start() for m in re.finditer(r'(?=<div class="flex items-start gap-3\.5 rounded-2xl)', badges_str)]
assert len(badge_pts) == 4, len(badge_pts)
BADGE_ROWS = [badges_str[a:b] for a, b in zip(badge_pts, badge_pts[1:] + [len(badges_str)])]
assert all(mask(r) == mask(BADGE_ROWS[0]) for r in BADGE_ROWS), 'badge rows differ'
tiles_zone = S1[grid_at + len(GRID_LIC):SETAD_AT]
assert tiles_zone.endswith('</div>'), tiles_zone[-60:]
tiles_str = tiles_zone[:-len('</div>')]
tile_pts = [m.start() for m in re.finditer(r'(?=<div custom="[0-9.]+"><article)', tiles_str)]
assert len(tile_pts) == 6, len(tile_pts)
CERT_ROWS_RAW = [tiles_str[a:b] for a, b in zip(tile_pts, tile_pts[1:] + [len(tiles_str)])]
SETAD_RAW = S1[SETAD_AT:]
print('  licenses: 4 badges + 6 certs + setad box')

t = Tok(BADGE_ROWS[0])
t.run(0, '{ES_LC_BT}').run(1, '{ES_LC_BD}')
BADGE_TPL = t.result()
BADGE_DATA = [dict(title=runs(r)[0], desc=runs(r)[1]) for r in BADGE_ROWS]

t = Tok(CERT_ROWS_RAW[0])
t.attr('custom="' + re.search(r'<div custom="([0-9.]+)"><article', CERT_ROWS_RAW[0]).group(1) + '"', 'custom="{ES_LC_CC}"')
t.run(0, '{ES_LC_CAT}').run(1, '{ES_LC_CODE}').run(2, '{ES_LC_TITLE}')
t.run(3, '{ES_LC_DESC}').run(4, '{ES_LC_ORG}').run(5, '{ES_LC_PERIOD}')
CERT_TPL = t.result()
CERT_CC = [re.search(r'<div custom="([0-9.]+)"><article', r).group(1) for r in CERT_ROWS_RAW]
CERT_DATA = []
for r in CERT_ROWS_RAW:
    rr = runs(r)
    assert len(rr) == 6, rr
    CERT_DATA.append(dict(cat=rr[0], code=rr[1], title=rr[2], desc=rr[3], org=rr[4], period=rr[5]))

setad_runs = runs(SETAD_RAW)
assert setad_runs[3:7] == ['ارائه پیش\u200cفاکتور رسمی ممهور در کمتر از ۲ ساعت کاری', 'پیوست برگه\u200cهای آنالیز فنی و گواهی\u200cهای آزمایشگاهی IP67 / IP68', 'امکان بازدید حضوری هیئت\u200cهای کارشناسی از خطوط تولید کارخانه', 'ضمانت\u200cنامه معتبر ۲۴ ماهه تعویض و ۵ سال خدمات پس از فروش'], setad_runs[3:7]
i_setad_phone = find_run(setad_runs, lambda s: s.startswith('۰۳۱'))
assert setad_runs[i_setad_phone - 2].startswith('دریافت') and i_setad_phone + 2 == len(setad_runs), setad_runs[i_setad_phone - 2:i_setad_phone + 2]
i0 = SETAD_RAW.index('<div class="flex items-center gap-2 text-[13px] font-bold text-slate-800">')
item_pts = [m.start() for m in re.finditer(r'(?=<div class="flex items-center gap-2 text-\[13px\] font-bold text-slate-800">)', SETAD_RAW)]
assert len(item_pts) == 4, len(item_pts)
i4_end = SETAD_RAW.index('</span></div>', item_pts[3]) + len('</span></div>')
setad_inner = SETAD_RAW[item_pts[0]:i4_end]
SIS_ROWS = [SETAD_RAW[a:b] for a, b in zip(item_pts, item_pts[1:] + [i4_end])]
assert len(SIS_ROWS) == 4 and all(mask(r) == mask(SIS_ROWS[0]) for r in SIS_ROWS), 'setad li differ'
SIS_DATA = [runs(r)[0] for r in SIS_ROWS]
assert SIS_DATA == setad_runs[3:7]
t = Tok(SIS_ROWS[0])
t.run(0, '{ES_LC_SI}')
SIS_TPL = t.result()

t = Tok(SETAD_RAW)
t.run(0, '{ES_LC_SB}').run(1, '{ES_LC_SH}').run(2, '{ES_LC_SD}')
t.attr(setad_inner, '{ES_LC_SIS}')
t.run(i_setad_phone - 2, '{ES_LC_DT}').run(i_setad_phone - 1, '{ES_LC_DD}')
t.run(i_setad_phone, '{ES_LC_PHONE}').run(i_setad_phone + 1, '{ES_LC_PT}')
t.attr('href="tel:031-91091011"', 'href="{ES_LC_PU}"')
t.attr('href="shop.html"', 'href="{ES_LC_SHU}"')
SETAD_TPL = t.result()

# ── assert بازسازی licenses (رشته کامل) ──
lic_skeleton = head_lic + '{ES_LC_BADGES}' + '</div></div>' + GRID_LIC + '{ES_LC_TILES}' + '</div>' + SETAD_TPL
lic_rebuilt = fill(lic_skeleton, [
    ('{ES_LC_BADGES}', badges_str), ('{ES_LC_TILES}', tiles_str), ('{ES_LC_SIS}', setad_inner),
    ('{ES_LC_SB}', setad_runs[0]), ('{ES_LC_SH}', setad_runs[1]), ('{ES_LC_SD}', setad_runs[2]),
    ('{ES_LC_DT}', setad_runs[i_setad_phone - 2]), ('{ES_LC_DD}', setad_runs[i_setad_phone - 1]),
    ('{ES_LC_PHONE}', setad_runs[i_setad_phone]), ('{ES_LC_PT}', setad_runs[i_setad_phone + 1]),
    ('{ES_LC_PU}', 'tel:031-91091011'), ('{ES_LC_SHU}', 'shop.html'),
])
assert lic_rebuilt == S1, 'licenses skeleton rebuild failed'
print('  rebuild OK: licenses (skeleton byte-exact)')

# ══════════ faq-list ══════════
seg = main_seg('faq.html')
F1 = seg[2832:2832 + 14015]
LIST_TAG = '<div class="lg:col-span-8" data-faq-list="true">'
list_at = F1.index(LIST_TAG)
aside_frag = F1[:list_at]
aside_runs = runs(aside_frag)
i_faq_phone = find_run(aside_runs, lambda s: s.startswith('۰۳۱'))
assert aside_runs[2:5] == ['خرید، استعلام قیمت و سامانه ستاد', 'مشخصات فنی و استانداردهای IP67 / IP68', 'گارانتی، نصب و خدمات پس از فروش'], aside_runs[2:5]
assert len(aside_runs) == i_faq_phone + 2, (len(aside_runs), i_faq_phone)
assert 'پاسخ سوال' in aside_runs[i_faq_phone - 2], aside_runs[i_faq_phone - 2]
cat_anchor = aside_frag.index('<div class="flex items-center gap-2.5 rounded-xl bg-slate-50 px-3.5 py-2.5')
help_anchor = aside_frag.index('<div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5">')
cat_container = aside_frag[cat_anchor:help_anchor]
assert cat_container.endswith('</div></div>'), cat_container[-60:]
cat_zone = cat_container[:-len('</div>')]
cat_pts = [m.start() for m in re.finditer(r'(?=<div class="flex items-center gap-2\.5 rounded-xl bg-slate-50)', cat_zone)]
CAT_ROWS = [cat_zone[a:b] for a, b in zip(cat_pts, cat_pts[1:] + [len(cat_zone)])]
assert len(CAT_ROWS) == 3 and all(mask(r) == mask(CAT_ROWS[0]) for r in CAT_ROWS), 'cat rows differ'
assert aside_runs[2:5] == [runs(r)[0] for r in CAT_ROWS]
t = Tok(aside_frag)
t.run(0, '{ES_FL_AT}').run(1, '{ES_FL_AC}')
t.attr(cat_container, '{ES_FL_CATS}</div>')
t.run(i_faq_phone - 2, '{ES_FL_HT}').run(i_faq_phone - 1, '{ES_FL_HD}')
t.run(i_faq_phone, '{ES_FL_PHONE}').run(i_faq_phone + 1, '{ES_FL_RT}')
t.attr('href="tel:031-91091011"', 'href="{ES_FL_PU}"')
t.attr('href="refund_returns.html"', 'href="{ES_FL_RU}"')
ASIDE_TPL = t.result()
t = Tok(CAT_ROWS[0])
t.run(0, '{ES_FL_CAT_R}')
CAT_TPL = t.result()
CATS_DATA = [runs(r)[0] for r in CAT_ROWS]

items_zone_at = list_at + len(LIST_TAG) + len('<div class="space-y-4">')
item_pts = [m.start() for m in re.finditer(r'(?=<div custom="[0-9.]+"><div class="overflow-hidden rounded-2xl)', F1)]
assert len(item_pts) == 6, len(item_pts)
tail_at = F1.rfind('</div></div></div></div></section>')
FAQ_ITEMS_SRC = F1[items_zone_at:tail_at + len('</div>')]
FAQ_TAIL = F1[tail_at + len('</div>'):]
assert FAQ_TAIL == '</div></div></div></section>', FAQ_TAIL
ITEMS_RAW = [F1[item_pts[i]:item_pts[i + 1]] for i in range(5)] + [F1[item_pts[5]:tail_at + len('</div>')]]
t = Tok(ITEMS_RAW[0])
t.attr('custom="0"', 'custom="{ES_FL_C}"')
t.run(0, '{ES_FL_CAT}')
t.run(1, '{ES_FL_Q}')
t.attr('data-faq-toggle="0"', 'data-faq-toggle="{ES_FL_I}"')
t.attr('data-faq-icon="0"', 'data-faq-icon="{ES_FL_I}"')
t.attr('rotate-45 bg-emerald-800 text-white', '{ES_FL_SI}')
t.attr('data-faq-body="0"', 'data-faq-body="{ES_FL_I}"')
t.attr('"block border-t', '"{ES_FL_SB} border-t')
t.run(2, '{ES_FL_A}')
ITEM_TPL = t.result()
FAQ_DATA = []
for it in ITEMS_RAW:
    rr = runs(it)
    assert len(rr) == 3, rr
    FAQ_DATA.append(dict(cat=rr[0], q=rr[1], a=rr[2], open='rotate-45 bg-emerald-800 text-white' in it))

FAQ_CYC = ['0', '0.03', '0.06', '0.09', '0.12', '0.15']


def render_faq_item(d, i):
    return (ITEM_TPL
            .replace('{ES_FL_C}', FAQ_CYC[i % 6])
            .replace('{ES_FL_I}', str(i))
            .replace('{ES_FL_SI}', 'rotate-45 bg-emerald-800 text-white' if d['open'] else 'bg-emerald-100 text-emerald-800')
            .replace('{ES_FL_SB}', 'block' if d['open'] else 'hidden')
            .replace('{ES_FL_CAT}', d['cat']).replace('{ES_FL_Q}', d['q']).replace('{ES_FL_A}', d['a']))


rebuilt = ''.join(render_faq_item(FAQ_DATA[i], i) for i in range(6))
assert rebuilt == FAQ_ITEMS_SRC, 'faq items rebuild failed'
faq_skeleton = ASIDE_TPL + LIST_TAG + '<div class="space-y-4">{ES_FL_ITEMS}</div></div></div></section>'
faq_rebuilt = fill(faq_skeleton, [
    ('{ES_FL_CATS}', cat_zone), ('{ES_FL_ITEMS}', FAQ_ITEMS_SRC),
    ('{ES_FL_AT}', aside_runs[0]), ('{ES_FL_AC}', aside_runs[1]),
    ('{ES_FL_HT}', aside_runs[i_faq_phone - 2]), ('{ES_FL_HD}', aside_runs[i_faq_phone - 1]),
    ('{ES_FL_PHONE}', aside_runs[i_faq_phone]), ('{ES_FL_RT}', aside_runs[i_faq_phone + 1]),
    ('{ES_FL_PU}', 'tel:031-91091011'), ('{ES_FL_RU}', 'refund_returns.html'),
])
assert faq_rebuilt == F1, 'faq skeleton rebuild failed'
print('  rebuild OK: faq (skeleton byte-exact)')

# ══════════ refund-policy ══════════
seg = main_seg('refund_returns.html')
R1 = seg[2809:2809 + 13763]
GRID_CARDS = '<div class="grid gap-6 md:grid-cols-3">'
cards_at = R1.index(GRID_CARDS)
head_refund = R1[:cards_at]
assert head_refund == '<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8 lg:py-20">', head_refund
GRID2 = '<div class="mt-12 grid gap-8 lg:grid-cols-2">'
cov_child_at = R1.index('<div custom="0.08">')
cov_grid_at = R1.rfind(GRID2, 0, cov_child_at)
assert cov_grid_at > cards_at
cards_zone = R1[cards_at + len(GRID_CARDS):cov_grid_at]
assert cards_zone.endswith('</div>'), cards_zone[-40:]
cards_str = cards_zone[:-len('</div>')]
card_pts = [m.start() for m in re.finditer(r'(?=<div custom="[0-9.]+"><div class="card-light)', cards_str)]
assert len(card_pts) == 3, len(card_pts)
CARD_ROWS_RAW = [cards_str[a:b] for a, b in zip(card_pts, card_pts[1:] + [len(cards_str)])]


def mask_nc(doc):
    return mask(re.sub(r'custom="[0-9.]+"', 'custom=""', doc))


assert all(mask_nc(r) == mask_nc(CARD_ROWS_RAW[0]) for r in CARD_ROWS_RAW), 'refund cards differ'
CARD_CC = [re.search(r'<div custom="([0-9.]+)"><div class="card-light', r).group(1) for r in CARD_ROWS_RAW]
CARD_DATA = [dict(title=runs(r)[0], desc=runs(r)[1]) for r in CARD_ROWS_RAW]
t = Tok(CARD_ROWS_RAW[0])
t.attr('custom="' + CARD_CC[0] + '"', 'custom="{ES_RP_CC}"')
t.run(0, '{ES_RP_CT}').run(1, '{ES_RP_CD}')
CARD_TPL = t.result()
rebuilt = ''.join(
    CARD_TPL.replace('{ES_RP_CC}', CARD_CC[i])
    .replace('{ES_RP_CT}', CARD_DATA[i]['title']).replace('{ES_RP_CD}', CARD_DATA[i]['desc'])
    for i in range(3))
assert rebuilt == cards_str, 'refund cards rebuild failed'

exc_child_at = R1.index('<div custom="0.12">', cov_child_at + 10)
proc_at = R1.index('<div class="mt-12" custom="0.15">')
COVERED_RAW = R1[cov_child_at:exc_child_at]
EXCLUDED_RAW = R1[exc_child_at:proc_at]
assert COVERED_RAW.endswith('</div></div>') and EXCLUDED_RAW.endswith('</div></div>')


def list_box(raw, h2_tok, sub_tok, list_tok):
    t = Tok(raw)
    t.run(0, h2_tok).run(1, sub_tok)
    ul = raw.index('<ul class="space-y-4">')
    ul_end = raw.index('</ul>', ul) + 5
    t.attr(raw[ul:ul_end], '<ul class="space-y-4">' + list_tok + '</ul>')
    return t.result(), raw[ul + len('<ul class="space-y-4">'):ul_end - 5]


COVERED_TPL, cov_lis = list_box(COVERED_RAW, '{ES_RP_CT}', '{ES_RP_CS}', '{ES_RP_CL}')
EXCLUDED_TPL, exc_lis = list_box(EXCLUDED_RAW, '{ES_RP_XT}', '{ES_RP_XS}', '{ES_RP_XL}')
cov_pts = [m.start() for m in re.finditer(r'(?=<li class="flex items-start gap-3)', cov_lis)]
COV_ROWS = [cov_lis[a:b] for a, b in zip(cov_pts, cov_pts[1:] + [len(cov_lis)])]
exc_pts = [m.start() for m in re.finditer(r'(?=<li class="flex items-start gap-3)', exc_lis)]
EXC_ROWS = [exc_lis[a:b] for a, b in zip(exc_pts, exc_pts[1:] + [len(exc_lis)])]
assert len(COV_ROWS) == 5 and all(mask(r) == mask(COV_ROWS[0]) for r in COV_ROWS), 'cov li differ'
assert len(EXC_ROWS) == 4 and all(mask(r) == mask(EXC_ROWS[0]) for r in EXC_ROWS), 'exc li differ'
t = Tok(COV_ROWS[0])
t.run(0, '{ES_RP_CI}')
COV_TPL = t.result()
t = Tok(EXC_ROWS[0])
t.run(0, '{ES_RP_XI}')
EXC_TPL = t.result()
COV_DATA = [runs(r)[0] for r in COV_ROWS]
EXC_DATA = [runs(r)[0] for r in EXC_ROWS]
ul_cov = COVERED_RAW[COVERED_RAW.index('<ul class="space-y-4">'):COVERED_RAW.index('</ul>') + 5]
rebuilt = '<ul class="space-y-4">' + ''.join(COV_TPL.replace('{ES_RP_CI}', s) for s in COV_DATA) + '</ul>'
assert rebuilt == ul_cov, 'covered rebuild failed'
ul_exc = EXCLUDED_RAW[EXCLUDED_RAW.index('<ul class="space-y-4">'):EXCLUDED_RAW.index('</ul>') + 5]
rebuilt = '<ul class="space-y-4">' + ''.join(EXC_TPL.replace('{ES_RP_XI}', s) for s in EXC_DATA) + '</ul>'
assert rebuilt == ul_exc, 'excluded rebuild failed'

PROC_RAW = R1[proc_at:]
proc_runs = runs(PROC_RAW)
i_rp = find_run(proc_runs, lambda s: s == 'فرآیند اجرایی پشتیبانی فنی')
i_pt = find_run(proc_runs, lambda s: s == 'مراحل ثبت درخواست گارانتی و خدمات پس از فروش')
i_rph = find_run(proc_runs, lambda s: s.startswith('واحد خدمات پس از فروش'))
i_radd = find_run(proc_runs, lambda s: s.startswith('آدرس کارخانه'))
i_rcta = find_run(proc_runs, lambda s: s.startswith('ثبت آنلاین'))
assert (i_rp, i_pt) == (0, 1) and i_rph == 2 and i_radd == 12 and i_rcta == 13, (i_rp, i_pt, i_rph, i_radd, i_rcta)
card_starts = [m.start() for m in re.finditer(r'(?=<div class="rounded-2xl border border-slate-200/90 bg-white p-6">)', PROC_RAW)]
assert len(card_starts) == 3, len(card_starts)
sp = card_starts[0]
c3 = card_starts[2]
card3_end = PROC_RAW.index('</p>', c3) + len('</p></div>')
sz = PROC_RAW[sp:card3_end]
spts2 = card_starts
SROWS = [sz[a - sp:b - sp] for a, b in zip(spts2, spts2[1:] + [card3_end])]
assert len(SROWS) == 3, len(SROWS)
assert mask(SROWS[0]) == mask(SROWS[1]) == mask(SROWS[2]), 'steps differ'
STEPS_DATA = []
for r in SROWS:
    rr = runs(r)
    assert len(rr) == 3 and rr[0] in ('۱', '۲', '۳'), rr
    STEPS_DATA.append(dict(title=rr[1], desc=rr[2]))
t = Tok(SROWS[0])
t.run(0, '{ES_RP_SN}').run(1, '{ES_RP_ST}').run(2, '{ES_RP_SD}')
STEP_TPL = t.result()
t = Tok(PROC_RAW)
t.run(i_rp, '{ES_RP_PB}').run(i_pt, '{ES_RP_PT}').run(i_rph, '{ES_RP_PH}')
t.run(i_radd, '{ES_RP_ADD}').run(i_rcta, '{ES_RP_CTT}')
t.attr('href="tel:031-91091011"', 'href="{ES_RP_PU}"')
t.attr('href="contact.html"', 'href="{ES_RP_CU}"')
t.attr(sz, '{ES_RP_SG}')
PROC_TPL = t.result()
rebuilt = ''.join(
    STEP_TPL.replace('{ES_RP_SN}', ['۱', '۲', '۳'][i])
    .replace('{ES_RP_ST}', STEPS_DATA[i]['title']).replace('{ES_RP_SD}', STEPS_DATA[i]['desc'])
    for i in range(3))
assert rebuilt == sz, 'steps rebuild failed'
ref_skeleton = head_refund + GRID_CARDS + '{ES_RP_CARDS}' + '</div>' + GRID2 + COVERED_TPL + EXCLUDED_TPL + PROC_TPL
ref_rebuilt = fill(ref_skeleton, [
    ('{ES_RP_CARDS}', cards_str), ('{ES_RP_CL}', ul_cov[22:-5]), ('{ES_RP_XL}', ul_exc[22:-5]), ('{ES_RP_SG}', sz),
    ('{ES_RP_CT}', runs(COVERED_RAW)[0]), ('{ES_RP_CS}', runs(COVERED_RAW)[1]),
    ('{ES_RP_XT}', runs(EXCLUDED_RAW)[0]), ('{ES_RP_XS}', runs(EXCLUDED_RAW)[1]),
    ('{ES_RP_PB}', proc_runs[i_rp]), ('{ES_RP_PT}', proc_runs[i_pt]),
    ('{ES_RP_PH}', proc_runs[i_rph]), ('{ES_RP_ADD}', proc_runs[i_radd]), ('{ES_RP_CTT}', proc_runs[i_rcta]),
    ('{ES_RP_PU}', 'tel:031-91091011'), ('{ES_RP_CU}', 'contact.html'),
])
assert ref_rebuilt == R1, 'refund skeleton rebuild failed'
print('  rebuild OK: refund (skeleton byte-exact)')

# ══════════ recruit-jobs ══════════
seg = main_seg('recruitment.html')
C1 = seg[2845:2845 + 35974]
HR_AT = C1.index('<div class="mt-16" custom="0.1">')
HEAD = C1[:1516]
PANELS_RAW = {'rnd': C1[1516:9615], 'project': C1[9615:17785], 'accounting': C1[17785:22231], 'it': C1[22231:HR_AT]}
HR_RAW = C1[HR_AT:]
assert all(p.startswith('<div data-recruit-tab-panel=') for p in PANELS_RAW.values())
GRID3 = '<div class="grid gap-6 md:grid-cols-2">'
head_runs = runs(HEAD)
assert len(head_runs) == 6 and 'واحدهای سازمانی' in head_runs[0], head_runs
t = Tok(HEAD)
t.run(0, '{ES_RJ_H2}').run(1, '{ES_RJ_HD}')
btn_at = HEAD.index('<button type="button" data-recruit-tab-btn="rnd"')
btn_end = HEAD.rindex('</button>') + len('</button>')
t.attr(HEAD[btn_at:btn_end], '{ES_RJ_TABS}')
HEAD_TPL = t.result()
TAB_KEYS = ['rnd', 'project', 'accounting', 'it']
TAB_BTN_RAW = []
for k in TAB_KEYS:
    m = re.search(r'<button type="button" data-recruit-tab-btn="' + k + r'" class="[^"]*">([^<]*)</button>', HEAD)
    TAB_BTN_RAW.append(m.group(0))
for x in TAB_BTN_RAW:
    assert x.count('data-recruit-tab-btn=') == 1, x
assert sum('bg-emerald-800 text-white shadow-xs' in x for x in TAB_BTN_RAW) == 1, 'exactly one active tab'
TAB_BTNS = [re.search(r'>([^<]*)</button>', x).group(1) for x in TAB_BTN_RAW]
assert TAB_BTNS == head_runs[2:6], (TAB_BTNS, head_runs[2:6])
t = Tok(TAB_BTN_RAW[0])
t.attr('data-recruit-tab-btn="rnd"', 'data-recruit-tab-btn="{ES_RJ_KEY}"')
t.attr('bg-emerald-800 text-white shadow-xs', '{ES_RJ_BS}')
t.run(0, '{ES_RJ_BTN}')
TABBTN_TPL = t.result()
rebuilt = ''.join(
    TABBTN_TPL.replace('{ES_RJ_KEY}', TAB_KEYS[i])
    .replace('{ES_RJ_BS}', 'bg-emerald-800 text-white shadow-xs' if i == 0 else 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-900')
    .replace('{ES_RJ_BTN}', TAB_BTNS[i])
    for i in range(4))
assert rebuilt == HEAD[btn_at:btn_end], 'tab btns rebuild failed'

PANEL_INTRO = {}
PANEL_JOBS_RAW = {}
for k, raw in PANELS_RAW.items():
    g = raw.index(GRID3)
    PANEL_INTRO[k] = raw[:g + len(GRID3)]
    PANEL_JOBS_RAW[k] = raw[g + len(GRID3):]
def mask_panel(doc):
    doc = re.sub(r'data-recruit-tab-panel="[a-z]+"', 'data-recruit-tab-panel=""', doc)
    doc = doc.replace(' class="block"', ' class=""').replace(' class="hidden"', ' class=""')
    return mask(doc)


assert all(mask_panel(PANEL_INTRO[k]) == mask_panel(PANEL_INTRO['rnd']) for k in TAB_KEYS), 'panel intro differ'
INTRO_DATA = {}
for k in TAB_KEYS:
    rr = runs(PANEL_INTRO[k])
    assert len(rr) == 3, (k, rr)
    INTRO_DATA[k] = dict(badge=rr[0], title=rr[1], desc=rr[2])
t = Tok(PANEL_INTRO['rnd'])
t.attr('data-recruit-tab-panel="rnd"', 'data-recruit-tab-panel="{ES_RJ_KEY}"')
t.attr('"block">', '"{ES_RJ_PS}">')
t.run(0, '{ES_RJ_BADGE}').run(1, '{ES_RJ_TITLE}').run(2, '{ES_RJ_DESC}')
t.attr(GRID3, GRID3 + '{ES_RJ_JOBS}</div></div>')
PANEL_TPL = t.result()

JOB_TPLS = []
for k in TAB_KEYS:
    zone = PANEL_JOBS_RAW[k]
    assert zone.endswith('</div></div>'), zone[-40:]
    z = zone[:-len('</div></div>')]
    pts = [m.start() for m in re.finditer(r'(?=<article class="card-light)', z)]
    for i, a in enumerate(pts):
        e = pts[i + 1] if i + 1 < len(pts) else len(z)
        JOB_TPLS.append((k, z[a:e]))
JOBS_DATA = []
for k, card in JOB_TPLS:
    rr = runs(card)
    assert len(rr) >= 4, (k, rr)
    sk_at = card.index('<ul class="space-y-2">')
    sk_zone = card[sk_at:card.index('</ul>', sk_at) + 5]
    skills = runs(sk_zone)
    JOBS_DATA.append(dict(tab=k, type=rr[0], exp=rr[1], title=rr[2], loc=rr[3], skills=skills))
assert len(JOBS_DATA) == 7, len(JOBS_DATA)
k0, card0 = JOB_TPLS[0]
sk_at = card0.index('<ul class="space-y-2">')
sk_zone = card0[sk_at:card0.index('</ul>', sk_at) + 5]
sk_inner = sk_zone[:-len('</ul>')]
spts = [m.start() for m in re.finditer(r'(?=<li class="flex items-start gap-2 text-\[13px\])', sk_inner)]
SKILL_ROWS = [sk_inner[a:b] for a, b in zip(spts, spts[1:] + [len(sk_inner)])]
assert all(mask(r) == mask(SKILL_ROWS[0]) for r in SKILL_ROWS), 'skill li differ'
t = Tok(SKILL_ROWS[0])
t.run(0, '{ES_RJ_SK}')
SKILL_TPL = t.result()
t = Tok(card0)
t.run(0, '{ES_RJ_TYPE}').run(1, '{ES_RJ_EXP}').run(2, '{ES_RJ_TITLE}').run(3, '{ES_RJ_LOC}')
t.attr(sk_zone, '<ul class="space-y-2">{ES_RJ_SKILLS}</ul>')
JOB_TPL = t.result()

panel_rebuilt = ''
for i, k in enumerate(TAB_KEYS):
    jobs = ''.join(
        JOB_TPL.replace('{ES_RJ_TYPE}', j['type']).replace('{ES_RJ_EXP}', j['exp'])
        .replace('{ES_RJ_TITLE}', j['title']).replace('{ES_RJ_LOC}', j['loc'])
        .replace('{ES_RJ_SKILLS}', ''.join(SKILL_TPL.replace('{ES_RJ_SK}', s) for s in j['skills']))
        for j in JOBS_DATA if j['tab'] == k)
    panel_rebuilt += (PANEL_TPL
                      .replace('{ES_RJ_KEY}', k)
                      .replace('{ES_RJ_PS}', 'block' if i == 0 else 'hidden')
                      .replace('{ES_RJ_BADGE}', INTRO_DATA[k]['badge'])
                      .replace('{ES_RJ_TITLE}', INTRO_DATA[k]['title'])
                      .replace('{ES_RJ_DESC}', INTRO_DATA[k]['desc'])
                      .replace('{ES_RJ_JOBS}', jobs))
assert panel_rebuilt == C1[1516:HR_AT], 'panels rebuild failed'

hr_runs = runs(HR_RAW)
form_at = HR_RAW.index('<form data-contact-form="true"')
form_end = HR_RAW.index('</form>', form_at) + len('</form>')
FORM_RAW = HR_RAW[form_at:form_end]
frr = runs(FORM_RAW)
i_ln = frr.index('نام و نام خانوادگی *')
i_lt = frr.index('شماره تلفن همراه *')
i_lo = frr.index('واحد سازمانی مورد نظر *')
i_le = frr.index('میزان سابقه کار مرتبط')
i_lm = find_run(frr, lambda s: s.startswith('خلاصه سوابق'))
i_st = i_lm + 1
assert frr[i_st] == 'ثبت و ارسال درخواست استخدام', frr[i_st]
assert i_ln == 0 and i_lt == 1 and i_lo == 2, (i_ln, i_lt, i_lo)
assert frr[-1] is not None and len(hr_runs) >= 10
i_okb = len(hr_runs) - 1
i_okt = i_okb - 2
i_okd = i_okb - 1
opt_at = FORM_RAW.index('<option', FORM_RAW.index('واحد سازمانی مورد نظر'))
opt_end = FORM_RAW.index('</option>', FORM_RAW.index('فناوری اطلاعات')) + len('</option>')
opt_zone = FORM_RAW[opt_at:opt_end]
assert opt_zone.count('<option') == 4, opt_zone
i_st_abs = find_run(hr_runs, lambda s: s == 'ثبت و ارسال درخواست استخدام')
assert i_st_abs == i_okb - 3, (i_st_abs, i_okb)
t = Tok(HR_RAW)
t.run(0, '{ES_RJ_HB}').run(1, '{ES_RJ_H2F}').run(2, '{ES_RJ_HDF}')
t.run(3, '{ES_RJ_AL}').run(4, '{ES_RJ_ADDR}').run(5, '{ES_RJ_HL}').run(6, '{ES_RJ_HOURS}')
t.run(find_run(hr_runs, lambda s: s == 'نام و نام خانوادگی *'), '{ES_RJ_LN}')
t.run(find_run(hr_runs, lambda s: s == 'شماره تلفن همراه *'), '{ES_RJ_LT}')
t.run(find_run(hr_runs, lambda s: s == 'واحد سازمانی مورد نظر *'), '{ES_RJ_LO}')
t.attr(opt_zone, '{ES_RJ_ORGS}')
t.run(find_run(hr_runs, lambda s: s == 'میزان سابقه کار مرتبط'), '{ES_RJ_LE}')
t.run(find_run(hr_runs, lambda s: s.startswith('خلاصه سوابق')), '{ES_RJ_LM}')
t.run(i_st_abs, '{ES_RJ_ST}')
t.run(i_okt, '{ES_RJ_OKT}').run(i_okd, '{ES_RJ_OKD}').run(i_okb, '{ES_RJ_OKB}')
t.attr('placeholder="مثال: علی رضایی"', 'placeholder="{ES_RJ_PN}"')
t.attr('placeholder="0913..."', 'placeholder="{ES_RJ_PT}"')
t.attr('placeholder="تحصیلات، مهارت\u200cهای تخصصی و سوابق کاری خود را بنویسید..."', 'placeholder="{ES_RJ_PM}"')
t.attr('<form data-contact-form="true" class="flex flex-col gap-4">', '<form data-contact-form="true" class="flex flex-col gap-4"{ES_RJ_FX}>')
HR_TPL = t.result()
print('  recruit tokens ok (tabs/panels/jobs/hr)')

rec_skeleton = HEAD_TPL + '{ES_RJ_PANELS}' + HR_TPL
rec_rebuilt = fill(rec_skeleton, [
    ('{ES_RJ_PANELS}', panel_rebuilt), ('{ES_RJ_TABS}', HEAD[btn_at:btn_end]),
    ('{ES_RJ_H2}', head_runs[0]), ('{ES_RJ_HD}', head_runs[1]),
    ('{ES_RJ_HB}', hr_runs[0]), ('{ES_RJ_H2F}', hr_runs[1]), ('{ES_RJ_HDF}', hr_runs[2]),
    ('{ES_RJ_AL}', hr_runs[3]), ('{ES_RJ_ADDR}', hr_runs[4]), ('{ES_RJ_HL}', hr_runs[5]), ('{ES_RJ_HOURS}', hr_runs[6]),
    ('{ES_RJ_LN}', hr_runs[find_run(hr_runs, lambda s: s == 'نام و نام خانوادگی *')]),
    ('{ES_RJ_LT}', hr_runs[find_run(hr_runs, lambda s: s == 'شماره تلفن همراه *')]),
    ('{ES_RJ_LO}', hr_runs[find_run(hr_runs, lambda s: s == 'واحد سازمانی مورد نظر *')]),
    ('{ES_RJ_ORGS}', opt_zone),
    ('{ES_RJ_LE}', hr_runs[find_run(hr_runs, lambda s: s == 'میزان سابقه کار مرتبط')]),
    ('{ES_RJ_LM}', hr_runs[find_run(hr_runs, lambda s: s.startswith('خلاصه سوابق'))]),
    ('{ES_RJ_ST}', hr_runs[i_okb - 3]), ('{ES_RJ_OKT}', hr_runs[i_okt]), ('{ES_RJ_OKD}', hr_runs[i_okd]), ('{ES_RJ_OKB}', hr_runs[i_okb]),
    ('{ES_RJ_PN}', 'مثال: علی رضایی'), ('{ES_RJ_PT}', '0913...'),
    ('{ES_RJ_PM}', 'تحصیلات، مهارت\u200cهای تخصصی و سوابق کاری خود را بنویسید...'),
    ('{ES_RJ_FX}', ''),
])
assert rec_rebuilt == C1, 'recruit skeleton rebuild failed'
print('  rebuild OK: recruit (skeleton byte-exact)')

# ══════════ سکشن contact مشترک ══════════
seg = main_seg('licenses-and-honors.html')
CTA_RAW = seg[2864 + 21239:]
idx = main_seg('index.html')
isp = []
off = 0
while True:
    p = idx.find('<section', off)
    if p < 0:
        break
    isp.append(p)
    off = p + 8
assert CTA_RAW == idx[isp[13]:isp[14]], 'cta != home contact'
print('  contact CTA byte-equal to home (reuse id contact)')

# ══════════ اسکیم‌ها (پیش‌فرض = میرور) ══════════
SCHEMA = {
    'licenses-hero': hero_schema('licenses'),
    'faq-hero': hero_schema('faq'),
    'refund-hero': hero_schema('refund'),
    'recruit-hero': hero_schema('recruitment'),
    'licenses-certs': [
        ('badges', 'repeater', 'بج\u200cهای بالا (۴ کارت)', [{'title': b['title'], 'desc': b['desc']} for b in BADGE_DATA]),
        ('certs', 'repeater', 'گواهینامه\u200cها و افتخارات', [
            {'cat': c['cat'], 'code': c['code'], 'title': c['title'], 'desc': c['desc'], 'org': c['org'], 'period': c['period']} for c in CERT_DATA]),
        ('s_badge', 'text', 'باکس ستاد — بج', setad_runs[0]),
        ('s_h2', 'text', 'باکس ستاد — عنوان', setad_runs[1]),
        ('s_desc', 'textarea', 'باکس ستاد — توضیح', setad_runs[2]),
        ('s_items', 'repeater', 'باکس ستاد — فهرست موارد', [{'item': s} for s in SIS_DATA]),
        ('docs_title', 'text', 'باکس دریافت مستندات — عنوان', setad_runs[i_setad_phone - 2]),
        ('docs_desc', 'text', 'باکس دریافت مستندات — توضیح', setad_runs[i_setad_phone - 1]),
        ('phone', 'text', 'تلفن (نمایشی)', setad_runs[i_setad_phone]),
        ('phone_url', 'url', 'لینک تلفن', 'tel:031-91091011'),
        ('products_text', 'text', 'دکمه محصولات — متن', setad_runs[i_setad_phone + 1]),
        ('products_url', 'url', 'دکمه محصولات — لینک', '/shop/'),
    ],
    'faq-list': [
        ('aside_title', 'text', 'سایدبار — عنوان', aside_runs[0]),
        ('aside_count', 'text', 'سایدبار — شمارنده', aside_runs[1]),
        ('cats', 'repeater', 'سایدبار — دسته\u200cبندی\u200cها', [{'title': c} for c in CATS_DATA]),
        ('help_title', 'text', 'جعبه تماس — عنوان', aside_runs[i_faq_phone - 2]),
        ('help_desc', 'textarea', 'جعبه تماس — توضیح', aside_runs[i_faq_phone - 1]),
        ('phone', 'text', 'تلفن (نمایشی)', aside_runs[i_faq_phone]),
        ('phone_url', 'url', 'لینک تلفن', 'tel:031-91091011'),
        ('refund_text', 'text', 'دکمه گارانتی — متن', aside_runs[i_faq_phone + 1]),
        ('refund_url', 'url', 'دکمه گارانتی — لینک', '/refund_returns/'),
        ('items', 'repeater', 'پرسش و پاسخ\u200cها', [
            {'cat': d['cat'], 'question': d['q'], 'answer': d['a'], 'open': 1 if d['open'] else 0} for d in FAQ_DATA]),
    ],
    'refund-policy': [
        ('cards', 'repeater', 'کارت\u200cهای بالای صفحه (۳)', [
            {'title': c['title'], 'desc': c['desc']} for c in CARD_DATA]),
        ('c_title', 'text', 'گارانتی شامل — عنوان', runs(COVERED_RAW)[0]),
        ('c_sub', 'text', 'گارانتی شامل — زیرعنوان', runs(COVERED_RAW)[1]),
        ('covered', 'repeater', 'موارد تحت پوشش', [{'item': s} for s in COV_DATA]),
        ('x_title', 'text', 'خارج از شمول — عنوان', runs(EXCLUDED_RAW)[0]),
        ('x_sub', 'text', 'خارج از شمول — زیرعنوان', runs(EXCLUDED_RAW)[1]),
        ('excluded', 'repeater', 'موارد خارج از شمول', [{'item': s} for s in EXC_DATA]),
        ('p_badge', 'text', 'فرآیند — بج', proc_runs[i_rp]),
        ('p_title', 'text', 'فرآیند — عنوان', proc_runs[i_pt]),
        ('phone_text', 'text', 'دکمه تلفن — متن', proc_runs[i_rph]),
        ('phone_url', 'url', 'لینک تلفن', 'tel:031-91091011'),
        ('steps', 'repeater', 'مراحل (۳ گام)', [
            {'title': s['title'], 'desc': s['desc']} for s in STEPS_DATA]),
        ('address', 'textarea', 'آدرس ارسال مرسولات', proc_runs[i_radd]),
        ('cta_text', 'text', 'دکمه ثبت آنلاین — متن', proc_runs[i_rcta]),
        ('cta_url', 'url', 'دکمه ثبت آنلاین — لینک', '/contact/'),
    ],
    'recruit-jobs': [
        ('h2', 'text', 'عنوان بخش تب\u200cها', head_runs[0]),
        ('desc', 'textarea', 'توضیح بخش تب\u200cها', head_runs[1]),
        ('tabs', 'repeater', 'واحدهای سازمانی (تب\u200cها)', [
            {'key': TAB_KEYS[i], 'btn': TAB_BTNS[i], 'open': 1 if i == 0 else 0,
             'badge': INTRO_DATA[TAB_KEYS[i]]['badge'], 'title': INTRO_DATA[TAB_KEYS[i]]['title'],
             'desc': INTRO_DATA[TAB_KEYS[i]]['desc']} for i in range(4)]),
        ('jobs', 'repeater', 'موقعیت\u200cهای شغلی', [
            {'tab': j['tab'], 'type': j['type'], 'exp': j['exp'], 'title': j['title'],
             'loc': j['loc'], 'skills': '\n'.join(j['skills'])} for j in JOBS_DATA]),
        ('hr_badge', 'text', 'فرم — بج منابع انسانی', hr_runs[0]),
        ('hr_h2', 'text', 'فرم — عنوان', hr_runs[1]),
        ('hr_desc', 'textarea', 'فرم — توضیح', hr_runs[2]),
        ('addr_label', 'text', 'فرم — برچسب محل کارخانه', hr_runs[3]),
        ('addr', 'textarea', 'فرم — محل کارخانه', hr_runs[4]),
        ('hours_label', 'text', 'فرم — برچسب ساعات کاری', hr_runs[5]),
        ('hours', 'text', 'فرم — ساعات کاری', hr_runs[6]),
        ('l_name', 'text', 'برچسب نام و نام خانوادگی', 'نام و نام خانوادگی *'),
        ('ph_name', 'text', 'جانگهدار نام', 'مثال: علی رضایی'),
        ('l_tel', 'text', 'برچسب تلفن همراه', 'شماره تلفن همراه *'),
        ('ph_tel', 'text', 'جانگهدار تلفن', '0913...'),
        ('l_org', 'text', 'برچسب واحد سازمانی', 'واحد سازمانی مورد نظر *'),
        ('l_exp', 'text', 'برچسب سابقه کار', 'میزان سابقه کار مرتبط'),
        ('l_msg', 'text', 'برچسب سوابق', hr_runs[find_run(hr_runs, lambda s: s.startswith('خلاصه سوابق'))]),
        ('ph_msg', 'text', 'جانگهدار سوابق', 'تحصیلات، مهارت\u200cهای تخصصی و سوابق کاری خود را بنویسید...'),
        ('submit', 'text', 'متن دکمه ارسال', hr_runs[i_okb - 3]),
        ('ok_t', 'text', 'پیام موفقیت — عنوان', hr_runs[i_okt]),
        ('ok_d', 'text', 'پیام موفقیت — متن', hr_runs[i_okd]),
        ('ok_btn', 'text', 'پیام موفقیت — دکمه', hr_runs[i_okb]),
    ],
}

# ══════════ تولید PHP ══════════
def php_sq(v):
    return "'" + str(v).replace('\\', '\\\\').replace("'", "\\'") + "'"


def php_nl(v):
    # خط جدید واقعی داخل رشته تک‌کوتیشن PHP معتبر است (لایترال \n نه!)
    return php_sq(v)


REPEATERS = {
    'badges': [('title', 'text', 'عنوان'), ('desc', 'text', 'توضیح')],
    'certs': [('cat', 'text', 'دسته'), ('code', 'text', 'کد گواهینامه'), ('title', 'text', 'عنوان گواهینامه'), ('desc', 'textarea', 'توضیح'), ('org', 'text', 'مرجع صادرکننده'), ('period', 'text', 'دوره اعتبار')],
    's_items': [('item', 'textarea', 'متن')],
    'cats': [('title', 'text', 'عنوان دسته')],
    'faq_items': [('cat', 'text', 'دسته'), ('question', 'text', 'پرسش'), ('answer', 'textarea', 'پاسخ'), ('open', 'switch', 'باز باشد')],
    'r_cards': [('title', 'text', 'عنوان'), ('desc', 'textarea', 'توضیح')],
    'r_items': [('item', 'textarea', 'متن')],
    'steps': [('title', 'text', 'عنوان گام'), ('desc', 'textarea', 'توضیح گام')],
    'tabs': [('key', 'text', 'کلید تب (لاتین)'), ('btn', 'text', 'متن دکمه'), ('open', 'switch', 'تب فعال'), ('badge', 'text', 'بج واحد'), ('title', 'text', 'عنوان واحد'), ('desc', 'textarea', 'توضیح واحد')],
    'jobs': [('tab', 'text', 'کلید تب (لاتین)'), ('type', 'text', 'نوع همکاری'), ('exp', 'text', 'سابقه'), ('title', 'text', 'عنوان موقعیت'), ('loc', 'text', 'محل'), ('skills', 'textarea', 'مهارت\u200cها (هر خط یک مورد)')],
}


def schema_rows_php(fields):
    lines = []
    for f in fields:
        key, ftype, label = f[0], f[1], f[2]
        default = f[3] if len(f) > 3 else ''
        if ftype == 'repeater':
            rep_key = key
            if key == 'items':
                rep_key = 'faq_items'
            elif key == 'cards':
                rep_key = 'r_cards'
            elif key in ('covered', 'excluded'):
                rep_key = 'r_items'
            subs = REPEATERS[rep_key]
            lines.append("                ['key' => '%s', 'type' => 'repeater', 'label' => __('%s', 'erfansanat'), 'add_label' => __('افزودن ردیف', 'erfansanat')," % (key, label))
            lines.append("                    'fields' => [")
            for sk, st, sl in subs:
                extra = ", 'default' => '1'" if sk == 'open' else ''
                lines.append("                        ['key' => '%s', 'type' => '%s', 'label' => __('%s', 'erfansanat')%s]," % (sk, st, sl, extra))
            lines.append("                    ],")
            lines.append("                    'default' => [")
            for row in default:
                cells = ', '.join(
                    ("'skills' => " + php_nl(row['skills'])) if k == 'skills' else (php_sq(k) + ' => ' + php_sq(row[k]))
                    for k in row)
                lines.append('                        [' + cells + '],')
            lines.append('                    ],')
            lines.append('                ],')
        else:
            lines.append("                ['key' => '%s', 'type' => '%s', 'label' => __('%s', 'erfansanat'), 'default' => %s]," % (key, ftype, label, php_sq(default)))
    return '\n'.join(lines)


FILES_DESC = {
    'licenses-hero': 'هیرو لندینگ مجوزها — کرامب، بج، عنوان دوبخشی و معرفی',
    'faq-hero': 'هیرو لندینگ سوالات متداول — کرامب، بج، عنوان دوبخشی و معرفی',
    'refund-hero': 'هیرو لندینگ شرایط گارانتی — کرامب، بج، عنوان دوبخشی و معرفی',
    'recruit-hero': 'هیرو لندینگ فرصت\u200cهای شغلی — کرامب، بج، عنوان دوبخشی و معرفی',
    'licenses-certs': 'بج\u200cها + گرید گواهینامه\u200cها (Repeater) + باکس ستاد و دریافت مستندات',
    'faq-list': 'آکاردئون دسته\u200cبندی\u200cشده (Repeater با data-faq-*) + سایدبار',
    'refund-policy': 'کارت\u200cها + پوشش/شمول گارانتی + مراحل پشتیبانی (همه Repeater)',
    'recruit-jobs': 'تب\u200cهای واحدها + موقعیت\u200cها (Repeater) + فرم استخدام (data-contact-form — تا ۸.۶ نمایشی)',
}


def php_section(slug, title, mirror, heredoc, skeleton, row_templates, fixed_fields, loops, extras):
    out_path = 'template-parts/landing/section-' + slug + '.php'
    body = []
    body.append('<?php')
    body.append('/**')
    body.append(' * %s — برش بایت\u200cدقیق میرور %s (تسک ۷.۸)' % (title, mirror))
    body.append(' * %s' % FILES_DESC[slug])
    body.append(' * رندر: SectionManager → رپر template-parts/sections/ → این فایل')
    body.append(' *')
    body.append(' * @package ErfanSanat')
    body.append(' */')
    body.append('declare(strict_types=1);')
    body.append('')
    body.append("/** @var array $args {section_id: string, fields: array<string, mixed>, post_id: int} */")
    body.append('')
    body.append("$es_f = $args['fields'] ?? [];")
    body.append('')
    for group_tok, (var, tpl) in row_templates.items():
        hd = heredoc + var[-1]
        body.append('%s = <<<\'%s\'' % (var, hd))
        body.append(tpl)
        body.append('%s;' % hd)
        body.append('')
    var = '$' + heredoc.lower().replace('7', '_7').replace('hh', '_h')
    body.append('%s = <<<\'%s\'' % (var, heredoc))
    body.append(skeleton)
    body.append('%s;' % heredoc)
    body.append('')
    body.append(loops)
    body.append('')
    tr = []
    for tok, expr in extras:
        tr.append("    '%s' => %s," % (tok, expr))
    for tok, key, ftype in fixed_fields:
        if ftype == 'url':
            expr = "es_link_url((string) ($es_f['%s'] ?? ''))" % key
        elif ftype == 'media':
            expr = "es_media_url((string) ($es_f['%s'] ?? ''))" % key
        else:
            expr = "(string) ($es_f['%s'] ?? '')" % key
        tr.append("    '%s' => %s," % (tok, expr))
    body.append('echo strtr(%s, [' % var)
    body.append('\n'.join(tr))
    body.append(']);')
    content = '\n'.join(body) + '\n'
    os.makedirs(os.path.dirname(os.path.join(ROOT, out_path)), exist_ok=True)
    open(os.path.join(ROOT, out_path), 'w', encoding='utf-8').write(content)
    wrap = ("<?php\n/**\n * سکشن %s — مسیر رندر SectionManager؛ مارک\u200cآپ در %s\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\nes_get_template('%s', get_defined_vars());\n" % (slug, out_path, out_path))
    open(os.path.join(ROOT, 'template-parts/sections/' + slug + '.php'), 'w', encoding='utf-8').write(wrap)
    print('  wrote', out_path, '+ رپر')


# ── هیروها ──
for pg in PAGES:
    pre = HP[pg]
    slug = ('recruit' if pg == 'recruitment' else pg) + '-hero'
    fixed = [('{ES_' + pre + '_' + k.upper() + '}', k, ft) for k, ft, _ in HERO_FIELDS]
    php_section(slug, 'سکشن هیرو «%s»' % pg, PAGES[pg], 'ES' + pre + 'H',
                HERO_TPL[pg], {}, fixed, '', [('{ES_HOME_URL}', "esc_url(home_url('/'))")])

# ── licenses-certs ──
lic_loops = """// بج\u200cهای بالا
$es_badges_out = '';
foreach (array_values(is_array($es_f['badges'] ?? null) ? $es_f['badges'] : []) as $es_row) {
    $es_badges_out .= strtr($ESLCB, [
        '{ES_LC_BT}' => (string) ($es_row['title'] ?? ''),
        '{ES_LC_BD}' => (string) ($es_row['desc'] ?? ''),
    ]);
}

// کاشی\u200cهای گواهینامه (custom دوره\u200cای ۰.۰۴/۰.۱/۰.۱۶)
$es_certs_out = '';
foreach (array_values(is_array($es_f['certs'] ?? null) ? $es_f['certs'] : []) as $es_i => $es_row) {
    $es_certs_out .= strtr($ESLCC, [
        '{ES_LC_CC}' => ['0.04', '0.1', '0.16'][$es_i % 3],
        '{ES_LC_CAT}' => (string) ($es_row['cat'] ?? ''),
        '{ES_LC_CODE}' => (string) ($es_row['code'] ?? ''),
        '{ES_LC_TITLE}' => (string) ($es_row['title'] ?? ''),
        '{ES_LC_DESC}' => (string) ($es_row['desc'] ?? ''),
        '{ES_LC_ORG}' => (string) ($es_row['org'] ?? ''),
        '{ES_LC_PERIOD}' => (string) ($es_row['period'] ?? ''),
    ]);
}

// فهرست باکس ستاد
$es_sis_out = '';
foreach (array_values(is_array($es_f['s_items'] ?? null) ? $es_f['s_items'] : []) as $es_row) {
    $es_sis_out .= strtr($ESLCS, ['{ES_LC_SI}' => (string) ($es_row['item'] ?? '')]);
}"""
php_section('licenses-certs', 'سکشن گواهینامه\u200cها و افتخارات', 'licenses-and-honors.html', 'ESLC7',
            lic_skeleton,
            {'{ES_LC_BADGES}': ('$ESLCB', BADGE_TPL), '{ES_LC_TILES}': ('$ESLCC', CERT_TPL), '{ES_LC_SIS}': ('$ESLCS', SIS_TPL)},
            [('{ES_LC_SB}', 's_badge', 'text'), ('{ES_LC_SH}', 's_h2', 'text'), ('{ES_LC_SD}', 's_desc', 'textarea'),
             ('{ES_LC_DT}', 'docs_title', 'text'), ('{ES_LC_DD}', 'docs_desc', 'text'), ('{ES_LC_PHONE}', 'phone', 'text'),
             ('{ES_LC_PT}', 'products_text', 'text'), ('{ES_LC_PU}', 'phone_url', 'url'), ('{ES_LC_SHU}', 'products_url', 'url')],
            lic_loops,
            [('{ES_LC_BADGES}', '$es_badges_out'), ('{ES_LC_TILES}', '$es_certs_out'), ('{ES_LC_SIS}', '$es_sis_out')])

# ── faq-list ──
faq_loops = """// دسته\u200cبندی\u200cهای سایدبار
$es_cats_out = '';
foreach (array_values(is_array($es_f['cats'] ?? null) ? $es_f['cats'] : []) as $es_row) {
    $es_cats_out .= strtr($ESFLC, ['{ES_FL_CAT_R}' => (string) ($es_row['title'] ?? '')]);
}

// آیتم\u200cهای آکاردئون (custom دوره\u200cای + ایندکس data-faq-*)
$es_items_out = '';
$es_cyc = ['0', '0.03', '0.06', '0.09', '0.12', '0.15'];
foreach (array_values(is_array($es_f['items'] ?? null) ? $es_f['items'] : []) as $es_i => $es_row) {
    $es_open = (int) ($es_row['open'] ?? 0) === 1;
    $es_items_out .= strtr($ESFLI, [
        '{ES_FL_C}' => $es_cyc[$es_i % 6],
        '{ES_FL_I}' => (string) $es_i,
        '{ES_FL_SI}' => $es_open ? 'rotate-45 bg-emerald-800 text-white' : 'bg-emerald-100 text-emerald-800',
        '{ES_FL_SB}' => $es_open ? 'block' : 'hidden',
        '{ES_FL_CAT}' => (string) ($es_row['cat'] ?? ''),
        '{ES_FL_Q}' => (string) ($es_row['question'] ?? ''),
        '{ES_FL_A}' => (string) ($es_row['answer'] ?? ''),
    ]);
}"""
php_section('faq-list', 'سکشن سوالات متداول', 'faq.html', 'ESFL7',
            faq_skeleton,
            {'{ES_FL_CATS}': ('$ESFLC', CAT_TPL), '{ES_FL_ITEMS}': ('$ESFLI', ITEM_TPL)},
            [('{ES_FL_AT}', 'aside_title', 'text'), ('{ES_FL_AC}', 'aside_count', 'text'),
             ('{ES_FL_HT}', 'help_title', 'text'), ('{ES_FL_HD}', 'help_desc', 'textarea'),
             ('{ES_FL_PHONE}', 'phone', 'text'), ('{ES_FL_PU}', 'phone_url', 'url'),
             ('{ES_FL_RT}', 'refund_text', 'text'), ('{ES_FL_RU}', 'refund_url', 'url')],
            faq_loops,
            [('{ES_FL_CATS}', '$es_cats_out'), ('{ES_FL_ITEMS}', '$es_items_out')])

# ── refund-policy ──
ref_loops = """// کارت\u200cهای بالا (custom دوره\u200cای ۰/۰.۰۶/۰.۱۲)
$es_cards_out = '';
foreach (array_values(is_array($es_f['cards'] ?? null) ? $es_f['cards'] : []) as $es_i => $es_row) {
    $es_cards_out .= strtr($ESRPC, [
        '{ES_RP_CC}' => ['0', '0.06', '0.12'][$es_i % 3],
        '{ES_RP_CT}' => (string) ($es_row['title'] ?? ''),
        '{ES_RP_CD}' => (string) ($es_row['desc'] ?? ''),
    ]);
}

// تحت پوشش / خارج از شمول
$es_cov_out = '';
foreach (array_values(is_array($es_f['covered'] ?? null) ? $es_f['covered'] : []) as $es_row) {
    $es_cov_out .= strtr($ESRPL, ['{ES_RP_CI}' => (string) ($es_row['item'] ?? '')]);
}

$es_exc_out = '';
foreach (array_values(is_array($es_f['excluded'] ?? null) ? $es_f['excluded'] : []) as $es_row) {
    $es_exc_out .= strtr($ESRPX, ['{ES_RP_XI}' => (string) ($es_row['item'] ?? '')]);
}

// گام\u200cها (شماره فارسی از ایندکس)
$es_steps_out = '';
foreach (array_values(is_array($es_f['steps'] ?? null) ? $es_f['steps'] : []) as $es_i => $es_row) {
    $es_steps_out .= strtr($ESRPS, [
        '{ES_RP_SN}' => es_fa_num($es_i + 1),
        '{ES_RP_ST}' => (string) ($es_row['title'] ?? ''),
        '{ES_RP_SD}' => (string) ($es_row['desc'] ?? ''),
    ]);
}"""
php_section('refund-policy', 'سکشن شرایط گارانتی', 'refund_returns.html', 'ESRP7',
            ref_skeleton,
            {'{ES_RP_CARDS}': ('$ESRPC', CARD_TPL), '{ES_RP_CL}': ('$ESRPL', COV_TPL),
             '{ES_RP_XL}': ('$ESRPX', EXC_TPL), '{ES_RP_SG}': ('$ESRPS', STEP_TPL)},
            [('{ES_RP_CT}', 'c_title', 'text'), ('{ES_RP_CS}', 'c_sub', 'text'),
             ('{ES_RP_XT}', 'x_title', 'text'), ('{ES_RP_XS}', 'x_sub', 'text'),
             ('{ES_RP_PB}', 'p_badge', 'text'), ('{ES_RP_PT}', 'p_title', 'text'),
             ('{ES_RP_PH}', 'phone_text', 'text'), ('{ES_RP_PU}', 'phone_url', 'url'),
             ('{ES_RP_ADD}', 'address', 'textarea'), ('{ES_RP_CTT}', 'cta_text', 'text'), ('{ES_RP_CU}', 'cta_url', 'url')],
            ref_loops,
            [('{ES_RP_CARDS}', '$es_cards_out'), ('{ES_RP_CL}', '$es_cov_out'),
             ('{ES_RP_XL}', '$es_exc_out'), ('{ES_RP_SG}', '$es_steps_out')])

# ── recruit-jobs ──
rec_loops = """// دکمه\u200cهای تب + پنل\u200cها + optionهای فرم
$es_tabs_out = '';
$es_panels_out = '';
$es_orgs_out = '';
$es_jobs_all = array_values(is_array($es_f['jobs'] ?? null) ? $es_f['jobs'] : []);
foreach (array_values(is_array($es_f['tabs'] ?? null) ? $es_f['tabs'] : []) as $es_tab) {
    $es_key = (string) ($es_tab['key'] ?? '');
    $es_open = (int) ($es_tab['open'] ?? 0) === 1;
    $es_btn = (string) ($es_tab['btn'] ?? '');
    $es_tabs_out .= strtr($ESRJB, [
        '{ES_RJ_KEY}' => esc_attr($es_key),
        '{ES_RJ_BS}' => $es_open ? 'bg-emerald-800 text-white shadow-xs' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-900',
        '{ES_RJ_BTN}' => $es_btn,
    ]);
    $es_orgs_out .= '<option value="' . $es_btn . '">' . $es_btn . '</option>';

    $es_jobs_out = '';
    foreach ($es_jobs_all as $es_row) {
        if ((string) ($es_row['tab'] ?? '') !== $es_key) {
            continue;
        }
        $es_skills_out = '';
        foreach (preg_split('/\\r\\n|\\r|\\n/', trim((string) ($es_row['skills'] ?? ''))) as $es_sk) {
            if ($es_sk === '') {
                continue;
            }
            $es_skills_out .= strtr($ESRJS, ['{ES_RJ_SK}' => $es_sk]);
        }
        $es_jobs_out .= strtr($ESRJJ, [
            '{ES_RJ_TYPE}' => (string) ($es_row['type'] ?? ''),
            '{ES_RJ_EXP}' => (string) ($es_row['exp'] ?? ''),
            '{ES_RJ_TITLE}' => (string) ($es_row['title'] ?? ''),
            '{ES_RJ_LOC}' => (string) ($es_row['loc'] ?? ''),
            '{ES_RJ_SKILLS}' => $es_skills_out,
        ]);
    }
    $es_panels_out .= strtr($ESRJP, [
        '{ES_RJ_KEY}' => esc_attr($es_key),
        '{ES_RJ_PS}' => $es_open ? 'block' : 'hidden',
        '{ES_RJ_BADGE}' => (string) ($es_tab['badge'] ?? ''),
        '{ES_RJ_TITLE}' => (string) ($es_tab['title'] ?? ''),
        '{ES_RJ_DESC}' => (string) ($es_tab['desc'] ?? ''),
        '{ES_RJ_JOBS}' => $es_jobs_out,
    ]);
}"""
php_section('recruit-jobs', 'سکشن فرصت\u200cهای شغلی و فرم استخدام', 'recruitment.html', 'ESRJ7',
            rec_skeleton,
            {'{ES_RJ_TABS}': ('$ESRJB', TABBTN_TPL), '{ES_RJ_PANELS}': ('$ESRJP', PANEL_TPL),
             '{ES_RJ_JOBS}': ('$ESRJJ', JOB_TPL), '{ES_RJ_SKILLS}': ('$ESRJS', SKILL_TPL)},
            [('{ES_RJ_HB}', 'hr_badge', 'text'), ('{ES_RJ_H2F}', 'hr_h2', 'text'), ('{ES_RJ_HDF}', 'hr_desc', 'textarea'),
             ('{ES_RJ_AL}', 'addr_label', 'text'), ('{ES_RJ_ADDR}', 'addr', 'textarea'),
             ('{ES_RJ_HL}', 'hours_label', 'text'), ('{ES_RJ_HOURS}', 'hours', 'text'),
             ('{ES_RJ_LN}', 'l_name', 'text'), ('{ES_RJ_PN}', 'ph_name', 'text'),
             ('{ES_RJ_LT}', 'l_tel', 'text'), ('{ES_RJ_PT}', 'ph_tel', 'text'),
             ('{ES_RJ_LO}', 'l_org', 'text'), ('{ES_RJ_LE}', 'l_exp', 'text'),
             ('{ES_RJ_LM}', 'l_msg', 'text'), ('{ES_RJ_PM}', 'ph_msg', 'text')],
            rec_loops,
            [('{ES_RJ_H2}', "(string) ($es_f['h2'] ?? '')"),
             ('{ES_RJ_HD}', "(string) ($es_f['desc'] ?? '')"),
             ('{ES_RJ_TABS}', '$es_tabs_out'),
             ('{ES_RJ_PANELS}', '$es_panels_out'),
             ('{ES_RJ_ORGS}', '$es_orgs_out'),
             ('{ES_RJ_FX}', "(string) apply_filters('es_recruit_form_extra', '', $es_f)"),
             ('{ES_RJ_ST}', "(string) ($es_f['submit'] ?? '')"),
             ('{ES_RJ_OKT}', "(string) ($es_f['ok_t'] ?? '')"),
             ('{ES_RJ_OKD}', "(string) ($es_f['ok_d'] ?? '')"),
             ('{ES_RJ_OKB}', "(string) ($es_f['ok_btn'] ?? '')")])

# ══════════ کلاس LandingSections ══════════
GROUPS = {
    'licenses': [('licenses-hero', 'هیرو مجوزها و افتخارات'), ('licenses-certs', 'گواهینامه\u200cها، افتخارات و ستاد'), ('contact', 'تماس با ما (مشترک با صفحه اصلی)')],
    'faq': [('faq-hero', 'هیرو سوالات متداول'), ('faq-list', 'پرسش و پاسخ\u200cها (آکاردئون)'), ('contact', 'تماس با ما (مشترک با صفحه اصلی)')],
    'refund': [('refund-hero', 'هیرو شرایط گارانتی'), ('refund-policy', 'پوشش گارانتی و مراحل پشتیبانی'), ('contact', 'تماس با ما (مشترک با صفحه اصلی)')],
    'recruitment': [('recruit-hero', 'هیرو فرصت\u200cهای شغلی'), ('recruit-jobs', 'واحدها، موقعیت\u200cها و فرم استخدام')],
}
TEMPLATES = {
    'licenses': 'page-templates/page-licenses.php',
    'faq': 'page-templates/page-faq.php',
    'refund': 'page-templates/page-refund.php',
    'recruitment': 'page-templates/page-recruitment.php',
}
BODY = []
BODY.append('<?php')
BODY.append('/**')
BODY.append(' * ثبت سکشن\u200cهای چهار لندینگ (licenses/faq/refund/recruitment) در SectionManager — ترتیب عین میرور')
BODY.append(' * ref: uploads/static-mirror/{licenses-and-honors,faq,refund_returns,recruitment}.html (تسک ۷.۸)')
BODY.append(' * حالت متای خالی = رندر کامل میرور (پیش\u200cفرض\u200cهای اسکیم = مقادیر میرور)')
BODY.append(' *')
BODY.append(' * @package ErfanSanat')
BODY.append(' */')
BODY.append('declare(strict_types=1);')
BODY.append('')
BODY.append('namespace ErfanSanat\\Theme\\Sections;')
BODY.append('')
BODY.append('final class LandingSections {')
BODY.append('')
BODY.append('    /** ترتیب سکشن\u200cهای هر گروه — عین ترتیب <section>های میرور */')
for g in ('licenses', 'faq', 'refund', 'recruitment'):
    BODY.append('    public const ORDER_' + g.upper() + ' = [')
    for sid, _ in GROUPS[g]:
        BODY.append("            '%s'," % sid)
    BODY.append('    ];')
BODY.append('')
BODY.append('    public static function init(): void {')
BODY.append("        add_filter('es_sections_registry', [self::class, 'register']);")
BODY.append('    }')
BODY.append('')
BODY.append('    /**')
BODY.append('     * ثبت سکشن\u200cهای ۴ گروه با اسکیم فیلدها (مقادیر پیش\u200cفرض = میرور)')
BODY.append('     *')
BODY.append('     * @param array<string,array> $registry')
BODY.append('     * @return array<string,array>')
BODY.append('     */')
BODY.append('    public static function register(array $registry): array {')
for g in GROUPS:
    BODY.append("        $registry['%s'] ??= [];" % g)
BODY.append('')
BODY.append('        // ── تعریف سکشن\u200cها (به ترتیب میرور) ──')
for g, items in GROUPS.items():
    for sid, label in items:
        BODY.append("        $registry['%s'][] = [" % g)
        BODY.append("            'id'     => '%s'," % sid)
        BODY.append("            'label'  => __('%s', 'erfansanat')," % label)
        if sid == 'contact':
            BODY.append("            'schema' => \\ErfanSanat\\Theme\\Sections\\HomeSections::contact_schema(),")
        else:
            BODY.append('            \'schema\' => [')
            BODY.append(schema_rows_php(SCHEMA[sid]))
            BODY.append('            ],')
        BODY.append('        ];')
BODY.append('        return $registry;')
BODY.append('    }')
BODY.append('}')
open(os.path.join(ROOT, 'inc/Sections/LandingSections.php'), 'w', encoding='utf-8').write('\n'.join(BODY) + '\n')
print('  wrote inc/Sections/LandingSections.php')

# ══════════ قالب‌های برگه ══════════
TPL_NAMES = {
    'licenses': 'مجوزها و افتخارات',
    'faq': 'سوالات متداول',
    'refund': 'شرایط گارانتی و خدمات پس از فروش',
    'recruitment': 'فرصت\u200cهای شغلی و استخدام',
}
for g, tname in TPL_NAMES.items():
    php = ("<?php\n"
           "/**\n"
           " * Template Name: %s\n"
           " *\n"
           " * قالب برگه «%s» — رندر سکشن\u200cهای SectionManager (گروه %s)\n"
           " * ترتیب/فعال\u200cبودن و فیلدها از متای _es_sections برگه؛ حالت خالی = عین میرور\n"
           " *\n"
           " * @package ErfanSanat\n"
           " */\n"
           "declare(strict_types=1);\n\n"
           "get_header();\n\n"
           "es_render_sections((int) get_queried_object_id());\n\n"
           "get_footer();\n" % (tname, tname, g))
    open(os.path.join(ROOT, TEMPLATES[g]), 'w', encoding='utf-8').write(php)
    print('  wrote', TEMPLATES[g])

# ══════════ فایل\u200cهای موردانتظار تست ══════════
def page_expected(group):
    parts = {
        'licenses': (HERO_RAW['licenses'], S1, True),
        'faq': (HERO_RAW['faq'], F1, True),
        'refund': (HERO_RAW['refund'], R1, True),
        'recruitment': (HERO_RAW['recruitment'], C1, False),
    }
    h, m1, has_cta = parts[group]
    h = h.replace('href="index.html"', 'href="' + HOME_T + '"')
    if group == 'licenses':
        m1 = m1.replace('href="shop.html"', 'href="' + HOME_T + 'shop/"')
    if group == 'faq':
        m1 = m1.replace('href="refund_returns.html"', 'href="' + HOME_T + 'refund_returns/"')
    if group == 'refund':
        m1 = m1.replace('href="contact.html"', 'href="' + HOME_T + 'contact/"')
    cta = CTA_RAW.replace('href="contact.html"', 'href="' + HOME_T + 'contact/"') if has_cta else ''
    return h + m1 + cta


for g in PAGES:
    exp = page_expected(g)
    open(os.path.join(ROOT, 'temp/p78_expect_%s.html' % g), 'w', encoding='utf-8').write(exp)
    print('  wrote temp/p78_expect_%s.html (%d)' % (g, len(exp)))

# ══════════ پچ SectionManager: TEMPLATE_GROUPS ×۴ ══════════
SM = os.path.join(ROOT, 'inc/Admin/SectionManager.php')
sm = open(SM, encoding='utf-8').read()
anchor = "        'page-templates/page-contact.php' => 'contact',\n    ];"
if 'page-licenses' not in sm:
    assert sm.count(anchor) == 1, 'SM anchor'
    sm = sm.replace(anchor, """        'page-templates/page-contact.php' => 'contact',
        'page-templates/page-licenses.php'   => 'licenses',
        'page-templates/page-faq.php'        => 'faq',
        'page-templates/page-refund.php'     => 'refund',
        'page-templates/page-recruitment.php' => 'recruitment',
    ];""", 1)
    open(SM, 'w', encoding='utf-8').write(sm)
    print('  patched inc/Admin/SectionManager.php (TEMPLATE_GROUPS ×۴)')
else:
    print('  SectionManager: قبلاً پچ شده')

# ══════════ پچ Theme.php: بوت LandingSections ══════════
TH = os.path.join(ROOT, 'inc/Theme.php')
th = open(TH, encoding='utf-8').read()
if 'LandingSections::init' not in th:
    old_boot = """            if (class_exists(Sections\\ContactSections::class)) {
                Sections\\ContactSections::init();
            }
"""
    assert th.count(old_boot) == 1, 'Theme anchor'
    th = th.replace(old_boot, old_boot + """            if (class_exists(Sections\\LandingSections::class)) {
                Sections\\LandingSections::init();
            }
""", 1)
    open(TH, 'w', encoding='utf-8').write(th)
    print('  patched inc/Theme.php (بوت LandingSections)')
else:
    print('  Theme.php: قبلاً پچ شده')

print('gen_p78 done.')
