# Task 5.10 — خدمات تخصصی (ردیف‌های تعاملی)

| فیلد | مقدار |
|---|---|
| فاز | 5 — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.11`، `Task 2.13` |
| وضعیت | `[IN PROGRESS — P1–P6 mock criteria verified; per-service destination URL unresolved under reference button/JS constraints]` |
| تست | `tests/test-task-10.php` |

> پیشرفت: پنج خدمت اکنون در Repeater ویرایش‌پذیرند؛ عنوان/توضیح/تصویر و counterها به JS hooks متصل و تست شده‌اند. شرط لینک‌دهی به صفحه خدمت هنوز باز است، چون reference برای ردیف‌ها `<button>` تعاملی دارد و URL ردیف در DOM/مرجع موجود نیست.

## ۱. هدف

تبدیل سکشن «خدمات تخصصی (ردیف‌های تعاملی)» از `index.html` به `template-parts/home/section-services.php` با اتصال کامل به Section Manager و بدون تغییر در `main.js`.

## ۲. فایل‌های هدف

- `template-parts/home/section-services.php`
- `inc/Sections/Schema/home/services.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. برش دقیق مارک‌آپ سکشن از `index.html` (بازه بایتی از `temp/_scan/sections.json` → سکشن #9) با حفظ تمام کلاس‌ها.
2. انتقال متون/تصاویر/لینک‌ها به Schema سکشن در `Registry` (کلید `services`).
3. حفظ قرارداد JS: `data-service-row`, `data-service-title`, `data-service-desc-text`, `data-service-img`, `data-service-counter`, `data-service-indicator`, `data-service-num`, `data-service-heading`, `data-service-row-icon`
4. حذف `custom="..."` و اتریبیوت‌های بازمانده React؛ تبدیل مسیر تصویر به `assets/img/...`.
5. پشتیبانی از حالت «داده خالی» → رندر بلوک پیش‌فرض یا حذف کامل بدون خطا.
6. ردیف‌های خدمات از Repeater (۵ خدمت: میدان، خیابان، پل، المان، ساختمان).
7. لینک هر ردیف به صفحه خدمت (`/services/{slug}/`).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — مقایسه ساختاری DOM با استاتیک ≥ ۹۹٪
- [ ] **P2** — هیچ متن هاردکدی در فایل سکشن نمانده باشد (همه از Schema)
- [ ] **P3** — تغییر محتوا از داشبورد در خروجی اعمال شود
- [ ] **P4** — در حالت خالی، خطا/Warning نداشته باشد
- [ ] **P5** — `data-service-counter` با تعداد ردیف‌ها هم‌خوان باشد
- [ ] **P6** — تعامل hover/ کلیک با JS موجود کار کند

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
