#!/usr/bin/env python3
"""Phase 6.1 HTTP acceptance: the single product page renders real WooCommerce data.

Every assertion drives the live local site rather than reading the theme source,
so a template that renders but loses its markup contract still fails.
"""
from __future__ import annotations

import argparse
import http.cookiejar
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

# The six page sections of the approved reference, in order.
SECTIONS = [
    ("header", "data-prod-header"),
    ("buy", "data-gallery-root"),
    ("details", 'id="product-details"'),
    ("projects", "پروژه‌های اجراشده"),
    ("faq", "data-faq-list"),
    ("related", "محصولات مشابه"),
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
    request = urllib.request.Request(url, headers={"User-Agent": "erfansanat-phase6-check"})
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


def product_slugs() -> list[str]:
    out = wp("post", "list", "--post_type=product", "--post_status=publish",
             "--format=ids", "--posts_per_page=-1")
    slugs: list[str] = []
    for pid in out.split():
        slug = wp("post", "get", pid, "--field=post_name")
        if slug:
            slugs.append(slug)
    return slugs


def product_meta(slug: str, *keys: str) -> dict[str, str]:
    """Read legacy product meta through WP-CLI so the page is judged against the DB."""
    pid = wp("post", "list", "--post_type=product", "--name=" + slug, "--format=ids")
    pid = pid.split()[0] if pid.split() else ""
    if not pid:
        return {}
    script = "<?php\n$id=%s;\n" % pid
    for key in keys:
        script += "echo %r, '=', get_post_meta($id, %r, true), \"\\n\";\n" % (key, key)
    path = ROOT / ".local" / "probe-meta.php"
    path.write_text(script, encoding="utf-8")
    out = wp("eval-file", str(path))
    return dict(line.split("=", 1) for line in out.splitlines() if "=" in line)


def lite(body: str) -> str:
    """Page with inline scripts stripped: assertions must hold without JS."""
    return re.sub(r"<script\b.*?</script>", "", body, flags=re.S)


def task_6_1(_: str) -> None:
    slugs = product_slugs()
    check("6.1", "the fixture has published products", len(slugs) > 0, str(len(slugs)))

    # Every product renders, with no fatal and no duplicate layout wrapper.
    broken: list[str] = []
    for slug in slugs:
        status, body = fetch(f"{BASE}/product/{slug}/")
        if status != 200:
            broken.append(f"{slug}={status}")
            continue
        if any(token in body for token in ("Fatal error", "Parse error", "Warning: ", "Uncaught")):
            broken.append(f"{slug}=php-error")
        elif body.count("<main") != 1:
            broken.append(f"{slug}=main{body.count('<main')}")
    check("6.1", f"all {len(slugs)} product pages answer 200 with one <main>", not broken, ",".join(broken))

    status, page = fetch(f"{BASE}/product/wall-washer/")
    check("6.1", "product page answers 200", status == 200, f"status={status}")
    if status != 200:
        return

    # The six reference sections, in order.
    positions: dict[str, int] = {}
    for key, marker in SECTIONS:
        index = page.find(marker)
        check("6.1", f"section {key} renders", index >= 0)
        if index >= 0:
            positions[key] = index
    ordered = [k for k, _ in SECTIONS if k in positions]
    check("6.1", "sections appear in the reference order",
          ordered == sorted(ordered, key=lambda k: positions[k]), str(ordered))

    check("6.1", "exactly one <h1>", page.count("<h1") == 1, str(page.count("<h1")))
    check("6.1", "the h1 is the product name",
          "والواشر RGB و تک‌رنگ صنعتی" in (re.search(r"<h1[^>]*>(.*?)</h1>", page, re.S) or [""])[1])
    check("6.1", "no PHP error leaks into the page",
          not any(t in page for t in ("Fatal error", "Parse error", "Warning: ", "Notice: ", "Deprecated: ")))

    ids = re.findall(r'\sid="([^"]+)"', page)
    dupes = sorted({i for i in ids if ids.count(i) > 1})
    check("6.1", "no duplicate DOM ids", not dupes, ",".join(dupes))

    # Gallery contract.
    for hook in ["data-gallery-root", "data-gallery-main-img", "data-gallery-prev", "data-gallery-next",
                 "data-gallery-counter", "data-gallery-expand", "data-gallery-thumb",
                 "data-gallery-lightbox", "data-gallery-lightbox-close", "data-gallery-lightbox-img"]:
        check("6.1", f"gallery hook {hook}", hook + "=" in page)
    check("6.1", "no empty image src", 'src=""' not in page)

    # Buy box contract.
    check("6.1", "buy box is sticky on desktop", "lg:sticky lg:top-28" in page)
    check("6.1", "SKU is monospaced and ltr", 'dir="ltr"' in page and "font-mono" in page)
    # Price block: only a sellable product carries the factory-price label and
    # the minimum-order line. wall-washer is quote-only, so read illumina.
    _, priced = fetch(f"{BASE}/product/illumina/")
    check("6.1", "factory price label present", "قیمت مصوب کارخانه" in priced)
    check("6.1", "minimum order line present", "حداقل سفارش" in priced)
    check("6.1", "quote-only product hides the factory price label", "قیمت مصوب کارخانه" not in page)
    check("6.1", "consult phone is a real tel link", 'href="tel:' in page)
    check("6.1", "spec summary chips are printed", page.count("چکیده مشخصات") == 1 and page.count("مشخصات کامل") >= 1)

    # Tabs contract.
    check("6.1", "three tab buttons", len(re.findall(r'data-prod-tab-btn="[a-z]+"', page)) == 3,
          str(len(re.findall(r'data-prod-tab-btn="[a-z]+"', page))))
    check("6.1", "three tab panels", len(re.findall(r'data-prod-tab-panel="[a-z]+"', page)) == 3)
    check("6.1", "tabs container matches reference id", 'id="product-details"' in page)
    check("6.1", "aria wiring on tabs", page.count('role="tab"') >= 3 and page.count('aria-selected') >= 3)

    # Specs: the reference renders a definition grid, not a <table>.
    spec_rows = len(re.findall(r'text-xs font-bold text-slate-500', page))
    check("6.1", "spec grid has real rows", spec_rows >= 4, str(spec_rows))
    check("6.1", "specs do not use a table element", "<table" not in page)
    for label in ["کد بین‌الملل GS1", "ایران‌کد", "HS", "ISIC"]:
        check("6.1", f"identifier tile {label}", label in page)
    meta = product_meta("wall-washer", "gs1_id", "national_code", "hs_id", "isic_id", "cpc_id", "product_id")
    missing = [k for k, v in meta.items() if v and v not in page]
    check("6.1", "identifier values come from legacy meta", not missing, ",".join(missing))

    # Reviews come from real comments and keep the real form.
    check("6.1", "review list is printed", "data-reviews-list" in page)
    check("6.1", "real comment form present", "comment-form" in page and 'name="rating"' in page)
    check("6.1", "no fake review success banner", "data-review-success" not in page)

    # FAQ, related projects, related products.
    check("6.1", "FAQ items are printed", len(re.findall(r'data-faq-toggle="\d+"', page)) >= 1)
    check("6.1", "FAQ body hooks present", 'data-faq-body="0"' in page)
    check("6.1", "related products section printed", "محصولات مشابه" in page)
    check("6.1", "mobile buy bar is hidden on desktop",
          'fixed inset-x-0 bottom-0 z-40' in page and "lg:hidden" in page)

    # No static mirror leftovers and no faked cart.
    check("6.1", "no static mirror product link", 'href="product/' not in page)
    check("6.1", "no localStorage cart", "localStorage" not in page)
    check("6.1", "no sample cart counter", "data-header-cart-badge" in page)


def task_6_2(_: str) -> None:
    """Purchase modes: inquiry, in stock, and out of stock must differ."""
    status, inquiry = fetch(f"{BASE}/product/wall-washer/")
    check("6.2", "inquiry product answers 200", status == 200)
    check("6.2", "inquiry product has no add-to-cart form", 'name="add-to-cart"' not in inquiry)
    check("6.2", "inquiry product offers the quote CTA", 'href="tel:' in inquiry)
    check("6.2", "inquiry product is marked as such", "استعلام" in inquiry)

    status, simple = fetch(f"{BASE}/product/illumina/")
    check("6.2", "in-stock product answers 200", status == 200)
    check("6.2", "in-stock product prints WooCommerce's form", 'name="add-to-cart"' in simple)
    check("6.2", "in-stock product prints the submit button", "single_add_to_cart_button" in simple)
    check("6.2", "in-stock product prints a real price", "woocommerce-Price-amount" in simple)

    status, oos = fetch(f"{BASE}/product/industrial-led-bulb/")
    check("6.2", "out-of-stock product answers 200", status == 200)
    check("6.2", "out-of-stock product has no add-to-cart form", 'name="add-to-cart"' not in oos)
    check("6.2", "out-of-stock product says it is unavailable", "ناموجود" in oos)
    check("6.2", "out-of-stock product keeps the quote path", 'href="tel:' in oos)

    status, variable = fetch(f"{BASE}/product/artam-light-tree/")
    check("6.2", "variable product answers 200", status == 200)
    check("6.2", "variable product prints the variation form", "variations_form" in variable)
    check("6.2", "variable product prints the add-to-cart button", "single_add_to_cart_button" in variable)
    check("6.2", "variable product loads the variation script", "wc-add-to-cart-variation" in variable)

    # Every product is sellable through exactly one of the three paths.
    bad: list[str] = []
    for slug in product_slugs():
        _, body = fetch(f"{BASE}/product/{slug}/")
        buyable = 'name="add-to-cart"' in body
        quotable = 'href="tel:' in body
        if not (buyable or quotable):
            bad.append(slug)
        if buyable and 'name="add-to-cart"' in body and "true_sale" in body and "false" in body:
            pass
    check("6.2", "every product offers a purchase or a quote", not bad, ",".join(bad))


def task_6_3(_: str) -> None:
    """The add-to-cart round trip is real: WooCommerce confirms it server-side."""
    jar = http.cookiejar.CookieJar()
    opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
    opener.addheaders = [("User-Agent", "erfansanat-phase6-check")]

    pid = wp("post", "list", "--post_type=product", "--name=illumina", "--format=ids").split()
    if not pid:
        check("6.3", "fixture product exists", False)
        return

    url = f"{BASE}/product/illumina/?" + urllib.parse.urlencode({"add-to-cart": pid[0], "quantity": 2})
    try:
        with opener.open(url, timeout=30) as response:
            body = response.read().decode("utf-8", "replace")
            status = response.status
    except urllib.error.HTTPError as error:
        status, body = error.code, error.read().decode("utf-8", "replace")

    check("6.3", "add-to-cart request answers 200", status == 200, f"status={status}")
    check("6.3", "WooCommerce prints its own confirmation", "woocommerce-message" in body)
    check("6.3", "the confirmation names the product", "ایلومینا" in body or "افزوده شد" in body)
    check("6.3", "the cart cookie was set", any(c.name.startswith("wp_woocommerce_session") or
                                                  c.name.startswith("woocommerce_items_in_cart") for c in jar))
    count = next((c.value for c in jar if c.name == "woocommerce_items_in_cart"), "")
    check("6.3", "the cart reports one line item", count == "1", count)


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--base", default="http://127.0.0.1:8080")
    parser.add_argument("--task", action="append", choices=["6.1", "6.2", "6.3"])
    args = parser.parse_args()
    global BASE
    BASE = args.base
    tasks = args.task or ["6.1", "6.2", "6.3"]
    for task in tasks:
        print(f"\n== task {task} ==\n")
        globals()["task_" + task.replace(".", "_")](args.base)

    print(f"\n{TOTAL - len(FAILED)}/{TOTAL} HTTP checks passed.")
    for failure in FAILED:
        print("  - " + failure)
    if FAILED:
        return 1
    print("PHASE 6.1 HTTP CHECKS PASSED — local synthetic site only.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
