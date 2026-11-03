# Task 6.2 — آرشیوها: `archive-product.php`, `taxonomy-product_cat.php`, `taxonomy-product_tag.php`

**فاز:** 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 6.1

## هدف

آرشیو با همان گرید/فیلتر سمت کلاینت، FAQ اختصاصی دسته، توضیحات دسته و صفحه‌بندی سئو-دوست.

## فایل‌های هدف

- `woocommerce/archive-product.php`
- `woocommerce/taxonomy-product_cat.php`
- `woocommerce/taxonomy-product_tag.php`
- `template-parts/archive/product-card.php`
- `template-parts/archive/filters.php`
- `inc/Woo/ArchiveQuery.php`

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

1. تولید کارت‌ها با `data-product-*` از داده واقعی
2. صفحه‌بندی سرور + لینک‌های سئو
3. FAQ دسته از term meta `faq`
4. دراوور فیلتر (`data-archive-drawer`)

## شروط پاس شدن (Pass Conditions)

- [ ] `/shop/` و `/product_cat/{slug}/` رندر صحیح با کارت‌های منطبق
- [ ] صفحه > ۱ بدون خطا و با `rel=next/prev`
- [ ] `data-product-*` همه پر شوند
- [ ] دسته بدون محصول حالت خالی داشته باشد

## تست‌های خودکار

- تست آرشیو
- تست صفحه‌بندی
- تست داده‌هوک‌ها
- تست حالت خالی

> اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-2.php`
