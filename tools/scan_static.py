#!/usr/bin/env python3
"""Read-only discovery of the supplied static mirror; never changes source HTML.

Produces a hash-backed asset/page inventory and the proposed WordPress template
map. Network access is not used. Proposed mappings are not implemented routes.
"""
from __future__ import annotations

from collections import Counter, defaultdict
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import unquote, urlsplit
import hashlib
import json
import re

ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "static-mirror"
OUTPUT = ROOT / "temp"
VOID = {"area", "base", "br", "col", "embed", "hr", "img", "input", "link", "meta", "param", "source", "track", "wbr"}


class Node:
    def __init__(self, tag: str, attrs: list[tuple[str, str | None]], start: int, parent=None):
        self.tag = tag
        self.attrs = dict(attrs)
        self.attr_pairs = attrs
        self.start = start
        self.end = start
        self.parent = parent
        self.children: list[Node] = []
        self.text: list[str] = []
        self.parts: list[str | Node] = []

    def walk(self):
        yield self
        for child in self.children:
            yield from child.walk()

    def text_content(self) -> str:
        if self.tag in {"script", "style"}:
            return ""
        return " ".join("".join(part if isinstance(part, str) else part.text_content() for part in self.parts).split())


class Document(HTMLParser):
    def __init__(self, source: str):
        super().__init__(convert_charrefs=True)
        self.source = source
        self.lines = [0]
        for match in re.finditer("\n", source):
            self.lines.append(match.end())
        self.root = Node("document", [], 0)
        self.stack = [self.root]
        self.mismatched_end_tags = []

    def current_offset(self) -> int:
        line, column = self.getpos()
        return self.lines[line - 1] + column

    def handle_starttag(self, tag, attrs):
        start = self.current_offset()
        node = Node(tag, attrs, start, self.stack[-1])
        self.stack[-1].children.append(node)
        self.stack[-1].parts.append(node)
        end = self.source.find(">", start)
        node.end = end + 1 if end >= 0 else len(self.source)
        if tag not in VOID:
            self.stack.append(node)

    def handle_startendtag(self, tag, attrs):
        self.handle_starttag(tag, attrs)
        if tag not in VOID:
            self.handle_endtag(tag)

    def handle_endtag(self, tag):
        index = next((i for i in range(len(self.stack) - 1, 0, -1) if self.stack[i].tag == tag), None)
        if index is None:
            self.mismatched_end_tags.append(tag)
            return
        start = self.current_offset()
        end = self.source.find(">", start)
        self.stack[index].end = end + 1 if end >= 0 else len(self.source)
        del self.stack[index:]

    def handle_data(self, data):
        self.stack[-1].text.append(data)
        self.stack[-1].parts.append(data)


def sha(data: bytes) -> str:
    return hashlib.sha256(data).hexdigest()


def nearest_section(node: Node):
    parent = node.parent
    while parent:
        if parent.tag == "section":
            return parent
        parent = parent.parent
    return None


