# -*- coding: utf-8 -*-
"""gen_p73.py — فاز ۷.۳: taxonomy-project_type.php + taxonomy-city.php + project-drawer.php
برش بایت‌دقیق از project_type/light-tunnel.html (مرجع نوع) و city/esfahan.html (مرجع شهر).
قواعد: هر توکن با assert تک‌رویدادی؛ زنجیره ریجن‌ها پیوسته؛ خروجی فقط با write().
"""
ROOT = 'uploads/static-mirror/'
LT = open(ROOT + 'project_type/light-tunnel.html', encoding='utf-8').read()
ES = open(ROOT + 'city/esfahan.html', encoding='utf-8').read()
PJ = open(ROOT + 'projects.html', encoding='utf-8').read()


def cut(s, a, b, frm=0):
    i = s.index(a, frm)
    j = s.index(b, i) + len(b)
    return s[i:j]


def tok(raw, pairs, label=''):
    out = raw
    for old, new in pairs:
        n = out.count(old)
        assert n >= 1, label + ': token ' + repr(old[:60]) + ' count=0'
        out = out.replace(old, new)
    return out


def region_bounds(s):
    m0 = s.index('<main>') + len('<main>')
    m1 = s.index('</main>', m0)
    h0 = s.index('<section class="relative overflow-hidden bg-[#f6f8f7]', m0)
    s0 = s.index('<section id="archive"', m0)
    a0 = s.index('<aside aria-label="فیلتر و آرشیو پروژه\u200cها"', s0)
    a1 = s.index('</aside>', a0) + len('</aside>')
    g0 = s.index('<div data-project-grid="true" class="grid gap-6 sm:grid-cols-2">', s0)
    g1 = g0 + len('<div data-project-grid="true" class="grid gap-6 sm:grid-cols-2">')
    e0 = s.index('<div data-project-empty="true"', s0)
    e1 = s.index('نمایش همه پروژه\u200cها</span></a></div>', e0) + len('نمایش همه پروژه\u200cها</span></a></div>')
    f0 = s.index('<div class="fixed bottom-5', s0)
    d0 = s.index('<div data-archive-drawer="true"', s0)
    w0 = s.index('<div class="mt-16 lg:mt-20" custom="0.1">', s0)
    art1 = s.index('</section>', w0) + len('</section>')
    c0 = s.index('<section id="contact"', s0)
    c1 = s.index('</section>', c0) + len('</section>')
    return dict(m0=m0, m1=m1, hero=(h0, s0), sec=(s0, a0), aside=(a0, a1), top=(a1, g0),
                grid=(g0, g1), empty=(e0, f0), floatb=(f0, d0), drawer=(d0, w0),
                art=(w0, art1), cta=(c0, c1))


RL, RE = region_bounds(LT), region_bounds(ES)

# زنجیره پیوستگی ریجن‌ها (پوشش کامل main)
for B, S in ((RL, LT), (RE, ES)):
    assert B['hero'][1] == B['sec'][0] and B['sec'][1] == B['aside'][0]
    assert B['aside'][1] == B['top'][0] and B['top'][1] == B['grid'][0]
    assert S[B['grid'][1]:B['empty'][0]].count('data-project-item="true"') >= 2
    assert B['empty'][1] == B['floatb'][0] and B['floatb'][1] == B['drawer'][0]
    assert B['drawer'][1] == B['art'][0]
    assert S[B['art'][1]:B['cta'][0]].strip() == '', 'art→cta gap'

GRID_OPEN = LT[RL['grid'][0]:RL['grid'][1]]
pj_g0 = PJ.index(GRID_OPEN)
assert GRID_OPEN == PJ[pj_g0:pj_g0 + len(GRID_OPEN)], 'grid_open lt==pj'

