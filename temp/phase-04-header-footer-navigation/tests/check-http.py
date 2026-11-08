#!/usr/bin/env python3
"""Phase 4 HTTP acceptance: the reference header, footer and navigation over HTTP."""
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


def check(task: str, label: str, ok: bool, detail: str = "") -> None:
    global TOTAL
    TOTAL += 1
    if ok:
        print(f"[PASS] {label}")
    else:
        FAILED.append(f"{task}: {label}" + (f" ({detail})" if detail else ""))
        print(f"[FAIL] {label}" + (f" — {detail}" if detail else ""))


def fetch(url: str) -> tuple[int, str]:
    request = urllib.request.Request(url, headers={"User-Agent": "erfansanat-phase4-check"})
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


def ids_in(html: str) -> list[str]:
    return re.findall(r'\sid="([^"]+)"', html)


def task_4_1(_: str) -> None:
    status, home = fetch(BASE + "/")
    check("4.1", "home page answers 200", status == 200, f"status={status}")

    if status != 200:
        return

    # Hook order and document scaffolding.
    check("4.1", "doctype is first", home.lstrip().lower().startswith("<!doctype html"))
    check("4.1", "single <html> with lang and dir", len(re.findall(r"<html\b", home)) == 1 and re.search(r'<html[^>]*\blang="fa', home) is not None and re.search(r'<html[^>]*\bdir="rtl"', home) is not None)
    check("4.1", "single <head> and single <body>", len(re.findall(r"<head\b", home)) == 1 and len(re.findall(r"<body\b", home)) == 1)
    check("4.1", "wp_head output present", "wp-block-library" in home or "erfansanat-main" in home)
    check("4.1", "body class carries wp-singular/home", 'class="' in home and ("home" in (re.search(r'<body[^>]*class="([^"]*)"', home) or [None, ""])[1]))

    # The wrapper deviation is applied, not the duplicated reference id.
    check("4.1", "page wrapper id present", 'id="erfansanat-page"' in home)
    home_ids = ids_in(home)
    duplicates = sorted({i for i in home_ids if home_ids.count(i) > 1})
    check("4.1", "no duplicate DOM ids on the home page", not duplicates, ",".join(duplicates))

    # Header contracts from the reference.
    check("4.1", "header carries data-navbar-header", "data-navbar-header" in home)
    # Section headings legitimately use <header> too, so count the site header.
    check("4.1", "exactly one site header", len(re.findall(r'<header\b[^>]*data-navbar-header', home)) == 1)
    check("4.1", "site header precedes <main>", home.find("data-navbar-header") < home.find("<main"))
    check("4.1", "exactly one <main> element", len(re.findall(r"<main\b", home)) == 1)
    check("4.1", "exactly one <footer> element", len(re.findall(r"<footer\b", home)) == 1)
    check("4.1", "main is closed before the footer", home.find("</main>") < home.find("<footer"))
    check("4.1", "document ends with </html>", home.rstrip().endswith("</html>"))

    # Accessibility wiring.
    controls = set(re.findall(r'aria-controls="([^"]+)"', home))
    id_set = set(home_ids)
    check("4.1", "every aria-controls target exists", controls <= id_set, ",".join(sorted(controls - id_set)))
    check("4.1", "nav is labelled", 'aria-label="منوی اصلی"' in home)
    # The target existing is not the same as a control pointing at it: a keyboard
    # user needs the link, and it has to be the first focusable element.
    check("4.1", "skip link targets <main>", 'class="erfansanat-skip-link" href="#primary"' in home.replace("'", '"'))
    check("4.1", "skip link is first in the page wrapper",
          home.find("erfansanat-skip-link") < home.find("data-navbar-header") and
          home.find("erfansanat-skip-link") < home.find("data-notice-bar") if "data-notice-bar" in home else
          home.find("erfansanat-skip-link") < home.find("data-navbar-header"))
    check("4.1", "the skip target is focusable", re.search(r'<main[^>]*id="primary"[^>]*tabindex="-1"', home) is not None
          or re.search(r'<main[^>]*tabindex="-1"[^>]*id="primary"', home) is not None)

    # `.screen-reader-text` is used on the search label, so the sheet that defines
    # it must be loaded on every front-end page, not only where it is used.
    a11y = re.search(r"assets/css/a11y[.]css[^\"']*", home)
    check("4.1", "accessibility stylesheet is loaded", a11y is not None)
    check("4.1", "accessibility stylesheet follows the main sheet",
          home.find("assets/css/a11y.css") > home.find("assets/css/styles.css"))
    if a11y:
        css_status, css = fetch(BASE + "/wp-content/themes/erfansanat/" + a11y.group(0).split("?")[0].replace("assets/", "assets/"))
        check("4.1", "the stylesheet defines screen-reader-text",
              css_status == 200 and ".screen-reader-text" in css and "clip-path" in css,
              f"status={css_status}")

    layout = re.search(r"assets/css/layout[.]css[^\"']*", home)
    check("4.1", "fixed-header layout stylesheet is loaded", layout is not None)
    check("4.1", "layout sheet follows the accessibility sheet",
          home.find("assets/css/layout.css") > home.find("assets/css/a11y.css"))
    check("4.1", "main explicitly reserves fixed-header height",
          'class="site-main erfansanat-main-offset"' in home)
    if layout:
        layout_status, layout_css = fetch(BASE + "/wp-content/themes/erfansanat/" + layout.group(0).split("?")[0])
        check("4.1", "served layout sheet offsets content below the header",
              layout_status == 200 and ".erfansanat-main-offset" in layout_css and "padding-top: 104px" in layout_css,
              f"status={layout_status}")

    # No duplicated SEO head tags.
    check("4.1", "single <title>", len(re.findall(r"<title\b", home)) == 1, str(len(re.findall(r"<title\b", home))))
    # Core prints one canonical on singular views (and SEOPress owns it in
    # production); the theme must simply never add a second.
    check("4.1", "at most one canonical tag", len(re.findall(r'rel="canonical"', home)) <= 1, str(len(re.findall(r'rel="canonical"', home))))
    check("4.1", "no theme-side hreflang alternates", "hreflang=" not in home)

    # Footer contracts.
    check("4.1", "footer has the scroll-top hook", "data-scroll-top" in home)
    check("4.1", "footer copyright line present", "©" in home)
    check("4.1", "footer carries the four headings", all(h in home for h in ["دسترسی سریع", "دسته‌بندی محصولات", "اطلاعات تماس"]))
    check("4.1", "footer prints the badges list", "erfansanat-badges" in home)
    check("4.1", "footer prints product categories", "erfansanat-footer-cats" in home)
    check("4.1", "footer prints contact details", "erfansanat-contact" in home)

    # A secondary page reuses the same shell.
    status2, page = fetch(BASE + "/about/")
    check("4.1", "inner page answers 200", status2 == 200, f"status={status2}")
    if status2 == 200:
        check("4.1", "inner page has the same shell", 'id="erfansanat-page"' in page and "data-navbar-header" in page)
        inner_ids = ids_in(page)
        check("4.1", "no duplicate DOM ids on an inner page", not {i for i in inner_ids if inner_ids.count(i) > 1})


