# Task 6.3 — گالری محصول + لایت‌باکس

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 6.2`، `Task 3.10` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-03.php` |

## ۱. هدف

گالری با زنجیره fallback (`galerry_produ` → `_product_image_gallery` → `gallery-filed` → `image-2..6`) و همان هوک‌های `data-gallery-*` استاتیک، شامل لایت‌باکس و شمارنده.

## ۲. فایل‌های هدف

- `template-parts/product/gallery.php`
- `inc/Woo/ProductGallery.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. تولید thumbnail و main image با `data-gallery-src` صحیح.
2. حذف تصاویر تکراری/نامعتبر.
3. تصویر شاخص محصول به‌عنوان تصویر اول اگر گالری خالی است.
4. alt متنی معنادار (نام محصول + شماره).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — گالری با داده هر چهار کلید قدیمی کار کند
- [ ] **P2** — لایت‌باکس با JS موجود باز/بسته شود
- [ ] **P3** — محصول بدون گالری: فقط تصویر شاخص (بدون خطا)
- [ ] **P4** — تصاویر `loading="lazy"` به‌جز تصویر اول

## ۵. تست‌های خودکار

- `fallback زنجیره‌ای`
- `لایت‌باکس`
- `بدون گالری`
- `lazy`

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
