# P03-T016 — متاباکس روابط محصول

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.2` · وابستگی‌ها: `P03-T012`, `P03-T001`, `P03-T003`

## هدف دقیق
پروژه‌های مرتبط و کاربردهای محصول به آرایه/JSON موجود متصل شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Admin/ProductMetaboxes.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
`project-ids`, `usage_countr`

## شروط پذیرش اختصاصی
- [ ] AC-01: project-ids فقط ID پروژه معتبر بپذیرد.
- [ ] AC-02: usage_countr فقط term_id پروژه‌تایپ بپذیرد.
- [ ] AC-03: فرمت ذخیره قبلی حفظ شود.

## سناریوهای تست اختصاصی
- [ ] SC-01: رابط دوطرفه داده را خراب نکند.
- [ ] SC-02: ID حذف‌شده پاک‌سازی شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-016.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-016.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
