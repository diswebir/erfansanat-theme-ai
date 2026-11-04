# Task 8.15: فرم بازخورد مشتریان (client-feedback)

## فاز مربوطه
- **Phase 8:** جستجو و فیلتر Native، فرم‌ها، سئو و ممیزی نهایی (`temp/phase-08-ajax-filters-and-audit`)
- **وضعیت:** [PASSED] — live AJAX/CPT submission verified; user approved the live preview
- **سطح اتمیک:** یک کامپوننت / یک قابلیت مستقل — حداکثر یک خروجی قابل تست مستقل

## هدف (Goal)
فرم client-feedback با کلاس‌های styles.css، ذخیره و نمایش مدیریتی.

## محدوده (Scope)
### داخل محدوده (In Scope)
- پیاده‌سازی کامل همین قابلیت/کامپوننت، بدون placeholder و بدون حذف کلاس CSS یا `data-*` استاتیک.
- اتصال داده‌ای دقیقاً طبق `DATA_BLUEPRINT.md` و منبع استاتیک ذکرشده.

### خارج از محدوده (Out of Scope)
- هر قابلیت تسک‌های دیگر این فاز (در بخش پیش‌نیاز/وابستگی مشخص شده)؛ در این تسک فقط در حد hook/placeholder ساختاری مجاز است، نه پیاده‌سازی آن‌ها.

## پیش‌نیازها (Dependencies)
- Task 8.12
- فاز ۱ فریز و پاس‌شده است (`91/91`)؛ به تسک‌های ۱.۱ تا ۱.۳ دست زده نمی‌شود.

## فایل‌های هدف (Target Files)
- `inc/Forms/ClientFeedbackForm.php`
- `inc/Core/Plugin.php` (register the route service)

## منبع استاتیک و داده (Static Source & Data Contract)
- **منبع استاتیک:** بدون فایل استاتیک — styles.css
- **کلیدهای متا / داده:** `—`
- قانون طلایی: حفظ ۱۰۰٪ کلاس‌ها، ساختار DOM، `id`ها و اتریبیوت‌های `data-*` فایل استاتیک؛ هیچ HTML/CSS از صفر طراحی نمی‌شود.

## گام‌های پیاده‌سازی (Implementation Steps)
1. `[READ MEMORY]` خواندن `PROJECT_MEMORY.md`، بخش مرتبط `STATIC_TEMPLATE_MAP.md` و `DATA_BLUEPRINT.md`.
2. `[THINK & PLAN]` استخراج DOM/داده دقیق همین کامپوننت از منبع استاتیک و طراحی امضای کلاس/تابع.
3. `[WRITE TEST]` تکمیل تست اختصاصی این تسک (پایین) تا قبل از پیاده‌سازی قرمز (RED) باشد.
4. `[IMPLEMENT]` پیاده‌سازی فقط فایل‌های هدف همین تسک، با `declare(strict_types=1)`، escape/sanitize کامل و بدون TODO.
5. `[RUN TEST & CHECK CONDITIONS]` اجرای تست همین تسک + تست تسک‌های قبلی همین فاز (جلوگیری از regression).
6. `[UPDATE MEMORY]` به‌روزرسانی وضعیت همین تسک در `MASTER_PLAN.md` و `PROJECT_MEMORY.md`، ثبت در `QA_REFLECTION_LOG.md` و کامیت جدا با پیام پیشنهادی.

## شروط پاس شدن (Pass Conditions)
- [ ] همه فایل‌های هدف وجود دارند و `php -l` آن‌ها بدون خطاست (برای فایل‌های PHP).
- [ ] رفتار اختصاصی این تسک («فرم بازخورد مشتریان (client-feedback)») دقیقاً طبق هدف بالا کار می‌کند.
- [ ] کلاس‌ها/`data-*`/ساختار منبع استاتیک ۱۰۰٪ حفظ شده (در تسک‌های فرانت‌اند).
- [ ] کلیدهای متا دقیقاً با همان املای blueprint خوانده/نوشته می‌شوند (در تسک‌های داده‌ای): `—`.
- [ ] هیچ warning/notice از فایل‌های قالب با `WP_DEBUG=true` تولید نمی‌شود.
- [ ] تست اختصاصی این تسک سبز است و تست‌های قبلی همین فاز همچنان سبزند.

## سناریوهای تست (Test Scenarios — فایل: `tests/test-task-15.php`)
1. وجود و lint فایل‌های هدف.
2. رفتار مثبت (Happy Path) قابلیت این تسک روی وردپرس زنده (`/opt/wp-testbed`).
3. رفتار منفی/مرزی مرتبط (داده خالی، کاربر بدون دسترسی، nonce نامعتبر یا فیلتر غیرفعال — هرکدام مرتبط بود).
4. عدم تولید warning/notice قالب و عدم regression تسک‌های قبلی فاز.

## Definition of Done
- تست اختصاصی سبز + سوئیت فاز تا این تسک سبز + وضعیت `[PASSED]` در `MASTER_PLAN.md` + کامیت مستقل.

## پیام کامیت پیشنهادی
`feat(phase-8): task 8.15 — فرم بازخورد مشتریان (client-feedback)`

## Blocker
User approved the narrow native-theme implementation; Elementor remains excluded. The accessible feedback form is rendered in the existing homepage clients section and uses the established nonce-protected `client_feedback` route and message CPT. WordPress 7.2/PHP 8.5 task tests passed; a DOM harness loaded the actual homepage and theme JavaScript, validated required fields, submitted a real AJAX request to WordPress, and verified a stored submission ID/status response. The user approved the live preview, closing visual acceptance. No native Chromium screenshot was captured because compatible browser libraries are unavailable.
