# Task 3.1 — ثبت `projects` + `project_type` + `city` و اصلاح Rewriteها

**فاز:** 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده  
**وضعیت:** `[PENDING]`  
**وابستگی:** —

## هدف

ثبت دقیق CPT و تکسونومی‌ها طبق DATA_BLUEPRINT + ستون‌های ادمین (`same_key_hrefl`, `number2-viewc`) و فیلترهای دراپ‌داون شهر/نوع پروژه.

## فایل‌های هدف

- `inc/Content/PostTypes.php`
- `inc/Content/Taxonomies.php`
- `inc/Admin/ListTables.php`
- `inc/Content/Rewrites.php`

## مراحل اجرا (چرخه ۶ گامی)

| گام | اقدام |
|---|---|
| `[READ MEMORY]` | خواندن `temp/PROJECT_MEMORY.md` و `temp/STATIC_TEMPLATE_MAP.md` و همین فایل |
| `[THINK & PLAN]` | تحلیل مارک‌آپ استاتیک مربوطه و طرح اتصال به وردپرس |
| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` |
| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM |
| `[RUN TEST]` | اجرا و رفع خطا تا ۱۰۰٪ شروط |
| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md`, `QA_REFLECTION_LOG.md` + کامیت |

## جزئیات پیاده‌سازی

1. `register_post_type('projects')` با تنظیمات دقیق بلوپرینت
2. `register_taxonomy('project_type'|'city')`
3. بازنویسی `product_cat` به `product_cat` (نه product-category) با `with_front=true`
4. `product_tag` slug=`product-tag`, `with_front=false`
5. ستون‌های لیست ادمین + فیلترها + قابل مرتب‌سازی
6. Flush rewrite فقط هنگام فعال‌سازی/تغییر نسخه

## شروط پاس شدن (Pass Conditions)

- [ ] `get_post_type_archive_link('projects')` = `/projects/`
- [ ] `get_term_link` برای `city` = `/city/{slug}/` و `project_type` = `/project_type/{slug}/`
- [ ] دسته محصول = `/product_cat/{slug}/`
- [ ] بدون flush در هر بار بارگذاری (فقط on activation)
- [ ] ستون‌ها و فیلترها در لیست ادمین پروژه‌ها ظاهر شوند

## تست‌های خودکار

- تست URL تولیدی
- تست ثبت تایپ/تکسونومی
- تست نبود flush_rewrite_rules در هوک روزمره
- تست ستون‌های ادمین

> اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-1.php`