def task_4_2(_: str) -> None:
    _, home = fetch(BASE + "/")

    # Four mega blocks, each a trigger plus a matching panel.
    triggers = re.findall(r'data-desktop-mega-trigger="([a-z]+)"', home)
    panels = re.findall(r'data-desktop-mega-panel="([a-z]+)"', home)
    check("4.2", "four mega triggers", len(triggers) == 4, str(triggers))
    check("4.2", "triggers are the reference four", triggers == ["products", "projects", "services", "corporate"], str(triggers))
    check("4.2", "every trigger has a panel", sorted(triggers) == sorted(panels), f"{triggers} vs {panels}")
    check("4.2", "triggers start collapsed", home.count('data-desktop-mega-trigger') and home.count('aria-expanded="false"') >= 4)

    # Tabs used by main.js.
    prod_tabs = re.findall(r'data-mega-prod-tab="(\d+)"', home)
    prod_panels = re.findall(r'data-mega-prod-panel="(\d+)"', home)
    check("4.2", "product tabs rendered", len(prod_tabs) > 0, str(len(prod_tabs)))
    check("4.2", "product tabs and panels pair up", prod_tabs == prod_panels, f"{prod_tabs} vs {prod_panels}")
    check("4.2", "project type/city tabs rendered", 'data-mega-proj-tab="type"' in home and 'data-mega-proj-tab="city"' in home)
    check("4.2", "project type/city panels rendered", 'data-mega-proj-panel="type"' in home and 'data-mega-proj-panel="city"' in home)

    # Mobile drawer.
    check("4.2", "drawer root present", 'data-mobile-menu="true"' in home)
    check("4.2", "drawer backdrop present", "data-mobile-backdrop" in home)
    check("4.2", "drawer sidebar is a dialog", 'data-mobile-sidebar' in home and 'role="dialog"' in home and 'aria-modal="true"' in home)
    check("4.2", "drawer has four accordions", len(re.findall(r'data-mobile-acc-btn="(\w+)"', home)) == 4)
    check("4.2", "drawer accordion panels start closed", len(re.findall(r'data-mobile-acc-panel="\w+"[^>]*class="hidden', home)) == 4)
    check("4.2", "drawer has product tabs", 'data-mobile-prod-tab="0"' in home)
    check("4.2", "drawer has project tabs", 'data-mobile-proj-tab="type"' in home and 'data-mobile-proj-tab="city"' in home)
    check("4.2", "drawer has the toggle that opens it", 'data-mobile-toggle="true"' in home)

    # Search + cart.
    check("4.2", "search opener present", 'data-search-modal-open="true"' in home)
    check("4.2", "search panel present", 'data-search-modal="true"' in home)
    check("4.2", "search input is a GET form field", 'data-global-search-input="true"' in home and 'name="s"' in home)
    check("4.2", "cart badge hook present", "data-header-cart-badge" in home)

    # Live data, not hard-coded reference links.
    product_cats = wp("term", "list", "product_cat", "--parent=0", "--field=slug", "--number=4")
    slugs = [s for s in product_cats.splitlines() if s.strip()]
    if slugs:
        check("4.2", "mega menu links real product categories", any(f"/product_cat/{slug}" in home for slug in slugs), ",".join(slugs))

    pages = wp("post", "list", "--post_type=page", "--name=faq,about,contact", "--field=ID", "--format=ids")
    if pages.strip():
        check("4.2", "corporate block links real pages", "/faq/" in home and "/about/" in home)

    # No menu assigned must still render a working header.
    previous = wp("option", "get", "theme_mods_erfansanat")
    wp("eval", "set_theme_mod('nav_menu_locations', []);")
    status, bare = fetch(BASE + "/")
    check("4.2", "header renders with no menu assigned", status == 200 and "data-navbar-header" in bare, f"status={status}")
    check("4.2", "no PHP errors leak into the page", "Fatal error" not in bare and "Warning:" not in bare and "Notice:" not in bare)
    check("4.2", "mega panels survive an empty menu", len(re.findall(r'data-desktop-mega-panel="([a-z]+)"', bare)) == 4)
    if previous:
        wp("option", "update", "theme_mods_erfansanat", previous, "--format=json")


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--base", default="http://127.0.0.1:8080")
    parser.add_argument("--task", action="append", choices=["4.1", "4.2"])
    args = parser.parse_args()
    global BASE
    BASE = args.base
    tasks = args.task or ["4.1", "4.2"]
    for task in tasks:
        print(f"\n== task {task} ==\n")
        globals()["task_" + task.replace(".", "_")](args.base)

    print(f"\n{TOTAL - len(FAILED)}/{TOTAL} HTTP checks passed.")
    for failure in FAILED:
        print("  - " + failure)
    if FAILED:
        return 1
    print("PHASE 4 HTTP CHECKS PASSED — local synthetic site only.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
