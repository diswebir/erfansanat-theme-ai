# Task 6.1 — `single-product.php` و اتصال کامل متاها

**فاز:** 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه  
**وضعیت:** `[PENDING]`  
**وابستگی:** —

## هدف

هیرو، گالری (`galerry_produ` + گالری ووکامرس)، زیرعنوان، عنوان انگلیسی، شناسه‌های قانونی، اطلاعیه پیش از خرید، کاتالوگ، جدول ۱۸ ویژگی، ویژگی‌ها/کاربردهای Repeater، پروژه‌های مرتبط، FAQ، نظرات.

## فایل‌های هدف

- `woocommerce/single-product.php`
- `woocommerce/content-single-product.php`
- `template-parts/product/hero.php`
- `gallery.php`
- `tabs.php`
- `specs.php`
- `legal-ids.php`
- `related-projects.php`
- `faq.php`
- `inc/Woo/ProductData.php`

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

1. خواندن `true_sale` برای حالت خرید/استعلام
2. گالری با fallback زنجیره‌ای `galerry_produ` → `_product_image_gallery` → `gallery-filed` → `image-2..6`
3. جدول ویژگی‌ها از `pa_*` (۱۸ مورد) با ترتیب ثابت
4. Repeaterها با `RepeaterCodec`
5. فرم افزودن به سبد استاندارد ووکامرس (متغیر/ساده)
6. نظرات با `comments_template`

## شروط پاس شدن (Pass Conditions)

- [ ] محصول `true_sale=true` دکمه/فرم خرید داشته باشد
- [ ] محصول `true_sale=false` دکمه استعلام + لینک تماس داشته باشد
- [ ] سکشن خالی رندر نشود
- [ ] همه متاها با املای دقیق خوانده شوند
- [ ] بدون خطای PHP با محصول بدون گالری/بدون ویژگی

## تست‌های خودکار

- تست دو حالت فروش
- تست fallback گالری
- تست جدول ویژگی
- تست محصول بدون داده

> اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-1.php`
