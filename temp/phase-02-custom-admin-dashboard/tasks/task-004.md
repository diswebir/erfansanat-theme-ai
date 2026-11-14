# P02-T004 — پوسته UI تب‌بندی RTL

> فاز 2: داشبورد اختصاصی ادمین و Section Builder · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `2.1` · وابستگی‌ها: `P02-T003`

## هدف دقیق
shell تب‌ها، ناوبری کیبورد و استایل اختصاصی ادمین ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Admin/ThemeOptions.php`
- `erfansanat/assets/admin/options.css`
- `erfansanat/assets/admin/options.js`

## منابع/مرجع
- `static-mirror/assets/css/styles.css`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: RTL و فونت فارسی درست باشد.
- [x] AC-02: تب‌ها با کیبورد/ARIA کار کنند.
- [x] AC-03: استایل ادمین به فرانت‌اند نشت نکند.

## سناریوهای تست اختصاصی
- [x] SC-01: جابجایی تب بدون reload.
- [x] SC-02: focus trap/aria-selected درست باشد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-004.php`
- اجرای تکی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-004.php`
- تست فاز: `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-004.php` → **24 passed, 0 failed, 0 pending**.
