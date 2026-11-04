# Task 3.13 — Quick Edit برای شناسه‌ها و وضعیت فروش

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.9` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-13.php` |

## ۱. هدف

قابلیت Quick Edit برای `gs1_id`, `national_code`, `hs_id`, `isic_id`, `cpc_id`, `product_id`, `zireonvan`, `english_titl`, `true_sale` با ذخیره امن و بدون شکستن ستون‌ها.

## ۲. فایل‌های هدف

- `inc/Admin/QuickEdit.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `quick_edit_custom_box` برای ستون‌های هدف.
2. پرکردن مقدار فعلی با JS در `admin_footer` (فقط در لیست محصول).
3. ذخیره در `save_post` با nonce مخصوص quick edit.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — ویرایش سریع هر فیلد ذخیره شود
- [x] **P2** — مقدار فعلی در فرم پیش‌پر شود
- [x] **P3** — ذخیره سریع، متاهای دیگر را تغییر ندهد

## ۵. تست‌های خودکار

- `ذخیره سریع`
- `پیش‌پرکردن`
- `عدم تغییر سایر متاها`

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
