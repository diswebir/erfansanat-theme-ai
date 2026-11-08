# Task 3.1: ثبت projects، services، تکسونومی‌ها و Rewriteها

[PASSED]

## هدف و برنامه فنی
ثبت projects/project_type/city طبق بلوپرینت؛ services مستقل با smartelement به‌عنوان آیتم؛ حفظ product_cat و product-tag و ستون‌های ادمین.

## فایل‌های هدف
- `inc/ContentTypes.php`
- `inc/Rewrites.php`

## وابستگی‌ها
- 2.3

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [x] آرشیو و تک‌پروژه و تکسونومی‌ها بدون 404 باشند.
- [x] services و تک‌خدمت‌ها قابل مشاهده باشند؛ smartelement یکی از خدمات باشد.
- [x] اسلاگ‌های حیاتی و with_front مطابق بلوپرینت باشند.
- [x] aliasها فقط پس از audit مسیر فعال شوند.
- [x] فیلتر شهر/نوع و ستون same_key_hrefl کار کند.

## آزمون
- `temp/phase-03-cpt-and-legacy-meta/tests/test-task-01.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.

## نتیجه اجرا — 2026-11-04

- PHPUnit: **۱۲ تست / ۱۳۵ assertion** (`TaskP03T01Test`). HTTP: **۱۵/۱۵** (`check-http.py --task 3.1`).
- رگرسیون: فاز ۱ (۳۲ تست/۷۸۸ assertion، ۸۳/۸۳ HTTP) و فاز ۲ (۳۵ تست/۱۹۲ assertion، ۴۹/۴۹ HTTP) بدون تغییر. PHPCS پاک.
- `inc/ContentTypes.php`: ثبت `projects` و `services` با تنظیمات مو‌به‌موی بلوپرینت، `project_type` و `city` (سلسله‌مراتبی، `with_front=false`، REST، ستون ادمین)، اجبار قرارداد سئوی ووکامرس (`product_cat` با `with_front=true`، `product-tag`، `product`)، ستون‌های شهر/نوع/تاریخ تکمیل و فیلترهای دراپ‌داون، ستون `same_key_hrefl`، و ثبت ۱۶ کلید متای تاریخی با نوع واقعی.
- `inc/Rewrites.php`: فلاش کنترل‌شده با نسخه (`erfansanat_rewrite_version`) به‌جای فلاش هر درخواست؛ جدول alias فقط پیشنهادی و **غیرفعال** تا audit مسیر زنده.
- **تغییر محیط:** mu-plugin آزمایشی `erfansanat-local-fixture-types.php` که قبلاً این تایپ‌ها را فراهم می‌کرد غیرفعال شد (به `.local/disabled/` منتقل شد و در `.gitignore` است). اکنون تنها منبع ثبت، خود قالب است و آزمون هم این را بررسی می‌کند.
- اندازه‌گیری: `/projects/`، `/services/`، تک‌پروژه، تک‌خدمت، آرشیو ترم `city`/`project_type`/`product_cat` و `/product/{slug}/` همه ۲۰۰؛ مسیر ناشناخته ۴۰۴؛ `/products/` همچنان ۴۰۴ (alias فعال نشده).
- محدودیت: ۱۸ ویژگی `pa_*` و مقادیر ترم‌ها از دادهٔ ساختگی محلی است؛ اسلاگ فارسی ترم‌ها به‌صورت percent-encoded در URL ظاهر می‌شود (رفتار هسته، نه باگ قالب). `/city/` و `/project_type/` بدون ترم، ۴۰۴ می‌دهند که رفتار طبیعی آرشیو خالی است.
