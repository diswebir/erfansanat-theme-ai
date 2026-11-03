# Task 8.6 — فرم تماس/مشاوره: پردازش، Honeypot، Nonce، ایمیل

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 8.5`، `Task 7.17` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-06.php` |

## ۱. هدف

اندپوینت `es_submit_form` (فرم تماس) با Nonce، Honeypot پنهان، زمان‌سنج حداقلی، محدودیت نرخ و ارسال ایمیل اعلان فارسی + پیام موفقیت/خطا در همان مارک‌آپ استاتیک.

## ۲. فایل‌های هدف

- `inc/Forms/FormHandler.php`
- `inc/Forms/Notifier.php`
- `assets/js/forms.js`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. اعتبارسنجی سمت سرور: ایمیل، تلفن ایرانی، طول پیام.
2. Honeypot + time-trap (حداقل ۳ ثانیه).
3. ذخیره در جدول + `wp_mail` با قالب HTML فارسی و Reply-To.
4. پاسخ JSON با `success/errors` و نمایش در `data-contact-success`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ارسال معتبر → ۲۰۰ + ردیف در DB + ایمیل (mock)
- [ ] **P2** — بدون nonce → ۴۰۳
- [ ] **P3** — Honeypot پر → رد بی‌صدا (۲۰۰ بدون ذخیره)
- [ ] **P4** — ارسال تکراری سریع → محدود شود

## ۵. تست‌های خودکار

- `ارسال موفق`
- `nonce`
- `honeypot`
- `rate limit`

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