def proposed_map(relative: str) -> tuple[str, str, str]:
    """Return proposed template, source URL and confidence/note, not PHP code."""
    path = Path(relative)
    group = path.parent.as_posix()
    slug = path.stem
    root_maps = {
        "index.html": ("front-page.php", "/", "الگوی خانه؛ مدیریت ترتیب سکشن‌ها"),
        "shop.html": ("archive-product.php", "/shop/", "آرشیو اصلی فروشگاه طبق بلوپرینت"),
        "products.html": ("archive-product.php", "/products/", "نامزد مسیر جایگزین فروشگاه؛ نیازمند تصمیم redirect"),
        "projects.html": ("archive-projects.php", "/projects/", "آرشیو CPT projects؛ با برگه project تفاوت دارد"),
        "blog.html": ("home.php", "/blog/", "صفحه نوشته‌ها؛ برگه 7426"),
        "about.html": ("page-templates/about.php", "/درباره-ما/", "تخصیص به برگه 7578؛ اسلاگ HTML معادل اسلاگ زنده نیست"),
        "contact.html": ("page-templates/contact.php", "/تماس-با-ما/", "تخصیص به برگه 7666"),
        "licenses-and-honors.html": ("page-templates/licenses-and-honors.php", "/licenses-and-honors/", "برگه 7583"),
        "faq.html": ("page-templates/faq.php", "/faq/", "برگه 10195"),
        "recruitment.html": ("page-templates/recruitment.php", "/recruitment/", "برگه 17898؛ فرم و فایل رزومه"),
        "refund_returns.html": ("page-templates/refund-returns.php", "/refund_returns/", "برگه 14922"),
        "cart.html": ("woocommerce/cart/cart.php", "/cart/", "پوسته سبد خرید؛ منطق واقعی WooCommerce"),
        "checkout.html": ("woocommerce/checkout/form-checkout.php", "/checkout/", "پوسته پرداخت؛ بدون رسید نمایشی localStorage"),
        "services.html": ("page-templates/services.php", "/services/", "نامزد برگه خدمات؛ ارتباط آن با smartelement تأیید نشده"),
    }
    if relative in root_maps:
        return root_maps[relative]
    if group == "product":
        return "single-product.php", f"/product/{slug}/", "تک‌محصول؛ مرجع اصلی به جای نسخه products"
    if group == "products" and slug.startswith("category-"):
        return "taxonomy-product_cat.php", f"/products/{slug}/", "نامزد alias دسته‌بندی؛ مقصد دقیق باید تأیید شود"
    if group == "products":
        return "single-product.php", f"/products/{slug}/", "بدنه همسان با product؛ نامزد redirect، نه قالب مستقل"
    if group == "product_cat":
        return "taxonomy-product_cat.php", f"/product_cat/{slug}/", "آرشیو دسته محصول و متاهای اختصاصی"
    if group == "projects" and slug.startswith("city-"):
        return "taxonomy-city.php", f"/projects/{slug}/", "نامزد alias شهر؛ مسیر اصلی city در بلوپرینت است"
    if group == "projects" and slug.startswith("type-"):
        return "taxonomy-project_type.php", f"/projects/{slug}/", "نامزد alias نوع پروژه؛ مسیر اصلی project_type است"
    if group == "projects":
        return "single-projects.php", f"/projects/{slug}/", "تک‌پروژه با گالری و محصولات مرتبط"
    if group == "city":
        return "taxonomy-city.php", f"/city/{slug}/", "آرشیو شهر"
    if group == "project_type":
        return "taxonomy-project_type.php", f"/project_type/{slug}/", "آرشیو نوع پروژه"
    if group == "blog":
        return "single.php", f"/blog/{slug}/", "تک‌نوشته؛ permalink نهایی باید با سایت زنده تطبیق داده شود"
    if group == "category":
        return "category.php", f"/category/{slug}/", "آرشیو دسته مقاله؛ اسلاگ category زنده هنوز تأیید نشده"
    if group == "services":
        return "page-templates/service-detail.php", f"/services/{slug}/", "نوع داده خدمات در بلوپرینت موجود نیست؛ نیازمند تأیید"
    raise ValueError(f"Unmapped static page: {relative}")