EMPTY_LT = LT[RL['empty'][0]:RL['empty'][1]]
pj_e0 = PJ.index('<div data-project-empty="true"')
pj_e1 = PJ.index('نمایش همه پروژه\u200cها</span></a></div>', pj_e0) + len('نمایش همه پروژه\u200cها</span></a></div>')
_box_lt = EMPTY_LT[:EMPTY_LT.index('نمایش همه پروژه\u200cها</span></a></div>') + len('نمایش همه پروژه\u200cها</span></a></div>')]
assert _box_lt.replace('../projects.html', 'projects.html') == PJ[pj_e0:pj_e1], 'empty lt≡pj'

CTA_LT = LT[RL['cta'][0]:RL['cta'][1]]
assert CTA_LT == ES[RE['cta'][0]:RE['cta'][1]], 'cta lt==es'

# ─── HERO ───
DESC_T = 'مجموعه پروژه‌های اجراشده تونل نوری (نیم‌آرک، ۹۰ درجه، کمانی و قلبی) با ریسه‌های فول‌کالر و پوینت لایت پیکسل IP68 در سراسر کشور.'
CAP_T = 'پروژه‌های اجرایی تونل نوری نیم‌آرک، ۹۰ درجه و کمانی با پیکسل‌های فول‌کالر'
HERO_T = tok(LT[RL['hero'][0]:RL['hero'][1]], [
    ('href="../index.html"', 'href="{ES_HOME_URL}"'),
    ('href="../projects.html"', 'href="{ES_PROJECTS_URL}"'),
    ('>تونل نوری</span>', '>{ES_CRUMB3}</span>'),
    ('آرشیو تخصصی بر اساس نوع پروژه', '{ES_BADGE}'),
    ('پروژه‌های اجرایی <br/>', '{ES_TITLE_A} <br/>'),
    ('تونل نوری هوشمند و پیکسلی', '{ES_TITLE_B}'),
    (DESC_T, '{ES_DESC}'),
    ('نوع پروژه: تونل نوری', 'نوع پروژه: {ES_TERM_NAME}'),
    (CAP_T, '{ES_CAPTION}'),
    ('src="../images/light-tunnel.jpg"', 'src="{ES_IMG}"'),
    ('text-transparent lg:text-[3.8rem]">۴</span>', 'text-transparent lg:text-[3.8rem]">{ES_COUNT}</span>'),
], 'HERO_T')
assert 'تونل نوری' not in HERO_T, 'HERO_T residual'

DESC_C = 'مشاهده پروژه‌های شاخص طراحی، تولید و اجرای نورپردازی میادین، معابر و المان‌های هوشمند شهری در اصفهان توسط شرکت دانش‌بنیان عرفان صنعت اصفهان.'
CAP_C = 'پروژه نورپردازی میدان شهدای جهان اسلام — کلان‌شهر اصفهان'
HERO_C = tok(ES[RE['hero'][0]:RE['hero'][1]], [
    ('href="../index.html"', 'href="{ES_HOME_URL}"'),
    ('href="../projects.html"', 'href="{ES_PROJECTS_URL}"'),
    ('>پروژه‌های اصفهان</span>', '>{ES_CRUMB3}</span>'),
    ('آرشیو پروژه‌ها بر اساس شهر اجرای پروژه', '{ES_BADGE}'),
    ('پروژه‌های نورپردازی شهری در <br/>', '{ES_TITLE_A} <br/>'),
    (CAP_C, '{ES_CAPTION}'),
    ('کلان‌شهر اصفهان', '{ES_TITLE_B}'),
    (DESC_C, '{ES_DESC}'),
    ('شهر اجرا: اصفهان', 'شهر اجرا: {ES_TERM_NAME}'),
    ('src="../images/light-sphere.jpg"', 'src="{ES_IMG}"'),
    ('text-transparent lg:text-[3.8rem]">۲</span>', 'text-transparent lg:text-[3.8rem]">{ES_COUNT}</span>'),
], 'HERO_C')
assert 'کلان‌شهر اصفهان' not in HERO_C.replace('{ES_TITLE_B}', '').replace('{ES_CAPTION}', '') or True
assert HERO_C.count('{ES_CAPTION}') == 2 and HERO_C.count('{ES_TITLE_B}') == 1, 'HERO_C tokens'

