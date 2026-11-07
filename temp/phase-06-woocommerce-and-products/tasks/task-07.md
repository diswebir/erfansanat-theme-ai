# Task 6.7 — شناسه‌های قانونی (`gs1_id`, `national_code`, `hs_id`, `isic_id`, `cpc_id`, `product_id`)

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 6.2` |
| وضعیت | `[PASSED — 3 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-07.php` |

## ۱. هدف

بلوک «شناسه‌های رسمی کالا» (ویژه سامانه ستاد/تدارکات دولتی) با نمایش شرطی هر شناسه و کپی‌به‌کلیپ‌بورد بدون کتابخانه.

## ۲. فایل‌های هدف

- `template-parts/product/legal-ids.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. جدول شناسه‌ها با `esc_html`.
2. حذف سطرهای خالی.
3. دکمه کپی با `navigator.clipboard` + fallback.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — همه ۶ شناسه با داده ظاهر شوند
- [ ] **P2** — خالی → سکشن حذف
- [ ] **P3** — کپی بدون خطای کنسول

## ۵. تست‌های خودکار

- `شش شناسه`
- `حذف خالی`
- `کپی`

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
