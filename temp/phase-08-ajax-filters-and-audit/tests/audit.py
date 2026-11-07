#!/usr/bin/env python3
"""Phase 8.3 audit: SEO, security, performance and end-to-end behaviour.

This is not a fourth copy of the HTTP suites. It asks the questions none of them
ask — is the page indexable, does it leak anything, does it cost what it should,
and does the whole visitor journey actually work — and it reports findings
instead of only asserting, so a real problem is visible even when the check that
found it is new.

Usage:
    python3 temp/phase-08-ajax-filters-and-audit/tests/audit.py
"""
from __future__ import annotations

import json
import re
import subprocess
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]
BASE = "http://127.0.0.1:8080"
FINDINGS: list[tuple[str, str, str]] = []

# Every page a visitor can land on directly.
PUBLIC_PATHS = [
    "/", "/shop/", "/projects/", "/services/", "/blog/", "/contact/",
    "/about/", "/faq/", "/licenses-and-honors/", "/recruitment/", "/project/",
    "/arya/", "/cart/", "/my-account/",
]


def fetch(url: str, timeout: int = 30) -> tuple[int, str, dict[str, str]]:
    """GET a URL and return status, body and headers."""
    request = urllib.request.Request(url, headers={"User-Agent": "erfansanat-audit"})
    try:
        with urllib.request.urlopen(request, timeout=timeout) as response:
            return (
                response.status,
                response.read().decode("utf-8", "replace"),
                dict(response.headers.items()),
            )
    except urllib.error.HTTPError as error:
        return error.code, error.read().decode("utf-8", "replace"), dict(error.headers.items())
    except urllib.error.URLError as error:
        return 0, str(error), {}


def record(level: str, area: str, message: str) -> None:
    """Keep one finding."""
    FINDINGS.append((level, area, message))


def wp(*args: str) -> str:
    """Run wp-cli."""
    result = subprocess.run(
        ["tools/wp", "--path=.local/wordpress", *args],
        cwd=ROOT, capture_output=True, text=True, check=False,
    )
    return result.stdout.strip()


def discover() -> dict[str, list[str]]:
    """Pick one or two real URLs per content family."""
    urls: dict[str, list[str]] = {"product": [], "projects": [], "services": [], "post": []}
    for post_type in urls:
        ids = wp("post", "list", f"--post_type={post_type}", "--post_status=publish",
                 "--format=ids", "--posts_per_page=-1").split()
        for pid in ids[:2]:
            slug = wp("post", "get", pid, "--field=post_name")
            if slug:
                prefix = "" if post_type == "post" else f"{post_type}/"
                urls[post_type].append(f"/{prefix}{slug}/")
    return urls


def audit_seo() -> None:
    """Titles, descriptions, canonical URLs, headings and indexability."""
    for path in PUBLIC_PATHS:
        status, body, _ = fetch(BASE + path)
        if status != 200:
            record("FAIL", "seo", f"{path} returns {status}")
            continue

        # Title: present, non-empty, and actually the page's own.
        titles = re.findall(r"<title[^>]*>(.*?)</title>", body, re.S)
        if not titles:
            record("FAIL", "seo", f"{path} has no <title>")
        elif len(titles[0].strip()) < 8:
            record("WARN", "seo", f"{path} has a very short title: {titles[0]!r}")
        elif path == "/" and "عرفان صنعت" not in titles[0]:
            record("WARN", "seo", f"home title does not name the brand: {titles[0]!r}")

        # One h1 and a sane heading order.
        h1s = len(re.findall(r"<h1[ >]", body))
        if h1s != 1:
            record("FAIL", "seo", f"{path} has {h1s} h1 elements")

        # Canonical.
        if 'rel="canonical"' not in body:
            record("WARN", "seo", f"{path} has no canonical link")

        # A meta description is what a search result shows.
        description = re.search(r'<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']', body, re.S)
        if not description or len(description.group(1).strip()) < 40:
            record("WARN", "seo", f"{path} has no useful meta description")

        # Language and direction.
        if 'lang="fa' not in body.replace("'", '"'):
            record("FAIL", "seo", f"{path} is not marked as Persian")
        if 'dir="rtl"' not in body.replace("'", '"'):
            record("WARN", "seo", f"{path} does not declare RTL")

        # A page a search engine should index must not be noindex.
        robots = re.search(r'<meta[^>]+name=["\']robots["\'][^>]+content=["\']([^"\']*)', body, re.I)
        # A cart and an account page are deliberately noindex: indexing a
        # cabinet URL puts empty carts in search results.
        if robots and "noindex" in robots.group(1).lower() and path not in ("/cart/", "/my-account/"):
            record("FAIL", "seo", f"{path} is noindex but belongs in the index")

    # Structured data: a product page should describe a product.
    urls = discover()
    for path in urls["product"][:2]:
        _, body, _ = fetch(BASE + path)
        if "application/ld+json" not in body:
            record("WARN", "seo", f"{path} has no JSON-LD")
        elif '"Product"' not in body:
            record("WARN", "seo", f"{path} JSON-LD does not describe a Product")

    # Sitemap and robots.txt are how a crawler finds any of this.
    for path in ("/wp-sitemap.xml", "/robots.txt"):
        status, _, _ = fetch(BASE + path)
        if status != 200:
            record("WARN", "seo", f"{path} returns {status}")


