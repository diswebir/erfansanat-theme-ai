#!/usr/bin/env python3
"""Static mirror analyzer: sections, variants, data-* hooks, assets per page."""
import re, os, json, hashlib, sys

ROOT = sys.argv[1] if len(sys.argv) > 1 else 'reference/static-mirror'
OUT  = sys.argv[2] if len(sys.argv) > 2 else 'temp/_scan'

def rel(p): return os.path.relpath(p, ROOT).replace('\\','/')

files = sorted(rel(os.path.join(r,f)) for r,d,fs in os.walk(ROOT) for f in fs if f.endswith('.html'))
report = {}

def grab(h, tag):
    i = h.find('<'+tag)
    if i < 0: return ''
    # naive nesting-safe scan for the tag
    depth, j = 0, i
    pat = re.compile(r'<(/?)'+tag+r'\b', re.I)
    for m in pat.finditer(h, i):
        depth += -1 if m.group(1) else 1
        if depth == 0:
            j = h.find('>', m.end())+1
            return h[i:j]
    return h[i:]

for f in files:
    p = os.path.join(ROOT, f)
    h = open(p, encoding='utf-8').read()
    title = re.search(r'<title>(.*?)</title>', h, re.S)
    desc  = re.search(r'<meta name="description" content="(.*?)"', h)
    secs = []
    for m in re.finditer(r'<section\b([^>]*)>', h):
        attrs = m.group(1)
        sid  = re.search(r'id="([^"]*)"', attrs)
        aria = re.search(r'aria-label="([^"]*)"', attrs)
        cls  = re.search(r'class="([^"]*)"', attrs)
        secs.append({
            'id': sid.group(1) if sid else None,
            'aria': aria.group(1) if aria else None,
            'class': (cls.group(1)[:120] if cls else None),
        })
    data_attrs = sorted(set(re.findall(r'(data-[a-z0-9-]+)=', h)))
    header = grab(h,'header'); footer = grab(h,'footer')
    report[f] = {
        'title': title.group(1).strip() if title else None,
        'description': desc.group(1).strip() if desc else None,
        'bytes': len(h),
        'sections': secs,
        'n_sections': len(secs),
        'data_attrs': data_attrs,
        'header_hash': hashlib.md5(header.encode()).hexdigest()[:8],
        'header_len': len(header),
        'footer_hash': hashlib.md5(footer.encode()).hexdigest()[:8],
        'footer_len': len(footer),
        'inline_svg': len(re.findall(r'<svg\b', h)),
        'forms': re.findall(r'<form\b[^>]*>', h),
        'h1': [re.sub(r'<[^>]+>','',x).strip()[:120] for x in re.findall(r'<h1[^>]*>(.*?)</h1>', h, re.S)],
        'imgs': sorted(set(re.findall(r'(?:src|href)="(images/[^"]+)"', h))),
        'has_video': '<video' in h,
        'accordion': len(re.findall(r'data-faq|accordion', h)),
        'tabs': len(re.findall(r'role="tab"', h)),
        'jsonld': re.findall(r'"@type":"([A-Za-z]+)"', h),
    }

os.makedirs(OUT, exist_ok=True)
json.dump(report, open(os.path.join(OUT,'pages.json'),'w',encoding='utf-8'), ensure_ascii=False, indent=1)

# ---------- console summary ----------
print(f"{'file':52} {'sec':>3} {'hdr':>8} {'svg':>4} {'tabs':>4} {'frm':>3}  h1 / title")
for f,v in report.items():
    print(f"{f:52} {v['n_sections']:3} {v['header_hash']:>8} {v['inline_svg']:4} {v['tabs']:4} {len(v['forms']):3}  {(v['h1'][0] if v['h1'] else v['title'] or '')[:60]}")
print("\nTOTAL FILES:", len(report))
print("SECTIONS TOTAL:", sum(v['n_sections'] for v in report.values()))
