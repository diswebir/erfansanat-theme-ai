# Task 2.3: Section Manager با ترتیب، وضعیت و فیلدهای تکرارشونده

[PASSED]

## هدف و برنامه فنی
رجیستری سکشن‌ها و متادیتای هر برگه؛ جابه‌جایی قابل دسترس با صفحه‌کلید علاوه بر drag/drop؛ media/link/icon/repeater.

## فایل‌های هدف
- `inc/Admin/SectionManager.php`

## وابستگی‌ها
- 2.2

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [x] ترتیب رندر دقیقاً برابر ترتیب ذخیره‌شده باشد.
- [x] سکشن غیرفعال رندر نشود.
- [x] فیلدهای repeater بدون گم‌شدن آیتم ذخیره شوند.
- [x] شناسه سکشن ناشناخته و داده مخرب رد شود.
- [x] پیش‌فرض خانه تمام ۱۵ سکشن مرجع را پوشش دهد.

## آزمون
- `temp/phase-02-custom-admin-dashboard/tests/test-task-03.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.

## نتیجه اجرا — 2026-11-03

- PHPUnit: **۱۳ تست / ۷۵ assertion، بدون خطا و بدون هشدار** (`TaskP02T03Test`).
- HTTP: **۱۴ بررسی برای تسک ۲.۳**؛ کل HTTP فاز ۲: **۴۹/۴۹**. رگرسیون فاز ۱: **۳۲ تست / ۷۸۸ assertion** و **۸۳/۸۳** بررسی HTTP. PHPCS بدون خطا/هشدار.
- `inc/Sections.php`: رجیستری ۱۵ سکشن مرجع با ترتیب DOM (`hero, project-types, projects, about, knowledge, history, products, catalog, services, quality, support, clients, blog, contact-cta, location`)، لنگرهای مرجع (`home, projects, about, products, services, blog-preview, contact`) و فیلدهای هر سکشن.
- `inc/Admin/SectionManager.php`: پنل ترتیب با `draggable`، دکمه‌های ▲/▼ با `aria-label` (جابه‌جایی فقط با صفحه‌کلید)، کلید `Alt+ArrowUp/Down` روی ردیف، روشن/خاموش کردن هر سکشن و ویرایش درجای محتوا؛ متاباکس ترتیب مخصوص برگه با `_erfansanat_sections`.
- `assets/admin/options.js`: جمع‌آوری مسیرهای `section.field` و `repeater.index.field`، بازشماره‌گذاری ردیف‌های repeater بر اساس ترتیب DOM (حذف ردیف میانی آیتم بعدی را گم نمی‌کند) و ارسال `home_sections` به‌صورت JSON.
- `front-page.php` + ۱۵ قالب `template-parts/home/section-*.php`: اسکلت ساختاری (فاز ۵ طراحی نهایی را می‌گذارد)؛ هر سکشن `data-section` و لنگر مرجع را دارد.
- امنیت: کلید ناشناخته/تکراری/غیرآرایه دور ریخته می‌شود، `javascript:` رد می‌شود، `media_id` باید پیوست واقعی باشد، آیکون از فهرست بسته `ICONS` انتخاب می‌شود، متن با `sanitize_text_field`، سقف ۳۰ سکشن و ۲۰ ردیف.
- اصلاح‌های انجام‌شده: (۱) لینک با URL نامعتبر به پیش‌فرض مرجع برمی‌گشت و دکمهٔ معیوب را زنده می‌کرد؛ اکنون URL خالی می‌شود. (۲) دو هشدار «Undefined array key» در مسیر لینک پیش‌فرض. (۳) `??` درون شرط بدون استفاده در شاخهٔ مقدار. (۴) نام متغیرهای سراسری قوالب و پارامترهای `$default` برای PHPCS.
- محدودیت: drag/drop واقعی مرورگر آزمون نشده (بدون Chromium)؛ فقط مارک‌آپ `draggable`، دکمه‌های صفحه‌کلید و کد JS بررسی شده‌اند. تصاویر سکشن‌ها خالی است چون رسانهٔ ساختگی به‌صورت پیش‌فرض تخصیص داده نشده.
