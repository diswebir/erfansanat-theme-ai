# Task 1.1 — اسکلت پایه قالب و معماری شیءگرا (OOP)

**فاز:** 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک  
**وضعیت:** `[PENDING]`  
**وابستگی:** —

## هدف

ساخت `style.css` (هدر قالب)، `functions.php` سبک، `index.php`، Autoloader دستی در `inc/Autoloader.php` و Namespace `ErfanSanat\Theme`. راه‌اندازی `Theme` به‌عنوان Kernel با الگوی Singleton و ثبت `add_theme_support`های لازم.

## فایل‌های هدف

- `style.css`
- `functions.php`
- `index.php`
- `inc/Autoloader.php`
- `inc/Theme.php`
- `inc/Contracts/ModuleInterface.php`

## مراحل اجرا (چرخه ۶ گامی)

| گام | اقدام |
|---|---|
| `[READ MEMORY]` | خواندن `temp/PROJECT_MEMORY.md` و `temp/STATIC_TEMPLATE_MAP.md` و همین فایل |
| `[THINK & PLAN]` | تحلیل مارک‌آپ استاتیک مربوطه و طرح اتصال به وردپرس |
| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` |
| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM |
| `[RUN TEST]` | اجرا و رفع خطا تا ۱۰۰٪ شروط |
| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md`, `QA_REFLECTION_LOG.md` + کامیت |

## جزئیات پیاده‌سازی

1. هدر `style.css` با فیلدهای استاندارد وردپرس (Text Domain: `erfansanat`)
2. Autoloader PSR-4 دستی: `ErfanSanat\Theme\Foo\Bar` → `inc/Foo/Bar.php` (بدون Composer)
3. Kernel: ثبت هوک‌ها، `after_setup_theme` برای `title-tag`, `post-thumbnails`, `html5`, `custom-logo`, `woocommerce`, `wc-product-gallery-zoom/slider/lightbox`
4. `add_theme_support('align-wide')` فقط اگر با CSS استاتیک تعارض نداشت
5. `load_theme_textdomain` با پوشه `languages/`
6. ثبت منوها در `inc/Setup/Menus.php` (بدون Walker در این تسک)

## شروط پاس شدن (Pass Conditions)

- [ ] فعال‌سازی قالب بدون هیچ Notice/Warning در `WP_DEBUG=true`
- [ ] `php -l` روی همه فایل‌های PHP بدون خطا
- [ ] `get_theme_support('title-tag')`, `post-thumbnails`, `html5`, `woocommerce` همگی true
- [ ] کلاس‌ها با Autoloader بارگذاری می‌شوند (بدون `require` دستی در functions.php)
- [ ] `wp_get_theme()->get('TextDomain') === 'erfansanat'`

## تست‌های خودکار

- لینت همه فایل‌های PHP
- وجود add_theme_supportهای الزامی در سورس
- بارگذاری کلاس Kernel از طریق Autoloader
- نبود Notice در بوت

> اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-1.php`
