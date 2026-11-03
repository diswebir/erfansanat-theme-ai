# Task 6.9 — سبد خرید ووکامرس با پوسته cart.html

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-09.php`

## 🎯 هدف
جدول سبد، تغییر تعداد و خلاصه — مارک‌آپ میرور با هوک‌های استاندارد ووکامرس

## 🔗 وابستگی‌ها
- Task 6.1

## 📎 مراجع (منابع برش/استخراج)
- cart.html (cart-items-list/item-row/qty-inc/dec/remove/clear، cart-subtotal/tax/grandtotal)

## 📤 فایل‌های خروجی
- `woocommerce/cart/cart.php (override)`
- `بسط inc/Woo/Setup.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. override قالب cart.php ووکامرس: کپی مارک‌آپ میرور؛ ردیف‌ها با هوک woocommerce_cart_item_* (نام/تصویر/قیمت/جمع) و دکمه‌های qty با کلاس‌های cart-qty-inc/dec (اتریبیوت name استاندارد cart حفظ)
2. خلاصه سفارش: subtotal/tax/grandtotal با کلاس‌های میرور (هوک‌های جمع ووکامرس)
3. دکمه cart-clear → استاندارد empty cart ووکامرس
4. حالت سبد خالی: مارک‌آپ cart-empty میرور + دکمه بازگشت به فروشگاه
5. پیام‌های ووکامرس (خطا/موفقیت) با استایل میرور

## ⛔ محدودیت‌ها و خط‌قرمزها
- نام فیلدهای ووکامرس دست نخورد (cart[qty]...)
- بدون جاوااسکریپت سبد دستی

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر سبد خالی/پر مطابق میرور
- [ ] به‌روزرسانی qty با فرم استاندارد ووکامرس
- [ ] جمع‌ها صحیح با هوک‌ها

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-09.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
