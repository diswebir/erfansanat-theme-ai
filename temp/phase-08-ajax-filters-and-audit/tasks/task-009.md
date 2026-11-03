# P08-T009 — مرتب‌سازی/صفحه‌بندی/حذف فیلتر

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.1` · وابستگی‌ها: `P08-T006`, `P06-T021`

## هدف دقیق
sorting، pagination و reset در آرشیو فیلترشده بدون رفرش کار کنند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/assets/js/product-filters.js`
- `erfansanat/inc/Core/SmartFilters.php`

## منابع/مرجع
- `static-mirror/shop.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: مرتب‌سازی قیمت/تاریخ/محبوبیت درست باشد.
- [ ] AC-02: صفحه‌بندی با فیلترها حفظ شود.
- [ ] AC-03: reset همه state را پاک کند.

## سناریوهای تست اختصاصی
- [ ] SC-01: صفحه دوم فیلترشده نتیجه درست بدهد.
- [ ] SC-02: reset تعداد کل را برگرداند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-009.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-009.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