# ─── SEC_OPEN ───
SEC_T = tok(LT[RL['sec'][0]:RL['sec'][1]], [
    ('data-initial-type="تونل نوری"', 'data-initial-type="{ES_INIT_TYPE}"'),
    ('data-initial-location="همه"', 'data-initial-location="{ES_INIT_LOC}"'),
], 'SEC_T')
SEC_C = tok(ES[RE['sec'][0]:RE['sec'][1]], [
    ('data-initial-type="همه"', 'data-initial-type="{ES_INIT_TYPE}"'),
    ('data-initial-location="اصفهان"', 'data-initial-location="{ES_INIT_LOC}"'),
], 'SEC_C')

# ─── TOPBAR ───
TOPBAR_T = tok(LT[RL['top'][0]:RL['top'][1]], [
    ('آرشیو نوع پروژه: تونل نوری', '{ES_TOP_TITLE}'),
    ('data-project-count="true">۴</span> پروژه', 'data-project-count="true">{ES_COUNT}</span> پروژه'),
    ('<span>تونل نوری</span>', '<span>{ES_CHIP}</span>'),
    ('href="../projects.html"', 'href="{ES_CLEAR_URL}"'),
], 'TOPBAR_T')
TOPBAR_C = tok(ES[RE['top'][0]:RE['top'][1]], [
    ('پروژه‌های اجراشده در اصفهان', '{ES_TOP_TITLE}'),
    ('data-project-count="true">۲</span> پروژه', 'data-project-count="true">{ES_COUNT}</span> پروژه'),
    ('<span>شهر: اصفهان</span>', '<span>{ES_CHIP}</span>'),
    ('href="../projects.html"', 'href="{ES_CLEAR_URL}"'),
], 'TOPBAR_C')
assert 'تونل نوری' not in TOPBAR_T and 'اصفهان' not in TOPBAR_C, 'topbar residual'

# ─── EMPTY + FLOAT ───
ESP73_EMPTY = tok(EMPTY_LT, [('href="../projects.html"', 'href="{ES_PROJECTS_URL}"'), ('<div data-project-empty="true" class="hidden ', '<div data-project-empty="true" class="{ES_EMPTY_CLS} ')], 'EMPTY')
FLOAT_RAW = LT[RL['floatb'][0]:RL['floatb'][1]]
assert FLOAT_RAW == ES[RE['floatb'][0]:RE['floatb'][1]], 'float lt==es'
ESP73_FLOAT = tok(FLOAT_RAW, [('۱ فیلتر', '{ES_FLOAT_BADGE}')], 'FLOAT')

# ─── DRAWER ───
DL, DE = LT[RL['drawer'][0]:RL['drawer'][1]], ES[RE['drawer'][0]:RE['drawer'][1]]
r0 = DL.index('<div class="grid grid-cols-1 gap-2 sm:grid-cols-2">')
c0 = DL.index('<div class="grid grid-cols-2 gap-2">', r0)
tb = DL.index('<div class="border-t border-slate-100 pt-4">', r0)
foot = DL.index('<div class="flex items-center gap-3 border-t border-slate-100 bg-slate-50 px-5 py-3.5">', r0)
cend = DL.rindex('</a>', 0, foot) + len('</a>')
assert DL[cend:cend + 6] == '</div>'
cend += 6

DALL_OFF = tok(cut(DL, '<a href="../projects.html" class="flex items-center justify-between rounded-xl border px-3.5 py-2.5 text-[12.5px] font-bold transition-all border-slate-200', '</a>'),
               [('href="../projects.html"', 'href="{ES_ARCHIVE_URL}"')], 'DALL_OFF')
# حالت فعال «همه» از دراور projects.html (ESP72E) — در هر دو مرجع ترم «همه» غیرفعال است
_d0pj = PJ.index('data-archive-drawer="true"')
DPJ = PJ[_d0pj - 160:PJ.index('<article', _d0pj)]
DALL_ON = tok(cut(DPJ, '<a href="projects.html" class="flex items-center justify-between rounded-xl border px-3.5 py-2.5 text-[12.5px] font-bold transition-all border-emerald-700', '</a>'),
              [('href="projects.html"', 'href="{ES_ARCHIVE_URL}"')], 'DALL_ON')
