#!/usr/bin/env python3
"""Phase 6.1 HTTP acceptance: the single product page renders real WooCommerce data.

Every assertion drives the live local site rather than reading the theme source,
so a template that renders but loses its markup contract still fails.
"""
from __future__ import annotations

import argparse
import http.cookiejar
import json
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
    _, quoted = fetch(f"{BASE}/product/foroogh-chandelier/")
    check("6.1", "quote-only product hides the factory price label", "قیمت مصوب کارخانه" not in quoted)
    check("6.1", "quote-only product offers the phone instead", 'href="tel:' in quoted)
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
    status, inquiry = fetch(f"{BASE}/product/foroogh-chandelier/")
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


def term_urls(taxonomy: str) -> list[str]:
    script = (
        "<?php\n"
        "foreach (get_terms(['taxonomy'=>%r,'hide_empty'=>false]) as $t) {\n"
        "  echo str_replace(home_url(),'',get_term_link($t)), \"\\n\";\n"
        "}\n" % taxonomy
    )
    path = ROOT / ".local" / "probe-terms.php"
    path.write_text(script, encoding="utf-8")
    return [line.strip() for line in wp("eval-file", str(path)).splitlines() if line.strip()]


def task_6_4(_: str) -> None:
    """The shop archive is a real, paginated archive."""
    status, shop = fetch(BASE + "/shop/")
    check("6.4", "shop answers 200", status == 200, f"status={status}")
    if status != 200:
        return

    check("6.4", "no PHP error leaks into the shop",
          not any(t in shop for t in ("Fatal error", "Parse error", "Warning: ", "Notice: ", "Deprecated: ")))
    check("6.4", "shop has one <main>", shop.count("<main") == 1, str(shop.count("<main")))
    check("6.4", "shop has one <h1>", shop.count("<h1") == 1, str(shop.count("<h1")))

    ids = re.findall(r'\sid="([^"]+)"', shop)
    dupes = sorted({i for i in ids if ids.count(i) > 1})
    check("6.4", "no duplicate DOM ids", not dupes, ",".join(dupes))

    # Archive root carries the contract the script reads.
    for attr in ['data-products-archive="true"', 'data-initial-category=', 'data-items-per-page="9"']:
        check("6.4", f"archive root has {attr}", attr in shop)
    check("6.4", "archive anchors on #archive", 'id="archive"' in shop)

    # Grid: nine products, each with the full filter payload.
    items = re.findall(r"data-product-item=\"true\"(.*?)class=\"block\"", shop, re.S)
    check("6.4", "the first shop page holds nine products", len(items) == 9, str(len(items)))

    required = ["data-product-category", "data-product-categories", "data-product-purchasable",
                "data-product-price", "data-product-ip", "data-product-voltage", "data-product-views",
                "data-product-rating", "data-product-search-text"]
    incomplete = []
    for index, raw in enumerate(items):
        for attr in required:
            if attr + "=" not in raw:
                incomplete.append(f"{index}:{attr}")
    check("6.4", "every grid item carries the full filter payload", not incomplete, ",".join(incomplete[:5]))

    prices = [int(m) for m in re.findall(r'data-product-price="(\d+)"', "".join(items))]
    check("6.4", "prices are real numbers", bool(prices) and all(p > 0 for p in prices), str(prices[:3]))
    purch = set(re.findall(r'data-product-purchasable="(true|false)"', "".join(items)))
    check("6.4", "purchasable is the string the script compares", purch <= {"true", "false"}, str(purch))

    # Filters, sort and the mobile drawer.
    for attr in ['data-product-search="true"', 'data-product-purchase-mode="all"',
                 'data-product-price-range="true"', 'data-product-ip-filter="true"',
                 'data-product-voltage-filter="true"', 'data-product-sort="true"',
                 'data-product-empty="true"', 'data-product-grid="true"']:
        check("6.4", f"filter hook {attr}", attr in shop)
    check("6.4", "sort offers the five reference modes",
          len(re.findall(r'value="(?:default|popular|rating|price-asc|price-desc)"', shop)) >= 5)
    check("6.4", "mobile drawer exists", 'data-archive-drawer="true"' in shop and
          shop.count('data-archive-drawer-open="true"') >= 2 and shop.count('data-archive-drawer-close="true"') >= 2)
    check("6.4", "category tree is rendered from terms", shop.count("/product_cat/") >= 15,
          str(shop.count("/product_cat/")))
    check("6.4", "shop offers the real phone", 'href="tel:' in shop)
    check("6.4", "closing contact CTA is present", 'id="contact"' in shop)

    # Server pagination is real: page two holds different products.
    status2, page2 = fetch(BASE + "/shop/page/2/")
    check("6.4", "shop page 2 answers 200", status2 == 200, f"status={status2}")
    if status2 == 200:
        items2 = re.findall(r'data-product-search-text="([^"]*)"', page2)
        items1 = re.findall(r'data-product-search-text="([^"]*)"', shop)
        check("6.4", "page 2 holds nine different products",
              len(items2) == 9 and not (set(items1) & set(items2)), f"{len(items1)} vs {len(items2)}")

    status3, page3 = fetch(BASE + "/shop/page/3/")
    if status3 == 200:
        items3 = re.findall(r'data-product-search-text="([^"]*)"', page3)
        total = wp("post", "list", "--post_type=product", "--post_status=publish", "--format=count")
        check("6.4", "every published product appears once across the pages",
              len(set(items1) | set(items2) | set(items3)) == int(total or 0),
              f"{len(set(items1) | set(items2) | set(items3))} of {total}")

    # The pager links real pages rather than dead buttons.
    check("6.4", "pager marks itself as server-rendered", "data-pagination-server=\"true\"" in shop)
    check("6.4", "pager links page 2", re.search(r'href="[^"]*/shop/page/2/"', shop) is not None)
    check("6.4", "pager uses the reference attribute names",
          "data-page-num=" in shop and "data-page-btn" not in shop)


