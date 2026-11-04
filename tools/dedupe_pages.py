#!/usr/bin/env python3
"""Detect duplicate static pages (same rendered body) and group by layout family."""
import re, os, json, hashlib, sys
from collections import defaultdict, OrderedDict

ROOT = sys.argv[1] if len(sys.argv)>1 else 'reference/static-mirror'
OUT  = sys.argv[2] if len(sys.argv)>2 else 'temp/_scan'
os.makedirs(OUT, exist_ok=True)

def body(p):
    h = open(p, encoding='utf-8').read()
    i = h.find('<body'); j = h.find('</body>')
    return h[i:j]
def norm(s):
    # strip absolute canonical/meta noise so duplicates collapse
    s = re.sub(r'<link rel="canonical"[^>]*>', '', s)
    s = re.sub(r'https?://erfansanat\.com[^"\' ]*', 'URL', s)
    s = re.sub(r'\s+', ' ', s)
    return s

files = sorted(os.path.relpath(os.path.join(r,f), ROOT).replace('\\','/')
               for r,d,fs in os.walk(ROOT) for f in fs if f.endswith('.html'))
byhash = defaultdict(list)
for f in files:
    h = hashlib.md5(norm(body(os.path.join(ROOT,f))).encode()).hexdigest()[:10]
    byhash[h].append(f)

groups = [v for v in byhash.values() if len(v) > 1]
print(f"total files={len(files)}  unique bodies={len(byhash)}  duplicate groups={len(groups)}")
for g in sorted(groups, key=lambda x:-len(x)):
    print("  DUP:", " | ".join(g))

# layout family classification via the static scaffold that encloses <main>
def layout(f):
    h = open(os.path.join(ROOT,f), encoding='utf-8').read()
    m = re.search(r'<main[^>]*>', h)
    return m.group(0)[:200] if m else '(no <main>)'
fams = defaultdict(list)
for f in files:
    fams[layout(f)].append(f)
print(f"\nlayout families (by <main> opening tag): {len(fams)}")
for k,v in sorted(fams.items(), key=lambda kv:-len(kv[1])):
    print(f"  n={len(v):3}  {k}")
    print(f"        e.g. {v[:4]}")

json.dump({'dups':groups,'families':{k:v for k,v in fams.items()}},
          open(os.path.join(OUT,'dedupe.json'),'w',encoding='utf-8'), ensure_ascii=False, indent=1)