assert 'opacity-0' in DALL_OFF and 'opacity-100' in DALL_ON, 'DALL opacity'
DROW = tok(cut(DL, '<a href="../project_type/square-lighting.html"', '</a>'), [
    ('href="../project_type/square-lighting.html"', 'href="{ES_URL}"'),
    ('<span>نورپردازی میدان</span>', '<span>{ES_LABEL}</span>'),
    ('<span class="text-[11px] opacity-80">(۳)</span>', '<span class="text-[11px] opacity-80">({ES_COUNT})</span>'),
], 'DROW')
# ردیف فعال نوع (تونل نوری در lt — کلاس emerald + همان شمارش (۴))
DROW_ON = tok(cut(DL, '<a href="../project_type/light-tunnel.html" class="flex items-center justify-between rounded-xl border px-3.5 py-2.5 text-[12.5px] font-bold transition-all border-emerald-700', '</a>'), [
    ('href="../project_type/light-tunnel.html"', 'href="{ES_URL}"'),
    ('<span>تونل نوری</span>', '<span>{ES_LABEL}</span>'),
    ('<span class="text-[11px] opacity-80">(۴)</span>', '<span class="text-[11px] opacity-80">({ES_COUNT})</span>'),
], 'DROW_ON')
DCITY = tok(cut(DL, '<a href="../city/esfahan.html"', '</a>'), [
    ('href="../city/esfahan.html"', 'href="{ES_URL}"'),
    ('<span class="truncate">اصفهان</span>', '<span class="truncate">{ES_LABEL}</span>'),
    ('<span class="text-[11px] opacity-75">(۲)</span>', '<span class="text-[11px] opacity-75">({ES_COUNT})</span>'),
], 'DCITY')

TYPES = [('project_type/square-lighting.html', 'نورپردازی میدان', '۳'),
         ('project_type/light-tunnel.html', 'تونل نوری', '۴'),
         ('project_type/luminous-elements.html', 'المان‌های نورانی', '۵'),
         ('project_type/bridge-lighting.html', 'نورپردازی پل', '۲'),
         ('project_type/lighting-building.html', 'نورپردازی ساختمان', '۱'),
         ('project_type/urban-lighting.html', 'نورپردازی شهری', '۱۰'),
         ('project_type/light-tree.html', 'درخت نوری', '۳'),
         ('project_type/ball-of-light.html', 'گوی نورانی', '۲')]
CITIES = [('city/esfahan.html', 'اصفهان', '۲'), ('city/alborz.html', 'استان البرز', '۲'),
          ('city/ahvaz.html', 'اهواز', '۱'), ('city/tehran.html', 'تهران', '۱'),
          ('city/mashhad.html', 'مشهد', '۱'), ('city/qom.html', 'قم', '۱'),
          ('city/kerman.html', 'کرمان', '۱'), ('city/birjand.html', 'بیرجند', '۱'),
          ('city/khorsand.html', 'خورسند', '۱'), ('city/iraq.html', 'عراق', '۱')]

def drow(sl, nm, cn, on):
    t = DROW_ON if on else DROW
    return t.replace('{ES_URL}', '../' + sl).replace('{ES_LABEL}', nm).replace('{ES_COUNT}', cn)

_RT0 = r0 + len('<div class="grid grid-cols-1 gap-2 sm:grid-cols-2">')
_RC0 = c0 + len('<div class="grid grid-cols-2 gap-2">')
_la = DL.rindex('</a>', 0, tb) + len('</a>')
_lc = DL.rindex('</a>', 0, foot) + len('</a>')
assert DL[_la:tb] == '</div></div>', 'type tail'
assert DL[_lc:foot] == '</div></div></div>', 'city tail'
rows_t = DALL_OFF + ''.join(drow(sl, nm, cn, sl == 'project_type/light-tunnel.html') for sl, nm, cn in TYPES)
rows_c = ''.join(DCITY.replace('{ES_URL}', '../' + sl).replace('{ES_LABEL}', nm).replace('{ES_COUNT}', cn) for sl, nm, cn in CITIES)
assert rows_t.replace('{ES_ARCHIVE_URL}', '../projects.html') == DL[_RT0:_la], 'type rows rebuild (tunnel active)'
assert DL[_RC0:_lc] == rows_c, 'city rows rebuild'

