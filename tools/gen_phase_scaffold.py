#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Generate temp/phase-XX-*/{tasks,tests} + temp/MASTER_PLAN.md from tools/plan/phaseNN.py.

Each plan module exports PHASE = {num, slug, title, goal, tasks:[...]} where a task is:
  {id, size(S/M/L), deps:[task ids], title, desc, files:[...], steps:[...], pass_:[...], asserts:[...]}
"""
import importlib
import os
import shutil
import sys

ROOT = 'temp'
sys.path.insert(0, 'tools/plan')

SIZE_LABEL = {'S': 'کوچک (~نیم روز)', 'M': 'متوسط (~۱ روز)', 'L': 'بزرگ (~۲ روز)'}


def esc(s: str) -> str:
    return s.replace('\\', '\\\\').replace('|', '\\|').replace('`', '`')


def php_str(s: str) -> str:
    """Single-quoted PHP literal, UTF-8 preserved, safe for backslash/apostrophe."""
    out = s.replace('\\', '\\\\').replace("'", "\\'")
    return "'" + out + "'"


# Tasks already implemented and verified (id -> status). Regeneration preserves
# the real state so the plan never regresses to PENDING.
DONE_STATUS = {
    "1.1": "[PASSED]", "1.2": "[PASSED]", "1.3": "[PASSED]", "1.4": "[PASSED]",
    "1.5": "[PASSED]", "1.6": "[PASSED]", "1.7": "[PASSED]", "1.8": "[PASSED]",
    "1.9": "[PASSED]", "1.10": "[PASSED]", "1.11": "[PASSED]", "1.12": "[PASSED]",
    "2.1": "[PASSED]", "2.2": "[PASSED]", "2.3": "[PASSED]", "2.4": "[PASSED]", "2.5": "[PASSED]", "2.6": "[PASSED]", "2.7": "[PASSED]", "2.8": "[PASSED]", "2.9": "[PASSED]", "2.10": "[PASSED]", "2.11": "[PASSED]", "2.12": "[PASSED]", "2.13": "[PASSED]", "2.14": "[PASSED]", "2.15": "[PASSED]",
    "3.1": "[PASSED]", "3.2": "[PASSED]", "3.3": "[PASSED]",
}


def status_of(t):
    return DONE_STATUS.get(t['id'], '[PENDING]')


def task_md(ph, t):
    L = []
    A = L.append
    A(f"# Task {t['id']} — {t['title']}\n")
    A(f"| فیلد | مقدار |")
    A("|---|---|")
    A(f"| فاز | {ph['num']} — {ph['title']} |")
    A(f"| اندازه | `{t['size']}` — {SIZE_LABEL[t['size']]} |")
    A(f"| وابستگی | {'—' if not t['deps'] else '، '.join(f'`Task {d}`' for d in t['deps'])} |")
    A(f"| وضعیت | `{status_of(t)}` |")
    A(f"| تست | `tests/test-task-{t['id'].split('.')[1].zfill(2)}.php` |\n")
    A("## ۱. هدف\n")
    A(t['desc'] + "\n")
    A("## ۲. فایل‌های هدف\n")
    for f in t['files']:
        A(f"- `{f}`")
    A("\n## ۳. مراحل پیاده‌سازی (گام‌به‌گام)\n")
    for i, s in enumerate(t['steps'], 1):
        A(f"{i}. {s}")
    A("\n## ۴. شروط پاس شدن (Pass Conditions)\n")
    A("> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.\n")
    done = status_of(t) in ('[PASSED]', '[TESTING]')
    for i, p in enumerate(t['pass_'], 1):
        A(f"- [{'x' if done else ' '}] **P{i}** — {p}")
    A("\n## ۵. تست‌های خودکار\n")
    for a in t['asserts']:
        A(f"- `{a}`")
    A("\n## ۶. چرخه اجرایی (۶ گام اجباری)\n")
    A("| گام | اقدام |")
    A("|---|---|")
    A("| `[READ MEMORY]` | `temp/PROJECT_MEMORY.md` + `temp/STATIC_TEMPLATE_MAP.md` + این فایل |")
    A("| `[THINK & PLAN]` | تحلیل مارک‌آپ/داده مربوطه و طرح اتصال |")
    A("| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` (تبدیل `pending()` به `check()`) |")
    A("| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM/هوک |")
    A("| `[RUN TEST]` | اجرا تا ۱۰۰٪ PASS + ثبت در `QA_REFLECTION_LOG.md` |")
    A("| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md` + کامیت |")
    A("\n## ۷. ضدالگوها (Anti-patterns) — انجام نده\n")
    A("- طراحی مجدد مارک‌آپ یا افزودن کلاس Tailwind جدید (قانون R1)")
    A("- تغییر فایل `assets/js/main.js` یا `assets/css/styles.css` (md5 باید ثابت بماند)")
    A("- استفاده از `TODO`/`...`/حذف بخشی از HTML برای کوتاه‌سازی (قانون R6)")
    A("- خواندن/نوشتن متا با املای «تصحیح‌شده» (مثل `english_title` به‌جای `english_titl`)\n")
    return "\n".join(L)


def test_php(ph, t):
    n = t['id'].split('.')[1].zfill(2)
    checks = "\n".join(
        f"// {a}\ncheck(" + php_str(a) + ", function () { return null; /* IMPLEMENT: return true when {a} holds */ });"
        for a in t['asserts'])
    return f"""<?php