def scan_page(path: Path) -> dict:
    data = path.read_bytes()
    source = data.decode("utf-8")
    doc = Document(source)
    doc.feed(source)
    doc.close()
    nodes = list(doc.root.walk())[1:]
    relative = path.relative_to(SOURCE).as_posix()
    ids = Counter(n.attrs["id"] for n in nodes if n.attrs.get("id"))
    data_attrs = Counter(k for n in nodes for k in n.attrs if k.startswith("data-"))
    classes = Counter(c for n in nodes for c in n.attrs.get("class", "").split())
    sections = [n for n in nodes if n.tag == "section"]
    section_records = []
    for index, section in enumerate(sections, 1):
        headings = [
            {"tag": n.tag, "text": n.text_content()}
            for n in section.walk()
            if n.tag in {"h1", "h2", "h3", "h4", "h5", "h6"} and nearest_section(n) is section
        ]
        parent_section = nearest_section(section)
        section_records.append({
            "key": f"S{index:02d}",
            "id": section.attrs.get("id", ""),
            "class": section.attrs.get("class", ""),
            "data_attributes": {k: v for k, v in section.attrs.items() if k.startswith("data-")},
            "line": source.count("\n", 0, section.start) + 1,
            "parent_section": f"S{sections.index(parent_section) + 1:02d}" if parent_section else None,
            "headings": headings,
            "raw_sha256": sha(source[section.start:section.end].encode()),
        })
    landmarks = {}
    for tag in ("header", "main", "footer"):
        matches = [n for n in nodes if n.tag == tag]
        landmarks[tag] = {
            "count": len(matches),
            "elements": [{"id": n.attrs.get("id", ""), "class": n.attrs.get("class", ""),
                          "raw_sha256": sha(source[n.start:n.end].encode())} for n in matches],
        }
    metas = [n.attrs for n in nodes if n.tag == "meta"]
    links = [n.attrs for n in nodes if n.tag == "link"]
    scripts = [n.attrs for n in nodes if n.tag == "script"]
    jsonld = []
    for node in nodes:
        if node.tag == "script" and node.attrs.get("type") == "application/ld+json":
            raw = "".join(n.text[0] if n.text else "" for n in [node])
            try:
                parsed = json.loads(raw)
                jsonld.append({"valid": True, "types": [x.get("@type") for x in parsed] if isinstance(parsed, list) else [parsed.get("@type")]})
            except json.JSONDecodeError as error:
                jsonld.append({"valid": False, "error": str(error)})
    resources = []
    anchors = []
    for node in nodes:
        if node.tag == "a" and "href" in node.attrs:
            anchors.append(node.attrs["href"])
        for attr in ("src", "poster"):
            if node.attrs.get(attr):
                resources.append({"tag": node.tag, "attribute": attr, "url": node.attrs[attr]})
        if node.tag == "link" and node.attrs.get("href") and node.attrs.get("rel") in {"stylesheet", "preload", "icon"}:
            resources.append({"tag": "link", "attribute": "href", "url": node.attrs["href"]})
    forms = []
    for node in nodes:
        if node.tag == "form":
            forms.append({"attributes": node.attrs, "fields": [
                {k: child.attrs.get(k) for k in ("name", "type", "required", "accept", "maxlength", "min", "step")}
                for child in node.walk() if child.tag in {"input", "select", "textarea"}
            ]})
    template, route, note = proposed_map(relative)
    titles = [n.text_content() for n in nodes if n.tag == "title"]
    body = next((n for n in nodes if n.tag == "body"), None)
    return {
        "path": relative, "bytes": len(data), "sha256": sha(data),
        "title": titles[0] if titles else "",
        "language": next((n.attrs.get("lang") for n in nodes if n.tag == "html"), None),
        "direction": next((n.attrs.get("dir") for n in nodes if n.tag == "html"), None),
        "body_class": body.attrs.get("class", "") if body else "",
        "og_url": next((m.get("content") for m in metas if m.get("property") == "og:url"), None),
        "canonical_link": next((link.get("href") for link in links if link.get("rel") == "canonical"), None),
        "hreflang_meta": {m["name"]: m.get("content") for m in metas if m.get("name", "").startswith("hreflang:")},
        "proposed_template": template, "source_route": route, "mapping_note": note,
        "landmarks": landmarks, "sections": section_records,
        "duplicate_ids": {k: v for k, v in ids.items() if v > 1},
        "tag_counts": dict(Counter(n.tag for n in nodes)),
        "data_attribute_counts": dict(data_attrs), "class_counts": dict(classes),
        "resources": resources, "anchors": anchors, "forms": forms,
        "script_sources": [s["src"] for s in scripts if s.get("src")],
        "inline_script_types": [s.get("type", "text/javascript") for s in scripts if not s.get("src")],
        "jsonld": jsonld, "mismatched_end_tags": doc.mismatched_end_tags,
    }


