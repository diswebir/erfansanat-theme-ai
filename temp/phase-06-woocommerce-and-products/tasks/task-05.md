# Task 6.5 — single-product — ناحیه خرید: true_sale، تعداد (min/step)، سوئچ‌ها و toast

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-05.php`

## 🎯 هدف
منطق دوگانه خرید آنلاین/استعلامی + سازگاری WPC Quantity و Variation Swatches با مارک‌آپ pdp-* میرور

## 🔗 وابستگی‌ها
- Task 6.2
- Task 3.6

## 📎 مراجع (منابع برش/استخراج)
- product/wall-washer.html ناحیه خرید (pdp-add-to-cart، pdp-qty-*، min-qty/step-qty، opt-group/opt-btn، pdp-cart-toast)

## 📤 فایل‌های خروجی
- `template-parts/single-product/purchase.php`
- `inc/Woo/PurchaseFlow.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. true_sale=true: فرم add-to-cart استاندارد ووکامرس داخل مارک‌آپ میرور: قیمت (wc_price IRT)، دکمه pdp-add-to-cart، qty با اتریبیوت‌های min-qty/step-qty میرور sync با تنظیمات WPC (خواندن از افزونه یا متا محصول)
2. محصول متغیر: فرم variations ووکامرس + سوئچ‌های opt-btn از Variation Swatches (فیلترهای مارک‌آپ افزونه به کلاس‌های میرور نگاشت شود)
3. true_sale=false: جای دکمه خرید → دکمه استعلام قیمت (لینک به فرم استعلام فاز 8.6 یا تماس) + قیمت به‌صورت «استعلامی» مطابق میرور
4. toast افزودن به سبد pdp-cart-toast حفظ و اتصال به رویداد ووکامرس (added_to_cart)
5. PurchaseFlow: فیلترهای woocommerce_is_purchasable برای true_sale=false → خرید بسته + تغییر متن دکمه آرشیوها به استعلام

## ⛔ محدودیت‌ها و خط‌قرمزها
- فرم‌های ووکامرس باید نام فیلد استاندارد داشته باشند (خرید واقعی کار کند)
- هیچ جاوااسکریپت خرید دستی — استاندارد ووکامرس

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] وضعیت true با fixture: فرم add-to-cart استاندارد + کلاس‌های میرور
- [ ] وضعیت false: دکمه استعلام + is_purchasable فیلترشده
- [ ] qty اتریبیوت‌های min/step مطابق میرور
- [ ] toast hook حفظ شده

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-05.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
