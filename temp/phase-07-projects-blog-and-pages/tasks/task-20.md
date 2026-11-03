# Task 7.20 — برگه «شرایط گارانتی» (`template-refund.php`)

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 2.11` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-20.php` |

## ۱. هدف

۳ سکشن سیاست‌نامه با ساختار تیتر/بند و ویرایش کامل از داشبورد (بدون نیاز به ویرایشگر متن).

## ۲. فایل‌های هدف

- `page-templates/template-refund.php`
- `inc/Sections/Schema/refund.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Repeater بندها (عنوان، متن) با `wp_kses_post`.
2. فهرست شماره‌دار با همان استایل استاتیک.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — همه بندها ویرایش‌پذیر باشند
- [ ] **P2** — HTML مجاز محدود شود (`wp_kses_post`)

## ۵. تست‌های خودکار

- `ویرایش‌پذیری`
- `kses`

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