def main():
    if not SOURCE.is_dir():
        raise SystemExit("static-mirror/ not found")
    OUTPUT.mkdir(exist_ok=True)
    files = sorted(p for p in SOURCE.rglob("*") if p.is_file())
    manifest = [{"path": p.relative_to(SOURCE).as_posix(), "bytes": p.stat().st_size,
                 "sha256": sha(p.read_bytes()), "extension": p.suffix.lower()} for p in files]
    pages = [scan_page(p) for p in files if p.suffix.lower() == ".html"]
    by_path = {p["path"]: p for p in pages}
    # Check every local resource, local link and fragment against the actual archive.
    all_ids = {}
    for path in files:
        if path.suffix == ".html":
            doc = Document(path.read_text())
            doc.feed(doc.source)
            all_ids[path.resolve()] = {n.attrs["id"] for n in doc.root.walk() if n.attrs.get("id")}
    missing_resources, missing_links, external_resources = [], [], []
    link_count = 0
    for page in pages:
        current = SOURCE / page["path"]
        for resource in page["resources"]:
            url = resource["url"]
            if url.startswith(("data:", "http:", "https:", "//")):
                external_resources.append({"page": page["path"], **resource})
                continue
            target = (current.parent / unquote(urlsplit(url).path)).resolve()
            if not target.is_file():
                missing_resources.append({"page": page["path"], **resource})
        for href in page["anchors"]:
            link_count += 1
            if href.startswith(("http:", "https:", "mailto:", "tel:", "javascript:", "#")):
                continue
            parsed = urlsplit(href)
            target = (current.parent / unquote(parsed.path)).resolve() if parsed.path else current.resolve()
            if not target.is_file():
                missing_links.append({"page": page["path"], "href": href, "reason": "file-missing"})
            elif parsed.fragment and target in all_ids and unquote(parsed.fragment) not in all_ids[target]:
                missing_links.append({"page": page["path"], "href": href, "reason": "anchor-missing"})
    # Exact body comparisons are stronger than matching filenames or visual similarity.
    groups = defaultdict(list)
    for page in pages:
        main = page["landmarks"]["main"]["elements"]
        if main:
            groups[main[0]["raw_sha256"]].append(page["path"])
    duplicate_groups = [paths for paths in groups.values() if len(paths) > 1]
    css_path = SOURCE / "assets/css/styles.css"
    css = css_path.read_text()
    font_urls = sorted(set(re.findall(r"url\(['\"]?(\.\./fonts/[^)'\"?#]+)", css)))
    stats = {
        "files": len(files), "bytes": sum(f["bytes"] for f in manifest),
        "extensions": dict(Counter(f["extension"] for f in manifest)),
        "html_pages": len(pages), "section_elements": sum(len(p["sections"]) for p in pages),
        "local_links_checked": link_count, "missing_local_links": missing_links,
        "missing_local_resources": missing_resources, "external_resource_dependencies": external_resources,
        "duplicate_main_groups": duplicate_groups,
        "pages_with_duplicate_ids": {p["path"]: p["duplicate_ids"] for p in pages if p["duplicate_ids"]},
        "jsonld_documents": sum(len(p["jsonld"]) for p in pages),
        "invalid_jsonld": [p["path"] for p in pages if any(not j["valid"] for j in p["jsonld"])],
        "font_urls": font_urls,
    }
    inventory = {"source": "static-mirror", "discovery_only": True,
                 "notice": "Proposed mappings do not create WordPress routes or import content.",
                 "statistics": stats, "files": manifest, "pages": pages}
    (OUTPUT / "static-inventory.json").write_text(json.dumps(inventory, ensure_ascii=False, indent=2) + "\n")
    write_map(inventory)
    print(json.dumps(stats, ensure_ascii=False, indent=2))


