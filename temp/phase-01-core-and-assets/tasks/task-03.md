# Task 1.3 — انتقال Assets و مدیر Enqueue شرطی

**فاز:** 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 1.2

## هدف

انتقال `assets/css/styles.css`, `assets/js/main.js`, ۳ فونت Vazirmatn، ۲۲ تصویر و لوگو به ساختار `assets/` قالب + کلاس `AssetManager` با نسخه‌گذاری `filemtime` و Enqueue شرطی بر اساس `STATIC_TEMPLATE_MAP.md`.

## فایل‌های هدف

- `assets/css/styles.css`
- `assets/js/main.js`
- `assets/fonts/*`
- `assets/img/**`
- `assets/video/hero-video.mp4`
- `inc/Assets/AssetManager.php`
- `inc/Assets/Manifest.php`

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

1. کپی بایت‌به‌بایت فایل‌های CSS/JS/فونت (بدون تغییر محتوا)
2. جابه‌جایی تصاویر به `assets/img/` و نگه‌داشتن مسیرهای `url()` در CSS بدون شکستگی
3. `Manifest.php` = آرایه نگاشت «صفحه → دارایی‌های لازم»
4. `register/enqueue` با `filemtime()` به‌عنوان ورژن
5. `preload` فونت arabic + تصویر LCP هیرو (فقط صفحه اصلی)
6. `defer` برای `main.js` (همان رفتار استاتیک)

## شروط پاس شدن (Pass Conditions)

- [ ] همه دارایی‌ها HTTP 200 (مسیرهای نسبی صحیح)
- [ ] هیچ `url(...)` شکسته‌ای در CSS نباشد (بررسی خودکار مسیر فونت‌ها)
- [ ] JS/CSS در همه صفحات یک‌بار و بدون تکرار لود شود
- [ ] صفحه‌ای که هیرو ندارد، `hero` را preload نکند
- [ ] نسخه‌گذاری با filemtime کار کند (تغییر mtime → تغییر query string)

## تست‌های خودکار

- مقایسه md5 فایل مبدأ و مقصد (بدون تغییر)
- حل مسیر فونت‌ها از داخل CSS
- شبیه‌سازی enqueue در سه صفحه و شمارش assets
- بررسی preload شرطی

> اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-3.php`
