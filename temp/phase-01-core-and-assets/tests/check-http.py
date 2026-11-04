#!/usr/bin/env python3
"""HTTP acceptance checks for Phase 1 of the Erfan Sanat theme.

The PHP-Wasm development server blocks a request that originates inside its own
PHP process, so every HTTP-level condition is verified here with curl instead of
with WP_UnitTestCase. The script targets the isolated local site only and never
touches a remote host.

Usage:
    python3 temp/phase-01-core-and-assets/tests/check-http.py [--task 1.1] [--base URL]
"""

from __future__ import annotations

import argparse
import json
import re
import subprocess
import sys
import urllib.parse
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]
WP_ROOT = ROOT / ".local/wordpress"
DEBUG_LOG = ROOT / ".local/data/debug.log"
CREDENTIALS = ROOT / ".local/credentials.json"
ASSETS = ROOT / "erfansanat/assets"

# Offline-only notices caused by WP_HTTP_BLOCK_EXTERNAL inside the sandbox.
OFFLINE_NOTICE = re.compile(
    r"translations_api\(\)|wp_version_check\(\)|wp_update_themes\(\)|"
    r"_load_textdomain_just_in_time|wp_check_php_version|"
    r"wp\.org|WordPress\.org"
)

failures: list[str] = []
checked = 0
current_task = ""


def check(task: str, label: str, passed: bool, detail: str = "") -> None:
    """Record one assertion so every task reports a comparable tally."""
    global checked
    if task != current_task:
        print(f"\n== task {task} ==")
        globals()["current_task"] = task
    checked += 1
    mark = "PASS" if passed else "FAIL"
    suffix = f" — {detail}" if detail and not passed else ""
    print(f"[{mark}] {label}{suffix}")
    if not passed:
        failures.append(f"{task}: {label}{suffix}")


def curl_raw(path: str, base: str, jar: Path | None = None, data: str | None = None) -> tuple[int, str, bytes]:
    """Return (status, headers, raw body bytes) for one local request."""
    command = ["curl", "-sS", "-m", "60", "-D", "-", "--compressed"]
    if jar is not None:
        command += ["-b", str(jar), "-c", str(jar)]
    command += ["-o", "-"]
    if data is not None:
        command += ["--data", data]
    command.append(base + path)
    result = subprocess.run(command, capture_output=True, check=False)
    raw = result.stdout
    head_bytes, _, body = raw.partition(b"\r\n\r\n")
    if not body and b"\n\n" in raw:
        head_bytes, _, body = raw.partition(b"\n\n")
    head = head_bytes.decode("utf-8", errors="replace")
    statuses = re.findall(r"^HTTP/\S+ (\d{3})", head, re.MULTILINE)
    status = int(statuses[-1]) if statuses else 0
    return status, head, body


def curl(path: str, base: str, jar: Path | None = None, data: str | None = None) -> tuple[int, str, str]:
    """Text version of curl_raw for markup and stylesheet assertions."""
    status, head, body = curl_raw(path, base, jar=jar, data=data)
    return status, head, body.decode("utf-8", errors="replace")


def log_size() -> int:
    """Current debug log length in lines."""
    if not DEBUG_LOG.exists():
        return 0
    return len(DEBUG_LOG.read_text(errors="replace").splitlines())


def log_growth(before: int) -> list[str]:
    """New debug log lines that are not known offline-only notices."""
    if not DEBUG_LOG.exists():
        return []
    lines = DEBUG_LOG.read_text(errors="replace").splitlines()[before:]
    return [line for line in lines if line.strip() and not OFFLINE_NOTICE.search(line)]


def php_errors(body: str) -> list[str]:
    """PHP diagnostics accidentally printed into the page."""
    patterns = [
        r"<b>Fatal error</b>",
        r"<b>Parse error</b>",
        r"<b>Warning</b>",
        r"<b>Deprecated</b>",
        r"<b>Notice</b>",
        r"Uncaught (?:Error|Exception|TypeError)",
        r"There has been a critical error",
    ]
    return [pattern for pattern in patterns if re.search(pattern, body)]


def head_of(body: str) -> str:
    """Only the document head, where enqueued assets are printed."""
    return body.split("</head>")[0]


def asset_links(head: str) -> list[str]:
    """Every stylesheet/script URL printed in the head."""
    tags = re.findall(r"<(?:link|script)\b[^>]*>", head)
    urls = []
    for tag in tags:
        match = re.search(r"(?:href|src)=['\"]([^'\"]+)['\"]", tag)
        if match:
            urls.append(match.group(1))
    return urls


