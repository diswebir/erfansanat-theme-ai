# Task 6.10 — checkout، فیلدهای شخص/شرکت و صفحه دریافت فاکتور (thank-you)

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-10.php`

## 🎯 هدف
پوسته checkout میرور + toggle نوع خریدار + فیلدهای رسید (کد پستی/شناسه ملی) + thank-you مطابق checkout-receipt

## 🔗 وابستگی‌ها
- Task 6.9

## 📎 مراجع (منابع برش/استخراج)
- checkout.html (buyer-type-radio، legal-fields، checkout-form، receipt-code/customer/phone/method)

## 📤 فایل‌های خروجی
- `woocommerce/checkout/*.php overrides (form-checkout.php، thankyou.php)`
- `inc/Woo/CheckoutFields.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. استایل فرم checkout با مارک‌آپ میرور؛ فیلدهای استاندارد ووکامرس حفظ
2. فیلد نوع خریدار: radio buyer-type (شخص حقیقی/حقوقی) → توگل بلوک legal-fields (شناسه ملی/نام شرکت) با JS میرور hook حفظ
3. ذخیره فیلدها: woocommerce_checkout_create_order → order meta (buyer_type، national_id، company_name)
4. thank-you: مارک‌آپ receipt میرور (کد سفارش، نام، تلفن، روش پرداخت) با هوک‌های استاندارد
5. سازگاری درگاه‌ها: بدون تغییر فرآیند پرداخت (فقط نمایش)

## ⛔ محدودیت‌ها و خط‌قرمزها
- فیلدهای سفارشی optional باشند تا درگاه/چک‌اوت نشکند
- هرگز فیلد اجباری ووکامرس حذف نشود

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] toggle حقیقی/حقوقی کار کند
- [ ] متادیتای سفارش ذخیره شود (fixture)
- [ ] thank-you مارک‌آپ receipt بدهد
- [ ] فرم بدون خطا submit شود

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-10.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
