# P02-T016 — SectionManager: UI ترتیب و فعال‌سازی

> فاز 2: داشبورد اختصاصی ادمین و Section Builder · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `2.3` · وابستگی‌ها: `P02-T015`

## هدف دقیق
Drag&Drop، روشن/خاموش و افزودن/حذف سکشن در metabox برگه ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Admin/SectionMetabox.php`
- `erfansanat/assets/admin/sections.js`

## منابع/مرجع
- `agent.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: ترتیب جدید بدون reload ذخیره شود.
- [ ] AC-02: سکشن خاموش رندر نشود.
- [ ] AC-03: دسترسی/nonce متاباکس بررسی شود.

## سناریوهای تست اختصاصی
- [ ] SC-01: جابجایی دو سکشن در خروجی اعمال شود.
- [ ] SC-02: ذخیره غیرمجاز رد شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-016.php`
- اجرای تکی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-016.php`
- تست فاز: `bash temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
