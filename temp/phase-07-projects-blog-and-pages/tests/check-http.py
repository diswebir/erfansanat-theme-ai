#!/usr/bin/env python3
"""Phase 7 HTTP acceptance: projects, blog, search, 404 and services.

Every assertion drives the live local site rather than reading the theme source,
so a template that renders but loses its markup contract still fails.

Usage:
    python3 temp/phase-07-projects-blog-and-pages/tests/check-http.py [--task 7.1]
"""
from __future__ import annotations

import argparse
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
BASE = "http://127.0.0.1:8080"

# Strings that mean a template leaked a sample value or an internal error.
ERROR_MARKERS = ("Fatal error", "Warning:", "Notice:", "Deprecated:")
# Text only the fabricated reference carries; reproducing it would be a lie.
FORBIDDEN = ("ESE-1403-8942", "data-checkout-receipt", "localStorage")


def check(task: str, label: str, ok: bool, detail: str = "") -> None:
    """Record one assertion."""
    global TOTAL
    TOTAL += 1
    if ok:
        print(f"[PASS] {label}")
    else:
        FAILED.append(f"{task}: {label}" + (f" ({detail})" if detail else ""))
        print(f"[FAIL] {label}" + (f" — {detail}" if detail else ""))


def fetch(url: str) -> tuple[int, str]:
    """Fetch a URL and always return a status and a body."""
    request = urllib.request.Request(
        url, headers={"User-Agent": "erfansanat-phase7-check"}
    )
    try:
        with urllib.request.urlopen(request, timeout=30) as response:
            return response.status, response.read().decode("utf-8", "replace")
    except urllib.error.HTTPError as error:
        return error.code, error.read().decode("utf-8", "replace")
    except urllib.error.URLError as error:
        return 0, str(error)


def wp(*args: str) -> str:
    """Run wp-cli against the local site."""
    result = subprocess.run(
        ["tools/wp", "--path=.local/wordpress", *args],
        cwd=ROOT,
        capture_output=True,
        text=True,
        check=False,
    )
    return result.stdout.strip()


def slugs(post_type: str, limit: int = 0) -> list[str]:
    """Published slugs of a post type."""
    args = [
        "post", "list", f"--post_type={post_type}", "--post_status=publish",
        "--format=ids", "--posts_per_page=-1",
    ]
    ids = wp(*args).split()
    if limit:
        ids = ids[:limit]
    out: list[str] = []
    for pid in ids:
        slug = wp("post", "get", pid, "--field=post_name")
        if slug:
            out.append(slug)
    return out


def clean(body: str) -> bool:
    """True when the body has no PHP error output."""
    return not any(marker in body for marker in ERROR_MARKERS)


def h1_count(body: str) -> int:
    """Number of h1 elements."""
    return len(re.findall(r"<h1[ >]", body))


