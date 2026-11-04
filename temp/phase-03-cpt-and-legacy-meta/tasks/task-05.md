# Task 3.5 — Flush نسخه‌دار + مانیتور ۴۰۴ پس از فعال‌سازی

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.4` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-05.php` |

## ۱. هدف

سیستم امن flush: فقط در `after_switch_theme` و هنگام تغییر `ES_REWRITE_VERSION`، به‌همراه ابزار گزارش ۴۰۴ برای اطمینان از حفظ ۱۰۰٪ URLها.

## ۲. فایل‌های هدف

- `inc/Content/Rewrites.php`
- `inc/Constants.php`
- `inc/Admin/UrlAudit.php`
- `inc/Theme.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. ذخیره `ES_REWRITE_VERSION` در آپشن و مقایسه با ثابت.
2. `flush_rewrite_rules(false)` (soft) در زمان لازم.
3. صفحه ابزار «ممیزی URL» که نقشه URLهای مهم را نشان دهد (shop, product_cat, projects, city, project_type, product).
4. لاگ ۴۰۴ داخلی (بدون کوکی) برای ادمین.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — پس از تغییر نسخه rewrite، فلش یک‌بار انجام شود
- [x] **P2** — در دو بار بارگذاری متوالی فلش انجام **نشود**
- [x] **P3** — گزارش ممیزی همه ۶ خانواده URL را پوشش دهد

## ۵. تست‌های خودکار

- `یک‌بار فلش`
- `عدم فلش تکراری`
- `پوشش ممیزی`

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
