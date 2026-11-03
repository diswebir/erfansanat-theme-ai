# Task 2.5 — ریفیلد Media Picker (کتابخانه رسانه)

| فیلد | مقدار |
|---|---|
| فاز | 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 2.4` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-05.php` |

## ۱. هدف

کنترل انتخاب تصویر/فایل بر پایه `wp.media` بدون وابستگی اضافه: انتخاب، پیش‌نمایش، حذف، ذخیره attachment ID (یا URL در فیلدهای فایل) و پشتیبانی چندتایی.

## ۲. فایل‌های هدف

- `assets/admin/media.js`
- `inc/Admin/Fields/types/media.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `wp_enqueue_media()` فقط در صفحات قالب.
2. دکمه انتخاب/حذف + پیش‌نمایش تصویر و نام فایل.
3. حالت `multiple` برای گالری؛ ذخیره CSV از IDها.
4. اعتبارسنجی سمت سرور: `attachment_exists`/`wp_get_attachment_url`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — انتخاب تصویر، ID صحیح را ذخیره کند
- [x] **P2** — ID نامعتبر (۹۹۹۹۹۹) سرور رد کند
- [x] **P3** — حالت چندتایی CSV صحیح بدهد
- [x] **P4** — حذف، مقدار را پاک کند (نه رشته `undefined`)

## ۵. تست‌های خودکار

- `ذخیره ID`
- `رد ID نامعتبر`
- `CSV چندتایی`
- `پاک‌سازی`

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
