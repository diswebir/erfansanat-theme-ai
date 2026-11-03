# Task 3.9 — متاباکس «اطلاعات محصولات» — تب شناسه‌ها و عناوین

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.3`، `Task 3.8` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-09.php` |

## ۱. هدف

تب اول متاباکس محصول: `gs1_id`, `national_code`, `hs_id`, `isic_id`, `cpc_id`, `product_id`, `zireonvan`, `english_titl` (املای تاریخی)، `true_sale` (رادیو بله/خیر)، `before-sale-c`.

## ۲. فایل‌های هدف

- `inc/Admin/Metaboxes/ProductMeta.php`
- `inc/Admin/Metaboxes/Tabs/ProductIdentityTab.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. ثبت متاباکس با `add_meta_box` روی `product` (سمت راست/تمام‌عرض).
2. فیلدها با نام دقیق (بدون تغییر املا).
3. `true_sale` رادیویی با مقادیر رشته‌ای `"true"`/`"false"`.
4. Nonce + capability (`edit_product`) + `save_post_product`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — همه ۱۰ کلید ذخیره/بازیابی شوند
- [ ] **P2** — `english_titl` (نه `english_title`) ذخیره شود
- [ ] **P3** — `true_sale` فقط `true`/`false` باشد
- [ ] **P4** — ذخیره بدون nonce مسدود شود

## ۵. تست‌های خودکار

- `CRUD کلیدها`
- `املای کلید`
- `مقدار true_sale`
- `nonce`

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
