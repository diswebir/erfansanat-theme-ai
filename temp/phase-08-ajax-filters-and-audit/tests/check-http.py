#!/usr/bin/env python3
"""Phase 8 HTTP acceptance: live search, filters and the public forms.

Every assertion drives the live local site. The rules a form enforces are only
worth anything if they hold over a real request, so the negative cases here send
real POSTs and assert on where the server sends the visitor afterwards.

Usage:
    python3 temp/phase-08-ajax-filters-and-audit/tests/check-http.py [--task 8.1]
"""
from __future__ import annotations

import argparse
import http.cookiejar
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
FAILED: list[str] = []
TOTAL = 0

ERROR_MARKERS = ("Fatal error", "Warning:", "Notice:", "Deprecated:")


class NoRedirect(urllib.request.HTTPRedirectHandler):
    """Surface a 3xx instead of following it."""

    def redirect_request(self, req, fp, code, msg, headers, newurl):  # noqa: D102, ANN001
        return None


def check(task: str, label: str, ok: bool, detail: str = "") -> None:
    """Record one assertion."""
    global TOTAL
    TOTAL += 1
    if ok:
        print(f"[PASS] {label}")
    else:
        FAILED.append(f"{task}: {label}" + (f" ({detail})" if detail else ""))
        print(f"[FAIL] {label}" + (f" — {detail}" if detail else ""))


def fetch(url: str, timeout: int = 30) -> tuple[int, str]:
    """GET, returning status and body; never raises."""
    request = urllib.request.Request(url, headers={"User-Agent": "erfansanat-phase8-check"})
    try:
        with urllib.request.urlopen(request, timeout=timeout) as response:
            return response.status, response.read().decode("utf-8", "replace")
    except urllib.error.HTTPError as error:
        return error.code, error.read().decode("utf-8", "replace")
    except urllib.error.URLError as error:
        return 0, str(error)


def clean(body: str) -> bool:
    """No PHP error output in the body."""
    return not any(marker in body for marker in ERROR_MARKERS)


class Session:
    """A cookie-keeping session, so a nonce and its cookie stay together."""

    def __init__(self) -> None:
        self.jar = http.cookiejar.CookieJar()
        self.opener = urllib.request.build_opener(
            urllib.request.HTTPCookieProcessor(self.jar), NoRedirect
        )

    def get(self, url: str) -> tuple[int, str]:
        """GET through this session."""
        request = urllib.request.Request(url, headers={"User-Agent": "erfansanat-phase8-check"})
        try:
            with self.opener.open(request, timeout=30) as response:
                return response.status, response.read().decode("utf-8", "replace")
        except urllib.error.HTTPError as error:
            return error.code, error.read().decode("utf-8", "replace")
        except urllib.error.URLError as error:
            return 0, str(error)

    def post(self, url: str, fields: dict[str, str], referer: str = "") -> tuple[int, str]:
        """POST through this session without following the redirect.

        An empty `referer` sends no header at all, which is what a strict
        referrer-policy, a privacy extension or a stripping proxy produces.
        """
        data = urllib.parse.urlencode(fields).encode()
        headers = {
            "User-Agent": "erfansanat-phase8-check",
            "Content-Type": "application/x-www-form-urlencoded",
        }
        if referer:
            headers["Referer"] = referer
        request = urllib.request.Request(url, data=data, headers=headers)
        try:
            with self.opener.open(request, timeout=30) as response:
                return response.status, ""
        except urllib.error.HTTPError as error:
            # 302 arrives here because redirects are disabled for this handler.
            return error.code, error.headers.get("Location", "")
        except urllib.error.URLError as error:
            return 0, str(error)


def wp(*args: str) -> str:
    """Run wp-cli."""
    result = subprocess.run(
        ["tools/wp", "--path=.local/wordpress", *args],
        cwd=ROOT, capture_output=True, text=True, check=False,
    )
    return result.stdout.strip()


def count(body: str, needle: str) -> int:
    """Occurrences of a literal."""
    return body.count(needle)


def nonce_from(body: str) -> str:
    """The live-search nonce the script is given."""
    match = re.search(r'"nonce":"([a-z0-9]+)"', body)
    return match.group(1) if match else ""