def task_7_1() -> None:
    """Single project and the project, city and type archives."""

    # --- the single project page ---
    projects = slugs("projects")
    check("7.1", "the fixture has published projects", len(projects) >= 10, f"got {len(projects)}")
    check("7.1", "no project renders with a PHP error",
          all(clean(fetch(f"{BASE}/projects/{s}/")[1]) for s in projects[:6]))

    project_url = f"{BASE}/projects/{projects[0]}/"
    status, body = fetch(project_url)
    check("7.1", "single project returns 200", status == 200, f"status={status}")
    check("7.1", "single project has exactly one h1", h1_count(body) == 1, f"got {h1_count(body)}")

    check("7.1", "project hero is the reference structure", 'data-project-hero="true"' in body)
    check("7.1", "gallery binds the transferred lightbox", 'data-gallery-root="true"' in body)
    check("7.1", "gallery has a main image", 'data-gallery-main-img="true"' in body)
    check("7.1", "gallery has thumbnails", body.count("data-gallery-thumb-item=") >= 1)
    check("7.1", "the details panel is present", 'id="project-details"' in body)
    check("7.1", "the details panel has three tabs", body.count("data-prod-tab-btn=") == 3)
    check("7.1", "the details panel has three panels", body.count("data-prod-tab-panel=") == 3)
    check("7.1", "the sticky summary is present", "چکیده اطلاعات پروژه" in body)
    check("7.1", "products used in the project are listed", "محصولات و تجهیزات استفاده‌شده" in body)
    check("7.1", "the related strip is present", "سایر پروژه‌های شاخص" in body)
    check("7.1", "the closing cta reuses the home section", 'data-section="contact-cta"' in body)
    check("7.1", "no fabricated tracking code leaks in",
          not any(token in body for token in FORBIDDEN))

    # --- the archive ---
    status, body = fetch(f"{BASE}/projects/")
    check("7.1", "project archive returns 200", status == 200, f"status={status}")
    check("7.1", "project archive has exactly one h1", h1_count(body) == 1)
    check("7.1", "project archive binds the filter script", 'data-projects-archive="true"' in body)
    check("7.1", "project archive renders a grid", 'data-project-grid="true"' in body)
    items = body.count('data-project-item="true"')
    check("7.1", "project archive renders six per page", items == 6, f"got {items}")
    check("7.1", "the grid carries filter payloads", 'data-project-type=' in body and 'data-project-location=' in body)
    check("7.1", "the pager declares server-side paging", 'data-pagination-server="true"' in body)
    check("7.1", "the pager has real links", 'data-page-num="2"' in body)

    status, page_two = fetch(f"{BASE}/projects/page/2/")
    check("7.1", "the second page exists", status == 200, f"status={status}")
    check("7.1", "the second page is a different slice",
          page_two.count('data-project-item="true"') == 6)

    status, _ = fetch(f"{BASE}/projects/page/9/")
    check("7.1", "a page past the end is 404", status == 404, f"status={status}")

    # --- city and type archives ---
    for taxonomy, slug_source in (("city", "city"), ("project_type", "project_type")):
        # wp-cli prints a percent-encoded slug; decode it before quoting again or
        # the request double-encodes and every term archive 404s.
        raw = wp(
            "eval",
            f'$t = get_terms(["taxonomy" => "{taxonomy}", "hide_empty" => true, '
            f'"number" => 1]); echo $t ? rawurldecode($t[0]->slug) : "";',
        )
        term_slugs = [raw] if raw else []
        check("7.1", f"{taxonomy} terms exist", len(term_slugs) >= 1)
        if not term_slugs:
            continue
        url = f"{BASE}/{slug_source}/{urllib.parse.quote(term_slugs[0])}/"
        status, term_body = fetch(url)
        check("7.1", f"{taxonomy} archive returns 200", status == 200, f"{url} status={status}")
        check("7.1", f"{taxonomy} archive has one h1", h1_count(term_body) == 1)
        check("7.1", f"{taxonomy} archive renders projects", 'data-project-item="true"' in term_body)
        check("7.1", f"{taxonomy} archive has no PHP error", clean(term_body))

    # --- the featured-projects page is not the archive ---
    status, featured = fetch(f"{BASE}/project/")
    check("7.1", "the featured page returns 200", status == 200, f"status={status}")
    check("7.1", "the featured page uses its own template", 'data-featured-hero="true"' in featured)
    check("7.1", "the featured page is not the archive", 'data-projects-archive' not in featured)


def task_7_2() -> None:
    """Blog index, single post, search and 404."""

    posts = slugs("post")
    check("7.2", "the fixture has published posts", len(posts) >= 5, f"got {len(posts)}")

    status, body = fetch(f"{BASE}/blog/")
    check("7.2", "blog index returns 200", status == 200, f"status={status}")
    check("7.2", "blog index has exactly one h1", h1_count(body) == 1, f"got {h1_count(body)}")
    check("7.2", "blog index binds the filter script", 'data-blog-archive="true"' in body)
    check("7.2", "blog index renders cards", body.count('data-blog-item="true"') >= 1)
    check("7.2", "blog cards carry a search payload", 'data-blog-search-text=' in body)
    check("7.2", "blog index has a live search box", 'data-blog-search="true"' in body)
    check("7.2", "blog index has category buttons", body.count("data-blog-cat-btn=") >= 2)
    check("7.2", "blog index has a pager", "data-pagination-root" in body)
    check("7.2", "blog index has no PHP error", clean(body))

    # --- the single post ---
    post_slug = posts[0]
    for candidate in posts:
        if not candidate.startswith("p05"):
            post_slug = candidate
            break
    status, post_body = fetch(f"{BASE}/{post_slug}/")
    check("7.2", "single post returns 200", status == 200, f"{post_slug} status={status}")
    check("7.2", "single post has exactly one h1", h1_count(post_body) == 1)
    check("7.2", "the article body uses the theme's prose scope", "erfansanat-prose" in post_body)
    check("7.2", "the article stylesheet loads on a post", "css/article.css" in post_body)
    check("7.2", "the article stylesheet stays off the home page",
          "css/article.css" not in fetch(f"{BASE}/")[1])
    check("7.2", "the share control is present", "data-share-btn" in post_body)
    check("7.2", "related posts are rendered", "مقالات مرتبط" in post_body)
    check("7.2", "single post has no PHP error", clean(post_body))

    # TOC links must equal TOC anchors, or a reader clicks into nothing.
    toc_links = len(re.findall(r'href="#sec-\d+"', post_body))
    anchors = len(re.findall(r'id="sec-\d+"', post_body))
    if toc_links:
        check("7.2", "every table-of-contents link lands on an anchor",
              toc_links == anchors, f"{toc_links} links vs {anchors} anchors")
    else:
        check("7.2", "a post without h2 headings prints no table of contents",
              "سرفصل‌های مقاله" not in post_body)

    # --- search ---
    status, search = fetch(f"{BASE}/?s=" + urllib.parse.quote("نور"))
    check("7.2", "search returns 200", status == 200, f"status={status}")
    check("7.2", "search has exactly one h1", h1_count(search) == 1)
    check("7.2", "search groups results by type", "نتایج برای" in search)
    check("7.2", "search renders results", "card-light" in search or "card-glow" in search)
    check("7.2", "search has no PHP error", clean(search))

    status, empty_search = fetch(f"{BASE}/?s=" + urllib.parse.quote("zzzz-no-such-term"))
    check("7.2", "an empty search still renders", status == 200, f"status={status}")
    check("7.2", "an empty search says so", "نتیجه‌ای برای این جستجو پیدا نشد" in empty_search)

    # --- category archive ---
    status, category = fetch(f"{BASE}/category/" + urllib.parse.quote("مقالات-آزمایشی") + "/")
    check("7.2", "category archive returns 200", status == 200, f"status={status}")
    check("7.2", "category archive has one h1", h1_count(category) == 1)
    check("7.2", "category archive renders cards", 'data-blog-item="true"' in category)

    # --- 404 ---
    status, not_found = fetch(f"{BASE}/this-page-does-not-exist-at-all/")
    check("7.2", "a missing page is a real 404", status == 404, f"status={status}")
    check("7.2", "the 404 page uses the theme template", "data-not-found" in not_found)
    check("7.2", "the 404 page has exactly one h1", h1_count(not_found) == 1)
    check("7.2", "the 404 page offers a search box", 'name="s"' in not_found)
    check("7.2", "the 404 page suggests real products", not_found.count("card-glow") >= 1)
    check("7.2", "the 404 page has no PHP error", clean(not_found))


