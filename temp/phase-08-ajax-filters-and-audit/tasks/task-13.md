# Task 8.13 — ممیزی JSON-LD (Organization, WebSite, Breadcrumb, Product, Article, FAQPage)

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 4.1` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-13.php` |

## ۱. هدف

تولید اسکیمای ساختاری برای محصول/مقاله/پروژه/برگه FAQ و تطابق با اسکریپت `ese-jsonld-schema` استاتیک.

## ۲. فایل‌های هدف

- `inc/SEO/JsonLd.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Product: name, image, description, brand, sku/mpn/gtin (`gs1_id`), offers (price/availability).
2. Article: headline, datePublished/dateModified, author, image.
3. BreadcrumbList در همه صفحات داخلی.
4. FAQPage فقط در صفحات دارای FAQ.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — همه اسکیماها با `json_decode` معتبر باشند
- [ ] **P2** — `gtin13` از `gs1_id` (در صورت معتبر بودن) پر شود
- [ ] **P3** — بدون تکرار اسکیما با SEOPress

## ۵. تست‌های خودکار

- `اعتبار JSON`
- `gtin`
- `عدم تکرار`

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