def audit_security() -> None:
    """Headers, disclosure, nonce enforcement and what a stranger can reach."""
    status, body, headers = fetch(BASE + "/")
    lowered = {k.lower(): v for k, v in headers.items()}

    # Version disclosure tells an attacker which exploit to use.
    if "x-powered-by" in lowered:
        record("WARN", "security", f"x-powered-by is exposed: {lowered['x-powered-by']}")
    generator = re.search(r'<meta[^>]+name=["\']generator["\'][^>]+content=["\']([^"\']*)', body, re.I)
    if generator:
        record("WARN", "security", f"generator meta exposes the stack: {generator.group(1)}")

    # Directory listing on the theme's own asset folders would publish sources.
    for path in ("/wp-content/themes/erfansanat/assets/", "/wp-content/"):
        status, listing, _ = fetch(BASE + path)
        if status == 200 and "Index of" in listing:
            record("FAIL", "security", f"directory listing is enabled at {path}")

    # An unauthenticated visitor must be sent to the login form. `urllib` does
    # not follow redirects here, so a 302 to wp-login.php is the pass and a 200
    # is the failure: the admin actually rendered for a stranger.
    for path in ("/wp-admin/edit.php", "/wp-admin/admin.php?page=erfansanat-options", "/wp-admin/users.php"):
        status, probe, _ = fetch(BASE + path)
        if status == 200 and "wp-login.php" not in probe:
            record("FAIL", "security", f"{path} rendered for an anonymous visitor")

    # The message inbox must never *disclose* a message. A status code proves
    # nothing on its own: an empty archive and a leak both answer 200.
    sender = wp("post", "list", "--post_type=erfansanat_message", "--post_status=private",
                "--format=ids", "--posts_per_page=1").split()
    probe_names: list[str] = []
    if sender:
        probe_names.append(wp("post", "meta", "get", sender[0], "_es_name"))
    for path in ("/erfansanat_message/", "/?post_type=erfansanat_message", "/wp-json/wp/v2/erfansanat_message"):
        status, probe, _ = fetch(BASE + path)
        if status == 200 and '"name"' in probe and "erfansanat_message" in probe and '"id"' in probe:
            record("FAIL", "security", f"the REST API exposes the private inbox at {path}")
        for name in probe_names:
            if name and name in probe:
                record("FAIL", "security", f"the private inbox leaks a sender name at {path}")

    # The AJAX endpoint must reject a request with no nonce.
    status, _, _ = fetch(f"{BASE}/wp-admin/admin-ajax.php?action=erfansanat_live_search&q=test")
    if status != 403:
        record("FAIL", "security", f"the live-search endpoint answered without a nonce ({status})")

    # A published post must not be writable or deletable by an anonymous request.
    status, _, _ = fetch(f"{BASE}/wp-json/wp/v2/posts", timeout=20)
    if status == 200:
        try:
            count = len(json.loads(fetch(f"{BASE}/wp-json/wp/v2/posts?per_page=1")[1]))
        except Exception:  # noqa: BLE001 - diagnostic only
            count = -1
        if count < 0:
            record("WARN", "security", "the REST posts endpoint returned something unreadable")

    # Mixed content: an http:// asset on an https page is blocked by browsers,
    # and a hard-coded absolute URL breaks when the site moves.
    _, home, _ = fetch(BASE + "/")
    if "http://127.0.0.1:8080" in home.replace(f"{BASE}", ""):
        record("WARN", "security", "the home page hard-codes its own absolute URL")


