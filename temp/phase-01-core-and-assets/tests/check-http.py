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


def curl(path: str, base: str, jar: Path | None = None, data: str | None = None) -> tuple[int, str, str]:
    """Return (status, headers, body) for one local request."""
    command = ["curl", "-sS", "-m", "30", "-D", "-", "--compressed"]
    if jar is not None:
        command += ["-b", str(jar), "-c", str(jar)]
    command += ["-o", "-"]
    if data is not None:
        command += ["--data", data]
    command.append(base + path)
    result = subprocess.run(command, capture_output=True, text=True, check=False)
    raw = result.stdout
    head, _, body = raw.partition("\r\n\r\n")
    if not body and "\n\n" in raw:
        head, _, body = raw.partition("\n\n")
    statuses = re.findall(r"^HTTP/\S+ (\d{3})", head, re.MULTILINE)
    status = int(statuses[-1]) if statuses else 0
    return status, head, body


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
    urls = asset_links(head)

    check(
        "1.2",
        "no wp-block-library stylesheet",
        not [url for url in urls if "wp-block-library" in url or "wp-block-library" in head],
    )
    check("1.2", "no global-styles stylesheet", "global-styles" not in head)
    check("1.2", "no block-support inline styles", "wp-block-" not in head or "wp-block-library" not in head)
    check("1.2", "no emoji detection script", "wp-emoji" not in home and "emoji-release.min.js" not in home)
    check("1.2", "no emoji stylesheet", "emoji.css" not in head)
    check("1.2", "no oEmbed host script", "wp-embed.min.js" not in home)
    check("1.2", "no generator meta", 'name="generator"' not in head)
    check("1.2", "no RSD link", 'rel="EditURI"' not in head)
    check("1.2", "no shortlink tag", 'rel="shortlink"' not in head)
    check("1.2", "no WordPress version query strings", not re.search(r"\?ver=6\.", head))

    shop_status, _, shop = curl("/shop/", base)
    check("1.2", "shop archive still renders", shop_status == 200, f"got {shop_status}")
    check(
        "1.2",
        "WooCommerce front-end assets survive clean-up",
        "woocommerce" in shop or "wc-" in shop,
    )
    check("1.2", "shop page keeps its add-to-cart markup", 'add_to_cart' in shop or "add-to-cart" in shop)
    check("1.2", "product archive is not blank", "product" in shop.lower())

    new_lines = log_growth(before)
    check("1.2", "clean-up adds no warning to the debug log", not new_lines, "; ".join(new_lines[:3]))

    check_admin_editor(base, "1.2")


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
    status, _, body = curl("/wp-login.php", base, jar=jar, data=data)
    check(task, "administrator can sign in", status == 200 and "/wp-admin/" in body, f"got {status}")

    for path, needle, label in [
        ("/wp-admin/post-new.php?post_type=page", "wp-block-editor", "block editor loads for pages"),
        ("/wp-admin/post-new.php?post_type=product", "wp-block-editor", "block editor loads for products"),
        ("/wp-admin/admin.php?page=wc-settings", "woocommerce", "WooCommerce settings screen loads"),
        ("/wp-admin/upload.php", "wp-list-table", "media library loads"),
    ]:
        status, _, body = curl(path, base, jar=jar)
        check(task, label, status == 200 and needle in body, f"status={status}")


def task_1_3(base: str) -> None:
    """Phase 1.3: transferred assets answer 200 and keep relative font paths."""
    status, _, home = curl("/", base)
    head = head_of(home)
    urls = asset_links(head)
    own = [url for url in urls if "/themes/erfansanat/" in url]
    check("1.3", "theme stylesheet is printed in the head", any(url.endswith(".css") or ".css?" in url for url in own), str(own))
    check("1.3", "theme script is printed in the head", any(".js" in url for url in own), str(own))

    css_url = next((url for url in own if "styles.css" in url), "")
    if css_url:
        status, _, css = curl(css_url.replace(base, ""), base)
        check("1.3", "styles.css returns 200", status == 200, f"status={status} url={css_url}")
        check("1.3", "styles.css keeps its font-face rules", "@font-face" in css)
        check("1.3", "styles.css keeps the relative font path", "../fonts/vazirmatn-arabic.woff2" in css)
        version = re.search(r"[?&]ver=([^&'\"]+)", css_url)
        expected = str(int((ASSETS / "css/styles.css").stat().st_mtime))
        check("1.3", "stylesheet version equals filemtime", bool(version) and version.group(1) == expected, f"ver={version.group(1) if version else 'none'} expected={expected}")
    else:
        check("1.3", "styles.css is enqueued", False, str(own))

    for font in ("vazirmatn-arabic.woff2", "vazirmatn-latin.woff2", "vazirmatn-latin-ext.woff2"):
        path = f"/wp-content/themes/erfansanat/assets/fonts/{font}"
        status, headers, _ = curl(path, base)
        check("1.3", f"font {font} returns 200", status == 200, f"status={status}")
        check("1.3", f"font {font} is served as WOFF2", "font/woff2" in headers.lower() or "application/octet-stream" in headers.lower())

    for name in ("styles.css", "main.js"):
        reference = ROOT / "static-mirror/assets" / ("css/" + name if name.endswith(".css") else "js/" + name)
        copied = ASSETS / ("css/" + name if name.endswith(".css") else "js/" + name)
        check("1.3", f"{name} is byte-identical to the reference", copied.exists() and reference.read_bytes() == copied.read_bytes())

    home_urls = [url for url in urls if "hero-video" in url]
    check("1.3", "home page does not preload the hero video", not home_urls, str(home_urls))
    shop_status, _, shop = curl("/shop/", base)
    check("1.3", "shop page loads no hero video element", "hero-video.mp4" not in shop, f"status={shop_status}")
    video_status, headers, _ = curl("/wp-content/themes/erfansanat/assets/video/hero-video.mp4", base)
    if video_status == 200:
        check("1.3", "hero video is served only on demand", "preload" not in headers.lower())
    else:
        check("1.3", "hero video file is reachable when requested", video_status == 206, f"status={video_status}")

    nonce = re.search(r"<body[^>]*class=\"([^\"]*)\"", home)
    body_classes = nonce.group(1) if nonce else ""
    check("1.3", "body carries the home context class", "home" in body_classes, body_classes)


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
