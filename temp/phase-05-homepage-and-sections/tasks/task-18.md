# Task 5.18 — کارت‌های استاندارد: `card-product`, `card-project`, `card-post`

| فیلد | مقدار |
|---|---|
| فاز | 5 — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 5.8`، `Task 5.14` |
| وضعیت | `[PASSED — archive/search and Woo archive/related mock consumers; live Woo unverified]` |
| تست | `tests/test-task-18.php` |

> پیشرفت: `card-product`, `card-project`, `card-post` از fragmentهای reference رندر می‌شوند. آرشیو/fallback جستجو در `index.php` هر سه نوع را dispatch می‌کند و `woocommerce/content-product.php` همان کارت محصول را در حلقه‌های آرشیو محصول و related-products وصل می‌کند؛ data hooks برای فیلتر محصول حفظ شده و callbackهای core که markup تکراری/لینک تو‌در‌تو می‌ساختند موقتاً حذف و سپس بازیابی می‌شوند. تست‌های mock: **10 PASS**؛ نصب زنده WooCommerce/browser هنوز smoke نشده است.

## ۱. هدف

سه فایل کارت مشترک با **دقیقاً** همان مارک‌آپ کارت‌های استاتیک و پارامترهای ورودی (`$args`) — مصرف‌کننده در صفحه اصلی، آرشیوها، مرتبط‌ها و جستجو.

## ۲. فایل‌های هدف

- `template-parts/cards/card-product.php`
- `template-parts/cards/card-project.php`
- `template-parts/cards/card-post.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. استخراج مارک‌آپ کارت از `products.html` (`data-product-item`) و `projects.html`/`blog.html`.
2. پارامترها: `post_id`/`term_id`, `show_excerpt`, `size`, `badge`, `class`.
3. پر کردن `data-product-*` از داده واقعی برای فیلتر سمت کلاینت.
4. بدون `h2` تکراری در صفحه (سلسله‌مراتب عنوان).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — کارت محصول خروجی برابر نمونه استاتیک (کلاس‌ها)
- [ ] **P2** — همه `data-product-*` پر شوند
- [ ] **P3** — کارت در ۳ مصرف‌کننده بدون خطا رندر شود
- [ ] **P4** — هیچ کارت بدون تصویر، خطا ندهد (placeholder)

## ۵. تست‌های خودکار

- `انطباق کارت`
- `پر بودن هوک‌ها`
- `سه مصرف‌کننده`
- `حالت بدون تصویر`

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