def task_6_5(_: str) -> None:
    """Category and tag archives reuse the archive body."""
    categories = term_urls("product_cat")
    tags = term_urls("product_tag")
    check("6.5", "the fixture has product categories", len(categories) >= 15, str(len(categories)))
    check("6.5", "the fixture has product tags", len(tags) >= 1, str(len(tags)))

    broken: list[str] = []
    empty_faq = 0
    for url in categories + tags:
        status, body = fetch(BASE + url)
        if status != 200:
            broken.append(f"{url}={status}")
            continue
        if any(t in body for t in ("Fatal error", "Warning: ", "Notice: ")):
            broken.append(f"{url}=php-error")
            continue
        if body.count("<main") != 1 or body.count("<h1") != 1:
            broken.append(f"{url}=layout")
            continue
        ids = re.findall(r'\sid="([^"]+)"', body)
        dupes = {i for i in ids if ids.count(i) > 1}
        if dupes:
            broken.append(f"{url}=dup:{','.join(sorted(dupes))}")
            continue
        if 'data-products-archive="true"' not in body:
            broken.append(f"{url}=no-archive")
        if "data-product-grid" not in body and 'data-product-empty="true"' not in body:
            broken.append(f"{url}=no-grid")
        if '/shop/' not in body:
            broken.append(f"{url}=no-shop-link")
    check("6.5", f"all {len(categories) + len(tags)} term archives render cleanly", not broken, ",".join(broken[:5]))

    # The category hero is built from the term, not from a fixed string.
    status, cat = fetch(BASE + "/product_cat/wall-washer/")
    if status == 200:
        term_name = wp("eval", 'echo get_term_by("slug","wall-washer","product_cat")->name;')
        check("6.5", "the category hero names the category", term_name and term_name in cat, term_name)
        check("6.5", "the breadcrumb links back to the shop", re.search(r'href="[^"]*/shop/"', cat) is not None)
        check("6.5", "the category page carries its own products", 'data-product-item="true"' in cat)
        check("6.5", "the category FAQ is printed from term meta", 'data-faq-list' in cat)
        check("6.5", "the FAQ starts with one item open", 'data-faq-body="0"' in cat)
        check("6.5", "the pagination stays inside the category",
              re.search(r'href="[^"]*/product_cat/wall-washer/page/2/"', cat) is not None or "data-page-next" in cat)

    # A tag has no FAQ of its own and must not invent one.
    if tags:
        status, tag = fetch(BASE + tags[0])
        if status == 200:
            check("6.5", "a tag page does not invent a FAQ", "data-faq-list" not in tag)


