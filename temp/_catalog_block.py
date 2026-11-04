# ─── کاتالوگ (تب docs) از baloti-string ───
b = open('/home/user/erfansanat-theme-ai/uploads/static-mirror/product/baloti-string.html', encoding='utf-8').read()
cb0 = b.find('<button type="button" data-prod-tab-btn="docs"')
assert cb0 > 0
cb1 = b.find('</button>', cb0) + len('</button>')
BTN_CATALOG = b[cb0:cb1]
BTN_CATALOG = BTN_CATALOG.replace('class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900"',
                                  'class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_BTN_CLS}"', 1)
BTN_CATALOG = BTN_CATALOG.replace('class="lucide lucide-download h-4 w-4 text-emerald-700"', 'class="lucide lucide-download h-4 w-4 {ES_ICON_CLS}"', 1)
assert '{ES_BTN_CLS}' in BTN_CATALOG and '{ES_ICON_CLS}' in BTN_CATALOG
dp0 = b.find('<div data-prod-tab-panel="docs"')
dp1 = b.find('<div data-prod-tab-panel="reviews"')
DOCS = b[dp0:dp1]
dc0 = DOCS.find('<a href="#contact-cta"')
dc1 = DOCS.find('</a>', dc0) + len('</a>')
DOC_HEAD = DOCS[:dc0]
DOC_CARD = DOCS[dc0:dc1]
DOC_CARD = DOC_CARD.replace('href="#contact-cta"', 'href="{ES_URL}" target="_blank" rel="noopener"', 1)
DOC_CARD = DOC_CARD.replace('class="lucide lucide-file-text h-5 w-5"', 'class="{ES_ICON}"', 1)
DOC_CARD = DOC_CARD.replace('<span class="rounded-md bg-slate-200/80 px-2.5 py-1 text-[10px] font-black text-slate-700">PDF</span>',
                            '<span class="rounded-md bg-slate-200/80 px-2.5 py-1 text-[10px] font-black text-slate-700">{ES_BADGE}</span>', 1)
DOC_CARD = DOC_CARD.replace('<div class="text-sm font-extrabold text-slate-900 group-hover:text-emerald-800">کاتالوگ فنی ریسه بلوطی هفت رنگ</div>',
                            '<div class="text-sm font-extrabold text-slate-900 group-hover:text-emerald-800">{ES_TITLE}</div>', 1)
DOC_CARD = DOC_CARD.replace('<div class="mt-1 text-xs text-slate-500">مشخصات و رنگ‌بندی — PDF</div>',
                            '<div class="mt-1 text-xs text-slate-500">{ES_SUB}</div>', 1)
for t in ('{ES_URL}', '{ES_ICON}', '{ES_BADGE}', '{ES_TITLE}', '{ES_SUB}'):
    assert t in DOC_CARD, t
DOC_TAIL = DOCS[dc1:]
import re as _re64
DOC_TAIL = _re64.sub(r'<a href="#contact-cta".*?</a>', '', DOC_TAIL, flags=_re64.S)
assert DOC_TAIL == '</div></div></div></div>', repr(DOC_TAIL)
DOCS_CARDS = [
    ('#contact-cta', 'lucide lucide-file-text h-5 w-5', 'PDF', 'کاتالوگ فنی ریسه بلوطی هفت رنگ', 'مشخصات و رنگ‌بندی — PDF'),
    ('#contact-cta', 'lucide lucide-file-code2 lucide-file-code-2 h-5 w-5', 'DWG', 'نقشه ابعاد و جزئیات اجرایی', 'مناسب طراحی و مشاوره — DWG'),
    ('#contact-cta', 'lucide lucide-file-text h-5 w-5', 'PDF', 'راهنمای نصب و راه‌اندازی', 'PDF مراحل نصب و نکات ایمنی'),
]
reb_docs = DOC_HEAD
for u, ic, bd, tt, sb in DOCS_CARDS:
    reb_docs += DOC_CARD.replace('{ES_URL}', u).replace('{ES_ICON}', ic).replace('{ES_BADGE}', bd).replace('{ES_TITLE}', tt).replace('{ES_SUB}', sb)
reb_docs += DOC_TAIL
assert reb_docs.replace(' target="_blank" rel="noopener"', '') == DOCS, 'docs rebuild mismatch'