/**
 * Test — Task {t['id']}: {t['title']}
 * Phase {ph['num']}: {ph['title']}
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-{ph['num']:02d}-{ph['slug']}/tests/test-task-{n}.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task {t['id']}: {t['title']} ==\\n";
echo "   phase {ph['num']} | size {t['size']} | files: {len(t['files'])} | pass conditions: {len(t['pass_'])}\\n";

// --- Pass Conditions ({len(t['pass_'])} شرط) -----------------------------------
{checks}

// --- summary -----------------------------------------------------------------
summary();
"""


def run_phase_sh(ph):
    n = ph['num']
    lines = []
    for t in ph['tasks']:
        pad = t['id'].split('.')[1].zfill(2)
        # Titles are truncated for readability — strip backticks so the
        # generated shell never hits command substitution.
        label = t["title"].replace("`", "").replace('"', "'")[:40]
        lines.append(f'run_test "Task {t["id"]} — {label}" "test-task-{pad}.php"')
    body = "\n".join(lines)
    return f"""#!/usr/bin/env bash
# Phase {n} test suite — {ph['title']}
# Runs the whole phase inside ONE php-wasm boot (~100s of boot time per process).
set -uo pipefail
cd "$(dirname "$0")/../../.."      # repository root
exec node tools/harness/run-phase.mjs temp/phase-{n:02d}-{ph['slug']}/tests

# --- per-task breakdown (one process per task, slow; kept for reference) ---
: <<'REFERENCE'
source tools/harness/test-runner.sh
cd temp
{body}
phase_summary "Phase {n}"
REFERENCE
"""


def master_plan(phases):
    L = []
    A = L.append
    total = sum(len(p['tasks']) for p in phases)
    sm = {k: sum(1 for p in phases for t in p['tasks'] if t['size'] == k) for k in ('S', 'M', 'L')}
    A("# MASTER_PLAN — نقشه راه کامل پروژه\n")
    A(f"**{len(phases)} فاز · {total} تسک اتمیک** (اندازه: S={sm['S']} · M={sm['M']} · L={sm['L']})\n")
    A("> وضعیت‌ها: `[PENDING]` · `[IN_PROGRESS]` · `[TESTING]` · `[PASSED]`  ")
    A("> قانون: هیچ تسکی بدون `100% PASS` در تست خودکار بسته نمی‌شود؛ هیچ فازی بدون پاس‌شدن همه تسک‌هایش وارد فاز بعد نمی‌شود.\n")
    A("\n## تصمیم‌های Gate 0 (تأیید کاربر — 2026-11-03)\n")
    A("| # | موضوع | تصمیم |")
    A("|---|---|---|")
    A("| G1 | واریانت‌های هیرو | فقط `home1` رندر می‌شود؛ `home2`/`home3` به‌صورت فایل آماده و قابل فعال‌سازی از داشبورد (Task 5.20) |")
    A("| G2 | لندینگ `arya`/`smartelement` | فعلاً مسکوت؛ بعداً با Section Builder |")
    A("| G3 | سبد/پرداخت/حساب | Override قالب‌های ووکامرس با UI استاتیک + حفظ هوک‌ها |")
    A("| G4 | محتوا | کاملاً داینامیک از دیتابیس، صفر هاردکد |")
    A("| G5 | منوها | ساختار + آیتم + فیلدهای مگامنو در Appearance → Menus؛ فقط تنظیمات نمایشی در داشبورد |")
    A("| G6 | تحویل | Stage 1 → Gate 1 |")
    A("\n---\n")
    for p in phases:
        A(f"\n## Phase {p['num']} — {p['title']}\n")
        A(f"`temp/phase-{p['num']:02d}-{p['slug']}/`  ")
        A(f"**هدف:** {p['goal']}\n")
        A(f"**{len(p['tasks'])} تسک**\n")
        A("| # | تسک | اندازه | وابستگی | وضعیت | فایل‌های کلیدی |")
        A("|---|---|---|---|---|---|")
        for t in p['tasks']:
            dep = '، '.join(f'`{d}`' for d in t['deps']) or '—'
            files = t['files']
            fshow = '`' + files[0] + '`' + (f' +{len(files)-1}' if len(files) > 1 else '')
            A(f"| {t['id']} | {t['title']} | `{t['size']}` | {dep} | `{status_of(t)}` | {fshow} |")
        A(f"\n**گیت فاز:** `tests/run-phase-{p['num']:02d}-tests.sh`\n")
    A("\n---\n")
    A("## Gateها\n")
    A("| دروازه | شرط عبور | وضعیت |")
    A("|---|---|---|")
    A("| Gate 0 | تأیید اسکن و پاسخ سؤالات ابهام | ✅ PASSED |")
    A("| Gate 1 | پایان فاز ۱ — 170/170 assertion سبز، بودجه عملکرد OK | ✅ PASSED |")
    A("| Gate 2..8 | پایان هر فاز: گزارش تست + تأیید | — |")
    A("\n## وابستگی‌های بحرانی (زنجیره مسیر)\n")
    A("```text")
    A("1.1 → 1.2 → 1.3 → 1.4 → 1.5 (هارنس رندر: پیش‌نیاز همه تست‌های ساختاری)")
    A("                      1.6 → 1.9 → 1.10 → 1.12")
    A("2.1 → 2.3 → 2.4 → {2.7, 2.9, 2.10, 2.14}")
    A("2.3 → 2.6 → 2.11 → 2.12 → 2.13  (Section Builder)")
    A("3.8 → {3.9..3.17} → 3.18")
    A("1.11 → 4.2 → {4.4, 4.5, 4.8} ; 4.6 (Appearance) → 4.7")
    A("2.11+2.13 → 5.1 → {5.2..5.16} ; 5.17 (کارت‌ها) → 5.18 → 5.20")
    A("6.1 → 6.2 → {6.3..6.11} ; 5.17 → 6.12 → {6.13, 6.14, 6.15, 6.16}")
    A("7.1 → {7.2,7.3,7.4} ; 7.5 → {7.6,7.7} ; 2.11 → {7.16..7.21} → 7.22")
    A("8.5 → {8.6,8.7,8.8,8.9} → 8.10 → 8.11 ; 8.1 → 8.2 ; 8.3/8.4 → 8.18")
    A("```")
    return "\n".join(L) + "\n"


def main():
    phases = []
    for i in range(1, 9):
        mod = importlib.import_module(f'phase{i:02d}')
        importlib.reload(mod)
        phases.append(mod.PHASE)

    os.makedirs(f'{ROOT}/tests', exist_ok=True)
    for ph in phases:
        d = f"{ROOT}/phase-{ph['num']:02d}-{ph['slug']}"
        os.makedirs(f'{d}/tasks', exist_ok=True)
        os.makedirs(f'{d}/tests', exist_ok=True)
        # clean stale task/test files (renumbering safe)
        for sub in ('tasks', 'tests'):
            for fn in os.listdir(f'{d}/{sub}'):
                if fn.startswith(('task-', 'test-task-')) and fn.endswith(('.md', '.php')):
                    os.remove(f'{d}/{sub}/{fn}')
        with open(f'{d}/README.md', 'w', encoding='utf-8') as f:
            f.write(f"# Phase {ph['num']} — {ph['title']}\n\n**هدف:** {ph['goal']}\n\n"
                    f"**{len(ph['tasks'])} تسک اتمیک:** "
                    + '، '.join(f"`{t['id']}`" for t in ph['tasks'])
                    + f"\n\n**اجرای تست‌های فاز:**\n```bash\nbash {d}/tests/run-phase-{ph['num']:02d}-tests.sh\n```\n")
        for t in ph['tasks']:
            pad = t['id'].split('.')[1].zfill(2)
            with open(f"{d}/tasks/task-{pad}.md", 'w', encoding='utf-8') as f:
                f.write(task_md(ph, t))
            # A hand-written test (tools/plan/filled/<phase>/test-task-NN.php)
            # always wins over the generated scaffold: the tests are the spec,
            # the generator only seeds new ones.
            filled = f"tools/plan/filled/phase-{ph['num']:02d}/test-task-{pad}.php"
            if os.path.isfile(filled):
                shutil.copyfile(filled, f"{d}/tests/test-task-{pad}.php")
            else:
                with open(f"{d}/tests/test-task-{pad}.php", 'w', encoding='utf-8') as f:
                    f.write(test_php(ph, t))
        p = f"{d}/tests/run-phase-{ph['num']:02d}-tests.sh"
        with open(p, 'w', encoding='utf-8') as f:
            f.write(run_phase_sh(ph))
        os.chmod(p, 0o755)

    with open(f'{ROOT}/MASTER_PLAN.md', 'w', encoding='utf-8') as f:
        f.write(master_plan(phases))

    total = sum(len(p['tasks']) for p in phases)
    print(f"generated {total} atomic tasks in {len(phases)} phases")
    for p in phases:
        print(f"  phase {p['num']}: {len(p['tasks']):2} tasks  ({', '.join(t['size'] for t in p['tasks'])})")


if __name__ == '__main__':
    main()
