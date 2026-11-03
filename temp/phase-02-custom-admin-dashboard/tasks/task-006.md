# P02-T006 — فیلدهای Media/Link/Icon

> فاز 2: داشبورد اختصاصی ادمین و Section Builder · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `2.2` · وابستگی‌ها: `P02-T005`

## هدف دقیق
انتخاب رسانه، لینک و آیکون با wp.media و sanitization پیاده شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Admin/FieldRenderer.php`
- `erfansanat/assets/admin/media-field.js`

## منابع/مرجع
- `static-mirror/images/ese-logo.svg`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: Media ID/URL طبق schema ذخیره شود.
- [ ] AC-02: لینک داخلی/خارجی و rel/target امن باشند.
- [ ] AC-03: آیکون فقط از مجموعه مجاز انتخاب شود.

## سناریوهای تست اختصاصی
- [ ] SC-01: انتخاب/حذف رسانه ذخیره شود.
- [ ] SC-02: javascript: در لینک رد شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-006.php`
- اجرای تکی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-006.php`
- تست فاز: `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
