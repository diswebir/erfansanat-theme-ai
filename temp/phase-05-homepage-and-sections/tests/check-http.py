#!/usr/bin/env python3
"""Phase 5 HTTP acceptance: the fifteen home sections render real data."""
from __future__ import annotations

import argparse
import re
import subprocess
import sys
import urllib.error
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]
FAILED: list[str] = []
TOTAL = 0
BASE = "http://127.0.0.1:8080"

REFERENCE_ORDER = [
    "hero",
    "project-types",
    "projects",
    "about",
    "knowledge",
    "history",
    "products",
    "catalog",
    "services",
    "quality",
    "support",
    "clients",
    "blog",
    "contact-cta",
    "location",
]


def check(task: str, label: str, ok: bool, detail: str = "") -> None:
    global TOTAL
    TOTAL += 1
    if ok:
        print(f"[PASS] {label}")
    else:
        FAILED.append(f"{task}: {label}" + (f" ({detail})" if detail else ""))
        print(f"[FAIL] {label}" + (f" — {detail}" if detail else ""))


def fetch(url: str) -> tuple[int, str]:
    request = urllib.request.Request(url, headers={"User-Agent": "erfansanat-phase5-check"})
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


def task_5_1(_: str) -> None:
    status, home = fetch(BASE + "/")
    check("5.1", "front page answers 200", status == 200, f"status={status}")
    if status != 200:
        return

    check("5.1", "no PHP error leaks into the page",
          not any(token in home for token in ["Fatal error", "Parse error", "Warning: ", "Notice: ", "Deprecated: "]))

    # All fifteen sections render, in the reference order.
    positions = {}
    for key in REFERENCE_ORDER:
        marker = f'data-section="{key}"'
        index = home.find(marker)
        check("5.1", f"section {key} renders", index >= 0)
        if index >= 0:
            positions[key] = index

    ordered = [k for k in REFERENCE_ORDER if k in positions]
    check(
        "5.1",
        "sections appear in the reference order",
        ordered == sorted(ordered, key=lambda k: positions[k]),
        str(ordered),
    )

    # The reference anchors exist once each.
    ids = re.findall(r'\sid="([^"]+)"', home)
    duplicates = sorted({i for i in ids if ids.count(i) > 1})
    check("5.1", "no duplicate DOM ids", not duplicates, ",".join(duplicates))

    for anchor in ["home", "projects", "about", "products", "services", "blog-preview", "contact"]:
        check("5.1", f"reference anchor #{anchor} exists once", ids.count(anchor) == 1, str(ids.count(anchor)))

    # The hero owns #home, the wrapper does not repeat it.
    hero = home[home.find('data-section="hero"'):]
    check("5.1", "hero section owns id=home", hero[:2000].count('id="home"') == 1)

    # Hero JS contract.
    check("5.1", "hero has five scene buttons", len(re.findall(r'data-home1-scene-btn="\d+"', home)) == 5)
    check("5.1", "hero has five scene images", len(re.findall(r'data-home1-scene-img="\d+"', home)) == 5)
    check("5.1", "hero has the play trigger", 'data-home1-play-trigger="true"' in home)
    check("5.1", "hero has video controls", 'data-home1-video-close="true"' in home and 'data-home1-video-mute="true"' in home)
    check("5.1", "hero category grid present", 'data-home1-categories-grid="true"' in home)

    # History JS contract.
    check("5.1", "history rail has six steps", len(re.findall(r'data-home2-history-btn="\d+"', home)) == 6)
    check("5.1", "history has six panels", len(re.findall(r'data-home2-history-panel="\d+"', home)) == 6)
    check("5.1", "history has prev/next", 'data-home2-history-prev="true"' in home and 'data-home2-history-next="true"' in home)
    check("5.1", "history progress bar present", 'data-history-progress-bar="true"' in home)

    # Services JS contract.
    check("5.1", "services root hook present", 'data-services-section="true"' in home)
    check("5.1", "services has five rows", len(re.findall(r'data-service-row="\d+"', home)) == 5)
    check("5.1", "services preview target present", 'data-service-preview-img="true"' in home)
    check("5.1", "services preview counter present", 'data-service-preview-counter="true"' in home)


