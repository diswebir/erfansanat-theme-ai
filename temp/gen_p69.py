#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
ژنراتور ۶.۹ — سبد خرید ووکامرس (woocommerce/cart/cart.php)
برش بایت‌دقیق از uploads/static-mirror/cart.html + توکن‌های {ES_*}
انحراف‌های مستند از میرور (الزام فرم استاندارد ووکامرس):
  ۱) span[data-cart-row-qty] → input number با name="cart[key][qty]"
  ۲) دکمه‌های حذف/بازنشانی/افزودن سریع → لینک استاندارد ووکامرس
"""
import os

ROOT = '/home/user/erfansanat-theme-ai'
s = open(os.path.join(ROOT, 'uploads/static-mirror/cart.html'), encoding='utf-8').read()


def nowdoc(name, body, tag):
    return "$" + name + " = <<<'{t}'\n{b}\n{t};\n".format(t=tag, b=body)


def tok(R, old, new):
    assert R.count(old) >= 1, old[:70]
    return R.replace(old, new, 1)


# ═══════════ برش رگیون‌ها ═══════════
MAIN_OPEN = '<main class="bg-slate-50/50" data-cart-page="true">'
i1 = s.index(MAIN_OPEN) + len(MAIN_OPEN)
i2 = s.index('<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">', i1)
HERO_RAW = s[i1:i2]

row1 = s.index('<div data-cart-row="true"')
HEAD_RAW = s[i2:row1]

row2 = s.index('<div data-cart-row="true"', row1 + 200)
ROW_RAW = s[row1:row2]

cont_close = s.index('</div><div custom="0.08">', row2) + len('</div>')
row2_core = s[row2:cont_close - len('</div>')]

aside0 = s.index('<aside class="lg:col-span-4', cont_close)
MID_RAW = s[cont_close:aside0]
aside_end = s.index('</aside>', aside0) + len('</aside>')
ASIDE_RAW = s[aside0:aside_end]
main_close = s.index('</main>', aside_end)
FOOT_RAW = s[aside_end:main_close]

# آیتم quick-add
QA_MARK = '<div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-3">'
qa1 = s.index(QA_MARK)
qa2 = s.index(QA_MARK, qa1 + 300)
QA_ITEM_RAW = s[qa1:qa2]

g0 = MID_RAW.index('<div class="grid gap-3 s')
g_end = MID_RAW.index('">', g0) + len('">')
q_end = MID_RAW.index('</div></div></div></div></div>', g_end) + len('</div>')  # بستن خود آیتم سوم داخل run است
items_block = MID_RAW[g_end:q_end]
assert items_block.count(QA_MARK) == 3, 'QA items count'
MID = MID_RAW[:g_end] + '{ES_QA_ITEMS}' + MID_RAW[q_end:]

# ═══════════ HERO ═══════════
HERO = HERO_RAW
HERO = tok(HERO, '<a href="index.html"', '<a href="{ES_HOME_URL}"')
HERO = tok(HERO, '<a href="shop.html"', '<a href="{ES_SHOP_URL}"')

# ═══════════ HEAD ═══════════
HEAD = tok(HEAD_RAW, '<span data-cart-page-count="true" class="rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-extrabold text-emerald-800">۲ کالا</span>',
           '<span data-cart-page-count="true" class="rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-extrabold text-emerald-800">{ES_COUNT_LINE}</span>')
HEAD = tok(HEAD, '<button type="button" data-cart-reset-default="true" class="text-xs font-bold text-emerald-700 hover:underline cursor-pointer">بازنشانی اقلام نمونه کارخانه</button>',
           '<a href="{ES_RESET_URL}" data-cart-reset-default="true" class="text-xs font-bold text-emerald-700 hover:underline cursor-pointer">بازنشانی اقلام نمونه کارخانه</a>')
# فرم استاندارد ووکامرس — شروع بلافاصله قبل از ردیف‌ها
HEAD = HEAD + '<form action="{ES_CART_URL}" method="post" class="woocommerce-cart-form">'

# ═══════════ ROW ═══════════
ROW = tok(ROW_RAW, 'data-cart-slug="needle-string-lights"', 'data-cart-slug="{ES_SLUG}"')
ROW = tok(ROW, 'data-cart-title="ریسه سوزنی شهری ضد آب (IP68)"', 'data-cart-title="{ES_TITLE}"')
ROW = tok(ROW, 'data-cart-price="0"', 'data-cart-price="{ES_PRICE_ATTR}"')
ROW = tok(ROW, 'data-cart-qty="6"', 'data-cart-qty="{ES_QTY_ATTR}"')
ROW = tok(ROW, 'data-cart-min="10"', 'data-cart-min="{ES_MIN_ATTR}"')
ROW = tok(ROW, 'data-cart-step="5"', 'data-cart-step="{ES_STEP_ATTR}"')
ROW = tok(ROW, '<a href="product/needle-string-lights.html" class="h-22 w-22 shrink-0 overflow-hidden rounded-2xl border border-slate-100 bg-slate-100">',
           '<a href="{ES_URL}" class="h-22 w-22 shrink-0 overflow-hidden rounded-2xl border border-slate-100 bg-slate-100">')
ROW = tok(ROW, '<img src="images/string-lights.jpg" alt="ریسه سوزنی شهری ضد آب (IP68)" class="h-full w-full object-cover"/>', '{ES_IMG_TAG}')
ROW = tok(ROW, '<span>ریسه ال ای دی مدل سوزنی</span><span>·</span><span dir="ltr" class="font-mono">SKU: ESE-NSL-7101</span><span>·</span><span class="text-emerald-700">IP68</span>', '{ES_META}')
ROW = tok(ROW, '<a href="product/needle-string-lights.html" class="mt-1 block text-base font-extrabold text-slate-900 hover:text-emerald-700">ریسه سوزنی شهری ضد آب (IP68)</a>',
           '<a href="{ES_URL}" class="mt-1 block text-base font-extrabold text-slate-900 hover:text-emerald-700">{ES_TITLE}</a>')
ROW = tok(ROW, '<div class="mt-1 text-xs font-semibold text-slate-500">ویژگی انتخابی: <span class="text-slate-700">آفتابی گرم · ۶ متری ۲۲۰ ولت</span></div>', '{ES_VARIANT}')
ROW = tok(ROW, '<div class="mt-1.5 inline-block rounded-lg bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-800">حداقل سفارش: ۱۰ · گام افزایش: مضرب ۵</div>', '{ES_MIN_CHIP}')

# دکمه حذف → لینک استاندارد ووکامرس
rb0 = ROW.index('<button type="button" data-cart-row-remove="true"')
rb1 = ROW.index('>', rb0) + 1
rb_tag = ROW[rb0:rb1]
ROW = ROW[:rb0] + '<a href="{ES_REMOVE_URL}"' + rb_tag[len('<button type="button"'):] + ROW[rb1:]
rb2 = ROW.index('</button>', rb0)
ROW = ROW[:rb2] + '</a>' + ROW[rb2 + len('</button>'):]

# span تعداد → input استاندارد ووکامرس
ROW = tok(ROW, '<span data-cart-row-qty="true" class="min-w-12 px-2 text-center font-mono text-sm font-black text-slate-900 tabular-nums">۶</span>',
          '<input type="number" data-cart-row-qty="true" name="cart[{ES_KEY}][qty]" value="{ES_QTY_ATTR}" min="{ES_MIN_ATTR}" max="" step="{ES_STEP_ATTR}" inputmode="numeric" aria-label="تعداد" class="min-w-12 bg-transparent px-2 text-center font-mono text-sm font-black text-slate-900 tabular-nums outline-none"/>')
ROW = tok(ROW, '<div class="text-[11px] font-medium text-slate-400">فی واحد: ۰ تومان</div>', '<div class="text-[11px] font-medium text-slate-400">فی واحد: {ES_UNIT}</div>')
ROW = tok(ROW, '<div data-cart-row-total="true" class="text-sm font-black text-emerald-800 tabular-nums sm:text-base">۰ تومان</div>',
          '<div data-cart-row-total="true" class="text-sm font-black text-emerald-800 tabular-nums sm:text-base">{ES_ROW_TOTAL}</div>')

# ═══════════ MID (quick-add) ═══════════
MID = tok(MID, '<a href="shop.html" class="text-xs font-bold text-emerald-700 hover:underline">', '<a href="{ES_SHOP_URL}" class="text-xs font-bold text-emerald-700 hover:underline">')

# ═══════════ QA_ITEM ═══════════
QA = tok(QA_ITEM_RAW, '<a href="product/point-light-pixel.html" class="block truncate text-xs font-extrabold text-slate-800 hover:text-emerald-700">پوینت لایت پیکسل ۴ سانتی فول‌کالر</a>',
         '<a href="{ES_URL}" class="block truncate text-xs font-extrabold text-slate-800 hover:text-emerald-700">{ES_TITLE}</a>')
QA = tok(QA, '<div class="mt-0.5 text-[11px] font-bold text-emerald-700">۹۸,۰۰۰ تومان</div>', '<div class="mt-0.5 text-[11px] font-bold text-emerald-700">{ES_PRICE}</div>')
QA = tok(QA, 'data-product-slug="point-light-pixel"', 'data-product-slug="{ES_SLUG}"')
QA = tok(QA, 'data-product-title="پوینت لایت پیکسل ۴ سانتی فول‌کالر"', 'data-product-title="{ES_TITLE}"')
QA = tok(QA, 'data-product-price="0"', 'data-product-price="{ES_PRICE_ATTR}"')
QA = tok(QA, 'data-product-img="/images/light-tunnel.jpg"', 'data-product-img="{ES_IMG}"')
QA = tok(QA, 'data-product-min="50"', 'data-product-min="{ES_MIN}"')
QA = tok(QA, 'data-product-step="10"', 'data-product-step="{ES_STEP}"')
qb0 = QA.index('<button type="button" data-quick-add-cart="true"')
qb1 = QA.index('>', qb0) + 1
qb_tag = QA[qb0:qb1]
QA = QA[:qb0] + '<a href="{ES_ADD_URL}"' + qb_tag[len('<button type="button"'):] + QA[qb1:]
qb2 = QA.index('</button>', qb0)
QA = QA[:qb2] + '</a>' + QA[qb2 + len('</button>'):]

# ═══════════ ASIDE ═══════════
ASIDE = tok(ASIDE_RAW, '<span data-cart-subtotal="true" class="font-extrabold text-slate-900 tabular-nums">۰ تومان</span>',
            '<span data-cart-subtotal="true" class="font-extrabold text-slate-900 tabular-nums">{ES_SUBTOTAL}</span>')
ASIDE = tok(ASIDE, '<span data-cart-grand-total="true" class="text-xl font-black text-emerald-800 tabular-nums">۰ تومان</span>',
            '<span data-cart-grand-total="true" class="text-xl font-black text-emerald-800 tabular-nums">{ES_GRAND}</span>')
ASIDE = tok(ASIDE, '<a href="checkout.html" class="btn-primary w-full justify-center py-3.5 text-sm whitespace-nowrap">',
            '<a href="{ES_CHECKOUT_URL}" class="btn-primary w-full justify-center py-3.5 text-sm whitespace-nowrap">')

# ═══════════ EMPTY (خارج از میرور — الگوی جعبه خالی ۶.۸) ═══════════
EMPTY = ('<div data-cart-empty="true" class="rounded-3xl border border-dashed border-slate-300 bg-slate-50/70 px-6 py-16 text-center">'
         '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto h-10 w-10 text-slate-300" aria-hidden="true"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg>'
         '<p class="mt-4 text-[14.5px] font-extrabold text-slate-700">سبد خرید شما خالی است.</p>'
         '<p class="mt-1.5 text-[12.5px] text-slate-500">محصولات تولیدی کارخانه را مرور کنید و اقلام سفارش خود را اضافه نمایید.</p>'
         '<a href="{ES_SHOP_URL}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-bold text-white hover:bg-emerald-800">'
         '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>'
         '<span>بازگشت به فروشگاه محصولات</span></a></div>')

# ═══════════ assert بازسازی بایت‌دقیق (با سه انحراف مستند روی مرجع) ═══════════
INP = '<input type="number" data-cart-row-qty="true" name="cart[{key}][qty]" value="{qty}" min="{min}" max="" step="{step}" inputmode="numeric" aria-label="تعداد" class="min-w-12 bg-transparent px-2 text-center font-mono text-sm font-black text-slate-900 tabular-nums outline-none"/>'

def m_row_swaps(html, f):
    """سه انحراف مستند روی برش مرجع میرور اعمال می‌شود"""
    b0 = html.index('<button type="button" data-cart-row-remove="true"')
    b1 = html.index('>', b0) + 1
    html = html[:b0] + '<a href="' + f['remove'] + '"' + html[b0 + len('<button type="button"'):b1] + html[b1:]
    b2 = html.index('</button>', b0)
    html = html[:b2] + '</a>' + html[b2 + len('</button>'):]
    s0 = html.index('<span data-cart-row-qty')
    s1 = html.index('</span>', s0) + len('</span>')
    inp = INP.format(key=f['key'], qty=f['qty'], min=f['min'], step=f['step'])
    return html[:s0] + inp + html[s1:]

R1F = dict(slug='needle-string-lights', title='ریسه سوزنی شهری ضد آب (IP68)', url='product/needle-string-lights.html',
           img='<img src="images/string-lights.jpg" alt="ریسه سوزنی شهری ضد آب (IP68)" class="h-full w-full object-cover"/>',
           meta='<span>ریسه ال ای دی مدل سوزنی</span><span>·</span><span dir="ltr" class="font-mono">SKU: ESE-NSL-7101</span><span>·</span><span class="text-emerald-700">IP68</span>',
           variant='<div class="mt-1 text-xs font-semibold text-slate-500">ویژگی انتخابی: <span class="text-slate-700">آفتابی گرم · ۶ متری ۲۲۰ ولت</span></div>',
           chip='<div class="mt-1.5 inline-block rounded-lg bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-800">حداقل سفارش: ۱۰ · گام افزایش: مضرب ۵</div>',
           price_attr='0', qty='6', min='10', step='5', key='AB12',
           unit='۰ تومان', total='۰ تومان', remove='?remove_item=AB12&_wpnonce=x')
R2F = dict(slug='point-light-pixel', title='پوینت لایت پیکسل ۴ سانتی فول‌کالر', url='product/point-light-pixel.html',
           img='<img src="images/light-tunnel.jpg" alt="پوینت لایت پیکسل ۴ سانتی فول‌کالر" class="h-full w-full object-cover"/>',
           meta='<span>پوینت لایت پیکسل LED</span><span>·</span><span dir="ltr" class="font-mono">SKU: ESE-PLP-040</span><span>·</span><span class="text-emerald-700">IP68</span>',
           variant='<div class="mt-1 text-xs font-semibold text-slate-500">ویژگی انتخابی: <span class="text-slate-700">فول‌کالر هوشمند (RGB) · قاب PLT</span></div>',
           chip='<div class="mt-1.5 inline-block rounded-lg bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-800">حداقل سفارش: ۵۰ · گام افزایش: مضرب ۱۰</div>',
           price_attr='0', qty='50', min='50', step='10', key='CD34',
           unit='۰ تومان', total='۰ تومان', remove='?remove_item=CD34&_wpnonce=x')

def row_r(f):
    out = ROW
    for k, v in [('ES_SLUG', f['slug']), ('ES_TITLE', f['title']), ('ES_PRICE_ATTR', f['price_attr']),
                 ('ES_QTY_ATTR', f['qty']), ('ES_MIN_ATTR', f['min']), ('ES_STEP_ATTR', f['step']),
                 ('ES_URL', f['url']), ('ES_IMG_TAG', f['img']), ('ES_META', f['meta']),
                 ('ES_VARIANT', f['variant']), ('ES_MIN_CHIP', f['chip']), ('ES_REMOVE_URL', f['remove']),
                 ('ES_KEY', f['key']), ('ES_UNIT', f['unit']), ('ES_ROW_TOTAL', f['total'])]:
        out = out.replace('{' + k + '}', v)
    return out

assert row_r(R1F) == m_row_swaps(ROW_RAW, R1F), 'ROW rebuild 1 failed'
assert row_r(R2F) == m_row_swaps(row2_core, R2F), 'ROW rebuild 2 failed'

QA1F = dict(url='product/point-light-pixel.html', title='پوینت لایت پیکسل ۴ سانتی فول‌کالر', price='۹۸,۰۰۰ تومان',
            slug='point-light-pixel', price_attr='0', img='/images/light-tunnel.jpg', min='50', step='10', add='?add-to-cart=202')
QA2F = dict(url='product/point-light-pixel-6cm.html', title='پوینت لایت پیکسل ۶ سانتی پرقدرت', price='۱۳۵,۰۰۰ تومان',
            slug='point-light-pixel-6cm', price_attr='0', img='/images/light-sphere.jpg', min='50', step='10', add='?add-to-cart=203')
QA3F = dict(url='product/needle-string-lights.html', title='ریسه سوزنی شهری ضد آب (IP68)', price='۵۹۰,۰۰۰ تومان',
            slug='needle-string-lights', price_attr='0', img='/images/string-lights.jpg', min='10', step='5', add='?add-to-cart=201')

def qa_r(f):
    out = QA
    for k, v in [('ES_URL', f['url']), ('ES_TITLE', f['title']), ('ES_PRICE', f['price']), ('ES_SLUG', f['slug']),
                 ('ES_PRICE_ATTR', f['price_attr']), ('ES_IMG', f['img']), ('ES_MIN', f['min']),
                 ('ES_STEP', f['step']), ('ES_ADD_URL', f['add'])]:
        out = out.replace('{' + k + '}', v)
    return out

def m_qa_swaps(html, f):
    b0 = html.index('<button type="button" data-quick-add-cart="true"')
    b1 = html.index('>', b0) + 1
    html = html[:b0] + '<a href="' + f['add'] + '"' + html[b0 + len('<button type="button"'):b1] + html[b1:]
    b2 = html.index('</button>', b0)
    return html[:b2] + '</a>' + html[b2 + len('</button>'):]

assert qa_r(QA1F) == m_qa_swaps(QA_ITEM_RAW, QA1F), 'QA rebuild failed'
_qa_parts = items_block.split(QA_MARK)[1:]
assert len(_qa_parts) == 3, 'QA split'
assert qa_r(QA1F) == m_qa_swaps(QA_MARK + _qa_parts[0], QA1F), 'QA item1'
assert qa_r(QA2F) == m_qa_swaps(QA_MARK + _qa_parts[1], QA2F), 'QA item2'
assert qa_r(QA3F) == m_qa_swaps(QA_MARK + _qa_parts[2], QA3F), 'QA item3'

# سربرگ: بازنشانی button→a + form-open
def m_head_swaps(html):
    b0 = html.index('<button type="button" data-cart-reset-default="true"')
    b1 = html.index('>', b0) + 1
    html = html[:b0] + '<a href="?empty-cart=1"' + html[b0 + len('<button type="button"'):b1] + html[b1:]
    b2 = html.index('</button>', b0)
    html = html[:b2] + '</a>' + html[b2 + len('</button>'):]
    return html + FORM_OPEN

FORM_OPEN = '<form action="?cart=1" method="post" class="woocommerce-cart-form">'
def head_r():
    return HEAD.replace('{ES_COUNT_LINE}', '۲ کالا').replace('{ES_RESET_URL}', '?empty-cart=1').replace('{ES_CART_URL}', '?cart=1')

def hero_r():
    return HERO.replace('{ES_HOME_URL}', 'index.html').replace('{ES_SHOP_URL}', 'shop.html')

assert head_r() == m_head_swaps(HEAD_RAW), 'HEAD rebuild failed'
assert hero_r() == HERO_RAW, 'HERO rebuild failed'

MID_r = MID.replace('{ES_QA_ITEMS}', items_block).replace('{ES_SHOP_URL}', 'shop.html')
ASIDE_r = ASIDE.replace('{ES_SUBTOTAL}', '۰ تومان').replace('{ES_GRAND}', '۰ تومان').replace('{ES_CHECKOUT_URL}', 'checkout.html')
assert MID_r == MID_RAW, 'MID rebuild failed'
assert ASIDE_r == ASIDE_RAW, 'ASIDE rebuild failed'

reb = hero_r() + head_r() + row_r(R1F) + row_r(R2F) + '</div><button type="submit" name="update_cart" value="1" class="hidden" aria-label="بروزرسانی سبد خرید" tabindex="-1"></button></form>' + MID.replace('{ES_QA_ITEMS}', qa_r(QA1F) + qa_r(QA2F) + qa_r(QA3F)).replace('{ES_SHOP_URL}', 'shop.html') + ASIDE_r + FOOT_RAW
raw_sum = HERO_RAW + HEAD_RAW + ROW_RAW + row2_core + '</div>' + MID_RAW + ASIDE_RAW + FOOT_RAW
assert raw_sum == s[i1:main_close], 'fragment boundaries drift: %d vs %d' % (len(raw_sum), len(s[i1:main_close]))
ref_mid = MID_RAW[:g_end] + ''.join(m_qa_swaps(QA_MARK + part, f) for part, f in zip(items_block.split(QA_MARK)[1:], (QA1F, QA2F, QA3F))) + MID_RAW[q_end:]
ref = HERO_RAW + m_head_swaps(HEAD_RAW) + m_row_swaps(ROW_RAW, R1F) + m_row_swaps(row2_core, R2F) + '</div><button type="submit" name="update_cart" value="1" class="hidden" aria-label="بروزرسانی سبد خرید" tabindex="-1"></button></form>' + ref_mid + ASIDE_RAW + FOOT_RAW
assert reb == ref, 'FULL rebuild failed: %d vs %d' % (len(reb), len(ref))
print('cart slices OK; hero:', len(HERO_RAW), 'head:', len(HEAD_RAW), 'row:', len(ROW_RAW), 'mid:', len(MID_RAW), 'aside:', len(ASIDE_RAW))

# ═══════════ نوشتن woocommerce/cart/cart.php ═══════════
os.makedirs(os.path.join(ROOT, 'woocommerce/cart'), exist_ok=True)
PHP = (
"<?php\n"
"/**\n"
" * سبد خرید ووکامرس — برش بایت‌دقیق میرور cart.html\n"
" * فرگمنت‌ها با temp/gen_p69.py تولید شده‌اند.\n"
" *\n"
" * انحراف‌های مستند از میرور (الزام فرم استاندارد ووکامرس — بدون JS دستی):\n"
" *  ۱) span[data-cart-row-qty] → input number با name=\"cart[key][qty]\" + دکمه update_cart\n"
" *  ۲) دکمه‌های حذف/بازنشانی/افزودن سریع → لینک‌های استاندارد ووکامرس (data-hook حفظ شده)\n"
" *\n"
" * @package ErfanSanat\n"
" */\n"
"declare(strict_types=1);\n\n"
+ nowdoc('ES_HERO', HERO, 'ESP69H')
+ nowdoc('ES_HEAD', HEAD, 'ESP69A')
+ nowdoc('ES_ROW', ROW, 'ESP69R')
+ nowdoc('ES_MID', MID, 'ESP69M')
+ nowdoc('ES_QA', QA, 'ESP69Q')
+ nowdoc('ES_ASIDE', ASIDE, 'ESP69S')
+ nowdoc('ES_FOOT', FOOT_RAW, 'ESP69F')
+ nowdoc('ES_EMPTY', EMPTY, 'ESP69E')
+ """if (!function_exists('WC')) {
    return; // بدون ووکامرس چیزی رندر نمی‌شود
}
$es_cart = WC()->cart ?? null;
if (!$es_cart) {
    return;
}

