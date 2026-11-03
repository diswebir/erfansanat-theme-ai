# Task 06.01 — قالب تک‌محصول

**فاز:** phase-06-woocommerce-and-products · **وضعیت:** `[PENDING]`

## شرح تسک
- `single-product.php` + `template-parts/single-product/*` از روی product/*.html میرور: هیرو (breadcrumb، H1، zireonvan، english_titl)، گالری (galerry_produ + ووکامرس، lightbox میرور)، تب‌های مشخصات (جدول ۱۸ pa_*)، product-feature/product-feature2_copy/usage_product (+ usage_countr ترم‌ها)، FAQ متا، پروژه‌های مرتبط (project-ids)، محصولات مرتبط، دیدگاه‌های ووکامرس با مارک‌آپ review-* میرور
- منطق خرید: `true_sale='true'` → دکمه افزودن به سبد + min/step qty (سازگار WPC Product Quantity) + swatch (Variation Swatches)؛ `true_sale='false'` → دکمه استعلام/تماس + نمایش قیمت با استعلام
- اطلاعیه قبل از خرید (before-sale-c)، کاتالوگ (product-catalog-file)، شناسه‌های قانونی در تب مشخصات
- پنهان‌سازی سکشن‌های خالی وقتی متا پر نیست

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر محصول ساده/متغیر/استعلامی (fixture) بدون خطا
- [ ] حفظ ۱۰۰٪ کلاس‌ها و data-hookهای میرور در بخش‌های ثابت
- [ ] سکشن خالی رندر نمی‌شود (تست شرطی)

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
