# P02-T004 — پوسته UI تب‌بندی RTL

> فاز 2: داشبورد اختصاصی ادمین و Section Builder · وضعیت: `[PENDING]` · اندازه: اتمیک
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
- [ ] AC-01: RTL و فونت فارسی درست باشد.
- [ ] AC-02: تب‌ها با کیبورد/ARIA کار کنند.
- [ ] AC-03: استایل ادمین به فرانت‌اند نشت نکند.

## سناریوهای تست اختصاصی
- [ ] SC-01: جابجایی تب بدون reload.
- [ ] SC-02: focus trap/aria-selected درست باشد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-004.php`
- اجرای تکی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-004.php`
- تست فاز: `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