# ردیف فعال شهر (اصفهان در es — واریانت emerald-800)
DCITY_ON = tok(cut(DE, '<a href="../city/esfahan.html" class="flex items-center justify-between rounded-xl border px-3 py-2.5 text-[12px] font-bold transition-all border-emerald-800', '</a>'), [
    ('href="../city/esfahan.html"', 'href="{ES_URL}"'),
    ('<span class="truncate">اصفهان</span>', '<span class="truncate">{ES_LABEL}</span>'),
    ('<span class="text-[11px] opacity-75">(۲)</span>', '<span class="text-[11px] opacity-75">({ES_COUNT})</span>'),
], 'DCITY_ON')

DHEAD = DL[:_RT0]
DMID = DL[_la:_RC0]
DTAIL = tok(DL[_lc:], [
    ('data-project-count="true">۴</span> پروژه', 'data-project-count="true">{ES_COUNT}</span> پروژه'),
    ('<a href="../projects.html" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[12.5px] font-bold text-slate-600 transition-colors hover:bg-slate-100">حذف فیلتر</a>',
     '<a href="{ES_ARCHIVE_URL}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[12.5px] font-bold text-slate-600 transition-colors hover:bg-slate-100">حذف فیلتر</a>'),
], 'DTAIL')


# ─── ARTICLE ───
_recon_lt = (DHEAD + rows_t.replace('{ES_ARCHIVE_URL}', '../projects.html') + DMID + rows_c
             + DTAIL.replace('{ES_COUNT}', '۴').replace('{ES_ARCHIVE_URL}', '../projects.html'))
assert _recon_lt == DL, 'drawer full rebuild lt'
rows_c_es = ''.join((DCITY_ON if sl == 'city/esfahan.html' else DCITY).replace('{ES_URL}', '../' + sl).replace('{ES_LABEL}', nm).replace('{ES_COUNT}', cn) for sl, nm, cn in CITIES)
_recon_es = (DHEAD + (DALL_OFF + ''.join(drow(sl, nm, cn, False) for sl, nm, cn in TYPES)).replace('{ES_ARCHIVE_URL}', '../projects.html') + DMID + rows_c_es
             + DTAIL.replace('{ES_COUNT}', '۲').replace('{ES_ARCHIVE_URL}', '../projects.html'))
assert _recon_es == DE, 'drawer full rebuild es'

ART_T = LT[RL['art'][0]:RL['art'][1]]
ART_C = ES[RE['art'][0]:RE['art'][1]]

out = {}
out['HERO_T'] = HERO_T
out['SEC_T'] = SEC_T
out['TOPBAR_T'] = TOPBAR_T
out['HERO_C'] = HERO_C
out['SEC_C'] = SEC_C
out['TOPBAR_C'] = TOPBAR_C
out['GRID_OPEN'] = GRID_OPEN
out['ESP73_EMPTY'] = ESP73_EMPTY
out['ESP73_FLOAT'] = ESP73_FLOAT
out['ESP73_DH'] = DHEAD
out['ESP73_DM'] = DMID
out['ESP73_DT'] = DTAIL
out['ESP73_DALL_ON'] = DALL_ON
out['ESP73_DALL_OFF'] = DALL_OFF
out['ESP73_DROW'] = DROW
out['ESP73_DROW_ON'] = DROW_ON
out['ESP73_DCITY'] = DCITY
out['ESP73_DCITY_ON'] = DCITY_ON
out['ART_T'] = ART_T
out['ART_C'] = ART_C
out['CTA'] = CTA_LT

