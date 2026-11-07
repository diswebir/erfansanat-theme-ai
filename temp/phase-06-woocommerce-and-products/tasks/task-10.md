# Task 6.10 — تب‌های صفحه محصول (`data-prod-tab-*`) و نظرات

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 6.2` |
| وضعیت | `[PASSED — 3 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-10.php` |

## ۱. هدف

تب‌های معرفی / مشخصات / شناسه‌ها / نظرات با همان آکاردئون و `data-prod-tab-btn|panel|icon|container`, به‌همراه فرم نظر با ستاره‌دهی (`data-review-star*`) و شمارنده نظرات.

## ۲. فایل‌های هدف

- `template-parts/product/tabs.php`
- `template-parts/product/reviews.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. ساختار تب با `aria-selected`/`role="tab"` صحیح.
2. نظرات با `comments_template` و اورراید `woocommerce/single-product-reviews.php`.
3. فرم نظر ووکامرس با امتیازدهی (بدون نوشتن فرم جدید).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — تب‌ها با JS موجود سوییچ شوند
- [ ] **P2** — فرم نظر ووکامرس کامل کار کند (ثبت نظر تست‌شده)
- [ ] **P3** — دسترسی‌پذیری: `aria-*` و کلیدهای جهت

## ۵. تست‌های خودکار

- `سوییچ تب`
- `ثبت نظر`
- `aria`

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