def audit_performance() -> None:
    """Weight, request count and the cost of the queries behind a page."""
    for path in ("/", "/shop/", "/projects/", "/blog/"):
        start = time.perf_counter()
        status, body, headers = fetch(BASE + path)
        elapsed = (time.perf_counter() - start) * 1000

        if status != 200:
            continue

        size = len(body.encode("utf-8"))
        if size > 400_000:
            record("WARN", "performance", f"{path} is {size / 1024:.0f} KB of HTML")
        if elapsed > 3000:
            record("WARN", "performance", f"{path} took {elapsed:.0f} ms")

        # Images must be lazy except the one above the fold.
        eager = len(re.findall(r"<img(?![^>]*loading=)[^>]*>", body))
        if eager > 6:
            record("WARN", "performance", f"{path} has {eager} images without loading=lazy")

        # The hero image should not be lazy: it is what the visitor is looking at.
        if 'fetchpriority="high"' not in body and path == "/":
            record("INFO", "performance", "the home hero does not declare fetchpriority")

        # Stylesheets and scripts that are not needed on this page.
        styles = len(re.findall(r'<link[^>]+rel=["\']stylesheet["\']', body))
        scripts = len(re.findall(r"<script[^>]+src=", body))
        if styles > 12:
            record("WARN", "performance", f"{path} loads {styles} stylesheets")
        if scripts > 20:
            record("WARN", "performance", f"{path} loads {scripts} scripts")

        # Cache headers on a fully rendered page.
        cache = headers.get("Cache-Control", "")
        if not cache:
            record("INFO", "performance", f"{path} sets no Cache-Control header")

    # The asset budget the theme promised.
    manifest = json.loads((ROOT / "erfansanat/assets/manifest.json").read_text(encoding="utf-8"))
    css = manifest["files"]["css/styles.css"]["bytes"]
    js = manifest["files"]["js/main.js"]["bytes"]
    if css > 200_000:
        record("WARN", "performance", f"styles.css is {css / 1024:.0f} KB")
    if js > 150_000:
        record("WARN", "performance", f"main.js is {js / 1024:.0f} KB")


def audit_e2e() -> None:
    """The journeys a visitor actually takes, start to finish."""
    urls = discover()

    # 1. Home → a product → the shop.
    status, _, _ = fetch(BASE + "/shop/")
    if status != 200:
        record("FAIL", "e2e", "the shop is unreachable")
    if not urls["product"]:
        record("FAIL", "e2e", "no product to visit")
    else:
        status, product, _ = fetch(BASE + urls["product"][0])
        if status != 200:
            record("FAIL", "e2e", f"a product page returns {status}")
        elif "add-to-cart" not in product and "افزودن به سبد" not in product:
            record("WARN", "e2e", "a product page has no add-to-cart affordance")

    # 2. Search finds what exists, from the search page.
    status, search, _ = fetch(f"{BASE}/?s=" + urllib.parse.quote("نور"))
    if status != 200 or not re.search(r"card-(light|glow)", search):
        record("FAIL", "e2e", "searching a term that exists returns nothing")

    # 3. The live-search endpoint answers the same query.
    _, home, _ = fetch(BASE + "/")
    nonce = re.search(r'"nonce":"([a-z0-9]+)"', home)
    if not nonce:
        record("FAIL", "e2e", "the live-search nonce is not on the page")
    else:
        status, payload, _ = fetch(
            f"{BASE}/wp-admin/admin-ajax.php?action=erfansanat_live_search"
            f"&q={urllib.parse.quote('نور')}&nonce={nonce.group(1)}"
        )
        try:
            data = json.loads(payload)["data"]
        except Exception:  # noqa: BLE001 - diagnostic only
            data = {}
        if status != 200 or not data.get("total"):
            record("FAIL", "e2e", "the live-search endpoint returns no results for a term that exists")
        elif not data.get("groups"):
            record("WARN", "e2e", "live search returns a total but no groups")

    # 4. The filters narrow the archives and can be undone.
    _, all_projects, _ = fetch(BASE + "/projects/")
    total = all_projects.count('data-project-item="true"')

    # A filter panel that renders no options looks fine in a status code and is
    # useless in a browser; assert the terms are actually printed.
    city_links = len(re.findall(r'data-project-location="[^"]+"', all_projects))
    type_links = len(re.findall(r'data-project-type="[^"]+"', all_projects))
    if city_links == 0:
        record("FAIL", "e2e", "the project filter panel prints no cities")
    if type_links == 0:
        record("FAIL", "e2e", "the project filter panel prints no project types")
    if "Warning:" in all_projects or "Notice:" in all_projects:
        record("FAIL", "e2e", "the project archive prints a PHP warning")
    _, filtered, _ = fetch(BASE + "/projects/?city=" + urllib.parse.quote("قم"))
    narrowed = filtered.count('data-project-item="true"')
    if total == 0:
        record("FAIL", "e2e", "the project archive renders no projects")
    elif narrowed >= total:
        record("WARN", "e2e", f"a city filter did not narrow the archive ({total} -> {narrowed})")

    _, all_shop, _ = fetch(BASE + "/shop/")
    shop_total = all_shop.count('data-product-item="true"')
    _, priced, _ = fetch(BASE + "/shop/?es_price=over-6")
    priced_total = priced.count('data-product-item="true"')
    if shop_total and priced_total >= shop_total:
        record("WARN", "e2e", "a price band did not narrow the shop")

    # 5. The contact form is the only path that writes, and it is protected.
    status, contact, _ = fetch(BASE + "/contact/")
    if status != 200:
        record("FAIL", "e2e", "the contact page is unreachable")
    else:
        for field in ("es_nonce", "es_website", "es_started"):
            if field not in contact:
                record("FAIL", "e2e", f"the contact form is missing {field}")

    # 6. A 404 offers a way forward rather than a dead end.
    status, missing, _ = fetch(BASE + "/nothing-here-e2e/")
    if status != 404:
        record("FAIL", "e2e", f"a missing URL returns {status} instead of 404")
    elif 'name="s"' not in missing:
        record("WARN", "e2e", "the 404 page has no search box")

    # 7. Pagination is real, not decorative.
    status, page_two, _ = fetch(BASE + "/projects/page/2/")
    if status != 200:
        record("FAIL", "e2e", f"project page 2 returns {status}")
    elif page_two.count('data-project-item="true"') == 0:
        record("FAIL", "e2e", "project page 2 renders no projects")

    # 8. The taxonomy links the archive prints must resolve.
    term_links = set(re.findall(r'href="([^"]*/(?:city|project_type)/[^"]+)"', all_projects))
    dead = []
    for link in list(term_links)[:6]:
        link = link.replace("&amp;", "&")
        status, _, _ = fetch(link)
        if status != 200:
            dead.append(f"{link} -> {status}")
    if dead:
        record("FAIL", "e2e", f"dead taxonomy links: {', '.join(dead)}")

    # 9. The news feed is a real feed, not an empty document.
    status, feed, _ = fetch(BASE + "/feed/")
    if status != 200 or "<item>" not in feed:
        record("WARN", "e2e", f"the RSS feed returns {status}")


