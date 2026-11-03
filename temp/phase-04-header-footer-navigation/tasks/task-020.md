# P04-T020 — Parity و دسترس‌پذیری ناوبری

> فاز 4: هدر، مگامنو، منوی موبایل و فوتر · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `4.1` · وابستگی‌ها: `P04-T002`, `P04-T003`, `P04-T004`, `P04-T005`, `P04-T006`, `P04-T007`, `P04-T008`, `P04-T009`, `P04-T010`, `P04-T011`, `P04-T012`, `P04-T013`, `P04-T014`, `P04-T015`, `P04-T016`, `P04-T017`, `P04-T018`, `P04-T019`

## هدف دقیق
تست DOM/کیبورد/موبایل کل هدر و فوتر و اصلاح نشتی‌ها انجام شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `temp/phase-04-header-footer-navigation/tests/test-task-020.php`

## منابع/مرجع
- `static-mirror/index.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: کلاس/data-attrهای هدر/فوتر با مرجع برابر باشند.
- [ ] AC-02: focus/Escape/ARIA در مودال و drawer کار کنند.
- [ ] AC-03: در 375/768/1440 overflow افقی نباشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: اسنپ‌شات DOM هدر/فوتر پاس شود.
- [ ] SC-02: پیمایش کامل با کیبورد ممکن باشد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-020.php`
- اجرای تکی: `php temp/phase-04-header-footer-navigation/tests/test-task-020.php`
- تست فاز: `bash temp/phase-04-header-footer-navigation/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