def task_1_1(base: str) -> None:
    """Phase 1.1: activation is healthy, RTL baseline is correct, source intact."""
    before = log_size()
    pages = {"/": 200, "/shop/": 200, "/cart/": 200}
    bodies: dict[str, str] = {}
    for path, expected in pages.items():
        status, _, body = curl(path, base)
        bodies[path] = body
        check("1.1", f"GET {path} returns {expected}", status == expected, f"got {status}")
        check("1.1", f"GET {path} has no PHP diagnostic", not php_errors(body), ", ".join(php_errors(body)))

    status, _, body = curl("/erfansanat-missing-page-check/", base)
    check("1.1", "unknown URL answers 404", status == 404, f"got {status}")

    home = bodies["/"]
    check("1.1", "html element is RTL", 'dir="rtl"' in home)
    check("1.1", "html element is fa-IR", 'lang="fa-IR"' in home)
    check("1.1", "exactly one document title", len(re.findall(r"<title[ >]", head_of(home))) == 1)
    check("1.1", "viewport meta is present", 'name="viewport"' in head_of(home))
    check("1.1", "wp_head output precedes body", home.find("</head>") < home.find("<body"))

    new_lines = log_growth(before)
    check(
        "1.1",
        "requests add no warning to the debug log",
        not new_lines,
        "; ".join(new_lines[:3]),
    )

    diff = subprocess.run(
        ["git", "diff", "--exit-code", "--", "static-mirror"],
        cwd=ROOT,
        capture_output=True,
        text=True,
        check=False,
    )
    check("1.1", "static reference is unchanged", diff.returncode == 0)


def task_1_2(base: str) -> None:
    """Phase 1.2: block, global-style and emoji output is gone from the front end."""
    before = log_size()
    status, _, home = curl("/", base)
    check("1.2", "home page still renders", status == 200, f"got {status}")
    head = head_of(home)

    check("1.2", "no wp-block-library stylesheet", "wp-block-library" not in home)
    check("1.2", "no block-support inline styles", "wp-block-support" not in home)
    check("1.2", "no global-styles stylesheet", "global-styles" not in home)
    check("1.2", "no classic-theme-styles", "classic-theme-styles" not in home)
    check("1.2", "no emoji detection script", "wp-emoji" not in home)
    check("1.2", "no emoji stylesheet", "emoji.css" not in head and "wp-emoji" not in home)
    check("1.2", "no emoji CDN hint", "s.w.org/images/core/emoji" not in home)
    check("1.2", "no oEmbed host script", "wp-embed.min.js" not in home)
    check("1.2", "no oEmbed discovery tag", "json+oembed" not in home and "xml+oembed" not in home)
    check("1.2", "no generator meta", "name=\"generator\"" not in home and "name='generator'" not in home)
    check("1.2", "no RSD link", "rel=\"EditURI\"" not in head and "rel='EditURI'" not in head)
    check("1.2", "no shortlink tag", "shortlink" not in head)
    check("1.2", "no REST discovery link", "api.w.org" not in home)
    check("1.2", "no WordPress core version strings", not re.search(r"\?ver=6\.", head))
    check("1.2", "canonical link still printed", "rel=\"canonical\"" in home or "rel='canonical'" in home)
    check("1.2", "robots meta still printed", "name=\"robots\"" in head or "name='robots'" in head)
    check("1.2", "feed autodiscovery still printed", "application/rss+xml" in head)

    shop_status, _, shop = curl("/shop/", base)
    check("1.2", "shop archive still renders", shop_status == 200, f"got {shop_status}")
    check("1.2", "WooCommerce front-end assets survive clean-up", "woocommerce" in shop and "woocommerce-js" in shop)
    check("1.2", "WooCommerce block stylesheet is kept", "wc-blocks" in shop)
    check("1.2", "shop keeps add-to-cart behaviour", "add-to-cart" in shop or "add_to_cart" in shop)
    check("1.2", "shop renders product cards", 'class="product' in shop or "woocommerce-loop-product" in shop)

    cart_status, _, cart = curl("/cart/", base)
    check("1.2", "cart page still renders with WooCommerce script", cart_status == 200 and "woocommerce" in cart, f"status={cart_status}")

    product_url = first_product_url(shop)
    if product_url:
        status, _, product = curl(product_url.replace(base, ""), base)
        check("1.2", "single product page renders", status == 200, f"{product_url} status={status}")
        check("1.2", "variation scripts kept on single product", "wc-single-product" in product or "variations" in product)
    else:
        check("1.2", "single product page renders", False, "no product link on the shop archive")

    new_lines = log_growth(before)
    check("1.2", "clean-up adds no warning to the debug log", not new_lines, "; ".join(new_lines[:3]))

    check_admin_editor(base, "1.2")


