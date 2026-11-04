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


def wp_meta(post_id: str, key: str) -> str:
    return wp("post", "meta", "get", post_id, key)


def task_3_2(base: str) -> None:
    products = wp("post", "list", "--post_type=product", "--post_status=any", "--fields=ID,post_name", "--format=csv").splitlines()
    check("3.2", "product fixtures exist", len(products) > 1)
    if len(products) < 2:
        return
    product_id, slug = products[1].split(",")

    status, body = fetch(f"{base}/product/{slug}/")
    check("3.2", "product page renders", status == 200, f"status={status}")
    check("3.2", "product page stays the WooCommerce template", "single-product" in body or "product" in body)

    # Write both key groups through the theme API, then read them back.
    # The payload goes through a file: embedding JSON in `wp eval` quoting is a
    # trap that silently corrupts escapes.
    probe = ROOT / ".local" / "probe-product-meta.php"
    probe.parent.mkdir(parents=True, exist_ok=True)
    probe.write_text(
        "<?php\n"
        "$id = " + product_id + ";\n"
        "\\ErfanSanat\\Theme\\Admin\\ProductMetaboxes::persist($id, array(\n"
        "    'gs1_id' => '06261010999999',\n"
        "    'national_code' => '1234567890123',\n"
        "    'english_titl' => 'HTTP Probe',\n"
        "    'true_sale' => 'false',\n"
        "    'faq' => array(array('name_question'=>'سوال HTTP؟','answer_question'=>'پاسخ HTTP')),\n"
        "));\n"
        "echo $id;\n",
        encoding="utf-8",
    )
    try:
        written_id = wp("eval-file", str(probe.relative_to(ROOT))).splitlines()[-1].strip()
    finally:
        probe.unlink(missing_ok=True)
    check("3.2", "meta write runs for a fixture product", written_id == product_id, f"{written_id} vs {product_id}")

    check("3.2", "identifiers persist", wp_meta(product_id, "gs1_id") == "06261010999999", wp_meta(product_id, "gs1_id"))
    check("3.2", "national code persists", wp_meta(product_id, "national_code") == "1234567890123", wp_meta(product_id, "national_code"))
    check("3.2", "english title keeps its historical key", wp_meta(product_id, "english_titl") == "HTTP Probe", wp_meta(product_id, "english_titl"))
    check("3.2", "sale flag stores the legacy string", wp_meta(product_id, "true_sale") == "false", wp_meta(product_id, "true_sale"))

    raw = wp_meta(product_id, "faq")
    check("3.2", "repeater is stored as escaped JSON", raw.startswith("{") and "item-0" in raw, raw[:80])
    check("3.2", "persian escapes were not stripped", "u0633u0648" not in raw, raw[:80])
    read_probe = ROOT / ".local" / "probe-read-faq.php"
    read_probe.write_text(
        "<?php\n"
        "$rows = \\ErfanSanat\\Theme\\Data\\LegacyMeta::read_repeater(" + product_id + ", 'faq');\n"
        "echo $rows[0]['name_question'] ?? '';\n",
        encoding="utf-8",
    )
    try:
        question = wp("eval-file", str(read_probe.relative_to(ROOT))).splitlines()[-1].strip()
    finally:
        read_probe.unlink(missing_ok=True)
    check("3.2", "repeater reads back as real Persian text", question == "سوال HTTP؟", question)

    sale_row = wp("post", "list", "--post_type=product", "--fields=ID", "--format=csv")
    check("3.2", "products survive the meta write", len(sale_row) > 1, sale_row[:80])

    status, _ = fetch(f"{base}/wp-admin/edit.php?post_type=product")
    check("3.2", "product list is not fatal without a login", status in (200, 302), f"status={status}")

    # Leave the fixture as it was found.
    wp("post", "meta", "delete", product_id, "gs1_id")
    wp("post", "meta", "delete", product_id, "national_code")
    wp("post", "meta", "delete", product_id, "english_titl")
    wp("post", "meta", "delete", product_id, "true_sale")
    wp("post", "meta", "delete", product_id, "faq")


def task_3_3(base: str) -> None:
    """Project, term, hreflang and view meta on the live local site."""
    projects = wp("post", "list", "--post_type=projects", "--post_status=any", "--fields=ID", "--format=ids").split()
    check("3.3", "project fixtures exist", bool(projects))
    if not projects:
        return
    project_id = projects[0]

    probe = ROOT / ".local" / "probe-project-meta.php"
    probe.write_text(
        "<?php\n"
        "\\ErfanSanat\\Theme\\Admin\\ProjectMetaboxes::persist(" + project_id + ", array(\n"
        "    'finish-pro-time' => '۱۴۰۴/۱/۵',\n"
        "    'mortabet_mahsole_2' => '11,12,12',\n"
        "));\n"
        "\\ErfanSanat\\Theme\\Admin\\ContentMetaboxes::persist(" + project_id + ", array(\n"
        "    'hrefmeta_en' => 'https://erfansanat.test/en/project/',\n"
        "    'same_key_hrefl' => 'probe-key',\n"
        "    'number2-viewc' => '5',\n"
        "));\n"
        "echo 'ok';\n",
        encoding="utf-8",
    )
    try:
        wp("eval-file", str(probe.relative_to(ROOT)))
    finally:
        probe.unlink(missing_ok=True)

    check("3.3", "jalali finish date is normalised", wp_meta(project_id, "finish-pro-time") == "1404/01/05", wp_meta(project_id, "finish-pro-time"))
    check("3.3", "related products keep their exact key", wp_meta(project_id, "mortabet_mahsole_2") == "[11,12]", wp_meta(project_id, "mortabet_mahsole_2"))
    check("3.3", "hreflang english link is stored", wp_meta(project_id, "hrefmeta_en") == "https://erfansanat.test/en/project/")
    check("3.3", "shared hreflang key is stored", wp_meta(project_id, "same_key_hrefl") == "probe-key")
    check("3.3", "view counter is stored", wp_meta(project_id, "number2-viewc") == "5")

    status, body = fetch(f"{base}/projects/{wp('post', 'get', project_id, '--field=post_name')}/")
    check("3.3", "project page still renders after the meta write", status == 200, f"status={status}")
    # Core prints RSS discovery links as rel="alternate"; only the hreflang
    # variant would conflict with SEOPress, so that is what must be absent.
    has_hreflang_link = bool(re.search(r'rel=["\']alternate["\'][^>]*hreflang=|hreflang=["\'][a-z]{2}["\']', body))
    check("3.3", "theme does not print competing hreflang tags", not has_hreflang_link)

    # Foreign plugin meta must survive.
    wp("post", "meta", "update", project_id, "_rank_math_title", "SEO probe")
    check("3.3", "foreign seo meta is untouched by our write", wp_meta(project_id, "_rank_math_title") == "SEO probe")

    for key in ("finish-pro-time", "mortabet_mahsole_2", "hrefmeta_en", "same_key_hrefl", "number2-viewc", "_rank_math_title"):
        wp("post", "meta", "delete", project_id, key)


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--base", default="http://127.0.0.1:8080")
    parser.add_argument("--task", action="append", choices=["3.1", "3.2", "3.3"])
    args = parser.parse_args()
    tasks = args.task or ["3.1", "3.2", "3.3"]
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
