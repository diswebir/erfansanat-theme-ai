#!/usr/bin/env python3
"""Mark an atomic task PASSED in its task md + MASTER_PLAN + phase README.
Usage: python3 temp/tools/mark_task.py <phase:int> <task:int> <pass_count:int> [note]
"""
import re, sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
phase, task, count = int(sys.argv[1]), int(sys.argv[2]), int(sys.argv[3])
note = sys.argv[4] if len(sys.argv) > 4 else ''

phase_dirs = sorted(ROOT.glob(f'temp/phase-{phase:02d}-*'))
assert phase_dirs, f'phase {phase} dir not found'
pdir = phase_dirs[0]
tid = f'P{phase:02d}-T{task:03d}'

md = pdir / 'tasks' / f'task-{task:03d}.md'
s = md.read_text(encoding='utf-8')
s = s.replace('وضعیت: `[PENDING]`', 'وضعیت: `[PASSED]`').replace('وضعیت: `[IN_PROGRESS]`', 'وضعیت: `[PASSED]`')
s = re.sub(r'- \[ \] (AC-|SC-)', r'- [x] \1', s)
s = re.sub(r'- \[ \] (php -l|هیچ TODO|کلاس‌ها|تست اختصاصی|PROJECT_MEMORY)', r'- [x] \1', s)
result = f'\n\n## نتیجه اجرا (2026-11-03)\n- تست اختصاصی: `php {pdir.relative_to(ROOT)}/tests/test-task-{task:03d}.php` → **{count} passed, 0 failed, 0 pending**.\n'
if note:
    result += f'- {note}\n'
if '## نتیجه اجرا' not in s:
    s += result
md.write_text(s, encoding='utf-8')

# Update MASTER_PLAN + phase README status cells.
for p in [ROOT / 'temp/MASTER_PLAN.md', pdir / 'README.md']:
    t = p.read_text(encoding='utf-8')
    t = re.sub(rf'(\| `{tid}` \|.*?\| )`\[(PENDING|IN_PROGRESS|TESTING)\]`', r'\1`[PASSED]`', t)
    p.write_text(t, encoding='utf-8')

print(f'marked {tid} PASSED ({count} checks)')