def first_product_url(shop: str) -> str:
    """Absolute URL of the first product linked from the first product card."""
    match = re.search(r'class="[^"]*woocommerce-LoopProduct-link[^"]*"[^>]*href="([^"]+)"', shop)
    if not match:
        match = re.search(r'href="(https?://[^"]+/product/[^"]+)"', shop)
    return match.group(1) if match else ""


def check_admin_editor(base: str, task: str) -> None:
    """The block editor and media screens must keep working for administrators."""
    if not CREDENTIALS.exists():
        check(task, "admin editor check skipped (no local credentials)", False, str(CREDENTIALS))
        return
    credentials = json.loads(CREDENTIALS.read_text())
    jar = ROOT / ".cache" / "phase01-admin.jar"
    jar.parent.mkdir(parents=True, exist_ok=True)
    curl("/wp-login.php", base, jar=jar)
    data = urllib.parse.urlencode(
        {
            "log": credentials["admin_user"],
            "pwd": credentials["admin_password"],
            "wp-submit": "ورود",
            "redirect_to": base + "/wp-admin/",
            "testcookie": "1",
        }
    )
    status, headers, body = curl("/wp-login.php", base, jar=jar, data=data)
    # A successful login answers 302 with a Location header; a failed one re-renders the form.
    redirected = status == 302 and "wp-admin" in headers
    landed = status == 200 and "/wp-admin/" in body
    check(task, "administrator can sign in", redirected or landed, f"got {status}")

    for path, needle, label in [
        ("/wp-admin/post-new.php?post_type=page", "wp-block-editor", "block editor loads for pages"),
        ("/wp-admin/post-new.php?post_type=product", "wp-block-editor", "block editor loads for products"),
        ("/wp-admin/admin.php?page=wc-settings", "wc-settings", "WooCommerce settings screen loads"),
        ("/wp-admin/upload.php", "media", "media library loads"),
    ]:
        status, _, body = curl(path, base, jar=jar)
        check(task, label, status == 200 and needle in body, f"status={status}")


def asset_meta(url: str, base: str) -> tuple[int, str, int]:
    """Status, content type and byte size for one asset URL."""
    status, headers, body = curl_raw(url.replace(base, ""), base)
    kind = ""
    for line in headers.splitlines():
        if line.lower().startswith("content-type:"):
            kind = line.split(":", 1)[1].strip()
            break
    return status, kind, len(body)


