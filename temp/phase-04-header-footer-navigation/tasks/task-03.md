# Task 4.3 — header.php — بلوک هدر بالایی (لوگو، اکشن‌ها، شمارنده سبد)

**فاز:** 04 — هدر، مگامنو، منوی موبایل و فوتر
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-04-header-footer-navigation/tests/test-task-03.php`

## 🎯 هدف
برش دقیق مارک‌آپ هدر بالایی با جایگزینی داینامیک لوگو/لینک‌ها/شمارنده سبد — بدون تغییر DOM

## 🔗 وابستگی‌ها
- Task 4.2
- Task 2.3
- Task 2.6

## 📎 مراجع (منابع برش/استخراج)
- temp/phase-04…/refs/header-ref.html
- header-ref خطوط لوگو و دکمه‌ها

## 📤 فایل‌های خروجی
- `بسط header.php`
- `template-parts/header/header-top.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. برش کپی عین مارک‌آپ به template-parts/header/header-top.php و include از header.php
2. لوگو: تابع es_logo_url() → custom-logo → آپشن header.logo → fallback ese-logo.svg + متن alt از آپشن
3. دکمه جستجو (data-search-modal-open) و سبد: کلاس‌ها/hookها عیناً؛ شمارنده header-cart-badge با WooCommerce cart count (فقط وقتی WC فعال) + متن ارسال سریع میرور
4. لینک‌های ثابت اکشن‌ها از آپشن (cta هدر) با fallback میرور
5. sticky behavior: حفظ اتریبیوت data-navbar-header (JS میرور خودش scroll را مدیریت می‌کند)

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ کلاس/اتریبیوت حذف یا تغییر نکنید — فقط مقادیر درون تگ‌ها داینامیک می‌شوند

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] مقایسه DOM خروجی با ref مطابق (تعداد تگ‌ها/کلاس‌ها/data-hookها برابر)
- [ ] لوگو fallback سه‌مرحله‌ای کار کند
- [ ] badge سبد عدد درست ووکامرس را نشان دهد (تست stub)

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-04-header-footer-navigation/tests/test-task-03.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit

## 🔄 چرخه اجرا
- [x] READ MEMORY — refها/نقشه برش از HEADER_MAP
- [x] THINK & PLAN — فرگمنت‌های byte-exact + نقاط داینامیک
- [x] WRITE TEST — `test-task-03.php`
- [x] IMPLEMENT — بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — پاس کامل (مجموع فاز ۴: 212/212)
- [x] UPDATE MEMORY + COMMIT
