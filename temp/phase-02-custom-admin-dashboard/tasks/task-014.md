# P02-T014 — تب تنظیمات آرشیو

> فاز 2: داشبورد اختصاصی ادمین و Section Builder · وضعیت: `[PENDING]` · اندازه: اتمیک
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
- [ ] AC-01: posts_per_page و نوع صفحه‌بندی schema داشته باشند.
- [ ] AC-02: مقدار نامعتبر به پیش‌فرض برگردد.
- [ ] AC-03: روی کوئری آرشیو اثر بگذارد.

## سناریوهای تست اختصاصی
- [ ] SC-01: تعداد کارت آرشیو عوض شود.
- [ ] SC-02: فیلتر پیش‌فرض اعمال شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-014.php`
- اجرای تکی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-014.php`
- تست فاز: `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
