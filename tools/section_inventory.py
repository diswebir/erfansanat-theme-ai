#!/usr/bin/env python3
"""Section-level inventory of every static page: id, aria, headings, data-hooks, byte span."""
import re, os, json, sys
from collections import Counter

ROOT = sys.argv[1] if len(sys.argv) > 1 else 'reference/static-mirror'
OUT  = sys.argv[2] if len(sys.argv) > 2 else 'temp/_scan'

TAG = re.compile(r'<(section|footer|header|main)\b', re.I)

def pairs(h):
    """Return balanced element spans for section/footer/header/main."""
    stack, spans = [], []
    for m in re.finditer(r'<(/?)(section|header|footer|main)\b([^>]*)>', h, re.I):
        closing, name, attrs = m.group(1), m.group(2).lower(), m.group(3)
        if not closing and not attrs.rstrip().endswith('/'):
            stack.append((name, m.start(), attrs))
        elif closing and stack and stack[-1][0] == name:
            nm, st, at = stack.pop()
            spans.append((nm, st, m.end(), at))
    return sorted(spans, key=lambda s: s[1])

def inventory(path):
    h = open(path, encoding='utf-8').read()
    out = []
    for name, start, end, attrs in pairs(h):
        if name not in ('section', 'footer', 'header'):
            continue
        body = h[start:end]
        sid  = re.search(r'\bid="([^"]*)"', attrs)
        aria = re.search(r'aria-label="([^"]*)"', attrs)
        cls  = re.search(r'class="([^"]*)"', attrs)
        heads = [(int(l), re.sub(r'<[^>]+>', '', t).strip())
                 for l, t in re.findall(r'<h([1-6])[^>]*>(.*?)</h\1>', body, re.S)]
        hooks = Counter(re.findall(r'(data-[a-z0-9-]+)', body))
        out.append({
            'tag': name,
            'id': sid.group(1) if sid else None,
            'aria': aria.group(1) if aria else None,
            'classes': cls.group(1) if cls else '',
            'bytes': end - start,
            'headings': heads[:8],
            'tabs': len(re.findall(r'role="tab"', body)),
            'forms': len(re.findall(r'<form\b', body)),
            'links': len(re.findall(r'<a\b', body)),
            'imgs': len(re.findall(r'<img\b', body)),
            'svg': len(re.findall(r'<svg\b', body)),
            'hooks': [k for k, _ in hooks.most_common(14)],
        })
    return out

files = sorted(os.path.relpath(os.path.join(r, f), ROOT).replace('\\', '/')
               for r, d, fs in os.walk(ROOT) for f in fs if f.endswith('.html'))
data = {f: inventory(os.path.join(ROOT, f)) for f in files}
os.makedirs(OUT, exist_ok=True)
json.dump(data, open(os.path.join(OUT, 'sections.json'), 'w', encoding='utf-8'), ensure_ascii=False, indent=1)

for f in ['index.html', 'products.html', 'product/delsa-light-tree.html',
          'projects/esfahan-shohada-square.html', 'blog.html', 'blog/types-of-light-tunnel.html',
          'about.html', 'contact.html', 'services.html', 'services/square-lighting.html']:
    print(f"\n########## {f}")
    for s in data[f]:
        ttl = ' / '.join(x[1][:42] for x in s['headings'] if x[0] <= 2)[:110]
        print(f"[{s['tag']:7}] id={str(s['id'])[:18]:18} b={s['bytes']:6} tabs={s['tabs']} frm={s['forms']} lnk={s['links']:3} img={s['imgs']:2} | {ttl}")
        print(f"          hooks: {', '.join(s['hooks'][:9])}")