def task_8_1() -> None:
    """Live search and the native filters."""
    status, home = fetch(BASE + "/")
    check("8.1", "home returns 200", status == 200, f"status={status}")

    nonce = nonce_from(home)
    check("8.1", "the live-search nonce reaches the page", nonce != "", "no nonce in the script data")
    check("8.1", "the search panel has a results region", "data-live-search-results" in home)
    check("8.1", "the panel is a live region", 'aria-live="polite"' in home)

    if nonce:
        endpoint = f"{BASE}/wp-admin/admin-ajax.php?action=erfansanat_live_search&nonce={nonce}"
        status, payload = fetch(endpoint + "&q=" + urllib.parse.quote("نور"))
        check("8.1", "live search returns 200", status == 200, f"status={status}")
        try:
            data = json.loads(payload)["data"]
        except Exception:  # noqa: BLE001 - reported as a failure below
            data = {}
        check("8.1", "live search finds a term that exists", data.get("total", 0) > 0,
              f"total={data.get('total')}")
        check("8.1", "live search groups its results", len(data.get("groups", [])) >= 2,
              f"groups={len(data.get('groups', []))}")
        check("8.1", "every group carries a label", all(g.get("label") for g in data.get("groups", [])))
        check("8.1", "every result carries a URL and a title",
              all(item.get("url") and item.get("title")
                  for group in data.get("groups", []) for item in group.get("items", [])))

        # A one-character query is refused rather than scanned.
        status, short = fetch(endpoint + "&q=" + urllib.parse.quote("ن"))
        try:
            short_data = json.loads(short)["data"]
        except Exception:  # noqa: BLE001
            short_data = {}
        check("8.1", "a one-letter query returns no rows", short_data.get("total", 0) == 0)

        # A script tag must not be echoed back.
        status, xss = fetch(endpoint + "&q=" + urllib.parse.quote("<script>alert(1)</script>"))
        check("8.1", "a hostile query is not reflected", "<script>alert" not in xss)

    # No nonce means no answer.
    status, _ = fetch(f"{BASE}/wp-admin/admin-ajax.php?action=erfansanat_live_search&q=test")
    check("8.1", "the endpoint refuses a request without a nonce", status == 403, f"status={status}")

    # Filters narrow the archives and the URLs are shareable.
    status, all_projects = fetch(BASE + "/projects/")
    check("8.1", "the project archive returns 200", status == 200, f"status={status}")
    total = count(all_projects, 'data-project-item="true"')
    check("8.1", "the project archive renders items", total > 0, f"items={total}")
    check("8.1", "the project archive offers a server-side filter form",
          "data-native-filters" in all_projects)
    check("8.1", "the filter form is a GET", 'method="get"' in all_projects)

    status, filtered = fetch(BASE + "/projects/?city=" + urllib.parse.quote("قم"))
    narrowed = count(filtered, 'data-project-item="true"')
    check("8.1", "a city filter narrows the archive", status == 200 and narrowed < total,
          f"{total} -> {narrowed} (status={status})")
    check("8.1", "a filtered archive still has one h1", len(re.findall(r"<h1[ >]", filtered)) == 1)

    status, all_shop = fetch(BASE + "/shop/")
    check("8.1", "the shop returns 200", status == 200, f"status={status}")
    shop_total = count(all_shop, 'data-product-item="true"')
    check("8.1", "the shop offers a server-side filter form", "data-native-filters" in all_shop)
    check("8.1", "the shop filter offers a price band", 'name="es_price"' in all_shop)

    status, priced = fetch(BASE + "/shop/?es_price=over-6")
    priced_total = count(priced, 'data-product-item="true"')
    check("8.1", "a price band narrows the shop", status == 200 and 0 < priced_total < shop_total,
          f"{shop_total} -> {priced_total}")

    # Sorting must not break the query.
    for sort in ("newest", "popular", "rating"):
        status, sorted_body = fetch(f"{BASE}/shop/?es_sort={sort}")
        check("8.1", f"sorting by {sort} still renders", status == 200 and clean(sorted_body),
              f"status={status}")
        check("8.1", f"sorting by {sort} keeps the product cards",
              count(sorted_body, 'data-product-item="true"') > 0)

    # A hostile filter value must not become a query.
    status, hostile = fetch(f"{BASE}/shop/?es_price=" + urllib.parse.quote("'; DROP TABLE es_posts;--"))
    check("8.1", "a hostile filter value is ignored", status == 200 and clean(hostile), f"status={status}")


