# Task 5.7 — تاریخچه شرکت (۱۳۸۰ تا ۱۴۰۵) با اسکرول تعاملی

| فیلد | مقدار |
|---|---|
| فاز | 5 — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.11`، `Task 2.13` |
| وضعیت | `[PASSED — 10 mock checks including jsdom next/previous/wheel; live WP/browser unverified]` |
| تست | `tests/test-task-07.php` |

## ۱. هدف

تبدیل سکشن «تاریخچه شرکت (۱۳۸۰ تا ۱۴۰۵) با اسکرول تعاملی» از `index.html` به `template-parts/home/section-timeline.php` با اتصال کامل به Section Manager و بدون تغییر در `main.js`.

## ۲. فایل‌های هدف

- `template-parts/home/section-timeline.php`
- `inc/Sections/Schema/home/timeline.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. برش دقیق مارک‌آپ سکشن از `index.html` (بازه بایتی از `temp/_scan/sections.json` → سکشن #6) با حفظ تمام کلاس‌ها.
2. انتقال متون/تصاویر/لینک‌ها به Schema سکشن در `Registry` (کلید `timeline`).
3. حفظ قرارداد JS: `data-home2-history-btn/panel/prev/next`, `data-history-scroll-track`, `data-history-total`, `data-history-stage`, `data-history-step-label`, `data-history-progress-bar`
4. حذف `custom="..."` و اتریبیوت‌های بازمانده React؛ تبدیل مسیر تصویر به `assets/img/...`.
5. پشتیبانی از حالت «داده خالی» → رندر بلوک پیش‌فرض یا حذف کامل بدون خطا.
6. گره‌های تاریخچه از Repeater (سال، عنوان، توضیح، تصویر).
7. `data-history-total` باید با تعداد گره‌ها برابر باشد.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — مقایسه ساختاری DOM با استاتیک ≥ ۹۹٪
- [ ] **P2** — هیچ متن هاردکدی در فایل سکشن نمانده باشد (همه از Schema)
- [ ] **P3** — تغییر محتوا از داشبورد در خروجی اعمال شود
- [ ] **P4** — در حالت خالی، خطا/Warning نداشته باشد
- [ ] **P5** — `data-history-total` = تعداد واقعی گره‌ها
- [ ] **P6** — اسکرول/دکمه‌های قبل-بعد با JS موجود کار کنند

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