do_action('woocommerce_before_cart');

// اعلان‌های ووکامرس با استایل میرور
$es_notices = function_exists('wc_get_notices') ? (array) wc_get_notices() : [];
foreach ($es_notices as $es_type => $es_list) {
    $es_cls = $es_type === 'error'
        ? 'rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700'
        : 'rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-800';
    foreach ((array) $es_list as $es_notice) {
        $es_msg = is_array($es_notice) ? (string) ($es_notice['notice'] ?? '') : (string) $es_notice;
        echo '<div class="woocommerce-' . esc_attr((string) $es_type) . ' ' . $es_cls . '" role="alert">' . esc_html($es_msg) . '</div>';
    }
}
if ($es_notices !== [] && function_exists('wc_clear_notices')) {
    wc_clear_notices();
}

// حالت سبد خالی
// کلیدهای سبد ووکامرس باید حفظ شوند (array_values ممنوع)
$es_items = array_filter((array) $es_cart->get_cart(), 'is_array');
if ($es_items === []) {
    echo strtr($ES_EMPTY, [
        '{ES_SHOP_URL}' => esc_url(es_page_url('shop')),
    ]);
    do_action('woocommerce_after_cart');
    return;
}

// هیرو (breadcrumb + عنوان)
echo strtr($ES_HERO, [
    '{ES_HOME_URL}' => esc_url(home_url('/')),
    '{ES_SHOP_URL}' => esc_url(es_page_url('shop')),
]);

