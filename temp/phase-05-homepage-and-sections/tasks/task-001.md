# P05-T001 — Controller صفحه اصلی

> فاز 5: صفحه اصلی و کامپوننت‌های ماژولار · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `5.1` · وابستگی‌ها: `P02-T018`, `P04-T002`, `P04-T015`

## هدف دقیق
front-page.php حلقه رندر SectionManager و fallback ترتیب پیش‌فرض را اجرا کند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/front-page.php`

## منابع/مرجع
- `static-mirror/index.html`
- `temp/STATIC_TEMPLATE_MAP.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: ترتیب پیش‌فرض ۱۳ سکشن محتوایی + CTA/فوتر جهانی باشد.
- [x] AC-02: سکشن خاموش رندر نشود.
- [x] AC-03: ترتیب ادمین اعمال شود.

## سناریوهای تست اختصاصی
- [x] SC-01: خروجی پیش‌فرض با مرجع parity داشته باشد.
- [x] SC-02: جابجایی دو سکشن در خروجی دیده شود.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-05-homepage-and-sections/tests/test-task-001.php`
- اجرای تکی: `php temp/phase-05-homepage-and-sections/tests/test-task-001.php`
- تست فاز: `bash temp/phase-05-homepage-and-sections/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-05-homepage-and-sections/tests/test-task-001.php` → **14 passed, 0 failed, 0 pending**.
