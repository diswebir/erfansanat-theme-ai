# P02-T011 — تب مگامنو

> فاز 2: داشبورد اختصاصی ادمین و Section Builder · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `2.2` · وابستگی‌ها: `P02-T010`

## هدف دقیق
تنظیمات تب‌های محصولات/پروژه‌ها و منابع کوئری مگامنو ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Options/Tabs/MegaMenuTab.php`

## منابع/مرجع
- `static-mirror/index.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: تعداد/ترتیب آیتم‌ها قابل تنظیم باشد.
- [ ] AC-02: منبع ترم‌ها slug-agnostic باشد.
- [ ] AC-03: خالی‌بودن منو fallback امن داشته باشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: تغییر تعداد آیتم در مگامنو اعمال شود.
- [ ] SC-02: ترم فارسی درست لینک شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-011.php`
- اجرای تکی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-011.php`
- تست فاز: `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
