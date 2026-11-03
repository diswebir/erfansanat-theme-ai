# P06-T013 — FAQ محصول

> فاز 6: ووکامرس، محصول تکی و آرشیو فروشگاه · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `6.1` · وابستگی‌ها: `P06-T002`, `P03-T018`

## هدف دقیق
آکاردئون سوالات متداول محصول از repeater faq ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/product/faq.php`

## منابع/مرجع
- `static-mirror/product/delsa-light-tree.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: name_question/answer_question درست رندر شوند.
- [ ] AC-02: JS آکاردئون مرجع کار کند.
- [ ] AC-03: FAQ خالی سکشن نسازد.

## سناریوهای تست اختصاصی
- [ ] SC-01: باز/بسته‌شدن سوال کار کند.
- [ ] SC-02: پاسخ HTML مجاز escape کنترل‌شده داشته باشد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-013.php`
- اجرای تکی: `php temp/phase-06-woocommerce-and-products/tests/test-task-013.php`
- تست فاز: `bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
