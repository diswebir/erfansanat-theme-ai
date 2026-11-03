# P04-T004 — ردیف برند و اکشن‌های هدر

> فاز 4: هدر، مگامنو، منوی موبایل و فوتر · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `4.1` · وابستگی‌ها: `P04-T002`, `P02-T010`

## هدف دقیق
لوگو، جستجو، سبد و دکمه موبایل با data-attrهای مرجع ساخته شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/header/brand-row.php`

## منابع/مرجع
- `static-mirror/index.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: لوگو/alt/link از تنظیمات بیایند.
- [ ] AC-02: data-header-cart-badge/data-mobile-toggle حفظ شوند.
- [ ] AC-03: شمارنده سبد مقدار واقعی بگیرد.

## سناریوهای تست اختصاصی
- [ ] SC-01: لوگوی سفارشی رندر شود.
- [ ] SC-02: شمارنده سبد با تعداد آیتم برابر باشد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-004.php`
- اجرای تکی: `php temp/phase-04-header-footer-navigation/tests/test-task-004.php`
- تست فاز: `bash temp/phase-04-header-footer-navigation/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
