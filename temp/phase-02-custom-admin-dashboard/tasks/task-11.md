# Task 2.11 — Section Manager: Schema سکشن‌ها و متای `_es_sections`

| فیلد | مقدار |
|---|---|
| فاز | 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.3` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-11.php` |

## ۱. هدف

موتور سکشن: تعریف Schema برای صفحه اصلی (۱۵ سکشن) و برگه‌های ثابت (درباره/تماس/FAQ/مجوزها/گارانتی/استخدام)، ذخیره ترتیب+فعال‌بودن در متای `_es_sections`.

## ۲. فایل‌های هدف

- `inc/Sections/Registry.php`
- `inc/Sections/Store.php`
- `inc/Sections/Schema/home.php`
- `inc/Sections/Schema/about.php`, `contact.php`, `faq.php`, `licenses.php`, `warranty.php`, `recruitment.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. هر سکشن: `key`, `label`, `template` (فایل `template-parts/...`), فیلدها, `default_data`.
2. Store: خواندن/نوشتن `_es_sections` (آرایه `order`, `enabled`, `data`).
3. در نبود متا، Registry پیش‌فرض (ترتیب ۱..۱۵ همه فعال) را برگرداند.
4. کلید ناموجود در ذخیره‌ها، از Registry حذف/نادیده گرفته شود (schema drift).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — در نبود متا، دقیقاً ۱۵ سکشن صفحه اصلی به ترتیب پیش‌فرض برگردند
- [x] **P2** — ذخیره/بازیابی ترتیب و فعال‌بودن کار کند
- [x] **P3** — سکشن تازه‌اضافه‌شده در Registry به خروجی اضافه شود
- [x] **P4** — سکشن حذف‌شده در Registry باعث خطا نشود

## ۵. تست‌های خودکار

- `پیش‌فرض ۱۵ سکشن`
- `round-trip ترتیب`
- `schema drift`

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