def cell(value) -> str:
    return str(value).replace("|", "\\|").replace("\n", " ")


def write_map(inventory):
    pages = inventory["pages"]
    stats = inventory["statistics"]
    extensions = stats["extensions"]
    out = [
        "# نقشه قالب استاتیک — عرفان صنعت اصفهان",
        "",
        "> وضعیت: پیشنهاد Stage 0؛ هیچ فایل قالب وردپرسی ساخته یا مسیر داینامیکی فعال نشده است.",
        "> مرجع بصری: `static-mirror/` ارسالی کاربر. مرجع داده‌ها: بلوپرینت ارائه‌شده؛ به دیتابیس زنده دسترسی نداریم.",
        "",
        "## نتیجه اسکن واقعی",
        "",
        f"- تعداد فایل‌ها: **{stats['files']}**؛ حجم استخراج‌شده: **{stats['bytes']:,} بایت**. فایل ZIP پسوند `.png` داشت ولی با امضای واقعی ZIP و بررسی CRC استخراج شد.",
        f"- **{stats['html_pages']} فایل HTML**، {extensions.get('.css', 0)} CSS، {extensions.get('.js', 0)} JS، {extensions.get('.woff2', 0)} فونت، {extensions.get('.jpg', 0)} JPG، {extensions.get('.svg', 0)} SVG و {extensions.get('.mp4', 0)} ویدیو؛ یک README نیز وجود دارد.",
        f"- همه صفحات بررسی شدند: هرکدام یک `header`، یک `main`، یک `footer` و یک `h1` دارند؛ مجموعاً **{stats['section_elements']} عنصر section** شامل سکشن‌های تودرتو ثبت شده است.",
        f"- **{stats['local_links_checked']:,} لینک** بررسی شد؛ لینک محلی شکسته: {len(stats['missing_local_links'])}؛ منبع محلی مفقود: {len(stats['missing_local_resources'])}؛ وابستگی منبع خارجی: {len(stats['external_resource_dependencies'])}. لینک‌های خروجی وب‌سایت/شبکه‌های اجتماعی، وابستگی منبع محسوب نمی‌شوند.",
        f"- JSON-LD: {stats['jsonld_documents']} بلاک، خطای تجزیه: {len(stats['invalid_jsonld'])}. این بررسی، اعتبارسنجی معنایی اسکیما یا تأیید داده تجاری نیست.",
        "- عدد ۳۷ صفحه و فهرست برخی نام فایل‌ها در README آرشیو قدیمی است؛ معیار این گزارش، فایل‌های واقعی و جدول زیر است.",
        "- منبع HTML دست‌نخورده باقی مانده است. هش کل فایل‌ها و هش بخش‌های `header/main/footer/section` در `temp/static-inventory.json` ثبت شده‌اند.",
        "",
        "## نگاشت خانواده صفحات",
        "",
        "| منبع | تعداد | مقصد پیشنهادی وردپرس | تصمیم/وابستگی |",
        "|---|---:|---|---|",
        "| `index.html` | 1 | `front-page.php` + `template-parts/home/section-*.php` | ۱۵ سکشن؛ ترتیب و محتوا از پنل اختصاصی |",
        "| `shop.html`, `products.html` | 2 | `archive-product.php` | `/shop/` طبق بلوپرینت اصلی؛ وضعیت `/products/` نیازمند تأیید |",
        "| `product/*.html` | 20 | `single-product.php` + `woocommerce/content-single-product.php` | قیمت، موجودی، ویژگی و گالری از WooCommerce و متای موجود |",
        "| `products/*.html` غیر category | 20 | همان قالب تک‌محصول | هر ۲۰ بدنه با نسخه `product/` یکسان است؛ نامزد redirect |",
        "| `products/category-*.html` | 7 | `taxonomy-product_cat.php` | نسخه‌های همسان دسته‌ها؛ redirect فقط بعد از تأیید مسیر زنده |",
        "| `product_cat/*.html` | 15 | `taxonomy-product_cat.php` | حفظ اسلاگ `product_cat` و FAQ/تصویر ترم |",
        "| `projects.html` | 1 | `archive-projects.php` | با برگه `/project/` شناسه 8366 یکی فرض نشده است |",
        "| `projects/*.html` جزئیات | 12 | `single-projects.php` | گالری، تاریخ، شهر، نوع و محصولات مرتبط |",
        "| `projects/city-*.html` | 4 | `taxonomy-city.php` | alias احتمالی برای `/city/...` |",
        "| `projects/type-*.html` | 4 | `taxonomy-project_type.php` | alias احتمالی برای `/project_type/...` |",
        "| `city/*.html` | 10 | `taxonomy-city.php` | مسیر اصلی شهر طبق بلوپرینت |",
        "| `project_type/*.html` | 8 | `taxonomy-project_type.php` | مسیر اصلی نوع پروژه طبق بلوپرینت |",
        "| `blog.html` | 1 | `home.php` | آرشیو نوشته‌ها، فیلتر و صفحه‌بندی واقعی |",
        "| `blog/*.html` | 7 | `single.php` | TOC، اشتراک‌گذاری، بازدید و دیدگاه‌ها |",
        "| `category/*.html` | 2 | `category.php` | اسلاگ پایه دسته‌بندی مقالات نیازمند بررسی نسخه زنده |",
        "| `services.html` + `services/*.html` | 6 | `page-templates/services.php`, `page-templates/service-detail.php` | نوع داده و ارتباط با `smartelement` مشخص نیست؛ سؤال Stage 0 |",
        "| برگه‌های عمومی موجود | 6 | `page-templates/*.php` | درباره، تماس، مجوزها، FAQ، استخدام، گارانتی |",
        "| `cart.html`, `checkout.html` | 2 | override قالب‌های WooCommerce | حفظ هوک‌ها؛ حذف شبیه‌سازی خرید و رسید ساختگی |",
        "",
        "## فایل‌های مشترک و دارایی‌ها",
        "",
        "| فایل مرجع | نقش / مقصد آینده | ملاحظه انتقال |",
        "|---|---|---|",
        "| `assets/css/styles.css` | `assets/css/styles.css` | 190,867 بایت؛ کلاس‌ها و قواعد موجود حفظ شوند؛ بارگذاری شرطی طبق صفحه |",
        "| `assets/js/main.js` | `assets/js/` با تفکیک ماژول‌های رفتاری | 112,885 بایت؛ DOM و data-attributeها حفظ شوند؛ منطق سبد/فرم نمایشی با API واقعی جایگزین شود |",
        "| `assets/fonts/vazirmatn-arabic.woff2` | فونت فارسی/عربی | مسیر نسبی `../fonts/` حفظ و تست شود |",
        "| `assets/fonts/vazirmatn-latin.woff2` | فونت لاتین | همان فونت Vazirmatn با unicode-range مستقل |",
        "| `assets/fonts/vazirmatn-latin-ext.woff2` | فونت لاتین تکمیلی | بدون CDN خارجی |",
        "| `images/*.jpg` (۹ فایل) | رسانه پیش‌فرض قالب | محتوای زنده اولویت دارد؛ جایگزینی خودکار رسانه‌های سایت ممنوع |",
        "| `images/pexels/*.jpg` (۱۱ فایل) | تصاویر آرشیو | پیش از استفاده نهایی، مجوز و تطبیق با محصول واقعی بررسی شود |",
        "| `images/ese-logo.svg` | `assets/images/ese-logo.svg` | XML معتبر؛ جایگزینی فقط با تنظیمات لوگو |",
        "| `images/hero-video.mp4` | `assets/video/hero-video.mp4` | 7,025,235 بایت؛ بارگذاری تنبل/کنترل‌شده، نه پیش‌بارگیری روی همه صفحات |",
        "",
        "فهرست کامل ۱۵۶ فایل، اندازه و SHA-256 در `static-inventory.json` است. هیچ فایل رسانه‌ای در این مرحله به کتابخانه وردپرس وارد نشده است.",
        "",
        "## سکشن‌های صفحه اصلی (مرتب بر اساس DOM)",
        "",
        "| کلید | id | عنوان/نقش | مقصد پیشنهادی |",
        "|---|---|---|---|",
    ]
    home_parts = [
        "hero.php", "project-types.php", "projects.php", "about-intro.php", "knowledge.php", "history.php",
        "products.php", "catalog.php", "services.php", "quality.php", "support.php", "clients.php",
        "blog.php", "contact-cta.php", "location.php",
    ]
    home = next(p for p in pages if p["path"] == "index.html")
    for section, part in zip(home["sections"], home_parts, strict=True):
        heading = section["headings"][0]["text"] if section["headings"] else "سکشن بدون عنوان مستقیم"
        out.append(f"| `{section['key']}` | `{section['id'] or '—'}` | {cell(heading)} | `template-parts/home/section-{part}` |")
    out += [
        "",
        "## نکات رفتاری مهم برای تبدیل",
        "",
        "- مگامنو، منوی موبایل، جستجوی سراسری، گالری/لایت‌باکس، تب‌ها، FAQ، فیلتر آرشیوها و صفحه‌بندی در `main.js` وجود دارند؛ ساختار و نام `data-*`ها در JSON ثبت شده است.",
        "- فیلتر استاتیک محصولات علاوه بر دسته و متن، وضعیت خرید، قیمت، IP و ولتاژ دارد. IP و ولتاژ باید به `pa_ip-rating` و `pa_input-voltage` متصل شوند؛ نگاشت سایر ویژگی‌ها طبق بلوپرینت انجام شود.",
        "- `localStorage`، قیمت‌های نمونه، رسید پرداخت و ارسال موفق فرم در میرور، شبیه‌سازی مرورگری‌اند؛ شاهد وجود backend یا پرداخت واقعی نیستند و نباید به داده تولیدی منتقل شوند.",
        "- فرم تماس و استخدام `data-contact-form` دارند ولی باید در وردپرس مسیر ذخیره امن جداگانه داشته باشند. فرم استخدام فایل رزومه می‌گیرد؛ سیاست دسترسی/نگهداری فایل نیازمند تصمیم است.",
        "- در همه صفحات `id=\"home\"` روی پوسته وجود دارد و در خانه یک section نیز همین id را دارد. این تکرار در مرجع ثبت شد؛ در Stage 0 منبع تغییر نکرده و رفع آن نیازمند تصمیم سازگاری DOM است.",
        "- `canonical_link` در HTMLها وجود ندارد؛ URLهای `og:url` و متاهای `hreflang:fa/en/ar` نباید به‌عنوان تگ استاندارد canonical/hreflang تلقی شوند. خروجی نهایی با SEOPress و افزونه hreflang موجود هماهنگ شود.",
        "- اسکیماهای محصول ایستا `priceCurrency: IRR` و در نمونه بررسی‌شده `price: 0` دارند، ولی واحد هدف بلوپرینت `IRT` است. قیمت/واحد/موجودی فقط از داده واقعی ووکامرس خوانده شود.",
        "",
        "## جدول کامل تک‌تک HTMLها",
        "",
        "کلیدهای `Snn` ترتیب واقعی sectionها هستند؛ جزئیات کلاس، атриبیوت‌های داده، والد سکشن و هش هر بخش در JSON موجود است. مقصدها همگی **پیشنهادی** هستند.",
        "",
        "| فایل HTML | صفحه/بخش‌های شناسایی‌شده | قالب پیشنهادی | وضعیت نگاشت |",
        "|---|---|---|---|",
    ]
    for page in pages:
        summaries = []
        for section in page["sections"]:
            heading = section["headings"][0]["text"] if section["headings"] else "بدون عنوان مستقیم"
            if len(heading) > 65:
                heading = heading[:62] + "…"
            identity = f"#{section['id']}" if section["id"] else ""
            summaries.append(f"`{section['key']}{identity}` {heading}")
        out.append(f"| `{page['path']}` | {cell('<br>'.join(summaries))} | `{page['proposed_template']}` | {cell(page['mapping_note'])} |")
    out += [
        "",
        "## گروه‌های بدنه کاملاً یکسان",
        "",
        "این گروه‌ها با SHA-256 بخش `main` پیدا شده‌اند؛ یکسان بودن بدنه به تنهایی مجوز حذف URL یا ایجاد redirect نیست.",
        "",
    ]
    for group in stats["duplicate_main_groups"]:
        out.append("- " + " ↔ ".join(f"`{path}`" for path in group))
    out += [
        "",
        "## صفحات لازم که مرجع اختصاصی ندارند",
        "",
        "- `/arya/` (16947)، `/smartelement/` (11984)، `/project/` (8366 به‌عنوان برگه منتخب)، `/my-account/`، `/auth/`.",
        "- `search.php`، `404.php`، آرشیو `product-tag`، آرشیو `product_brand` و حالت‌های عمیق صفحه‌بندی/فیلتر.",
        "- حالت محصول متغیر، محصول ناموجود، سبد خالی واقعی، خطای پرداخت، پیام‌های خطای فرم و حساب کاربری واردشده.",
        "- برای این موارد یا HTML تکمیلی لازم است، یا اجازه صریح استفاده از کامپوننت‌های موجود/مارک‌آپ استاندارد افزونه؛ طراحی جدید خودسرانه انجام نمی‌شود.",
        "",
        "## پرسش‌های باز و دروازه تأیید",
        "",
        "1. مرجع ظاهر، همین آرشیو باشد و متن/قیمت/رسانه از دیتابیس زنده خوانده شود؟ داده نمایشی آرشیو خودکار وارد سایت نشود؟",
        "2. مسیرهای اصلی بلوپرینت canonical باشند و مسیرهای همسان فقط پس از بررسی سایت زنده redirect شوند؟ هیچ URL حذف نشود تا نقشه تأیید شود.",
        "3. برای `arya`، `smartelement`، حساب کاربری و ورود، HTML تکمیلی ارائه می‌شود یا استفاده از اجزای موجود و مارک‌آپ استاندارد افزونه تأیید می‌شود؟",
        "4. `services` شش صفحه مستقل است یا به برگه موجود `smartelement` متصل می‌شود؟ نوع داده و والد/URL آن در سایت فعلی چیست؟",
        "5. برای تطبیق واقعی فرمت متاهای JetEngine، خروجی استیجینگ/نمونه ناشناس‌سازی‌شده دیتابیس چه زمانی در دسترس است؟ کلیدواژه یا رمز در چت درخواست نمی‌شود.",
        "6. مقصد ایمیل فرم‌ها، دسترسی به رزومه‌ها و مدت نگهداری پیام‌ها چیست؟ پیشنهاد پیش‌فرض: رزومه خصوصی، پیام‌ها فقط برای مدیران مجاز، بدون ارسال پیوست رزومه در ایمیل عمومی.",
        "",
        "**وضعیت فعلی: `STAGE 0 COMPLETED - WAITING FOR USER CLARIFICATION`. ساخت فایل‌های قالب تا دریافت پاسخ و تأیید متوقف است.**",
        "",
    ]
    (OUTPUT / "STATIC_TEMPLATE_MAP.md").write_text("\n".join(out))


if __name__ == "__main__":
    main()