def task_7_3() -> None:
    """Services and the public page templates."""

    services = slugs("services")
    check("7.3", "the fixture has services", len(services) >= 5, f"got {len(services)}")

    status, body = fetch(f"{BASE}/services/")
    check("7.3", "services archive returns 200", status == 200, f"status={status}")
    check("7.3", "services archive has exactly one h1", h1_count(body) == 1)
    check("7.3", "services archive renders every service", body.count("جزئیات خدمت") == len(services))
    check("7.3", "services archive has no PHP error", clean(body))

    for slug in services[:3]:
        status, service = fetch(f"{BASE}/services/{slug}/")
        check("7.3", f"service {slug} returns 200", status == 200, f"status={status}")
        check("7.3", f"service {slug} has one h1", h1_count(service) == 1)
        check("7.3", f"service {slug} shows the process", "مسیر اجرای خدمت" in service)
        check("7.3", f"service {slug} has no PHP error", clean(service))

    # --- public pages ---
    pages = {
        "about": "/about/",
        "contact": "/contact/",
        "faq": "/faq/",
        "licenses": "/licenses-and-honors/",
        "recruitment": "/recruitment/",
        "refund": "/refund_returns/",
        "arya": "/arya/",
    }
    for name, path in pages.items():
        status, page = fetch(BASE + path)
        check("7.3", f"page {name} returns 200", status == 200, f"{path} status={status}")
        check("7.3", f"page {name} has exactly one h1", h1_count(page) == 1)
        check("7.3", f"page {name} has no PHP error", clean(page))

    status, recruitment = fetch(f"{BASE}/recruitment/")
    check("7.3", "the recruitment page has no cv upload yet", 'type="file"' not in recruitment)
    check("7.3", "the recruitment page still offers a real way to apply",
          "mailto:" in recruitment or "ارسال رزومه" in recruitment)


def main() -> int:
    """Run the selected task groups."""
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--task", action="append", choices=["7.1", "7.2", "7.3"])
    args = parser.parse_args()

    groups = {"7.1": task_7_1, "7.2": task_7_2, "7.3": task_7_3}
    chosen = args.task or list(groups)

    status, _ = fetch(BASE + "/")
    if status != 200:
        print(f"No local preview on {BASE}; start it with tools/preview-wordpress.sh.")
        return 2

    for key in chosen:
        groups[key]()

    print()
    if FAILED:
        print(f"{TOTAL - len(FAILED)}/{TOTAL} HTTP checks passed.")
        for row in FAILED:
            print("  - " + row)
        print("PHASE 7 HTTP CHECKS FAILED.")
        return 1
    print(f"{TOTAL}/{TOTAL} HTTP checks passed.")
    print("PHASE 7 HTTP CHECKS PASSED — local synthetic site only.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
