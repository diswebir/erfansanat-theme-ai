# Task 3.10 — تب فایل و گالری محصول + همگام‌سازی

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 3.9`، `Task 2.5` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-10.php` |

## ۱. هدف

فایل کاتالوگ (`product-catalog-file` به‌صورت URL) و گالری (`galerry_produ` CSV) با Fallback زنجیره‌ای `_product_image_gallery` → `gallery-filed` → `image-2..6`، و همگام‌سازی دوطرفه با گالری استاندارد ووکامرس.

## ۲. فایل‌های هدف

- `inc/Admin/Metaboxes/Tabs/ProductMediaTab.php`
- `inc/Woo/ProductGallery.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Media picker فایل (PDF) با ذخیره `wp_get_attachment_url`.
2. گالری چندتایی با ذخیره CSV ID.
3. Sync در `save_post` (هر دو جهت) با محافظ حلقه بی‌نهایت.
4. Reader نهایی `es_product_gallery()` با زنجیره fallback.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ذخیره گالری در `galerry_produ` و `_product_image_gallery` همگام شود
- [ ] **P2** — کلیشه قدیمی `gallery-filed`/`image-2..6` خوانده شود
- [ ] **P3** — URL کاتالوگ معتبر باشد (attachment موجود)
- [ ] **P4** — بدون گالری، خروجی آرایه خالی باشد (نه خطا)

## ۵. تست‌های خودکار

- `همگام‌سازی`
- `fallback قدیمی`
- `اعتبار کاتالوگ`
- `حالت خالی`

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
