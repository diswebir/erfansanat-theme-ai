# Task 3.14 — متاباکس پروژه (`second-image` تا `six-image`, تاریخ، محصولات مرتبط)

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 3.1`، `Task 3.8` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-14.php` |

## ۱. هدف

متاباکس پروژه با گالری ۵ تصویره با املای دقیق (`second-image`, `three-image`, `foure-image`, `five-image`, `six-image`)، `finish-pro-time` با اعتبارسنجی تاریخ جلالی، و `mortabet_mahsole_2`.

## ۲. فایل‌های هدف

- `inc/Admin/Metaboxes/ProjectMeta.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. گالری مدرن + آینه‌سازی با کلیدهای قدیمی (تک‌کلیک sync).
2. اعتبارسنجی تاریخ با الگوی `YYYY/MM/DD` جلالی.
3. انتخابگر چندگانه محصول برای `mortabet_mahsole_2`.
4. Nonce + `edit_post` capability.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — هر ۵ کلید تصویر ذخیره/بازیابی شوند (شامل `foure-image`)
- [ ] **P2** — تاریخ نامعتبر رد شود و مقدار قبلی حفظ شود
- [ ] **P3** — محصولات مرتبط با فرمت قبلی سازگار باشند

## ۵. تست‌های خودکار

- `۵ کلید تصویر`
- `اعتبار تاریخ`
- `سازگاری محصولات مرتبط`

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
