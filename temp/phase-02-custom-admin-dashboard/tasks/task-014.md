# P02-T014 — تب تنظیمات آرشیو

> فاز 2: داشبورد اختصاصی ادمین و Section Builder · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `2.2` · وابستگی‌ها: `P02-T005`

## هدف دقیق
تعداد کارت، صفحه‌بندی، فیلترهای پیش‌فرض و حالت آرشیوها قابل تنظیم شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Options/Tabs/ArchiveTab.php`

## منابع/مرجع
- `static-mirror/shop.html`
- `static-mirror/projects.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: posts_per_page و نوع صفحه‌بندی schema داشته باشند.
- [x] AC-02: مقدار نامعتبر به پیش‌فرض برگردد.
- [x] AC-03: روی کوئری آرشیو اثر بگذارد.

## سناریوهای تست اختصاصی
- [x] SC-01: تعداد کارت آرشیو عوض شود.
- [x] SC-02: فیلتر پیش‌فرض اعمال شود.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-014.php`
- اجرای تکی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-014.php`
- تست فاز: `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-014.php` → **9 passed, 0 failed, 0 pending**.
