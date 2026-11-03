# Task 3.12 — Repeaterهای محصول: `product-feature`, `product-feature2_copy`, `usage_product`, `faq`

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 3.8`، `Task 2.6` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-12.php` |

## ۱. هدف

چهار Repeater با زیرفیلدهای دقیق (`add-new-feature`, `vaule_of_variable`, `add-new-usage`, `name_question`, `answer_question`) در تب‌های مستقل و خواندن/نوشتن سازگار.

## ۲. فایل‌های هدف

- `inc/Admin/Metaboxes/Tabs/ProductRepeatersTab.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. هر Repeater با schema زیرفیلد و placeholder راهنما.
2. ذخیره با `RepeaterCodec` (حفظ فرمت).
3. خروجی‌ها با `esc_html`/`wp_kses_post` بسته به نوع محتوا.
4. حذف کامل وقتی خالی است.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — هر چهار Repeater CRUD کامل داشته باشند
- [ ] **P2** — `vaule_of_variable` با همین املا ذخیره شود
- [ ] **P3** — داده نمونه JetEngine بدون تغییر خوانده شود
- [ ] **P4** — سطر خالی ذخیره نشود

## ۵. تست‌های خودکار

- `CRUD چهارگانه`
- `املای کلید`
- `نمونه JetEngine`
- `سطر خالی`

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
