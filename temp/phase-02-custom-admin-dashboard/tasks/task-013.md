# P02-T013 — تب برند/رنگ و اسکریپت سفارشی

> فاز 2: داشبورد اختصاصی ادمین و Section Builder · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `2.2` · وابستگی‌ها: `P02-T009`

## هدف دقیق
رنگ‌های برند و کد سفارشی هدر/فوتر با محدودیت امنیتی ساخته شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Options/Tabs/BrandTab.php`

## منابع/مرجع
- `static-mirror/assets/css/styles.css`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: رنگ فقط CSS variable مجاز تولید کند.
- [ ] AC-02: اسکریپت فقط برای نقش مجاز و با escape کنترل شود.
- [ ] AC-03: تزریق CSS/JS به admin نشت نکند.

## سناریوهای تست اختصاصی
- [ ] SC-01: رنگ نامعتبر رد شود.
- [ ] SC-02: کد سفارش‌داده‌شده فقط در محل مجاز چاپ شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-013.php`
- اجرای تکی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-013.php`
- تست فاز: `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