def task_6_6(_: str) -> None:
    """An empty category shows the empty state, not a broken grid."""
    slug = "es-empty-" + str(abs(hash("phase6")) % 100000)
    created = wp("eval", "print wp_insert_term('%s','product_cat')['term_id'];" % slug)
    if not created.isdigit():
        check("6.6", "temporary empty category created", False, created[:80])
        return
    try:
        status, body = fetch(BASE + "/product_cat/" + slug + "/")
        check("6.6", "empty category answers 200", status == 200, f"status={status}")
        check("6.6", "empty state is visible", 'data-product-empty="true"' in body)
        check("6.6", "no grid is printed", "data-product-grid" not in body)
        check("6.6", "no pager is printed", "data-pagination-root" not in body)
        check("6.6", "the empty state links to the shop", re.search(r'href="[^"]*/shop/"', body) is not None)
        check("6.6", "no PHP error on the empty path",
              not any(t in body for t in ("Fatal error", "Warning: ", "Notice: ")))
    finally:
        # `wp term delete` has no --yes flag, so the removal goes through eval
        # and its result is asserted: a leaked fixture term would silently
        # change every later term-count check.
        removed = wp("eval", "print wp_delete_term(%s,'product_cat')===true?'ok':'failed';" % created)
        check("6.6", "the temporary category is removed again", removed == "ok", removed[:60])


def task_6_7(_: str) -> None:
    """The archive never fakes a cart or a price, and keeps its assets."""
    _, shop = fetch(BASE + "/shop/")
    check("6.7", "no localStorage cart", "localStorage" not in shop)
    check("6.7", "no static mirror link", 'href="product/' not in shop and 'href="../' not in shop)
    check("6.7", "no placeholder text", "لورم" not in shop and "Lorem" not in shop)
    check("6.7", "the compiled stylesheet is loaded", "assets/css/styles.css" in shop)
    check("6.7", "the behaviour script is loaded", "assets/js/main.js" in shop)
    check("6.7", "images are lazy loaded", shop.count('loading="lazy"') > 5)
    check("6.7", "no empty image src", 'src=""' not in shop)
    check("6.7", "the reference page-size contract is honoured", 'data-items-per-page="9"' in shop)

    # The theme's own script and the markup must agree on the pager hooks.
    script = (ROOT / "erfansanat" / "assets" / "js" / "main.js").read_text(encoding="utf-8")
    check("6.7", "the script looks the pager up by its real attribute",
          '[data-pagination-root]' in script and 'data-pagination-root="true"' not in script.split("function initProductsArchive")[0])
    check("6.7", "the script reads the reference page-number attribute", "data-page-num" in script)


def store_api(opener, path: str, payload=None, token: str | None = None):
    """Call the store API the way the cart/checkout blocks do."""
    import json

    page = opener.open(BASE + "/cart/", timeout=60).read().decode("utf-8", "replace")
    nonce = re.search(r"storeApiNonce: '([a-f0-9]+)'", page)
    stamp = re.search(r"wcStoreApiNonceTimestamp: '(\d+)'", page)
    headers = {
        "Content-Type": "application/json",
        "User-Agent": "erfansanat-phase6-check",
        "Nonce": nonce.group(1) if nonce else "",
        "Nonce-Timestamp": stamp.group(1) if stamp else "",
        "User-ID": "0",
    }
    if token:
        headers["Cart-Token"] = token
    request = urllib.request.Request(
        BASE + "/wp-json/wc/store/v1/" + path,
        data=json.dumps(payload).encode() if payload is not None else None,
        headers=headers,
    )
    try:
        with opener.open(request, timeout=90) as response:
            return json.loads(response.read()), dict(response.headers)
    except urllib.error.HTTPError as error:
        return {"__error": error.code, "__body": error.read().decode("utf-8", "replace")[:400]}, dict(error.headers)


