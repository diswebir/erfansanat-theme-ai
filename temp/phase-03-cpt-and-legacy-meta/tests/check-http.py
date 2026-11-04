#!/usr/bin/env python3
"""Phase 3 HTTP acceptance: real URLs answer, real 404s stay 404."""
from __future__ import annotations

import argparse
import json
import re
import subprocess
import sys
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]
FAILED: list[str] = []
TOTAL = 0


def check(task: str, label: str, ok: bool, detail: str = "") -> None:
    global TOTAL
    TOTAL += 1
    if ok:
        print(f"[PASS] {label}")
    else:
        FAILED.append(f"{task}: {label}" + (f" ({detail})" if detail else ""))
        print(f"[FAIL] {label}" + (f" — {detail}" if detail else ""))


def fetch(url: str) -> tuple[int, str]:
    request = urllib.request.Request(url, headers={"User-Agent": "erfansanat-phase3-check"})
    try:
        with urllib.request.urlopen(request, timeout=30) as response:
            return response.status, response.read().decode("utf-8", "replace")
    except urllib.error.HTTPError as error:
        return error.code, error.read().decode("utf-8", "replace")


def wp(*args: str) -> str:
    result = subprocess.run(
        ["tools/wp", "--path=.local/wordpress", *args],
        cwd=ROOT, capture_output=True, text=True, check=False,
    )
    return result.stdout.strip()


def term_url(taxonomy: str) -> str:
    return wp(
        "eval",
        f'$t=get_terms(["taxonomy"=>"{taxonomy}","hide_empty"=>false,"number"=>1])[0];'
        f'echo str_replace(home_url(),"",get_term_link($t));',
    ).splitlines()[-1]


def task_3_1(base: str) -> None:
    for path, label in [
        ("/projects/", "projects archive"),
        ("/services/", "services archive"),
        ("/shop/", "shop archive"),
    ]:
        status, _ = fetch(base + path)
        check("3.1", f"{label} answers 200", status == 200, f"status={status}")

    project = wp("post", "list", "--post_type=projects", "--fields=ID,post_name", "--format=csv").splitlines()
    service = wp("post", "list", "--post_type=services", "--fields=ID,post_name", "--format=csv").splitlines()
    check("3.1", "project fixtures exist", len(project) > 1)
    check("3.1", "service fixtures exist", len(service) > 1)
    if len(project) > 1:
        slug = project[1].split(",")[1]
        status, body = fetch(f"{base}/projects/{slug}/")
        check("3.1", "single project answers 200", status == 200, f"status={status} slug={slug}")
        check("3.1", "single project is not the fallback template", "no-results" not in body)
    if len(service) > 1:
        slug = service[1].split(",")[1]
        status, _ = fetch(f"{base}/services/{slug}/")
        check("3.1", "single service answers 200", status == 200, f"status={status} slug={slug}")

    for taxonomy in ("city", "project_type", "product_cat"):
        path = term_url(taxonomy)
        status, _ = fetch(base + path)
        check("3.1", f"{taxonomy} term archive answers 200", status == 200, f"status={status} path={path}")

    product = wp("post", "list", "--post_type=product", "--fields=ID,post_name", "--format=csv").splitlines()
    if len(product) > 1:
        slug = product[1].split(",")[1]
        status, _ = fetch(f"{base}/product/{slug}/")
        check("3.1", "single product keeps /product/ prefix", status == 200, f"status={status} slug={slug}")

    status, _ = fetch(base + "/this-path-must-not-exist-3129/")
    check("3.1", "unknown path still answers 404", status == 404, f"status={status}")

    status, _ = fetch(base + "/products/")
    check("3.1", "legacy /products/ alias is not silently activated", status == 404, f"status={status}")

    rules = wp("option", "get", "rewrite_rules", "--format=json")
    check("3.1", "rewrite rules contain the project paths", "project_type" in rules and "projects" in rules)


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--base", default="http://127.0.0.1:8080")
    parser.add_argument("--task", action="append", choices=["3.1"])
    args = parser.parse_args()
    tasks = args.task or ["3.1"]
    for task in tasks:
        print(f"\n== task {task} ==")
        globals()["task_" + task.replace(".", "_")](args.base)

    print(f"\n{TOTAL - len(FAILED)}/{TOTAL} HTTP checks passed.")
    for failure in FAILED:
        print("  - " + failure)
    if FAILED:
        return 1
    print("PHASE 3 HTTP CHECKS PASSED — local synthetic site only.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
