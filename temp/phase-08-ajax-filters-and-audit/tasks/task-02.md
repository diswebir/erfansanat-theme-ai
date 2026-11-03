# Task 8.2 — سیستم مدیریت فرم‌ها

> فاز: ایجکس، فرم‌ها و ممیزی نهایی · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
فرم‌های تماس/استعلام/استخدام/بازخورد با ذخیره در ادمین، Honeypot/Nonce و ایمیل.

## فایل‌های هدف
- `erfansanat/inc/Core/Forms.php`
- `erfansanat/inc/Admin/FormEntries.php`

## شروط پاس شدن (Pass Conditions)
- [ ] ثبت امن از فرانت‌اند
- [ ] نمایش لیست پیام‌ها در منوی اختصاصی ادمین
- [ ] Honeypot + Nonce
- [ ] ایمیل اطلاع‌رسانی

## تست
- اسکریپت: `tests/test-task-02.php` (اجرا: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-02.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
