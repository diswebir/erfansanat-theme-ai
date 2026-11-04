# Task 5.3 — معرفی پروژه‌های نورپردازی شهری

| فیلد | مقدار |
|---|---|
| فاز | 5 — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | `Task 2.11`، `Task 2.13` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-03.php` |

## ۱. هدف

تبدیل سکشن «معرفی پروژه‌های نورپردازی شهری» از `index.html` به `template-parts/home/section-projects-intro.php` با اتصال کامل به Section Manager و بدون تغییر در `main.js`.

## ۲. فایل‌های هدف

- `template-parts/home/section-projects-intro.php`
- `inc/Sections/Schema/home/projects-intro.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. برش دقیق مارک‌آپ سکشن از `index.html` (بازه بایتی از `temp/_scan/sections.json` → سکشن #2) با حفظ تمام کلاس‌ها.
2. انتقال متون/تصاویر/لینک‌ها به Schema سکشن در `Registry` (کلید `projects-intro`).
3. حفظ قرارداد JS: هیچ هوک JS (سکشن استاتیک با ۳ کارت)
4. حذف `custom="..."` و اتریبیوت‌های بازمانده React؛ تبدیل مسیر تصویر به `assets/img/...`.
5. پشتیبانی از حالت «داده خالی» → رندر بلوک پیش‌فرض یا حذف کامل بدون خطا.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — مقایسه ساختاری DOM با استاتیک ≥ ۹۹٪
- [x] **P2** — هیچ متن هاردکدی در فایل سکشن نمانده باشد (همه از Schema)
- [x] **P3** — تغییر محتوا از داشبورد در خروجی اعمال شود
- [x] **P4** — در حالت خالی، خطا/Warning نداشته باشد

## ۵. تست‌های خودکار

- `diff ساختاری سکشن`
- `اسکن هاردکد`
- `اثر داشبورد`
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
