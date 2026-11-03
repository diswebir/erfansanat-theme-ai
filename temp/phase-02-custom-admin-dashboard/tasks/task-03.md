# Task 2.3 — موتور مدیریت سکشن‌ها (Section Manager)

**فاز:** 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 2.1

## هدف

موتور سکشن برای برگه‌ها: فعال/غیرفعال، ترتیب Drag&Drop، و ویرایش تمام متون/تصاویر/دکمه‌ها/Repeaterها؛ ذخیره در متای برگه و رندر پویا در فرانت.

## فایل‌های هدف

- `inc/Admin/SectionManager.php`
- `inc/Admin/Fields/Repeater.php`
- `inc/Sections/Registry.php`
- `inc/Sections/Renderer.php`
- `assets/admin/sortable.js`

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

1. ثبت Schema سکشن‌ها برای صفحه اصلی (۱۵ سکشن) و برگه‌های ثابت
2. ذخیره ساختار در `_es_sections` (ترتیب + فعال + داده)
3. Drag&Drop با vanilla JS (بدون jQuery UI)
4. Renderer با `get_template_part` و پشتیبانی از fallback استاتیک

## شروط پاس شدن (Pass Conditions)

- [ ] تغییر ترتیب در ادمین، ترتیب خروجی فرانت را تغییر دهد
- [ ] غیرفعال‌سازی سکشن، آن را از خروجی حذف کند (بدون رندر خالی)
- [ ] Repeaterها با فرمت سازگار JetEngine (`{"item-0":{...}}`) ذخیره شوند
- [ ] Media picker فیلدها فقط attachment ID معتبر بپذیرد
- [ ] هیچ سکشنی در Schema بدون فیلد قابل ویرایش نباشد

## تست‌های خودکار

- تست ذخیره/بازیابی ترتیب
- تست خاموش‌کردن سکشن
- تست Repeater format
- تست sanitize فیلدهای media

> اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-3.php`