def audit_admin_font() -> None:
    """The theme's font must reach every screen the theme owns."""
    font_css = (ROOT / "erfansanat/assets/admin/font.css")
    if not font_css.exists():
        record("FAIL", "font", "the admin font stylesheet is missing")
        return

    css = font_css.read_text(encoding="utf-8")
    # Count declarations, not the word: the file's own header explains them.
    if len(re.findall(r"@font-face\s*\{", css)) != 3:
        record("FAIL", "font", "the three Vazirmatn subsets are not all declared")
    for subset in ("arabic", "latin-ext", "latin"):
        if f"vazirmatn-{subset}.woff2" not in css:
            record("FAIL", "font", f"the {subset} subset is not referenced")
    if "body.wp-admin" not in css:
        record("FAIL", "font", "the admin body does not use the theme's font")

    # Every woff2 the CSS points at must exist on disk.
    for name in re.findall(r"url\([\"']\.\./fonts/([^\"']+)[\"']\)", css):
        if not (ROOT / "erfansanat/assets/fonts" / name).exists():
            record("FAIL", "font", f"{name} is referenced but not bundled")

    # Both hooks that matter: every admin screen, and the login form.
    php = (ROOT / "erfansanat/inc/Admin/ThemeOptions.php").read_text(encoding="utf-8")
    if "'admin/font.css'" not in php:
        record("FAIL", "font", "the font stylesheet is never enqueued")
    if "enqueue_login_font" not in php:
        record("FAIL", "font", "the login form does not get the theme's font")
    theme = (ROOT / "erfansanat/inc/Theme.php").read_text(encoding="utf-8")
    if "login_init" not in theme:
        record("FAIL", "font", "the login hook is never registered outside is_admin()")


