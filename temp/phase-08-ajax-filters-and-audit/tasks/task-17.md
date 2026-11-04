# Task 8.17 — ممیزی سازگاری با افزونه‌های باقی‌مانده

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 8.15` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-17.php` |

## ۱. هدف

تست هم‌زیستی با SEOPress, LiteSpeed, WP-Parsidate, voorodak, fl-panel, hreflangpch, Variation Swatches, WPC Quantity — بدون خطا/تکرار خروجی.

## ۲. فایل‌های هدف

- `temp/phase-08-ajax-filters-and-audit/tests/plugin-compat.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. برای هر افزونه: بررسی هوک‌های مصرفی و تعارض احتمالی.
2. جلوگیری از خروجی تکراری (OG/Hreflang/Schema/بازدید).
3. مستندسازی تعارض‌ها در `temp/PLUGIN_COMPAT.md`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — بدون خروجی تکراری متا
- [ ] **P2** — بدون Fatal با فعال‌سازی هر افزونه (mock classes)
- [ ] **P3** — مستند سازگاری کامل

## ۵. تست‌های خودکار

- `بدون تکرار`
- `بدون Fatal`
- `مستند`

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
