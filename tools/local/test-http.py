#!/usr/bin/env python3
"""HTTP smoke tests for the running local WordPress preview (port 8080)."""
from __future__ import annotations
import json
import re
import subprocess
import sys
import tempfile
import urllib.parse
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
BASE = "http://127.0.0.1:8080"
creds = json.loads((ROOT / ".local/credentials.json").read_text())
failures: list[str] = []


def curl(path: str, *args: str, jar: str | None = None, host: str | None = None) -> tuple[int, str, str]:
    cmd = ["curl", "-sS", "-L", "--max-time", "40", "-w", "\n%{http_code} %{url_effective}"]
    if jar:
        cmd += ["-b", jar, "-c", jar]
    if host:
        cmd += ["-H", f"Host: {host}", "-H", "X-Forwarded-Proto: https"]
    cmd += list(args)
    cmd.append(BASE + path if path.startswith("/") else path)
    result = subprocess.run(cmd, capture_output=True, text=True)
    if result.returncode != 0:
        return 0, "", result.stderr
    body, _, tail = result.stdout.rpartition("\n")
    code, _, url = tail.partition(" ")
    return int(code or 0), url, body


def check(name: str, condition: bool, detail: str = "") -> None:
    print(("PASS" if condition else "FAIL") + ": " + name + (f" ({detail})" if detail and not condition else ""))
    if not condition:
        failures.append(name)


public_paths = [
    "/", "/shop/", "/product/artam-light-tree/", "/product_cat/strings-and-lighting-decorations/",
    "/projects/", "/projects/ahvaz-semi-pyramid-light/", "/services/", "/services/smartelement/",
    "/blog/", "/blog/led-wall-washer-guide/", "/cart/", "/my-account/", "/wp-login.php",
]
for path in public_paths:
    code, url, body = curl(path)
    check(f"GET {path}", code == 200 and 'dir="rtl"' in body, f"code={code} url={url}")

for path in ["/wp-content/db.php", "/wp-config.php", "/../wp-config.php", "/wp-content/uploads/evil.php"]:
    code, _, _ = curl(path)
    check(f"blocked {path}", code == 404, f"code={code}")

code, _, body = curl("/wp-json/wp/v2/types")
types = json.loads(body) if code == 200 else {}
check("REST exposes services/projects/product", all(k in types for k in ["services", "projects", "product"]))

code, _, body = curl("/", host="8080-sandbox.e2b.app")
check("preview host + HTTPS forwarding", code == 200 and "https://8080-sandbox.e2b.app" in body, f"code={code}")

with tempfile.TemporaryDirectory() as tmp:
    jar = str(Path(tmp) / "cookies.txt")
    curl("/wp-login.php", jar=jar)
    data = urllib.parse.urlencode({
        "log": creds["admin_user"], "pwd": creds["admin_password"], "wp-submit": "Log In",
        "redirect_to": BASE + "/wp-admin/", "testcookie": "1",
    })
    code, url, body = curl("/wp-login.php", "--data", data, jar=jar)
    check("admin login reaches dashboard", code == 200 and "/wp-admin/" in url and "dashboard" in body.lower(), f"url={url}")
    for path in ["/wp-admin/edit.php?post_type=product", "/wp-admin/edit.php?post_type=projects", "/wp-admin/edit.php?post_type=services", "/wp-admin/admin.php?page=wc-settings"]:
        code, url, body = curl(path, jar=jar)
        check(f"admin {path}", code == 200 and "wp-login.php" not in url, f"url={url}")
    # Synthetic cart/checkout flow.
    pid = subprocess.check_output(["tools/wp", f"--path={ROOT}/.local/wordpress", "eval", 'echo wc_get_product_id_by_sku("ES-FIX-002");'], cwd=ROOT, text=True).strip()
    code, _, _ = curl(f"/?add-to-cart={pid}", jar=jar)
    code, url, body = curl("/checkout/", jar=jar)
    check("checkout after add-to-cart", code == 200 and "پرداخت" in body and "/checkout/" in url, f"url={url}")

if failures:
    print(f"\n{len(failures)} HTTP checks failed: {failures}", file=sys.stderr)
    sys.exit(1)
print("\nAll local HTTP smoke checks passed.")
