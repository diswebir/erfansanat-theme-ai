# P02-T012 — تب فوتر/تماس/شبکه‌های اجتماعی

> فاز 2: داشبورد اختصاصی ادمین و Section Builder · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `2.2` · وابستگی‌ها: `P02-T010`

## هدف دقیق
اطلاعات تماس، نشانی، کپی‌رایت و شبکه‌های اجتماعی قابل ویرایش شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Options/Tabs/FooterTab.php`

## منابع/مرجع
- `static-mirror/index.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: تلفن/ایمیل/نشانی sanitize شوند.
- [x] AC-02: لینک شبکه اجتماعی با rel امن رندر شود.
- [x] AC-03: پیش‌فرض استاتیک حفظ شود.

## سناریوهای تست اختصاصی
- [x] SC-01: تغییر تلفن در فوتر/CTA دیده شود.
- [x] SC-02: لینک خالی رندر نشود.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-012.php`
- اجرای تکی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-012.php`
- تست فاز: `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-012.php` → **10 passed, 0 failed, 0 pending**.