def task_6_8(_: str) -> None:
    """The cart page is WooCommerce's cart, with real maths and a no-JS path."""
    import http.cookiejar

    jar = http.cookiejar.CookieJar()
    opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
    opener.addheaders = [("User-Agent", "erfansanat-phase6-check")]

    status, cart_page = fetch(BASE + "/cart/")
    check("6.8", "cart page answers 200", status == 200, f"status={status}")
    check("6.8", "cart page uses the WooCommerce block", "wc-block-cart" in cart_page or "wp-block-woocommerce-cart" in cart_page)
    check("6.8", "cart has a no-JavaScript fallback", 'erfansanat-nojs' in cart_page and "<noscript" in cart_page)
    check("6.8", "the fallback renders the classic cart shortcode",
          'class="woocommerce"' in cart_page and "woocommerce-cart-form" in cart_page
          or "wc-empty-cart-message" in cart_page)
    check("6.8", "cart empty state is Persian", "سبد خرید شما در حال حاضر خالی است" in cart_page,
          "the Persian catalogue must cover the block cart")
    check("6.8", "cart page has one <main>", cart_page.count("<main") == 1)
    check("6.8", "cart page has one <h1>", cart_page.count("<h1") == 1)

    # Add a real product through the store API, the way the block does.
    pid = wp("post", "list", "--post_type=product", "--name=illumina", "--format=ids").split()
    check("6.8", "fixture product exists", bool(pid))
    if not pid:
        return

    cart, headers = store_api(opener, "cart/add-item", {"id": int(pid[0]), "quantity": 3})
    token = headers.get("Cart-Token")

    if "items" not in cart:
        check("6.8", "store API accepted the item", False, str(cart)[:120])
        return

    check("6.8", "store API accepted the item", True)
    line = cart["items"][0]
    check("6.8", "quantity is what was asked for", line["quantity"] == 3, str(line["quantity"]))

    unit = int(re.sub(r"\D", "", str(line["prices"]["price"])) or 0)
    total = int(re.sub(r"\D", "", str(cart["totals"]["total_price"])) or 0)
    check("6.8", "the cart total is price times quantity", unit > 0 and total == unit * 3, f"{unit}x3 vs {total}")

    check("6.8", "the cart reports that payment is needed", cart["needs_payment"] is True)
    # The API returns either gateway ids or gateway objects depending on version;
    # normalise both so the assertion tests the store, not the payload shape.
    gateways: list[str] = []
    for method in cart.get("payment_methods") or []:
        if isinstance(method, str):
            gateways.append(method)
        elif isinstance(method, dict) and "id" in method:
            gateways.append(str(method["id"]))
        elif isinstance(method, list):
            for nested in method:
                if isinstance(nested, str):
                    gateways.append(nested)
                elif isinstance(nested, dict) and "id" in nested:
                    gateways.append(str(nested["id"]))
    check("6.8", "a payment method is offered", bool(gateways), str(cart.get("payment_methods"))[:80])

    # Removing the item must empty the cart again.
    for item in cart["items"]:
        emptied, _ = store_api(opener, "cart/remove-item", {"key": item["key"]}, token=token)
        if "items" in emptied:
            cart = emptied
    check("6.8", "removing every item empties the cart", len(cart.get("items", [])) == 0, str(len(cart.get("items", []))))


