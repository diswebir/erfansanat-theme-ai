# Task 2.13 — Renderer سکشن‌ها در فرانت + fallback

| فیلد | مقدار |
|---|---|
| فاز | 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 2.11` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-13.php` |

## ۱. هدف

`Renderer` که ترتیب و فعال‌بودن را از Store می‌خواند و `get_template_part` می‌زند؛ در صورت نبود فایل قالب یا خطا، fallback امن (بدون رندر خالی/خطا).

## ۲. فایل‌های هدف

- `inc/Sections/Renderer.php`
- `inc/Sections/Registry.php` (template-path validation)

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. پیمایش لیست نهایی: `if (!$enabled) continue;`
2. `locate_template` قبل از `get_template_part` برای جلوگیری از Warning.
3. انتقال `$data` سکشن به `template-part` با `set_query_var`/`args` (سازگار با WP 5.5+).
4. هوک `es_before_section`/`es_after_section` برای توسعه‌پذیری.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — خاموش‌کردن سکشن → صفر بایت خروجی از آن
- [x] **P2** — فایل قالب حذف‌شده → Warning ندهد و بقیه سکشن‌ها رندر شوند
- [x] **P3** — ترتیب خروجی = ترتیب ذخیره‌شده

## ۵. تست‌های خودکار

- `حذف سکشن`
- `فایل گم‌شده`
- `ترتیب خروجی`

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
