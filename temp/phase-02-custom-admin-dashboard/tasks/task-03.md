# Task 2.3 — موتور SectionManager

> فاز: داشبورد اختصاصی ادمین + Section Builder · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
مدیریت سکشن‌های صفحات: روشن/خاموش، ترتیب Drag&Drop، ویرایش متن/تصویر/آیکون/ویدیو/دکمه/Repeater و رندر پویا.

## فایل‌های هدف
- `erfansanat/inc/Admin/SectionManager.php`

## شروط پاس شدن (Pass Conditions)
- [ ] ذخیره ساختاریافته متادیتای سکشن‌ها
- [ ] پشتیبانی Media/Link/Icon/Repeater
- [ ] رندر بر اساس ترتیب انتخابی ادمین
- [ ] فعال/غیرفعال‌سازی هر سکشن

## تست
- اسکریپت: `tests/test-task-03.php` (اجرا: `php temp/phase-02-custom-admin-dashboard/tests/test-task-03.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
