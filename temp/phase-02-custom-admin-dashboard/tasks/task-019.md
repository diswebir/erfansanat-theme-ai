# P02-T019 — ادغام کامل فونت وزیرمتن در پنل پیشخوان قالب

> فاز 2: داشبورد اختصاصی ادمین و Section Builder · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت: الحاقی (درخواست کاربر 2026-11-04) · وابستگی‌ها: `P02-T003`, `P02-T004`, `P02-T016`

## هدف دقیق
فونت self-hosted وزیرمتن (variable 100–900) به‌صورت کامل در داشبورد اختصاصی، متاباکس‌های قالب، Section Builder و صفحات ترم قالب اعمال شود؛ بدون نشت به فرانت‌اند و با حفظ آیکون‌فونت‌ها (dashicons/media) و فیلدهای کد.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.

## فایل‌های هدف
- `erfansanat/inc/Admin/Typography.php`
- `erfansanat/assets/admin/font.css`

## وابستگی‌های ثبت‌شده
- `erfansanat/functions.php` (boot)
- `erfansanat/inc/Admin/ThemeOptions.php` (dependency handle فونت)
- `erfansanat/assets/admin/options.css` (حذف @font-face تکراری)

## شروط پذیرش اختصاصی
- [x] AC-01: سه subset وزیرمتن (arabic/latin-ext/latin) با unicode-range و font-display:swap و وزن 100–900 بارگذاری شوند.
- [x] AC-02: فونت فقط در صفحات ادمین قالب (داشبورد/ویرایشگرهای دارای متاباکس/ترم‌های قالب) enqueue شود؛ نه فرانت‌اند و نه صفحات ادمین غیرمرتبط.
- [x] AC-03: آیکون‌فونت‌ها (dashicons/media) و فیلدهای کد monospace حفظ شوند.

## سناریوهای تست اختصاصی
- [x] SC-01: داشبورد + ویرایش محصول + ترم product_cat کلاس بدنه و استایل فونت را بگیرند.
- [x] SC-02: صفحه edit.php عمومی و پروفایل فرانت‌اند فونت ادمین را نگیرند.

## تست و اجرا
- اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-019.php`
- اجرای تکی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-019.php`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-02-custom-admin-dashboard/tests/test-task-019.php` → **27 passed, 0 failed, 0 pending**.