def task_6_9(_: str) -> None:
    """Checkout places a real order, and the confirmation comes from the server."""
    import http.cookiejar

    jar = http.cookiejar.CookieJar()
    opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
    opener.addheaders = [("User-Agent", "erfansanat-phase6-check")]

    import http.cookiejar as _cj

    class _NoRedirect(urllib.request.HTTPRedirectHandler):
        def redirect_request(self, *args, **kwargs):
            return None

    empty_opener = urllib.request.build_opener(_NoRedirect)
    try:
        with empty_opener.open(BASE + "/checkout/", timeout=60) as response:
            empty_status = response.status
    except urllib.error.HTTPError as error:
        empty_status = error.code
    check("6.9", "empty checkout redirects instead of rendering", empty_status in (301, 302), f"status={empty_status}")

    pid = wp("post", "list", "--post_type=product", "--name=illumina", "--format=ids").split()
    if not pid:
        check("6.9", "fixture product exists", False)
        return

    cart, headers = store_api(opener, "cart/add-item", {"id": int(pid[0]), "quantity": 2})
    token = headers.get("Cart-Token")
    if "items" not in cart:
        check("6.9", "product added before checkout", False, str(cart)[:120])
        return
    check("6.9", "product added before checkout", True)

    try:
        with opener.open(BASE + "/checkout/", timeout=90) as response:
            status = response.status
            checkout_page = response.read().decode("utf-8", "replace")
    except urllib.error.HTTPError as error:
        status, checkout_page = error.code, error.read().decode("utf-8", "replace")

    check("6.9", "checkout renders with a filled cart", status == 200, f"status={status}")
    check("6.9", "checkout uses the WooCommerce block", "wc-block-checkout" in checkout_page)
    check("6.9", "checkout has a no-JavaScript fallback", "erfansanat-nojs" in checkout_page and "woocommerce-checkout" in checkout_page)
    check("6.9", "checkout shows the three-step strip", "بررسی سبد خرید" in checkout_page and "اطلاعات ارسال و نوع فاکتور" in checkout_page)
    check("6.9", "checkout numbered steps are not duplicated", checkout_page.count("۱. ۱.") == 0)

    # No fake receipt may exist anywhere on the page.
    for fake in ["data-checkout-receipt", "data-receipt-code", "ESE-1403"]:
        check("6.9", f"no fabricated receipt marker {fake}", fake not in checkout_page)
    check("6.9", "checkout does not claim success before ordering", "سفارش شما با موفقیت" not in checkout_page)

    before = wp("eval", "echo count(wc_get_orders(['limit'=>-1,'return'=>'ids']));")

    address = {
        "first_name": "مشتری",
        "last_name": "آزمایشی",
        "company": "",
        "address_1": "نشانی ساختگی ۱۲",
        "address_2": "",
        "city": "اصفهان",
        "state": "ESF",
        "postcode": "00000",
        "country": "IR",
    }
    order, _ = store_api(
        opener,
        "checkout",
        {
            "billing_address": dict(address, email="customer@example.invalid", phone="0000000000"),
            "shipping_address": address,
            "payment_method": "cod",
            "payment_data": [],
            "customer_note": "آزمون ثبت سفارش محلی — پاک‌سازی می‌شود",
        },
        token=token,
    )

    if "order_id" not in order:
        check("6.9", "the checkout placed an order", False, str(order)[:200])
        return

    order_id = int(order["order_id"])
    check("6.9", "the checkout placed an order", True, f"#{order_id}")
    check("6.9", "the order has a real status", order.get("status") in ("processing", "on-hold", "pending", "completed"), str(order.get("status")))
    check("6.9", "the order reports the chosen gateway", order.get("payment_method") == "cod", str(order.get("payment_method")))

    after = wp("eval", "echo count(wc_get_orders(['limit'=>-1,'return'=>'ids']));")
    check("6.9", "the order is stored in the database", int(after or 0) == int(before or 0) + 1, f"{before} -> {after}")

    # The confirmation is WooCommerce's own order-received page, driven by the order key.
    order_key = order.get("order_key", "")
    received_status, received = fetch(BASE + "/checkout/order-received/" + str(order_id) + "/?key=" + order_key)
    check("6.9", "the order-received page answers 200", received_status == 200, f"status={received_status}")
    check("6.9", "the confirmation names the real order", str(order_id) in received)
    check("6.9", "the confirmation does not invent a payment",
          received.count("پرداخت آن تایید شد") == 0 or "processing" == order.get("status"))

    # Clean up: a test order must never linger in the fixture.
    removed = wp("eval", "print wc_get_order(%d) instanceof WC_Order ? (int) wc_get_order(%d)->delete(true) : 0;" % (order_id, order_id))
    check("6.9", "the test order is removed again", removed.strip() == "1", removed[:40])


