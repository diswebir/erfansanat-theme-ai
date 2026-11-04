# Task 6.13 — آرشیو برچسب و برند (`taxonomy-product_tag.php`, `taxonomy-product_brand.php`)

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 6.12` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-13.php` |

## ۱. هدف

آرشیو برچسب/برند با همان گرید و hero کوچک، شمارش و صفحه‌بندی.

## ۲. فایل‌های هدف

- `woocommerce/taxonomy-product_tag.php`
- `woocommerce/taxonomy-product_brand.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. استفاده مجدد از `grid.php` برای پرهیز از تکرار.
2. Hero با نام ترم و توضیحات.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — هر دو آرشیو رندر شوند
- [ ] **P2** — ترم بدون محصول، حالت خالی داشته باشد

## ۵. تست‌های خودکار

- `رندر`
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
