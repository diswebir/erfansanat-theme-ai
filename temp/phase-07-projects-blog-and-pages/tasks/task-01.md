# Task 7.1 — `single-projects.php` و آرشیوهای `project_type`/`city`

**فاز:** 7 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها  
**وضعیت:** `[PENDING]`  
**وابستگی:** —

## هدف

گالری ۵ تصویره، تاریخ تکمیل، شهر/نوع، تب‌های جزئیات، محصولات استفاده‌شده و آرشیوهای فیلترشده.

## فایل‌های هدف

- `single-projects.php`
- `archive-projects.php`
- `taxonomy-project_type.php`
- `taxonomy-city.php`
- `template-parts/project/*.php`

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

1. گالری از `second-image..six-image` (فقط تصاویر موجود)
2. `finish-pro-time` با نمایش تاریخ شمسی
3. `mortabet_mahsole_2` → کارت محصول
4. آرشیو با فیلتر نوع/شهر و شمارنده

## شروط پاس شدن (Pass Conditions)

- [ ] رندر صحیح تک‌پروژه و هر دو آرشیو
- [ ] پروژه بدون گالری خطا ندهد
- [ ] لینک‌های تاکسونومی درست باشند

## تست‌های خودکار

- تست رندر
- تست گالری جزئی
- تست لینک تاکسونومی

> اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-1.php`
