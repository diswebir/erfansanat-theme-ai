# Task 6.7 — archive-product (shop) — هیرو، نوار فیلتر، گرید و صفحه‌بندی

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[DONE — 21/21]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-07.php`

## 🎯 هدف
آرشیو محصولات دقیقاً مطابق shop.html با فیلترهای GET کارا تا رسیدن فاز ۸

## 🔗 وابستگی‌ها
- Task 6.1
- Task 5.11

## 📎 مراجع (منابع برش/استخراج)
- shop.html (هیرو، نوار فیلتر product-*، گرید، pagination-*)

## 📤 فایل‌های خروجی
- `archive-product.php`
- `template-parts/archive/product-filters.php`
- `template-parts/archive/pagination.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هیرو آرشیو (برگه shop 8574: عنوان/توضیح از برگه)
2. نوار فیلتر میرور: جستجو (product-search)، دسته (product-category)، IP (product-ip-filter از pa_ip-rating termها)، ولتاژ (product-voltage-filter از pa_input-voltage)، وضعیت خرید (product-purchase-mode)، محدوده قیمت (product-price-range)، مرتب‌سازی (product-sort)، تعداد در صفحه (per-page)
3. pre_get_posts: اعمال فیلترهای GET با args امن (meta_query قیمت/اتریبیوت tax_query/true_sale meta) — در فاز ۸ AJAX می‌شود
4. گرید با card-product + ستون‌ها از آپشن
5. صفحه‌بندی میرور (pagination-root/page-btn/next/prev) با paginate_links و کلاس‌های میرور
6. شمارنده product-count و حالت product-empty

## ⛔ محدودیت‌ها و خط‌قرمزها
- فیلترها فقط GET امن + escaping
- حفظ همه data-hookها برای اتصال فاز ۸

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] args کوئری برای هر فیلتر (fixture) صحیح
- [ ] صفحه‌بندی کلاس‌های میرور
- [ ] حالت خالی رندر شود
- [ ] per-page از آپشن

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-07.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