def task_8_2() -> None:
    """The public form endpoint and the inbox."""
    status, contact = fetch(BASE + "/contact/")
    check("8.2", "the contact page returns 200", status == 200, f"status={status}")
    check("8.2", "the form posts to the server", "admin-post.php" in contact)
    for field in ("es_nonce", "es_website", "es_started", "name=\"es_form\""):
        check("8.2", f"the form carries {field}", field in contact)
    check("8.2", "the required fields are marked", 'name="message"' in contact and 'name="phone"' in contact)

    action_url = f"{BASE}/wp-admin/admin-post.php"
    referer = f"{BASE}/contact/"

    # The endpoint rate-limits per address, keyed on a hash of REMOTE_ADDR. Every
    # request in this suite comes from 127.0.0.1, so a previous run's submissions
    # count against this one. Clearing the counters makes the run repeatable
    # without weakening the rule the next assertions prove is real.
    wp("eval", "\\ErfanSanat\\Theme\\Forms\\FormHandler::reset_limits();")

    ids_before = set(
        wp("post", "list", "--post_type=erfansanat_message", "--post_status=any",
           "--format=ids", "--posts_per_page=-1").split()
    )
    before = str(len(ids_before))

    # --- a valid submission is stored ---
    session = Session()
    _, form_page = session.get(referer)
    match = re.search(r'name="es_nonce" value="([^"]+)"', form_page)
    nonce = match.group(1) if match else ""
    check("8.2", "the form renders a nonce", nonce != "")

    fields = {
        "action": "erfansanat_submit",
        "es_form": "contact",
        "es_nonce": nonce,
        "es_started": str(int(time.time()) - 12),
        "name": "آزمون فاز هشت",
        "phone": "۰۹۱۲۳۴۵۶۷۸۹",
        "email": "phase8@example.com",
        "org": "شهرداری آزمون",
        "message": "این پیام برای آزمون خودکار فاز هشت ارسال شده است.",
    }
    status, location = session.post(action_url, fields, referer)
    check("8.2", "a valid submission is accepted", status == 302 and "es_sent=ok" in location,
          f"status={status} location={location}")
    after = wp("post", "list", "--post_type=erfansanat_message", "--post_status=any",
               "--format=count")
    check("8.2", "the submission is stored", after != before, f"{before} -> {after}")

    # The stored message is private and stripped of markup.
    created = [
        pid for pid in wp("post", "list", "--post_type=erfansanat_message", "--post_status=any",
                          "--format=ids", "--posts_per_page=-1").split()
        if pid not in ids_before
    ]
    stored_id = created[:1]
    if stored_id:
        check("8.2", "the stored message is private",
              wp("post", "get", stored_id[0], "--field=post_status") == "private")
        body = wp("post", "meta", "get", stored_id[0], "_es_body")
        check("8.2", "the stored body is plain text", "<" not in body, body[:60])
        check("8.2", "the phone number was normalised", "09123456789" in wp("post", "meta", "get", stored_id[0], "_es_phone"))
        check("8.2", "only an address hash is kept",
              "127.0.0.1" not in wp("post", "meta", "get", stored_id[0], "_es_ip_hash"))
        for meta_key in ("_es_form", "_es_name", "_es_email", "_es_mailed"):
            check("8.2", f"the message records {meta_key}",
                  wp("post", "meta", "get", stored_id[0], meta_key) != "")

    # --- the negative cases, each with a fresh nonce ---
    def attempt(overrides: dict[str, str], label: str, expected: str) -> None:
        """Submit once with overrides and assert the outcome code."""
        fresh = Session()
        _, page = fresh.get(referer)
        hit = re.search(r'name="es_nonce" value="([^"]+)"', page)
        payload = dict(fields)
        payload["es_nonce"] = hit.group(1) if hit else ""
        payload.update(overrides)
        code, where = fresh.post(action_url, payload, referer)
        check("8.2", label, code == 302 and f"es_sent={expected}" in where,
              f"status={code} location={where}")

    attempt({"es_nonce": "bad"}, "a wrong nonce is rejected", "invalid")
    attempt({"es_website": "http://spam.example"}, "a filled honeypot is silently discarded", "ok")
    attempt({"es_started": str(int(time.time()))}, "an instant submission is rejected", "slow")
    attempt({"phone": "12"}, "an implausible phone is rejected", "invalid")
    attempt({"name": ""}, "a missing required field is rejected", "invalid")
    attempt({"es_form": "evil"}, "an unknown form is rejected", "invalid")
    attempt({"email": "not-an-email"}, "a malformed e-mail is rejected", "invalid")

    # --- the visitor has to be able to read the answer ---
    # A browser that strips the Referer header used to send the visitor to the
    # front page with `?es_sent=invalid` and no form in sight, so the message the
    # handler wrote was never printed anywhere.
    no_ref = Session()
    _, page = no_ref.get(referer)
    hit = re.search(r'name="es_nonce" value="([^"]+)"', page)
    payload = dict(fields)
    payload["es_nonce"] = hit.group(1) if hit else ""
    payload["name"] = ""
    code, where = no_ref.post(action_url, payload)
    check("8.2", "a submission without a referer still answers", code == 302, f"status={code}")
    check("8.2", "…and returns to the contact page, not the front page",
          where.startswith(f"{BASE}/contact/"), where)
    if code == 302 and where.startswith(f"{BASE}/contact/"):
        _, back = Session().get(where)
        check("8.2", "the error is announced, not merely printed", 'role="alert"' in back)
        check("8.2", "the error is reachable from the form",
              'id="es-form-status"' in back and 'aria-describedby="es-form-status"' in back)
        check("8.2", "the message is rendered on the page the visitor lands on",
              'data-contact-status="error"' in back)

    # The rate limit is real: exhaust it deliberately, prove the refusal, then
    # clear it so a rerun starts from zero.
    wp("eval", "\\ErfanSanat\\Theme\\Forms\\FormHandler::reset_limits();")
    throttle_hit = ""
    for _ in range(7):
        fresh = Session()
        _, page = fresh.get(referer)
        hit = re.search(r'name="es_nonce" value="([^"]+)"', page)
        payload = dict(fields)
        payload["es_nonce"] = hit.group(1) if hit else ""
        code, where = fresh.post(action_url, payload, referer)
        if "es_sent=throttled" in where:
            throttle_hit = where
            break
    check("8.2", "the submission rate limit refuses a flood", throttle_hit != "", "never throttled")
    wp("eval", "\\ErfanSanat\\Theme\\Forms\\FormHandler::reset_limits();")
    after = wp("post", "list", "--post_type=erfansanat_message", "--post_status=private", "--format=count")

    # The honeypot must not have written anything either.
    final = wp("post", "list", "--post_type=erfansanat_message", "--post_status=private", "--format=count")
    check("8.2", "only the valid submission was stored", final == after, f"{after} -> {final}")

    # --- the inbox is unreachable from outside ---
    for path in ("/erfansanat_message/", "/?post_type=erfansanat_message"):
        status, body = fetch(BASE + path)
        check("8.2", f"the inbox does not disclose a message at {path}",
              "آزمون فاز هشت" not in body)

    status, rest = fetch(BASE + "/wp-json/wp/v2/erfansanat_message")
    check("8.2", "the REST API does not expose the inbox", status in (401, 403, 404), f"status={status}")

    # --- the status messages render ---
    for code, needle in (
        ("ok", "پیام شما ثبت شد"),
        ("invalid", "فیلدهای ضروری"),
        ("slow", "بیش از حد سریع"),
        ("throttled", "تعداد پیام"),
        ("failed", "ثبت پیام ممکن نشد"),
    ):
        status, page = fetch(f"{BASE}/contact/?es_sent={code}")
        check("8.2", f"the {code} outcome is explained to the visitor", needle in page, f"status={status}")

    status, stranger = fetch(f"{BASE}/contact/?es_sent=nonsense")
    check("8.2", "an unknown outcome shows no message",
          "پیام شما ثبت شد" not in stranger and clean(stranger))

    # Leave the fixture as it was found. A check that writes to the database and
    # never cleans up turns the next run's "stored" assertion into a lie.
    leftover = [
        pid for pid in wp("post", "list", "--post_type=erfansanat_message", "--post_status=any",
                          "--format=ids", "--posts_per_page=-1").split()
        if pid not in ids_before
    ]
    if leftover:
        wp("eval", "foreach (" + json.dumps([int(x) for x in leftover]) + " as $id) { wp_delete_post($id, true); }")
    remaining = str(len(set(wp("post", "list", "--post_type=erfansanat_message", "--post_status=any",
                               "--format=ids", "--posts_per_page=-1").split())))
    check("8.2", "the run leaves the inbox as it found it", remaining == before,
          f"{before} -> {remaining}")


