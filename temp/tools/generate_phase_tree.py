#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Generate the atomic Stage-1 plan: 8 phases, 175 tasks, specs, test skeletons,
runners, master plan, index and legacy mapping. Planning only — no theme code."""
from __future__ import annotations
import importlib, os, re, stat, sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
TEMP = ROOT / 'temp'
sys.path.insert(0, str(ROOT / 'temp' / 'tools'))
from planning.common import PHASE_META, COMMON_DOD  # noqa: E402

PHASES = {}
for n in range(1, 9):
    mod = importlib.import_module(f'planning.phase_{n:02d}')
    folder, title = PHASE_META[n]
    tasks = []
    for i, t in enumerate(mod.TASKS, 1):
        t = dict(t)
        t['id'] = f'P{n:02d}-T{i:03d}'
        t['nn'] = f'{i:03d}'
        t['phase'] = n
        t['folder'] = folder
        t['phase_title'] = title
        t['deps_full'] = []
        for d in t['deps']:
            if d.startswith('P'):
                t['deps_full'].append(d)
            else:
                t['deps_full'].append(f'P{n:02d}-{d}')
        tasks.append(t)
    PHASES[n] = tasks

ALL = [t for n in PHASES for t in PHASES[n]]
BY_ID = {t['id']: t for t in ALL}
assert len(BY_ID) == len(ALL), 'duplicate task id'
for t in ALL:
    for d in t['deps_full']:
        assert d in BY_ID, f"{t['id']} depends on missing {d}"
        assert BY_ID[d]['phase'] < t['phase'] or (BY_ID[d]['phase'] == t['phase'] and BY_ID[d]['nn'] < t['nn']), f"forward dependency {t['id']} -> {d}"
    for s in t['sources']:
        p = ROOT / s
        assert p.exists(), f"{t['id']} source missing: {s}"

def md_list(items):
    return '\n'.join(f'- {x}' for x in items) if items else '- ندارد.'

def task_md(t):
    deps = ', '.join(f'`{d}`' for d in t['deps_full']) or 'ندارد'
    keys = ', '.join(f'`{k}`' for k in t['keys']) or '—'
    accept = '\n'.join(f'- [ ] AC-{i:02d}: {x}' for i, x in enumerate(t['accept'], 1))
    tests = '\n'.join(f'- [ ] SC-{i:02d}: {x}' for i, x in enumerate(t['tests'], 1))
    dod = '\n'.join(f'- [ ] {x}' for x in COMMON_DOD)
    return f"""# {t['id']} — {t['title']}

> فاز {t['phase']}: {t['phase_title']} · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `{t['legacy']}` · وابستگی‌ها: {deps}

## هدف دقیق
{t['goal']}

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.
{('- یادداشت: ' + t['notes']) if t['notes'] else ''}

## فایل‌های هدف
{md_list('`' + x + '`' for x in t['targets'])}

## منابع/مرجع
{md_list('`' + x + '`' for x in t['sources'])}

## کلیدهای داده/قرارداد
{keys}

## شروط پذیرش اختصاصی
{accept}

## سناریوهای تست اختصاصی
{tests}

## Definition of Done مشترک
{dod}