def task_1_3(base: str) -> None:
    """Phase 1.3: transferred assets answer 200 and stay byte-identical."""
    status, _, home = curl("/", base)
    check("1.3", "home page still renders", status == 200, f"got {status}")
    head = head_of(home)

    style_tag = re.search(r"<link[^>]+id=['\"]erfansanat-styles-css['\"][^>]*>", head)
    script_tag = re.search(r"<script[^>]+id=['\"]erfansanat-main-js['\"][^>]*>", head + home)
    check("1.3", "theme stylesheet is enqueued", bool(style_tag))
    check("1.3", "behaviour script is enqueued", bool(script_tag))

    style_url = re.search(r"href=['\"]([^'\"]+)['\"]", style_tag.group(0)).group(1) if style_tag else ""
    script_url = re.search(r"src=['\"]([^'\"]+)['\"]", script_tag.group(0)).group(1) if script_tag else ""

    for label, url, local, content_type in [
        ("styles.css", style_url, "css/styles.css", "text/css"),
        ("main.js", script_url, "js/main.js", "javascript"),
    ]:
        if not url:
            check("1.3", f"{label} URL is printed", False)
            continue
        status, kind, size = asset_meta(url, base)
        expected_size = (ASSETS / local).stat().st_size
        check("1.3", f"{label} returns 200", status == 200, f"status={status} url={url}")
        check("1.3", f"{label} serves the full file", size == expected_size, f"{size} != {expected_size}")
        check("1.3", f"{label} has a CSS/JS content type", content_type in kind.lower(), kind)
        version = re.search(r"[?&]ver=([^&'\"]+)", url)
        expected = str(int((ASSETS / local).stat().st_mtime))
        check(
            "1.3",
            f"{label} version equals filemtime",
            bool(version) and version.group(1) == expected,
            f"ver={version.group(1) if version else 'none'} expected={expected}",
        )

    fonts = ["vazirmatn-arabic.woff2", "vazirmatn-latin.woff2", "vazirmatn-latin-ext.woff2"]
    for font in fonts:
        url = f"/wp-content/themes/erfansanat/assets/fonts/{font}"
        status, kind, size = asset_meta(url, base)
        expected_size = (ASSETS / "fonts" / font).stat().st_size
        check("1.3", f"font {font} returns 200", status == 200 and size == expected_size, f"status={status} size={size}")
        check("1.3", f"font {font} is WOFF2", "woff2" in kind.lower(), kind)

    preloads = re.findall(r"<link rel=\"preload\"[^>]+>", head)
    font_preloads = [tag for tag in preloads if 'as="font"' in tag]
    check("1.3", "font subsets are preloaded", len(font_preloads) == 2, str(len(font_preloads)))
    check("1.3", "font preloads declare crossorigin", all("crossorigin" in tag for tag in font_preloads))
    check("1.3", "no media or video preload on the home page", not [tag for tag in preloads if "as=\"video\"" in tag or "as=\"image\"" in tag])

    css_status, _, css = curl(style_url.replace(base, ""), base) if style_url else (0, "", "")
    check("1.3", "stylesheet keeps its font-face rules", "@font-face" in css)
    check("1.3", "stylesheet keeps relative font paths", "../fonts/vazirmatn-arabic.woff2" in css)
    check("1.3", "stylesheet still carries the reference classes", ".hero-vignette" in css and ".card-light" in css)

    js_status, _, js = curl(script_url.replace(base, ""), base) if script_url else (0, "", "")
    check("1.3", "script keeps the reference behaviour engine", "initNavbar" in js and "DOMContentLoaded" in js)

    for relative in ("images/hero.jpg", "images/ese-logo.svg", "video/hero-video.mp4", "images/pexels/pexels-19112845.jpg"):
        url = f"/wp-content/themes/erfansanat/assets/{relative}"
        status, kind, size = asset_meta(url, base)
        check("1.3", f"{relative} returns 200", status == 200, f"status={status}")
        check("1.3", f"{relative} keeps its full size", size == (ASSETS / relative).stat().st_size, str(size))

    shop_status, _, shop = curl("/shop/", base)
    check("1.3", "shop page does not preload the hero video", "hero-video.mp4" not in shop, f"status={shop_status}")
    # Phase 4 laid the reference hero in, and the reference hero markup carries
    # a muted, looping <video preload="metadata"> for the inline teaser. The
    # Phase 1 contract was about the *asset pipeline*: the video must never be
    # enqueued or preloaded by the theme, and no page outside the hero may
    # reference it. That is what is asserted now.
    check("1.3", "the hero video is never enqueued as an asset",
          "hero-video" not in home.split("</head>")[0], "it appeared in <head>")
    check("1.3", "the hero video is only referenced inside the hero",
          home.count("hero-video.mp4") == 1, str(home.count("hero-video.mp4")))

    check("1.3", "missing asset returns 404", asset_meta("/wp-content/themes/erfansanat/assets/css/missing.css", base)[0] == 404)
    check("1.3", "theme PHP source is never served", asset_meta("/wp-content/themes/erfansanat/functions.php", base)[0] == 404)
    check("1.3", "reference source stays untouched", subprocess.run(["git", "diff", "--exit-code", "--", "static-mirror"], cwd=ROOT, capture_output=True, check=False).returncode == 0)


def main() -> int:
    """Run the requested tasks and return a shell-friendly status."""
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--base", default="http://127.0.0.1:8080")
    parser.add_argument("--task", action="append", choices=["1.1", "1.2", "1.3"])
    args = parser.parse_args()
    tasks = args.task or ["1.1", "1.2", "1.3"]

    if not WP_ROOT.exists():
        print("Local WordPress is missing; run tools/setup-wordpress.sh first.", file=sys.stderr)
        return 2
    status, _, _ = curl("/", args.base)
    if status == 0:
        print(f"No local preview on {args.base}; start it with tools/preview-wordpress.sh.", file=sys.stderr)
        return 2

    for task in tasks:
        globals()[f"task_{task.replace('.', '_')}"](args.base)

    print(f"\n{checked - len(failures)}/{checked} HTTP checks passed.")
    if failures:
        for failure in failures:
            print(f"  - {failure}")
        return 1
    print("PHASE 1 HTTP CHECKS PASSED — local synthetic site only.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
