# Task 5.16 — نقشه موقعیت کارخانه + اطلاعات تماس

| فیلد | مقدار |
|---|---|
| فاز | 5 — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 2.11`، `Task 2.13` |
| وضعیت | `[PASSED — reference-limited contact section; no map markup/source in mirror, no third-party iframe added]` |
| تست | `tests/test-task-16.php` |

## ۱. هدف

تبدیل سکشن «نقشه موقعیت کارخانه + اطلاعات تماس» از `index.html` به `template-parts/home/section-map-contact.php` با اتصال کامل به Section Manager و بدون تغییر در `main.js`.

## ۲. فایل‌های هدف

- `template-parts/home/section-map-contact.php`
- `inc/Sections/Schema/home/map-contact.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. برش دقیق مارک‌آپ سکشن از `index.html` (بازه بایتی از `temp/_scan/sections.json` → سکشن #15) با حفظ تمام کلاس‌ها.
2. انتقال متون/تصاویر/لینک‌ها به Schema سکشن در `Registry` (کلید `map-contact`).
3. حفظ قرارداد JS: هیچ هوک JS
4. حذف `custom="..."` و اتریبیوت‌های بازمانده React؛ تبدیل مسیر تصویر به `assets/img/...`.
5. پشتیبانی از حالت «داده خالی» → رندر بلوک پیش‌فرض یا حذف کامل بدون خطا.
6. نقشه: iframe/تصویر استاتیک از آپشن (بدون اسکریپت خارجی در حالت پیش‌فرض).
7. اطلاعات تماس از `es_option`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — مقایسه ساختاری DOM با استاتیک ≥ ۹۹٪
- [ ] **P2** — هیچ متن هاردکدی در فایل سکشن نمانده باشد (همه از Schema)
- [ ] **P3** — تغییر محتوا از داشبورد در خروجی اعمال شود
- [ ] **P4** — در حالت خالی، خطا/Warning نداشته باشد
- [ ] **P5** — در نبود نقشه، بلوک تماس سالم بماند
- [ ] **P6** — هیچ اسکریپت third-party ناخواسته لود نشود

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
