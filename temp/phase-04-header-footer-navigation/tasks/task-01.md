# Task 4.1 — `head` سفارشی: `wp_head`، متا، Hreflang و JSON-LD پایه

| فیلد | مقدار |
|---|---|
| فاز | 4 — هدر، مگامنو، منوی موبایل و فوتر |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 1.9`، `Task 2.7` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-01.php` |

## ۱. هدف

`inc/Frontend/Head.php`: تزریق Hreflang از متای `hrefmeta_*`، متاهای OG/Twitter پایه (در نبود SEOPress)، و اسکیمای `Organization`/`WebSite`/`BreadcrumbList` مطابق استاتیک — بدون تداخل با SEOPress.

## ۲. فایل‌های هدف

- `inc/Frontend/Head.php`
- `inc/SEO/JsonLd.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Hreflang از `hrefmeta_fa/en/ar` + پیش‌فرض `home_url()`.
2. JSON-LD مطابق اسکریپت `ese-jsonld-schema` استاتیک (Organization/WebSite/Breadcrumb).
3. اگر SEOPress فعال است، OG/Twitter توسط افزونه تولید شود و ما تکرار نکنیم (`defined('SEOPRESS_VERSION')`).
4. خروجی JSON-LD با `wp_json_encode` و `wp_kses` مجاز `<script>`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — در نبود SEOPress، تگ‌های OG و Twitter موجود باشند
- [ ] **P2** — با فعال بودن SEOPress، هیچ تگ تکراری OG تولید نشود
- [ ] **P3** — Hreflang سه زبان و تنها یک‌بار
- [ ] **P4** — JSON-LD معتبر (`json_decode` موفق) و شامل Organization + WebSite

## ۵. تست‌های خودکار

- `OG شرطی`
- `عدم تکرار`
- `Hreflang`
- `اعتبار JSON-LD`

## ۶. چرخه اجرایی (۶ گام اجباری)

| گام | اقدام |
|---|---|
| `[READ MEMORY]` | `temp/PROJECT_MEMORY.md` + `temp/STATIC_TEMPLATE_MAP.md` + این فایل |
| `[THINK & PLAN]` | تحلیل مارک‌آپ/داده مربوطه و طرح اتصال |
| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` (تبدیل `pending()` به `check()`) |
| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM/هوک |
| `[RUN TEST]` | اجرا تا ۱۰۰٪ PASS + ثبت در `QA_REFLECTION_LOG.md` |
| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md` + کامیت |

## ۷. ضدالگوها (Anti-patterns) — انجام نده

- طراحی مجدد مارک‌آپ یا افزودن کلاس Tailwind جدید (قانون R1)
- تغییر فایل `assets/js/main.js` یا `assets/css/styles.css` (md5 باید ثابت بماند)
- استفاده از `TODO`/`...`/حذف بخشی از HTML برای کوتاه‌سازی (قانون R6)
- خواندن/نوشتن متا با املای «تصحیح‌شده» (مثل `english_title` به‌جای `english_titl`)