def task_5_2(_: str) -> None:
    _, home = fetch(BASE + "/")

    # Products come from WooCommerce with real prices.
    product_count = wp("post", "list", "--post_type=product", "--post_status=publish", "--format=count")
    check("5.2", "the fixture has published products", product_count.isdigit() and int(product_count) > 0, product_count)
    check("5.2", "product cards render", "card-glow" in home and "/product/" in home)
    price_blocks = re.findall(r"woocommerce-Price-amount", home)
    check("5.2", "real WooCommerce price markup is printed", len(price_blocks) > 0, str(len(price_blocks)))

    # No sample value from the static mirror can leak in.
    check("5.2", "no static mirror product link", 'href="product/' not in home)
    check("5.2", "no static mirror project link", 'href="projects/' not in home)
    check("5.2", "no localStorage sample cart", "localStorage" not in home)

    # Projects come from the CPT.
    project_count = wp("post", "list", "--post_type=projects", "--post_status=publish", "--format=count")
    if project_count.isdigit() and int(project_count) > 0:
        check("5.2", "project cards render", "مشاهده جزئیات پروژه" in home)
        project_ids = wp("post", "list", "--post_type=projects", "--post_status=publish", "--field=ID", "--number=3")
        found = 0
        for pid in [p for p in project_ids.splitlines() if p.strip()]:
            permalink = wp("post", "url", pid)
            path = permalink.replace(BASE, "").replace("http://127.0.0.1:8080", "")
            if path and path in home:
                found += 1
        check("5.2", "project cards link real permalinks", found > 0, f"{found} matched")

    # Posts come from the blog.
    post_count = wp("post", "list", "--post_type=post", "--post_status=publish", "--format=count")
    if post_count.isdigit() and int(post_count) > 0:
        check("5.2", "post cards render", "مطالعه مقاله" in home)

    # Project tabs use real taxonomy slugs.
    panels = re.findall(r'data-home2-port-panel="([^"]+)"', home)
    check("5.2", "project gallery has an all tab", "all" in panels)
    check("5.2", "project gallery tabs match tab buttons",
          sorted(set(panels)) == sorted(set(re.findall(r'data-home2-port-tab="([^"]+)"', home))))

    type_slugs = wp("term", "list", "project_type", "--field=slug", "--hide_empty=1")
    slugs = [s for s in type_slugs.splitlines() if s.strip()]
    if slugs:
        matched = [s for s in slugs if s in panels or any(s in p for p in panels)]
        check("5.2", "project tabs come from real project types", len(matched) > 0, ",".join(slugs[:3]))

    # Cards are escaped and carry no unclosed markup.
    check("5.2", "no unescaped script tag in cards", "<script>alert" not in home)
    check("5.2", "images are lazy loaded", home.count('loading="lazy"') > 5)
    check("5.2", "no empty image src", 'src=""' not in home)

    # A page that is not the front page must not print the home sections.
    status, about = fetch(BASE + "/about/")
    check("5.2", "inner page answers 200", status == 200)
    if status == 200:
        check("5.2", "home sections do not leak into an inner page", 'data-section="hero"' not in about)


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--base", default="http://127.0.0.1:8080")
    parser.add_argument("--task", action="append", choices=["5.1", "5.2"])
    args = parser.parse_args()
    global BASE
    BASE = args.base
    tasks = args.task or ["5.1", "5.2"]
    for task in tasks:
        print(f"\n== task {task} ==\n")
        globals()["task_" + task.replace(".", "_")](args.base)

    print(f"\n{TOTAL - len(FAILED)}/{TOTAL} HTTP checks passed.")
    for failure in FAILED:
        print("  - " + failure)
    if FAILED:
        return 1
    print("PHASE 5 HTTP CHECKS PASSED — local synthetic site only.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
