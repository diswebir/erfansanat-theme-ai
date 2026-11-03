# P05-T001 — Controller صفحه اصلی

> فاز 5: صفحه اصلی و کامپوننت‌های ماژولار · وضعیت: `[PENDING]` · اندازه: اتمیک
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
- [ ] AC-01: ترتیب پیش‌فرض ۱۳ سکشن محتوایی + CTA/فوتر جهانی باشد.
- [ ] AC-02: سکشن خاموش رندر نشود.
- [ ] AC-03: ترتیب ادمین اعمال شود.

## سناریوهای تست اختصاصی
- [ ] SC-01: خروجی پیش‌فرض با مرجع parity داشته باشد.
- [ ] SC-02: جابجایی دو سکشن در خروجی دیده شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-05-homepage-and-sections/tests/test-task-001.php`
- اجرای تکی: `php temp/phase-05-homepage-and-sections/tests/test-task-001.php`
- تست فاز: `bash temp/phase-05-homepage-and-sections/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