// سربرگ گرید (شمارنده + بازنشانی) + شروع فرم استاندارد
$es_reset_url = function_exists('wc_get_cart_url') ? add_query_arg('empty-cart', '1', wc_get_cart_url()) : home_url('/cart/?empty-cart=1');
echo strtr($ES_HEAD, [
    '{ES_COUNT_LINE}'   => esc_html(es_fa_num((string) $es_cart->get_cart_contents_count()) . ' کالا'),
    '{ES_RESET_URL}'    => esc_url($es_reset_url),
    '{ES_CART_URL}'     => esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')),
]);

// ردیف‌های سبد با هوک‌های استاندارد woocommerce_cart_item_*
foreach ($es_items as $es_key => $es_item) {
    $_product = $es_item['data'] ?? null;
    if (!is_object($_product) && function_exists('wc_get_product')) {
        $_product = wc_get_product((int) ($es_item['product_id'] ?? 0));
    }
    if (!is_object($_product)) {
        continue;
    }
    $es_pid  = (int) ($es_item['product_id'] ?? 0);
    $es_qty  = max(1, (int) ($es_item['quantity'] ?? 1));
    $es_min  = max(1, (int) $_product->get_min_purchase_quantity());
    $es_step = max(1, (int) apply_filters('woocommerce_quantity_input_step', 1, $_product));
    $es_url  = method_exists($_product, 'get_permalink') ? (string) $_product->get_permalink() : (string) get_permalink($es_pid);
    $es_name = apply_filters('woocommerce_cart_item_name', (string) $_product->get_name(), $es_item, $es_key);
    $es_img_tag = apply_filters('woocommerce_cart_item_thumbnail', '', $es_item, $es_key);
    if ($es_img_tag === '') {
        $es_thumb = (int) get_post_meta($es_pid, '_thumbnail_id', true);
        $es_img_url = es_media_url($es_thumb > 0 ? $es_thumb : 'assets/images/light-tunnel.jpg');
        $es_img_tag = '<img src="' . esc_url($es_img_url) . '" alt="' . esc_attr($es_name) . '" class="h-full w-full object-cover"/>';
    }
    $es_price    = apply_filters('woocommerce_cart_item_price', (string) WC()->cart->get_product_price($_product), $es_item, $es_key);
    $es_subtotal = apply_filters('woocommerce_cart_item_subtotal', (string) WC()->cart->get_product_subtotal($_product, $es_qty), $es_item, $es_key);

    // خط متا: دسته · SKU · IP
    $es_cat = '';
    foreach ((array) wp_get_post_terms($es_pid, 'product_cat') as $es_t) {
        if (is_object($es_t) && ($es_t->name ?? '') !== '') {
            $es_cat = (string) $es_t->name;
            break;
        }
    }
    $es_ip = '';
    foreach ((array) wp_get_post_terms($es_pid, 'pa_ip-rating') as $es_t) {
        if (is_object($es_t) && ($es_t->name ?? '') !== '') {
            $es_ip = (string) $es_t->name;
            break;
        }
    }
    $es_sku = (string) $_product->get_sku();
    $es_meta = ($es_cat !== '' ? '<span>' . esc_html($es_cat) . '</span><span>·</span>' : '')
        . '<span dir="ltr" class="font-mono">SKU: ' . esc_html($es_sku) . '</span>'
        . ($es_ip !== '' ? '<span>·</span><span class="text-emerald-700">' . esc_html($es_ip) . '</span>' : '');

    $es_variant = (string) apply_filters('es_cart_item_variant', (string) ($es_item['es_variant'] ?? ''), $es_item, $es_key);
    $es_variant_html = $es_variant !== ''
        ? '<div class="mt-1 text-xs font-semibold text-slate-500">ویژگی انتخابی: <span class="text-slate-700">' . esc_html($es_variant) . '</span></div>'
        : '';
    $es_remove = function_exists('wc_get_cart_remove_url') ? wc_get_cart_remove_url($es_key) : '';

    echo strtr($ES_ROW, [
        '{ES_SLUG}'       => esc_attr((string) ($_product->get_slug() ?? '')),
        '{ES_TITLE}'      => $es_name,
        '{ES_PRICE_ATTR}' => esc_attr((string) ($_product->get_price() ?? '0')),
        '{ES_QTY_ATTR}'   => (string) $es_qty,
        '{ES_MIN_ATTR}'   => (string) $es_min,
        '{ES_STEP_ATTR}'  => (string) $es_step,
        '{ES_URL}'        => esc_url($es_url),
        '{ES_IMG_TAG}'    => $es_img_tag,
        '{ES_META}'       => $es_meta,
        '{ES_VARIANT}'    => $es_variant_html,
        '{ES_MIN_CHIP}'   => '<div class="mt-1.5 inline-block rounded-lg bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-800">' . esc_html('حداقل سفارش: ' . es_fa_num((string) $es_min) . ' · گام افزایش: مضرب ' . es_fa_num((string) $es_step)) . '</div>',
        '{ES_REMOVE_URL}' => esc_url($es_remove),
        '{ES_KEY}'        => esc_attr((string) $es_key),
        '{ES_UNIT}'       => $es_price,
        '{ES_ROW_TOTAL}'  => $es_subtotal,
    ]);
}
echo '</div>';
wp_nonce_field('woocommerce-cart');
echo '<button type="submit" name="update_cart" value="1" class="hidden" aria-label="بروزرسانی سبد خرید" tabindex="-1"></button>';
echo '</form>';

