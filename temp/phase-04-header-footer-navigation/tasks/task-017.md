# P04-T017 — Hreflang در head

> فاز 4: هدر، مگامنو، منوی موبایل و فوتر · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `4.1` · وابستگی‌ها: `P03-T023`

## هدف دقیق
تگ‌های hreflang واقعی از متاهای hrefmeta و same_key_hrefl تولید شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/Hreflang.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: link rel=alternate hreflang معتبر تولید شود.
- [ ] AC-02: metaهای قدیمی mirror جایگزین استاندارد شوند.
- [ ] AC-03: با hreflangpch/SEOPress تداخل نکند.

## سناریوهای تست اختصاصی
- [ ] SC-01: سه زبان fa/en/ar خروجی درست بدهند.
- [ ] SC-02: مقدار خالی تگ تولید نکند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-017.php`
- اجرای تکی: `php temp/phase-04-header-footer-navigation/tests/test-task-017.php`
- تست فاز: `bash temp/phase-04-header-footer-navigation/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
