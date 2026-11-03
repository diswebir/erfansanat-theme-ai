# Task 5.2 — هیرو واریانت home1 (اسلایدر صحنه + ویدیو + نوار دسته‌بندی)

| فیلد | مقدار |
|---|---|
| فاز | 5 — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.11`، `Task 2.13` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-02.php` |

## ۱. هدف

تبدیل سکشن «هیرو واریانت home1 (اسلایدر صحنه + ویدیو + نوار دسته‌بندی)» از `index.html` به `template-parts/home/section-hero.php` با اتصال کامل به Section Manager و بدون تغییر در `main.js`.

## ۲. فایل‌های هدف

- `template-parts/home/section-hero.php`
- `inc/Sections/Schema/home/hero.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. برش دقیق مارک‌آپ سکشن از `index.html` (بازه بایتی از `temp/_scan/sections.json` → سکشن #1) با حفظ تمام کلاس‌ها.
2. انتقال متون/تصاویر/لینک‌ها به Schema سکشن در `Registry` (کلید `hero`).
3. حفظ قرارداد JS: `data-home1-scene-img`, `data-home1-scene-btn`, `data-home1-overlay-item`, `data-home1-inline-video`, `data-home1-play-trigger`, `data-home1-video-close/mute/controls`, `data-home1-bottom-tab`, `data-home1-bottom-fillet`, `data-home1-categories-grid`, `data-home1-scene-title/cta/link`
4. حذف `custom="..."` و اتریبیوت‌های بازمانده React؛ تبدیل مسیر تصویر به `assets/img/...`.
5. پشتیبانی از حالت «داده خالی» → رندر بلوک پیش‌فرض یا حذف کامل بدون خطا.
6. تولید صحنه‌ها از Repeater داشبورد (تصویر، عنوان، لینک، CTA، is_video) با ایندکس صحیح `data-home1-scene-img="0..N"`.
7. ویدیو از `assets/video/hero-video.mp4` یا مقدار آپشن (پیش‌فرض G-A: فایل قالب).
8. دسته‌بندی‌های پایین هیرو از `product_cat` واقعی (نه ۵ آیتم hardcode) با fallback استاتیک.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — مقایسه ساختاری DOM با استاتیک ≥ ۹۹٪
- [ ] **P2** — هیچ متن هاردکدی در فایل سکشن نمانده باشد (همه از Schema)
- [ ] **P3** — تغییر محتوا از داشبورد در خروجی اعمال شود
- [ ] **P4** — در حالت خالی، خطا/Warning نداشته باشد
- [ ] **P5** — تعداد صحنه‌ها و ایندکس‌ها با `main.js` سازگار باشد (۰-based پیوسته)
- [ ] **P6** — ویدیو با پوستر و `preload="metadata"` رندر شود
- [ ] **P7** — در نبود ویدیو، دکمه play رندر نشود

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