// افزودن سریع مکمل‌ها (cross-sell → fallback جدیدترین محصولات)
$es_cross = array_slice(array_map('intval', (array) $es_cart->get_cross_sells()), 0, 3);
if ($es_cross === []) {
    $es_in_cart = array_map(static fn($row): int => (int) ($row['product_id'] ?? 0), $es_items);
    $es_cross = array_map(static fn($p): int => (int) ($p->ID ?? 0), (array) get_posts([
        'post_type'      => 'product',
        'posts_per_page' => 3,
        'post__not_in'   => $es_in_cart,
    ]));
}
$es_qa = '';
foreach ($es_cross as $es_cid) {
    if ($es_cid < 1) {
        continue;
    }
    $es_cp = function_exists('wc_get_product') ? wc_get_product($es_cid) : null;
    if (!is_object($es_cp)) {
        continue;
    }
    $es_cthumb = (int) get_post_meta($es_cid, '_thumbnail_id', true);
    $es_qa .= strtr($ES_QA, [
        '{ES_URL}'        => esc_url((string) get_permalink($es_cid)),
        '{ES_TITLE}'      => esc_html((string) get_the_title($es_cid)),
        '{ES_PRICE}'      => esc_html((string) $es_cp->get_price_html()),
        '{ES_SLUG}'       => esc_attr((string) ($es_cp->get_slug() ?? '')),
        '{ES_PRICE_ATTR}' => esc_attr((string) ($es_cp->get_price() ?? '0')),
        '{ES_IMG}'        => esc_attr(es_media_url($es_cthumb > 0 ? $es_cthumb : 'assets/images/light-tunnel.jpg')),
        '{ES_MIN}'        => esc_attr((string) max(1, (int) $es_cp->get_min_purchase_quantity())),
        '{ES_STEP}'       => esc_attr((string) max(1, (int) apply_filters('woocommerce_quantity_input_step', 1, $es_cp))),
        '{ES_ADD_URL}'    => esc_url((string) (function_exists('wc_get_cart_url') ? wc_get_cart_url() : '')) . '?add-to-cart=' . absint($es_cid),
    ]);
}
echo strtr($ES_MID, [
    '{ES_QA_ITEMS}' => $es_qa,
    '{ES_SHOP_URL}' => esc_url(es_page_url('shop')),
]);

// خلاصه صورت‌حساب
echo strtr($ES_ASIDE, [
    '{ES_SUBTOTAL}'    => esc_html((string) apply_filters('woocommerce_cart_subtotal', (string) $es_cart->get_cart_subtotal(), false, $es_cart)),
    '{ES_GRAND}'       => esc_html((string) $es_cart->get_total()),
    '{ES_CHECKOUT_URL}' => esc_url((string) (function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '')),
]);
echo $ES_FOOT;
do_action('woocommerce_after_cart');
"""
)
open(os.path.join(ROOT, 'woocommerce/cart/cart.php'), 'w', encoding='utf-8').write(PHP)
print('cart.php written:', len(PHP))
