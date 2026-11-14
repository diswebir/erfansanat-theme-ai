# P06-T021 — Toolbar مرتب‌سازی و صفحه‌بندی

> فاز 6: ووکامرس، محصول تکی و آرشیو فروشگاه · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `6.2` · وابستگی‌ها: `P06-T016`, `P02-T014`

## هدف دقیق
sorting/pagination/result-count مرجع به کوئری واقعی وصل شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/archive/toolbar.php`
- `erfansanat/template-parts/archive/pagination.php`

## منابع/مرجع
- `static-mirror/shop.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: مرتب‌سازی قیمت/تاریخ/محبوبیت کار کند.
- [x] AC-02: صفحه‌بندی لینک/شماره درست بدهد.
- [x] AC-03: result-count واقعی باشد.

## سناریوهای تست اختصاصی
- [x] SC-01: صفحه دوم آرشیو کارت درست بدهد.
- [x] SC-02: مرتب‌سازی نزولی قیمت کار کند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-021.php`
- اجرای تکی: `php temp/phase-06-woocommerce-and-products/tests/test-task-021.php`
- تست فاز: `bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-06-woocommerce-and-products/tests/test-task-021.php` → **23 passed, 0 failed, 0 pending**.