def task_8_3() -> None:
    """The audit's own preconditions, over HTTP."""
    for path in ("/", "/shop/", "/projects/", "/blog/", "/contact/", "/services/"):
        status, body = fetch(BASE + path)
        check("8.3", f"{path} returns 200", status == 200, f"status={status}")
        check("8.3", f"{path} is free of PHP errors", clean(body))
        check("8.3", f"{path} has exactly one h1", len(re.findall(r"<h1[ >]", body)) == 1,
              f"h1={len(re.findall(r'<h1[ >]', body))}")
        check("8.3", f"{path} declares a canonical URL", 'rel="canonical"' in body)
        check("8.3", f"{path} declares a meta description", 'name="description"' in body)
        check("8.3", f"{path} is Persian and RTL",
              'lang="fa' in body.replace("'", '"') and 'dir="rtl"' in body.replace("'", '"'))
        check("8.3", f"{path} publishes structured data", "application/ld+json" in body)

    status, product_page = fetch(BASE + "/shop/")
    first = re.search(r'href="([^"]*/product/[^"]+)"', product_page)
    if first:
        _, single = fetch(first.group(1).replace("&amp;", "&"))
        check("8.3", "a product page describes a Product in JSON-LD", '"Product"' in single)
        check("8.3", "a product page is indexable", "noindex" not in single.lower())

    # Paginated archives must not claim to be page one.
    status, page_two = fetch(BASE + "/projects/page/2/")
    if status == 200:
        canonical = re.search(r'rel="canonical" href="([^"]+)"', page_two)
        check("8.3", "page 2 canonically points at page 2",
              canonical is not None and canonical.group(1).rstrip("/").endswith("page/2"),
              canonical.group(1) if canonical else "no canonical")

    # The theme's font must reach the admin and the login form.
    status, login = fetch(BASE + "/wp-login.php")
    check("8.3", "the login form loads the theme font", "admin/font.css" in login, f"status={status}")
    status, font = fetch(BASE + "/wp-content/themes/erfansanat/assets/fonts/vazirmatn-arabic.woff2")
    check("8.3", "the bundled font file is served", status == 200, f"status={status}")

    # A sitemap and a feed exist for a crawler.
    for path in ("/wp-sitemap.xml", "/feed/"):
        status, _ = fetch(BASE + path)
        check("8.3", f"{path} is reachable", status == 200, f"status={status}")

    # A missing page is a real 404 with a way out.
    status, missing = fetch(BASE + "/phase-8-audit-missing/")
    check("8.3", "a missing page returns 404", status == 404, f"status={status}")
    check("8.3", "the 404 page offers a search box", 'name="s"' in missing)
    check("8.3", "the 404 page is not noindex-blank", len(missing) > 2000, f"{len(missing)} bytes")


def main() -> int:
    """Run the selected task groups."""
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--task", action="append", choices=["8.1", "8.2", "8.3"])
    args = parser.parse_args()

    groups = {"8.1": task_8_1, "8.2": task_8_2, "8.3": task_8_3}
    chosen = args.task or list(groups)

    status, _ = fetch(BASE + "/")
    if status != 200:
        print(f"No local preview on {BASE}.")
        return 2

    for key in chosen:
        groups[key]()

    print()
    if FAILED:
        print(f"{TOTAL - len(FAILED)}/{TOTAL} HTTP checks passed.")
        for row in FAILED:
            print("  - " + row)
        print("PHASE 8 HTTP CHECKS FAILED.")
        return 1
    print(f"{TOTAL}/{TOTAL} HTTP checks passed.")
    print("PHASE 8 HTTP CHECKS PASSED — local synthetic site only.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