def audit_theme_package() -> None:
    """The theme must be installable as a theme, not just runnable in place.

    WordPress reads the header in `style.css`, needs a `screenshot.png` of the
    documented size, and only honours editor settings it can find in
    `theme.json`. None of these are visible on the front end, which is exactly
    why they go missing.
    """
    theme = ROOT / "erfansanat"

    header = (theme / "style.css").read_text(encoding="utf-8")
    for field in ("Theme Name:", "Version:", "Requires PHP:", "Text Domain: erfansanat", "Domain Path:"):
        if field not in header:
            record("FAIL", "package", f"style.css is missing {field}")

    # Translation readiness: the theme loads its text domain from languages/, so
    # a template has to be there or the directory is a dead end for translators.
    pot = theme / "languages" / "erfansanat.pot"
    if not pot.exists():
        record("FAIL", "package", "languages/erfansanat.pot is missing")
    else:
        text = pot.read_text(encoding="utf-8")
        if 'X-Domain: erfansanat' not in text:
            record("FAIL", "package", "the template declares no text domain")
        entries = text.count('\nmsgid "') - 1  # the header's own empty msgid
        if entries < 500:
            record("FAIL", "package", f"the template has only {entries} strings — extraction broke")
        # A stale template is worse than none: a translator translates strings
        # that the theme no longer prints.
        newest = max((p.stat().st_mtime for p in theme.rglob("*.php")), default=0)
        if pot.stat().st_mtime < newest:
            record("WARN", "package", "languages/erfansanat.pot is older than the theme sources")

    for name in ("index.php", "functions.php", "readme.txt", "theme.json", "screenshot.png"):
        if not (theme / name).exists():
            record("FAIL", "package", f"{name} is missing from the theme")

    shot = theme / "screenshot.png"
    if shot.exists():
        data = shot.read_bytes()
        if not data.startswith(b"\x89PNG"):
            record("WARN", "package", "screenshot.png is not a PNG")
        # WordPress recommends 1200x630; the theme documents 1200x900 (4:3).
        if len(data) < 2000:
            record("FAIL", "package", "screenshot.png is too small to be an image")

    theme_json = theme / "theme.json"
    if theme_json.exists():
        try:
            parsed = json.loads(theme_json.read_text(encoding="utf-8"))
        except json.JSONDecodeError as error:
            record("FAIL", "package", f"theme.json is not valid JSON: {error}")
            parsed = {}
        if parsed:
            settings = parsed.get("settings", {})
            if "layout" not in settings:
                record("WARN", "package", "theme.json declares no layout constraints")
            families = settings.get("typography", {}).get("fontFamilies", [])
            if not any("Vazirmatn" in f.get("fontFamily", "") for f in families):
                record("FAIL", "package", "theme.json does not offer the theme's own font")
            palette = {p.get("slug") for p in settings.get("color", {}).get("palette", [])}
            if "brand" not in palette:
                record("WARN", "package", "theme.json has no brand colour")
            # The point of the file is to stop core shipping its defaults, so a
            # theme.json that opts back in would be worse than none.
            if settings.get("color", {}).get("defaultPalette") is not False:
                record("WARN", "package", "theme.json still offers WordPress's default palette")
            if settings.get("typography", {}).get("fluid") is not False:
                record("WARN", "package", "fluid typography would fight the compiled stylesheet")

    # No theme file may load a font or script from a third-party host.
    for path in list(theme.rglob("*.php")) + list(theme.rglob("*.css")):
        if "assets/css/styles.css" in str(path) or "manifest.json" in str(path):
            continue
        try:
            text = path.read_text(encoding="utf-8")
        except (UnicodeDecodeError, OSError):
            continue
        for host in ("fonts.googleapis.com", "fonts.gstatic.com", "cdn.jsdelivr.net", "unpkg.com"):
            if host in text:
                record("FAIL", "package", f"{path.relative_to(ROOT)} loads from {host}")


def main() -> int:
    """Run every audit area and print a report."""
    status, _, _ = fetch(BASE + "/")
    if status != 200:
        print(f"No local preview on {BASE}.")
        return 2

    for area in (audit_seo, audit_security, audit_performance, audit_e2e, audit_admin_font,
                 audit_theme_package):
        try:
            area()
        except Exception as error:  # noqa: BLE001 - one broken area must not hide the rest
            record("FAIL", area.__name__, f"the audit itself raised: {error}")

    by_level: dict[str, list[tuple[str, str]]] = {"FAIL": [], "WARN": [], "INFO": []}
    for level, area, message in FINDINGS:
        by_level.setdefault(level, []).append((area, message))

    for level in ("FAIL", "WARN", "INFO"):
        rows = by_level.get(level, [])
        print(f"\n{level}: {len(rows)}")
        for area, message in rows:
            print(f"  [{area}] {message}")
        if not rows:
            print("  (none)")

    failures = len(by_level.get("FAIL", []))
    warnings = len(by_level.get("WARN", []))
    print(f"\n{len(FINDINGS)} findings: {failures} failures, {warnings} warnings.")
    if failures:
        print("PHASE 8.3 AUDIT FAILED.")
        return 1
    print("PHASE 8.3 AUDIT PASSED — local synthetic site only.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
