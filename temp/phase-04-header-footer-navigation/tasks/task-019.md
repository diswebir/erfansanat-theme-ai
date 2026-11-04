# P04-T019 — Walker منوی لندینگ

> فاز 4: هدر، مگامنو، منوی موبایل و فوتر · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `4.2` · وابستگی‌ها: `P04-T001`

## هدف دقیق
منوی landing برای اسکرول سکشن‌های آریا/لندینگ ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/NavWalkers.php`

## منابع/مرجع
- `agent.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: لینک‌ها به id سکشن‌ها اشاره کنند.
- [x] AC-02: حالت active با اسکرول کار کند.
- [x] AC-03: در نبود منو fatal ندهد.

## سناریوهای تست اختصاصی
- [x] SC-01: کلیک روی آیتم به سکشن درست اسکرول کند.
- [x] SC-02: منوی خالی رندر نشود.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-019.php`
- اجرای تکی: `php temp/phase-04-header-footer-navigation/tests/test-task-019.php`
- تست فاز: `bash temp/phase-04-header-footer-navigation/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-04-header-footer-navigation/tests/test-task-019.php` → **18 passed, 0 failed, 0 pending**.
