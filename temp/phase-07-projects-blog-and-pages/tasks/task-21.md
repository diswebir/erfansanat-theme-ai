# Task 7.21 — برگه «فرصت‌های شغلی» (`template-recruitment.php`)

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.11` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-21.php` |

## ۱. هدف

برگه استخدام با تب‌های واحدها (`initRecruitmentTabs`), شرح موقعیت و فرم ارسال رزومه (Nonce + Honeypot، منطق در ۸.۵).

## ۲. فایل‌های هدف

- `page-templates/template-recruitment.php`
- `inc/Sections/Schema/recruitment.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Repeater موقعیت‌ها (واحد، عنوان، شرح، شرایط، لینک درخواست).
2. تب‌ها با `data-*` موجود.
3. فرم با فیلد رزومه (فقط PDF، حداکثر ۵MB) — پردازش در ۸.۵.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — تب‌ها با JS موجود کار کنند
- [ ] **P2** — فرم بدون JS قابل ارسال باشد
- [ ] **P3** — محدودیت نوع/حجم فایل در فرانت و بک‌اند اعمال شود

## ۵. تست‌های خودکار

- `تب‌ها`
- `فرم بدون JS`
- `محدودیت فایل`

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
