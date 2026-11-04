# Task 1.1: ساخت اسکلت پایه قالب، PSR-4 Autoloader و ثبت قابلیت‌های هسته وردپرس/ووکامرس

## فاز مربوطه
- **Phase 1:** زیرساخت هسته قالب، Autoloader، DeBloat و انتقال بی‌نقص Assets استاتیک (`temp/phase-01-core-and-assets`)
- **وضعیت:** `[PASSED]`

## شرح مهندسی تسک
ایجاد هدر استاندارد قالب وردپرس (RTL، نسخه PHP 8.4+ و وردپرس 7.1+)، پیاده‌سازی PSR-4 Autoloader اختصاصی تحت فضای نام ErfanSanat\Theme، بوت‌استرپ معماری شیءگرا در functions.php، ثبت add_theme_support برای title-tag، post-thumbnails، woocommerce، گالری‌های ووکامرس و html5، و ثبت ۴ موقعیت منوی اصلی (main-mega-menu، mobile-menu، tolidat-sherkat، landing).

## فایل‌های هدف (Target Files)
- `style.css`
- `functions.php`
- `index.php`
- `inc/Autoloader.php`
- `inc/Contracts/ServiceInterface.php`
- `inc/Core/ThemeSetup.php`
- `inc/Core/Plugin.php`

## شروط پاس شدن (Pass Conditions)
- [x] عبور ۱۰۰٪ تمامی فایل‌های PHP از بررسی سینتکس `php -l` بدون هیچ خطا یا هشدار در PHP 8.4.
- [x] فعال‌سازی موفق قالب `erfansanat-theme-ai` روی وردپرس زنده تست (`/opt/wp-testbed`) بدون بروز خطا در `WP_DEBUG = true`.
- [x] کارکرد صحیح PSR-4 Autoloader برای فضای نام `ErfanSanat\Theme\*` از پوشه `inc/`.
- [x] ثبت قطعی `current_theme_supports` برای `title-tag`، `post-thumbnails`، `woocommerce` و `html5`.
- [x] ثبت هر ۴ موقعیت منو (`main-mega-menu`، `mobile-menu`، `tolidat-sherkat`، `landing`) در `get_registered_nav_menus()`.

## چرخه اجرایی الزامی (6-Step CoT + TDD Loop)
1. `[READ MEMORY]` مطالعه `temp/PROJECT_MEMORY.md`، `temp/STATIC_TEMPLATE_MAP.md` و این فایل تسک.
2. `[THINK & PLAN]` تحلیل ساختار DOM استاتیک و طراحی دقیق معماری PHP/JS.
3. `[WRITE TEST]` تکمیل اسکریپت تست خودکار در `temp/phase-01-core-and-assets/tests/test-task-01.php`.
4. `[IMPLEMENT]` پیاده‌سازی کامل کد بدون هیچ‌گونه Placeholder یا حذف کلاس‌های CSS.
5. `[RUN TEST & CHECK CONDITIONS]` اجرای تست و ثبت نتیجه در `temp/QA_REFLECTION_LOG.md`.
6. `[UPDATE MEMORY]` به‌روزرسانی `temp/PROJECT_MEMORY.md` و `temp/MASTER_PLAN.md` + ثبت Git Commit.
