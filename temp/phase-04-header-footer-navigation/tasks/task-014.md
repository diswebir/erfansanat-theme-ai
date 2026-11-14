# P04-T014 — بخش اطلاعات فوتر

> فاز 4: هدر، مگامنو، منوی موبایل و فوتر · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `4.1` · وابستگی‌ها: `P02-T012`, `P04-T001`

## هدف دقیق
سکشن نشانی/دسترسی سریع/دسته‌بندی‌ها قبل از footer ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/global/footer-info.php`

## منابع/مرجع
- `static-mirror/index.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: سه ستون اطلاعات از options/menu بیایند.
- [x] AC-02: کلاس‌های grid/فاصله حفظ شوند.
- [x] AC-03: لینک‌ها escape شوند.

## سناریوهای تست اختصاصی
- [x] SC-01: تغییر نشانی در فوتر دیده شود.
- [x] SC-02: منوی خالی ستون را خراب نکند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-014.php`
- اجرای تکی: `php temp/phase-04-header-footer-navigation/tests/test-task-014.php`
- تست فاز: `bash temp/phase-04-header-footer-navigation/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-04-header-footer-navigation/tests/test-task-014.php` → **11 passed, 0 failed, 0 pending**.
