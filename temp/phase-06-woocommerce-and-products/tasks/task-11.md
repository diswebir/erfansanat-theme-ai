# Task 6.11 — محصولات مشابه و مکمل

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 5.18` |
| وضعیت | `[PASSED — 3 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-11.php` |

## ۱. هدف

بلوک محصولات مشابه با `wc_get_related_products` (یا ترم مشترک) و کارت استاندارد؛ حذف در صورت خالی بودن.

## ۲. فایل‌های هدف

- `template-parts/product/related.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. حداکثر ۴ محصول، ترتیب تصادفی با seed پایدار.
2. حذف محصول جاری از لیست.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — بدون محصول مرتبط، سکشن حذف شود
- [ ] **P2** — محصول جاری در لیست نباشد
- [ ] **P3** — کارت‌ها با `card-product` رندر شوند

## ۵. تست‌های خودکار

- `حذف خالی`
- `عدم نمایش جاری`
- `کارت استاندارد`

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
