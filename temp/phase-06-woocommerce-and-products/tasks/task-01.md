# Task 6.1 — Wrapper ووکامرس و پاکسازی پیش‌فرض‌های آن

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-01.php`

## 🎯 هدف
صفحات ووکامرس داخل shell قالب رندر شوند و خروجی‌های پیش‌فرض تکراری (breadcrumb/سایدبار/related) حذف شوند

## 🔗 وابستگی‌ها
- Task 4.2
- Task 4.6

## 📎 مراجع (منابع برش/استخراج)
- ساختار shell صفحات میرور

## 📤 فایل‌های خروجی
- `woocommerce.php`
- `inc/Woo/Setup.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. woocommerce.php: get_header → کانتینر main مطابق میرور → woocommerce_content() → get_footer (برای صفحات بدون قالب اختصاصی)
2. Setup: remove_action breadcrumb پیش‌فرض ووکامرس (breadcrumb سفارشی میرور در تسک‌های بعدی)
3. حذف سایدبار پیش‌فرض ووکامرس و output related products از سینگل (رندر دستی در 6.6)
4. فیلتر woocommerce_enqueue_styles: حذف استایل‌های غیرضروری ووکامرس (grid/layout اضافه) با حفظ blockui/select2 وقتی لازم
5. بازنویسی تعداد ستون‌های loop با فیلتر loop_shop_columns از آپشن (مطابق میرور)

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ رفتار پرداختی ووکامرس تغییر نکند
- استایل‌های حذف‌شده فقط ظاهری باشند

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] wrapper شامل woocommerce_content باشد
- [ ] breadcrumb/related/سایدبار پیش‌فرض حذف شده باشند
- [ ] ستون‌ها از آپشن خوانده شوند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-01.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
