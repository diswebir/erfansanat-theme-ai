# Task 5.1: پانزده سکشن خانه و رندر ترتیبی

[PASSED]

## هدف و برنامه فنی
برش سکشن‌های S01 تا S15 مطابق STATIC_TEMPLATE_MAP و اتصال به SectionManager بدون طراحی مجدد.

## فایل‌های هدف
- `front-page.php`
- `template-parts/home/`

## وابستگی‌ها
- 4.2

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [x] در پیش‌فرض، ترتیب و DOM سکشن‌ها با مرجع برابر باشد.
- [x] تغییر ترتیب و خاموش‌کردن سکشن در خروجی اعمال شود.
- [x] هیچ سکشن مرجع حذف یا با placeholder عوض نشود.

## آزمون
- `temp/phase-05-homepage-and-sections/tests/test-task-01.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.

## نتیجه اجرا

- `tests/test-task-01.php`: **۱۳ تست / ۱۵۳ assertion، بدون warning**.
- `check-http.py --task 5.1`: **۴۰ بررسی HTTP**.
- PHPCS روی کل `erfansanat/` تمیز.

### پانزده سکشن، دقیقاً به ترتیب مرجع
S01–S15 همه از `index.html` برش خوردند و در `Sections::registry()` به همان ترتیب STATIC_TEMPLATE_MAP ثبت شدند. هیچ سکشنی حذف یا با placeholder عوض نشد؛ حجم صفحه اصلی از ~۸۹KB (پوسته فاز ۴) به **۲۱۷KB** رسید که همان مارک‌آپ مرجع است.

| سکشن | هوک‌های JS بازسازی‌شده |
|---|---|
| S01 hero | `data-home1-hero-frame/scene-btn/scene-img/play-trigger/video-close/video-mute/categories-grid/bottom-tab` |
| S02 project-types | کارت‌های `card-glow` با آیکون و شماره |
| S03 projects | `data-home2-port-tab` + `data-home2-port-panel` |
| S06 history | `data-history-stage`، ۶ `data-home2-history-btn/panel`، `prev/next`، نوار پیشرفت |
| S09 services | `data-services-section`، ۵ `data-service-row`، `data-service-preview-img/counter/title/desc` |

### تصمیم دوم درباره ID تکراری
`Sections::render()` قبلاً id مرجع را روی پوشش `<section>` می‌گذاشت. با ورود هیرو، `id="home"` دو بار چاپ می‌شد. اکنون هر سکشن می‌تواند با `anchor_in_part` اعلام کند که id را خودش داخل قالب می‌نویسد؛ `home`، `projects` و `services` این‌گونه‌اند. نتیجه: هر هفت انکر مرجع دقیقاً یک بار و روی عنصر درست مرجع وجود دارد و هیچ ID تکراری در صفحه نیست.

### نقص واقعی که آزمون گرفت
`$erfansanat_video` در `section-hero.php` پیش از تعریف استفاده می‌شد (نام متغیر `$erfansanat_hero_video` بود). PHPUnit با `display-warnings` آن را به‌صورت «Undefined variable» در ۹ تست نشان داد؛ بدون این بررسی، ویدیو در محیطی با `WP_DEBUG` خاموش بی‌صدا از دست می‌رفت.

### محدودیت
باز/بسته شدن تب‌های S03 و اسلایدر تاریخچه در مرورگر واقعی دیده نشده؛ فقط ساختار و هوک‌ها آزموده شده‌اند.

### رگرسیون اصلاح‌شده
`testReferenceAnchorsAreReproduced` فاز ۲ انکرها را روی پوشش `<section id=...>` می‌شمرد. با انتقال `home/projects/services` به داخل قالب، قرارداد به «هر انکر یک بار، به ترتیب سند» تغییر کرد (سه انکر باقی‌مانده همچنان روی پوشش می‌مانند).
