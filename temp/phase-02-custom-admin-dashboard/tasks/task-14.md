# Task 2.14 — صادرات/واردات تنظیمات و `Reset` ایمن

| فیلد | مقدار |
|---|---|
| فاز | 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 2.1` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-14.php` |

## ۱. هدف

ابزار پشتیبان‌گیری JSON از `erfansanat_theme_options` + `_es_sections`، import با اعتبارسنجی schema، و reset انتخاب‌شده (فقط تب مربوطه) با تأیید دو مرحله‌ای.

## ۲. فایل‌های هدف

- `inc/Admin/ImportExport.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Export: `application/json` با `Content-Disposition` + nonce.
2. Import: `json_decode` + بازبینی schema (کلیدهای ناشناخته حذف) + گزارش تغییرات.
3. Reset: فقط کلیدهای یک تب، با nonce و تأیید.
4. پشتیبان‌گیری خودکار قبل از import در `erfansanat_theme_options_backup`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — export → import → خروجی یکسان (round-trip)
- [ ] **P2** — فایل JSON مخرب (کلید ناشناخته/نوع اشتباه) سرور را نشکند
- [ ] **P3** — reset فقط تب هدف را تغییر دهد

## ۵. تست‌های خودکار

- `round-trip`
- `فایل مخرب`
- `reset محدود`

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