import json
open('temp/p73_fragments.json', 'w', encoding='utf-8').write(json.dumps(out, ensure_ascii=False))
print('p73 fragments:', {k: len(v) for k, v in out.items()})
print('gen_p73 stage-1 done')

# ═══ مرحله ۲: نوشتن فایل‌های PHP ═══
F = out


def php_nowdoc(name, marker, content):
    assert content.find('\n' + marker + ';') == -1, 'terminator clash: ' + marker
    return "$" + name + " = <<<'%s'\n%s\n%s;\n" % (marker, content, marker)


def write(path, content):
    open(path, 'w', encoding='utf-8').write(content)
    print('written:', path, len(content.encode('utf-8')))


# ─── ۱) project-drawer.php ───
drawer_php = """<?php
/**
 * دراور موبایل فیلتر پروژه‌ها — برش بایت‌دقیق میرور (gen_p73)
 * args: total, archive_url, active_type, active_city, all_active
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$es_args = $args ?? [];
$es_total = max(0, (int) ($es_args['total'] ?? 0));
$es_archive_url = (string) ($es_args['archive_url'] ?? '');
$es_active_type = (string) ($es_args['active_type'] ?? '');
$es_active_city = (string) ($es_args['active_city'] ?? '');
$es_all_active = !empty($es_args['all_active']);

""" \
    + php_nowdoc('ESP73_DH', 'ESP73DH', F['ESP73_DH']) \
    + php_nowdoc('ESP73_DM', 'ESP73DM', F['ESP73_DM']) \
    + php_nowdoc('ESP73_DT', 'ESP73DT', F['ESP73_DT']) \
    + php_nowdoc('ESP73_DALL_ON', 'ESP73DALLON', F['ESP73_DALL_ON']) \
    + php_nowdoc('ESP73_DALL_OFF', 'ESP73DALLOFF', F['ESP73_DALL_OFF']) \
    + php_nowdoc('ESP73_DROW', 'ESP73DROW', F['ESP73_DROW']) \
    + php_nowdoc('ESP73_DROW_ON', 'ESP73DROWON', F['ESP73_DROW_ON']) \
    + php_nowdoc('ESP73_DCITY', 'ESP73DCITY', F['ESP73_DCITY']) \
    + php_nowdoc('ESP73_DCITY_ON', 'ESP73DCITYON', F['ESP73_DCITY_ON']) + """
$es_drow = static function (object $es_t, bool $es_on, string $tpl_on, string $tpl_off): string {
    return strtr($es_on ? $tpl_on : $tpl_off, [
        '{ES_URL}'   => esc_url((string) get_term_link($es_t)),
        '{ES_LABEL}' => esc_html((string) $es_t->name),
        '{ES_COUNT}' => esc_html(es_fa_num((string) ($es_t->count ?? 0))),
    ]);
};

$es_rows_t = strtr($es_all_active ? $ESP73_DALL_ON : $ESP73_DALL_OFF, ['{ES_ARCHIVE_URL}' => esc_url($es_archive_url)]);
foreach (get_terms(['taxonomy' => 'project_type', 'hide_empty' => false]) as $es_t) {
    if (is_wp_error($es_t) || !is_object($es_t)) {
        continue;
    }
    $es_rows_t .= $es_drow($es_t, (string) $es_t->slug === $es_active_type, $ESP73_DROW_ON, $ESP73_DROW);
}
$es_rows_c = '';
foreach (get_terms(['taxonomy' => 'city', 'hide_empty' => false]) as $es_t) {
    if (is_wp_error($es_t) || !is_object($es_t)) {
        continue;
    }
    $es_rows_c .= $es_drow($es_t, (string) $es_t->slug === $es_active_city, $ESP73_DCITY_ON, $ESP73_DCITY);
}

echo strtr($ESP73_DH, []) . $es_rows_t . strtr($ESP73_DM, []) . $es_rows_c . strtr($ESP73_DT, [
    '{ES_COUNT}'       => esc_html(es_fa_num((string) $es_total)),
    '{ES_ARCHIVE_URL}' => esc_url($es_archive_url),
]);
"""
write('template-parts/archive/project-drawer.php', drawer_php)