## تست و اجرا
- اسکریپت: `temp/{t['folder']}/tests/test-task-{t['nn']}.php`
- اجرای تکی: `php temp/{t['folder']}/tests/test-task-{t['nn']}.php`
- تست فاز: `bash temp/{t['folder']}/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
"""

def php_str(s):
    return "'" + s.replace('\\', '\\\\').replace("'", "\\'") + "'"

def target_check(target):
    if target.startswith('erfansanat/'):
        rel = target[len('erfansanat/'):]
        base = 'ES_THEME'
    elif target.startswith('temp/'):
        rel = target[len('temp/'):]
        base = 'ES_ROOT'
    else:
        rel = target
        base = 'ES_ROOT'
    fn = 'es_dir' if rel.endswith('/') else 'es_file'
    rel = rel.rstrip('/')
    return f"es_check( 'target: {target}', {fn}({php_str(rel)}, {base}) );"

def test_php(t):
    targets = '\n'.join(target_check(x) for x in t['targets'])
    scenarios = '\n'.join(f"es_pending( {php_str('SC-' + f'{i:02d}: ' + x)} );" for i, x in enumerate(t['tests'], 1))
    return f"""<?php
/**
 * {t['id']} — {t['title']}
 * Stage-1 atomic test skeleton. Target checks are executable now; behavioral
 * scenarios are explicitly PENDING until the task's WRITE TEST step automates them.
 * Run: php temp/{t['folder']}/tests/test-task-{t['nn']}.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( {php_str(t['id'])}, {php_str(t['title'])} );

{targets}

{scenarios}

es_summary();
"""

RUN_SH = """#!/usr/bin/env bash
# Comprehensive phase test runner: {phase}
set -u
DIR="$(cd "$(dirname "${{BASH_SOURCE[0]}}")" && pwd)"
ROOT="$(cd "$DIR/../../.." && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
echo "================ {phase} ================"
if [ -f "$ROOT/erfansanat/style.css" ]; then
  wp theme activate erfansanat || exit 1
fi
fail=0
for t in "$DIR"/test-task-*.php; do
  echo "---- $(basename "$t") ----"
  php "$t" || fail=1
done
if [ "$fail" -eq 0 ]; then echo "PHASE RESULT: ALL PASS"; else echo "PHASE RESULT: FAILURES/PENDING"; fi
exit "$fail"
"""

def write(path, text, executable=False):
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(text, encoding='utf-8')
    if executable:
        path.chmod(path.stat().st_mode | stat.S_IEXEC | stat.S_IXGRP | stat.S_IXOTH)

# Clean superseded 2-digit plan files (all were PENDING; git history preserves them).
for folder, _ in PHASE_META.values():
    for sub, pat in [('tasks', r'task-\d{2}\.md'), ('tests', r'test-task-\d{2}\.php')]:
        d = TEMP / folder / sub
        if d.exists():
            for p in d.iterdir():
                if re.fullmatch(pat, p.name):
                    p.unlink()

legacy_map = {}
for n, tasks in PHASES.items():
    folder, title = PHASE_META[n]
    base = TEMP / folder
    for t in tasks:
        write(base / 'tasks' / f"task-{t['nn']}.md", task_md(t))
        write(base / 'tests' / f"test-task-{t['nn']}.php", test_php(t))
        legacy_map.setdefault(t['legacy'], []).append(t['id'])
    write(base / 'tests' / 'run-phase-tests.sh', RUN_SH.format(phase=title), True)
    rows = '\n'.join(f"| `{t['id']}` | {t['title']} | `{t['legacy']}` | {', '.join(t['deps_full']) or '—'} | `[PENDING]` |" for t in tasks)
    write(base / 'README.md', f"""# Phase {n} — {title}

تعداد تسک اتمیک: **{len(tasks)}** · وضعیت: `[PENDING]`

| شناسه | تسک | تسک قبلی | وابستگی | وضعیت |
|---|---|---|---|---|
{rows}

اجرای همه تست‌ها: `bash temp/{folder}/tests/run-phase-tests.sh`
""")

# Master plan
master = ["# MASTER_PLAN — نقشه راه اتمیک پروژه", "",
          "> بازطراحی Stage 1 طبق درخواست کاربر: تسک‌های درشت به تسک‌های اتمیک، ریزتر و جامع‌تر تبدیل شدند.",
          f"> تعداد کل: **{len(ALL)} تسک** در ۸ فاز · آخرین به‌روزرسانی: 2026-11-03", "",
          "## وضعیت مراحل", "| مرحله | وضعیت |", "|---|---|",
          "| Stage 0 — تجهیز هارنس و اسکن | `[PASSED]` |",
          "| Stage 1 — برنامه اتمیک ۱۷۵ تسکی | `[PASSED]` |",
          "| دروازه تایید کاربر برای شروع Phase 1 | ⏳ در انتظار |", ""]
for n, tasks in PHASES.items():
    folder, title = PHASE_META[n]
    master += [f"## Phase {n} — {title} (`temp/{folder}`)", "",
               f"تعداد تسک: **{len(tasks)}** · تست جامع: `tests/run-phase-tests.sh`", "",
               "| شناسه | تسک | قبلی | وابستگی | وضعیت |", "|---|---|---|---|---|"]
    for t in tasks:
        master.append(f"| `{t['id']}` | {t['title']} | `{t['legacy']}` | {', '.join(t['deps_full']) or '—'} | `[PENDING]` |")
    master.append("")
master += ["## پروتکل هر تسک", "`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`",
           "- بعد از هر تسک/فاز: گزارش تست و توقف برای تایید کاربر.", "- تست strict: هر FAIL یا PENDING مانع پاس شدن فاز است.", ""]
write(TEMP / 'MASTER_PLAN.md', '\n'.join(master))

# Atomic index
index = ["# ATOMIC_TASK_INDEX — فهرست تسک‌های اتمیک", "",
         f"مجموع: **{len(ALL)} تسک** · هر تسک یک خروجی مستقل، وابستگی مشخص و سناریوی تست دارد.", ""]
for n, tasks in PHASES.items():
    folder, title = PHASE_META[n]
    index.append(f"## Phase {n} — {title} ({len(tasks)} تسک)")
    for t in tasks:
        index.append(f"- `{t['id']}` [{t['title']}]({folder}/tasks/task-{t['nn']}.md) — قبلی `{t['legacy']}`")
    index.append("")
write(TEMP / 'ATOMIC_TASK_INDEX.md', '\n'.join(index))

# Legacy mapping
lm = ["# LEGACY_TASK_MAP — نگاشت ۲۲ تسک قبلی به تسک‌های اتمیک", "",
      "شماره‌های قبلی دیگر استفاده نمی‌شوند؛ شناسه پایدار جدید `Pxx-Tyyy` است.", "",
      "| تسک قبلی | تسک‌های اتمیک جدید |", "|---|---|"]
for old in sorted(legacy_map, key=lambda x: tuple(int(i) for i in x.split('.'))):
    lm.append(f"| `{old}` | {', '.join('`' + x + '`' for x in legacy_map[old])} |")
write(TEMP / 'LEGACY_TASK_MAP.md', '\n'.join(lm))

# E2E runner
e2e = """#!/usr/bin/env bash
# End-to-end suite: re-run every phase's strict tests.
set -u
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
export PATH="$ROOT/temp/tools:$PATH"
overall=0
for d in "$ROOT"/temp/phase-*/tests/run-phase-tests.sh; do
  bash "$d" || overall=1
done
if [ "$overall" -eq 0 ]; then echo "E2E RESULT: ALL PHASES PASS"; else echo "E2E RESULT: FAILURES/PENDING"; fi
exit "$overall"
"""
write(TEMP / 'run-all-phases-e2e.sh', e2e, True)

print(f"Generated {len(PHASES)} phases, {len(ALL)} atomic tasks, {len(ALL)} test skeletons, 8 phase runners, 1 e2e runner.")
print('Per-phase counts:', {n: len(PHASES[n]) for n in PHASES})