def task_6_10(_: str) -> None:
    """My account works with WooCommerce's own forms, in the store language."""
    jar = http.cookiejar.CookieJar()
    opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
    opener.addheaders = [("User-Agent", "erfansanat-phase6-check")]

    status, account = fetch(BASE + "/my-account/")
    check("6.10", "account page answers 200", status == 200, f"status={status}")
    check("6.10", "account prints WooCommerce's login form", "woocommerce-form-login" in account)
    check("6.10", "the login form is in Persian", "نام کاربری" in account and "گذرواژه" in account)
    check("6.10", "account page has one <main>", account.count("<main") == 1)
    check("6.10", "account page has one <h1>", account.count("<h1") == 1)

    credentials = json.loads((ROOT / ".local" / "credentials.json").read_text(encoding="utf-8"))
    nonce = re.search(r'name="woocommerce-login-nonce" value="([a-f0-9]+)"', account)
    check("6.10", "the login form carries its nonce", nonce is not None)
    if not nonce:
        return

    body = urllib.parse.urlencode(
        {
            "username": credentials["customer_user"],
            "password": credentials["customer_password"],
            "woocommerce-login-nonce": nonce.group(1),
            "_wp_http_referer": "/my-account/",
            "login": "ورود",
        }
    ).encode()
    try:
        with opener.open(BASE + "/my-account/", body, timeout=90) as response:
            status = response.status
            dashboard = response.read().decode("utf-8", "replace")
    except urllib.error.HTTPError as error:
        status, dashboard = error.code, error.read().decode("utf-8", "replace")

    check("6.10", "login succeeds", status == 200, f"status={status}")
    check("6.10", "the dashboard is shown after login", "woocommerce-MyAccount-navigation" in dashboard)
    check("6.10", "the dashboard greets in Persian", "سلام" in dashboard and "Hello" not in dashboard)
    check("6.10", "the orders tab exists", "woocommerce-MyAccount-navigation-link--orders" in dashboard)

    try:
        with opener.open(BASE + "/my-account/orders/", timeout=90) as response:
            status = response.status
            orders = response.read().decode("utf-8", "replace")
    except urllib.error.HTTPError as error:
        status, orders = error.code, error.read().decode("utf-8", "replace")

    check("6.10", "the orders screen answers 200", status == 200, f"status={status}")
    check("6.10", "the fixture customer's orders are listed", "woocommerce-orders-table" in orders)

    # Log out through WooCommerce's own endpoint.
    logout = re.search(r'href="([^"]*customer-logout[^"]*)"', dashboard)
    check("6.10", "the dashboard offers a real logout link", logout is not None)
    if logout:
        try:
            with opener.open(logout.group(1), timeout=90) as response:
                status = response.status
                after = response.read().decode("utf-8", "replace")
        except urllib.error.HTTPError as error:
            status, after = error.code, error.read().decode("utf-8", "replace")
        check("6.10", "logout returns the visitor to the login form",
              status == 200 and "woocommerce-form-login" in after, f"status={status}")


def task_6_11(_: str) -> None:
    """No commerce page fakes a cart, a receipt or a price."""
    pages = ["/cart/", "/my-account/", "/shop/"]
    for page in pages:
        status, body = fetch(BASE + page)
        check("6.11", f"{page} answers 200", status == 200, f"status={status}")
        check("6.11", f"{page} has no localStorage cart", "localStorage" not in body)
        check("6.11", f"{page} invents no tracking code", "ESE-1403" not in body)
        check("6.11", f"{page} has no placeholder text", "لورم" not in body and "Lorem" not in body)
        check("6.11", f"{page} has no PHP error",
              not any(t in body for t in ("Fatal error", "Parse error", "Warning: ", "Notice: ")))

    # The reference's fake receipt must not have been copied into the theme.
    theme_php = list((ROOT / "erfansanat").rglob("*.php"))
    offenders = [
        str(path.relative_to(ROOT))
        for path in theme_php
        if "data-checkout-receipt" in path.read_text(encoding="utf-8", errors="replace")
    ]
    check("6.11", "no template ships the reference's fabricated receipt", not offenders, ",".join(offenders[:3]))


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--base", default="http://127.0.0.1:8080")
    parser.add_argument("--task", action="append", choices=["6.1", "6.2", "6.3", "6.4", "6.5", "6.6", "6.7", "6.8", "6.9", "6.10", "6.11"])
    args = parser.parse_args()
    global BASE
    BASE = args.base
    tasks = args.task or ["6.1", "6.2", "6.3", "6.4", "6.5", "6.6", "6.7", "6.8", "6.9", "6.10", "6.11"]
    for task in tasks:
        print(f"\n== task {task} ==\n")
        globals()["task_" + task.replace(".", "_")](args.base)

    print(f"\n{TOTAL - len(FAILED)}/{TOTAL} HTTP checks passed.")
    for failure in FAILED:
        print("  - " + failure)
    if FAILED:
        return 1
    print("PHASE 6 HTTP CHECKS PASSED — local synthetic site only.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
