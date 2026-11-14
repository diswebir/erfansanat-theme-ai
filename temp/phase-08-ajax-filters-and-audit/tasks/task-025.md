# P08-T025 — سخت‌سازی پس از ممیزی نهایی

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: تکمیلی (خارج از `8.x`، پس از ممیزی «اگر موردی مونده») · وابستگی‌ها: `P08-T024`

## هدف دقیق
قراردادهای ممیزی نهایی قفل شوند: بسته انتشار فقط از حالت committed ساخته شود، پاریتی زیپ↔گیت
بررسی شود، اثبات SC-02 (نصب تمیز از زیپ) تکرارپذیر و خودکار شود، و قرارداد حلقه‌های قالب
(بدون آلودگی global) به‌صورت تست محافظت شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.

## فایل‌های هدف
- `temp/tools/package-theme.sh` (حفاظ drift + پاریتی زیپ↔گیت)
- `temp/tools/verify-clean-install.sh` (اثبات تکرارپذیر SC-02)
- `.gitignore` (خروج از ایندکس برای آرتیفکت‌های موقت e2e)
- `temp/RELEASE_CHECKLIST.md` (ارجاع به اثبات خودکار)

## منابع/مرجع
- `agent.md`
- نتیجه ممیزی حلقه‌ها (2026-11-14): `the_post()` فقط در main-loop سه فایل مجاز؛ کارت‌ها ID صریح.

## کلیدهای داده/قرارداد
—(مستند در تست)

## شروط پذیرش اختصاصی
- [x] AC-01: `package-theme.sh` با drift در `erfansanat/` (تغییر یا فایل untracked) شکست بخورد و پاریتی فایل‌های زیپ با `git ls-files erfansanat/` را بررسی کند.
- [x] AC-02: `verify-clean-install.sh` بدون تعامل، نصب WP روی DB خالی + unzip زیپ + activate + رندر صفحه خانه را اثبات کند (SC-02 تکرارپذیر).
- [x] AC-03: قرارداد حلقه‌ها: هیچ `the_post()` bare خارج از فایل‌های main-loop (index.php، single-projects.php، woocommerce/single-product.php) و `setup_postdata` همیشه با `wp_reset_postdata` متوازن.
- [x] AC-04: آرتیفکت‌های موقت e2e (`temp/e2e-report.md`، `temp/e2e-state.txt`) از ایندکس گیت خارج شوند.

## سناریوهای تست اختصاصی
- تست: `temp/phase-08-ajax-filters-and-audit/tests/test-task-025.php`
- اسکریپت: `bash temp/tools/verify-clean-install.sh` → exit 0 + پیام موفقیت فارسی.
- بسته: `bash temp/tools/package-theme.sh` → پاریتی پاس؛ اجرای دوباره پس از drift نمونه‌ای → exit 3.

## شروط پذیرش عمومی
- [ ] `php -l` روی فایل‌های PHP تغییریافته بدون خطا.
- [x] هیچ TODO/FIXME باقی نماند.
- [ ] تست‌های وابستگی رگرسیون پاس شوند.
- [x] PROJECT_MEMORY و QA_REFLECTION_LOG به‌روزرسانی شوند.


## نتیجه اجرا (2026-11-14)
- تست اختصاصی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-025.php` → **18 passed, 0 failed, 0 pending**.
- سخت‌سازی: حفاظ drift + پاریتی zip↔git در package-theme.sh؛ verify-clean-install.sh (SC-02 تکرارپذیر، front_len=190269)؛ قرارداد حلقه‌ها قفل شد؛ e2e artifacts به .gitignore.
