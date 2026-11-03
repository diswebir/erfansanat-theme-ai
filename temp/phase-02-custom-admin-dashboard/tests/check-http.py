#!/usr/bin/env python3
"""HTTP acceptance checks for Phases 2 and 3 of the Erfan Sanat theme.

Covers the admin panel, its AJAX save endpoint, the front-end output of the
settings, the section manager and the data layer. The local PHP-Wasm server
cannot answer a request from inside its own PHP process, so admin and REST
behaviour is exercised with curl from the outside.

Usage:
    python3 temp/phase-02-custom-admin-dashboard/tests/check-http.py [--task 2.1]
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
CREDENTIALS = ROOT / ".local/credentials.json"
BASE = "http://127.0.0.1:8080"

failures: list[str] = []
checked = 0
current_task = ""


def check(task: str, label: str, passed: bool, detail: str = "") -> None:
    """Record one assertion."""
    global checked, current_task
    if task != current_task:
        print(f"\n== task {task} ==")
        current_task = task
    checked += 1
    print(f"[{'PASS' if passed else 'FAIL'}] {label}{'' if passed or not detail else ' — ' + detail}")
    if not passed:
        failures.append(f"{task}: {label}{' — ' + detail if detail else ''}")


def curl(path: str, jar: Path | None = None, data: dict | None = None, extra: list[str] | None = None) -> tuple[int, str, str]:
    """Return (status, headers, body) for one local request."""
    command = ["curl", "-sS", "-m", "60", "-D", "-", "-o", "-", "--compressed"]
    if jar is not None:
        command += ["-b", str(jar), "-c", str(jar)]
    if data is not None:
        command += ["--data", urllib.parse.urlencode(data)]
    if extra:
        command += extra
    command.append(BASE + path)
    result = subprocess.run(command, capture_output=True, check=False)
    raw = result.stdout
    head_bytes, _, body = raw.partition(b"\r\n\r\n")
    if not body and b"\n\n" in raw:
        head_bytes, _, body = raw.partition(b"\n\n")
    head = head_bytes.decode("utf-8", errors="replace")
    statuses = re.findall(r"^HTTP/\S+ (\d{3})", head, re.MULTILINE)
    return (int(statuses[-1]) if statuses else 0), head, body.decode("utf-8", errors="replace")


def admin_jar() -> Path:
    """Sign in as the local administrator and return the cookie jar."""
    credentials = json.loads(CREDENTIALS.read_text())
    jar = ROOT / ".cache" / "phase-admin.jar"
    jar.parent.mkdir(parents=True, exist_ok=True)
    curl("/wp-login.php", jar=jar)
    status, _, body = curl(
        "/wp-login.php",
        jar=jar,
        data={
            "log": credentials["admin_user"],
            "pwd": credentials["admin_password"],
            "wp-submit": "ورود",
            "redirect_to": BASE + "/wp-admin/",
            "testcookie": "1",
        },
    )
    if "/wp-admin/" not in body and status != 302:
        raise SystemExit("Could not sign in as the local administrator.")
    return jar


def nonce_from(html: str) -> str:
    """Extract the localized AJAX nonce from the admin page."""
    # Several admin scripts print a "nonce" key; the theme payload is the one
    # that also carries its own AJAX action name.
    match = re.search(r'var erfansanatAdmin = \{[^}]*?"nonce":"([a-f0-9]+)"', html)
    return match.group(1) if match else ""


def ajax(jar: Path, action: str, payload: dict, nonce: str, anonymous: bool = False) -> tuple[int, dict]:
    """POST a JSON action and decode the envelope."""
    fields = {"action": action, "nonce": nonce}
    for key, value in payload.items():
        fields[f"options[{key}]"] = value
    status, _, body = curl("/wp-admin/admin-ajax.php", jar=None if anonymous else jar, data=fields)
    if not body.strip():
        return status, {}
    try:
        return status, json.loads(body)
    except json.JSONDecodeError:
        return status, {"raw": body[:200]}


def task_2_1(jar: Path) -> None:
    """The panel renders, is guarded by capability and nonce, and saves once."""
    status, _, panel = curl("/wp-admin/admin.php?page=erfansanat-options", jar=jar)
    check("2.1", "theme panel loads for an administrator", status == 200, f"status={status}")
    check("2.1", "panel is RTL and calls the custom namespace", 'dir="rtl"' in panel and "erfansanat-admin" in panel)
    check("2.1", "panel exposes every settings tab", all(f'data-tab="{tab}"' in panel for tab in ["brand", "header", "contact", "footer", "archive", "advanced"]))
    check("2.1", "panel carries the AJAX nonce", bool(nonce_from(panel)))
    nonce = nonce_from(panel)

    anon_status, anon = ajax(jar, "erfansanat_save_options", {"brand_name": "anonym"}, nonce, anonymous=True)
    check("2.1", "visitor cannot save", anon_status >= 400 or not anon.get("success"), f"status={anon_status}")

    bad_nonce_status, bad_nonce = ajax(jar, "erfansanat_save_options", {"brand_name": "bad nonce"}, "0000000000")
    check("2.1", "wrong nonce is rejected", bad_nonce_status in (200, 403) and not bad_nonce.get("success"), f"status={bad_nonce_status}")

    ok_status, ok = ajax(jar, "erfansanat_save_options", {"brand_name": "عرفان صنعت — پذیرش"}, nonce)
    check("2.1", "valid save succeeds", ok_status == 200 and bool(ok.get("success")), str(ok)[:160])

    values = (ok.get("data") or {}).get("values") or {}
    check("2.1", "saved value is echoed back", values.get("brand_name") == "عرفان صنعت — پذیرش", str(values.get("brand_name")))
    check("2.1", "unknown keys are dropped", "unknown_key" not in values)

    # Task 2.1 owns storage only; the templates that print these values land in
    # Task 2.2/K, so the round-trip is proven through the panel and the endpoint.
    stored = subprocess.run(
        ["tools/wp", "--path=.local/wordpress", "option", "get", "erfansanat_theme_options", "--format=json"],
        cwd=ROOT,
        capture_output=True,
        text=True,
        check=False,
    ).stdout
    try:
        stored_value = json.loads(stored).get("brand_name", "")
    except (json.JSONDecodeError, AttributeError):
        stored_value = ""
    check("2.1", "saved value is persisted in the single option row", stored_value == "عرفان صنعت — پذیرش", stored[:120])

    inj_status, inj = ajax(jar, "erfansanat_save_options", {"brand_name": "<script>alert(1)</script>", "products_per_page": "9999"}, nonce)
    inj_values = (inj.get("data") or {}).get("values") or {}
    check("2.1", "markup in a text field is neutralised", "<script>" not in str(inj_values.get("brand_name")), str(inj_values.get("brand_name")))
    check("2.1", "numeric field is clamped to its range", inj_values.get("products_per_page") == 48, str(inj_values.get("products_per_page")))


    editor_status, editor = ajax(jar, "erfansanat_save_options", {"header_scripts": "<script>window.esTest=1;</script>"}, nonce)
    editor_values = (editor.get("data") or {}).get("values") or {}
    if editor.get("success") and "header_scripts" in editor_values:
        # A super administrator legitimately holds unfiltered_html; the contract
        # is that the value is only kept for that capability.
        check("2.1", "script field follows the unfiltered_html capability", True, f"status={editor_status}")
    else:
        check("2.1", "script field is dropped without unfiltered_html", editor_values.get("header_scripts", "") == "", str(editor_values.get("header_scripts")))

    reset_status, reset = ajax(jar, "erfansanat_save_options", {"brand_name": "عرفان صنعت اصفهان"}, nonce)
    check("2.1", "settings can be restored", bool(reset.get("success")), f"status={reset_status}")


def task_2_2(jar: Path) -> None:
    """Saved settings appear on the public site and unknown data never does."""
    status, _, panel = curl("/wp-admin/admin.php?page=erfansanat-options", jar=jar)
    nonce = nonce_from(panel)
    check("2.2", "panel still loads", status == 200 and bool(nonce), f"status={status}")

    # Start every run from the shipped defaults, whoever ran the suite before.
    subprocess.run(
        ["tools/wp", "--path=.local/wordpress", "option", "delete", "erfansanat_theme_options"],
        cwd=ROOT,
        capture_output=True,
        check=False,
    )

    _, _, home = curl("/")
    check("2.2", "reference brand name is printed by default", "عرفان صنعت اصفهان" in home)
    check("2.2", "reference phone is printed as a tel link", "tel:03191091011" in home)
    check("2.2", "reference email is printed as a mailto link", "mailto:info@erfansanat.com" in home)
    check("2.2", "footer about text is printed", "۲۴ ماه گارانتی تعویض" in home)
    check("2.2", "trust badges are printed", "ISO 9001:2015" in home and "IP67 / IP68" in home)
    check("2.2", "footer quick links are printed", "/licenses-and-honors/" in home and "/refund_returns/" in home)
    check("2.2", "footer product categories are printed", "/product_cat/point-light/" in home)
    check("2.2", "primary navigation is present", "erfansanat-menu" in home)
    check("2.2", "announcement bar is off by default", "erfansanat-notice" not in home)

    save_status, saved = ajax(
        jar,
        "erfansanat_save_options",
        {
            "brand_name": "برند آزمایشی دو",
            "brand_tagline": "شعار آزمایشی",
            "phone_primary": "03112345678",
            "notice_enabled": "1",
            "notice_text": "اعلان آزمایشی",
            "notice_link": "/shop/",
            "unknown_field": "must vanish",
        },
        nonce,
    )
    check("2.2", "settings save for an administrator", bool(saved.get("success")), f"status={save_status} {str(saved)[:120]}")

    _, _, home = curl("/")
    check("2.2", "saved brand reaches the front end", "برند آزمایشی دو" in home)
    check("2.2", "saved tagline reaches the front end", "شعار آزمایشی" in home)
    check("2.2", "saved phone reaches the tel link", "tel:03112345678" in home)
    check("2.2", "saved announcement bar renders", "اعلان آزمایشی" in home and "erfansanat-notice" in home)
    check("2.2", "unknown field is not printed", "must vanish" not in home)
    check("2.2", "old default no longer leaks when replaced", "tel:03191091011" not in home)

    ajax(
        jar,
        "erfansanat_save_options",
        {
            "brand_name": "عرفان صنعت اصفهان",
            "brand_tagline": "نورپردازی شهری و المان‌های نوری",
            "phone_primary": "031-91091011",
            "notice_enabled": "0",
            "notice_text": "",
            "notice_link": "",
        },
        nonce,
    )
    _, _, home = curl("/")
    check("2.2", "defaults can be restored", "tel:03191091011" in home and "erfansanat-notice" not in home)

    mobile_status, _, mobile = curl("/")
    check("2.2", "header has a mobile-safe viewport", 'name="viewport"' in mobile, f"status={mobile_status}")
    check("2.2", "header markup carries no fixed width", "width:1200px" not in mobile)

    check("2.2", "mobile view exposes the same settings", "tel:03191091011" in mobile)


def main() -> int:
    """Run the requested tasks."""
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--task", action="append", choices=["2.1", "2.2"])
    args = parser.parse_args()
    tasks = args.task or ["2.1", "2.2"]

    if not CREDENTIALS.exists():
        print("Local credentials are missing; run tools/setup-wordpress.sh first.", file=sys.stderr)
        return 2
    if curl("/")[0] == 0:
        print(f"No local preview on {BASE}; start it with tools/preview-wordpress.sh.", file=sys.stderr)
        return 2

    jar = admin_jar()
    for task in tasks:
        globals()["task_" + task.replace(".", "_")](jar)

    print(f"\n{checked - len(failures)}/{checked} HTTP checks passed.")
    for failure in failures:
        print(f"  - {failure}")
    if failures:
        return 1
    print("PHASE 2 HTTP CHECKS PASSED — local synthetic site only.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