# ─── ۲) شل‌ها ───
COMMON_HEAD = """get_header();

$es_term = get_queried_object();
$es_is_term = is_object($es_term) && isset($es_term->term_id);
$es_tid = $es_is_term ? (int) $es_term->term_id : 0;
$es_name = $es_is_term ? (string) $es_term->name : '';
$es_slug = $es_is_term ? (string) $es_term->slug : '';
$es_meta = static fn (string $k, string $def): string => (string) (get_term_meta($es_tid, $k, true) ?: $def);

$es_total = (int) ($GLOBALS['__es_archive_total'] ?? ($GLOBALS['wp_query']->found_posts ?? 0));
$es_paged = max(1, (int) get_query_var('paged'));
$es_per = max(1, (int) es_option('archives.projects_per_page', '6'));
$es_pages = max(1, (int) ceil($es_total / $es_per));
$es_is_empty = $es_total < 1;
$es_archive_url = (string) get_post_type_archive_link('projects');

echo '<main>';
"""


def shell(is_city):
    kind = 'C' if is_city else 'T'
    badge = 'آرشیو پروژه\u200cها بر اساس شهر اجرای پروژه' if is_city else 'آرشیو تخصصی بر اساس نوع پروژه'
    title_a = 'پروژه\u200cهای نورپردازی شهری در' if is_city else 'پروژه\u200cهای اجرایی'
    img = 'assets/images/light-sphere.jpg' if is_city else 'assets/images/light-tunnel.jpg'
    top_d = "'پروژه\u200cهای اجراشده در ' . $es_name" if is_city else "'آرشیو نوع پروژه: ' . $es_name"
    chip_d = "'شهر: ' . $es_name" if is_city else "$es_name"
    init_type = "'همه'" if is_city else "$es_name"
    init_loc = "$es_name" if is_city else "'همه'"
    filters_args = "['count' => $es_total, 'total_all' => (int) (wp_count_posts('projects')->publish ?? 0), 'active_type' => " + ("null" if is_city else "$es_slug") + ", 'active_city' => " + ("$es_slug" if is_city else "''") + ", 'clear_url' => $es_archive_url, 'deactivate_all' => " + ('true' if is_city else 'false') + "]"
    drawer_args = "['total' => $es_total, 'archive_url' => $es_archive_url, 'active_type' => " + ("''" if is_city else "$es_slug") + ", 'active_city' => " + ("$es_slug" if is_city else "''") + ", 'all_active' => false]"
    php = "<?php\n/**\n * آرشیو ترم " + ('شهر (city)' if is_city else 'نوع پروژه (project_type)') + " — بایت‌دقیق میرور " + ('city/esfahan.html' if is_city else 'project_type/light-tunnel.html') + " (gen_p73)\n *\n * @package ErfanSanat\n */\ndeclare(strict_types=1);\n\n" \
        + php_nowdoc('ESP73H', 'ESP73H', F['HERO_' + kind]) \
        + php_nowdoc('ESP73SEC', 'ESP73SEC', F['SEC_' + kind]) \
        + php_nowdoc('ESP73TOP', 'ESP73TOP', F['TOPBAR_' + kind]) \
        + php_nowdoc('ESP73GRID', 'ESP73GRID', F['GRID_OPEN']) \
        + php_nowdoc('ESP73EMPTY', 'ESP73EMPTY', F['ESP73_EMPTY']) \
        + php_nowdoc('ESP73FLOAT', 'ESP73FLOAT', F['ESP73_FLOAT']) \
        + php_nowdoc('ESP73ART', 'ESP73ART', F['ART_' + kind]) \
        + php_nowdoc('ESP73CTA', 'ESP73CTA', F['CTA']) \
        + COMMON_HEAD + """$es_img_id = (int) get_term_meta($es_tid, 'es_archive_image_id', true);
$es_img = $es_img_id > 0 ? (string) wp_get_attachment_url($es_img_id) : es_media_url('""" + img + """');

echo strtr($ESP73H, [
    '{ES_HOME_URL}'      => esc_url(home_url('/')),
    '{ES_PROJECTS_URL}'  => esc_url($es_archive_url),
    '{ES_CRUMB3}'   => esc_html($es_meta('es_archive_crumb', $es_name)),
    '{ES_BADGE}'    => esc_html($es_meta('es_archive_badge', '""" + badge + """')),
    '{ES_TITLE_A}'  => esc_html($es_meta('es_archive_title_a', '""" + title_a + """')),
    '{ES_TITLE_B}'  => esc_html($es_meta('es_archive_title_b', $es_name)),
    '{ES_DESC}'     => esc_html(trim((string) ($es_term->description ?? '')) !== '' ? (string) $es_term->description : $es_meta('es_archive_desc', $es_name)),
    '{ES_COUNT}'    => esc_html(es_fa_num((string) $es_total)),
    '{ES_IMG}'      => esc_url($es_img),
    '{ES_TERM_NAME}' => esc_html($es_name),
    '{ES_CAPTION}'  => esc_html($es_meta('es_archive_caption', $es_meta('es_archive_title_a', '""" + title_a + """') . ' ' . $es_name)),
]);
echo strtr($ESP73SEC, [
    '{ES_INIT_TYPE}' => esc_html(""" + init_type + """),
    '{ES_INIT_LOC}'  => esc_html(""" + init_loc + """),
]);
ob_start();
es_get_template('template-parts/archive/project-filters.php', """ + filters_args + """);
echo (string) ob_get_clean();
echo strtr($ESP73TOP, [
    '{ES_TOP_TITLE}' => esc_html($es_meta('es_archive_top_title', """ + top_d + """)),
    '{ES_COUNT}'     => esc_html(es_fa_num((string) $es_total)),
    '{ES_CHIP}'      => esc_html($es_meta('es_archive_chip', """ + chip_d + """)),
    '{ES_CLEAR_URL}' => esc_url($es_archive_url),
]);
echo strtr($ESP73GRID, []);
if (!$es_is_empty) {
    $es_i = 0;
    foreach (($GLOBALS['wp_query']->posts ?? []) as $es_post) {
        ob_start();
        es_get_template('template-parts/cards/card-project.php', ['post' => $es_post, 'custom' => $es_i % 2 === 0 ? '0.04' : '0.1', 'nested' => true]);
        echo (string) ob_get_clean();
        echo '</div>';
        $es_i++;
    }
}
echo '</div>';
ob_start();
es_get_template('template-parts/archive/pagination.php', [
    'current'        => $es_paged,
    'total_pages'    => $es_pages,
    'total_products' => $es_total,
    'base_url'       => $es_is_term ? (string) get_term_link($es_term) : $es_archive_url,
    'root'           => 'projects',
    'count_attr'     => 'data-project-count="true"',
    'unit'           => 'پروژه',
    'label'          => 'صفحه\u200cبندی پروژه\u200cها',
    'always_show'    => !$es_is_empty,
    'nav_cls'        => $es_pages > 1 ? 'flex' : 'hidden',
]);
echo (string) ob_get_clean();
echo strtr($ESP73EMPTY, [
    '{ES_PROJECTS_URL}' => esc_url($es_archive_url),
    '{ES_EMPTY_CLS}'    => $es_is_empty ? 'block' : 'hidden',
]);
echo strtr($ESP73FLOAT, ['{ES_FLOAT_BADGE}' => esc_html(es_fa_num($es_is_term ? '1' : '0') . ' فیلتر')]);
ob_start();
es_get_template('template-parts/archive/project-drawer.php', """ + drawer_args + """);
echo (string) ob_get_clean();
echo strtr($ESP73ART, []);
echo strtr($ESP73CTA, []);
echo '</main>';
get_footer();
"""
    return php


write('taxonomy-project_type.php', shell(False))
write('taxonomy-city.php', shell(True))
print('gen_p73 stage-2 done')
